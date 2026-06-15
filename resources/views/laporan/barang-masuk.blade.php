@extends('layouts.admin')

@section('content')
<script src="https://cdn.tailwindcss.com"></script>

<style>
    @media print {
        /* Sembunyikan sidebar, navbar bawaan layouts.admin, tombol cetak, dan filter dropdown */
        body *, .no-print, #filterJenisBahan, button, nav, aside {
            display: none !important;
        }
        /* Tampilkan hanya area laporan utama */
        #areaCetakUtama, #areaCetakUtama * {
            display: block !important;
        }
        /* Pastikan layout tabel melebar penuh saat diprint */
        #areaCetakUtama {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
        }
        /* Pertahankan background warna badge saat dicetak */
        * {
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
    }
</style>

<div id="areaCetakUtama" class="w-full mx-auto my-2 text-slate-700">
    
    <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between border-b border-slate-200 pb-4">
        <div>
            <div class="flex items-center space-x-2 text-xs text-slate-400 font-medium mb-1 no-print">
                <span>Pelaporan</span>
                <span>/</span>
                <span class="text-emerald-600">Laporan Barang Masuk</span>
            </div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-800">Jurnal & Rekapitulasi Barang Masuk</h1>
            <p class="text-sm text-slate-500 mt-0.5">Monitoring real-time untuk penambahan stok Material Pokok dan Material Pembantu</p>
        </div>
        
        <div class="flex items-center gap-2 mt-4 md:mt-0 no-print">
            <select id="filterJenisBahan" class="bg-white border border-slate-300 rounded-lg px-3 py-2 text-sm text-slate-700 focus:ring-2 focus:ring-emerald-500 focus:outline-none" onchange="filterLaporan()">
                <option value="semua">Semua Jenis Bahan</option>
                <option value="Pokok">Material Pokok Utama</option>
                <option value="Pembantu">Bahan Pembantu & Consumables</option>
            </select>

            <button onclick="window.print()" class="bg-slate-800 hover:bg-slate-900 text-white font-semibold text-sm px-4 py-2 rounded-lg shadow-sm flex items-center gap-2 transition-all">
                <span>🖨️</span> Cetak Laporan
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Total Transaksi Masuk</p>
                <h3 id="widgetTotal" class="text-2xl font-bold text-slate-800 mt-1">2 Transaksi</h3>
            </div>
            <div class="p-3 bg-emerald-50 text-emerald-600 rounded-lg text-xl">📥</div>
        </div>
        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Mutasi Material Pokok</p>
                <h3 id="widgetPokok" class="text-2xl font-bold text-indigo-600 mt-1">1 Log</h3>
            </div>
            <div class="p-3 bg-indigo-50 text-indigo-600 rounded-lg text-xl">🪵</div>
        </div>
        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Mutasi Bahan Pembantu</p>
                <h3 id="widgetPembantu" class="text-2xl font-bold text-blue-600 mt-1">1 Log</h3>
            </div>
            <div class="p-3 bg-blue-50 text-blue-600 rounded-lg text-xl">🧪</div>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5 space-y-4">
        <div class="overflow-x-auto rounded-lg border border-slate-200 bg-white">
            <table class="w-full text-sm text-left border-collapse">
                <thead>
                    <tr class="bg-slate-900 text-slate-300 text-xs uppercase tracking-wider border-b border-slate-700">
                        <th class="p-3.5 font-semibold">Tanggal</th>
                        <th class="p-3.5 font-semibold">Kelompok Modul</th>
                        <th class="p-3.5 font-semibold">Nama Item Material</th>
                        <th class="p-3.5 font-semibold">Spesifikasi Teknis</th>
                        <th class="p-3.5 font-semibold text-right">Volume / Qty Masuk</th>
                        <th class="p-3.5 font-semibold">Asal / Supplier</th>
                    </tr>
                </thead>
                <tbody id="tabelMasuk" class="divide-y divide-slate-100">
                    <tr class="baris-data hover:bg-slate-50 text-slate-700 transition-colors" data-kategori="Pokok">
                        <td class="p-3.5 text-xs text-slate-400 whitespace-nowrap">06/15/2026</td>
                        <td class="p-3.5"><span class="px-2.5 py-0.5 text-xs font-semibold rounded-full bg-indigo-50 text-indigo-600">Material Pokok</span></td>
                        <td class="p-3.5 font-medium text-slate-800">Kayu Jati</td>
                        <td class="p-3.5 text-xs text-slate-500">Grade: A | Lokasi: Gudang A Utama (10 Pcs)</td>
                        <td class="p-3.5 text-right font-mono font-bold text-emerald-600 whitespace-nowrap">+ 0.2000 M³</td>
                        <td class="p-3.5 text-xs text-slate-500">PT. Jati Permai</td>
                    </tr>
                    <tr class="baris-data hover:bg-slate-50 text-slate-700 transition-colors" data-kategori="Pembantu">
                        <td class="p-3.5 text-xs text-slate-400 whitespace-nowrap">06/15/2026</td>
                        <td class="p-3.5"><span class="px-2.5 py-0.5 text-xs font-semibold rounded-full bg-blue-50 text-blue-600">Bahan Pembantu</span></td>
                        <td class="p-3.5 font-medium text-slate-800">Lem Putih PVAc</td>
                        <td class="p-3.5 text-xs text-slate-500">Merk: Crona (1 Kg)</td>
                        <td class="p-3.5 text-right font-mono font-bold text-emerald-600 whitespace-nowrap">+ 5 Pcs</td>
                        <td class="p-3.5 text-xs text-slate-500">Toko Kimia Utama</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    function filterLaporan() {
        const filterValue = document.getElementById('filterJenisBahan').value;
        const rows = document.querySelectorAll('.baris-data');
        let totalTerlihat = 0, pokokCount = 0, pembantuCount = 0;

        rows.forEach(row => {
            const kat = row.getAttribute('data-kategori');
            
            if (filterValue === 'semua' || filterValue === kat) {
                row.classList.remove('hidden');
                totalTerlihat++;
                if (kat === 'Pokok') pokokCount++;
                if (kat === 'Pembantu') pembantuCount++;
            } else {
                row.classList.add('hidden');
            }
        });

        document.getElementById('widgetTotal').innerText = `${totalTerlihat} Transaksi`;
        document.getElementById('widgetPokok').innerText = `${pokokCount} Log`;
        document.getElementById('widgetPembantu').innerText = `${pembantuCount} Log`;
    }
</script>
@endsection