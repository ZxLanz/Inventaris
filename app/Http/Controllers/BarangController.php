<?php

namespace App\Http\Controllers;

use App\Models\Barang;
<<<<<<< HEAD
use App\Models\BarangAsset;
=======
>>>>>>> 7128ee3caecc07cd0adb1d836df3fe5b20ca7d83
use App\Models\Kategori;
use App\Models\Lokasi;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Log;
<<<<<<< HEAD
use Illuminate\Support\Facades\DB;
=======
use Illuminate\Support\Facades\Storage;
>>>>>>> 7128ee3caecc07cd0adb1d836df3fe5b20ca7d83
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
        
<<<<<<< HEAD
        $barangs = Barang::with(['kategori', 'lokasi', 'assets'])
            ->when($search, function ($query, $search) {
                $query->where('nama_barang', 'like', '%' . $search . '%')
                      ->orWhere('kode_barang', 'like', '%' . $search . '%')
                      ->orWhere('prefix', 'like', '%' . $search . '%');
=======
        $barangs = Barang::with(['kategori', 'lokasi'])
            ->when($search, function ($query, $search) {
                $query->where('nama_barang', 'like', '%' . $search . '%')
                      ->orWhere('kode_barang', 'like', '%' . $search . '%');
>>>>>>> 7128ee3caecc07cd0adb1d836df3fe5b20ca7d83
            })
            ->latest()->paginate(15)->withQueryString();
        
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
<<<<<<< HEAD
        // Validasi sesuai jenis barang
        $rules = [
            'jenis' => 'required|in:asset,consumable',
            'nama_barang' => 'required|string|max:150',
            'kategori_id' => 'required|exists:kategoris,id',
            'lokasi_id' => 'required|exists:lokasis,id',
            'jumlah' => 'required|integer|min:1',
            'satuan' => 'required|string|max:20',
            'tanggal_pengadaan' => 'required|date',
            'sumber_barang' => 'nullable|string|in:Pembelian,Hibah,Donasi,Bantuan Pemerintah,Lainnya', // BARU!
            'keterangan' => 'nullable|string|max:1000', // BARU!
        ];

        // Jika asset, prefix wajib dan harus unique per barang
        if ($request->jenis == 'asset') {
            $rules['prefix'] = [
                'required',
                'string',
                'max:10',
                'regex:/^[A-Z0-9-]+$/', // Hanya huruf besar, angka, dan dash
            ];
            $rules['kondisi'] = 'required|in:Baik,Rusak Ringan,Rusak Berat';
        }

        $validated = $request->validate($rules);

        // Jika consumable, set default
        if ($request->jenis == 'consumable') {
            $validated['kondisi'] = 'Baik';
            $validated['prefix'] = null;
            $validated['kode_barang'] = null;
        }

        DB::beginTransaction();
        try {
            // Upload gambar jika ada
            if ($request->hasFile('gambar')) {
                $file = $request->file('gambar');
                $filename = time() . '_' . $file->getClientOriginalName();
                
                $uploadPath = public_path('gambar-barang');
                if (!file_exists($uploadPath)) {
                    mkdir($uploadPath, 0755, true);
                }
                
                $file->move($uploadPath, $filename);
                $validated['gambar'] = $filename;
            }

            // Untuk asset, set kode_barang dari prefix (tanpa nomor urut)
            if ($request->jenis == 'asset') {
                $validated['kode_barang'] = $validated['prefix'];
            }

            // Simpan barang master
            $barang = Barang::create($validated);

            // Jika asset, generate unit per barang dengan nomor urut otomatis
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
            
            return back()->withErrors(['error' => 'Gagal menambahkan barang: ' . $e->getMessage()])
                        ->withInput();
        }
=======
        $validated = $request->validate([
            'kode_barang' => 'required|string|max:50|unique:barangs,kode_barang',
            'nama_barang' => 'required|string|max:150',
            'kategori_id' => 'required|exists:kategoris,id',
            'lokasi_id' => 'required|exists:lokasis,id',
            'jumlah' => 'required|integer|min:0',
            'satuan' => 'required|string|max:20',
            'kondisi' => 'required|in:Baik,Rusak Ringan,Rusak Berat',
            'tanggal_pengadaan' => 'required|date',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('gambar')) {
            $file = $request->file('gambar');
            $filename = time() . '_' . $file->getClientOriginalName();
            
            // Pastikan folder exists
            $uploadPath = public_path('gambar-barang');
            if (!file_exists($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }
            
            $file->move($uploadPath, $filename);
            $validated['gambar'] = $filename;
        }

        Barang::create($validated);

        return redirect()->route('barang.index')
                        ->with('success', 'Data barang berhasil ditambahkan.');
>>>>>>> 7128ee3caecc07cd0adb1d836df3fe5b20ca7d83
    }

    public function show(Barang $barang)
    {
<<<<<<< HEAD
        $barang->load(['kategori', 'lokasi', 'assets.lokasi']);
=======
        $barang->load(['kategori', 'lokasi']);
>>>>>>> 7128ee3caecc07cd0adb1d836df3fe5b20ca7d83
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
<<<<<<< HEAD
        // Validasi sesuai jenis
        $rules = [
            'nama_barang' => 'required|string|max:150',
            'kategori_id' => 'required|exists:kategoris,id',
            'lokasi_id' => 'required|exists:lokasis,id',
            'satuan' => 'required|string|max:20',
            'tanggal_pengadaan' => 'required|date',
            'sumber_barang' => 'nullable|string|in:Pembelian,Hibah,Donasi,Bantuan Pemerintah,Lainnya', // BARU!
            'keterangan' => 'nullable|string|max:1000', // BARU!
        ];

        // Untuk asset
        if ($barang->jenis == 'asset') {
            $rules['prefix'] = [
                'required',
                'string',
                'max:10',
                'regex:/^[A-Z0-9-]+$/',
            ];
            $rules['kondisi'] = 'required|in:Baik,Rusak Ringan,Rusak Berat';
            $rules['jumlah'] = 'required|integer|min:1';
        } else {
            // Untuk consumable, jumlah bisa diubah
            $rules['jumlah'] = 'required|integer|min:0';
        }

        $validated = $request->validate($rules);

        DB::beginTransaction();
        try {
            // Upload gambar jika ada
            if ($request->hasFile('gambar')) {
                if ($barang->gambar && file_exists(public_path('gambar-barang/' . $barang->gambar))) {
                    unlink(public_path('gambar-barang/' . $barang->gambar));
                }
                
                $file = $request->file('gambar');
                $filename = time() . '_' . $file->getClientOriginalName();
                
                $uploadPath = public_path('gambar-barang');
                if (!file_exists($uploadPath)) {
                    mkdir($uploadPath, 0755, true);
                }
                
                $file->move($uploadPath, $filename);
                $validated['gambar'] = $filename;
            }

            // Handle perubahan jumlah untuk asset
            if ($barang->jenis == 'asset') {
                $jumlahBaru = $validated['jumlah'];
                $jumlahLama = $barang->assets()->count();
                
                if ($jumlahBaru > $jumlahLama) {
                    // TAMBAH unit baru
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
                    // KURANGI unit (hapus yang tersedia dulu, dari yang terakhir)
                    $selisih = $jumlahLama - $jumlahBaru;
                    
                    // Ambil asset yang tersedia untuk dihapus
                    $assetTersedia = $barang->assets()
                        ->where('status', 'tersedia')
                        ->latest('id')
                        ->limit($selisih)
                        ->get();
                    
                    if ($assetTersedia->count() < $selisih) {
                        DB::rollBack();
                        $kurang = $selisih - $assetTersedia->count();
                        return back()->withErrors([
                            'error' => "Tidak bisa mengurangi {$selisih} unit. Hanya {$assetTersedia->count()} unit yang tersedia (tidak dipinjam). Masih kurang {$kurang} unit yang sedang dipinjam."
                        ])->withInput();
                    }
                    
                    // Hapus asset yang tersedia
                    foreach ($assetTersedia as $asset) {
                        $asset->delete();
                    }
                }

                // Update kode_barang dari prefix
                $validated['kode_barang'] = $validated['prefix'];
            }

            $barang->update($validated);

            DB::commit();

            return redirect()->route('barang.index')
                            ->with('success', 'Data barang berhasil diperbarui.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating barang: ' . $e->getMessage());
            
            return back()->withErrors(['error' => 'Gagal memperbarui barang: ' . $e->getMessage()])
                        ->withInput();
        }
    }

=======
        $validated = $request->validate([
            'kode_barang' => 'required|string|max:50|unique:barangs,kode_barang,' . $barang->id,
            'nama_barang' => 'required|string|max:150',
            'kategori_id' => 'required|exists:kategoris,id',
            'lokasi_id' => 'required|exists:lokasis,id',
            'jumlah' => 'required|integer|min:0',
            'satuan' => 'required|string|max:20',
            'kondisi' => 'required|in:Baik,Rusak Ringan,Rusak Berat',
            'tanggal_pengadaan' => 'required|date',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('gambar')) {
            // Hapus gambar lama jika ada
            if ($barang->gambar && file_exists(public_path('gambar-barang/' . $barang->gambar))) {
                unlink(public_path('gambar-barang/' . $barang->gambar));
            }
            
            $file = $request->file('gambar');
            $filename = time() . '_' . $file->getClientOriginalName();
            
            // Pastikan folder exists
            $uploadPath = public_path('gambar-barang');
            if (!file_exists($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }
            
            $file->move($uploadPath, $filename);
            $validated['gambar'] = $filename;
        }

        $barang->update($validated);

        return redirect()->route('barang.index')
                        ->with('success', 'Data barang berhasil diperbarui.');
    }

    /**
     * Method untuk cetak laporan PDF
     */
>>>>>>> 7128ee3caecc07cd0adb1d836df3fe5b20ca7d83
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

<<<<<<< HEAD
=======
    /**
     * PERBAIKAN UTAMA: Fungsi destroy yang diperbaiki
     */
>>>>>>> 7128ee3caecc07cd0adb1d836df3fe5b20ca7d83
    public function destroy(Barang $barang)
    {
        try {
            Log::info('Attempting to delete barang', [
                'id' => $barang->id,
<<<<<<< HEAD
                'nama_barang' => $barang->nama_barang,
                'jenis' => $barang->jenis
            ]);
            
            // CEK: Apakah ada asset yang sedang dipinjam?
            if ($barang->jenis == 'asset') {
                $assetDipinjam = $barang->assets()->where('status', 'dipinjam')->count();
                
                if ($assetDipinjam > 0) {
                    $kodeDipinjam = $barang->assets()
                        ->where('status', 'dipinjam')
                        ->pluck('kode_asset')
                        ->join(', ');
                    
                    return redirect()->route('barang.index')
                        ->with('error', "Tidak dapat menghapus barang '{$barang->nama_barang}'. Ada {$assetDipinjam} unit sedang dipinjam: {$kodeDipinjam}");
                }
            } else {
                // Untuk consumable, cek peminjaman aktif
                $peminjamanAktif = $barang->peminjaman()
                    ->whereIn('status', ['Menunggu Approval', 'Disetujui', 'Dipinjam', 'Terlambat'])
                    ->count();
                
                if ($peminjamanAktif > 0) {
                    return redirect()->route('barang.index')
                        ->with('error', "Tidak dapat menghapus barang '{$barang->nama_barang}'. Barang ini sedang dipinjam atau menunggu approval ({$peminjamanAktif} peminjaman aktif).");
                }
            }
            
            DB::beginTransaction();
            
            // HAPUS semua riwayat peminjaman yang sudah selesai
            $barang->peminjaman()->delete();
            
            // Hapus assets jika jenis asset
            if ($barang->jenis == 'asset') {
                Log::info('Deleting ' . $barang->assets()->count() . ' assets');
                $barang->assets()->delete();
            }
            
=======
                'nama_barang' => $barang->nama_barang
            ]);
            
>>>>>>> 7128ee3caecc07cd0adb1d836df3fe5b20ca7d83
            // Hapus gambar jika ada
            if ($barang->gambar) {
                $imagePath = public_path('gambar-barang/' . $barang->gambar);
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                    Log::info('Deleted image: ' . $barang->gambar);
                }
            }

<<<<<<< HEAD
            $barang->delete();
            
            DB::commit();
            
            Log::info('Successfully deleted barang with ID: ' . $barang->id);

            return redirect()->route('barang.index')
                ->with('success', 'Data barang berhasil dihapus.');
                            
        } catch (\Exception $e) {
            DB::rollBack();
            
=======
            // Hapus data dari database
            $barang->delete();
            
            Log::info('Successfully deleted barang with ID: ' . $barang->id);

            return redirect()->route('barang.index')
                            ->with('success', 'Data barang berhasil dihapus.');
                            
        } catch (\Exception $e) {
>>>>>>> 7128ee3caecc07cd0adb1d836df3fe5b20ca7d83
            Log::error('Error deleting barang', [
                'id' => $barang->id ?? 'unknown',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->route('barang.index')
<<<<<<< HEAD
                ->with('error', 'Gagal menghapus data barang: ' . $e->getMessage());
=======
                            ->with('error', 'Gagal menghapus data barang: ' . $e->getMessage());
>>>>>>> 7128ee3caecc07cd0adb1d836df3fe5b20ca7d83
        }
    }
}