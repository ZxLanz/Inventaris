<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaintenanceItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'maintenance_record_id',
        'nama_item',
        'deskripsi',
        'kategori_item',
        'is_completed',
        'urutan',
        'biaya_material',
        'biaya_jasa',
        'subtotal',
        'catatan',
        'completed_at',
    ];

    protected $casts = [
        'is_completed' => 'boolean',
        'urutan' => 'integer',
        'biaya_material' => 'decimal:2',
        'biaya_jasa' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'completed_at' => 'datetime',
    ];

    // ============================================================
    // RELATIONSHIPS
    // ============================================================
    
    public function maintenanceRecord()
    {
        return $this->belongsTo(MaintenanceRecord::class, 'maintenance_record_id');
    }

    // ============================================================
    // ACCESSORS
    // ============================================================
    
    public function getCompletedBadgeAttribute()
    {
        return $this->is_completed 
            ? '<span class="badge bg-success"><i class="bi bi-check-circle"></i> Selesai</span>'
            : '<span class="badge bg-secondary"><i class="bi bi-clock"></i> Belum</span>';
    }

    public function getKategoriBadgeAttribute()
    {
        $badges = [
            'pengecekan' => 'bg-info',
            'pembersihan' => 'bg-primary',
            'perbaikan' => 'bg-warning',
            'penggantian' => 'bg-danger',
            'upgrade' => 'bg-success',
            'instalasi' => 'bg-purple',
            'konfigurasi' => 'bg-teal',
            'testing' => 'bg-orange',
            'lainnya' => 'bg-secondary',
        ];
        
        $class = $badges[$this->kategori_item] ?? 'bg-secondary';
        $text = ucfirst($this->kategori_item);
        
        return "<span class=\"badge {$class}\">{$text}</span>";
    }

    // ============================================================
    // METHODS
    // ============================================================
    
    /**
     * Hitung subtotal otomatis
     */
    public function calculateSubtotal()
    {
        $this->subtotal = $this->biaya_material + $this->biaya_jasa;
        return $this->subtotal;
    }

    /**
     * Mark item as completed
     */
    public function markAsCompleted()
    {
        $this->is_completed = true;
        $this->completed_at = now();
        $this->save();
        
        // Recalculate parent maintenance total
        $this->maintenanceRecord->recalculateTotalBiaya();
    }

    /**
     * Mark item as incomplete
     */
    public function markAsIncomplete()
    {
        $this->is_completed = false;
        $this->completed_at = null;
        $this->save();
        
        // Recalculate parent maintenance total
        $this->maintenanceRecord->recalculateTotalBiaya();
    }
}
