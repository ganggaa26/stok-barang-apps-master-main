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
    Schema::create('materials', function (Blueprint $table){
    $table->id();
    $table->string('kode_material')->unique();
    $table->string('nama_material'); // Contoh: Kayu Bengkirai, Kayu Jati
    $table->string('jenis_material'); // Contoh: Kayu Solid
    $table->string('satuan')->default('M3');
    $table->decimal('stok_sekarang', 20, 6)->default(0); // Total kubikasi global di gudang
    $table->decimal('stok_minimum', 10, 4)->default(0);
    $table->timestamps();
    
    // Kolom tebal, panjang, lebar, tipe_kalkulasi DIHAPUS dari sini
});
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('materials');
    }
};
