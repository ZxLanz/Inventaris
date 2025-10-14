<?php

namespace App\Http\Controllers;

use App\Models\Peminjaman;
use App\Models\Barang;
use App\Models\BarangAsset;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;

class PeminjamanController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('auth'),
            new Middleware('permission:view peminjaman', only: ['index', 'show']),
            new Middleware('permission:manage peminjaman', only: ['create', 'store']),
            new Middleware('permission:approve peminjaman', only: ['approve', 'reject', 'returnForm', 'processReturn']),
        ];
    }

    public function index(Request $request)
    {
        $search = $request->search;
        $status = $request->status;
        
        $query = Peminjaman::with(['barang', 'barangAsset', 'user', 'approver']);
        
        if (!Auth::user()->hasRole('admin')) {
            $query->where('user_id', Auth::id());
        }
        
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('kode_peminjaman', 'like', '%' . $search . '%')
                  ->orWhere('nama_peminjam', 'like', '%' . $search . '%')
                  ->orWhere('kontak_peminjam', 'like', '%' . $search . '%');
            });
        }
        
        if ($status) {
            $query->where('status', $status);
        }
        
        $peminjaman = $query->latest()->paginate(15)->withQueryString();
        
        return view('peminjaman.index', compact('peminjaman'));
    }

    public function create()
    {
        $barangs = Barang::with(['kategori', 'lokasi'])
            ->get()
            ->filter(function($barang) {
                if ($barang->is_asset) {
                    $tersedia = BarangAsset::where('barang_id', $barang->id)
                        ->where('status', 'tersedia')
                        ->count();
                    
                    $barang->load(['assets' => function($query) {
                        $query->where('status', 'tersedia');
                    }]);
                    
                    return $tersedia > 0;
                }
                
                return $barang->stok_tersedia > 0;
            });
        
        return view('peminjaman.create', compact('barangs'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'barang_id' => 'required|exists:barangs,id',
            'barang_asset_id' => 'nullable|exists:barang_assets,id',
            'nama_peminjam' => 'required|string|max:150',
            'kontak_peminjam' => 'nullable|string|max:50',
            'lokasi_tujuan' => 'required|string|max:200',
            'jumlah_pinjam' => 'required|integer|min:1',
            'tanggal_pinjam' => 'required|date|after_or_equal:today',
            'tanggal_jatuh_tempo' => 'required|date|after:tanggal_pinjam',
            'keterangan' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $barang = Barang::findOrFail($validated['barang_id']);
            
            if ($barang->is_asset) {
                if (!$validated['barang_asset_id']) {
                    return back()->withErrors([
                        'barang_asset_id' => 'Silakan pilih unit asset yang akan dipinjam'
                    ])->withInput();
                }
                
                $asset = BarangAsset::find($validated['barang_asset_id']);
                if (!$asset || $asset->status !== 'tersedia') {
                    return back()->withErrors([
                        'barang_asset_id' => 'Asset tidak tersedia untuk dipinjam'
                    ])->withInput();
                }
                
                $validated['jumlah_pinjam'] = 1;
                
            } else {
                if ($barang->stok_tersedia < $validated['jumlah_pinjam']) {
                    return back()->withErrors([
                        'jumlah_pinjam' => 'Jumlah pinjam melebihi stok tersedia (' . $barang->stok_tersedia . ' unit)'
                    ])->withInput();
                }
                
                $validated['barang_asset_id'] = null;
            }

            $validated['kode_peminjaman'] = Peminjaman::generateKode();
            $validated['user_id'] = Auth::id();
            $validated['status'] = 'Menunggu Approval';

            $peminjaman = Peminjaman::create($validated);

            // UPDATE STATUS ASSET (jika asset)
            if ($barang->is_asset && $validated['barang_asset_id']) {
                $asset = BarangAsset::find($validated['barang_asset_id']);
                
                if (!$asset) {
                    throw new \Exception('Asset tidak ditemukan');
                }
                
                $asset->update([
                    'status' => 'dipinjam',
                    'peminjaman_id' => $peminjaman->id
                ]);
                
                Log::info('Asset reserved for approval', [
                    'asset_id' => $asset->id,
                    'kode_asset' => $asset->kode_asset,
                    'peminjaman_id' => $peminjaman->id
                ]);
            }

            DB::commit();

            return redirect()->route('peminjaman.index')
                            ->with('success', 'Peminjaman berhasil diajukan. Menunggu approval admin.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating peminjaman: ' . $e->getMessage());
            
            return back()->withErrors(['error' => 'Gagal membuat peminjaman: ' . $e->getMessage()])
                        ->withInput();
        }
    }

    public function show(Peminjaman $peminjaman)
    {
        $peminjaman->load(['barang.kategori', 'barang.lokasi', 'barangAsset.lokasi', 'user', 'approver']);
        
        if (!Auth::user()->hasRole('admin') && $peminjaman->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
        
        return view('peminjaman.show', compact('peminjaman'));
    }

    /**
     * ✅ APPROVE - Status LANGSUNG jadi "Dipinjam" (bukan "Disetujui")
     */
    public function approve(Peminjaman $peminjaman)
    {
        if ($peminjaman->status !== 'Menunggu Approval') {
            return back()->with('error', 'Peminjaman ini tidak dalam status menunggu approval.');
        }

        DB::beginTransaction();
        try {
            $barang = $peminjaman->barang;
            
            if ($barang->is_asset) {
                $asset = $peminjaman->barangAsset;
                
                if (!$asset) {
                    throw new \Exception('Asset tidak ditemukan pada peminjaman ini.');
                }
                
                if (!in_array($asset->status, ['tersedia', 'dipinjam'])) {
                    throw new \Exception('Asset tidak dapat diapprove. Status saat ini: ' . $asset->status);
                }
                
                if ($asset->status === 'tersedia') {
                    $asset->update([
                        'status' => 'dipinjam',
                        'peminjaman_id' => $peminjaman->id
                    ]);
                }
                
            } else {
                if ($barang->stok_tersedia < $peminjaman->jumlah_pinjam) {
                    throw new \Exception('Stok tidak mencukupi untuk approval ini.');
                }
            }

            // ✅ STATUS LANGSUNG JADI "Dipinjam" (BUKAN "Disetujui")
            $peminjaman->update([
                'status' => 'Dipinjam',
                'approved_by' => Auth::id(),
                'approved_at' => now(),
            ]);

            DB::commit();

            return back()->with('success', 'Peminjaman telah disetujui. Status: Dipinjam.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error approving peminjaman: ' . $e->getMessage());
            return back()->with('error', $e->getMessage());
        }
    }

    public function reject(Request $request, Peminjaman $peminjaman)
    {
        $validated = $request->validate([
            'alasan_ditolak' => 'required|string|max:500',
        ]);

        if ($peminjaman->status !== 'Menunggu Approval') {
            return back()->with('error', 'Peminjaman ini tidak dalam status menunggu approval.');
        }

        DB::beginTransaction();
        try {
            if ($peminjaman->barang->is_asset && $peminjaman->barang_asset_id) {
                $asset = $peminjaman->barangAsset;
                
                if ($asset) {
                    $asset->update([
                        'status' => 'tersedia',
                        'peminjaman_id' => null
                    ]);
                    
                    Log::info('Asset returned to tersedia after rejection', [
                        'asset_id' => $asset->id,
                        'kode_asset' => $asset->kode_asset,
                        'peminjaman_id' => $peminjaman->id
                    ]);
                }
            }

            $peminjaman->update([
                'status' => 'Ditolak',
                'approved_by' => Auth::id(),
                'approved_at' => now(),
                'alasan_ditolak' => $validated['alasan_ditolak'],
            ]);

            DB::commit();

            return back()->with('success', 'Peminjaman telah ditolak. Asset kembali tersedia.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error rejecting peminjaman: ' . $e->getMessage());
            return back()->with('error', 'Gagal menolak peminjaman: ' . $e->getMessage());
        }
    }

    public function returnForm(Peminjaman $peminjaman)
    {
        if (!in_array($peminjaman->status, ['Dipinjam', 'Terlambat'])) {
            return back()->with('error', 'Barang sudah dikembalikan atau belum dipinjam.');
        }

        $peminjaman->load(['barang', 'barangAsset']);
        
        return view('peminjaman.return', compact('peminjaman'));
    }

    public function processReturn(Request $request, Peminjaman $peminjaman)
    {
        $validated = $request->validate([
            'tanggal_kembali' => 'required|date|after_or_equal:tanggal_pinjam',
            'keterangan' => 'nullable|string',
        ]);

        if (!in_array($peminjaman->status, ['Dipinjam', 'Terlambat'])) {
            return back()->with('error', 'Barang sudah dikembalikan atau belum dipinjam.');
        }

        DB::beginTransaction();
        try {
            $tanggalKembali = \Carbon\Carbon::parse($validated['tanggal_kembali']);
            $tanggalJatuhTempo = \Carbon\Carbon::parse($peminjaman->tanggal_jatuh_tempo);
            
            $hariTerlambat = 0;
            $totalDenda = 0;
            
            if ($tanggalKembali->gt($tanggalJatuhTempo)) {
                $hariTerlambat = $tanggalKembali->diffInDays($tanggalJatuhTempo);
                $totalDenda = $hariTerlambat * 5000;
            }

            $peminjaman->update([
                'status' => 'Dikembalikan',
                'tanggal_kembali' => $validated['tanggal_kembali'],
                'total_denda' => $totalDenda,
                'keterangan' => $validated['keterangan'] ?? $peminjaman->keterangan,
            ]);

            if ($peminjaman->barang->is_asset && $peminjaman->barang_asset_id) {
                $asset = $peminjaman->barangAsset;
                if ($asset) {
                    $asset->update([
                        'status' => 'tersedia',
                        'peminjaman_id' => null
                    ]);
                    
                    Log::info('Asset returned', [
                        'asset_id' => $asset->id,
                        'kode_asset' => $asset->kode_asset,
                        'peminjaman_id' => $peminjaman->id
                    ]);
                }
            }

            DB::commit();

            $message = 'Barang berhasil dikembalikan.';
            if ($totalDenda > 0) {
                $message .= ' Denda keterlambatan: Rp ' . number_format($totalDenda, 0, ',', '.');
            }

            return redirect()->route('peminjaman.index')->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error processing return: ' . $e->getMessage());
            return back()->with('error', 'Gagal memproses pengembalian: ' . $e->getMessage());
        }
    }

    public function getAssetsByBarang(Request $request)
    {
        $barangId = $request->barang_id;
        
        if (!$barangId) {
            return response()->json([
                'success' => false,
                'message' => 'Barang ID required'
            ], 400);
        }

        $barang = Barang::find($barangId);
        
        if (!$barang) {
            return response()->json([
                'success' => false,
                'message' => 'Barang not found'
            ], 404);
        }

        if ($barang->is_consumable) {
            return response()->json([
                'success' => true,
                'is_asset' => false,
                'assets' => []
            ]);
        }

        $assets = BarangAsset::where('barang_id', $barangId)
            ->where('status', 'tersedia')
            ->with('lokasi')
            ->get()
            ->map(function($asset) {
                return [
                    'id' => $asset->id,
                    'kode_asset' => $asset->kode_asset,
                    'kondisi' => $asset->kondisi,
                    'lokasi' => $asset->lokasi->nama_lokasi ?? '-'
                ];
            });

        return response()->json([
            'success' => true,
            'is_asset' => true,
            'assets' => $assets
        ]);
    }

    public function cetakLaporan(Request $request)
    {
        $query = Peminjaman::with(['barang', 'barangAsset', 'user', 'approver']);
        
        if ($request->tanggal_dari && $request->tanggal_sampai) {
            $query->whereBetween('tanggal_pinjam', [
                $request->tanggal_dari,
                $request->tanggal_sampai
            ]);
        }
        
        if ($request->status) {
            $query->where('status', $request->status);
        }
        
        $peminjaman = $query->latest()->get();
        
        $data = [
            'title' => 'Laporan Peminjaman Barang',
            'date' => date('d F Y'),
            'periode' => $request->tanggal_dari && $request->tanggal_sampai 
                ? date('d/m/Y', strtotime($request->tanggal_dari)) . ' - ' . date('d/m/Y', strtotime($request->tanggal_sampai))
                : 'Semua Periode',
            'peminjaman' => $peminjaman,
            'total_denda' => $peminjaman->sum('total_denda'),
        ];
        
        $pdf = Pdf::loadView('peminjaman.laporan', $data);
        
        return $pdf->stream('Laporan-Peminjaman.pdf');
    }
}