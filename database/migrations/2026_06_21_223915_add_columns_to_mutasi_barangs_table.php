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
            // Amankan kolom 'nama_proyek' jika belum ada
            if (!Schema::hasColumn('mutasi_barangs', 'nama_proyek')) {
                $table->string('nama_proyek')->nullable()->after('asal_atau_proyek');
            }
            
            // Amankan kolom 'spesifikasi_lokasi' jika belum ada
            if (!Schema::hasColumn('mutasi_barangs', 'spesifikasi_lokasi')) {
                $table->string('spesifikasi_lokasi')->nullable()->after('nama_proyek');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mutasi_barangs', function (Blueprint $table) {
            // Drop kolom hanya jika kolom tersebut eksis di database
            if (Schema::hasColumn('mutasi_barangs', 'nama_proyek')) {
                $table->dropColumn('nama_proyek');
            }
            if (Schema::hasColumn('mutasi_barangs', 'spesifikasi_lokasi')) {
                $table->dropColumn('spesifikasi_lokasi');
            }
        });
    }
};