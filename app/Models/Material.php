<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    use HasFactory;

    protected $table = 'materials';

    protected $guarded = [];

    public function kategori()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function mutasis()
    {
        return $this->hasMany(MutasiBarang::class, 'material_id');
    }
}