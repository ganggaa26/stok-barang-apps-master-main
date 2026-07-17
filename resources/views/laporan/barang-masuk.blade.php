@extends('layouts.admin')

@section('content')
<script src="https://cdn.tailwindcss.com"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=IBM+Plex+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">

<style>
    #areaCetakUtama {
        --accent: #4f46e5;
        --accent-dark: #4338ca;
        --accent-soft: #eef2ff;
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
                <span class="text-indigo-600 font-semibold">Laporan Barang Masuk</span>
            </div>
            <h1 class="text-[26px] font-extrabold tracking-tight text-slate-900">Laporan Barang Masuk</h1>
            <p class="text-sm text-slate-500 mt-1 max-w-xl">
               Catatan seluruh material yang diterima gudang, beserta asal dan tanggalnya.
            </p>
        </div>

       <div id="actionContainer" class="flex items-center flex-wrap gap-2 no-print">
            <button onclick="window.print()" class="bg-slate-900 hover:bg-slate-800 text-white font-semibold text-sm px-4 h-9 rounded-lg shadow-sm flex items-center gap-2 transition-all">
                <span>&#128424;&#65039;</span> Cetak
            </button>
            <button onclick="downloadPDF()" class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-sm px-4 h-9 rounded-lg shadow-sm flex items-center gap-2 transition-all">
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
                <input type="text" id="cariNama" onkeyup="jalankanFilterGabungan()" placeholder="Cari nama barang..." class="focus:outline-none text-sm text-slate-700 bg-transparent w-full placeholder:text-slate-400">
            </div>
        </div>
        <div>
            <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-2">Kelompok Kategori</label>
            <select id="filterKategori" onchange="jalankanFilterGabungan()" class="w-full px-3.5 h-[38px] border border-slate-200 rounded-lg text-sm text-slate-700 bg-white focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                <option value="Semua">Semua Kategori</option>
                <option value="Pokok">Material Pokok</option>
                <option value="Pembantu">Bahan Pembantu</option>
            </select>
        </div>
       <div>
            <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-2">Rentang Tanggal Transaksi</label>
            <div class="flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-1.5 border border-slate-200 rounded-lg px-3.5 py-2 sm:h-[38px] sm:py-0 text-sm">
                <input type="date" id="tglMulai" onchange="jalankanFilterGabungan()" class="focus:outline-none text-slate-700 bg-transparent font-ledger text-xs w-full">
                <span class="text-slate-300 font-medium text-center sm:text-left">&rarr;</span>
                <input type="date" id="tglSelesai" onchange="jalankanFilterGabungan()" class="focus:outline-none text-slate-700 bg-transparent font-ledger text-xs w-full">
                <button onclick="resetSemuaFilter()" class="text-slate-400 hover:text-rose-600 px-1 font-bold text-sm transition-colors self-end sm:self-auto" title="Reset Filter">&times;</button>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
        <div class="stat-card bg-white p-4 rounded-xl border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Total Masuk Terfilter</p>
                <h3 id="widgetTotal" class="text-2xl font-extrabold text-slate-900 mt-1 font-ledger">
                    {{ $barangMasuk->count() }} <span class="text-sm font-semibold text-slate-400">Transaksi</span>
                </h3>
            </div>
            <div class="p-3 bg-indigo-50 text-indigo-600 rounded-xl text-xl">&#128229;</div>
        </div>

        <div class="stat-card bg-white p-4 rounded-xl border border-slate-200 shadow-sm flex items-center justify-between" style="--accent:#4f46e5">
            <div>
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Penerimaan Mat. Pokok</p>
                <h3 id="widgetPokok" class="text-2xl font-extrabold text-indigo-600 mt-1 font-ledger">
                    {{ $barangMasuk->filter(fn($item) => $item->material)->count() }} <span class="text-sm font-semibold text-slate-400">Log</span>
                </h3>
            </div>
            <div class="p-3 bg-indigo-50 text-indigo-600 rounded-xl text-xl">&#129717;</div>
        </div>

        <div class="stat-card bg-white p-4 rounded-xl border border-slate-200 shadow-sm flex items-center justify-between" style="--accent:#2563eb">
            <div>
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Penerimaan Mat. Pembantu</p>
                <h3 id="widgetPembantu" class="text-2xl font-extrabold text-blue-600 mt-1 font-ledger">
                    {{ $barangMasuk->filter(fn($item) => $item->materialPembantu)->count() }} <span class="text-sm font-semibold text-slate-400">Log</span>
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
                        <th class="p-3 border border-slate-300 font-semibold text-center">Jenis Transaksi</th>
                        <th class="p-3 border border-slate-300 font-semibold text-center">Jml Fisik</th>
                        <th class="p-3 border border-slate-300 font-semibold text-right">Volume</th>
                        <th class="p-3 border border-slate-300 font-semibold text-center">Satuan</th>
                        <th class="p-3 border border-slate-300 font-semibold">Asal / Supplier</th>
                    </tr>
                </thead>
                <tbody id="tabelMasuk">
                    @forelse($barangMasuk as $index => $item)
                        @php
                            $materialPokok = $item->material;
                            $materialPembantu = $item->materialPembantu;

                            $namaKategoriMaster = strtolower($materialPokok->kategori->nama_kategori ?? '');
                            $satuanKategoriMaster = strtolower($materialPokok->kategori->satuan ?? '');

                            $isLembaran = \Illuminate\Support\Str::contains($namaKategoriMaster, 'lembar') ||
                                          \Illuminate\Support\Str::contains($satuanKategoriMaster, 'lembar');

                            $satuanTeks = $isLembaran ? 'M²' : ($item->satuan ?? $materialPokok->satuan ?? $materialPembantu->satuan ?? 'M³');
                            $namaItemAman = $materialPokok->nama_material ?? $materialPembantu->nama_material ?? $item->nama_barang ?? '-';
                            $statusLogistikAman = $item->jenis_transaksi ?? '-';

                            $qtyAman = $item->qty_fisik ?? 0;
                            $supplierAman = $item->asal_supplier ?? '-';
                        @endphp

                        <tr class="baris-data hover:bg-slate-50 text-slate-700"
                            data-tanggal="{{ $item->tanggal }}"
                            data-item="{{ $namaItemAman }}"
                            data-kategori="{{ $item->material ? 'Pokok' : 'Pembantu' }}"
                            data-spesifikasi="{{ $statusLogistikAman }}"
                            data-qty="{{ $qtyAman }}"
                            data-volume="{{ number_format($item->kuantitas, 4) }} {{ $satuanTeks }}"
                            data-supplier="{{ $supplierAman }}">

                            <td class="p-3 border border-slate-300 text-center font-medium text-slate-400 font-ledger text-xs">{{ $index + 1 }}</td>

                            <td class="p-3 border border-slate-300 text-xs text-slate-500 whitespace-nowrap font-ledger">
                                {{ \Carbon\Carbon::parse($item->tanggal)->format('d-m-Y') }}
                            </td>

                            <td class="p-3 border border-slate-300 font-semibold text-slate-500">
                                {{ $item->material ? 'Material Pokok' : 'Bahan Pembantu' }}
                            </td>

                            <td class="p-3 border border-slate-300 max-w-xs break-words">
                                <button onclick="bukaModalItem(this)" class="text-left font-bold text-indigo-700 hover:text-indigo-900 underline decoration-dotted decoration-indigo-300 underline-offset-2 transition-colors">
                                    {{ $namaItemAman }}
                                </button>
                                <span class="text-xs text-slate-400 block mt-0.5">
                                    {{ $item->material->jenis_material ?? $item->materialPembantu->jenis_material ?? '-' }}
                                </span>
                            </td>

                            <td class="p-3 border border-slate-300 text-center">
                                <span class="inline-block w-24 py-1 text-[11px] font-bold uppercase tracking-wide rounded-md text-center {{ $statusLogistikAman == 'Stok Awal' ? 'bg-blue-50 text-blue-700 border border-blue-200' : 'bg-indigo-50 text-indigo-700 border border-indigo-200' }}">
                                    {{ $statusLogistikAman }}
                                </span>
                            </td>

                            <td class="p-3 border border-slate-300 text-center font-ledger font-semibold text-slate-700 whitespace-nowrap">
                                {{ $qtyAman }}
                            </td>

                            <td class="p-3 border border-slate-300 text-right font-ledger font-bold text-indigo-600 whitespace-nowrap">
                                {{ number_format($item->kuantitas, 4) }}
                            </td>

                            <td class="p-3 border border-slate-300 text-center text-[11px] font-bold font-ledger {{ $isLembaran ? 'text-amber-600 bg-amber-50 rounded p-1' : 'text-slate-400' }}">
                                {!! $satuanTeks !!}
                            </td>

                            <td class="p-3 border border-slate-300 text-xs text-slate-700">
                                <span class="font-semibold text-slate-800 block">
                                    {{ $supplierAman }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="p-10 border border-slate-300 text-center text-slate-400 italic">
                                Belum ada riwayat transaksi barang masuk yang tercatat.
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
                    <select id="pilihBarisPerHalaman" onchange="gantiBarisPerHalaman(this)" class="bg-white border border-slate-300 rounded-lg px-2 py-1 text-xs text-slate-700 focus:ring-2 focus:ring-indigo-500 focus:outline-none">
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

<div id="modalDetailMasuk" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm hidden flex items-center justify-center z-50 p-4 transition-opacity duration-300">
    <div class="bg-white rounded-2xl shadow-xl border border-slate-100 max-w-lg w-full overflow-hidden transform transition-all scale-100 duration-200">

        <div class="bg-gradient-to-r from-slate-900 to-indigo-950 px-6 py-4 flex items-center justify-between">
            <div class="flex items-center space-x-2.5 text-white">
                <span class="text-xl">&#128230;</span>
                <div>
                    <h3 class="text-base font-bold tracking-tight">Rincian Informasi Material</h3>
                    <p class="text-[11px] text-indigo-200 font-medium">Detail penerimaan barang masuk gudang</p>
                </div>
            </div>
            <button onclick="tutupModalItem()" class="text-indigo-200 hover:text-white bg-white/10 hover:bg-white/20 p-1.5 rounded-xl transition-all text-xs font-bold px-3">
                &#10005;
            </button>
        </div>

        <div class="p-6 space-y-4 bg-slate-50/50">
            <div class="grid grid-cols-2 gap-4">
                <div class="bg-white p-3.5 rounded-xl border border-slate-100 shadow-sm col-span-2">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block mb-1">Identitas Item</span>
                    <span id="modalNamaItem" class="text-sm font-bold text-slate-800">-</span>
                </div>

                <div class="bg-white p-3.5 rounded-xl border border-slate-100 shadow-sm">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block mb-1">Tanggal Masuk</span>
                    <span id="modalTanggal" class="text-sm font-bold text-slate-800 font-ledger">-</span>
                </div>

                <div class="bg-white p-3.5 rounded-xl border border-slate-100 shadow-sm">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block mb-1">Kuantitas Fisik</span>
                    <span id="modalQty" class="text-sm font-bold text-slate-800 font-ledger">-</span>
                </div>

                <div class="bg-white p-3.5 rounded-xl border border-slate-100 shadow-sm">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block mb-1">Vol / Luas Akhir</span>
                    <span id="modalVolume" class="text-sm font-bold text-indigo-600 font-ledger">-</span>
                </div>

                <div class="bg-white p-3.5 rounded-xl border border-slate-100 shadow-sm">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block mb-1">Status Logistik</span>
                    <span id="modalSpesifikasi" class="text-sm font-bold text-slate-800">-</span>
                </div>
            </div>

            <div class="bg-indigo-50/70 border border-indigo-100 rounded-xl p-4 space-y-2">
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-indigo-500 block mb-0.5">Asal / Supplier Resmi</span>
                    <p class="text-xs text-slate-500">Sumber pemasukan material ke dalam gudang.</p>
                </div>
                <div class="pt-1">
                    <span id="modalSupplier" class="w-full text-center text-sm font-extrabold text-indigo-700 bg-white px-3 py-2.5 rounded-lg border border-indigo-100 shadow-sm block whitespace-nowrap">
                        -
                    </span>
                </div>
            </div>
        </div>

        <div class="bg-slate-50 px-6 py-3.5 flex justify-end border-t border-slate-100">
            <button onclick="tutupModalItem()" class="px-4 py-2 bg-slate-200 hover:bg-slate-300 text-slate-700 text-xs font-bold rounded-xl transition-all shadow-sm">
                Tutup Dokumen
            </button>
        </div>

    </div>
</div>

<script>
    let halamanSaatIni = 1;
    let barisPerHalaman = 25;

    function jalankanFilterGabungan() {
        const keywordInput = document.getElementById('cariNama').value.toLowerCase();
        const kategoriInput = document.getElementById('filterKategori').value;
        const tglMulaiValue = document.getElementById('tglMulai').value;
        const tglSelesaiValue = document.getElementById('tglSelesai').value;

        const rows = document.querySelectorAll('#tabelMasuk .baris-data');

        let totalTerlihat = 0, pokokCount = 0, pembantuCount = 0;

        rows.forEach(row => {
            const namaItem = row.getAttribute('data-item').toLowerCase();
            const kategori = row.getAttribute('data-kategori');
            const tanggalRow = row.getAttribute('data-tanggal');
            const spesifikasi = (row.getAttribute('data-spesifikasi') || '').toLowerCase();
            const supplier = (row.getAttribute('data-supplier') || '').toLowerCase();

            let cocokKeyword = true;
            let cocokTanggal = true;
            let cocokKategori = true;

            if (keywordInput && !(namaItem.includes(keywordInput) || spesifikasi.includes(keywordInput) || supplier.includes(keywordInput))) {
                cocokKeyword = false;
            }
            if (tglMulaiValue && tanggalRow < tglMulaiValue) cocokTanggal = false;
            if (tglSelesaiValue && tanggalRow > tglSelesaiValue) cocokTanggal = false;
            if (kategoriInput !== 'Semua' && kategori !== kategoriInput) cocokKategori = false;

            if (cocokKeyword && cocokKategori && cocokTanggal) {
                row.classList.remove('hidden');
                totalTerlihat++;
                if (kategori === 'Pokok') pokokCount++;
                if (kategori === 'Pembantu') pembantuCount++;
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
        document.getElementById('cariNama').value = '';
        document.getElementById('filterKategori').value = 'Semua';
        document.getElementById('tglMulai').value = '';
        document.getElementById('tglSelesai').value = '';
        jalankanFilterGabungan();
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
        const semuaRow = Array.from(document.querySelectorAll('#tabelMasuk .baris-data'));
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

    function downloadPDF() {
        const actionContainer = document.getElementById('actionContainer');
        actionContainer.style.display = 'none';

        const element = document.getElementById('areaCetakUtama');

        const opsi = {
            margin: 12,
            filename: 'Laporan_Barang_Masuk_Komprehensif.pdf',
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

        const rows = Array.from(document.querySelectorAll('#tabelMasuk .baris-data'))
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
        XLSX.utils.book_append_sheet(wb, ws, 'Barang Masuk');

        const tanggal = new Date().toISOString().split('T')[0];
        XLSX.writeFile(wb, `Laporan_Barang_Masuk_${tanggal}.xlsx`);
    }

    document.addEventListener('DOMContentLoaded', function () {
        renderHalaman();
    });
</script>
<x-floating-paginator accent-btn="bg-indigo-600 hover:bg-indigo-700" />    {{-- di barang-masuk.blade.php --}}
@endsection