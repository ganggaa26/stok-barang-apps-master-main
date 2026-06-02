<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    // protected $table = 'categories'; // Nama tabel yang sesuai dengan migrasi 
    protected $fillable = [
        'nama_kategori',
        'tipe_material',
    ];
}
