@extends('layouts.admin')

@section('content')
<script src="https://cdn.tailwindcss.com"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

<style>
    @media print {
        body *, .no-print, #filterJenisBahan, button, nav, aside, header {
            display: none !important;
        }
        #areaCetakUtama, #areaCetakUtama * {
            display: block !important;
        }
        #areaCetakUtama {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            background: white;
        }
        * {
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
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
            <p class="text-sm text-slate-500 mt-0.5">Monitoring real-time untuk audit penyerapan bahan baku terhadap volume output produksi</p>
        </div>
        
        <div id="actionContainer" class="flex items-center gap-2 mt-4 md:mt-0 no-print">
            <select id="filterJenisBahan" class="bg-white border border-slate-300 rounded-lg px-3 py-2 text-sm text-slate-700 focus:ring-2 focus:ring-rose-500 focus:outline-none" onchange="filterLaporan()">
                <option value="semua">Semua Jenis Bahan</option>
                <option value="Pokok">Material Pokok Utama</option>
                <option value="Pembantu">Bahan Pembantu & Consumables</option>
            </select>

            <button onclick="window.print()" class="bg-slate-800 hover:bg-slate-900 text-white font-semibold text-sm px-4 py-2 rounded-lg shadow-sm flex items-center gap-2 transition-all">
                <span>🖨️</span> Cetak Laporan
            </button>

            <button onclick="downloadPDF()" class="bg-rose-600 hover:bg-rose-700 text-white font-semibold text-sm px-4 py-2 rounded-lg shadow-sm flex items-center gap-2 transition-all">
                <span>📥</span> Download PDF
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
                        <th class="p-3.5 font-semibold text-center w-12">No.</th>
                        <th class="p-3.5 font-semibold">Tanggal</th>
                        <th class="p-3.5 font-semibold">Kelompok Modul</th>
                        <th class="p-3.5 font-semibold">Nama Item Material</th>
                        <th class="p-3.5 font-semibold">Spesifikasi Detail</th>
                        <th class="p-3.5 font-semibold text-center">Jml Fisik</th>
                        <th class="p-3.5 font-semibold text-right">Volume / Qty Keluar</th>
                        <th class="p-3.5 font-semibold">Alokasi Project</th>
                        <th class="p-3.5 font-semibold">Output Produk Jadi (Klik Detail)</th>
                    </tr>
                </thead>
                <tbody id="tabelKeluar" class="divide-y divide-slate-100">
                    
                    <tr class="baris-data hover:bg-slate-50 text-slate-700 transition-colors" 
                        data-kategori="Pokok"
                        data-proyek="Resto Namora"
                        data-produk="Kitchen Set Jati"
                        data-target-unit="2"
                        data-material="Plywood / Triplek - Mercy (18mm)"
                        data-qty-fisik="12 Lbr"
                        data-volume-total="12 Lembar">
                        
                        <td class="p-3.5 text-center font-medium text-slate-400">1</td>
                        <td class="p-3.5 text-xs text-slate-400 whitespace-nowrap">06/15/2026</td>
                        <td class="p-3.5"><span class="px-2.5 py-0.5 text-xs font-semibold rounded-full bg-indigo-50 text-indigo-600">Material Pokok</span></td>
                        <td class="p-3.5 font-medium text-slate-800">Plywood / Triplek</td>
                        <td class="p-3.5 text-xs text-slate-500">Mercy (18mm)</td>
                        <td class="p-3.5 text-center font-mono font-semibold text-slate-700 whitespace-nowrap">12 Lbr</td>
                        <td class="p-3.5 text-right font-mono font-bold text-rose-600 whitespace-nowrap">- 12 Lembar</td>
                        
                        <td class="p-3.5">
                            <button onclick="hitungRasioKonsumsi(this)" class="text-left font-semibold text-indigo-600 hover:text-indigo-900 underline decoration-dotted transition-colors">
                                Resto Namora
                            </button>
                        </td>
                        
                        <td class="p-3.5 text-xs">
                            <button onclick="hitungRasioKonsumsi(this)" class="text-left flex items-center space-x-1 group">
                                <span class="text-rose-600 font-medium group-hover:scale-110 transition-transform">🏭 </span>
                                <span class="font-semibold text-slate-700 underline group-hover:text-indigo-600">Kitchen Set Jati</span> 
                                <span class="text-slate-400 font-normal ml-1">(2 Unit)</span>
                            </button>
                        </td>
                    </tr>

                    <tr class="baris-data hover:bg-slate-50 text-slate-700 transition-colors" 
                        data-kategori="Pembantu"
                        data-proyek="General Workshop"
                        data-produk="Non-Manufaktur"
                        data-target-unit="0"
                        data-material="Amplas Roll (Grid 240)"
                        data-qty-fisik="3 Mtr"
                        data-volume-total="3 Meter">
                        <td class="p-3.5 text-center font-medium text-slate-400">2</td>
                        <td class="p-3.5 text-xs text-slate-400 whitespace-nowrap">06/15/2026</td>
                        <td class="p-3.5"><span class="px-2.5 py-0.5 text-xs font-semibold rounded-full bg-blue-50 text-blue-600">Bahan Pembantu</span></td>
                        <td class="p-3.5 font-medium text-slate-800">Amplas Roll (Grid 120/180/240)</td>
                        <td class="p-3.5 text-xs text-slate-500">Kekasaran: Grid 240</td>
                        <td class="p-3.5 text-center font-mono font-semibold text-slate-700 whitespace-nowrap">3 Mtr</td>
                        <td class="p-3.5 text-right font-mono font-bold text-rose-600 whitespace-nowrap">- 3 Meter</td>
                        <td class="p-3.5 font-medium text-slate-500">General Workshop</td>
                        <td class="p-3.5 text-xs text-slate-400 italic">Maintenance / Finishing Room</td>
                    </tr>

                </tbody>
            </table>
        </div>
    </div>
</div>

<div id="modalRasioProduksi" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4 no-print">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-xl border border-slate-100 transform transition-all scale-100 space-y-5">
        
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <div class="flex items-center space-x-2">
                <span class="text-xl">📊</span>
                <h3 class="text-base font-bold text-slate-800">Kalkulator Konsumsi Bahan</h3>
            </div>
            <button onclick="tutupModalRasio()" class="text-slate-400 hover:text-slate-600 text-2xl font-semibold leading-none">&times;</button>
        </div>
        
        <div class="space-y-4 text-sm">
            <div class="bg-slate-50 p-3 rounded-xl border border-slate-200/60 grid grid-cols-2 gap-3">
                <div>
                    <span class="block text-xs font-semibold text-slate-400 uppercase tracking-wider">Nama Proyek</span>
                    <span id="lblProyek" class="font-bold text-slate-700">-</span>
                </div>
                <div>
                    <span class="block text-xs font-semibold text-slate-400 uppercase tracking-wider">Produk Jadi</span>
                    <span id="lblProduk" class="font-bold text-indigo-600">-</span>
                </div>
                <div class="col-span-2 border-t border-slate-200 pt-2 flex justify-between">
                    <span class="text-xs font-medium text-slate-500">Total Output Target:</span>
                    <span id="lblTargetUnit" class="font-bold text-slate-800">-</span>
                </div>
            </div>

            <div class="border border-slate-200 rounded-xl p-4 space-y-2.5">
                <span class="block text-xs font-bold text-slate-400 uppercase tracking-wider">Bahan Baku Terpakai:</span>
                
                <div class="flex justify-between border-b border-slate-100 pb-1.5 text-xs text-slate-500">
                    <span>Item Material:</span>
                    <span id="lblMaterial" class="font-semibold text-slate-800 text-right">-</span>
                </div>
                <div class="flex justify-between border-b border-slate-100 pb-1.5 text-xs text-slate-500">
                    <span>Kuantitas Fisik:</span>
                    <span id="lblQtyFisik" class="font-mono font-semibold text-slate-800">-</span>
                </div>
                <div class="flex justify-between border-b border-slate-100 pb-1.5 text-xs text-slate-500">
                    <span>Volume Total Massa:</span>
                    <span id="lblVolumeTotal" class="font-mono font-bold text-slate-800">-</span>
                </div>

                <div class="pt-2 mt-1 bg-indigo-50/70 p-3 rounded-lg border border-indigo-100 flex flex-col items-center justify-center text-center">
                    <span class="text-xs font-bold text-indigo-900 uppercase tracking-wide mb-1">Rasio Penggunaan Bahan baku</span>
                    <span id="lblHasilRasio" class="font-mono font-extrabold text-indigo-600 text-base">-</span>
                    <p class="text-[11px] text-slate-400 mt-1">Rata-rata material yang dihabiskan untuk membuat 1 Unit Produk</p>
                </div>
            </div>
        </div>

        <div class="flex justify-end pt-1">
            <button onclick="tutupModalRasio()" class="bg-slate-800 hover:bg-slate-900 text-white font-semibold text-xs px-4 py-2 rounded-lg shadow-sm transition-all">
                Selesai Tinjau
            </button>
        </div>
    </div>
</div>

<script>
    // FUNGSI UTAMA: MENGHITUNG KONSUMSI MATERIAL PER UNIT PRODUK
    function hitungRasioKonsumsi(element) {
        // Ambil element baris <tr> terdekat untuk membaca metadata
        const row = element.closest('tr');
        
        const proyek = row.getAttribute('data-proyek');
        const produk = row.getAttribute('data-produk');
        const targetUnit = parseFloat(row.getAttribute('data-target-unit')) || 0;
        const material = row.getAttribute('data-material');
        const qtyFisik = row.getAttribute('data-qty-fisik');
        const volumeTotal = row.getAttribute('data-volume-total');

        // Proteksi jika data penyerapan non-manufaktur (misal bahan pembantu workshop)
        if (targetUnit <= 0 || produk === 'Non-Manufaktur') {
            alert(`Material untuk "${proyek}" ini dialokasikan sebagai pengeluaran umum/bengkel pendukung, bukan untuk output produk manufaktur massal.`);
            return;
        }

        // Tampilkan data ke komponen teks di dalam Modal Pop-up
        document.getElementById('lblProyek').innerText = proyek;
        document.getElementById('lblProduk').innerText = produk;
        document.getElementById('lblTargetUnit').innerText = `${targetUnit} Unit`;
        document.getElementById('lblMaterial').innerText = material;
        document.getElementById('lblQtyFisik').innerText = qtyFisik;
        document.getElementById('lblVolumeTotal').innerText = volumeTotal;

        // Ambil angka mentah dari volume/kuantitas untuk kalkulasi pembagian matematika
        const nilaiAngkaMentah = parseFloat(volumeTotal) || parseFloat(qtyFisik) || 0;
        // Ambil satuan teksnya (misal: "Lembar", "M³", "m²")
        const satuanTeks = volumeTotal.replace(/[0-9.\-\s]/g, '').trim();

        // Rumus Rasio Konsumsi: Total Material Keluar / Total Output Unit Produk Jadi
        const rasioPerUnit = nilaiAngkaMentah / targetUnit;

        // Cetak hasil kalkulasi
        document.getElementById('lblHasilRasio').innerText = `${rasioPerUnit.toFixed(2)} ${satuanTeks} / 1 Unit`;

        // Buka Modal (hilangkan kelas hidden Tailwind)
        document.getElementById('modalRasioProduksi').classList.remove('hidden');
    }

    function tutupModalRasio() {
        document.getElementById('modalRasioProduksi').classList.add('hidden');
    }

    // Fungsi Filter Laporan
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
        
        reindexNomorTabel();
    }

    function reindexNomorTabel() {
        const rows = document.querySelectorAll('.baris-data:not(.hidden)');
        rows.forEach((row, index) => {
            row.querySelector('td:first-child').innerText = index + 1;
        });
    }

    // Fungsi Download PDF
    function downloadPDF() {
        const actionContainer = document.getElementById('actionContainer');
        actionContainer.style.display = 'none';

        const element = document.getElementById('areaCetakUtama');
        
        const opsi = {
            margin:       12,
            filename:     'Laporan_Barang_Keluar_PKSD.pdf',
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