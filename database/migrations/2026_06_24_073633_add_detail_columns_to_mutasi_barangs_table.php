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
            // Selalu bisa diisi, terlepas dari jenis_transaksi apa.
            $table->string('nama_proyek')->nullable()->after('asal_atau_proyek');

            // Khusus relevan saat jenis_transaksi = 'Barang Masuk' / 'Stok Awal'.
            $table->string('asal_supplier')->nullable()->after('nama_proyek');

            // Khusus relevan saat jenis_transaksi = 'Barang Keluar'.
            $table->string('nama_produk_jadi')->nullable()->after('asal_supplier');
            $table->unsignedInteger('qty_produksi')->nullable()->after('nama_produk_jadi');

            // Kuantitas fisik mentah sebelum dikonversi (mis. jumlah batang
            // sebelum dihitung jadi M3, atau jumlah lembar sebelum jadi M2),
            // dipasangkan dengan label satuannya.
            $table->decimal('qty_fisik', 12, 2)->nullable()->after('qty_produksi');
            $table->string('satuan_fisik')->nullable()->after('qty_fisik');
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        
        Schema::table('mutasi_barangs', function (Blueprint $table) {
            $table->dropColumn([
                'nama_proyek',
                'asal_supplier',
                'nama_produk_jadi',
                'qty_produksi',
                'qty_fisik',
                'satuan_fisik',
            ]);
        });
    }
};
