<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterMaterialPembantu extends Model
{
    use HasFactory;

    protected $table = 'master_material_pembantus';

    protected $fillable = [
        'kode_material',
        'category_id',
        'nama_material',
        'jenis_material',
        'tipe_kalkulasi',
        'satuan',
        'stok_sekarang',
        'stok_minimum'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    /**
     * Riwayat transaksi (mutasi) untuk material pembantu ini.
     * Dipakai jika nanti perlu tampilkan riwayat per item,
     * atau hitung ulang stok dari transaksi (audit/rekonsiliasi).
     */
  public function mutasi()
    {
        return $this->hasMany(MutasiMaterialPembantu::class, 'material_pembantu_id');
    }
}