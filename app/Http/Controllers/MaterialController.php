<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Material;   
use App\Models\Category;
use App\Models\MutasiBarang;
use Illuminate\Support\Str;
use DB;

class MaterialController extends Controller
{
   public function index(Request $request)
{
    $categories = Category::where('kelompok_material', 'LIKE', '%pokok%')->get();

    $materials = Material::with('kategori')->get()->map(function ($item) {
        $item->tipe_kalkulasi = $item->kategori ? $item->kategori->tipe_kalkulasi : 'volume_kayu';
        return $item;
    });

    // Ambil SEMUA riwayat — filter minggu/tanggal ditangani JS di sisi client
    $mutasiks = MutasiBarang::latest()->get();

    return view('admin.material.pokok', compact('categories', 'materials', 'mutasiks'));
}

 public function store(Request $request)
{
    // 1. Validasi Input Form dari JS
    $request->validate([
        'jenis_transaksi'   => 'required|string',
        'tanggal' => 'nullable|date', 
        'material_id'       => 'required', 
        'kuantitas'         => 'required|numeric',
        'qty_fisik'         => 'required|numeric',
        'spesifikasi'       => 'nullable|string',
    ]);

    // 2. Pengaman Tanggal Transaksi Otomatis
    $tanggalRaw = $request->input('tanggal');
    if (empty($tanggalRaw) || str_starts_with($tanggalRaw, '00') || str_starts_with($tanggalRaw, '02')) {
        $tanggalRaw = date('Y-m-d'); 
    }

    // 3. Ambil data master material asal
    $material = Material::findOrFail($request->material_id);

    if ($request->jenis_transaksi === 'Barang Keluar' && $request->kuantitas > $material->stok_sekarang) {
        return redirect()->back()
            ->withInput()
            ->with('error', 'Stok tidak mencukupi. Stok tersedia saat ini: ' . number_format($material->stok_sekarang, 2) . ' ' . $material->satuan);
    }
   

    $namaKategoriMaterial = optional($material->kategori)->nama_Kategori ?? $material->jenis_material ?? 'Tidak Diketahui';
    $textSupplier = trim($request->input('asal_barang') ?? $request->input('asalBarang') ?? $request->input('asal_supplier') ?? '');
    $textProyek = trim($request->input('nama_proyek') ?? $request->input('namaProyek') ?? '');

    if ($request->jenis_transaksi === 'Barang Keluar') {
        $asalSupplierFinal = null;
        $namaProyekFinal = ($textProyek !== '') ? $textProyek : 'General';
    } else {
        // Jika Barang Masuk atau Stok Awal, proyek WAJIB NULL (jadi strip di view)
        $namaProyekFinal = null; 

        if ($textSupplier !== '') {
            $asalSupplierFinal = $textSupplier;
        } else {
            // JIKA MAU STOK AWAL: Namanya disesuaikan jadi "Restock Stok Awal"
            if ($request->jenis_transaksi === 'Stok Awal' || $request->jenis_transaksi === 'Stok Awal Gudang') {
                $asalSupplierFinal = 'Restock Stok Awal';
            } else {
                $asalSupplierFinal = 'Restock Umum';
            }
        }
    }

    // 5. Simpan data ke database
    MutasiBarang::create([
        'material_id'        => $material->id,
        'kategori_material'  =>$namaKategoriMaterial, 
        'jenis_transaksi'    => $request->jenis_transaksi,
        'tangal'            => $tanggalRaw,
        'tanggal'            => $tanggalRaw,
        
        'tebal'              => $request->input('tebal'),
        'lebar'              => $request->input('lebar'),
        'panjang'            => $request->input('panjang'),
        'qty_fisik'          => $request->qty_fisik,   
        'kuantitas'          => $request->kuantitas,   
        'spesifikasi_lokasi' => $request->spesifikasi, 
        'lokasi_gudang'      => $request->input('lokasi_gudang'),
        
        'asal_supplier'      => $asalSupplierFinal,
        'nama_proyek'        => $namaProyekFinal,
    ]);

    // 6. Logika Update Stok Riil Dinamis pada Master Material Pokok
    if ($request->jenis_transaksi === 'Barang Masuk' || $request->jenis_transaksi === 'Stok Awal' || $request->jenis_transaksi === 'Stok Awal Gudang') {
        $material->increment('stok_sekarang', $request->kuantitas);
    } else if ($request->jenis_transaksi === 'Barang Keluar') {
        $material->decrement('stok_sekarang', $request->kuantitas);
    }

    return redirect()->route('material.pokok')->with('success', 'Jurnal Transaksi Material Pokok Berhasil Disimpan!');
}

public function update(Request $request, $id)
{
    $mutasi = MutasiBarang::findOrFail($id);
    $materialLama = Material::findOrFail($mutasi->material_id);

      if ($request->jenis_transaksi === 'Barang Keluar') {
        $stokTersediaSetelahDibalik = $materialLama->stok_sekarang;

        // Kalau transaksi LAMA jenisnya Barang Masuk/Stok Awal, berarti stok itu perlu dikurangi dulu
        // (karena efeknya nanti dibalik di dalam transaction)
        if (in_array($mutasi->jenis_transaksi, ['Barang Masuk', 'Stok Awal', 'Stok Awal Gudang'])) {
            $stokTersediaSetelahDibalik -= $mutasi->kuantitas;
        } else {
            // Kalau transaksi LAMA jenisnya Barang Keluar, stok itu perlu ditambah dulu
            $stokTersediaSetelahDibalik += $mutasi->kuantitas;
        }

        if ($request->kuantitas > $stokTersediaSetelahDibalik) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Stok tidak mencukupi. Stok tersedia: ' . number_format($stokTersediaSetelahDibalik, 2));
        }
    }

