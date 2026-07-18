<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mutasi_barangs', function (Blueprint $table) {
            $table->dropForeign('mutasi_barangs_material_id_foreign');
            $table->foreign('material_id')
                  ->references('id')->on('materials')
                  ->restrictOnDelete();
        });

        Schema::table('mutasi_material_pembantus', function (Blueprint $table) {
            $table->dropForeign('mutasi_material_pembantus_material_pembantu_id_foreign');
            $table->foreign('material_pembantu_id')
                  ->references('id')->on('master_material_pembantus')
                  ->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('mutasi_barangs', function (Blueprint $table) {
            $table->dropForeign('mutasi_barangs_material_id_foreign');
            $table->foreign('material_id')
                  ->references('id')->on('materials')
                  ->cascadeOnDelete();
        });

        Schema::table('mutasi_material_pembantus', function (Blueprint $table) {
            $table->dropForeign('mutasi_material_pembantus_material_pembantu_id_foreign');
            $table->foreign('material_pembantu_id')
                  ->references('id')->on('master_material_pembantus')
                  ->cascadeOnDelete();
        });
    }
};