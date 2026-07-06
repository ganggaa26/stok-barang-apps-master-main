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
            Schema::create('mutasi_material_pembantus', function (Blueprint $table) {
            $table->id();
            $table->foreignId('material_pembantu_id')->constrained('master_material_pembantus')->onDelete('cascade');
           $table->string('jenis_transaksi'); // 'Barang Masuk' atau 'Barang Keluar'
            $table->decimal('kuantitas', 10, 2);
            $table->date('tanggal');
            
            // Kolom spesifik kebutuhan Material Pembantu
            $table->string('spesifikasi')->nullable(); 
            $table->string('merk')->nullable();        
            $table->string('jenis_kimia')->nullable(); 
            $table->string('grit')->nullable();        
            $table->string('satuan_input')->nullable();
            $table->string('asal_atau_proyek')->nullable();
            
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mutasi_material_pembantus');
    }
};
