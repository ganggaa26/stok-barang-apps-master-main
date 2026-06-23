<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('materials', function (Blueprint $table) {
            $table->string('tipe_kalkulasi')->nullable()->after('jenis_material');
        });

        Schema::table('master_material_pembantus', function (Blueprint $table) {
            $table->string('tipe_kalkulasi')->nullable()->after('jenis_material');
        });
    }

    public function down(): void
    {
        Schema::table('materials', function (Blueprint $table) {
            $table->dropColumn('tipe_kalkulasi');
        });

        Schema::table('master_material_pembantus', function (Blueprint $table) {
            $table->dropColumn('tipe_kalkulasi');
        });
    }
};