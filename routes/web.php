<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\LokasiController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('auth')->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->middleware(['auth', 'verified'])
        ->name('dashboard');

    // Profile routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Barang routes
    Route::resource('barang', BarangController::class);

    // Kategori routes (hanya admin yang bisa manage)
    Route::resource('kategori', KategoriController::class)->middleware('role:admin');

    // Lokasi routes (hanya admin yang bisa manage)
    Route::resource('lokasi', LokasiController::class)->middleware('role:admin');

    // User management routes (hanya admin yang bisa manage)
    Route::resource('user', UserController::class)->middleware('role:admin');
});

require __DIR__.'/auth.php';
