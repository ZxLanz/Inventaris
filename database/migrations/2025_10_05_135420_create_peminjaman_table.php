<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('peminjaman', function (Blueprint $table) {
            $table->id();
            $table->string('kode_peminjaman', 50)->unique();
            
            // Relasi
            $table->foreignId('barang_id')
                  ->constrained('barangs')
                  ->onUpdate('cascade')
                  ->onDelete('restrict');
            
            $table->foreignId('user_id')
                  ->constrained('users')
                  ->onUpdate('cascade')
                  ->onDelete('restrict')
                  ->comment('User yang input peminjaman');
            
            // Data peminjam (orang yang benar-benar pinjam)
            $table->string('nama_peminjam', 150);
            $table->string('kontak_peminjam', 50)->nullable();
            
            // Detail peminjaman
            $table->integer('jumlah_pinjam');
            $table->date('tanggal_pinjam');
            $table->date('tanggal_jatuh_tempo');
            $table->date('tanggal_kembali')->nullable();
            
            // Status & approval
            $table->enum('status', [
                'Menunggu Approval',
                'Disetujui',
                'Ditolak',
                'Dipinjam',
                'Terlambat',
                'Dikembalikan'
            ])->default('Menunggu Approval');
            
            $table->foreignId('approved_by')
                  ->nullable()
                  ->constrained('users')
                  ->onUpdate('cascade')
                  ->onDelete('set null');
            
            $table->timestamp('approved_at')->nullable();
            
            // Denda (fixed Rp 5.000/hari)
            $table->decimal('total_denda', 10, 2)->default(0);
            
            // Keterangan
            $table->text('keterangan')->nullable();
            $table->text('alasan_ditolak')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('peminjaman');
    }
};
