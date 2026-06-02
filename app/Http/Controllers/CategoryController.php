<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;

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
        $request->validate([
            'nama_kategori' => 'required|string|max:255',
            'tipe_material' => 'required|string|max:255',
            
        ]);

        Category::create([
            'nama_kategori' => $request->nama_kategori,
            'tipe_material' => $request->tipe_material,
            
        ]);
      
        return redirect()->route('material.category')->with('success', 'Kategori berhasil ditambahkan.');
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

        
}h