    $textSupplier = trim($request->input('asal_barang') ?? $request->input('asalBarang') ?? $request->input('asal_supplier') ?? '');
    $textProyek = trim($request->input('nama_proyek') ?? $request->input('namaProyek') ?? '');

    if ($request->jenis_transaksi === 'Barang Keluar') {
        $asalSupplierFinal = null;
        $namaProyekFinal = ($textProyek !== '') ? $textProyek : 'General';
    } else {
        $namaProyekFinal = null;
        if ($textSupplier !== '') {
            $asalSupplierFinal = $textSupplier;
        } else {
            if (!empty($mutasi->asal_supplier)) {
                $asalSupplierFinal = $mutasi->asal_supplier;
            } else {
                $asalSupplierFinal = ($request->jenis_transaksi === 'Stok Awal' || $request->jenis_transaksi === 'Stok Awal Gudang')
                    ? 'Restock Stok Awal' : 'Restock Umum';
            }
        }
    }

    DB::transaction(function () use ($request, $mutasi, $materialLama, $asalSupplierFinal, $namaProyekFinal) {
        // 1. BATALKAN dulu efek stok dari data LAMA (sebelum diedit)
        $this->balikkanStokPokok($materialLama, $mutasi->jenis_transaksi, $mutasi->kuantitas);

        // 2. Simpan data baru ke mutasi_barangs
        $mutasi->update([
            'jenis_transaksi'    => $request->jenis_transaksi,
            'tanggal'            => $request->input('tanggal') ?? $mutasi->tanggal,
            'tebal'              => $request->input('tebal') ?? $mutasi->tebal,
            'lebar'              => $request->input('lebar') ?? $mutasi->lebar,
            'panjang'            => $request->input('panjang') ?? $mutasi->panjang,
            'qty_fisik'          => $request->input('qty_fisik') ?? $mutasi->qty_fisik,
            'kuantitas'          => $request->input('kuantitas') ?? $mutasi->kuantitas,
            'spesifikasi_lokasi' => $request->input('spesifikasi_lokasi') ?? $mutasi->spesifikasi_lokasi,
            'lokasi_gudang'      => $request->input('lokasi_gudang') ?? $mutasi->lokasi_gudang,
            'asal_supplier'      => $asalSupplierFinal,
            'nama_proyek'        => $namaProyekFinal,
        ]);

        // 3. TERAPKAN efek stok dari data BARU (setelah diedit)
        $materialBaru = Material::findOrFail($mutasi->material_id); // jaga-jaga kalau material_id ikut berubah nanti
        $this->terapkanStokPokok($materialBaru, $mutasi->jenis_transaksi, $mutasi->kuantitas);
    });

    return redirect()->route('material.pokok')->with('success', 'Data Transaksi Berhasil Diperbarui!');
}

private function terapkanStokPokok(Material $material, string $jenisTransaksi, $kuantitas): void
{
    if (in_array($jenisTransaksi, ['Barang Masuk', 'Stok Awal', 'Stok Awal Gudang'])) {
        $material->increment('stok_sekarang', $kuantitas);
    } elseif ($jenisTransaksi === 'Barang Keluar') {
        $material->decrement('stok_sekarang', $kuantitas);
    }
}

private function balikkanStokPokok(Material $material, string $jenisTransaksi, $kuantitas): void
{
    if (in_array($jenisTransaksi, ['Barang Masuk', 'Stok Awal', 'Stok Awal Gudang'])) {
        $material->decrement('stok_sekarang', $kuantitas);
    } elseif ($jenisTransaksi === 'Barang Keluar') {
        $material->increment('stok_sekarang', $kuantitas);
    }
}


    public function destroy($id)
        {
            $mutasi = MutasiBarang::findOrFail($id);
            $material = Material::findOrFail($mutasi->material_id);

            // Menyesuaikan ulang stok master agar tidak pincang setelah log dihapus
            if ($mutasi->jenis_transaksi === 'Barang Masuk' || $mutasi->jenis_transaksi === 'Stok Awal' || $mutasi->jenis_transaksi === 'Stok Awal Gudang') {
                $material->decrement('stok_sekarang', $mutasi->kuantitas);
            } else if ($mutasi->jenis_transaksi === 'Barang Keluar') {
                $material->increment('stok_sekarang', $mutasi->kuantitas);
            }

            $mutasi->delete();

            return redirect()->route('material.pokok')->with('success', 'Riwayat transaksi berhasil dihapus!');
        }
}