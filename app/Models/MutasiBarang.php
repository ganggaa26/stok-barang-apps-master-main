<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MutasiBarang extends Model
{
    protected $table = 'mutasi_barangs';

    protected $fillable = [
        'material_id',
        'kategori_material',
        'jenis_transaksi',
        'kuantitas',
        'tanggal',
        'keterangan',
        'merk',
        'spesifikasi',
        'satuan_input',
        'asal_atau_proyek',

        'nama_proyek',
        'asal_supplier',
        'nama_produk_jadi',
        'qty_produksi',
        'qty_fisik',
        'satuan_fisik',
    ];

    public function materialPembantu()
    {
        return $this->belongsTo(MasterMaterialPembantu::class, 'material_id');
    }

    public function material()
    {
        return $this->belongsTo(Material::class, 'material_id');
    }
}