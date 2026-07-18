<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Material;
use App\Models\MasterMaterialPembantu;
use App\Models\CalculationType;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class CategoryController extends Controller
{
   public function index()
{
    $categories = Category::with(['materials', 'materialPembantus'])
        ->orderBy('kategori')
        ->orderBy('nama_Kategori')
        ->get();

    return view('admin.material.category', compact('categories'));
}

public function create()
{
    $calculationTypes = CalculationType::all();

    $subCategories = Category::select('nama_Kategori as nama_kategori', 'kategori', 'kelompok_material')
        ->distinct()
        ->get();

    // Daftar Kategori sekarang dikelompokkan per Kelompok Material
    $daftarKategori = [
        'Material Pokok' => [
            'Kayu ',
            'Board',
            'Pelapis',
        ],
        'Material Pembantu' => [
            'Cairan Finishing',
            'Perekat / Lem',
            'Amplas',
            'Sekrup / Hardware',
        ],
    ];

    return view('admin.material.category_create', compact('calculationTypes', 'subCategories', 'daftarKategori'));
}



public function store(Request $request)
{
    $request->validate([
        'kategori'             => 'required|string|max:255',
        'nama_kategori'        => 'required|string|max:255',
        'kelompok_material'    => 'required|string|max:255',
        'satuan_dasar'         => 'required|string|max:50',
        'satuan_kustom_input'  => 'required_if:satuan_dasar,MANUAL|nullable|string|max:50',
        'nama_item_fisik'      => 'required|string|max:255',
        'tipe_kalkulasi'       => 'required|string|max:255',
        'rumus_custom'         => 'required_if:tipe_kalkulasi,CUSTOM_RUMUS|nullable|string|max:255',
        'stok_minimum'         => 'required|numeric|min:0',
    ]);

    $satuanFinal = $request->satuan_dasar === 'MANUAL'
        ? $request->satuan_kustom_input
        : $request->satuan_dasar;

    DB::transaction(function () use ($request, $satuanFinal) {
        $category = Category::firstOrCreate(
            [
                'nama_Kategori'     => $request->nama_kategori,
                'kelompok_material' => $request->kelompok_material,
                'kategori'          => $request->kategori,
            ],
            [
                'satuan_dasar'   => $satuanFinal,
                'tipe_kalkulasi' => $request->tipe_kalkulasi,
                'rumus_custom'   => $request->tipe_kalkulasi === 'CUSTOM_RUMUS' ? $request->rumus_custom : null,
            ]
        );

        $prefix = 'MAT-' . date('ymd') . '-';
        $kodeMaterial = $prefix . strtoupper(Str::random(4));
        $kelompok = strtolower(trim($request->kelompok_material));

        if (str_contains($kelompok, 'pokok')) {
            Material::create([
                'kode_material'  => $kodeMaterial,
                'nama_material'  => $request->nama_item_fisik,
                'category_id'    => $category->id,
                'tipe_kalkulasi' => $request->tipe_kalkulasi,
                'satuan'         => $satuanFinal,
                'stok_sekarang'  => 0,
                'stok_minimum'   => $request->stok_minimum,
            ]);
        } else {
            MasterMaterialPembantu::create([
                'kode_material'  => $kodeMaterial,
                'nama_material'  => $request->nama_item_fisik,
                'category_id'    => $category->id,
                'tipe_kalkulasi' => $request->tipe_kalkulasi,
                'satuan'         => $satuanFinal,
                'stok_sekarang'  => 0,
                'stok_minimum'   => $request->stok_minimum,
            ]);
        }
    });

    return redirect()->route('material.category')->with('success', 'Kategori dan Material berhasil ditambahkan!');
}

 public function edit($id)
{
    $category = Category::with(['materials', 'materialPembantus'])->findOrFail($id);

    $daftarKategori = [
        'Material Pokok' => [
            'Kayu Solid',
            'Board',
            'Pelapis',
        ],
        'Material Pembantu' => [
            'Cairan Finishing',
            'Perekat / Lem',
            'Amplas',
            'Sekrup / Hardware',
        ],
    ];

    $opsiKategori = $daftarKategori[$category->kelompok_material] ?? [];

    return view('admin.material.category_edit', compact('category', 'opsiKategori'));
}

public function update(Request $request, $id)
{
    $request->validate([
        'nama_Kategori'              => 'required|string|max:255',
        'kategori'                   => 'required|string|max:255',
        'satuan_dasar'               => 'required|string|max:50',
        'items'                      => 'nullable|array',
        'items.*.nama_material'      => 'required|string|max:255',
        'items.*.stok_minimum'       => 'required|numeric|min:0',
    ]);

    $category = Category::findOrFail($id);

    DB::transaction(function () use ($request, $category) {
        $category->update([
            'nama_Kategori' => $request->nama_Kategori,
            'kategori'      => $request->kategori,
            'satuan_dasar'  => $request->satuan_dasar,
        ]);

        if ($request->has('items')) {
            $isPokok = str_contains(strtolower($category->kelompok_material), 'pokok');

            foreach ($request->items as $itemId => $itemData) {
                $updateData = [
                    'nama_material' => $itemData['nama_material'],
                    'stok_minimum'  => $itemData['stok_minimum'],
                ];

                if ($isPokok) {
                    Material::where('id', $itemId)
                        ->where('category_id', $category->id)
                        ->update($updateData);
                } else {
                    MasterMaterialPembantu::where('id', $itemId)
                        ->where('category_id', $category->id)
                        ->update($updateData);
                }
            }
        }
    });

    return redirect()->route('material.category')->with('success', 'Kategori berhasil diperbarui.');
}

public function destroy($id)
{
    $category = Category::findOrFail($id);

    $jumlahItem = $category->materials()->count() + $category->materialPembantus()->count();

    if ($jumlahItem > 0) {
        return redirect()->route('material.category')
            ->with('error', "Tidak bisa dihapus, kategori ini masih memiliki {$jumlahItem} item material.");
    }

    $category->delete();

    return redirect()->route('material.category')->with('success', 'Kategori berhasil dihapus.');
}
}