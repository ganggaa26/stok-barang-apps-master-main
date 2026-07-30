<?php

namespace App\Imports;

use App\Models\Material;
use App\Models\MasterMaterialPembantu;
use App\Models\MutasiBarang;
use App\Models\MutasiMaterialPembantu;
use Maatwebsite\Excel\Concerns\ToCollection;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class StokAwalImport implements ToCollection
{
    protected $kelompok;
    protected $itemId;
    protected $tipeKalkulasi;
    protected $errorRows = [];

    // Kamus alias nama kolom -> field internal, per tipe kalkulasi.
    // Bisa nambah alias baru kapan saja kalau ketemu penamaan lain di file industri.
    protected $skemaKolom = [
        'volume_kayu' => [
            'tanggal'   => ['tanggal'],
            'lokasi'    => ['lokasi'],
            'tebal'     => ['tebal'],
            'lebar'     => ['lebar'],
            'panjang'   => ['panjang', 'panjan'],
            'qty'       => ['qty (batang / pcs)', 'qty batang', 'qty (batang/pcs)', 'batang / pcs', 'qty(batang)'],
            'kuantitas' => ['qty (m3)', 'qty(m3)', 'qty m3', 'volume'],
            'pemakaian_qty'   => ['pemakaian (batang / pcs)', 'pemakaian', 'pemakaian batang'],
            'pemakaian_kuant' => ['qty pemakaian (m3)', 'qty pemakaian(m3)', 'pemakaian m3'],
        ],
        'lembar_board' => [
            'tanggal'   => ['tanggal'],
            'merk'      => ['merk', 'merek', 'jenis board'],
            'tebal'     => ['tebal', 'tebal (mm)', 'tebal mm'],
            'kuantitas' => ['jumlah', 'qty', 'qty (lembar)', 'lembar'],
            'lokasi'    => ['lokasi'],
        ],
        'lembar_hpl' => [
            'tanggal'   => ['tanggal'],
            'merk'      => ['merk', 'merek'],
            'kode'      => ['kode', 'kode warna', 'motif'],
            'kuantitas' => ['jumlah', 'qty', 'qty (lembar)', 'lembar'],
            'lokasi'    => ['lokasi'],
        ],
        'luas_veneer' => [
            'tanggal'   => ['tanggal'],
            'jenis'     => ['jenis', 'jenis kayu'],
            'bendel'    => ['bendel', 'no bendel', 'nomor bendel'],
            'tebal'     => ['tebal', 'tebal (mm)'],
            'lebar'     => ['lebar', 'lebar (cm)'],
            'panjang'   => ['panjang', 'panjang (cm)'],
            'kuantitas' => ['jumlah', 'qty', 'qty (lembar)', 'lembar'],
            'lokasi'    => ['lokasi'],
        ],
        // Default untuk Material Pembantu (volume_cairan, konversi_amplas, CUSTOM_RUMUS, dll)
        'pembantu_default' => [
            'tanggal'   => ['tanggal'],
            'merk'      => ['merk', 'merek'],
            'kuantitas' => ['jumlah', 'qty', 'kuantitas', 'volume'],
            'spesifikasi' => ['spesifikasi', 'keterangan', 'catatan'],
        ],
    ];

    public function __construct(string $kelompok, int $itemId, string $tipeKalkulasi)
    {
        $this->kelompok = $kelompok;
        $this->itemId = $itemId;
        $this->tipeKalkulasi = $this->kelompok === 'pembantu' ? 'pembantu_default' : $tipeKalkulasi;
    }

    public function collection($rows)
    {
        // 1. Cari baris header secara otomatis (bisa di baris ke-1, ke-4, ke-5, dst)
        $baris0Index = $this->cariBarisHeader($rows);
        if ($baris0Index === null) {
            $this->errorRows[] = 'Header kolom tidak ditemukan di file ini. Pastikan ada baris dengan nama kolom seperti "Tanggal", "Qty", dst.';
            return;
        }

        // 2. Petakan nama kolom di file -> posisi index, berdasarkan alias yang cocok
        $petaKolom = $this->petakanKolom($rows[$baris0Index]);

        if (!isset($petaKolom['tanggal'])) {
            $this->errorRows[] = 'Kolom "Tanggal" tidak ditemukan di header. Import dibatalkan.';
            return;
        }

        // 3. Proses baris-baris setelah header sebagai data
        DB::transaction(function () use ($rows, $baris0Index, $petaKolom) {
            for ($i = $baris0Index + 1; $i < $rows->count(); $i++) {
                $row = $rows[$i];
                $this->prosesBaris($row, $i + 1, $petaKolom);
            }
        });
    }

    private function cariBarisHeader($rows): ?int
    {
        // Kata kunci yang menandakan sebuah baris adalah header, bukan judul/data
        $kataKunciHeader = ['tanggal', 'qty', 'kuantitas', 'merk', 'jumlah', 'lokasi'];

        foreach ($rows as $index => $row) {
            $teksBaris = strtolower(implode(' ', $row->toArray()));
            $skor = 0;
            foreach ($kataKunciHeader as $kata) {
                if (str_contains($teksBaris, $kata)) $skor++;
            }
            // Kalau baris ini mengandung minimal 2 kata kunci, anggap ini baris header
            if ($skor >= 2) {
                return $index;
            }
            // Batasi pencarian ke 10 baris pertama saja, biar nggak salah tebak di tengah data
            if ($index > 10) break;
        }
        return null;
    }

    private function petakanKolom($headerRow): array
    {
        $skema = $this->skemaKolom[$this->tipeKalkulasi] ?? [];
        $peta = [];

        foreach ($headerRow as $colIndex => $namaKolom) {
            $namaBersih = strtolower(trim((string) $namaKolom));
            if ($namaBersih === '') continue;

            foreach ($skema as $field => $aliasList) {
                if (isset($peta[$field])) continue; // sudah ketemu, jangan ditimpa
                foreach ($aliasList as $alias) {
                    if (str_contains($namaBersih, $alias)) {
                        $peta[$field] = $colIndex;
                        break;
                    }
                }
            }
        }
        return $peta;
    }

    private function prosesBaris($row, int $nomorBaris, array $peta)
    {
        $ambil = fn($field) => isset($peta[$field]) ? ($row[$peta[$field]] ?? null) : null;

        $tanggalRaw = trim((string) $ambil('tanggal'));
        if ($tanggalRaw === '') return; // baris kosong, lewati diam-diam

        try {
            $tanggal = $this->parseTanggal($tanggalRaw);
        } catch (\Exception $e) {
            $this->errorRows[] = "Baris {$nomorBaris}: tanggal '{$tanggalRaw}' tidak dikenali";
            return;
        }

        $kuantitas = $this->angkaAman($ambil('kuantitas')) ?? 0;
        if ($kuantitas <= 0) {
            $this->errorRows[] = "Baris {$nomorBaris}: kuantitas kosong/0, dilewati";
            return;
        }

        $dataUmum = [
            'tanggal'  => $tanggal,
            'lokasi'   => trim((string) $ambil('lokasi')),
            'tebal'    => $this->angkaAman($ambil('tebal')),
            'lebar'    => $this->angkaAman($ambil('lebar')),
            'panjang'  => $this->angkaAman($ambil('panjang')),
            'merk'     => trim((string) $ambil('merk')),
            'kode'     => trim((string) $ambil('kode')),
            'jenis'    => trim((string) $ambil('jenis')),
            'bendel'   => trim((string) $ambil('bendel')),
            'qty'      => $this->angkaAman($ambil('qty')) ?? $kuantitas,
            'kuantitas'=> $kuantitas,
            'spesifikasi' => trim((string) $ambil('spesifikasi')),
        ];

        if ($this->kelompok === 'pokok') {
            $this->simpanMaterialPokok('Stok Awal', $dataUmum);

            // Kalau file punya kolom "Pemakaian", catat juga sebagai Barang Keluar
            $pemakaianQty = $this->angkaAman($ambil('pemakaian_qty'));
            $pemakaianKuant = $this->angkaAman($ambil('pemakaian_kuant'));
            if ($pemakaianKuant && $pemakaianKuant > 0) {
                $dataKeluar = $dataUmum;
                $dataKeluar['qty'] = $pemakaianQty ?? 0;
                $dataKeluar['kuantitas'] = $pemakaianKuant;
                $this->simpanMaterialPokok('Barang Keluar', $dataKeluar, 'Riwayat lama (proyek tidak tercatat)');
            }
        } else {
            $this->simpanMaterialPembantu('Stok Awal', $dataUmum);
        }
    }

    private function simpanMaterialPokok(string $jenisTransaksi, array $d, string $proyek = '')
    {
        $material = Material::findOrFail($this->itemId);
        $namaKategoriMaterial = optional($material->kategori)->nama_Kategori ?? 'Tidak Diketahui';

        $spesifikasiGabungan = array_filter([
            $d['merk'] ? "Merk: {$d['merk']}" : null,
            $d['kode'] ? "Kode: {$d['kode']}" : null,
            $d['jenis'] ? "Jenis: {$d['jenis']}" : null,
            $d['bendel'] ? "Bendel: {$d['bendel']}" : null,
        ]);

        MutasiBarang::create([
            'material_id'        => $material->id,
            'kategori_material'  => $namaKategoriMaterial,
            'jenis_transaksi'    => $jenisTransaksi,
            'tanggal'            => $d['tanggal'],
            'tebal'              => $d['tebal'],
            'lebar'              => $d['lebar'],
            'panjang'            => $d['panjang'],
            'qty_fisik'          => $d['qty'],
            'kuantitas'          => $d['kuantitas'],
            'spesifikasi_lokasi' => implode(' | ', $spesifikasiGabungan) ?: '-',
            'lokasi_gudang'      => $d['lokasi'] ?: null,
            'asal_supplier'      => $jenisTransaksi !== 'Barang Keluar' ? 'Restock Stok Awal' : null,
            'nama_proyek'        => $jenisTransaksi === 'Barang Keluar' ? ($proyek ?: 'General') : null,
        ]);

        if (in_array($jenisTransaksi, ['Barang Masuk', 'Stok Awal', 'Stok Awal Gudang'])) {
            $material->increment('stok_sekarang', $d['kuantitas']);
        } elseif ($jenisTransaksi === 'Barang Keluar') {
            $material->decrement('stok_sekarang', $d['kuantitas']);
        }
    }

    private function simpanMaterialPembantu(string $jenisTransaksi, array $d)
    {
        $material = MasterMaterialPembantu::findOrFail($this->itemId);

        MutasiMaterialPembantu::create([
            'material_pembantu_id' => $material->id,
            'jenis_transaksi'      => $jenisTransaksi,
            'kuantitas'            => $d['kuantitas'],
            'tanggal'              => $d['tanggal'],
            'spesifikasi'          => $d['spesifikasi'] ?: '-',
            'merk'                 => $d['merk'] ?: '-',
            'satuan_input'         => $material->satuan ?? null,
            'asal_atau_proyek'     => null,
            'keterangan'           => $d['spesifikasi'] ?: '-',
        ]);

        if ($jenisTransaksi === 'Barang Keluar') {
            $material->decrement('stok_sekarang', $d['kuantitas']);
        } else {
            $material->increment('stok_sekarang', $d['kuantitas']);
        }
    }

    private function parseTanggal(string $raw): string
    {
        if (is_numeric($raw)) {
            return Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject((float) $raw))->format('Y-m-d');
        }
        foreach (['d-M-Y', 'd/m/Y', 'Y-m-d', 'd-m-Y'] as $format) {
            try {
                return Carbon::createFromFormat($format, trim($raw))->format('Y-m-d');
            } catch (\Exception $e) { continue; }
        }
        throw new \Exception('Format tanggal tidak dikenali');
    }

    private function angkaAman($nilai): ?float
    {
        if ($nilai === null || trim((string) $nilai) === '') return null;
        preg_match('/-?\d+(\.\d+)?/', (string) $nilai, $match);
        return isset($match[0]) ? (float) $match[0] : null;
    }

    public function getErrorRows(): array
    {
        return $this->errorRows;
    }
}