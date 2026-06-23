<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MasterMaterialPembantu;
use App\Models\Category;
use App\Models\MutasiBarang;

class MaterialPembantuController extends Controller
{
    public function index()
    {
        $items = MasterMaterialPembantu::all();

        // PENTING: pastikan model Category punya relasi materialPembantus()
        // yang mengarah ke MasterMaterialPembantu (bukan ke Material/bahan pokok).
        // Jika relasi 'materials' di Category masih mengarah ke tabel `materials`
        // (bahan pokok), dropdown Sub-Kategori -> Item di view akan kosong/salah.
        $categories = Category::with('materialPembantus')
            ->where('kelompok_material', 'Material Pembantu')
            ->get();

        $mutasiks = MutasiBarang::latest()->get();

        return view('admin.material.pembantu', compact('categories', 'mutasiks', 'items'));
    }

    public function store(Request $request)
    {
        // 1. Validasi
        $request->validate([
            'item_material'    => 'required|exists:master_material_pembantus,id',
            'jenis_transaksi'  => 'required|string',
            'kuantitas'        => 'required|numeric',
            'tanggal'          => 'required|date',
            'merk'             => 'nullable|string|max:255',
            'spesifikasi'      => 'nullable|string|max:255',
            'satuan_input'     => 'nullable|string|max:255',
            'asal_atau_proyek' => 'nullable|string|max:255',
        ]);

        $material = MasterMaterialPembantu::findOrFail($request->item_material);

        // 2. Simpan ke log transaksi
        MutasiBarang::create([
            'material_id'        => $material->id,
            'kategori_material'  => $material->jenis_material,
            'jenis_transaksi'    => $request->jenis_transaksi,
            'kuantitas'          => $request->kuantitas,
            'tanggal'            => $request->tanggal,
            'merk'               => $request->merk,
            'spesifikasi'        => $request->spesifikasi,
            'satuan_input'       => $request->satuan_input,
            'asal_atau_proyek'   => $request->asal_atau_proyek,
            'keterangan'         => trim(
                ($request->merk ? "Merek: {$request->merk}" : '') .
                ($request->spesifikasi ? " | Spek: {$request->spesifikasi}" : '')
            ),
        ]);

        // 3. Update stok_sekarang otomatis berdasarkan jenis transaksi
        if ($request->jenis_transaksi === 'Barang Keluar') {
            $material->decrement('stok_sekarang', $request->kuantitas);
        } else {
            // "Barang Masuk" maupun "Stok Awal" sama-sama menambah stok
            $material->increment('stok_sekarang', $request->kuantitas);
        }

        return redirect()->back()->with('success', 'Data mutasi bahan pembantu berhasil disimpan!');
    }
}
