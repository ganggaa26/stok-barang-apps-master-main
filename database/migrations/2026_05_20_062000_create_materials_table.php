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
   Schema::create('materials', function (Blueprint $table) {
   $table->id();
        $table->string('kode_material')->unique();
        $table->string('nama_material');
        $table->string('jenis_material'); // Pokok atau Pembantu
        $table->decimal('tebal', 8, 2)->nullable();   // Menggunakan decimal agar aman untuk cm (contoh: 2.5 cm)
        $table->decimal('panjang', 8, 2)->nullable();  // Menggunakan decimal untuk meter (contoh: 4.00 meter)
        $table->decimal('lebar', 8, 2)->nullable();
        $table->string('satuan');
        $table->integer('stok_sekarang')->default(0);
        $table->integer('stok_minimum')->default(0);
        $table->timestamps();
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
