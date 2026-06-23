<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Material;
use App\Models\MasterMaterialPembantu;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::all();
        return view('admin.material.category', compact('categories'));
    }

    public function create()
    {
        return view('admin.material.category_create');
    }

    public function store(Request $request)
    {
        // 1. Validasi Input
        $request->validate([
            'nama_Kategori'     => 'required|string|max:255',
            'kelompok_material' => 'required|string|max:255',
            'satuan_dasar'      => 'required|string|max:50',
            'nama_item_fisik'   => 'required|string|max:255',
            'tipe_kalkulasi'    => 'required|string|in:volume_kayu,lembar_board,lembar_hpl,luas_veneer,satuan_lem,satuan_sekrup,volume_cairan,konversi_amplas',
        ]);

        // 2. Cari kategori yang sudah ada (berdasarkan nama + kelompok),
        // atau buat baru kalau belum ada. Ini mencegah duplikat kategori
        // setiap kali form ini disubmit dengan sub-kategori yang sama.
        $category = Category::firstOrCreate(
            [
                'nama_Kategori'     => $request->nama_Kategori,
                'kelompok_material' => $request->kelompok_material,
            ],
            [
                'satuan_dasar' => $request->satuan_dasar,
            ]
        );

        // 3. Generate Kode Material
        $prefix = 'MAT-' . date('ymd') . '-';
        $randomString = strtoupper(Str::random(4));
        $kodeMaterial = $prefix . $randomString;

        $kelompok = strtolower(trim($request->kelompok_material));

        // 4. Logika Percabangan
        if (str_contains($kelompok, 'pokok')) {
            Material::create([
                'kode_material'  => $kodeMaterial,
                'nama_material'  => $request->nama_item_fisik,
                'jenis_material' => $category->nama_Kategori,
                'tipe_kalkulasi' => $request->tipe_kalkulasi,
                'satuan'         => $request->satuan_dasar,
                'stok_sekarang'  => 0,
                'stok_minimum'   => 0,
            ]);
        } else {
            MasterMaterialPembantu::create([
                'kode_material'  => $kodeMaterial,
                'nama_material'  => $request->nama_item_fisik,
                'jenis_material' => $category->nama_Kategori,
                'tipe_kalkulasi' => $request->tipe_kalkulasi,
                'satuan'         => $request->satuan_dasar,
                'stok_sekarang'  => 0,
                'stok_minimum'   => 0,
            ]);
        }

        // 5. Redirect
        return redirect()->route('material.category')->with('success', 'Kategori dan Material berhasil ditambahkan!');
    }
}