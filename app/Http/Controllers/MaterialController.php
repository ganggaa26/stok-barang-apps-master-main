<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Material;
use App\Models\Category;
use App\Models\Mutasik;
use App\Models\MutasiBarang;
use App\Models\MutasiSistem;


class MaterialController extends Controller
{
    public function index()
    {
        // 1. Ambil kategori khusus kelompok Material Pokok
        $categories = Category::where('kelompok_material', 'Material Pokok')->get();

        // 2. Ambil data dari tabel pokok (materials) lewat kolom jenis_material
        $materials = Material::whereIn('jenis_material', $categories->pluck('nama_Kategori'))->get();

        // 3. Ambil riwayat transaksi Bahan Pokok saja.
        // Karena mutasi_barangs dipakai bersama Pokok & Pembantu, kita filter
        // berdasarkan material_id yang ada di tabel materials (bahan pokok).
        $idMaterialPokok = $materials->pluck('id');
        $mutasiks = MutasiBarang::whereIn('material_id', $idMaterialPokok)
            ->latest()
            ->get();

        return view('admin.material.pokok', compact('categories', 'materials', 'mutasiks'));
    }

    public function storeMutasi(Request $request)
    {
        // 1. Validasi Input
        $request->validate([
            'material_id'      => 'required|exists:materials,id',
            'jenis_transaksi'  => 'required|string',
            'tanggal'          => 'required|date',
            'kuantitas'        => 'required|numeric',
            'spesifikasi'      => 'nullable|string|max:255',
            'satuan_input'     => 'nullable|string|max:255',
            'asal_atau_proyek' => 'nullable|string|max:255',
        ]);

        $material = Material::findOrFail($request->material_id);

        // 2. Simpan ke log transaksi
        MutasiBarang::create([
            'material_id'        => $material->id,
            'kategori_material'  => $material->jenis_material,
            'jenis_transaksi'    => $request->jenis_transaksi,
            'kuantitas'          => $request->kuantitas,
            'tanggal'            => $request->tanggal,
            'spesifikasi'        => $request->spesifikasi,
            'satuan_input'       => $request->satuan_input,
            'asal_atau_proyek'   => $request->asal_atau_proyek,
            'keterangan'         => $request->spesifikasi,
        ]);

        // 3. Update stok_sekarang otomatis (sama seperti modul Bahan Pembantu)
        if ($request->jenis_transaksi === 'Barang Keluar') {
            $material->decrement('stok_sekarang', $request->kuantitas);
        } else {
            $material->increment('stok_sekarang', $request->kuantitas);
        }

        return redirect()->back()->with('success', 'Data transaksi stok pokok berhasil diamankan!');
    }

    public function destroy($id)
{
   $log = MutasiBarang::findOrFail($id);

    // 2. Hapus datanya dari database
    $log->delete();

    // 3. Kembalikan ke halaman sebelumnya dengan pesan sukses
    return redirect()->back()->with('success', 'Data transaksi material pokok berhasil dihapus dari jurnal!');
}
}
