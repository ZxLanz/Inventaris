<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

use Illuminate\Database\Eloquent\Relations\HasMany;



class Barang extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'tanggal_pengadaan' => 'date',
    ];


    /**
     * Relationship: Barang belongs to Kategori
     */


    public function kategori(): BelongsTo
    {
        return $this->belongsTo(Kategori::class, 'kategori_id');
    }


    /**
     * Relationship: Barang belongs to Lokasi
     */


    public function lokasi(): BelongsTo
    {
        return $this->belongsTo(Lokasi::class, 'lokasi_id');
    }


    /**
     * Relationship: Barang has many Peminjaman
     */
    public function peminjaman(): HasMany
    {
        return $this->hasMany(Peminjaman::class, 'barang_id');
    }

    /**
     * Relationship: Barang has many BarangAsset (untuk jenis asset)
     */
    public function assets(): HasMany
    {
        return $this->hasMany(BarangAsset::class, 'barang_id');
    }

    /**
     * Accessor: Cek apakah barang ini asset atau consumable
     */
    public function getIsAssetAttribute(): bool
    {
        return $this->jenis === 'asset';
    }

    public function getIsConsumableAttribute(): bool
    {
        return $this->jenis === 'consumable';
    }

    /**
     * Accessor: Hitung total unit asset (untuk asset)
     */
    public function getTotalAssetAttribute(): int
    {
        if ($this->is_asset) {
            return $this->assets()->count();
        }
        return $this->jumlah;
    }

    /**
     * Accessor: Hitung asset tersedia
     */
    public function getAssetTersediaAttribute(): int
    {
        if ($this->is_asset) {
            return $this->assets()->where('status', 'tersedia')->count();
        }
        return $this->stok_tersedia;
    }

    /**
     * Accessor: Hitung asset dipinjam
     */
    public function getAssetDipinjamAttribute(): int
    {
        if ($this->is_asset) {
            return $this->assets()->where('status', 'dipinjam')->count();
        }
        return 0;
    }

    /**
     * Accessor: Hitung stok tersedia (untuk consumable)
     */
    public function getStokTersediaAttribute(): int
    {
        if ($this->is_consumable) {
            $dipinjam = $this->peminjaman()
                ->whereIn('status', ['Disetujui', 'Dipinjam', 'Terlambat'])
                ->sum('jumlah_pinjam');
            
            return $this->jumlah - $dipinjam;
        }
        
        return 0;
    }

    /**
     * BARU - Method: Generate kode asset berikutnya berdasarkan prefix
     * Contoh: prefix "LP" + urutan terakhir → LP-001, LP-002, dst
     */
    public function generateNextAssetCode(): string
    {
        if (!$this->prefix) {
            throw new \Exception('Prefix tidak boleh kosong');
        }

        // Ambil nomor terakhir dari assets yang ada
        $lastAsset = $this->assets()
            ->where('kode_asset', 'like', $this->prefix . '-%')
            ->orderByRaw('CAST(SUBSTRING_INDEX(kode_asset, "-", -1) AS UNSIGNED) DESC')
            ->first();
        
        if ($lastAsset) {
            // Extract nomor dari kode: LP-005 → 5
            preg_match('/-(\d+)$/', $lastAsset->kode_asset, $matches);
            $lastNumber = isset($matches[1]) ? intval($matches[1]) : 0;
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }
        
        // Format: PREFIX-001, PREFIX-002, dst (3 digit)
        return $this->prefix . '-' . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
    }

    /**
     * BARU - Method: Generate multiple asset codes sekaligus
     * @param int $jumlah - Berapa unit yang mau dibuat
     * @return array - Array of generated codes
     */
    public function generateMultipleAssetCodes(int $jumlah): array
    {
        $codes = [];
        
        for ($i = 0; $i < $jumlah; $i++) {
            $codes[] = $this->generateNextAssetCode();
            
            // Simulate insert untuk increment counter
            // (akan di-override saat actual insert di controller)
            $this->assets()->make(['kode_asset' => end($codes)]);
        }
        
        return $codes;
    }

    /**
     * BARU - Method: Cek apakah ada asset yang sedang dipinjam
     * @return bool
     */
    public function hasAssetDipinjam(): bool
    {
        if ($this->is_asset) {
            return $this->assets()->where('status', 'dipinjam')->exists();
        }
        
        // Untuk consumable, cek dari peminjaman
        return $this->peminjaman()
            ->whereIn('status', ['Disetujui', 'Dipinjam', 'Terlambat'])
            ->exists();
    }

    /**
     * BARU - Method: Get daftar asset yang dipinjam
     * @return \Illuminate\Support\Collection
     */
    public function getAssetDipinjamList()
    {
        if ($this->is_asset) {
            return $this->assets()
                ->where('status', 'dipinjam')
                ->get()
                ->pluck('kode_asset');
        }
        
        return collect();
    }


}
