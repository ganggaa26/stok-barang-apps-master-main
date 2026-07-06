<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MutasiBarang extends Model
{
    protected $table = 'mutasi_barangs';
    
    /**
     * Mass Assignment Protection
     * Hanya kolom yang ada di migration mutasi_barangs (Pokok)
     */
    protected $fillable = [
        'material_id',
        'kategori_material',
        'jenis_transaksi',
        'tebal',
        'lebar',
        'panjang',
        'kuantitas',
        'tanggal',
        'spesifikasi_lokasi',
        'qty_produksi',
        'qty_fisik',
        'satuan_fisik',
        'ukuran',            
        'nama_proyek',
        'asal_supplier',
        'asal_atau_proyek',
        'satuan_input',
        'keterangan'        
    ];

    /**
     * Relasi ke Tabel Material Pokok
     */
    public function material()
{
    return $this->belongsTo(Material::class, 'material_id');
}

}