<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up()
    {
     Schema::create('mutasi_barangs', function (Blueprint $table) {
    $table->id();
    $table->foreignId('material_id')->constrained('materials')->onDelete('cascade');
    $table->string('kategori_material'); 
    $table->string('jenis_transaksi'); // Stok Awal, Barang Masuk, Barang Keluar
    
    // Pindahan kolom ukuran fisik ke sini (Gunakan String/Varchar agar fleksibel)
    $table->string('tebal')->nullable();   // Bisa diisi "5" atau "4x6"
    $table->string('lebar')->nullable();   // Bisa diisi "12" atau "8x20"
    $table->string('panjang')->nullable(); // Bisa diisi "400" atau "100-280"
    
    $table->integer('qty_fisik')->default(0); // Jumlah batang/pcs
    $table->decimal('kuantitas', 10, 4)->default(0); // Hasil akhir volume (M3) per baris ini
    
    $table->date('tanggal');
    $table->string('spesifikasi_lokasi')->nullable(); // Contoh: C3, B2
    $table->string('asal_supplier')->nullable();
    $table->string('nama_proyek')->nullable();
    $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
   public function down(): void
{
    Schema::dropIfExists('mutasi_barangs');
}
};