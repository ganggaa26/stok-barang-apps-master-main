<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Material;   
use App\Models\Category;
use App\Models\MutasiBarang;
use Illuminate\Support\Str;
use DB;

class MaterialController extends Controller
{
   public function index(Request $request)
{
    $categories = Category::where('kelompok_material', 'LIKE', '%pokok%')->get();

    $materials = Material::with('kategori')->get()->map(function ($item) {
        $item->tipe_kalkulasi = $item->kategori ? $item->kategori->tipe_kalkulasi : 'volume_kayu';
        return $item;
    });

    // Ambil SEMUA riwayat — filter minggu/tanggal ditangani JS di sisi client
    $mutasiks = MutasiBarang::latest()->get();

    return view('admin.material.pokok', compact('categories', 'materials', 'mutasiks'));
}

  public function store(Request $request)
{
    // 1. Validasi Input Form dari JS
    $request->validate([
        'jenis_transaksi'   => 'required|string',
        'tanggal' => 'nullable|date', 
        'material_id'       => 'required', 
        'kuantitas'         => 'required|numeric',
        'qty_fisik'         => 'required|numeric',
        'spesifikasi'       => 'nullable|string',
    ]);

    // 2. Pengaman Tanggal Transaksi Otomatis
    $tanggalRaw = $request->input('tanggal');
    if (empty($tanggalRaw) || str_starts_with($tanggalRaw, '00') || str_starts_with($tanggalRaw, '02')) {
        $tanggalRaw = date('Y-m-d'); 
    }

    // 3. Ambil data master material asal
    $material = Material::findOrFail($request->material_id);
    $namaKategoriMaterial = optional($material->kategori)->nama_Kategori ?? $material->jenis_material ?? 'Tidak Diketahui';
    $textSupplier = trim($request->input('asal_barang') ?? $request->input('asalBarang') ?? $request->input('asal_supplier') ?? '');
    $textProyek = trim($request->input('nama_proyek') ?? $request->input('namaProyek') ?? '');

    if ($request->jenis_transaksi === 'Barang Keluar') {
        $asalSupplierFinal = null;
        $namaProyekFinal = ($textProyek !== '') ? $textProyek : 'General';
    } else {
        // Jika Barang Masuk atau Stok Awal, proyek WAJIB NULL (jadi strip di view)
        $namaProyekFinal = null; 

        if ($textSupplier !== '') {
            $asalSupplierFinal = $textSupplier;
        } else {
            // JIKA MAU STOK AWAL: Namanya disesuaikan jadi "Restock Stok Awal"
            if ($request->jenis_transaksi === 'Stok Awal' || $request->jenis_transaksi === 'Stok Awal Gudang') {
                $asalSupplierFinal = 'Restock Stok Awal';
            } else {
                $asalSupplierFinal = 'Restock Umum';
            }
        }
    }

    // 5. Simpan data ke database
    MutasiBarang::create([
        'material_id'        => $material->id,
        'kategori_material'  =>$namaKategoriMaterial, 
        'jenis_transaksi'    => $request->jenis_transaksi,
        'tangal'            => $tanggalRaw, // Menyesuaikan nama kolom 'tanggal' di DB kamu
        'tanggal'            => $tanggalRaw,
        
        'tebal'              => $request->input('tebal'),
        'lebar'              => $request->input('lebar'),
        'panjang'            => $request->input('panjang'),
        'qty_fisik'          => $request->qty_fisik,   
        'kuantitas'          => $request->kuantitas,   
        'spesifikasi_lokasi' => $request->spesifikasi, 
        'lokasi_gudang'      => $request->input('lokasi_gudang'),
        
        'asal_supplier'      => $asalSupplierFinal,
        'nama_proyek'        => $namaProyekFinal,
    ]);

    // 6. Logika Update Stok Riil Dinamis pada Master Material Pokok
    if ($request->jenis_transaksi === 'Barang Masuk' || $request->jenis_transaksi === 'Stok Awal' || $request->jenis_transaksi === 'Stok Awal Gudang') {
        $material->increment('stok_sekarang', $request->kuantitas);
    } else if ($request->jenis_transaksi === 'Barang Keluar') {
        $material->decrement('stok_sekarang', $request->kuantitas);
    }

    return redirect()->route('material.pokok')->with('success', 'Jurnal Transaksi Material Pokok Berhasil Disimpan!');
}
    public function update(Request $request, $id)
{
    $mutasi = MutasiBarang::findOrFail($id);
    $material = Material::findOrFail($mutasi->material_id);
    $textSupplier = trim($request->input('asal_barang') ?? $request->input('asalBarang') ?? $request->input('asal_supplier') ?? '');
    $textProyek = trim($request->input('nama_proyek') ?? $request->input('namaProyek') ?? '');

    if ($request->jenis_transaksi === 'Barang Keluar') {
        $asalSupplierFinal = null;
        $namaProyekFinal = ($textProyek !== '') ? $textProyek : 'General';
    } else {
        $namaProyekFinal = null;
        
        if ($textSupplier !== '') {
            $asalSupplierFinal = $textSupplier;
        } else {
            // Amankan data lama yang sudah ada di database agar tidak kembali jadi strip
            if (!empty($mutasi->asal_supplier)) {
                $asalSupplierFinal = $mutasi->asal_supplier;
            } else {
                if ($request->jenis_transaksi === 'Stok Awal' || $request->jenis_transaksi === 'Stok Awal Gudang') {
                    $asalSupplierFinal = 'Restock Stok Awal';
                } else {
                    $asalSupplierFinal = 'Restock Umum';
                }
            }
        }
    }

    // 2. Update ke database
    $mutasi->update([
        'jenis_transaksi'    => $request->jenis_transaksi,
        'tanggal'            => $request->input('tanggal') ?? $mutasi->tanggal,
        'tebal'              => $request->input('tebal') ?? $mutasi->tebal,
        'lebar'              => $request->input('lebar') ?? $mutasi->lebar,
        'panjang'            => $request->input('panjang') ?? $mutasi->panjang,
        'qty_fisik'          => $request->input('qty_fisik') ?? $mutasi->qty_fisik,
        'kuantitas'          => $request->input('kuantitas') ?? $mutasi->kuantitas,
        'spesifikasi_lokasi' => $request->input('spesifikasi_lokasi') ?? $mutasi->spesifikasi_lokasi,
        'lokasi_gudang'      => $request->input('lokasi_gudang') ?? $mutasi->lokasi_gudang,
        
        'asal_supplier'      => $asalSupplierFinal,
        'nama_proyek'        => $namaProyekFinal,
    ]);

    return redirect()->route('material.pokok')->with('success', 'Data Transaksi Berhasil Diperbarui!');
}

    public function destroy($id)
        {
            $mutasi = MutasiBarang::findOrFail($id);
            $material = Material::findOrFail($mutasi->material_id);

            // Menyesuaikan ulang stok master agar tidak pincang setelah log dihapus
            if ($mutasi->jenis_transaksi === 'Barang Masuk' || $mutasi->jenis_transaksi === 'Stok Awal' || $mutasi->jenis_transaksi === 'Stok Awal Gudang') {
                $material->decrement('stok_sekarang', $mutasi->kuantitas);
            } else if ($mutasi->jenis_transaksi === 'Barang Keluar') {
                $material->increment('stok_sekarang', $mutasi->kuantitas);
            }

            $mutasi->delete();

            return redirect()->route('material.pokok')->with('success', 'Riwayat transaksi berhasil dihapus!');
        }
}