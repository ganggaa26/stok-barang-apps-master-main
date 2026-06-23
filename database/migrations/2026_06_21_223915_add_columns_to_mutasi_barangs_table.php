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
        Schema::table('mutasi_barangs', function (Blueprint $table) {
                $table->string('merk')->nullable()->after('kategori_material');
                $table->string('spesifikasi')->nullable()->after('merk');
                $table->string('satuan_input')->nullable()->after('spesifikasi');
                $table->string('asal_atau_proyek')->nullable()->after('satuan_input');
       
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mutasi_barangs', function (Blueprint $table) {
              $table->dropColumn(['merk', 'spesifikasi', 'satuan_input', 'asal_atau_proyek']);
        });
    }
};
