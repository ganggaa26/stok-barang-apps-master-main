@extends('layouts.admin')

@section('content')
<script src="https://cdn.tailwindcss.com"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=IBM+Plex+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">

<style>
    #areaCetakUtama {
        --accent: #e11d48;
        --accent-dark: #be123c;
        --accent-soft: #fff1f2;
        font-family: 'Inter', ui-sans-serif, system-ui, sans-serif;
    }
    .font-ledger { font-family: 'IBM Plex Mono', ui-monospace, monospace; font-variant-numeric: tabular-nums; }

    .ledger-stamp {
        display: inline-flex; align-items: center; gap: 6px;
        font-family: 'IBM Plex Mono', monospace;
        font-size: 10px; letter-spacing: .14em; text-transform: uppercase; font-weight: 600;
        color: var(--accent); border: 1.5px dashed var(--accent);
        padding: 4px 10px; border-radius: 5px; transform: rotate(-1deg);
        background: var(--accent-soft); white-space: nowrap;
    }

    .stat-card { position: relative; overflow: hidden; }
    .stat-card::before {
        content: ""; position: absolute; top: 0; left: 0; right: 0; height: 3px;
        background: var(--accent);
    }

    .ledger-table thead th { position: sticky; top: 0; z-index: 10; }
    .ledger-table tbody tr { transition: background-color .12s ease; }

    input:focus-visible, select:focus-visible, button:focus-visible {
        outline: 2px solid var(--accent); outline-offset: 1px;
    }

   @media print {
    body * {
        visibility: hidden;
    }
    #areaCetakUtama, #areaCetakUtama * {
        visibility: visible;
    }
    .no-print, #filterContainer, button, nav, aside {
        display: none !important;
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

    .page-hidden {
        display: none;
    }
</style>

