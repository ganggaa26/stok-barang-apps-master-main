<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema; // Mengimpor Schema dengan benar

class MaterialSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Matikan pengecekan foreign key sementara
        Schema::disableForeignKeyConstraints();

        // 2. Kosongkan tabel materials
        DB::table('materials')->truncate();

        // 3. Masukkan data dummy LENGKAP dengan kolom 'size'
        DB::table('materials')->insert([
            [
                'kode_material' => 'MAT-001',
                'nama_material' => 'Kayu Jati Gelondongan',
                'jenis_material' => 'Pokok',
                'size' => 'Diameter 40cm, P 2m', // Nilai Size Baru
                'satuan' => 'Pcs',
                'stok_sekarang' => 3, // Set di bawah minimum agar memicu alert dashboard
                'stok_minimum' => 10,
                'kualitas' => 'Grade A',
                'lokasi_gudang' => 'Gudang A',
                'blok_area' => 'Blok A-1',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kode_material' => 'MAT-002',
                'nama_material' => 'Papan Kayu Mahoni',
                'jenis_material' => 'Pokok',
                'size' => '3cm x 20cm x 3m', // Nilai Size Baru
                'satuan' => 'Pcs',
                'stok_sekarang' => 2,
                'stok_minimum' => 8,
                'kualitas' => 'Grade B',
                'lokasi_gudang' => 'Gudang A',
                'blok_area' => 'Blok A-3',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kode_material' => 'MAT-003',
                'nama_material' => 'Engsel Pintu Besi',
                'jenis_material' => 'Pilihan',
                'size' => '4 Inch', // Nilai Size Baru
                'satuan' => 'Kotak',
                'stok_sekarang' => 1,
                'stok_minimum' => 5,
                'kualitas' => 'Standard',
                'lokasi_gudang' => 'Gudang B',
                'blok_area' => 'Rak B-2',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // 4. Hidupkan kembali pengecekan foreign key
        Schema::enableForeignKeyConstraints();
    }
}