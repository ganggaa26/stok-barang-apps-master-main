<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BahanPokok; // Sesuaikan dengan nama Model Eloquent Anda

class BahanPokokController extends Controller
{
    // 1. Menampilkan Form Input Utama
    public function index()
    {
        return view('pokok'); 
    }

    // 2. Menerima data Form dan Menyimpannya ke Database
    public function store(Request $request)
    {
        // Validasi input data logistik & spesifikasi dinamis
        $validated = $request->validate([
            'sub_kategori'       => 'required',
            'item_barang'        => 'required',
            'jenis_transaksi'    => 'required',
            'tanggal'            => 'required|date',
            'quantity'           => 'required|numeric',
            'asal_supplier'      => 'nullable|string',
            'nama_proyek'        => 'nullable|string',
            // Atribut spesifikasi opsional tergantung sub_kategori
            'tebal_kayu'         => 'nullable|numeric',
            'lebar_kayu'         => 'nullable|numeric',
            'panjang_kayu'       => 'nullable|numeric',
            'grade_kayu'         => 'nullable|string',
            'lokasi_gudang'      => 'nullable|string',
            'detail_merk'        => 'nullable|string',
            'detail_spesifikasi' => 'nullable|string',
            'no_bendel'          => 'nullable|string',
            'tebal_veneer'       => 'nullable|numeric',
            'lebar_veneer'       => 'nullable|numeric',
            'panjang_veneer'     => 'nullable|numeric',
        ]);

        // Simpan Log Transaksi
        BahanPokok::create($validated);

        // Jika transaksi adalah barang masuk/stok awal, arahkan ke laporan barang masuk
        if (in_array($request->jenis_transaksi, ['Barang Masuk', 'Stok Awal'])) {
            return redirect()->route('laporan.masuk')->with('success', 'Material baru berhasil didaftarkan ke gudang!');
        }

        return redirect()->back()->with('success', 'Transaksi keluar berhasil dicatat.');
    }

    // 3. Menampilkan Halaman Laporan Terpisah (Otomatis Tersambung)
    public function laporanMasuk()
    {
        // HANYA MENGAMBIL: "Barang Masuk" dan "Stok Awal"
        $barangMasuk = BahanPokok::whereIn('jenis_transaksi', ['Barang Masuk', 'Stok Awal'])
                                 ->orderBy('tanggal', 'desc')
                                 ->get();

        // LOGIKA MENGHITUNG STATISTIK SECARA REAL-TIME
        $totalVolume = 0;
        $totalLembar = 0;

        foreach ($barangMasuk as $item) {
            if ($item->sub_kategori === 'kayu_solid') {
                // Konversi rumus kubikasi ke M3
                $totalVolume += (($item->tebal_kayu * $item->lebar_kayu * $item->panjang_kayu) / 1000000) * $item->quantity;
            } else {
                // Jumlah lembaran untuk Plywood, HPL, Veneer
                $totalLembar += $item->quantity;
            }
        }

        return view('laporan-masuk', [
            'barangMasuk'    => $barangMasuk,
            'totalVolume'    => $totalVolume,
            'totalLembar'    => $totalLembar,
            'totalTransaksi' => $barangMasuk->count()
        ]);
    }
}