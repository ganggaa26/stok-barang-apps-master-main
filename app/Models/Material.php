<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    use HasFactory;

    protected $table = 'materials'; // Memastikan mengarah ke tabel materials

    protected $guarded = []; // Atau masukkan fillable-mu jika ada

    // Wajib Tambahkan Fungsi Ini di Sini!
   public function kategori()
{
    // Menghubungkan kolom kategori_material (atau category_id) ke id tabel categories
    return $this->belongsTo(Category::class, 'kategori_material', 'id'); 
}

    public function mutasis()
    {
        return $this->hasMany(MutasiBarang::class, 'material_id');
    }
}