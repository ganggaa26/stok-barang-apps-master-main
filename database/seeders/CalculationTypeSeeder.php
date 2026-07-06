<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\CalculationType;

class CalculationTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            //--MATERIAL POKOK--
            [
                'kelompok_material' => 'Material Pokok',
                'nama_tipe' => 'Volume Kayu (T x L x P, hasil M³)',
                'kode_rumus' => 'volume_kayu',
            ],
            
            [
                'kelompok_material' => 'Material Pokok',
                'nama_tipe' => 'Lembar Board (Merk + Tebal mm)',
                'kode_rumus' => 'lembar_board',
            ],

            [
                'kelompok_material' => 'Material Pokok',
                'nama_tipe' => 'Luas Veneer (L x P, hasil M²)',
                'kode_rumus' => 'luas_veneer',
            ],

            [
                'kelompok_material' => 'Material Pokok',
                'nama_tipe' => 'Lembar HPL (Merk + Kode Warna) — HPL',
                'kode_rumus' => 'lembar_hpl',
            ],

            [
                'kelompok_material' => 'Material Pokok',
                'nama_tipe' => 'Luas Veneer (L x P, hasil M²) — Veneer',
                'kode_rumus' => 'luas_veneer',
            ],

            //--MATERIAL PEMBANTU--
            [
                'kelompok_material' => 'Material Pembantu',
                'nama_tipe' => 'Volume Cairan (Merk + Jenis Kimia, Liter) — Cat, Thinner',
                'kode_rumus' => 'volume_cairan',
            ],

            [
                'kelompok_material' => 'Material Pembantu',
                'nama_tipe' => 'Konversi Roll-Meter (Merk + Grit) — Amplas',
                'kode_rumus' => 'konversi_amplas',
            ],

            //--MATERIAL MANUAL--
            [
                'kelompok_material' => 'Material Pembantu',
                'nama_tipe' => 'Satuan Bebas / Input Manual (Pcs/Box/Kg)',
                'kode_rumus' => 'input_manual',
            ],
        ];

        foreach ($data as $item) {
            CalculationType::updateOrCreate(
                ['kode_rumus' => $item['kode_rumus']], // Mencegah duplikat data kalau dijalankan ulang
                $item
            );
        }
    }
}
