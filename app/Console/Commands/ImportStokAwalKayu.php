<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ImportStokAwalKayu extends Command
{
    /**
     * Contoh pemakaian:
     * php artisan import:stok-awal-kayu "Kayu Bengkirai" "Kayu Solid" M3 storage/app/import/bengkirai.csv
     *
     * Argumen:
     * 1. nama material (nama_material)  -> "Kayu Bengkirai"
     * 2. jenis material (jenis_material) -> "Kayu Solid"
     * 3. satuan (satuan)                 -> "M3"
     * 4. path file csv relatif dari base_path()
     */
    protected $signature = 'import:stok-awal-kayu
        {nama : Nama material, misal "Kayu Bengkirai"}
        {jenis : Jenis material, misal "Kayu Solid"}
        {satuan : Satuan, misal M3}
        {csv : Path file csv, misal storage/app/import/bengkirai.csv}
        {--kode= : Kode material manual, kalau kosong akan auto-generate}
        {--lokasi_gudang= : Lokasi gudang default untuk material ini}';

    protected $description = 'Import data stok awal kayu dari CSV ke tabel materials + mutasi_barangs';

    public function handle()
    {
        $namaMaterial = $this->argument('nama');
        $jenisMaterial = $this->argument('jenis');
        $satuan = $this->argument('satuan');
        $csvPath = base_path($this->argument('csv'));

        if (!file_exists($csvPath)) {
            $this->error("File CSV tidak ditemukan: {$csvPath}");
            return 1;
        }

        // 1. Cari material yang sudah ada (biar tidak duplikat kalau command dijalankan ulang),
        //    kalau belum ada, buat baru.
        $material = DB::table('materials')
            ->where('nama_material', $namaMaterial)
            ->where('jenis_material', $jenisMaterial)
            ->first();

        if ($material) {
            $materialId = $material->id;
            $this->info("Material '{$namaMaterial}' sudah ada (ID: {$materialId}), transaksi akan ditambahkan ke material ini.");
        } else {
            $kode = $this->option('kode') ?? $this->generateKode($namaMaterial);
            $materialId = DB::table('materials')->insertGetId([
                'kode_material'  => $kode,
                'nama_material'  => $namaMaterial,
                'jenis_material' => $jenisMaterial,
                'satuan'         => $satuan,
                'stok_sekarang'  => 0,
                'qty_fisik'      => 0,
                'stok_minimum'   => 0,
                'lokasi_gudang'  => $this->option('lokasi_gudang'),
                'created_at'     => now(),
                'updated_at'     => now(),
            ]);
            $this->info("Material baru dibuat: '{$namaMaterial}' (kode: {$kode}, ID: {$materialId})");
        }

        // 2. Baca CSV dan insert ke mutasi_barangs sebagai "Stok Awal"
        $handle = fopen($csvPath, 'r');
        $header = fgetcsv($handle); // baris pertama = header, dilewati
        $rowCount = 0;
        $errorRows = [];

        while (($row = fgetcsv($handle)) !== false) {
            $rowCount++;

            // urutan kolom: tanggal, lokasi, tebal, lebar, panjang, qty_batang, qty_m3
            [$tanggalRaw, $lokasi, $tebal, $lebar, $panjang, $qtyBatang, $qtyM3] = array_pad($row, 7, null);

            // Pakai format eksplisit "d-M-Y" (contoh: 27-Jun-2025), BUKAN Carbon::parse() yang
            // ambigu — Carbon::parse() pernah salah baca tahun untuk beberapa string tanggal.
            try {
                $tanggal = Carbon::createFromFormat('d-M-Y', trim($tanggalRaw))->format('Y-m-d');
            } catch (\Exception $e) {
                $errorRows[] = "Baris {$rowCount}: tanggal tidak valid ('{$tanggalRaw}')";
                continue;
            }

            DB::table('mutasi_barangs')->insert([
                'material_id'       => $materialId,
                'kategori_material' => $jenisMaterial,
                'jenis_transaksi'   => 'Stok Awal',
                'tebal'             => $tebal,
                'lebar'             => $lebar,
                'panjang'           => $panjang,
                'qty_fisik'         => (int) ($qtyBatang ?: 0),
                'kuantitas'         => (float) ($qtyM3 ?: 0),
                'tanggal'           => $tanggal,
                'spesifikasi_lokasi' => $lokasi,
                'created_at'        => now(),
            ]);
        }
        fclose($handle);

        // 3. Update stok_sekarang & qty_fisik material = total dari semua mutasi_barangs miliknya
        //    (Stok Awal dihitung sebagai baseline stok, sesuai logika dashboard yang sudah ada)
        $totalStok = DB::table('mutasi_barangs')
            ->where('material_id', $materialId)
            ->sum('kuantitas');

        $totalQtyFisik = DB::table('mutasi_barangs')
            ->where('material_id', $materialId)
            ->sum('qty_fisik');

        DB::table('materials')
            ->where('id', $materialId)
            ->update([
                'stok_sekarang' => $totalStok,
                'qty_fisik'     => $totalQtyFisik,
                'updated_at'    => now(),
            ]);

        $this->info("Selesai. {$rowCount} baris diproses untuk '{$namaMaterial}'.");
        $this->info("Stok sekarang: {$totalStok} {$satuan} | Qty fisik: {$totalQtyFisik}");

        if (!empty($errorRows)) {
            $this->warn('Ada baris yang dilewati karena error:');
            foreach ($errorRows as $err) {
                $this->warn(" - {$err}");
            }
        }

        return 0;
    }

    private function generateKode(string $nama): string
    {
        $slug = strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $nama), 0, 4));
        return 'MAT-' . now()->format('ymd') . '-' . $slug;
    }
}