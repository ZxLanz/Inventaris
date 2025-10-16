<?php

namespace App\Http\Controllers;

use App\Models\MaintenanceRecord;
use App\Models\MaintenanceItem;
use App\Models\BarangAsset;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

class MaintenanceController extends Controller
{
    /**
     * Display a listing of maintenance records.
     */
    public function index(Request $request)
    {
        $query = MaintenanceRecord::with(['asset.barang', 'asset.lokasi', 'teknisi'])
            ->latest('tanggal_mulai');

        if ($request->filled('maintenance_type')) {
            $query->where('maintenance_type', $request->maintenance_type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('tanggal_mulai')) {
            $query->whereDate('tanggal_mulai', '>=', $request->tanggal_mulai);
        }
        if ($request->filled('tanggal_selesai')) {
            $query->whereDate('tanggal_selesai', '<=', $request->tanggal_selesai);
        }

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
            'items' => 'required|array|min:1',
            'items.*.nama_item' => 'required|string|max:255',
            'items.*.deskripsi' => 'nullable|string',
            'items.*.kategori' => 'required|in:persiapan,perbaikan,penggantian,pembersihan,upgrade,testing',
            'items.*.biaya_material' => 'required|numeric|min:0',
            'items.*.biaya_jasa' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            $fotoSebelum = null;
            if ($request->hasFile('foto_sebelum')) {
                $fotoSebelum = $request->file('foto_sebelum')->store('maintenance', 'public');
            }

            $teknisiNama = null;
            if (!empty($validated['teknisi_id'])) {
                $teknisi = User::find($validated['teknisi_id']);
                $teknisiNama = $teknisi ? $teknisi->name : null;
            }

            $maintenance = MaintenanceRecord::create([
                'barang_asset_id' => $validated['barang_asset_id'],
                'maintenance_type' => $validated['maintenance_type'],
                'teknisi_id' => $validated['teknisi_id'],
                'teknisi_nama' => $teknisiNama,
                'vendor_name' => $validated['vendor_name'],
                'tanggal_mulai' => $validated['tanggal_mulai'],
                'masalah_ditemukan' => $validated['masalah_ditemukan'],
                'foto_sebelum' => $fotoSebelum,
                'status' => 'in_progress',
                'created_by' => auth()->id(),
            ]);

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

            $maintenance->recalculate();

            $asset = BarangAsset::find($validated['barang_asset_id']);
            $asset->update(['status' => 'maintenance']);

            DB::commit();

            return redirect()
                ->route('maintenance.show', $maintenance)
                ->with('success', 'Maintenance record berhasil dibuat!');

        } catch (\Exception $e) {
            DB::rollBack();
            
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
            if ($request->hasFile('foto_sebelum')) {
                if ($maintenance->foto_sebelum && Storage::disk('public')->exists($maintenance->foto_sebelum)) {
                    Storage::disk('public')->delete($maintenance->foto_sebelum);
                }
                $validated['foto_sebelum'] = $request->file('foto_sebelum')->store('maintenance', 'public');
            }

            if (!empty($validated['teknisi_id'])) {
                $teknisi = User::find($validated['teknisi_id']);
                $validated['teknisi_nama'] = $teknisi ? $teknisi->name : null;
            } else {
                $validated['teknisi_nama'] = null;
            }

            $maintenance->update($validated);

            $existingItemIds = [];
            foreach ($validated['items'] as $itemData) {
                if (isset($itemData['id'])) {
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

            $maintenance->items()->whereNotIn('id', $existingItemIds)->delete();
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
            $fotoSesudah = null;
            if ($request->hasFile('foto_sesudah')) {
                $fotoSesudah = $request->file('foto_sesudah')->store('maintenance', 'public');
            }

            $maintenance->update([
                'tanggal_selesai' => $validated['tanggal_selesai'],
                'hasil_maintenance' => $validated['hasil_maintenance'],
                'foto_sesudah' => $fotoSesudah,
                'catatan' => $validated['catatan'] ?? null,
                'status' => 'completed',
            ]);

            $asset = $maintenance->asset;
            $asset->update(['status' => 'tersedia']);

            DB::commit();

            return redirect()
                ->route('maintenance.show', $maintenance)
                ->with('success', 'Maintenance berhasil diselesaikan!');

        } catch (\Exception $e) {
            DB::rollBack();
            
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
            if ($maintenance->foto_sebelum && Storage::disk('public')->exists($maintenance->foto_sebelum)) {
                Storage::disk('public')->delete($maintenance->foto_sebelum);
            }
            if ($maintenance->foto_sesudah && Storage::disk('public')->exists($maintenance->foto_sesudah)) {
                Storage::disk('public')->delete($maintenance->foto_sesudah);
            }

            if ($maintenance->status === 'in_progress') {
                $maintenance->asset->update(['status' => 'tersedia']);
            }

            $maintenance->items()->delete();
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

    /**
     * Cetak Laporan Maintenance (PDF)
     */
    public function cetakLaporan(Request $request)
    {
        $query = MaintenanceRecord::with([
            'asset.barang.kategori',
            'asset.lokasi',
            'teknisi',
            'items'
        ])->orderBy('tanggal_mulai', 'desc');

        if ($request->filled('tanggal_mulai')) {
            $query->whereDate('tanggal_mulai', '>=', $request->tanggal_mulai);
        }

        if ($request->filled('tanggal_selesai')) {
            $query->whereDate('tanggal_mulai', '<=', $request->tanggal_selesai);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('maintenance_type')) {
            $query->where('maintenance_type', $request->maintenance_type);
        }

        $maintenances = $query->get();

        $stats = [
            'total' => $maintenances->count(),
            'total_biaya' => $maintenances->sum('total_biaya'),
            'total_material' => $maintenances->sum('total_biaya_material'),
            'total_jasa' => $maintenances->sum('total_biaya_jasa'),
            'preventive_count' => $maintenances->where('maintenance_type', 'preventive')->count(),
            'preventive_biaya' => $maintenances->where('maintenance_type', 'preventive')->sum('total_biaya'),
            'corrective_count' => $maintenances->where('maintenance_type', 'corrective')->count(),
            'corrective_biaya' => $maintenances->where('maintenance_type', 'corrective')->sum('total_biaya'),
            'completed_count' => $maintenances->where('status', 'completed')->count(),
            'completed_biaya' => $maintenances->where('status', 'completed')->sum('total_biaya'),
            'in_progress_count' => $maintenances->where('status', 'in_progress')->count(),
            'in_progress_biaya' => $maintenances->where('status', 'in_progress')->sum('total_biaya'),
        ];

        $title = 'Laporan Maintenance & Perbaikan Barang';
        $date = now()->locale('id')->isoFormat('D MMMM YYYY, HH:mm') . ' WIB';
        
        $filterInfo = [];
        if ($request->filled('tanggal_mulai') || $request->filled('tanggal_selesai')) {
            $start = $request->tanggal_mulai ? \Carbon\Carbon::parse($request->tanggal_mulai)->locale('id')->isoFormat('D MMMM YYYY') : 'Awal';
            $end = $request->tanggal_selesai ? \Carbon\Carbon::parse($request->tanggal_selesai)->locale('id')->isoFormat('D MMMM YYYY') : 'Akhir';
            $filterInfo['periode'] = "Periode: {$start} - {$end}";
        }
        if ($request->filled('status')) {
            $filterInfo['status'] = 'Status: ' . ($request->status == 'completed' ? 'Selesai' : 'Sedang Proses');
        }
        if ($request->filled('maintenance_type')) {
            $filterInfo['tipe'] = 'Tipe: ' . ucfirst($request->maintenance_type);
        }

        $pdf = Pdf::loadView('maintenance.laporan', compact(
            'maintenances',
            'stats',
            'title',
            'date',
            'filterInfo'
        ));

        $pdf->setPaper('a4', 'landscape');

        return $pdf->stream('laporan-maintenance-' . date('Y-m-d') . '.pdf');
    }
}