<div id="areaCetakUtama" class="w-full mx-auto my-2 text-slate-700">

    <div class="mb-7 flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4 border-b border-slate-200 pb-5">
        <div>
            <div class="flex items-center space-x-2 text-xs text-slate-400 font-medium mb-2 no-print">
                <a href="{{ route('dashboard') }}" class="text-indigo-600 hover:text-slate-400 hover:underline transition-colors">Pelaporan</a>
                <span class="text-slate-300">/</span>
                <span class="text-rose-600 font-semibold">Laporan Barang Keluar</span>
            </div>
            <h1 class="text-[26px] font-extrabold tracking-tight text-slate-900">Laporan Barang Keluar</h1>
            <p class="text-sm text-slate-500 mt-1 max-w-xl">
               Catatan material yang keluar dari gudang beserta proyek atau produk tujuannya.
            </p>
        </div>

        <div id="actionContainer" class="flex items-center gap-2 no-print">
            <button onclick="window.print()" class="bg-slate-900 hover:bg-slate-800 text-white font-semibold text-sm px-4 h-9 rounded-lg shadow-sm flex items-center gap-2 transition-all">
                <span>&#128424;&#65039;</span> Cetak
            </button>
            <button onclick="downloadPDF()" class="bg-rose-600 hover:bg-rose-700 text-white font-semibold text-sm px-4 h-9 rounded-lg shadow-sm flex items-center gap-2 transition-all">
                <span>&#128229;</span> PDF
            </button>
            <button onclick="exportExcel()" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm px-4 h-9 rounded-lg shadow-sm flex items-center gap-2 transition-all">
                <span>&#128202;</span> Excel
            </button>
        </div>
    </div>

    <div id="filterContainer" class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm mb-6 grid grid-cols-1 md:grid-cols-3 gap-4 no-print">
        <div>
            <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-2">Cari Nama Material</label>
            <div class="flex items-center gap-1.5 border border-slate-200 rounded-lg px-3.5 h-[38px]">
                <span class="text-slate-400 text-xs">&#128269;</span>
                <input type="text" id="cariNamaItem" onkeyup="jalankanFilterGabungan()" placeholder="Cari nama barang..." class="focus:outline-none text-sm text-slate-700 bg-transparent w-full placeholder:text-slate-400">
            </div>
        </div>
        <div>
            <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-2">Kelompok Kategori</label>
            <select id="filterJenisBahan" onchange="jalankanFilterGabungan()" class="w-full px-3.5 h-[38px] border border-slate-200 rounded-lg text-sm text-slate-700 bg-white focus:ring-2 focus:ring-rose-500 focus:outline-none">
                <option value="semua">Semua Jenis Bahan</option>
                <option value="Pokok">Material Pokok</option>
                <option value="Pembantu">Bahan Pembantu</option>
            </select>
        </div>
        <div>
            <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-2">Rentang Tanggal Transaksi</label>
            <div class="flex items-center gap-1.5 border border-slate-200 rounded-lg px-3.5 h-[38px] text-sm">
                <input type="date" id="tglMulai" onchange="jalankanFilterGabungan()" class="focus:outline-none text-slate-700 bg-transparent font-ledger text-xs w-full">
                <span class="text-slate-300 font-medium">&rarr;</span>
                <input type="date" id="tglSelesai" onchange="jalankanFilterGabungan()" class="focus:outline-none text-slate-700 bg-transparent font-ledger text-xs w-full">
                <button onclick="resetSemuaFilter()" class="text-slate-400 hover:text-rose-600 px-1 font-bold text-sm transition-colors" title="Reset Filter">&times;</button>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
        <div class="stat-card bg-white p-4 rounded-xl border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Total Keluar Terfilter</p>
                <h3 id="widgetTotal" class="text-2xl font-extrabold text-slate-900 mt-1 font-ledger">
                    {{ $barangKeluar->count() }} <span class="text-sm font-semibold text-slate-400">Transaksi</span>
                </h3>
            </div>
            <div class="p-3 bg-rose-50 text-rose-600 rounded-xl text-xl">&#128228;</div>
        </div>

        <div class="stat-card bg-white p-4 rounded-xl border border-slate-200 shadow-sm flex items-center justify-between" style="--accent:#4f46e5">
            <div>
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Penyerapan Mat. Pokok</p>
                <h3 id="widgetPokok" class="text-2xl font-extrabold text-indigo-600 mt-1 font-ledger">
                    {{ $barangKeluar->filter(fn($item) => stripos($item->kategori, 'pokok') !== false)->count() }} <span class="text-sm font-semibold text-slate-400">Log</span>
                </h3>
            </div>
            <div class="p-3 bg-indigo-50 text-indigo-600 rounded-xl text-xl">&#129717;</div>
        </div>

        <div class="stat-card bg-white p-4 rounded-xl border border-slate-200 shadow-sm flex items-center justify-between" style="--accent:#2563eb">
            <div>
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Penyerapan Mat. Pembantu</p>
                <h3 id="widgetPembantu" class="text-2xl font-extrabold text-blue-600 mt-1 font-ledger">
                    {{ $barangKeluar->filter(fn($item) => stripos($item->kategori, 'pembantu') !== false)->count() }} <span class="text-sm font-semibold text-slate-400">Log</span>
                </h3>
            </div>
            <div class="p-3 bg-blue-50 text-blue-600 rounded-xl text-xl">&#128295;</div>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5 space-y-4">
        <div class="overflow-x-auto rounded-lg border border-slate-300">
            <table class="ledger-table w-full text-sm text-left border-collapse border border-slate-300">
                <thead>
                    <tr class="bg-slate-900 text-white text-[11px] uppercase tracking-wider">
                        <th class="p-3 border border-slate-300 font-semibold text-center w-12">No.</th>
                        <th class="p-3 border border-slate-300 font-semibold">Tanggal</th>
                        <th class="p-3 border border-slate-300 font-semibold">Kelompok Modul</th>
                        <th class="p-3 border border-slate-300 font-semibold">Nama Item Material</th>
                        <th class="p-3 border border-slate-300 font-semibold">Spesifikasi Detail</th>
                        <th class="p-3 border border-slate-300 font-semibold text-center">Jml Fisik</th>
                        <th class="p-3 border border-slate-300 font-semibold text-right">Volume / Qty Keluar</th>
                        <th class="p-3 border border-slate-300 font-semibold">Alokasi Project</th>
                    </tr>
                </thead>

                <tbody id="tabelKeluar">
                    @forelse($barangKeluar as $index => $log)
                        <tr class="baris-data hover:bg-slate-50 text-slate-700"
                            data-kategori="{{ $log->kategori }}"
                            data-tanggal="{{ $log->tanggal }}"
                            data-item="{{ $log->nama_material ?? 'Material tidak ditemukan' }}"
                            data-spesifikasi="{{ $log->spesifikasi ?? '-' }}"
                            data-qty="{{ $log->qty_fisik ?? $log->kuantitas }} {{ $log->satuan_fisik ?? $log->satuan }}"
                            data-volume="- {{ $log->kuantitas }} {{ $log->satuan_input ?? $log->satuan }}"
                            data-proyek="{{ $log->nama_proyek ?? $log->asal_atau_proyek ?? '-' }}"
                           data-produk="{{ (isset($log->nama_produk_jadi) && $log->nama_produk_jadi && $log->nama_produk_jadi !== 'Non-Manufaktur' && $log->nama_produk_jadi !== 'Produksi') ? $log->nama_produk_jadi : '' }}"

                            data-target-unit="{{ $log->qty_produksi ?? 0 }}"
                            data-material="{{ $log->nama_material ?? 'Material tidak ditemukan' }} - {{ $log->spesifikasi ?? '-' }}">
                            <td class="p-3 border border-slate-300 text-center font-medium text-slate-400 font-ledger text-xs">
                                {{ $index + 1 }}
                            </td>

                            <td class="p-3 border border-slate-300 text-xs text-slate-500 whitespace-nowrap font-ledger">
                                {{ \Carbon\Carbon::parse($log->tanggal)->format('d-m-Y') }}
                            </td>

                            <td class="p-3 border border-slate-300">
                            @if(isset($log->kategori) && (in_array(strtolower($log->kategori), ['pokok', 'material pokok', 'bahan baku', 'baku', 'kayu solid', 'kayu'])))
                                <span class="inline-block px-2.5 py-1 text-[11px] font-bold uppercase tracking-wide rounded-md bg-indigo-50 text-indigo-700 border border-indigo-200">
                                    Material Pokok
                                </span>
                            @else
                                <span class="inline-block px-2.5 py-1 text-[11px] font-bold uppercase tracking-wide rounded-md bg-blue-50 text-blue-700 border border-blue-200">
                                    Bahan Pembantu
                                </span>
                            @endif
                        </td>
                            <td class="p-3 border border-slate-300 max-w-xs break-words">
                                <button onclick="hitungRasioKonsumsi(this)" class="text-left font-bold text-rose-700 hover:text-rose-900 underline decoration-dotted decoration-rose-300 underline-offset-2 transition-colors">
                                    {{ $log->nama_material ?? 'Material tidak ditemukan' }}
                                </button>
                            </td>

                            <td class="p-3 border border-slate-300 text-xs text-slate-500">
                                {{ $log->spesifikasi ?? '-' }}
                            </td>

                            <td class="p-3 border border-slate-300 text-center font-ledger font-semibold text-slate-700 whitespace-nowrap">
                                {{ $log->qty_fisik ?? $log->kuantitas }} {{ $log->satuan_fisik ?? $log->satuan }}
                            </td>

                            <td class="p-3 border border-slate-300 text-right font-ledger font-bold text-rose-600 whitespace-nowrap">
                                - {{ $log->kuantitas }} {{ $log->satuan_input ?? $log->satuan }}
                            </td>

                           <td class="p-3 border border-slate-300 text-xs text-slate-700">
                            <span class="font-semibold text-slate-800 block">
                                {{ $log->nama_proyek ?? $log->asal_atau_proyek ?? '-' }}
                            </span>

                            <span class="text-[11px] text-slate-400 block mt-0.5">
                                {{ (isset($log->nama_produk_jadi) && $log->nama_produk_jadi && $log->nama_produk_jadi !== 'Non-Manufaktur' && $log->nama_produk_jadi !== 'Produksi') ? $log->nama_produk_jadi : 'Produk Jadi' }}
                            </span>
                        </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="p-10 border border-slate-300 text-center text-slate-400 italic">
                                Belum ada data barang keluar.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pt-1 border-t border-slate-100 no-print">
            <span id="infoPaginasi" class="text-xs text-slate-500 font-ledger">Menampilkan 0-0 dari 0 data</span>

            <div class="flex items-center gap-3">
                <div class="flex items-center gap-1.5 text-xs text-slate-500">
                    <span>Baris per halaman</span>
                    <select id="pilihBarisPerHalaman" onchange="gantiBarisPerHalaman(this)" class="bg-white border border-slate-300 rounded-lg px-2 py-1 text-xs text-slate-700 focus:ring-2 focus:ring-rose-500 focus:outline-none">
                        <option value="25" selected>25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                        <option value="semua">Semua</option>
                    </select>
                </div>
                <div id="tombolHalaman" class="flex items-center gap-1"></div>
            </div>
        </div>
    </div>
