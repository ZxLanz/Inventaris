<?php

namespace App\Http\Controllers;

use App\Models\BarangAsset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class BarangAssetController extends Controller
{
    /**
     * Hapus individual asset unit
     * 
     * @param BarangAsset $barangAsset
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(BarangAsset $barangAsset)
    {
        try {
            Log::info('Attempting to delete barang asset', [
                'id' => $barangAsset->id,
                'kode_asset' => $barangAsset->kode_asset,
                'status' => $barangAsset->status
            ]);

            // CEK: Apakah asset sedang dipinjam?
            if ($barangAsset->status == 'dipinjam') {
                return redirect()->route('barang.index')
                    ->with('error', "Tidak dapat menghapus asset {$barangAsset->kode_asset}. Asset ini sedang dipinjam.");
            }

            DB::beginTransaction();

            $kodeAsset = $barangAsset->kode_asset;
            $namaBarang = $barangAsset->barang->nama_barang;
            $barangId = $barangAsset->barang_id;

            // Hapus gambar jika ada dan berbeda dengan gambar master
            if ($barangAsset->gambar && $barangAsset->gambar != $barangAsset->barang->gambar) {
                $imagePath = public_path('gambar-barang/' . $barangAsset->gambar);
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                    Log::info('Deleted asset image: ' . $barangAsset->gambar);
                }
            }

            // Hapus asset
            $barangAsset->delete();

            // Update jumlah di barang master
            $barang = \App\Models\Barang::find($barangId);
            if ($barang) {
                $barang->jumlah = $barang->assets()->count();
                $barang->save();
            }

            DB::commit();

            Log::info('Successfully deleted barang asset with ID: ' . $barangAsset->id);

            return redirect()->route('barang.index')
                ->with('success', "Asset {$kodeAsset} ({$namaBarang}) berhasil dihapus.");

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error deleting barang asset', [
                'id' => $barangAsset->id ?? 'unknown',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('barang.index')
                ->with('error', 'Gagal menghapus asset: ' . $e->getMessage());
        }
    }

    /**
     * Update status asset (tersedia, dipinjam, maintenance, rusak)
     * 
     * @param Request $request
     * @param BarangAsset $barangAsset
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateStatus(Request $request, BarangAsset $barangAsset)
    {
        try {
            $validated = $request->validate([
                'status' => 'required|in:tersedia,dipinjam,maintenance,rusak',
            ]);

            $oldStatus = $barangAsset->status;
            $barangAsset->status = $validated['status'];
            $barangAsset->save();

            Log::info('Updated barang asset status', [
                'id' => $barangAsset->id,
                'kode_asset' => $barangAsset->kode_asset,
                'old_status' => $oldStatus,
                'new_status' => $validated['status']
            ]);

            return response()->json([
                'success' => true,
                'message' => "Status asset {$barangAsset->kode_asset} berhasil diubah menjadi {$validated['status']}",
                'data' => [
                    'status' => $barangAsset->status,
                    'kode_asset' => $barangAsset->kode_asset
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error updating barang asset status', [
                'id' => $barangAsset->id ?? 'unknown',
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengubah status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show detail asset
     * 
     * @param BarangAsset $barangAsset
     * @return \Illuminate\View\View
     */
    public function show(BarangAsset $barangAsset)
    {
        $barangAsset->load(['barang.kategori', 'lokasi']);
        
        return view('barang-asset.show', compact('barangAsset'));
    }

    /**
     * Edit asset individual
     * 
     * @param BarangAsset $barangAsset
     * @return \Illuminate\View\View
     */
    public function edit(BarangAsset $barangAsset)
    {
        $lokasis = \App\Models\Lokasi::all();
        
        return view('barang-asset.edit', compact('barangAsset', 'lokasis'));
    }

    /**
     * Update asset individual
     * 
     * @param Request $request
     * @param BarangAsset $barangAsset
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, BarangAsset $barangAsset)
    {
        try {
            // Validasi input
            $validated = $request->validate([
                'lokasi_id' => 'required|exists:lokasis,id',
                'kondisi' => 'required|in:Baik,Rusak Ringan,Rusak Berat',
                'status' => 'required|in:tersedia,dipinjam,maintenance,rusak',
                'keterangan' => 'nullable|string|max:500',
                'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            DB::beginTransaction();

            // Upload gambar jika ada
            if ($request->hasFile('gambar')) {
                // Hapus gambar lama jika ada dan bukan gambar master
                if ($barangAsset->gambar && 
                    $barangAsset->gambar != $barangAsset->barang->gambar && 
                    file_exists(public_path('gambar-barang/' . $barangAsset->gambar))) {
                    unlink(public_path('gambar-barang/' . $barangAsset->gambar));
                    Log::info('Deleted old asset image: ' . $barangAsset->gambar);
                }

                $file = $request->file('gambar');
                $filename = time() . '_' . $barangAsset->kode_asset . '_' . $file->getClientOriginalName();

                $uploadPath = public_path('gambar-barang');
                if (!file_exists($uploadPath)) {
                    mkdir($uploadPath, 0755, true);
                }

                $file->move($uploadPath, $filename);
                $validated['gambar'] = $filename;

                Log::info('Uploaded new asset image: ' . $filename);
            }

            // Update asset
            $barangAsset->update($validated);

            DB::commit();

            Log::info('Successfully updated barang asset', [
                'id' => $barangAsset->id,
                'kode_asset' => $barangAsset->kode_asset,
                'changes' => $validated
            ]);

            return redirect()->route('barang.index')
                ->with('success', "Asset {$barangAsset->kode_asset} berhasil diperbarui.");

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            
            return back()->withErrors($e->errors())
                ->withInput();

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Error updating barang asset', [
                'id' => $barangAsset->id ?? 'unknown',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()->withErrors(['error' => 'Gagal memperbarui asset: ' . $e->getMessage()])
                ->withInput();
        }
    }
}
