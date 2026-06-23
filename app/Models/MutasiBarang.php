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
    ];

    /**
     * Relasi ke master material pembantu.
     * Catatan: kolom material_id di tabel mutasi_barangs ini dipakai
     * bersama untuk Bahan Pokok (materials) maupun Bahan Pembantu
     * (master_material_pembantus). Pakai relasi yang sesuai konteks
     * modul yang memanggil (lihat juga material() di bawah).
     */
    public function materialPembantu()
    {
        return $this->belongsTo(MasterMaterialPembantu::class, 'material_id');
    }

    /**
     * Relasi ke master Bahan Pokok (tabel materials).
     * Dipakai oleh view pokok.blade.php (mis. $log->material->nama_material).
     */
    public function material()
    {
        return $this->belongsTo(Material::class, 'material_id');
    }
}
