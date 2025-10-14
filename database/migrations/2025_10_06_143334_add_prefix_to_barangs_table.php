<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Tambah kolom prefix
        Schema::table('barangs', function (Blueprint $table) {
            $table->string('prefix', 10)->nullable()->after('jenis');
        });

        // 2. Migrate data existing - extract prefix dari kode_barang
        DB::table('barangs')
            ->whereNotNull('kode_barang')
            ->where('jenis', 'asset')
            ->get()
            ->each(function ($barang) {
                // Extract prefix: LP001 → LP, ATK-SP-01 → ATK-SP
                preg_match('/^([A-Z-]+)/', $barang->kode_barang, $matches);
                $prefix = $matches[1] ?? null;
                
                if ($prefix) {
                    DB::table('barangs')
                        ->where('id', $barang->id)
                        ->update(['prefix' => $prefix]);
                }
            });

        // 3. Set prefix required untuk asset (tapi tetap nullable untuk backward compatibility)
        // Nanti di validation controller yang enforce required
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('barangs', function (Blueprint $table) {
            $table->dropColumn('prefix');
        });
    }
};
