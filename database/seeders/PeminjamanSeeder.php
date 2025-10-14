<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PeminjamanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('peminjaman')->insert([
            [
                'kode_peminjaman' => 'PJM-20251001-001',
                'barang_id' => 1, // Laptop Dell
                'user_id' => 2, // Petugas
                'nama_peminjam' => 'Budi Santoso',
                'kontak_peminjam' => '081234567890',
                'jumlah_pinjam' => 2,
                'tanggal_pinjam' => Carbon::now()->subDays(5),
                'tanggal_jatuh_tempo' => Carbon::now()->addDays(2),
                'tanggal_kembali' => null,
                'status' => 'Dipinjam',
                'approved_by' => 1, // Admin
                'approved_at' => Carbon::now()->subDays(5),
                'total_denda' => 0,
                'keterangan' => 'Untuk presentasi proyek',
                'alasan_ditolak' => null,
                'created_at' => Carbon::now()->subDays(5),
                'updated_at' => Carbon::now()->subDays(5),
            ],
            [
                'kode_peminjaman' => 'PJM-20251002-001',
                'barang_id' => 2, // Proyektor
                'user_id' => 1, // Admin
                'nama_peminjam' => 'Ani Wijaya',
                'kontak_peminjam' => 'ani@email.com',
                'jumlah_pinjam' => 1,
                'tanggal_pinjam' => Carbon::now()->subDays(10),
                'tanggal_jatuh_tempo' => Carbon::now()->subDays(3),
                'tanggal_kembali' => null,
                'status' => 'Terlambat',
                'approved_by' => 1,
                'approved_at' => Carbon::now()->subDays(10),
                'total_denda' => 15000, // 3 hari x 5000
                'keterangan' => 'Untuk rapat besar',
                'alasan_ditolak' => null,
                'created_at' => Carbon::now()->subDays(10),
                'updated_at' => Carbon::now(),
            ],
            [
                'kode_peminjaman' => 'PJM-20251003-001',
                'barang_id' => 5, // Kursi Kantor
                'user_id' => 2, // Petugas
                'nama_peminjam' => 'Citra Lestari',
                'kontak_peminjam' => '082345678901',
                'jumlah_pinjam' => 3,
                'tanggal_pinjam' => Carbon::now()->addDays(1),
                'tanggal_jatuh_tempo' => Carbon::now()->addDays(7),
                'tanggal_kembali' => null,
                'status' => 'Menunggu Approval',
                'approved_by' => null,
                'approved_at' => null,
                'total_denda' => 0,
                'keterangan' => 'Untuk acara seminar',
                'alasan_ditolak' => null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'kode_peminjaman' => 'PJM-20250925-001',
                'barang_id' => 6, // Printer
                'user_id' => 1,
                'nama_peminjam' => 'Dedi Kurniawan',
                'kontak_peminjam' => '083456789012',
                'jumlah_pinjam' => 1,
                'tanggal_pinjam' => Carbon::now()->subDays(15),
                'tanggal_jatuh_tempo' => Carbon::now()->subDays(8),
                'tanggal_kembali' => Carbon::now()->subDays(5),
                'status' => 'Dikembalikan',
                'approved_by' => 1,
                'approved_at' => Carbon::now()->subDays(15),
                'total_denda' => 15000, // Terlambat 3 hari
                'keterangan' => 'Untuk cetak dokumen proyek',
                'alasan_ditolak' => null,
                'created_at' => Carbon::now()->subDays(15),
                'updated_at' => Carbon::now()->subDays(5),
            ],
            [
                'kode_peminjaman' => 'PJM-20251004-001',
                'barang_id' => 3, // Meja Rapat
                'user_id' => 2,
                'nama_peminjam' => 'Dinas Pendidikan Kota B',
                'kontak_peminjam' => '022-1234567',
                'jumlah_pinjam' => 2,
                'tanggal_pinjam' => Carbon::now()->subDays(2),
                'tanggal_jatuh_tempo' => Carbon::now()->addDays(5),
                'tanggal_kembali' => null,
                'status' => 'Ditolak',
                'approved_by' => 1,
                'approved_at' => Carbon::now()->subDays(2),
                'total_denda' => 0,
                'keterangan' => 'Untuk acara workshop',
                'alasan_ditolak' => 'Barang sedang digunakan untuk acara internal',
                'created_at' => Carbon::now()->subDays(2),
                'updated_at' => Carbon::now()->subDays(2),
            ],
        ]);
    }
}
