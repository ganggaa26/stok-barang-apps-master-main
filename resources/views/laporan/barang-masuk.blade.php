@extends('layouts.admin')

@section('content')
<script src="https://cdn.tailwindcss.com"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

<style>
    @media print {
        body *, .no-print, #filterContainer, button, nav, aside {
            display: none !important;
        }
        /* Tampilkan hanya area laporan utama */
        #areaCetakUtama, #areaCetakUtama * {
            display: block !important;
        }
        #areaCetakUtama {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
        }
        * {
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
    }
</style>

<div id="areaCetakUtama" class="w-full mx-auto my-2 text-slate-700">
    
    <div class="mb-6 flex flex-col lg:flex-row lg:items-center lg:justify-between border-b border-slate-200 pb-4">
        <div>
            <div class="flex items-center space-x-2 text-xs text-slate-400 font-medium mb-1 no-print">
                <span>Pelaporan</span>
                <span>/</span>
                <span class="text-emerald-600">Laporan Barang Masuk</span>
            </div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-800">Jurnal & Rekapitulasi Barang Masuk</h1>
            <p class="text-sm text-slate-500 mt-0.5">Monitoring real-time untuk penambahan stok Material Pokok dan Material Pembantu</p>
        </div>
        
        <div id="actionContainer" class="flex flex-wrap items-center gap-2 mt-4 lg:mt-0 no-print">
            <div id="filterContainer" class="flex flex-wrap items-center gap-2 bg-slate-50 border border-slate-200 rounded-xl p-1.5 shadow-sm">
                
                <!-- INPUT CARI NAMA BARANG (BARU) -->
                <div class="bg-white border border-slate-300 rounded-lg px-2 h-8 flex items-center">
                    <span class="text-slate-400 mr-1 text-xs">🔍</span>
                    <input type="text" id="cariNamaItem" onkeyup="jalankanFilterGabungan()" placeholder="Cari nama barang..." class="focus:outline-none text-xs text-slate-700 bg-transparent w-36">
                </div>

                <select id="filterJenisBahan" onchange="jalankanFilterGabungan()" class="bg-white border border-slate-300 rounded-lg px-2.5 py-1 text-xs text-slate-700 focus:ring-2 focus:ring-emerald-500 focus:outline-none h-8">
                    <option value="semua">Semua Jenis Bahan</option>
                    <option value="Pokok">Material Pokok Utama</option>
                    <option value="Pembantu">Bahan Pembantu & Consumables</option>
                </select>

                <div class="flex items-center gap-1 bg-white border border-slate-300 rounded-lg px-2 h-8 text-xs">
                    <input type="date" id="tglMulai" onchange="jalankanFilterGabungan()" class="focus:outline-none text-slate-700 bg-transparent">
                    <span class="text-slate-400 font-medium">s.d</span>
                    <input type="date" id="tglSelesai" onchange="jalankanFilterGabungan()" class="focus:outline-none text-slate-700 bg-transparent">
                    <button onclick="resetSemuaFilter()" class="text-slate-400 hover:text-rose-600 px-1 font-bold text-sm" title="Reset Filter">×</button>
                </div>
            </div>

            <button onclick="window.print()" class="bg-slate-800 hover:bg-slate-900 text-white font-semibold text-sm px-4 py-2 rounded-lg shadow-sm flex items-center gap-2 transition-all h-9">
                <span>🖨️</span> Cetak
            </button>

            <button onclick="downloadPDF()" class="bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-sm px-4 py-2 rounded-lg shadow-sm flex items-center gap-2 transition-all h-9">
                <span>📥</span> PDF
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Total Masuk Terfilter</p>
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
                        <th class="p-3.5 font-semibold text-center w-12">No.</th>
                        <th class="p-3.5 font-semibold">Tanggal</th>
                        <th class="p-3.5 font-semibold">Kelompok Modul</th>
                        <th class="p-3.5 font-semibold">Nama Item Material (Klik Detail)</th>
                        <th class="p-3.5 font-semibold">Spesifikasi Teknis</th>
                        <th class="p-3.5 font-semibold text-center">Jml Batang/Lbr</th>
                        <th class="p-3.5 font-semibold text-right">Vol / Luas Akhir</th>
                        <th class="p-3.5 font-semibold">Asal / Supplier</th>
                    </tr>
                </thead>
                <tbody id="tabelMasuk" class="divide-y divide-slate-100">
                    
                    <tr class="baris-data hover:bg-slate-50 text-slate-700 transition-colors" 
                        data-kategori="Pokok"
                        data-tanggal="2026-06-15"
                        data-item="Kayu Jati"
                        data-spesifikasi="Grade: A | Lokasi: Gudang A Utama"
                        data-qty="10 Btg"
                        data-volume="+ 0.2000 M³"
                        data-supplier="PT. Jati Permai">
                        
                        <td class="p-3.5 text-center font-medium text-slate-400">1</td>
                        <td class="p-3.5 text-xs text-slate-500 whitespace-nowrap">2026-06-15</td>
                        <td class="p-3.5"><span class="px-2.5 py-0.5 text-xs font-semibold rounded-full bg-indigo-50 text-indigo-600">Material Pokok</span></td>
                        
                        <td class="p-3.5">
                            <button onclick="bukaModalItem(this)" class="text-left font-bold text-emerald-600 hover:text-emerald-800 underline decoration-dotted transition-colors">
                                Kayu Jati
                            </button>
                        </td>
                        
                        <td class="p-3.5 text-xs text-slate-500">Grade: A | Lokasi: Gudang A Utama</td>
                        <td class="p-3.5 text-center font-mono font-semibold text-slate-700 whitespace-nowrap">10 Btg</td>
                        <td class="p-3.5 text-right font-mono font-bold text-emerald-600 whitespace-nowrap">+ 0.2000 M³</td>
                        
                        <td class="p-3.5 text-xs">
                            <button onclick="bukaModalItem(this)" class="text-left text-slate-600 hover:text-indigo-600 font-medium underline">
                                PT. Jati Permai
                            </button>
                        </td>
                    </tr>

                    <tr class="baris-data hover:bg-slate-50 text-slate-700 transition-colors" 
                        data-kategori="Pembantu"
                        data-tanggal="2026-06-20"
                        data-item="Lem Putih PVAc"
                        data-spesifikasi="Merk: Crona (1 Kg)"
                        data-qty="5 Pcs"
                        data-volume="+ 5 Pcs"
                        data-supplier="Toko Kimia Utama">
                        
                        <td class="p-3.5 text-center font-medium text-slate-400">2</td>
                        <td class="p-3.5 text-xs text-slate-500 whitespace-nowrap">2026-06-20</td>
                        <td class="p-3.5"><span class="px-2.5 py-0.5 text-xs font-semibold rounded-full bg-blue-50 text-blue-600">Bahan Pembantu</span></td>
                        
                        <td>
                            <button onclick="bukaModalItem(this)" class="text-left font-bold text-emerald-600 hover:text-emerald-800 underline decoration-dotted transition-colors">
                                Lem Putih PVAc
                            </button>
                        </td>
                        
                        <td class="p-3.5 text-xs text-slate-500">Merk: Crona (1 Kg)</td>
                        <td class="p-3.5 text-center font-mono font-semibold text-slate-700 whitespace-nowrap">5 Pcs</td>
                        <td class="p-3.5 text-right font-mono font-bold text-emerald-600 whitespace-nowrap">+ 5 Pcs</td>
                        
                        <td class="p-3.5 text-xs">
                            <button onclick="bukaModalItem(this)" class="text-left text-slate-600 hover:text-indigo-600 font-medium underline">
                                Toko Kimia Utama
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div id="modalDetailMasuk" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4 no-print">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-xl border border-slate-100 space-y-5">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <div class="flex items-center space-x-2">
                <span class="text-xl">📥</span>
                <h3 class="text-base font-bold text-slate-800">Verifikasi Log Pasokan Masuk</h3>
            </div>
            <button onclick="tutupModalItem()" class="text-slate-400 hover:text-slate-600 text-2xl font-semibold leading-none">&times;</button>
        </div>
        
        <div class="space-y-3 text-sm">
            <div class="bg-emerald-50/50 p-3 rounded-xl border border-emerald-100">
                <span class="block text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Nama Item Material</span>
                <span id="modalNamaItem" class="font-bold text-slate-800 text-base block mt-0.5">-</span>
                <span id="modalSpesifikasi" class="text-xs text-slate-500 block mt-1">-</span>
            </div>

            <div class="border border-slate-200 rounded-xl p-4 space-y-2.5">
                <div class="flex justify-between text-xs text-slate-500 border-b border-slate-100 pb-1.5">
                    <span>Tanggal Masuk Gudang:</span>
                    <span id="modalTanggal" class="font-semibold text-slate-700">-</span>
                </div>
                <div class="flex justify-between text-xs text-slate-500 border-b border-slate-100 pb-1.5">
                    <span>Kuantitas Koli/Fisik:</span>
                    <span id="modalQty" class="font-mono font-bold text-slate-800">-</span>
                </div>
                <div class="flex justify-between text-xs text-slate-500 border-b border-slate-100 pb-1.5">
                    <span>Volume Skala Ukur:</span>
                    <span id="modalVolume" class="font-mono font-bold text-emerald-600">-</span>
                </div>
                <div class="flex justify-between text-xs text-slate-500 pt-1">
                    <span>Vendor / Supplier Asal:</span>
                    <span id="modalSupplier" class="font-semibold text-indigo-600">-</span>
                </div>
            </div>

            <div class="p-3 bg-slate-50 rounded-xl border border-slate-200 text-center">
                <span class="text-xs font-bold text-slate-500 block uppercase tracking-wide">Status Quality Control</span>
                <span class="inline-block mt-1.5 px-3 py-1 bg-emerald-100 text-emerald-800 text-xs font-bold rounded-full">Passed & Verified</span>
            </div>
        </div>

        <div class="flex justify-end pt-1">
            <button onclick="tutupModalItem()" class="bg-slate-800 hover:bg-slate-900 text-white font-semibold text-xs px-4 py-2 rounded-lg shadow-sm">
                Tutup Dokumen
            </button>
        </div>
    </div>
