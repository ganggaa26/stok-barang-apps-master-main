<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Imports\StokAwalImport;
use App\Models\Material;
use Maatwebsite\Excel\Facades\Excel;

class ImportController extends Controller
{
   public function form()
{
    $daftarKategoriPerKelompok = [
        'pokok' => \App\Models\Category::where('kelompok_material', 'LIKE', '%pokok%')
            ->get(['id', 'nama_Kategori']),
        'pembantu' => \App\Models\Category::where('kelompok_material', 'Material Pembantu')
            ->get(['id', 'nama_Kategori']),
    ];

    $semuaMaterialPokok = \App\Models\Material::all(['id', 'nama_material', 'category_id']);
    $semuaMaterialPembantu = \App\Models\MasterMaterialPembantu::all(['id', 'nama_material', 'category_id']);

    return view('admin.material.import', compact(
        'daftarKategoriPerKelompok',
        'semuaMaterialPokok',
        'semuaMaterialPembantu'
    ));
}

    public function store(Request $request)
    {
        $request->validate([
            'kelompok_material' => 'required|in:pokok,pembantu',
            'item_id'           => 'required|integer',
            'file_excel'        => 'required|file|mimes:xlsx,xls',
        ]);

         $tipeKalkulasi = 'volume_kayu';
    if ($request->kelompok_material === 'pokok') {
        $material = \App\Models\Material::findOrFail($request->item_id);
        $tipeKalkulasi = optional($material->kategori)->tipe_kalkulasi ?? 'volume_kayu';
    }

          $import = new StokAwalImport($request->kelompok_material, $request->item_id, $tipeKalkulasi);
    Excel::import($import, $request->file('file_excel'));

    $errors = $import->getErrorRows();
    if (!empty($errors)) {
        return redirect()->route('material.import.form')
            ->with('error', 'Selesai dengan ' . count($errors) . ' baris dilewati: ' . implode('; ', array_slice($errors, 0, 5)));
    }

    return redirect()->route('material.import.form')
        ->with('success', 'Import berhasil! Data langsung muncul di halaman Input Stok.');
}
}