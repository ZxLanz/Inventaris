<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class MaintenanceRecord extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
        'next_maintenance_date' => 'date',
        'total_biaya_material' => 'decimal:2',
        'total_biaya_jasa' => 'decimal:2',
        'biaya_lain_lain' => 'decimal:2',
        'total_biaya' => 'decimal:2',
    ];

    // ============================================================
    // BOOT METHOD - Auto-generate kode maintenance
    // ============================================================
    
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($maintenance) {
            if (empty($maintenance->kode_maintenance)) {
                $maintenance->kode_maintenance = self::generateKodeMaintenance();
            }
        });
    }

    /**
     * Generate unique maintenance code dengan safety mechanism
     * Format: MNT-YYYYMMDD-XXX
     */
    public static function generateKodeMaintenance(): string
    {
        $prefix = 'MNT';
        $date = date('Ymd'); // Format: 20251014
        $maxAttempts = 100; // Safety limit
        
        for ($attempt = 0; $attempt < $maxAttempts; $attempt++) {
            // Cari record terakhir hari ini berdasarkan PATTERN kode (bukan created_at)
            $lastRecord = self::withTrashed() // Include soft deleted
                ->where('kode_maintenance', 'LIKE', "{$prefix}-{$date}-%")
                ->orderByRaw('CAST(SUBSTRING(kode_maintenance, -3) AS UNSIGNED) DESC') // Sort by nomor urut
                ->lockForUpdate() // Prevent race condition
                ->first();
            
            if ($lastRecord) {
                // Extract 3 digit terakhir dan increment
                $lastNumber = (int) substr($lastRecord->kode_maintenance, -3);
                $newNumber = $lastNumber + 1;
            } else {
                // Tidak ada record hari ini, mulai dari 1
                $newNumber = 1;
            }
            
            // Format kode: MNT-20251014-001
            $kode = sprintf('%s-%s-%03d', $prefix, $date, $newNumber);
            
            // Double check: pastikan kode belum ada (safety net)
            $exists = self::withTrashed()
                ->where('kode_maintenance', $kode)
                ->exists();
            
            if (!$exists) {
                return $kode;
            }
            
            // Jika masih duplicate (rare case), loop lagi
            // Dengan lockForUpdate, ini seharusnya tidak terjadi
        }
        
        // Ultimate fallback: jika semua attempt gagal (should never happen)
        // Gunakan microtime untuk guarantee uniqueness
        $timestamp = substr(str_replace('.', '', microtime(true)), -6);
        return sprintf('%s-%s-%s', $prefix, $date, $timestamp);
    }

    // ============================================================
    // RELATIONSHIPS
    // ============================================================
    
    /**
     * Maintenance belongs to BarangAsset
     */
    public function asset(): BelongsTo
    {
        return $this->belongsTo(BarangAsset::class, 'barang_asset_id');
    }

    /**
     * Maintenance belongs to User (teknisi)
     */
    public function teknisi(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teknisi_id');
    }

    /**
     * Maintenance belongs to User (creator)
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Maintenance has many items
     */
    public function items(): HasMany
    {
        return $this->hasMany(MaintenanceItem::class, 'maintenance_record_id');
    }

    // ============================================================
    // ACCESSORS
    // ============================================================
    
    /**
     * Get items count
     */
    public function getItemsCountAttribute(): int
    {
        return $this->items()->count();
    }

    /**
     * Get status badge class
     */
    public function getStatusBadgeAttribute(): string
    {
        return match($this->status) {
            'scheduled' => 'bg-info',
            'in_progress' => 'bg-warning text-dark',
            'completed' => 'bg-success',
            'cancelled' => 'bg-secondary',
            default => 'bg-secondary',
        };
    }

    /**
     * Get maintenance type label
     */
    public function getMaintenanceTypeLabelAttribute(): string
    {
        return match($this->maintenance_type) {
            'preventive' => 'Preventive',
            'corrective' => 'Corrective',
            default => ucfirst($this->maintenance_type),
        };
    }

    // ============================================================
    // SCOPES
    // ============================================================
    
    /**
     * Scope: In progress maintenance
     */
    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    /**
     * Scope: Completed maintenance
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope: By maintenance type
     */
    public function scopeByType($query, string $type)
    {
        return $query->where('maintenance_type', $type);
    }

    /**
     * Scope: By date range
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('tanggal_mulai', [$startDate, $endDate]);
    }

    // ============================================================
    // METHODS
    // ============================================================
    
    /**
     * Recalculate total biaya from maintenance items
     */
    public function recalculate(): void
    {
        $items = $this->items;
        
        $totalMaterial = $items->sum('biaya_material');
        $totalJasa = $items->sum('biaya_jasa');
        
        $this->update([
            'total_biaya_material' => $totalMaterial,
            'total_biaya_jasa' => $totalJasa,
            'total_biaya' => $totalMaterial + $totalJasa + ($this->biaya_lain_lain ?? 0),
        ]);
    }

    /**
     * Mark maintenance as completed
     */
    public function markAsCompleted(array $data): void
    {
        $this->update([
            'status' => 'completed',
            'tanggal_selesai' => $data['tanggal_selesai'] ?? now(),
            'hasil_maintenance' => $data['hasil_maintenance'] ?? null,
            'foto_sesudah' => $data['foto_sesudah'] ?? null,
            'catatan' => $data['catatan'] ?? null,
        ]);

        // Update asset status back to available
        if ($this->asset) {
            $this->asset->update(['status' => 'tersedia']);
        }
    }

    /**
     * Calculate downtime in hours
     */
    public function calculateDowntime(): int
    {
        if (!$this->tanggal_selesai) {
            return 0;
        }

        $start = $this->tanggal_mulai->startOfDay();
        $end = $this->tanggal_selesai->endOfDay();

        return $start->diffInHours($end);
    }
}