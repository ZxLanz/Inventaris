<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\BarangAssetController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\LokasiController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PeminjamanController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    // Profile routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Barang laporan route (harus sebelum resource)
    Route::get('/barang/laporan', [BarangController::class, 'cetakLaporan'])->name('barang.laporan');
    
    // BarangAsset routes (untuk hapus & update status individual asset)
    Route::delete('/barang-asset/{barangAsset}', [BarangAssetController::class, 'destroy'])
        ->name('barang-asset.destroy');
    Route::patch('/barang-asset/{barangAsset}/status', [BarangAssetController::class, 'updateStatus'])
        ->name('barang-asset.update-status');
    Route::get('/barang-asset/{barangAsset}', [BarangAssetController::class, 'show'])
        ->name('barang-asset.show');
    Route::get('/barang-asset/{barangAsset}/edit', [BarangAssetController::class, 'edit'])
        ->name('barang-asset.edit');
    Route::put('/barang-asset/{barangAsset}', [BarangAssetController::class, 'update'])
        ->name('barang-asset.update');
    
    // Peminjaman routes (sebelum resource)
    Route::get('/peminjaman/laporan', [PeminjamanController::class, 'cetakLaporan'])->name('peminjaman.laporan');
    Route::post('/peminjaman/{peminjaman}/approve', [PeminjamanController::class, 'approve'])->name('peminjaman.approve');
    Route::post('/peminjaman/{peminjaman}/reject', [PeminjamanController::class, 'reject'])->name('peminjaman.reject');
    
    // Return routes
    Route::get('/peminjaman/{peminjaman}/return', [PeminjamanController::class, 'returnForm'])->name('peminjaman.return');
    Route::post('/peminjaman/{peminjaman}/return', [PeminjamanController::class, 'processReturn'])->name('peminjaman.processReturn');
    
    // API untuk fetch assets
    Route::get('/api/peminjaman/get-assets', [PeminjamanController::class, 'getAssetsByBarang'])->name('peminjaman.getAssets');
    
    // Resource routes
    Route::resource('user', UserController::class);
    Route::resource('kategori', KategoriController::class);
    Route::resource('lokasi', LokasiController::class);
    Route::resource('barang', BarangController::class);
    Route::resource('peminjaman', PeminjamanController::class)->except(['edit', 'update', 'destroy']);
});

require __DIR__.'/auth.php';
