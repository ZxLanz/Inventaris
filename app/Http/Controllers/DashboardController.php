<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Kategori;
use App\Models\Lokasi;
use App\Models\User;
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

        // Kondisi Balik
        $kondisiBaik = Barang::where('kondisi', 'Baik')->count();
        $kondisiRusakRingan = Barang::where('kondisi', 'Rusak Ringan')->count();
        $kondisiRusakBerat = Barang::where('kondisi', 'Rusak Berat')->count();

        // Barang Terbaru
        $barangTerbaru = Barang::with(['kategori', 'lokasi'])->latest()->take(5)->get();

        return view('dashboard', compact(
            'jumlahBarang',
            'jumlahKategori', 
            'jumlahLokasi',
            'jumlahUser',
            'kondisiBaik',
            'kondisiRusakRingan',
            'kondisiRusakBerat',
            'barangTerbaru'
        ));
    }
}