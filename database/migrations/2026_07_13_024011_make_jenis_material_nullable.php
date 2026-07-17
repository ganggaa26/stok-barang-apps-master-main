<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('materials', function (Blueprint $table) {
            $table->string('jenis_material')->nullable()->change();
        });
        Schema::table('master_material_pembantus', function (Blueprint $table) {
            $table->string('jenis_material')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('materials', function (Blueprint $table) {
            $table->string('jenis_material')->nullable(false)->change();
        });
        Schema::table('master_material_pembantus', function (Blueprint $table) {
            $table->string('jenis_material')->nullable(false)->change();
        });
    }
};