</div>

<div id="modalRasioProduksi" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm hidden flex items-center justify-center z-50 p-4 transition-opacity duration-300">
    <div class="bg-white rounded-2xl shadow-xl border border-slate-100 max-w-lg w-full overflow-hidden transform transition-all scale-100 duration-200">

        <div class="bg-gradient-to-r from-slate-900 to-rose-950 px-6 py-4 flex items-center justify-between">
            <div class="flex items-center space-x-2.5 text-white">
                <span class="text-xl">&#128202;</span>
                <div>
                    <h3 class="text-base font-bold tracking-tight">Pelacakan Distribusi Bahan</h3>
                    <p class="text-[11px] text-rose-200 font-medium">Detail tujuan penyerapan material produksi</p>
                </div>
            </div>
            <button onclick="tutupModalRasio()" class="text-rose-200 hover:text-white bg-white/10 hover:bg-white/20 p-1.5 rounded-xl transition-all text-xs font-bold px-3">
                &#10005;
            </button>
        </div>

        <div class="p-6 space-y-4 bg-slate-50/50">
            <div class="grid grid-cols-2 gap-4">
                <div class="bg-white p-3.5 rounded-xl border border-slate-100 shadow-sm">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block mb-1">Nomor SPK</span>
                    <span id="lblTargetUnit" class="text-sm font-bold text-slate-800 font-ledger">-</span>
                </div>

                <div class="bg-white p-3.5 rounded-xl border border-slate-100 shadow-sm">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block mb-1">Nama Material</span>
                    <span id="lblMaterial" class="text-sm font-bold text-slate-800">-</span>
                </div>

                <div class="bg-white p-3.5 rounded-xl border border-slate-100 shadow-sm">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block mb-1">Qty Fisik Keluar</span>
                    <span id="lblQtyFisik" class="text-sm font-bold text-slate-800 font-ledger">-</span>
                </div>

                <div class="bg-white p-3.5 rounded-xl border border-slate-100 shadow-sm">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block mb-1">Volume / Luas Total</span>
                    <span id="lblVolumeTotal" class="text-sm font-bold text-slate-800 font-ledger">-</span>
                </div>
            </div>

            <div class="bg-rose-50/70 border border-rose-100 rounded-xl p-4 space-y-2">
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-rose-500 block mb-0.5">Tujuan Penggunaan Bahan</span>
                    <p class="text-xs text-slate-500">Material dikeluarkan dari gudang demi peruntukan komponen berikut:</p>
                </div>
                <div class="pt-1">
                    <span id="lblHasilRasio" class="w-full text-center text-sm font-extrabold text-rose-700 bg-white px-3 py-2.5 rounded-lg border border-rose-100 shadow-sm block whitespace-nowrap">
                        -
                    </span>
                </div>
            </div>
        </div>

        <div class="bg-slate-50 px-6 py-3.5 flex justify-end border-t border-slate-100">
            <button onclick="tutupModalRasio()" class="px-4 py-2 bg-slate-200 hover:bg-slate-300 text-slate-700 text-xs font-bold rounded-xl transition-all shadow-sm">
                Tutup Dokumen
            </button>
        </div>

    </div>
