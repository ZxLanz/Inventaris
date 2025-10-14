<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('barang_assets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('barang_id')
                  ->constrained('barangs')
                  ->onDelete('cascade');
            $table->string('kode_asset')->unique();
            $table->foreignId('lokasi_id')
                  ->constrained('lokasis')
                  ->onUpdate('cascade');
            $table->enum('kondisi', ['Baik', 'Rusak Ringan', 'Rusak Berat'])->default('Baik');
            $table->enum('status', ['tersedia', 'dipinjam', 'maintenance', 'rusak'])->default('tersedia');
            $table->date('tanggal_pengadaan');
            $table->string('gambar')->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('barang_assets');
    }
};