</div>

<script>
    // LOGIKA UTAMA FILTER GABUNGAN (NAMA BARANG, PERIODE WAKTU & JENIS BAHAN)
    function jalankanFilterGabungan() {
        const namaItemValue = document.getElementById('cariNamaItem').value.toLowerCase();
        const jenisBahanValue = document.getElementById('filterJenisBahan').value;
        const tglMulaiValue = document.getElementById('tglMulai').value;
        const tglSelesaiValue = document.getElementById('tglSelesai').value;
        const rows = document.querySelectorAll('.baris-data');
        
        let totalTerlihat = 0, pokokCount = 0, pembantuCount = 0;

        rows.forEach(row => {
            const tglRow = row.getAttribute('data-tanggal');
            const katRow = row.getAttribute('data-kategori');
            const namaRow = row.getAttribute('data-item').toLowerCase();
            
            let cocokNama = true;
            let cocokTanggal = true;
            let cocokKategori = true;

            // Validasi filter Nama Barang (Mendukung pencarian sebagian kata/case-insensitive)
            if (namaItemValue && !namaRow.includes(namaItemValue)) cocokNama = false;

            // Validasi filter Tanggal Periode
            if (tglMulaiValue && tglRow < tglMulaiValue) cocokTanggal = false;
            if (tglSelesaiValue && tglRow > tglSelesaiValue) cocokTanggal = false;

            // Validasi filter Jenis Kategori Bahan
            if (jenisBahanValue !== 'semua' && katRow !== jenisBahanValue) cocokKategori = false;

            // Jika memenuhi semua kriteria filter
            if (cocokNama && cocokTanggal && cocokKategori) {
                row.classList.remove('hidden');
                totalTerlihat++;
                if (katRow === 'Pokok') pokokCount++;
                if (katRow === 'Pembantu') pembantuCount++;
            } else {
                row.classList.add('hidden');
            }
        });

        // Mutasi update widget visual angka
        document.getElementById('widgetTotal').innerText = `${totalTerlihat} Transaksi`;
        document.getElementById('widgetPokok').innerText = `${pokokCount} Log`;
        document.getElementById('widgetPembantu').innerText = `${pembantuCount} Log`;

        reindexNomorTabel();
    }

    function resetSemuaFilter() {
        document.getElementById('cariNamaItem').value = '';
        document.getElementById('filterJenisBahan').value = 'semua';
        document.getElementById('tglMulai').value = '';
        document.getElementById('tglSelesai').value = '';
        jalankanFilterGabungan();
    }

    function reindexNomorTabel() {
        const rows = document.querySelectorAll('.baris-data:not(.hidden)');
        rows.forEach((row, index) => {
            row.querySelector('td:first-child').innerText = index + 1;
        });
    }

    // HANDLER MODAL POP-UP DETAIL ITEM & SUPPLY CHAIN
    function bukaModalItem(element) {
        const row = element.closest('tr');
        
        document.getElementById('modalNamaItem').innerText = row.getAttribute('data-item');
        document.getElementById('modalSpesifikasi').innerText = row.getAttribute('data-spesifikasi');
        document.getElementById('modalTanggal').innerText = row.getAttribute('data-tanggal');
        document.getElementById('modalQty').innerText = row.getAttribute('data-qty');
        document.getElementById('modalVolume').innerText = row.getAttribute('data-volume');
        document.getElementById('modalSupplier').innerText = row.getAttribute('data-supplier');

        document.getElementById('modalDetailMasuk').classList.remove('hidden');
    }

    function tutupModalItem() {
        document.getElementById('modalDetailMasuk').classList.add('hidden');
    }

    // ENGINE PRINT TO PDF
    function downloadPDF() {
        const actionContainer = document.getElementById('actionContainer');
        actionContainer.style.display = 'none';

        const element = document.getElementById('areaCetakUtama');
        
        const opsi = {
            margin:       12,
            filename:     'Laporan_Barang_Masuk_Komprehensif.pdf',
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