<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Kategori;
use App\Models\Lokasi;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Log;
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
        
        $barangs = Barang::with(['kategori', 'lokasi'])
            ->when($search, function ($query, $search) {
                $query->where('nama_barang', 'like', '%' . $search . '%')
                      ->orWhere('kode_barang', 'like', '%' . $search . '%');
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
    }

    public function show(Barang $barang)
    {
        $barang->load(['kategori', 'lokasi']);
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

    /**
     * PERBAIKAN UTAMA: Fungsi destroy yang diperbaiki
     */
    public function destroy(Barang $barang)
    {
        try {
            Log::info('Attempting to delete barang', [
                'id' => $barang->id,
                'nama_barang' => $barang->nama_barang
            ]);
            
            // Hapus gambar jika ada
            if ($barang->gambar) {
                $imagePath = public_path('gambar-barang/' . $barang->gambar);
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                    Log::info('Deleted image: ' . $barang->gambar);
                }
            }

            // Hapus data dari database
            $barang->delete();
            
            Log::info('Successfully deleted barang with ID: ' . $barang->id);

            return redirect()->route('barang.index')
                            ->with('success', 'Data barang berhasil dihapus.');
                            
        } catch (\Exception $e) {
            Log::error('Error deleting barang', [
                'id' => $barang->id ?? 'unknown',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->route('barang.index')
                            ->with('error', 'Gagal menghapus data barang: ' . $e->getMessage());
        }
    }
}