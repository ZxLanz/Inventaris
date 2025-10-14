<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
<<<<<<< HEAD
=======
    /**
     * Run the migrations.
     */
>>>>>>> 7128ee3caecc07cd0adb1d836df3fe5b20ca7d83
    public function up(): void
    {
        Schema::create('barangs', function (Blueprint $table) {
            $table->id();
<<<<<<< HEAD
            $table->enum('jenis', ['asset', 'consumable'])->default('asset');
            $table->string('kode_barang', 50)->nullable()->unique();
=======
            $table->string('kode_barang', 50)->unique();
>>>>>>> 7128ee3caecc07cd0adb1d836df3fe5b20ca7d83
            $table->string('nama_barang', 150);
            
            $table->foreignId('kategori_id')
                  ->constrained('kategoris')
                  ->onUpdate('cascade')
                  ->onDelete('restrict');
                  
            $table->foreignId('lokasi_id')
                  ->constrained('lokasis')
                  ->onUpdate('cascade')
                  ->onDelete('restrict');
                  
            $table->integer('jumlah')->default(0);
            $table->string('satuan', 20);
            $table->enum('kondisi', ['Baik', 'Rusak Ringan', 'Rusak Berat'])->default('Baik');
<<<<<<< HEAD
            $table->date('tanggal_pengadaan');
=======
            $table->date('tanggal_pengadaan'); // TAMBAHKAN BARIS INI
>>>>>>> 7128ee3caecc07cd0adb1d836df3fe5b20ca7d83
            $table->text('keterangan')->nullable();
            $table->string('gambar')->nullable();
            $table->timestamps();
        });
    }

<<<<<<< HEAD
=======
    /**
     * Reverse the migrations.
     */
>>>>>>> 7128ee3caecc07cd0adb1d836df3fe5b20ca7d83
    public function down(): void
    {
        Schema::dropIfExists('barangs');
    }
};