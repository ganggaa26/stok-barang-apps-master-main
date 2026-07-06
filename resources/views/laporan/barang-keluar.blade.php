@extends('layouts.admin')

@section('content')
<script src="https://cdn.tailwindcss.com"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

<style>
    @media print {
        body *, .no-print, #filterContainer, button, nav, aside {
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
                <span class="text-rose-600">Laporan Barang Keluar</span>
            </div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-800">Jurnal & Alokasi Barang Keluar</h1>
            <p class="text-sm text-slate-500 mt-0.5">
                Monitoring real-time untuk audit penyerapan bahan baku terhadap volume output produksi
            </p>
        </div>

        <div id="actionContainer" class="flex flex-wrap items-center gap-2 mt-4 lg:mt-0 no-print">
            <div id="filterContainer" class="flex flex-wrap items-center gap-2 bg-slate-50 border border-slate-200 rounded-xl p-1.5 shadow-sm">
                <div class="bg-white border border-slate-300 rounded-lg px-2 h-8 flex items-center">
                    <span class="text-slate-400 mr-1 text-xs">🔍</span>
                    <input type="text" id="cariNamaItem" onkeyup="jalankanFilterGabungan()" placeholder="Cari nama barang..." class="focus:outline-none text-xs text-slate-700 bg-transparent w-36">
                </div>

                <select id="filterJenisBahan" onchange="jalankanFilterGabungan()" class="bg-white border border-slate-300 rounded-lg px-2.5 py-1 text-xs text-slate-700 focus:ring-2 focus:ring-rose-500 focus:outline-none h-8">
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

            <button onclick="downloadPDF()" class="bg-rose-600 hover:bg-rose-700 text-white font-semibold text-sm px-4 py-2 rounded-lg shadow-sm flex items-center gap-2 transition-all h-9">
                <span>📥</span> PDF
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Total Keluar Terfilter</p>
                <h3 id="widgetTotal" class="text-2xl font-bold text-slate-800 mt-1">
                    {{ $barangKeluar->count() }} Transaksi
                </h3>
            </div>
            <div class="p-3 bg-rose-50 text-rose-600 rounded-lg text-xl">📤</div>
        </div>

        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Penyerapan Mat. Pokok</p>
                <h3 id="widgetPokok" class="text-2xl font-bold text-indigo-600 mt-1">
                    {{ $barangKeluar->where('kategori', 'Pokok')->count() }} Log
                </h3>
            </div>
            <div class="p-3 bg-indigo-50 text-indigo-600 rounded-lg text-xl">🪵</div>
        </div>

        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Penyerapan Mat. Pembantu</p>
                <h3 id="widgetPembantu" class="text-2xl font-bold text-blue-600 mt-1">
                    {{ $barangKeluar->where('kategori', 'Pembantu')->count() }} Log
                </h3>
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
                        <th class="p-3.5 font-semibold">Spesifikasi Detail</th>
                        <th class="p-3.5 font-semibold text-center">Jml Fisik</th>
                        <th class="p-3.5 font-semibold text-right">Volume / Qty Keluar</th>
                        <th class="p-3.5 font-semibold">Alokasi Project</th>
                    </tr>
                </thead>

                <tbody id="tabelKeluar" class="divide-y divide-slate-100">
                    @forelse($barangKeluar as $index => $log)
                        <tr class="baris-data hover:bg-slate-50 text-slate-700 transition-colors"
                            data-kategori="{{ $log->kategori }}"
                            data-tanggal="{{ $log->tanggal }}"
                            data-item="{{ $log->nama_material ?? 'Material tidak ditemukan' }}"
                            data-spesifikasi="{{ $log->spesifikasi ?? '-' }}"
                            data-qty="{{ $log->qty_fisik ?? $log->kuantitas }} {{ $log->satuan_fisik ?? $log->satuan }}"
                            data-volume="- {{ $log->kuantitas }} {{ $log->satuan_input ?? $log->satuan }}"
                            data-proyek="{{ $log->nama_proyek ?? $log->asal_atau_proyek ?? '-' }}"
                            data-produk="{{ $log->nama_produk_jadi ?? 'Non-Manufaktur' }}"
                            data-target-unit="{{ $log->qty_produksi ?? 0 }}"
                            data-material="{{ $log->nama_material ?? 'Material tidak ditemukan' }} - {{ $log->spesifikasi ?? '-' }}">

                            <td class="p-3.5 text-center font-medium text-slate-400">
                                {{ $index + 1 }}
                            </td>

                            <td class="p-3.5 text-xs text-slate-500 whitespace-nowrap">
                                {{ \Carbon\Carbon::parse($log->tanggal)->format('d-m-Y') }}
                            </td>

                            <td class="p-3.5">
                                @if($log->kategori === 'Pokok')
                                    <span class="px-2.5 py-0.5 text-xs font-semibold rounded-full bg-indigo-50 text-indigo-600">
                                        Material Pokok
                                    </span>
                                @else
                                    <span class="px-2.5 py-0.5 text-xs font-semibold rounded-full bg-blue-50 text-blue-600">
                                        Bahan Pembantu
                                    </span>
                                @endif
                            </td>

                            <td class="p-3.5">
                                <button onclick="hitungRasioKonsumsi(this)" class="text-left font-bold text-emerald-600 hover:text-emerald-800 underline decoration-dotted transition-colors">
                                    {{ $log->nama_material ?? 'Material tidak ditemukan' }}
                                </button>
                            </td>

                            <td class="p-3.5 text-xs text-slate-500">
                                {{ $log->spesifikasi ?? '-' }}
                            </td>

                            <td class="p-3.5 text-center font-mono font-semibold text-slate-700 whitespace-nowrap">
                                {{ $log->qty_fisik ?? $log->kuantitas }} {{ $log->satuan_fisik ?? $log->satuan }}
                            </td>

                            <td class="p-3.5 text-right font-mono font-bold text-rose-600 whitespace-nowrap">
                                - {{ $log->kuantitas }} {{ $log->satuan_input ?? $log->satuan }}
                            </td>

                            <td class="p-3.5 text-xs">
                                <button onclick="hitungRasioKonsumsi(this)" class="text-left flex flex-col group">
                                    <span class="font-semibold text-slate-700 underline group-hover:text-indigo-600">
                                        {{ $log->nama_proyek ?? $log->asal_atau_proyek ?? '-' }}
                                    </span>
                                    <span class="text-slate-400 font-normal mt-0.5">
                                        {{ $log->nama_produk_jadi ?? 'Non-Manufaktur' }}
                                        @if($log->qty_produksi)
                                            ({{ $log->qty_produksi }} Unit)
                                        @endif
                                    </span>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="p-8 text-center text-slate-400 italic">
                                Belum ada data barang keluar.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div id="modalRasioProduksi" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4 no-print">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-xl border border-slate-100 space-y-5">

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

            if (namaItemValue && !namaRow.includes(namaItemValue)) cocokNama = false;
            if (tglMulaiValue && tglRow < tglMulaiValue) cocokTanggal = false;
            if (tglSelesaiValue && tglRow > tglSelesaiValue) cocokTanggal = false;
            if (jenisBahanValue !== 'semua' && katRow !== jenisBahanValue) cocokKategori = false;

            if (cocokNama && cocokTanggal && cocokKategori) {
                row.classList.remove('hidden');
                totalTerlihat++;
                if (katRow === 'Pokok') pokokCount++;
                if (katRow === 'Pembantu') pembantuCount++;
            } else {
                row.classList.add('hidden');
            }
        });

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

    function hitungRasioKonsumsi(element) {
        const row = element.closest('tr');

        const proyek = row.getAttribute('data-proyek');
        const produk = row.getAttribute('data-produk');
        const targetUnit = parseFloat(row.getAttribute('data-target-unit')) || 0;
        const material = row.getAttribute('data-material');
        const qtyFisik = row.getAttribute('data-qty');
        const volumeTotal = row.getAttribute('data-volume');

        if (targetUnit <= 0 || produk === 'Non-Manufaktur') {
            alert(`Material untuk "${proyek}" ini dialokasikan sebagai pengeluaran umum/bengkel pendukung, bukan untuk output produk manufaktur massal.`);
            return;
        }

        document.getElementById('lblProyek').innerText = proyek;
        document.getElementById('lblProduk').innerText = produk;
        document.getElementById('lblTargetUnit').innerText = `${targetUnit} Unit`;
        document.getElementById('lblMaterial').innerText = material;
        document.getElementById('lblQtyFisik').innerText = qtyFisik;
        document.getElementById('lblVolumeTotal').innerText = volumeTotal;

        const nilaiAngkaMentah = Math.abs(parseFloat(volumeTotal)) || Math.abs(parseFloat(qtyFisik)) || 0;
        const satuanTeks = volumeTotal.replace(/[0-9.\-\s+]/g, '').trim();
        const rasioPerUnit = nilaiAngkaMentah / targetUnit;

        document.getElementById('lblHasilRasio').innerText = `${rasioPerUnit.toFixed(2)} ${satuanTeks} / 1 Unit`;
        document.getElementById('modalRasioProduksi').classList.remove('hidden');
    }

    function tutupModalRasio() {
        document.getElementById('modalRasioProduksi').classList.add('hidden');
    }

    function downloadPDF() {
        const actionContainer = document.getElementById('actionContainer');
        actionContainer.style.display = 'none';

        const element = document.getElementById('areaCetakUtama');

        const opsi = {
            margin: 12,
            filename: 'Laporan_Barang_Keluar_PKSD.pdf',
            image: { type: 'jpeg', quality: 0.98 },
            html2canvas: { scale: 2, useCORS: true },
            jsPDF: { unit: 'mm', format: 'a4', orientation: 'landscape' }
        };

        html2pdf().set(opsi).from(element).save().then(() => {
            actionContainer.style.display = 'flex';
        });
    }
</script>
@endsection