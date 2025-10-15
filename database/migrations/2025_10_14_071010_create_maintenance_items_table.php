<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('maintenance_items', function (Blueprint $table) {
            $table->id();
            
            // Relasi
            $table->foreignId('maintenance_record_id')->constrained('maintenance_records')->onDelete('cascade');
            
            // Detail Item
            $table->string('nama_item');
            $table->text('deskripsi')->nullable();
            $table->enum('kategori_item', [
                'pengecekan',
                'pembersihan', 
                'perbaikan',
                'penggantian',
                'upgrade',
                'instalasi',
                'konfigurasi',
                'testing',
                'lainnya'
            ])->default('lainnya');
            
            // Status
            $table->boolean('is_completed')->default(false);
            $table->integer('urutan')->default(0);
            
            // Biaya
            $table->decimal('biaya_material', 15, 2)->default(0);
            $table->decimal('biaya_jasa', 15, 2)->default(0);
            $table->decimal('subtotal', 15, 2)->default(0);
            
            // Catatan
            $table->text('catatan')->nullable();
            
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            
            // Indexes
            $table->index('maintenance_record_id');
            $table->index('is_completed');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('maintenance_items');
    }
};