</div>

<script>
    let halamanSaatIni = 1;
    let barisPerHalaman = 25;

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

        document.getElementById('widgetTotal').innerHTML = `${totalTerlihat} <span class="text-sm font-semibold text-slate-400">Transaksi</span>`;
        document.getElementById('widgetPokok').innerHTML = `${pokokCount} <span class="text-sm font-semibold text-slate-400">Log</span>`;
        document.getElementById('widgetPembantu').innerHTML = `${pembantuCount} <span class="text-sm font-semibold text-slate-400">Log</span>`;

        halamanSaatIni = 1;
        renderHalaman();
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

   function gantiBarisPerHalaman(select) {
    barisPerHalaman = select.value === 'semua' ? Infinity : parseInt(select.value, 10);

    // sinkronkan dropdown bawah tabel & floating panel
    document.getElementById('pilihBarisPerHalaman').value = select.value;
    document.getElementById('pilihBarisPerHalamanFloat').value = select.value;

    halamanSaatIni = 1;
    renderHalaman();
}

    function pindahHalaman(nomorHalaman) {
        halamanSaatIni = nomorHalaman;
        renderHalaman();
    }

    function renderHalaman() {
        const semuaRow = Array.from(document.querySelectorAll('#tabelKeluar .baris-data'));
        const rowCocok = semuaRow.filter(row => !row.classList.contains('hidden'));
        const totalData = rowCocok.length;

        const totalHalaman = barisPerHalaman === Infinity
            ? 1
            : Math.max(1, Math.ceil(totalData / barisPerHalaman));

        if (halamanSaatIni > totalHalaman) halamanSaatIni = totalHalaman;
        if (halamanSaatIni < 1) halamanSaatIni = 1;

        const mulai = barisPerHalaman === Infinity ? 0 : (halamanSaatIni - 1) * barisPerHalaman;
        const selesai = barisPerHalaman === Infinity ? totalData : mulai + barisPerHalaman;

        rowCocok.forEach((row, index) => {
            const nomorSelEl = row.querySelector('td:first-child');
            if (index >= mulai && index < selesai) {
                row.classList.remove('page-hidden');
                if (nomorSelEl) nomorSelEl.innerText = index + 1;
            } else {
                row.classList.add('page-hidden');
            }
        });

        renderInfoDanTombolPaginasi(totalData, totalHalaman, mulai, selesai);
    }

   function renderInfoDanTombolPaginasi(totalData, totalHalaman, mulai, selesai) {
        const tampilMulai = totalData === 0 ? 0 : mulai + 1;
        const tampilSelesai = Math.min(selesai, totalData);
        const teksInfo = `Menampilkan ${tampilMulai}-${tampilSelesai} dari ${totalData} data`;

        document.getElementById('infoPaginasi').innerText = teksInfo;
        const infoFloat = document.getElementById('infoPaginasiFloat');
        if (infoFloat) infoFloat.innerText = teksInfo;

        const badgeHal = document.getElementById('floatBadgeHal');
        if (badgeHal) badgeHal.innerText = `${halamanSaatIni}/${totalHalaman}`;

        ['tombolHalaman', 'tombolHalamanFloat'].forEach(idKontainer => {
            const kontainer = document.getElementById(idKontainer);
            if (!kontainer) return;
            kontainer.innerHTML = '';
            if (totalHalaman <= 1) return;

            const buatTombol = (label, nomor, aktif = false, nonaktif = false) => {
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.innerText = label;
                btn.disabled = nonaktif;
                btn.className = 'px-2 py-1 text-[11px] font-semibold rounded-lg border transition-colors '
                    + (aktif
                        ? 'bg-indigo-600 text-white border-indigo-600'
                        : 'bg-white text-slate-600 border-slate-300 hover:bg-slate-50')
                    + (nonaktif ? ' opacity-40 cursor-not-allowed' : '');
                if (!nonaktif) btn.onclick = () => pindahHalaman(nomor);
                return btn;
            };

            const buatElipsis = () => {
                const span = document.createElement('span');
                span.innerText = '...';
                span.className = 'px-1 text-slate-400 text-xs';
                return span;
            };

            kontainer.appendChild(buatTombol('‹ Sebelumnya', halamanSaatIni - 1, false, halamanSaatIni === 1));

            let awal = Math.max(1, halamanSaatIni - 2);
            let akhir = Math.min(totalHalaman, awal + 4);
            awal = Math.max(1, akhir - 4);

            if (awal > 1) {
                kontainer.appendChild(buatTombol('1', 1));
                if (awal > 2) kontainer.appendChild(buatElipsis());
            }

            for (let i = awal; i <= akhir; i++) {
                kontainer.appendChild(buatTombol(String(i), i, i === halamanSaatIni));
            }

            if (akhir < totalHalaman) {
                if (akhir < totalHalaman - 1) kontainer.appendChild(buatElipsis());
                kontainer.appendChild(buatTombol(String(totalHalaman), totalHalaman));
            }

            kontainer.appendChild(buatTombol('Selanjutnya ›', halamanSaatIni + 1, false, halamanSaatIni === totalHalaman));
        });
    }

  function hitungRasioKonsumsi(element) {
        const row = element.closest('tr');

        const proyek = row.getAttribute('data-proyek') || '-';
        const produk = row.getAttribute('data-produk') || '';
        const material = row.getAttribute('data-material') || '-';
        const qtyFisik = row.getAttribute('data-qty') || '0';
        const volumeTotal = row.getAttribute('data-volume') || '0';

        const volumeBersih = volumeTotal.replace(/^-?\s*/, '');

        document.getElementById('lblTargetUnit').textContent = proyek;
        document.getElementById('lblMaterial').textContent = material;
        document.getElementById('lblQtyFisik').textContent = qtyFisik;
        document.getElementById('lblVolumeTotal').textContent = volumeBersih;

        if (!produk || produk.trim() === '' || produk === 'Non-Manufaktur' || produk === 'Produksi' || produk === '-') {
            document.getElementById('lblHasilRasio').textContent = `Alokasi: Produk Jadi`;
        } else {
            document.getElementById('lblHasilRasio').textContent = `Alokasi: ${produk}`;
        }

        document.getElementById('modalRasioProduksi').classList.remove('hidden');
    }

    function bukaModal(targetUnit, material, qtyFisik, volumeTotal) {
        document.getElementById('lblTargetUnit').innerText = `${targetUnit} Unit`;
        document.getElementById('lblMaterial').innerText = material;
        document.getElementById('lblQtyFisik').innerText = qtyFisik;
        document.getElementById('lblVolumeTotal').innerText = volumeTotal;

        const nilaiAngkaMentah = Math.abs(parseFloat(volumeTotal)) || Math.abs(parseFloat(qtyFisik)) || 0;
        const satuanTeks = volumeTotal.replace(/[0-9.\-\s+]/g, '').trim() || 'Unit';

        const unitJadi = parseFloat(targetUnit) || 1;
        const rasioPerUnit = nilaiAngkaMentah / unitJadi;

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

    function exportExcel() {
    const table = document.querySelector('table');
    const headers = Array.from(table.querySelectorAll('thead th')).map(th => th.innerText.trim());

    const rows = Array.from(document.querySelectorAll('#tabelKeluar .baris-data'))
        .filter(row => !row.classList.contains('hidden'));

    const data = rows.map(row =>
        Array.from(row.querySelectorAll('td')).map(td => td.innerText.trim().replace(/\n+/g, ' - '))
    );

    if (data.length === 0) {
        alert('Tidak ada data untuk diexport (cek filter yang aktif).');
        return;
    }

    const worksheetData = [headers, ...data];
    const ws = XLSX.utils.aoa_to_sheet(worksheetData);

    const colWidths = headers.map((header, colIndex) => {
        const semuaIsiKolom = [header, ...data.map(row => row[colIndex] || '')];
        const panjangMax = Math.max(...semuaIsiKolom.map(teks => String(teks).length));
        return { wch: Math.min(panjangMax + 2, 40) };
    });
    ws['!cols'] = colWidths;

    const wb = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(wb, ws, 'Barang Keluar');

    const tanggal = new Date().toISOString().split('T')[0];
    XLSX.writeFile(wb, `Laporan_Barang_Keluar_${tanggal}.xlsx`);
}

document.addEventListener('DOMContentLoaded', function () {
    renderHalaman();
});
</script>
<x-floating-paginator accent-btn="bg-rose-600 hover:bg-rose-700" />        {{-- di barang-keluar.blade.php --}}
@endsection