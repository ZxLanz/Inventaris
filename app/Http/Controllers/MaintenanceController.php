<?php

namespace App\Http\Controllers;

use App\Models\MaintenanceRecord;
use App\Models\MaintenanceItem;
use App\Models\BarangAsset;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class MaintenanceController extends Controller
{
    /**
     * Display a listing of maintenance records.
     */
    public function index(Request $request)
    {
        $query = MaintenanceRecord::with(['asset.barang', 'asset.lokasi', 'teknisi'])
            ->latest('tanggal_mulai');

        // Filter by maintenance type
        if ($request->filled('maintenance_type')) {
            $query->where('maintenance_type', $request->maintenance_type);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->filled('tanggal_mulai')) {
            $query->whereDate('tanggal_mulai', '>=', $request->tanggal_mulai);
        }
        if ($request->filled('tanggal_selesai')) {
            $query->whereDate('tanggal_selesai', '<=', $request->tanggal_selesai);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('kode_maintenance', 'like', "%{$search}%")
                  ->orWhereHas('asset', function($q) use ($search) {
                      $q->where('kode_asset', 'like', "%{$search}%")
                        ->orWhereHas('barang', function($q) use ($search) {
                            $q->where('nama_barang', 'like', "%{$search}%");
                        });
                  });
            });
        }

        $maintenances = $query->paginate(15)->withQueryString();

        // Statistics for dashboard
        $stats = [
            'total' => MaintenanceRecord::count(),
            'in_progress' => MaintenanceRecord::where('status', 'in_progress')->count(),
            'completed' => MaintenanceRecord::where('status', 'completed')->count(),
            'total_cost' => MaintenanceRecord::where('status', 'completed')->sum('total_biaya'),
        ];

        return view('maintenance.index', compact('maintenances', 'stats'));
    }

    /**
     * Show the form for creating a new maintenance record.
     */
    public function create()
    {
        // Get available assets and users
        $assets = BarangAsset::with(['barang', 'lokasi'])
            ->whereIn('status', ['tersedia', 'rusak'])
            ->get();
        
        $teknisis = User::all();

        return view('maintenance.create', compact('assets', 'teknisis'));
    }

    /**
     * Store a newly created maintenance record in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'barang_asset_id' => 'required|exists:barang_assets,id',
            'maintenance_type' => 'required|in:preventive,corrective',
            'teknisi_id' => 'nullable|exists:users,id',
            'vendor_name' => 'nullable|string|max:255',
            'tanggal_mulai' => 'required|date',
            'masalah_ditemukan' => 'nullable|string',
            'foto_sebelum' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            
            // Maintenance Items
            'items' => 'required|array|min:1',
            'items.*.nama_item' => 'required|string|max:255',
            'items.*.deskripsi' => 'nullable|string',
            'items.*.kategori' => 'required|in:persiapan,perbaikan,penggantian,pembersihan,upgrade,testing',
            'items.*.biaya_material' => 'required|numeric|min:0',
            'items.*.biaya_jasa' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            // Upload foto sebelum
            $fotoSebelum = null;
            if ($request->hasFile('foto_sebelum')) {
                $fotoSebelum = $request->file('foto_sebelum')->store('maintenance', 'public');
            }

            // Get teknisi name if teknisi_id is provided
            $teknisiNama = null;
            if (!empty($validated['teknisi_id'])) {
                $teknisi = User::find($validated['teknisi_id']);
                $teknisiNama = $teknisi ? $teknisi->name : null;
            }

            // Create maintenance record
            $maintenance = MaintenanceRecord::create([
                'barang_asset_id' => $validated['barang_asset_id'],
                'maintenance_type' => $validated['maintenance_type'],
                'teknisi_id' => $validated['teknisi_id'],
                'teknisi_nama' => $teknisiNama, // 🆕 Auto-fill dari users.name
                'vendor_name' => $validated['vendor_name'],
                'tanggal_mulai' => $validated['tanggal_mulai'],
                'masalah_ditemukan' => $validated['masalah_ditemukan'],
                'foto_sebelum' => $fotoSebelum,
                'status' => 'in_progress',
                'created_by' => auth()->id(),
            ]);

            // Create maintenance items
            foreach ($validated['items'] as $itemData) {
                $maintenance->items()->create([
                    'nama_item' => $itemData['nama_item'],
                    'deskripsi' => $itemData['deskripsi'] ?? null,
                    'kategori' => $itemData['kategori'],
                    'biaya_material' => $itemData['biaya_material'],
                    'biaya_jasa' => $itemData['biaya_jasa'],
                    'subtotal' => $itemData['biaya_material'] + $itemData['biaya_jasa'],
                    'is_completed' => false,
                ]);
            }

            // Recalculate total biaya
            $maintenance->recalculate();

            // Update asset status to maintenance
            $asset = BarangAsset::find($validated['barang_asset_id']);
            $asset->update(['status' => 'maintenance']);

            DB::commit();

            return redirect()
                ->route('maintenance.show', $maintenance)
                ->with('success', 'Maintenance record berhasil dibuat!');

        } catch (\Exception $e) {
            DB::rollBack();
            
            // Delete uploaded file if exists
            if ($fotoSebelum && Storage::disk('public')->exists($fotoSebelum)) {
                Storage::disk('public')->delete($fotoSebelum);
            }

            return back()
                ->withInput()
                ->with('error', 'Gagal menyimpan data: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified maintenance record.
     */
    public function show(MaintenanceRecord $maintenance)
    {
        $maintenance->load([
            'asset.barang',
            'asset.lokasi',
            'teknisi',
            'items'
        ]);

        return view('maintenance.show', compact('maintenance'));
    }

    /**
     * Show the form for editing the specified maintenance record.
     */
    public function edit(MaintenanceRecord $maintenance)
    {
        if ($maintenance->status === 'completed') {
            return back()->with('error', 'Maintenance yang sudah selesai tidak bisa diedit!');
        }

        $maintenance->load('items');
        $assets = BarangAsset::with(['barang', 'lokasi'])->get();
        $teknisis = User::all();

        return view('maintenance.edit', compact('maintenance', 'assets', 'teknisis'));
    }

    /**
     * Update the specified maintenance record in storage.
     */
    public function update(Request $request, MaintenanceRecord $maintenance)
    {
        if ($maintenance->status === 'completed') {
            return back()->with('error', 'Maintenance yang sudah selesai tidak bisa diedit!');
        }

        $validated = $request->validate([
            'maintenance_type' => 'required|in:preventive,corrective',
            'teknisi_id' => 'nullable|exists:users,id',
            'vendor_name' => 'nullable|string|max:255',
            'tanggal_mulai' => 'required|date',
            'masalah_ditemukan' => 'nullable|string',
            'foto_sebelum' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            
            // Maintenance Items
            'items' => 'required|array|min:1',
            'items.*.id' => 'nullable|exists:maintenance_items,id',
            'items.*.nama_item' => 'required|string|max:255',
            'items.*.deskripsi' => 'nullable|string',
            'items.*.kategori' => 'required|in:persiapan,perbaikan,penggantian,pembersihan,upgrade,testing',
            'items.*.biaya_material' => 'required|numeric|min:0',
            'items.*.biaya_jasa' => 'required|numeric|min:0',
            'items.*.is_completed' => 'boolean',
        ]);

        DB::beginTransaction();
        try {
            // Handle foto sebelum
            if ($request->hasFile('foto_sebelum')) {
                // Delete old photo
                if ($maintenance->foto_sebelum && Storage::disk('public')->exists($maintenance->foto_sebelum)) {
                    Storage::disk('public')->delete($maintenance->foto_sebelum);
                }
                $validated['foto_sebelum'] = $request->file('foto_sebelum')->store('maintenance', 'public');
            }

            // Get teknisi name if teknisi_id is provided
            if (!empty($validated['teknisi_id'])) {
                $teknisi = User::find($validated['teknisi_id']);
                $validated['teknisi_nama'] = $teknisi ? $teknisi->name : null;
            } else {
                $validated['teknisi_nama'] = null;
            }

            // Update maintenance record
            $maintenance->update($validated);

            // Update or create items
            $existingItemIds = [];
            foreach ($validated['items'] as $itemData) {
                if (isset($itemData['id'])) {
                    // Update existing item
                    $item = MaintenanceItem::find($itemData['id']);
                    $item->update([
                        'nama_item' => $itemData['nama_item'],
                        'deskripsi' => $itemData['deskripsi'] ?? null,
                        'kategori' => $itemData['kategori'],
                        'biaya_material' => $itemData['biaya_material'],
                        'biaya_jasa' => $itemData['biaya_jasa'],
                        'subtotal' => $itemData['biaya_material'] + $itemData['biaya_jasa'],
                        'is_completed' => $itemData['is_completed'] ?? false,
                    ]);
                    $existingItemIds[] = $item->id;
                } else {
                    // Create new item
                    $newItem = $maintenance->items()->create([
                        'nama_item' => $itemData['nama_item'],
                        'deskripsi' => $itemData['deskripsi'] ?? null,
                        'kategori' => $itemData['kategori'],
                        'biaya_material' => $itemData['biaya_material'],
                        'biaya_jasa' => $itemData['biaya_jasa'],
                        'subtotal' => $itemData['biaya_material'] + $itemData['biaya_jasa'],
                        'is_completed' => $itemData['is_completed'] ?? false,
                    ]);
                    $existingItemIds[] = $newItem->id;
                }
            }

            // Delete items that are not in the request
            $maintenance->items()->whereNotIn('id', $existingItemIds)->delete();

            // Recalculate total biaya
            $maintenance->recalculate();

            DB::commit();

            return redirect()
                ->route('maintenance.show', $maintenance)
                ->with('success', 'Maintenance record berhasil diupdate!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Gagal mengupdate data: ' . $e->getMessage());
        }
    }

    /**
     * Complete maintenance (mark as done)
     */
    public function complete(Request $request, MaintenanceRecord $maintenance)
    {
        $validated = $request->validate([
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'hasil_maintenance' => 'required|string',
            'foto_sesudah' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'catatan' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            // Upload foto sesudah
            $fotoSesudah = null;
            if ($request->hasFile('foto_sesudah')) {
                $fotoSesudah = $request->file('foto_sesudah')->store('maintenance', 'public');
            }

            // Update maintenance record
            $maintenance->update([
                'tanggal_selesai' => $validated['tanggal_selesai'],
                'hasil_maintenance' => $validated['hasil_maintenance'],
                'foto_sesudah' => $fotoSesudah,
                'catatan' => $validated['catatan'] ?? null,
                'status' => 'completed',
            ]);

            // Update asset status back to available
            $asset = $maintenance->asset;
            $asset->update(['status' => 'tersedia']);

            DB::commit();

            return redirect()
                ->route('maintenance.show', $maintenance)
                ->with('success', 'Maintenance berhasil diselesaikan!');

        } catch (\Exception $e) {
            DB::rollBack();
            
            // Delete uploaded file if exists
            if ($fotoSesudah && Storage::disk('public')->exists($fotoSesudah)) {
                Storage::disk('public')->delete($fotoSesudah);
            }

            return back()
                ->withInput()
                ->with('error', 'Gagal menyelesaikan maintenance: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified maintenance record from storage.
     */
    public function destroy(MaintenanceRecord $maintenance)
    {
        DB::beginTransaction();
        try {
            // Delete photos
            if ($maintenance->foto_sebelum && Storage::disk('public')->exists($maintenance->foto_sebelum)) {
                Storage::disk('public')->delete($maintenance->foto_sebelum);
            }
            if ($maintenance->foto_sesudah && Storage::disk('public')->exists($maintenance->foto_sesudah)) {
                Storage::disk('public')->delete($maintenance->foto_sesudah);
            }

            // Update asset status if still in maintenance
            if ($maintenance->status === 'in_progress') {
                $maintenance->asset->update(['status' => 'tersedia']);
            }

            // Delete items (cascade)
            $maintenance->items()->delete();

            // Delete maintenance record
            $maintenance->delete();

            DB::commit();

            return redirect()
                ->route('maintenance.index')
                ->with('success', 'Maintenance record berhasil dihapus!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }
}