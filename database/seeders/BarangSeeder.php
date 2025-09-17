<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BarangSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('barangs')->insert([
            [
                'kode_barang' => 'LP001',
                'nama_barang' => 'Laptop Dell Latitude 5420',
                'kategori_id' => 1,
                'lokasi_id' => 3,
                'jumlah' => 5,
                'satuan' => 'Unit',
                'kondisi' => 'Baik',
                'keterangan' => null,
                'gambar' => null,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'kode_barang' => 'PR001',
                'nama_barang' => 'Proyektor Epson EB-X500T',
                'kategori_id' => 1,
                'lokasi_id' => 1,
                'jumlah' => 2,
                'satuan' => 'Unit',
                'kondisi' => 'Baik',
                'keterangan' => null,
                'gambar' => null,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'kode_barang' => 'MK000',
                'nama_barang' => 'Meja Rapat Kayu Jati',
                'kategori_id' => 2,
                'lokasi_id' => 1,
                'jumlah' => 5,
                'satuan' => 'Buah',
                'kondisi' => 'Baik',
                'keterangan' => null,
                'gambar' => null,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'kode_barang' => 'PPTK-SP-01',
                'nama_barang' => 'Spidol Whiteboard Snowman',
                'kategori_id' => 3,
                'lokasi_id' => 3,
                'jumlah' => 50,
                'satuan' => 'Pcs',
                'kondisi' => 'Baik',
                'keterangan' => null,
                'gambar' => null,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);
    }
}