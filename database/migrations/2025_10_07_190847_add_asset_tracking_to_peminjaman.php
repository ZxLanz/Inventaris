<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tambah kolom di tabel peminjaman
        Schema::table('peminjaman', function (Blueprint $table) {
            // Untuk track unit asset mana yang dipinjam
            $table->foreignId('barang_asset_id')
                  ->nullable()
                  ->after('barang_id')
                  ->constrained('barang_assets')
                  ->onUpdate('cascade')
                  ->onDelete('set null')
                  ->comment('ID unit asset spesifik yang dipinjam (khusus untuk asset)');
            
            // Lokasi tujuan peminjaman
            $table->string('lokasi_tujuan', 200)
                  ->nullable()
                  ->after('kontak_peminjam')
                  ->comment('Kemana barang dipinjam');
        });

        // 2. Tambah kolom di tabel barang_assets
        Schema::table('barang_assets', function (Blueprint $table) {
            // Untuk tracking peminjaman aktif
            $table->foreignId('peminjaman_id')
                  ->nullable()
                  ->after('status')
                  ->constrained('peminjaman')
                  ->onUpdate('cascade')
                  ->onDelete('set null')
                  ->comment('ID peminjaman yang sedang aktif');
        });
    }

    public function down(): void
    {
        Schema::table('peminjaman', function (Blueprint $table) {
            $table->dropForeign(['barang_asset_id']);
            $table->dropColumn(['barang_asset_id', 'lokasi_tujuan']);
        });

        Schema::table('barang_assets', function (Blueprint $table) {
            $table->dropForeign(['peminjaman_id']);
            $table->dropColumn('peminjaman_id');
        });
    }
};
