<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('maintenance_records', function (Blueprint $table) {
            $table->id();
            
            // Relasi (WAJIB)
            $table->foreignId('barang_asset_id')->constrained('barang_assets')->onDelete('cascade');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            
            // Info Maintenance (WAJIB)
            $table->enum('maintenance_type', ['preventive', 'corrective']);
            $table->string('kode_maintenance', 50)->unique()->nullable();
            
            // Jadwal (tanggal_mulai WAJIB, tanggal_selesai OPTIONAL)
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai')->nullable();
            
            // Status (WAJIB dengan default)
            $table->enum('status', ['scheduled', 'in_progress', 'completed', 'cancelled'])->default('scheduled');
            $table->enum('priority', ['low', 'medium', 'high', 'critical'])->default('medium');
            
            // Teknisi (SEMUA OPTIONAL - bisa internal atau vendor)
            $table->foreignId('teknisi_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('teknisi_nama')->nullable(); // Auto-filled dari users.name
            $table->string('teknisi_kontak', 50)->nullable();
            $table->string('vendor_name')->nullable(); // Untuk vendor external
            
            // Biaya (akan di-calculate otomatis dari items)
            $table->decimal('total_biaya_material', 15, 2)->default(0);
            $table->decimal('total_biaya_jasa', 15, 2)->default(0);
            $table->decimal('biaya_lain_lain', 15, 2)->default(0);
            $table->decimal('total_biaya', 15, 2)->default(0);
            
            // Detail (SEMUA OPTIONAL)
            $table->text('masalah_ditemukan')->nullable();
            $table->text('tindakan_dilakukan')->nullable();
            $table->text('hasil_maintenance')->nullable();
            $table->text('catatan')->nullable();
            
            // Files (OPTIONAL)
            $table->string('foto_sebelum')->nullable();
            $table->string('foto_sesudah')->nullable();
            
            // Tracking (OPTIONAL)
            $table->integer('downtime_hours')->default(0);
            $table->date('next_maintenance_date')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index('maintenance_type');
            $table->index('status');
            $table->index(['tanggal_mulai', 'tanggal_selesai']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('maintenance_records');
    }
};