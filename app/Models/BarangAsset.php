<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BarangAsset extends Model
{
    use HasFactory;

    protected $table = 'barang_assets';

    protected $guarded = ['id'];

    protected $casts = [
        'tanggal_pengadaan' => 'date',
    ];

    // ========== RELASI YANG SUDAH ADA ========== 
    
    public function barang(): BelongsTo
    {
        return $this->belongsTo(Barang::class, 'barang_id');
    }

    public function lokasi(): BelongsTo
    {
        return $this->belongsTo(Lokasi::class, 'lokasi_id');
    }

    // ========== 🆕 RELASI BARU - TAMBAHKAN INI ========== 
    
    /**
     * Relationship: Asset belongs to Peminjaman (peminjaman aktif)
     */
    public function peminjaman(): BelongsTo
    {
        return $this->belongsTo(Peminjaman::class, 'peminjaman_id');
    }

    // ========== ACCESSOR (TETAP SAMA) ========== 
    
    public function getIsTersediaAttribute(): bool
    {
        return $this->status === 'tersedia';
    }

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

    public function getKondisiBadgeAttribute(): string
    {
        return match($this->kondisi) {
            'Baik' => 'bg-success',
            'Rusak Ringan' => 'bg-warning text-dark',
            'Rusak Berat' => 'bg-danger',
            default => 'bg-secondary',
        };
    }
}