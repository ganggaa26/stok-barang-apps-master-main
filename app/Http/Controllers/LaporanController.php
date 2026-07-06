<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MutasiBarang;
use App\Models\Material; 
use Illuminate\Support\Facades\DB;

class LaporanController extends Controller
{
    /**
     * Menampilkan Jurnal Laporan Barang Masuk & Stok Awal
     */
   public function barangMasuk()
{
    // Menggunakan Eloquent agar bisa membaca relasi material sampai ke level master kategorinya
    $barangMasuk = MutasiBarang::with(['material.kategori', 'materialPembantu'])
        ->whereIn('jenis_transaksi', ['Barang Masuk', 'Stok Awal'])
        ->orderBy('tanggal', 'desc')
        ->get();

    // Hitung data info card atas
    $totalTransaksiTerfilter = $barangMasuk->count();
    
    $mutasiMaterialPokok = MutasiBarang::whereIn('jenis_transaksi', ['Barang Masuk', 'Stok Awal'])
        ->whereHas('material')
        ->count();

    $mutasiBahanPembantu = MutasiBarang::whereIn('jenis_transaksi', ['Barang Masuk', 'Stok Awal'])
        ->whereHas('materialPembantu')
        ->count();

    // Kirim data ke view blade
    return view('laporan.barang-masuk', compact(
        'barangMasuk', 
        'totalTransaksiTerfilter', 
        'mutasiMaterialPokok', 
        'mutasiBahanPembantu'
    ));
}

    public function laporanStok(Request $request)
    {
        // Ubah nama penampung menjadi $laporanStok agar sinkron dengan blade kamu
        $laporanStok = Material::with(['kategori'])
            ->withSum(['mutasis as total_masuk' => function ($query) {
                $query->whereIn('jenis_transaksi', ['Barang Masuk', 'Stok Awal', 'Stok Awal Gudang']);
            }], 'kuantitas')
            ->withSum(['mutasis as total_keluar' => function ($query) {
                $query->where('jenis_transaksi', 'Barang Keluar');
            }], 'kuantitas')
            ->get()
            ->map(function ($item) {
                // Tambahkan properti custom jika di blade kamu membutuhkan kolom ini
                $item->kategori = 'Pokok'; 
                $item->kode = $item->kode_material;
                $item->nama = $item->nama_material;
                return $item;
            });

        // Lempar variabel $laporanStok ke view laporan.stok
        return view('laporan.stok', compact('laporanStok'));
    }
} 