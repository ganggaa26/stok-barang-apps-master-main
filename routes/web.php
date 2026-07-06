<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\MaterialController;
use App\Http\Controllers\MaterialPembantuController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\LaporanController; // Controller laporan kita panggil di sini
use App\Models\MutasiBarang;

Route::get('/', function () {
    return view('welcome');
});

// Semua Route yang membutuhkan Login (Autentikasi)
Route::middleware('auth')->group(function () {
    
    // --- PROFILE USER ---
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // --- DASHBOARD ---
    Route::get('/dashboard', function () {
        $totalMaterial = DB::table('materials')->count();

        $totalTransaksi = DB::table('mutasi_barangs')
            ->whereMonth('tanggal', now()->month)
            ->whereYear('tanggal', now()->year)
            ->count();

        $materialMenipis = DB::table('materials')
            ->whereRaw('stok_sekarang <= stok_minimum')
            ->get();

        return view('dashboard', compact(
            'totalMaterial',
            'totalTransaksi',
            'materialMenipis'
        ));
    })->middleware(['auth', 'verified'])->name('dashboard');

    // --- MATERIAL POKOK (Menggunakan MaterialController) ---
    Route::get('/material/pokok', [MaterialController::class, 'index'])->name('material.pokok');
    Route::post('/material/pokok/store', [MaterialController::class, 'store'])->name('material.pokok.store');
    Route::get('/material/pokok/{id}/edit', [MaterialController::class, 'edit'])->name('material.pokok.edit');
    Route::put('/material/pokok/{id}', [MaterialController::class, 'update'])->name('material.pokok.update');
    
    // HAPUS: Kita daftarkan route delete di sini agar tombol hapus berfungsi!
    Route::delete('/material/pokok/{id}', [MaterialController::class, 'destroy'])->name('material.pokok.destroy');

    // --- MATERIAL PEMBANTU (Menggunakan MaterialPembantuController) ---
    Route::get('/material/pembantu', [MaterialPembantuController::class, 'index'])->name('material.pembantu');
    Route::post('/material/pembantu/store', [MaterialPembantuController::class, 'store'])->name('material.pembantu.store');
    Route::get('/material/pembantu/edit/{id}', [MaterialPembantuController::class, 'edit'])->name('material.pembantu.edit');
    Route::put('/material/pembantu/update/{id}', [MaterialPembantuController::class, 'update'])->name('material.pembantu.update');
    Route::delete('/material/pembantu/{id}', [MaterialPembantuController::class, 'destroy'])->name('material.pembantu.destroy');

    // --- MUTASI / TRANSAKSI STOK ---
    Route::post('/material/mutasi', [MaterialController::class, 'storeMutasi'])->name('material.storeMutasi');

    // --- MASTER DATA KATEGORI ---
    Route::get('/material/category', [CategoryController::class, 'index'])->name('material.category');
    Route::get('/material/category/create', [CategoryController::class, 'create'])->name('material.category.create');
    Route::post('/material/category/store', [CategoryController::class, 'store'])->name('material.category.store');

    // --- PELAPORAN (DIALIKHAN KE LAPORAN CONTROLLER AGAR REAL-TIME & BISA FILTER) ---
    
    // 1. Jalur Laporan Stok Utama
    Route::get('/laporan/stok', [LaporanController::class, 'laporanStok'])->name('laporan.stok');

    // 2. Jalur Jurnal Laporan Barang Masuk
Route::get('/laporan/barang-masuk', [LaporanController::class, 'barangMasuk'])->name('laporan.masuk');
    
    // 3. Jalur Jurnal Laporan Barang Keluar
    Route::get('/laporan/barang-keluar', function () {
        $barangKeluarPokok = DB::table('mutasi_barangs')
            ->join('materials', 'mutasi_barangs.material_id', '=', 'materials.id')
            ->where('mutasi_barangs.jenis_transaksi', 'Barang Keluar')
            ->select(
                'mutasi_barangs.*',
                'materials.nama_material',
                'materials.kode_material',
                'materials.satuan',
                DB::raw("'Pokok' as kategori")
            )
            ->get();

        $barangKeluarPembantu = DB::table('mutasi_barangs')
            ->join('master_material_pembantus', 'mutasi_barangs.material_id', '=', 'master_material_pembantus.id')
            ->where('mutasi_barangs.jenis_transaksi', 'Barang Keluar')
            ->select(
                'mutasi_barangs.*',
                'master_material_pembantus.nama_material',
                'master_material_pembantus.kode_material',
                'master_material_pembantus.satuan',
                DB::raw("'Pembantu' as kategori")
            )
            ->get();

        $barangKeluar = $barangKeluarPokok
            ->merge($barangKeluarPembantu)
            ->sortByDesc('tanggal')
            ->values();

        return view('laporan.Barang-keluar', compact('barangKeluar'));
    })->name('laporan.keluar');

});

require __DIR__.'/auth.php';
