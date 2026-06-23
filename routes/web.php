<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB; // Ditambahkan agar DB::table() di dashboard tidak error
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\MaterialController;
use App\Http\Controllers\MaterialPembantuController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\CategoryController;

Route::get('/', function () {
    return view('welcome');
});

// Dashboard: Menampilkan notifikasi stok yang menipis
Route::get('/dashboard', function () {
    $materialMenipis = DB::table('materials')
        ->whereRaw('stok_sekarang <= stok_minimum')
        ->get();
    return view('dashboard', compact('materialMenipis'));
})->middleware(['auth', 'verified'])->name('dashboard');

// Semua Route yang membutuhkan Login (Autentikasi)
Route::middleware('auth')->group(function () {
    
    // --- PROFILE USER ---
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // --- MATERIAL POKOK (Menggunakan MaterialController) ---
    Route::get('/material/pokok', [MaterialController::class, 'index'])->name('material.pokok');
    Route::post('/material/pokok/store', [MaterialController::class, 'storeMutasi'])->name('material.pokok.store');
    
    // --- MATERIAL PEMBANTU (Menggunakan MaterialPembantuController) ---
    Route::get('/material/pembantu', [MaterialPembantuController::class, 'index'])->name('material.pembantu');
    Route::post('/material/pembantu/store', [MaterialPembantuController::class, 'store'])->name('material.pembantu.store');

    // --- MUTASI / TRANSAKSI STOK ---
    Route::post('/material/mutasi', [MaterialController::class, 'storeMutasi'])->name('material.storeMutasi');

    // --- SUPPLIER ---
    Route::get('/material/supplier', [SupplierController::class, 'index'])->name('material.supplier');

    // --- MASTER DATA KATEGORI (Fitur Edit & Update Telah Dihapus Sesuai Permintaan Industri) ---
    Route::get('/material/category', [CategoryController::class, 'index'])->name('material.category');
    Route::get('/material/category/create', [CategoryController::class, 'create'])->name('material.category.create');
    Route::post('/material/category/store', [CategoryController::class, 'store'])->name('material.category.store');

    // --- LAPORAN LOGISTIK ---
    Route::get('/laporan/stok', function () {
        return view('laporan.stok');
    })->name('laporan.stok');

    Route::get('/laporan/barang-masuk', function () {
        return view('laporan.Barang-masuk');
    })->name('laporan.masuk');

    Route::get('/laporan/barang-keluar', function () {
        return view('laporan.Barang-keluar');
    })->name('laporan.keluar');
});

require __DIR__.'/auth.php';