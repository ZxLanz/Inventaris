<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class Peminjaman extends Model
{
    use HasFactory;

    protected $table = 'peminjaman';

    protected $guarded = ['id'];

    protected $casts = [
        'tanggal_pinjam' => 'date',
        'tanggal_jatuh_tempo' => 'date',
        'tanggal_kembali' => 'date',
        'approved_at' => 'datetime',
        'total_denda' => 'decimal:2',
    ];

    // ========== RELASI YANG SUDAH ADA ========== 
    
    public function barang(): BelongsTo
    {
        return $this->belongsTo(Barang::class, 'barang_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // ========== 🆕 RELASI BARU - TAMBAHKAN INI ========== 
    
    /**
     * Relationship: Peminjaman belongs to BarangAsset (untuk asset)
     */
    public function barangAsset(): BelongsTo
    {
        return $this->belongsTo(BarangAsset::class, 'barang_asset_id');
    }

    // ========== ACCESSOR & SCOPE (TETAP SAMA) ========== 
    
    public function getHariTerlambatAttribute(): int
    {
        if ($this->status === 'Dikembalikan' && $this->tanggal_kembali) {
            $jatuhTempo = Carbon::parse($this->tanggal_jatuh_tempo);
            $kembali = Carbon::parse($this->tanggal_kembali);
            
            if ($kembali->gt($jatuhTempo)) {
                return $kembali->diffInDays($jatuhTempo);
            }
        } elseif (in_array($this->status, ['Dipinjam', 'Terlambat'])) {
            $jatuhTempo = Carbon::parse($this->tanggal_jatuh_tempo);
            $today = Carbon::today();
            
            if ($today->gt($jatuhTempo)) {
                return $today->diffInDays($jatuhTempo);
            }
        }
        
        return 0;
    }

    public function getDendaAttribute(): float
    {
        return $this->hari_terlambat * 5000;
    }

    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeAktif($query)
    {
        return $query->whereIn('status', ['Disetujui', 'Dipinjam', 'Terlambat']);
    }

    public function scopeMenungguApproval($query)
    {
        return $query->where('status', 'Menunggu Approval');
    }

    public static function generateKode(): string
    {
        $date = date('Ymd');
        $lastKode = self::whereDate('created_at', today())
                        ->orderBy('id', 'desc')
                        ->first();
        
        if ($lastKode) {
            $lastNumber = intval(substr($lastKode->kode_peminjaman, -3));
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }
        
        return 'PJM-' . $date . '-' . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
    }
}