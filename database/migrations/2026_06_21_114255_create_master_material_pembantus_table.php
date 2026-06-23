<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('master_material_pembantus', function (Blueprint $table) {
            $table->id();
            $table->string('kode_material');
            $table->string('nama_material');
            $table->string('jenis_material');
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
        Schema::dropIfExists('master_material_pembantus');
    }
};
