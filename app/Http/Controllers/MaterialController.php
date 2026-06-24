<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Material;
use App\Models\Category;
use App\Models\Mutasik;
use App\Models\MutasiBarang;
use App\Models\MutasiSistem;
use Illuminate\Support\Facades\DB;


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
    $request->validate([
        'material_id'      => 'required|exists:materials,id',
        'jenis_transaksi'  => 'required|string',
        'tanggal'          => 'required|date',
        'kuantitas'        => 'required|numeric',

        'nama_proyek'      => 'nullable|string|max:255',
        'asal_supplier'    => 'nullable|string|max:255',
        'nama_produk_jadi' => 'nullable|string|max:255',
        'qty_produksi'     => 'nullable|numeric',

        'qty_fisik'        => 'nullable|numeric',
        'satuan_fisik'     => 'nullable|string|max:255',

        'spesifikasi'      => 'nullable|string|max:255',
        'satuan_input'     => 'nullable|string|max:255',
        'asal_atau_proyek' => 'nullable|string|max:255',
    ]);

    $material = Material::findOrFail($request->material_id);

    MutasiBarang::create([
        'material_id'       => $material->id,
        'kategori_material' => $material->jenis_material,
        'jenis_transaksi'   => $request->jenis_transaksi,
        'kuantitas'         => $request->kuantitas,
        'tanggal'           => $request->tanggal,

        'spesifikasi'       => $request->spesifikasi,
        'satuan_input'      => $request->satuan_input,

        'asal_atau_proyek'  => $request->asal_atau_proyek,

        'nama_proyek'       => $request->nama_proyek,
        'asal_supplier'     => $request->asal_supplier,
        'nama_produk_jadi'  => $request->nama_produk_jadi,
        'qty_produksi'      => $request->qty_produksi,

        'qty_fisik'         => $request->qty_fisik,
        'satuan_fisik'      => $request->satuan_fisik,

        'keterangan'        => $request->spesifikasi,
    ]);

    if ($request->jenis_transaksi === 'Barang Keluar') {
        $material->decrement('stok_sekarang', $request->kuantitas);
    } else {
        $material->increment('stok_sekarang', $request->kuantitas);
    }

    return redirect()->back()->with(
        'success',
        'Data transaksi stok pokok berhasil diamankan!'
    );
}

    public function edit($id)
    {
        return redirect()
            ->route('material.pokok')
            ->with('success', 'Silakan gunakan tombol Edit pada tabel transaksi material pokok.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'material_id'      => 'required|exists:materials,id',
            'jenis_transaksi'  => 'required|string',
            'tanggal'          => 'required|date',
            'kuantitas'        => 'required|numeric',

            'nama_proyek'      => 'nullable|string|max:255',
            'asal_supplier'    => 'nullable|string|max:255',
            'nama_produk_jadi' => 'nullable|string|max:255',
            'qty_produksi'     => 'nullable|numeric',

            'qty_fisik'        => 'nullable|numeric',
            'satuan_fisik'     => 'nullable|string|max:255',

            'spesifikasi'      => 'nullable|string|max:255',
            'satuan_input'     => 'nullable|string|max:255',
            'asal_atau_proyek' => 'nullable|string|max:255',
        ]);

        DB::transaction(function () use ($request, $id) {
            $log = MutasiBarang::findOrFail($id);
            $materialLama = Material::findOrFail($log->material_id);
            $materialBaru = Material::findOrFail($request->material_id);

            $this->balikkanStokMaterial($materialLama, $log->jenis_transaksi, $log->kuantitas);

            $log->update([
                'material_id'       => $materialBaru->id,
                'kategori_material' => $materialBaru->jenis_material,
                'jenis_transaksi'   => $request->jenis_transaksi,
                'kuantitas'         => $request->kuantitas,
                'tanggal'           => $request->tanggal,

                'spesifikasi'       => $request->spesifikasi,
                'satuan_input'      => $request->satuan_input,
                'asal_atau_proyek'  => $request->asal_atau_proyek,

                'nama_proyek'       => $request->nama_proyek,
                'asal_supplier'     => $request->asal_supplier,
                'nama_produk_jadi'  => $request->nama_produk_jadi,
                'qty_produksi'      => $request->qty_produksi,

                'qty_fisik'         => $request->qty_fisik,
                'satuan_fisik'      => $request->satuan_fisik,

                'keterangan'        => $request->spesifikasi,
            ]);

            $this->terapkanStokMaterial($materialBaru, $request->jenis_transaksi, $request->kuantitas);
        });

        return redirect()->route('material.pokok')->with(
            'success',
            'Data transaksi stok pokok berhasil diperbarui!'
        );
    }

    public function destroy($id)
    {
        DB::transaction(function () use ($id) {
            $log = MutasiBarang::findOrFail($id);
            $material = Material::findOrFail($log->material_id);

            $this->balikkanStokMaterial($material, $log->jenis_transaksi, $log->kuantitas);
            $log->delete();
        });

        return redirect()->back()->with('success', 'Data transaksi material pokok berhasil dihapus dari jurnal!');
    }

    private function terapkanStokMaterial(Material $material, string $jenisTransaksi, $kuantitas): void
    {
        if ($jenisTransaksi === 'Barang Keluar') {
            $material->decrement('stok_sekarang', $kuantitas);
            return;
        }

        $material->increment('stok_sekarang', $kuantitas);
    }

    private function balikkanStokMaterial(Material $material, string $jenisTransaksi, $kuantitas): void
    {
        if ($jenisTransaksi === 'Barang Keluar') {
            $material->increment('stok_sekarang', $kuantitas);
            return;
        }

        $material->decrement('stok_sekarang', $kuantitas);
    }


//     public function destroy($id)
// {
//    $log = MutasiBarang::findOrFail($id);

//     // 2. Hapus datanya dari database
//     $log->delete();

//     // 3. Kembalikan ke halaman sebelumnya dengan pesan sukses
//     return redirect()->back()->with('success', 'Data transaksi material pokok berhasil dihapus dari jurnal!');
// }

}
