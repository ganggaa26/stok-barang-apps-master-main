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
    // 1. Data Pokok (logic lama, cuma dirapikan)
    $barangMasukPokok = MutasiBarang::with(['material', 'material.kategori'])
        ->whereIn('jenis_transaksi', ['Barang Masuk', 'Stok Awal'])
        ->get();

    // 2. Data Pembantu — tabel BEDA, di-map biar bentuknya sama kayak Pokok
    $barangMasukPembantu = \App\Models\MutasiMaterialPembantu::with('masterMaterialPembantu')
        ->whereIn('jenis_transaksi', ['Barang Masuk', 'Stok Awal'])
        ->get()
        ->map(function ($item) {
            $item->material = null; // blade cek ini buat nentuin badge Pokok/Pembantu
            $item->materialPembantu = $item->masterMaterialPembantu;
            $item->asal_supplier = $item->asal_atau_proyek; 
            $item->qty_fisik = $item->kuantitas;
            return $item;
        });

    // 3. Gabung & urutkan
    $barangMasuk = $barangMasukPokok
        ->merge($barangMasukPembantu)
        ->sortByDesc('tanggal')
        ->values();

    $totalTransaksiTerfilter = $barangMasuk->count();
    $mutasiMaterialPokok = $barangMasukPokok->count();
    $mutasiBahanPembantu = $barangMasukPembantu->count();

    return view('laporan.barang-masuk', compact(
        'barangMasuk',
        'totalTransaksiTerfilter',
        'mutasiMaterialPokok',
        'mutasiBahanPembantu'
    ));
}

    /**
     * Menampilkan Jurnal Laporan Barang Keluar (SUDAH DIGABUNG & DINAMIS)
     */
   public function barangKeluar()
{
    // 1. Material Pokok (nggak berubah)
    $barangKeluarPokok = DB::table('mutasi_barangs')
        ->join('materials', 'mutasi_barangs.material_id', '=', 'materials.id')
        ->where('mutasi_barangs.jenis_transaksi', 'Barang Keluar')
        ->select(
            'mutasi_barangs.*',
            'materials.nama_material',
            'materials.kode_material',
            'materials.satuan',
            DB::raw("'Material Pokok' as kategori")
        )
        ->get();

    // 2. Material Pembantu — FIX: query dari mutasi_material_pembantus, bukan mutasi_barangs
    $barangKeluarPembantu = DB::table('mutasi_material_pembantus')
        ->join('master_material_pembantus', 'mutasi_material_pembantus.material_pembantu_id', '=', 'master_material_pembantus.id')
        ->where('mutasi_material_pembantus.jenis_transaksi', 'Barang Keluar')
        ->select(
            'mutasi_material_pembantus.*',
            'master_material_pembantus.nama_material',
            'master_material_pembantus.kode_material',
            'master_material_pembantus.satuan',
            DB::raw("'Bahan Pembantu' as kategori")
        )
        ->get()
        ->map(function ($item) {
            $item->nama_proyek = $item->asal_atau_proyek;
            $item->qty_fisik = $item->kuantitas;
            $item->satuan_fisik = $item->satuan;
            $item->satuan_input = $item->satuan;
            $item->nama_produk_jadi = null;
            $item->qty_produksi = null;
            return $item;
        });

    // 3. Gabung & urutkan
    $barangKeluar = $barangKeluarPokok
        ->merge($barangKeluarPembantu)
        ->sortByDesc('tanggal')
        ->values();

    return view('laporan.barang-keluar', compact('barangKeluar'));
}

    /**
     * Menampilkan Ringkasan Laporan Stok Material
     */
   public function laporanStok(Request $request)
{
   $petaSatuanVolume = [
    'volume_kayu'  => 'M³',
    'lembar_board' => 'Lembar',
    'lembar_hpl'   => 'Lembar',
    'luas_veneer'  => 'M²',
];
$petaSatuanFisik = [
    'volume_kayu'  => 'Batang',
    'lembar_board' => 'Lembar',
    'lembar_hpl'   => 'Lembar',
    'luas_veneer'  => 'Lembar',
];

$laporanPokok = Material::with(['kategori'])
    ->withSum(['mutasis as total_masuk' => function ($query) {
        $query->whereIn('jenis_transaksi', ['Barang Masuk', 'Stok Awal', 'Stok Awal Gudang']);
    }], 'kuantitas')
    ->withSum(['mutasis as total_keluar' => function ($query) {
        $query->where('jenis_transaksi', 'Barang Keluar');
    }], 'kuantitas')
    ->withSum(['mutasis as total_qty_fisik_masuk' => function ($query) {
        $query->whereIn('jenis_transaksi', ['Barang Masuk', 'Stok Awal', 'Stok Awal Gudang']);
    }], 'qty_fisik')
    ->withSum(['mutasis as total_qty_fisik_keluar' => function ($query) {
        $query->where('jenis_transaksi', 'Barang Keluar');
    }], 'qty_fisik')
    ->get()
    ->map(function ($item) use ($petaSatuanVolume, $petaSatuanFisik) {
        $item->kategori_label = 'Pokok';
        $item->kode = $item->kode_material;
        $item->nama = $item->nama_material;
        $item->jenis = $item->jenis_material;
        $item->nama_subkategori = $item->kategori->nama_Kategori ?? '-';
        $item->qty_fisik_akhir = ($item->total_qty_fisik_masuk ?? 0) - ($item->total_qty_fisik_keluar ?? 0);
        $item->satuan = $petaSatuanVolume[$item->tipe_kalkulasi] ?? ($item->satuan ?: '-');
        $item->satuan_fisik_tampil = $petaSatuanFisik[$item->tipe_kalkulasi] ?? '-';
        return $item;
    });

    // 2. Data Material Pembantu (tabel BEDA: master_material_pembantus + mutasi_material_pembantus)
    $laporanPembantu = \App\Models\MasterMaterialPembantu::withSum(['mutasi as total_masuk' => function ($query) {
            $query->whereIn('jenis_transaksi', ['Barang Masuk', 'Stok Awal']);
        }], 'kuantitas')
        ->withSum(['mutasi as total_keluar' => function ($query) {
            $query->where('jenis_transaksi', 'Barang Keluar');
        }], 'kuantitas')
        ->get()
        ->map(function ($item) {
        $item->kategori_label = 'Pembantu';
        $item->kode = $item->kode_material;
        $item->nama = $item->nama_material;
        $item->jenis = $item->jenis_material;
        $item->nama_subkategori = $item->kategori->nama_Kategori ?? '-';
        $item->qty_fisik_akhir = ($item->total_qty_fisik_masuk ?? 0) - ($item->total_qty_fisik_keluar ?? 0);
        $item->satuan = $petaSatuanVolume[$item->tipe_kalkulasi] ?? ($item->satuan ?: '-');
        $item->satuan_fisik_tampil = $petaSatuanFisik[$item->tipe_kalkulasi] ?? '-';
    return $item;
        return $item;
});

    // 3. GABUNG di PHP (bukan di database) — ini bagian yang aku maksud tadi
    $laporanStok = $laporanPokok->merge($laporanPembantu);

    return view('laporan.stok', compact('laporanStok'));
}
}