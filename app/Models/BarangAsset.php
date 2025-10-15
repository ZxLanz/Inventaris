<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BarangAsset extends Model
{
    use HasFactory;

    protected $table = 'barang_assets';

    protected $guarded = ['id'];

    protected $casts = [
        'tanggal_pengadaan' => 'date',
    ];

    // ============================================================
    // RELATIONSHIPS
    // ============================================================
    
    /**
     * Asset belongs to Barang (master data)
     */
    public function barang(): BelongsTo
    {
        return $this->belongsTo(Barang::class, 'barang_id');
    }

    /**
     * Asset belongs to Lokasi
     */
    public function lokasi(): BelongsTo
    {
        return $this->belongsTo(Lokasi::class, 'lokasi_id');
    }

    /**
     * Asset belongs to Peminjaman (peminjaman aktif)
     */
    public function peminjaman(): BelongsTo
    {
        return $this->belongsTo(Peminjaman::class, 'peminjaman_id');
    }

    /**
     * 🆕 Asset has many Maintenance Records
     */
    public function maintenanceRecords(): HasMany
    {
        return $this->hasMany(MaintenanceRecord::class, 'barang_asset_id');
    }

    /**
     * 🆕 Get last maintenance record
     */
    public function lastMaintenance()
    {
        return $this->hasOne(MaintenanceRecord::class, 'barang_asset_id')
                    ->latestOfMany('tanggal_selesai');
    }

    // ============================================================
    // ACCESSORS
    // ============================================================
    
    /**
     * Check if asset is available
     */
    public function getIsTersediaAttribute(): bool
    {
        return $this->status === 'tersedia';
    }

    /**
     * Status badge HTML
     */
    public function getStatusBadgeAttribute(): string
    {
        return match($this->status) {
            'tersedia' => 'bg-success',
            'dipinjam' => 'bg-primary',
            'maintenance' => 'bg-warning',
            'rusak' => 'bg-danger',
            default => 'bg-secondary',
        };
    }

    /**
     * Kondisi badge HTML
     */
    public function getKondisiBadgeAttribute(): string
    {
        return match($this->kondisi) {
            'Baik' => 'bg-success',
            'Rusak Ringan' => 'bg-warning text-dark',
            'Rusak Berat' => 'bg-danger',
            default => 'bg-secondary',
        };
    }

    /**
     * 🆕 Get total maintenance cost
     */
    public function getTotalMaintenanceCostAttribute(): float
    {
        return $this->maintenanceRecords()->sum('total_biaya');
    }

    /**
     * 🆕 Get total maintenance count
     */
    public function getTotalMaintenanceCountAttribute(): int
    {
        return $this->maintenanceRecords()->count();
    }

    // ============================================================
    // SCOPES
    // ============================================================
    
    /**
     * 🆕 Scope: Assets yang sedang maintenance
     */
    public function scopeOnMaintenance($query)
    {
        return $query->where('status', 'maintenance');
    }

    /**
     * 🆕 Scope: Assets yang perlu maintenance (overdue)
     */
    public function scopeMaintenanceOverdue($query)
    {
        return $query->whereNotNull('next_maintenance_date')
                     ->where('next_maintenance_date', '<', now())
                     ->where('status', '!=', 'maintenance');
    }
}