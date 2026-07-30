<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\MaterialController;
use App\Http\Controllers\MaterialPembantuController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\LaporanController; 
use App\Http\Controllers\UserController;
use App\Models\MutasiBarang;
use App\Http\Controllers\ImportController;

Route::get('/', function () {
    return view('welcome');
});

// Semua Route yang membutuhkan Login (Autentikasi)
Route::middleware('auth')->group(function () {
    
    // --- PROFILE USER ---
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::post('/profile/avatar', [ProfileController::class, 'updateAvatar'])->name('profile.avatar.update');
    Route::delete('/profile/avatar', [ProfileController::class, 'destroyAvatar'])->name('profile.avatar.destroy');

    // --- DASHBOARD ---
    Route::get('/dashboard', function () {
    // Total material = Pokok + Pembantu
    $totalMaterial = DB::table('materials')->count()
        + DB::table('master_material_pembantus')->count();

    // Barang Masuk bulan ini (Pokok + Pembantu), TIDAK termasuk Stok Awal
    $totalBarangMasuk = DB::table('mutasi_barangs')
            ->where('jenis_transaksi', 'Barang Masuk')
            ->whereMonth('tanggal', now()->month)
            ->whereYear('tanggal', now()->year)
            ->count()
        + DB::table('mutasi_material_pembantus')
            ->where('jenis_transaksi', 'Barang Masuk')
            ->whereMonth('tanggal', now()->month)
            ->whereYear('tanggal', now()->year)
            ->count();

    // Barang Keluar bulan ini (Pokok + Pembantu)
    $totalBarangKeluar = DB::table('mutasi_barangs')
            ->where('jenis_transaksi', 'Barang Keluar')
            ->whereMonth('tanggal', now()->month)
            ->whereYear('tanggal', now()->year)
            ->count()
        + DB::table('mutasi_material_pembantus')
            ->where('jenis_transaksi', 'Barang Keluar')
            ->whereMonth('tanggal', now()->month)
            ->whereYear('tanggal', now()->year)
            ->count();

            // Ambil spesifikasi transaksi TERAKHIR per material (Material Pokok)
        $latestIdPokok = DB::table('mutasi_barangs')
            ->select('material_id', DB::raw('MAX(id) as max_id'))
            ->groupBy('material_id');
        $spekPokokMap = DB::table('mutasi_barangs')
            ->joinSub($latestIdPokok, 'latest', fn($j) => $j->on('mutasi_barangs.id', '=', 'latest.max_id'))
            ->pluck('spesifikasi_lokasi', 'mutasi_barangs.material_id');

        // Ambil spesifikasi transaksi TERAKHIR per material (Material Pembantu)
        $latestIdPembantu = DB::table('mutasi_material_pembantus')
            ->select('material_pembantu_id', DB::raw('MAX(id) as max_id'))
            ->groupBy('material_pembantu_id');
        $spekPembantuMap = DB::table('mutasi_material_pembantus')
            ->joinSub($latestIdPembantu, 'latest', fn($j) => $j->on('mutasi_material_pembantus.id', '=', 'latest.max_id'))
            ->pluck('spesifikasi', 'mutasi_material_pembantus.material_pembantu_id');

   $menipisPokok = DB::table('materials')
    ->leftJoin('categories', 'materials.category_id', '=', 'categories.id')
    ->select('materials.*', 'categories.nama_Kategori as kategori_nama')
    ->where('materials.stok_sekarang', '<=', 0)
    ->get()
    ->map(function ($item) use ($spekPokokMap) {
        $item->spesifikasi_detail = $spekPokokMap[$item->id] ?? null;
        return $item;
    });

    $menipisPembantu = DB::table('master_material_pembantus')
        ->leftJoin('categories', 'master_material_pembantus.category_id', '=', 'categories.id')
        ->select('master_material_pembantus.*', 'categories.nama_Kategori as kategori_nama')
        ->where('master_material_pembantus.stok_sekarang', '<=', 0)
        ->get()
        ->map(function ($item) use ($spekPembantuMap) {
            $item->spesifikasi_detail = $spekPembantuMap[$item->id] ?? null;
            $item->size = null;
            $item->kualitas = null;
            $item->lokasi_gudang = null;
            $item->stok_minimum = $item->stok_minimum ?? 0;
            return $item;
    });

    $semuaMaterialMenipis = $menipisPokok->merge($menipisPembantu);

    // Total ASLI (dipakai di card "Peringatan"), dihitung SEBELUM dipotong 5
    $totalMaterialMenipis = $semuaMaterialMenipis->count();

    // Dashboard cuma nampilin 5 yang PALING kritis
    // (selisih stok_minimum - stok_sekarang paling besar)
    $materialMenipis = $semuaMaterialMenipis
        ->sortByDesc(function ($item) {
            return ($item->stok_minimum ?? 0) - $item->stok_sekarang;
        })
        ->take(5)
        ->values();

    // Data grafik Weekly Transaction Trends: Senin s.d Minggu minggu ini
    $namaHari = ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'];
    $awalMinggu = now()->startOfWeek(); // Senin

    $weeklyLabels = [];
    $weeklyIncoming = [];
    $weeklyOutgoing = [];

    for ($i = 0; $i < 7; $i++) {
        $tanggal = $awalMinggu->copy()->addDays($i);
        $weeklyLabels[] = $namaHari[$i];

        $masuk = DB::table('mutasi_barangs')
                ->where('jenis_transaksi', 'Barang Masuk')
                ->whereDate('tanggal', $tanggal)
                ->count()
            + DB::table('mutasi_material_pembantus')
                ->where('jenis_transaksi', 'Barang Masuk')
                ->whereDate('tanggal', $tanggal)
                ->count();

        $keluar = DB::table('mutasi_barangs')
                ->where('jenis_transaksi', 'Barang Keluar')
                ->whereDate('tanggal', $tanggal)
                ->count()
            + DB::table('mutasi_material_pembantus')
                ->where('jenis_transaksi', 'Barang Keluar')
                ->whereDate('tanggal', $tanggal)
                ->count();

        $weeklyIncoming[] = $masuk;
        $weeklyOutgoing[] = $keluar;
    }

    return view('dashboard', compact(
        'totalMaterial',
        'totalBarangMasuk',
        'totalBarangKeluar',
        'materialMenipis',
        'totalMaterialMenipis',
        'weeklyLabels',
        'weeklyIncoming',
        'weeklyOutgoing'
    ));
})->middleware(['auth', 'verified'])->name('dashboard');

   // --- KHUSUS ADMIN: MATERIAL POKOK, PEMBANTU, KATEGORI ---
    Route::middleware('admin')->group(function () {

        // --- MATERIAL POKOK (Menggunakan MaterialController) ---
        Route::get('/material/pokok', [MaterialController::class, 'index'])->name('material.pokok');
        Route::post('/material/pokok/store', [MaterialController::class, 'store'])->name('material.pokok.store');
        Route::get('/material/pokok/{id}/edit', [MaterialController::class, 'edit'])->name('material.pokok.edit');
        Route::put('/material/pokok/{id}', [MaterialController::class, 'update'])->name('material.pokok.update');
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
        Route::get('/material/category/{id}/edit', [CategoryController::class, 'edit'])->name('material.category.edit');
        Route::put('/material/category/{id}', [CategoryController::class, 'update'])->name('material.category.update');
        Route::delete('/material/category/{id}', [CategoryController::class, 'destroy'])->name('material.category.destroy');

        // --- KHUSUS ADMIN: IMPORT EXCEL ---
    Route::get('/material/import', [ImportController::class, 'form'])->name('material.import.form');
    Route::post('/material/import', [ImportController::class, 'store'])->name('material.import.store');

        // --- KELOLA PENGGUNA (khusus admin) ---
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    });


    // --- PELAPORAN (DIALIKHAN KE LAPORAN CONTROLLER AGAR REAL-TIME & BISA FILTER) ---
    
    // 1. Jalur Laporan Stok Utama
    Route::get('/laporan/stok', [LaporanController::class, 'laporanStok'])->name('laporan.stok');

    // 2. Jalur Jurnal Laporan Barang Masuk
    Route::get('/laporan/barang-masuk', [LaporanController::class, 'barangMasuk'])->name('laporan.masuk');
    
    // 3. Jalur Jurnal Laporan Barang Keluar
    Route::get('/laporan/barang-keluar', [LaporanController::class, 'barangKeluar'])->name('laporan.keluar'); 
});



require __DIR__.'/auth.php';
