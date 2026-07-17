<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // 1. Tambah kolom category_id (nullable dulu, biar data lama gak error)
        Schema::table('materials', function (Blueprint $table) {
            $table->unsignedBigInteger('category_id')->nullable()->after('jenis_material');
        });
        Schema::table('master_material_pembantus', function (Blueprint $table) {
            $table->unsignedBigInteger('category_id')->nullable()->after('jenis_material');
        });

        // 2. Migrasi data lama: cocokkan jenis_material (nama) -> category_id berdasarkan nama_Kategori
        $categories = DB::table('categories')->get();

        foreach ($categories as $cat) {
            DB::table('materials')
                ->where('jenis_material', $cat->nama_Kategori)
                ->update(['category_id' => $cat->id]);

            DB::table('master_material_pembantus')
                ->where('jenis_material', $cat->nama_Kategori)
                ->update(['category_id' => $cat->id]);
        }

        // 3. Baru tambahkan foreign key constraint setelah data konsisten
        Schema::table('materials', function (Blueprint $table) {
            $table->foreign('category_id')->references('id')->on('categories')->nullOnDelete();
        });
        Schema::table('master_material_pembantus', function (Blueprint $table) {
            $table->foreign('category_id')->references('id')->on('categories')->nullOnDelete();
        });
    }

    public function down()
    {
        Schema::table('materials', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->dropColumn('category_id');
        });
        Schema::table('master_material_pembantus', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->dropColumn('category_id');
        });
    }
};  