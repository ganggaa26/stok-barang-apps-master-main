<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MutasiMaterialPembantu extends Model
{
    protected $table = 'mutasi_material_pembantus';

    protected $fillable = [
        'material_pembantu_id',
        'jenis_transaksi',
        'kuantitas',
        'tanggal',
        'spesifikasi',
        'merk',
        'jenis_kimia',
        'grit',
        'satuan_input',
        'asal_atau_proyek',
        'keterangan'
    ];

    /**
     * Relasi ke Tabel Master Material Pembantu
     */
    public function masterMaterialPembantu()
    {
        return $this->belongsTo(MasterMaterialPembantu::class, 'material_pembantu_id');
    }
}