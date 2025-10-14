<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Kategori;
use App\Models\Lokasi;
use App\Models\User;
use App\Models\Peminjaman;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Hitung total data
        $jumlahBarang = Barang::count();
        $jumlahKategori = Kategori::count();
        $jumlahLokasi = Lokasi::count();
        $jumlahUser = User::count();

        // Kondisi Barang
        $kondisiBaik = Barang::where('kondisi', 'Baik')->count();
        $kondisiRusakRingan = Barang::where('kondisi', 'Rusak Ringan')->count();
        $kondisiRusakBerat = Barang::where('kondisi', 'Rusak Berat')->count();

        // Barang Terbaru
        $barangTerbaru = Barang::with(['kategori', 'lokasi'])->latest()->take(5)->get();

        // === BARU: Statistik Peminjaman ===
        $totalPeminjaman = Peminjaman::count();
        $peminjamanAktif = Peminjaman::whereIn('status', ['Dipinjam', 'Terlambat'])->count();
        $peminjamanTerlambat = Peminjaman::where('status', 'Terlambat')->count();
        $menungguApproval = Peminjaman::where('status', 'Menunggu Approval')->count();
        
        // Peminjaman Terbaru (5 terakhir)
        $peminjamanTerbaru = Peminjaman::with(['barang', 'user'])
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard', compact(
            'jumlahBarang',
            'jumlahKategori', 
            'jumlahLokasi',
            'jumlahUser',
            'kondisiBaik',
            'kondisiRusakRingan',
            'kondisiRusakBerat',
            'barangTerbaru',
            // Peminjaman
            'totalPeminjaman',
            'peminjamanAktif',
            'peminjamanTerlambat',
            'menungguApproval',
            'peminjamanTerbaru'
        ));
    }
}