<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Material;
use App\Models\MasterMaterialPembantu;
class Category extends Model
{
    use HasFactory;

    protected $table = 'categories';
    protected $fillable = ['nama_Kategori', 'kelompok_material', 'satuan_dasar'];

    
    public function materials()
    {
        
        return $this->hasMany(Material::class, 'jenis_material', 'nama_Kategori');
    }

    public function materialPembantus()
    {
        return $this->hasMany(MasterMaterialPembantu::class, 'jenis_material', 'nama_Kategori');
    }
}