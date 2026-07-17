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

    protected $fillable = [
        'nama_Kategori',
        'kelompok_material',
        'kategori',
        'satuan_dasar',
        'tipe_kalkulasi',
    ];

    public function materials()
    {
          return $this->hasMany(Material::class, 'category_id');
    }

    public function materialPembantus()
    {
          return $this->hasMany(MasterMaterialPembantu::class, 'category_id');
    }

    public function getItemsAttribute()
{
    return $this->kelompok_material === 'Material Pokok'
        ? $this->materials
        : $this->materialPembantus;
}
}