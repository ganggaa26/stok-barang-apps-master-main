<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Material extends Model
{
    use HasFactory;

    protected $table = 'materials';

    // Pastikan 'jenis_material' ADA di dalam array ini
    protected $fillable = [
        'kode_material',
        'nama_material',
        'jenis_material', 
        'tipe_kalkulasi',
        'satuan',
        'stok_sekarang',
        'stok_minimum'
    ];
}