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
        Schema::table('materials', function (Blueprint $table) {
        $table->string('size', 50)->nullable();
        $table->string('kualitas', 50)->nullable();
        $table->string('lokasi_gudang', 100)->nullable();
        $table->string('blok_area', 50)->nullable()->after('lokasi_gudang');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('materials', function (Blueprint $table) {
            $table->dropColumn('kualitas');
            $table->dropColumn('lokasi_gudang');
            $table->dropColumn('blok_area');
        });
    }
};
