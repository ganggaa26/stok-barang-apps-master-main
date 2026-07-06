<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CalculationType extends Model
{
    use HasFactory;

    // Mengenalkan nama tabelnya ke Laravel
    protected $table = 'calculation_types';

    // Mendaftarkan kolom yang boleh diisi
    protected $fillable = [
        'kelompok_material',
        'nama_tipe',
        'kode_rumus',
    ];
}