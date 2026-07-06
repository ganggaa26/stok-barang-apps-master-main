<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Material;
use App\Models\MasterMaterialPembantu;
use App\Models\CalculationType;
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
        $calculationTypes = CalculationType::all();
        $subCategories = Category::select('nama_Kategori as nama_kategori')->distinct()->get();
        
        if (!$subCategories) {
            $subCategories = collect();
        }
        
        //  1: Memasukkan kembali 'subCategories' ke dalam compact agar tidak undefined di Blade
        return view('admin.material.category_create', compact('calculationTypes', 'subCategories'));
    }

    public function store(Request $request)
    {
        //  2: Mengubah validasi tipe_kalkulasi menjadi string biasa (max:255)
        // Ini dilakukan agar input rumus buatanmu seperti '2*2' tidak ditolak oleh system
        $request->validate([
            'nama_kategori'     => 'required|string|max:255',
            'kelompok_material' => 'required|string|max:255',
            'satuan_dasar'      => 'required|string|max:50',
            'nama_item_fisik'   => 'required|string|max:255',
            'tipe_kalkulasi'    => 'required|string|max:255', 
        ]);

        //  3: Menyelaraskan nama request key (nama_kategori kecil sesuai dengan validasi diatas)
        $category = Category::firstOrCreate(
            [
                'nama_Kategori'     => $request->nama_kategori,
                'kelompok_material' => $request->kelompok_material,
            ],
            [
                'satuan_dasar'   => $request->satuan_dasar,
                'tipe_kalkulasi' => $request->tipe_kalkulasi, 
            ]
        );

        // 3. Generate Kode Material
        $prefix = 'MAT-' . date('ymd') . '-';
        $randomString = strtoupper(Str::random(4));
        $kodeMaterial = $prefix . $randomString;

        $kelompok = strtolower(trim($request->kelompok_material));

        //  4 (SOLUSI UTAMA ERROR DB SQLSRV): 
        // Mengamankan nilai tipe_kalkulasi langsung dari request input agar tidak bernilai NULL 
        // ketika kategori lama ditemukan namun record material baru mau didaftarkan
        $tipeKalkulasiFix = $request->tipe_kalkulasi;

        // 4. Logika Percabangan Penyimpanan Data
        if (str_contains($kelompok, 'pokok')) {
            Material::create([
                'kode_material'  => $kodeMaterial,
                'nama_material'  => $request->nama_item_fisik,
                'jenis_material' => $category->nama_Kategori,
                'tipe_kalkulasi' => $tipeKalkulasiFix, // Menggunakan variabel pengaman
                'satuan'         => $request->satuan_dasar,
                'stok_sekarang'  => 0,
                'stok_minimum'   => 0,
            ]);
        } else {
            MasterMaterialPembantu::create([
                'kode_material'  => $kodeMaterial,
                'nama_material'  => $request->nama_item_fisik,
                'jenis_material' => $category->nama_Kategori,
                'tipe_kalkulasi' => $tipeKalkulasiFix, // Menggunakan variabel pengaman
                'satuan'         => $request->satuan_dasar,
                'stok_sekarang'  => 0,
                'stok_minimum'   => 0,
            ]);
        }

        // 5. Redirect
        return redirect()->route('material.category')->with('success', 'Kategori dan Material berhasil ditambahkan!');
    }

    public function edit($id)
    {
        $category = Category::findOrFail($id);
        return view('admin.material.category_edit', compact('category'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_Kategori' => 'required|string|max:255',
        ]);

        $category = Category::findOrFail($id);
        $category->update([
            'nama_Kategori' => $request->nama_Kategori,
        ]);

        return redirect()->route('material.category')->with('success', 'Kategori berhasil diperbarui.');
    }
}