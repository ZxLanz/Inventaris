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
                'tanggal_pengadaan' => '2023-05-15',
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
                'tanggal_pengadaan' => '2022-11-20',
                'keterangan' => null,
                'gambar' => null,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'kode_barang' => 'MJ005',
                'nama_barang' => 'Meja Rapat Kayu Jati',
                'kategori_id' => 2,
                'lokasi_id' => 1,
                'jumlah' => 5,
                'satuan' => 'Buah',
                'kondisi' => 'Baik',
                'tanggal_pengadaan' => '2021-02-10',
                'keterangan' => null,
                'gambar' => null,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'kode_barang' => 'ATK-SP-01',
                'nama_barang' => 'Spidol Whiteboard Snowman',
                'kategori_id' => 3,
                'lokasi_id' => 3,
                'jumlah' => 50,
                'satuan' => 'Pcs',
                'kondisi' => 'Baik',
                'tanggal_pengadaan' => '2024-01-30',
                'keterangan' => null,
                'gambar' => null,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'kode_barang' => 'KR002',
                'nama_barang' => 'Kursi Kantor Ergonomis',
                'kategori_id' => 2,
                'lokasi_id' => 2,
                'jumlah' => 10,
                'satuan' => 'Unit',
                'kondisi' => 'Baik',
                'tanggal_pengadaan' => '2023-08-25',
                'keterangan' => 'Kursi dengan sandaran punggung adjustable',
                'gambar' => null,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'kode_barang' => 'PR002',
                'nama_barang' => 'Printer Canon Pixma G3010',
                'kategori_id' => 1,
                'lokasi_id' => 2,
                'jumlah' => 3,
                'satuan' => 'Unit',
                'kondisi' => 'Baik',
                'tanggal_pengadaan' => '2023-12-05',
                'keterangan' => 'Printer multifungsi dengan tangki tinta',
                'gambar' => null,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'kode_barang' => 'ATK-PL-01',
                'nama_barang' => 'Pulpen Pilot G2',
                'kategori_id' => 3,
                'lokasi_id' => 1,
                'jumlah' => 100,
                'satuan' => 'Pcs',
                'kondisi' => 'Baik',
                'tanggal_pengadaan' => '2024-02-15',
                'keterangan' => null,
                'gambar' => null,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'kode_barang' => 'AC001',
                'nama_barang' => 'AC Split Daikin 1.5 PK',
                'kategori_id' => 4,
                'lokasi_id' => 1,
                'jumlah' => 2,
                'satuan' => 'Unit',
                'kondisi' => 'Baik',
                'tanggal_pengadaan' => '2022-06-18',
                'keterangan' => 'AC dengan teknologi inverter',
                'gambar' => null,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);
    }
}