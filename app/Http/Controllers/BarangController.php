<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\BarangAsset;
use App\Models\Kategori;
use App\Models\Lokasi;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

class BarangController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('auth'),
        ];
    }

    public function index(Request $request)
    {
        $search = $request->search;

        $barangs = Barang::with(['kategori', 'lokasi', 'assets'])
            ->when($search, function ($query, $search) {
                $query->where('nama_barang', 'like', '%' . $search . '%')
                      ->orWhere('kode_barang', 'like', '%' . $search . '%')
                      ->orWhere('prefix', 'like', '%' . $search . '%');
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();
        
        return view('barang.index', compact('barangs'));
    }

    public function create()
    {
        $kategoris = Kategori::all();
        $lokasis = Lokasi::all();
        return view('barang.create', compact('kategoris', 'lokasis'));
    }

    public function store(Request $request)
    {
        // Validasi sesuai jenis barang
        $rules = [
            'jenis' => 'required|in:asset,consumable',
            'nama_barang' => 'required|string|max:150',
            'kategori_id' => 'required|exists:kategoris,id',
            'lokasi_id' => 'required|exists:lokasis,id',
            'jumlah' => 'required|integer|min:1',
            'satuan' => 'required|string|max:20',
            'tanggal_pengadaan' => 'required|date',
            'sumber_barang' => 'nullable|string|in:Pembelian,Hibah,Donasi,Bantuan Pemerintah,Lainnya',
            'keterangan' => 'nullable|string|max:1000',
        ];

        if ($request->jenis == 'asset') {
            $rules['prefix'] = ['required', 'string', 'max:10', 'regex:/^[A-Z0-9-]+$/'];
            $rules['kondisi'] = 'required|in:Baik,Rusak Ringan,Rusak Berat';
        }

        $validated = $request->validate($rules);

        if ($request->jenis == 'consumable') {
            $validated['kondisi'] = 'Baik';
            $validated['prefix'] = null;
            $validated['kode_barang'] = null;
        }

        DB::beginTransaction();
        try {
            if ($request->hasFile('gambar')) {
                $file = $request->file('gambar');
                $filename = time() . '_' . $file->getClientOriginalName();
                $uploadPath = public_path('gambar-barang');
                if (!file_exists($uploadPath)) mkdir($uploadPath, 0755, true);
                $file->move($uploadPath, $filename);
                $validated['gambar'] = $filename;
            }

            if ($request->jenis == 'asset') {
                $validated['kode_barang'] = $validated['prefix'];
            }

            $barang = Barang::create($validated);

            if ($request->jenis == 'asset') {
                $jumlah = $validated['jumlah'];
                for ($i = 1; $i <= $jumlah; $i++) {
                    $kodeAsset = $barang->generateNextAssetCode();
                    BarangAsset::create([
                        'barang_id' => $barang->id,
                        'kode_asset' => $kodeAsset,
                        'lokasi_id' => $validated['lokasi_id'],
                        'kondisi' => $validated['kondisi'],
                        'status' => 'tersedia',
                        'tanggal_pengadaan' => $validated['tanggal_pengadaan'],
                        'gambar' => $validated['gambar'] ?? null,
                    ]);
                }
            }

            DB::commit();

            $message = $request->jenis == 'asset' 
                ? "Barang asset '{$barang->nama_barang}' berhasil ditambahkan dengan {$jumlah} unit."
                : "Barang consumable '{$barang->nama_barang}' berhasil ditambahkan.";

            return redirect()->route('barang.index')->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating barang: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Gagal menambahkan barang: ' . $e->getMessage()])->withInput();
        }
    }

    public function show(Barang $barang)
    {
        $barang->load(['kategori', 'lokasi', 'assets.lokasi']);
        return view('barang.show', compact('barang'));
    }

    public function edit(Barang $barang)
    {
        $kategoris = Kategori::all();
        $lokasis = Lokasi::all();
        return view('barang.edit', compact('barang', 'kategoris', 'lokasis'));
    }

    public function update(Request $request, Barang $barang)
    {
        $rules = [
            'nama_barang' => 'required|string|max:150',
            'kategori_id' => 'required|exists:kategoris,id',
            'lokasi_id' => 'required|exists:lokasis,id',
            'satuan' => 'required|string|max:20',
            'tanggal_pengadaan' => 'required|date',
            'sumber_barang' => 'nullable|string|in:Pembelian,Hibah,Donasi,Bantuan Pemerintah,Lainnya',
            'keterangan' => 'nullable|string|max:1000',
        ];

        if ($barang->jenis == 'asset') {
            $rules['prefix'] = ['required', 'string', 'max:10', 'regex:/^[A-Z0-9-]+$/'];
            $rules['kondisi'] = 'required|in:Baik,Rusak Ringan,Rusak Berat';
            $rules['jumlah'] = 'required|integer|min:1';
        } else {
            $rules['jumlah'] = 'required|integer|min:0';
        }

        $validated = $request->validate($rules);

        DB::beginTransaction();
        try {
            if ($request->hasFile('gambar')) {
                if ($barang->gambar && file_exists(public_path('gambar-barang/' . $barang->gambar))) {
                    unlink(public_path('gambar-barang/' . $barang->gambar));
                }
                $file = $request->file('gambar');
                $filename = time() . '_' . $file->getClientOriginalName();
                $uploadPath = public_path('gambar-barang');
                if (!file_exists($uploadPath)) mkdir($uploadPath, 0755, true);
                $file->move($uploadPath, $filename);
                $validated['gambar'] = $filename;
            }

            if ($barang->jenis == 'asset') {
                $jumlahBaru = $validated['jumlah'];
                $jumlahLama = $barang->assets()->count();

                if ($jumlahBaru > $jumlahLama) {
                    $selisih = $jumlahBaru - $jumlahLama;
                    for ($i = 1; $i <= $selisih; $i++) {
                        $kodeAsset = $barang->generateNextAssetCode();
                        BarangAsset::create([
                            'barang_id' => $barang->id,
                            'kode_asset' => $kodeAsset,
                            'lokasi_id' => $validated['lokasi_id'],
                            'kondisi' => $validated['kondisi'],
                            'status' => 'tersedia',
                            'tanggal_pengadaan' => $validated['tanggal_pengadaan'],
                            'gambar' => $validated['gambar'] ?? $barang->gambar,
                        ]);
                    }
                } elseif ($jumlahBaru < $jumlahLama) {
                    $selisih = $jumlahLama - $jumlahBaru;
                    $assetTersedia = $barang->assets()
                        ->where('status', 'tersedia')
                        ->latest('id')
                        ->limit($selisih)
                        ->get();
                    if ($assetTersedia->count() < $selisih) {
                        DB::rollBack();
                        $kurang = $selisih - $assetTersedia->count();
                        return back()->withErrors([
                            'error' => "Tidak bisa mengurangi {$selisih} unit. Masih {$kurang} unit sedang dipinjam."
                        ])->withInput();
                    }
                    foreach ($assetTersedia as $asset) $asset->delete();
                }

                $validated['kode_barang'] = $validated['prefix'];
            }

            $barang->update($validated);
            DB::commit();
            return redirect()->route('barang.index')->with('success', 'Data barang berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating barang: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Gagal memperbarui barang: ' . $e->getMessage()])->withInput();
        }
    }

    public function cetakLaporan()
    {
        $barangs = Barang::with(['kategori', 'lokasi'])->get();
        $data = [
            'title' => 'Laporan Data Barang Inventaris',
            'date' => date('d F Y'),
            'barangs' => $barangs
        ];
        $pdf = Pdf::loadView('barang.laporan', $data);
        return $pdf->stream('Laporan-Inventaris-barang.pdf');
    }

    public function destroy(Barang $barang)
    {
        try {
            Log::info('Attempting to delete barang', [
                'id' => $barang->id,
                'nama_barang' => $barang->nama_barang,
                'jenis' => $barang->jenis
            ]);

            if ($barang->jenis == 'asset') {
                $assetDipinjam = $barang->assets()->where('status', 'dipinjam')->count();
                if ($assetDipinjam > 0) {
                    return redirect()->route('barang.index')
                        ->with('error', "Tidak dapat menghapus '{$barang->nama_barang}', ada {$assetDipinjam} unit sedang dipinjam.");
                }
            }

            DB::beginTransaction();
            $barang->peminjaman()->delete();

            if ($barang->jenis == 'asset') $barang->assets()->delete();

            if ($barang->gambar && file_exists(public_path('gambar-barang/' . $barang->gambar))) {
                unlink(public_path('gambar-barang/' . $barang->gambar));
            }

            $barang->delete();
            DB::commit();

            Log::info('Successfully deleted barang: ' . $barang->id);
            return redirect()->route('barang.index')->with('success', 'Data barang berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting barang: ' . $e->getMessage());
            return redirect()->route('barang.index')->with('error', 'Gagal menghapus barang: ' . $e->getMessage());
        }
    }
}
