@extends('layouts.admin')

@section('content')
<script src="https://cdn.tailwindcss.com"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

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
                <span class="text-emerald-600">Laporan Stok</span>
            </div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-800">Jurnal & Rekapitulasi Stok Gudang</h1>
            <p class="text-sm text-slate-500 mt-0.5">Monitoring real-time untuk ketersediaan material pokok dan bahan pembantu</p>
        </div>
        
        <div id="actionContainer" class="flex flex-wrap items-center gap-2 mt-4 md:mt-0 no-print">
            <select id="filterJenisBahan" class="bg-white border border-slate-300 rounded-lg px-3 py-2 text-sm text-slate-700 focus:ring-2 focus:ring-emerald-500 focus:outline-none" onchange="filterLaporan()">
                <option value="semua">Semua Jenis Bahan</option>
                <option value="Pokok">Material Pokok Utama</option>
                <option value="Pembantu">Bahan Pembantu & Consumables</option>
            </select>

            <button onclick="window.print()" class="bg-slate-800 hover:bg-slate-900 text-white font-semibold text-sm px-4 py-2 rounded-lg shadow-sm flex items-center gap-2 transition-all">
                <span>🖨️</span> Cetak Laporan
            </button>

            <button onclick="downloadPDF()" class="bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-sm px-4 py-2 rounded-lg shadow-sm flex items-center gap-2 transition-all">
                <span>📥</span> Download PDF
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Total Item Terlihat</p>
                <h3 id="widgetTotal" class="text-2xl font-bold text-slate-800 mt-1">2 Material</h3>
            </div>
            <div class="p-3 bg-emerald-50 text-emerald-600 rounded-lg text-xl">📦</div>
        </div>
        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Stok Material Pokok</p>
                <h3 id="widgetPokok" class="text-2xl font-bold text-indigo-600 mt-1">1 Item</h3>
            </div>
            <div class="p-3 bg-indigo-50 text-indigo-600 rounded-lg text-xl">🪵</div>
        </div>
        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Stok Bahan Pembantu</p>
                <h3 id="widgetPembantu" class="text-2xl font-bold text-blue-600 mt-1">1 Item</h3>
            </div>
            <div class="p-3 bg-blue-50 text-blue-600 rounded-lg text-xl">🧪</div>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5 space-y-4">
        <div class="overflow-x-auto rounded-lg border border-slate-200 bg-white">
            <table class="w-full text-sm text-left border-collapse">
                <thead>
                    <tr class="bg-slate-900 text-slate-300 text-xs uppercase tracking-wider border-b border-slate-700">
                        <th class="p-3.5 font-semibold text-center w-12">No.</th>
                        <th class="p-3.5 font-semibold">Kode</th>
                        <th class="p-3.5 font-semibold">Nama Item Material</th>
                        <th class="p-3.5 font-semibold">Kelompok</th>
                        <th class="p-3.5 font-semibold text-right">Stok Awal</th>
                        <th class="p-3.5 font-semibold text-right text-emerald-400">Masuk (+)</th>
                        <th class="p-3.5 font-semibold text-right text-rose-400">Keluar (-)</th>
                        <th class="p-3.5 font-semibold text-right">Stok Akhir</th>
                        <th class="p-3.5 font-semibold text-center">Satuan</th>
                        <th class="p-3.5 font-semibold text-center">Status</th>
                    </tr>
                </thead>
                <tbody id="tabelStok" class="divide-y divide-slate-100">
                    <tr class="baris-data hover:bg-slate-50 text-slate-700 transition-colors" data-kategori="Pokok">
                        <td class="p-3.5 text-center font-medium text-slate-400">1</td>
                        <td class="p-3.5 font-bold text-slate-800 whitespace-nowrap">MAT-001</td>
                        <td class="p-3.5">
                            <span class="font-semibold text-slate-800 block">Kayu Jati Gelondongan</span>
                            <span class="text-xs text-slate-400 block mt-0.5">Grade: A | Lokasi: Gudang A (Blok A-1)</span>
                        </td>
                        <td class="p-3.5"><span class="px-2.5 py-0.5 text-xs font-semibold rounded-full bg-indigo-50 text-indigo-600 whitespace-nowrap">Material Pokok</span></td>
                        <td class="p-3.5 text-right font-mono text-slate-500">3.0000</td>
                        <td class="p-3.5 text-right font-mono font-semibold text-emerald-600">+ 0.2000</td>
                        <td class="p-3.5 text-right font-mono font-semibold text-rose-600">- 0.2000</td>
                        <td class="p-3.5 text-right font-mono font-bold text-rose-500">3.0000</td>
                        <td class="p-3.5 text-center text-xs font-semibold text-slate-400">M³</td>
                        <td class="p-3.5 text-center">
                            <span class="px-2.5 py-0.5 text-xs font-bold rounded-full bg-rose-50 text-rose-600 whitespace-nowrap">Butuh Restock</span>
                        </td>
                    </tr>
                    <tr class="baris-data hover:bg-slate-50 text-slate-700 transition-colors" data-kategori="Pembantu">
                        <td class="p-3.5 text-center font-medium text-slate-400">2</td>
                        <td class="p-3.5 font-bold text-slate-800 whitespace-nowrap">MAT-002</td>
                        <td class="p-3.5">
                            <span class="font-semibold text-slate-800 block">Lem Putih PVAc</span>
                            <span class="text-xs text-slate-400 block mt-0.5">Merk: Crona (1 Kg)</span>
                        </td>
                        <td class="p-3.5"><span class="px-2.5 py-0.5 text-xs font-semibold rounded-full bg-blue-50 text-blue-600 whitespace-nowrap">Bahan Pembantu</span></td>
                        <td class="p-3.5 text-right font-mono text-slate-500">0</td>
                        <td class="p-3.5 text-right font-mono font-semibold text-emerald-600">+ 5</td>
                        <td class="p-3.5 text-right font-mono font-semibold text-rose-600">- 3</td>
                        <td class="p-3.5 text-right font-mono font-bold text-slate-800">2</td>
                        <td class="p-3.5 text-center text-xs font-semibold text-slate-400">Pcs</td>
                        <td class="p-3.5 text-center">
                            <span class="px-2.5 py-0.5 text-xs font-bold rounded-full bg-rose-50 text-rose-600 whitespace-nowrap">Butuh Restock</span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    // Fungsi Filter Laporan & Update Widget Angka
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

        document.getElementById('widgetTotal').innerText = `${totalTerlihat} Material`;
        document.getElementById('widgetPokok').innerText = `${pokokCount} Item`;
        document.getElementById('widgetPembantu').innerText = `${pembantuCount} Item`;

        // Mengurutkan ulang nomor tabel secara dinamis (1, 2, 3...) setelah filter
        reindexNomorTabel();
    }

    // Fungsi Otomatis Mengurutkan Nomor Tabel
    function reindexNomorTabel() {
        const rows = document.querySelectorAll('.baris-data:not(.hidden)');
        rows.forEach((row, index) => {
            row.querySelector('td:first-child').innerText = index + 1;
        });
    }

    // Fungsi Download PDF Menggunakan html2pdf.js (Format Landscape A4)
    function downloadPDF() {
        const actionContainer = document.getElementById('actionContainer');
        actionContainer.style.display = 'none';

        const element = document.getElementById('areaCetakUtama');
        
        const opsi = {
            margin:       12,
            filename:     'Laporan_Stok_Gudang_PKSD.pdf',
            image:        { type: 'jpeg', quality: 0.98 },
            html2canvas:  { scale: 2, useCORS: true },
            jsPDF:        { unit: 'mm', format: 'a4', orientation: 'landscape' }
        };

        html2pdf().set(opsi).from(element).save().then(() => {
            actionContainer.style.display = 'flex';
        });
    }
</script>
@endsection