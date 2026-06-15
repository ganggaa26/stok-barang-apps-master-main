@extends('layouts.admin')

@section('content')
<script src="https://cdn.tailwindcss.com"></script>

<style>
    @media print {
        /* Sembunyikan elemen navigasi, tombol aksi, filter, dan remah roti agar tidak ikut tercetak */
        body *, .no-print, #filterJenisBahan, button, nav, aside, header {
            display: none !important;
        }
        /* Fokuskan pencetakan hanya pada container laporan utama */
        #areaCetakUtama, #areaCetakUtama * {
            display: block !important;
        }
        /* Atur posisi kontainer agar memenuhi kertas saat dicetak */
        #areaCetakUtama {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            background: white;
        }
        /* Memaksa browser untuk tetap mencetak warna background badge/ikon */
        * {
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
        /* Hilangkan bayangan box border agar cetakan teks lebih tajam */
        .shadow-sm {
            box-shadow: none !important;
        }
    }
</style>

<div id="areaCetakUtama" class="w-full mx-auto my-2 text-slate-700">
    
    <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between border-b border-slate-200 pb-4">
        <div>
            <div class="flex items-center space-x-2 text-xs text-slate-400 font-medium mb-1 no-print">
                <span>Pelaporan</span>
                <span>/</span>
                <span class="text-rose-600">Laporan Barang Keluar</span>
            </div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-800">Jurnal & Alokasi Barang Keluar</h1>
            <p class="text-sm text-slate-500 mt-0.5">Monitoring real-time untuk penerapan bahan baku dan consumables dalam produksi</p>
        </div>
        
        <div class="flex items-center gap-2 mt-4 md:mt-0 no-print">
            <select id="filterJenisBahan" class="bg-white border border-slate-300 rounded-lg px-3 py-2 text-sm text-slate-700 focus:ring-2 focus:ring-rose-500 focus:outline-none" onchange="filterLaporan()">
                <option value="semua">Semua Jenis Bahan</option>
                <option value="Pokok">Material Pokok Utama</option>
                <option value="Pembantu">Bahan Pembantu & Consumables</option>
            </select>

            <button onclick="window.print()" class="bg-rose-600 hover:bg-rose-700 text-white font-semibold text-sm px-4 py-2 rounded-lg shadow-sm flex items-center gap-2 transition-all">
                <span>🖨️</span> Cetak Laporan
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Total Transaksi Keluar</p>
                <h3 id="widgetTotal" class="text-2xl font-bold text-slate-800 mt-1">2 Transaksi</h3>
            </div>
            <div class="p-3 bg-rose-50 text-rose-600 rounded-lg text-xl">📤</div>
        </div>
        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Penyerapan Mat. Pokok</p>
                <h3 id="widgetPokok" class="text-2xl font-bold text-indigo-600 mt-1">1 Log</h3>
            </div>
            <div class="p-3 bg-indigo-50 text-indigo-600 rounded-lg text-xl">🪵</div>
        </div>
        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Penyerapan Mat. Pembantu</p>
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
                        <th class="p-3.5 font-semibold">Spesifikasi Detail</th>
                        <th class="p-3.5 font-semibold text-right">Volume / Qty Keluar</th>
                        <th class="p-3.5 font-semibold">Alokasi Project Produksi</th>
                    </tr>
                </thead>
                <tbody id="tabelKeluar" class="divide-y divide-slate-100">
                    <tr class="baris-data hover:bg-slate-50 text-slate-700 transition-colors" data-kategori="Pokok">
                        <td class="p-3.5 text-xs text-slate-400 whitespace-nowrap">06/15/2026</td>
                        <td class="p-3.5"><span class="px-2.5 py-0.5 text-xs font-semibold rounded-full bg-indigo-50 text-indigo-600">Material Pokok</span></td>
                        <td class="p-3.5 font-medium text-slate-800">Plywood / Triplek</td>
                        <td class="p-3.5 text-xs text-slate-500">Mercy (18mm)</td>
                        <td class="p-3.5 text-right font-mono font-bold text-rose-600 whitespace-nowrap">- 12 Lembar</td>
                        <td class="p-3.5 text-xs text-slate-500">Project Kitchen Set Jati</td>
                    </tr>
                    <tr class="baris-data hover:bg-slate-50 text-slate-700 transition-colors" data-kategori="Pembantu">
                        <td class="p-3.5 text-xs text-slate-400 whitespace-nowrap">06/15/2026</td>
                        <td class="p-3.5"><span class="px-2.5 py-0.5 text-xs font-semibold rounded-full bg-blue-50 text-blue-600">Bahan Pembantu</span></td>
                        <td class="p-3.5 font-medium text-slate-800">Amplas Roll (Grid 120/180/240)</td>
                        <td class="p-3.5 text-xs text-slate-500">Kekasaran: Grid 240</td>
                        <td class="p-3.5 text-right font-mono font-bold text-rose-600 whitespace-nowrap">- 3 Meter</td>
                        <td class="p-3.5 text-xs text-slate-500">Workshop Finishing Room</td>
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