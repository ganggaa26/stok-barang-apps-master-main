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
                <span class="text-emerald-600">Laporan Stok</span>
            </div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-800">Jurnal & Rekapitulasi Stok Gudang</h1>
            <p class="text-sm text-slate-500 mt-0.5">Monitoring real-time untuk analisis batas aman (Safety Stock) produksi manufaktur</p>
        </div>
        
        <div id="actionContainer" class="flex flex-wrap items-center gap-2 mt-4 lg:mt-0 no-print">
            <div id="filterContainer" class="flex flex-wrap items-center gap-2 bg-slate-50 border border-slate-200 rounded-xl p-1.5 shadow-sm">
                
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
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Total Item Terfilter</p>
                <h3 id="widgetTotal" class="text-2xl font-bold text-slate-800 mt-1">{{ $laporanStok->count() }} Material</h3>
            </div>
            <div class="p-3 bg-emerald-50 text-emerald-600 rounded-lg text-xl">📦</div>
        </div>
        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Stok Material Pokok</p>
                <h3 id="widgetPokok" class="text-2xl font-bold text-indigo-600 mt-1">{{ $laporanStok->where('kategori', 'Pokok')->count() }} Item</h3>
            </div>
            <div class="p-3 bg-indigo-50 text-indigo-600 rounded-lg text-xl">🪵</div>
        </div>
        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Stok Bahan Pembantu</p>
                <h3 id="widgetPembantu" class="text-2xl font-bold text-blue-600 mt-1">{{ $laporanStok->where('kategori', 'Pembantu')->count() }} Item</h3>
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
                        <th class="p-3.5 font-semibold">Nama Item Material (Klik Detail)</th>
                        <th class="p-3.5 font-semibold">Kelompok</th>
                        <th class="p-3.5 font-semibold text-right">Stok Awal</th>
                        <th class="p-3.5 font-semibold text-right text-emerald-400">Masuk (+)</th>
                        <th class="p-3.5 font-semibold text-right text-rose-400">Keluar (-)</th>
                        <th class="p-3.5 font-semibold text-right">Stok Akhir</th>
                        <th class="p-3.5 font-semibold text-center">Jml Batang/Lbr</th>
                        <th class="p-3.5 font-semibold text-center">Satuan</th>
                        <th class="p-3.5 font-semibold text-center">Status</th>
                    </tr>
                </thead>
                <tbody id="tabelStok" class="divide-y divide-slate-100">
    @forelse($laporanStok as $index => $item)
    @php
        // Hitung Stok Awal Logis sebelum ada mutasi masuk/keluar di periode ini
        $masuk = $item->total_masuk ?? 0;
        $keluar = $item->total_keluar ?? 0;
        $stokAwal = $item->stok_sekarang - $masuk + $keluar;
    @endphp
    <tr class="baris-data hover:bg-slate-50 text-slate-700 transition-colors"
        data-kategori="{{ $item->kategori }}"
        data-kode="{{ $item->kode }}"
        data-nama="{{ $item->nama }}"
        data-tanggal="{{ now()->format('Y-m-d') }}"
        data-detail="{{ $item->jenis }}"
        data-stok-awal="{{ $stokAwal }}"
        data-masuk="{{ $masuk }}"
        data-keluar="{{ $keluar }}"
        data-stok-akhir="{{ $item->stok_sekarang }}"
        data-satuan="{{ $item->satuan }}">

        <td class="p-3.5 text-center font-medium text-slate-400">{{ $index + 1 }}</td>
        <td class="p-3.5 font-bold text-slate-800 whitespace-nowrap">{{ $item->kode }}</td>

        <td class="p-3.5">
            <button onclick="analisisTurnoverStok(this)" class="text-left block group">
                <span class="font-bold text-emerald-600 underline decoration-dotted group-hover:text-emerald-800 block">
                    {{ $item->nama }}
                </span>
                <span class="text-xs text-slate-400 block mt-0.5">
                    {{ $item->jenis }}
                </span>
            </button>
        </td>

        <td class="p-3.5">
            @if($item->kategori === 'Pokok')
                <span class="px-2.5 py-0.5 text-xs font-semibold rounded-full bg-indigo-50 text-indigo-600 whitespace-nowrap">
                    Material Pokok
                </span>
            @else
                <span class="px-2.5 py-0.5 text-xs font-semibold rounded-full bg-blue-50 text-blue-600 whitespace-nowrap">
                    Bahan Pembantu
                </span>
            @endif
        </td>
        <td class="p-3.5 text-right font-mono text-slate-500">{{ number_format($stokAwal, 2) }}</td>
        <td class="p-3.5 text-right font-mono font-semibold text-emerald-600">{{ number_format($masuk, 2) }}</td>
        <td class="p-3.5 text-right font-mono font-semibold text-rose-600">{{ number_format($keluar, 2) }}</td>
        <td class="p-3.5 text-right font-mono font-bold text-slate-800">{{ number_format($item->stok_sekarang, 2) }}</td>
        <td class="p-3.5 text-center font-mono font-semibold text-slate-700 whitespace-nowrap">{{ number_format($item->stok_sekarang, 2) }}</td>
        <td class="p-3.5 text-center text-xs font-semibold text-slate-400">{{ $item->satuan }}</td>

        <td class="p-3.5 text-center">
            @if($item->stok_sekarang <= $item->stok_minimum)
                <button onclick="analisisTurnoverStok(this)" class="px-2.5 py-0.5 text-xs font-bold rounded-full bg-rose-50 text-rose-600 hover:bg-rose-100 whitespace-nowrap transition-colors">
                    Butuh Restock
                </button>
            @else
                <button onclick="analisisTurnoverStok(this)" class="px-2.5 py-0.5 text-xs font-bold rounded-full bg-emerald-50 text-emerald-600 hover:bg-emerald-100 whitespace-nowrap transition-colors">
                    Aman
                </button>
            @endif
        </td>
    </tr>
                    @empty
                    <tr>
                        <td colspan="11" class="p-8 text-center text-slate-400 italic">
                            Belum ada data stok material.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div id="modalAnalisisStok" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4 no-print">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-xl border border-slate-100 space-y-5">
        
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <div class="flex items-center space-x-2">
                <span class="text-xl">📊</span>
                <h3 class="text-base font-bold text-slate-800">Audit & Analisis Perputaran Stok</h3>
            </div>
            <button onclick="tutupModalStok()" class="text-slate-400 hover:text-slate-600 text-2xl font-semibold leading-none">&times;</button>
        </div>
        
        <div class="space-y-4 text-sm">
            <div class="bg-slate-50 p-3 rounded-xl border border-slate-200/60 grid grid-cols-3 gap-2">
                <div class="col-span-1">
                    <span class="block text-xs font-semibold text-slate-400 uppercase tracking-wider">Kode</span>
                    <span id="lblKode" class="font-mono font-bold text-slate-700">-</span>
                </div>
                <div class="col-span-2">
                    <span class="block text-xs font-semibold text-slate-400 uppercase tracking-wider">Nama Bahan</span>
                    <span id="lblNama" class="font-bold text-slate-800 block truncate">-</span>
                </div>
            </div>

            <div class="border border-slate-200 rounded-xl p-4 space-y-2">
                <span class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Buku Logistik Rekapitulasi:</span>
                
                <div class="flex justify-between text-xs text-slate-500">
                    <span>Stok Saldo Awal:</span>
                    <span id="lblStokAwal" class="font-mono font-semibold text-slate-700">-</span>
                </div>
                <div class="flex justify-between text-xs text-slate-500">
                    <span>Pasokan Masuk (+):</span>
                    <span id="lblMasuk" class="font-mono font-semibold text-emerald-600">-</span>
                </div>
                <div class="flex justify-between text-xs text-slate-500">
                    <span>Kebutuhan Keluar (-):</span>
                    <span id="lblKeluar" class="font-mono font-semibold text-rose-600">-</span>
                </div>
                <div class="flex justify-between border-t border-slate-100 pt-1.5 text-xs font-bold text-slate-700">
                    <span>Stok Akhir Saat Ini:</span>
                    <span id="lblStokAkhir" class="font-mono text-indigo-600">-</span>
                </div>

                <div class="pt-3 mt-2 bg-emerald-50/60 p-3 rounded-lg border border-emerald-100 text-center space-y-1">
                    <span class="text-xs font-bold text-emerald-900 uppercase tracking-wide">Rasio Perputaran Stok (Turnover Rate)</span>
                    <span id="lblTurnover" class="font-mono font-extrabold text-emerald-600 text-base block">-</span>
                    <div id="boxRekomendasi" class="text-[11px] p-1.5 rounded mt-1 font-medium"></div>
                </div>
            </div>
        </div>

        <div class="flex justify-end pt-1">
            <button onclick="tutupModalStok()" class="bg-slate-800 hover:bg-slate-900 text-white font-semibold text-xs px-4 py-2 rounded-lg shadow-sm transition-all">
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
            const tglRow = row.getAttribute('data-tanggal') || "";
            const katRow = row.getAttribute('data-kategori');
            const namaRow = row.getAttribute('data-nama').toLowerCase();
            
            let cocokNama = true;
            let cocokTanggal = true;
            let cocokKategori = true;

            if (namaItemValue && !namaRow.includes(namaItemValue)) cocokNama = false;
            if (tglMulaiValue && tglRow && tglRow < tglMulaiValue) cocokTanggal = false;
            if (tglSelesaiValue && tglRow && tglRow > tglSelesaiValue) cocokTanggal = false;
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

        document.getElementById('widgetTotal').innerText = `${totalTerlihat} Material`;
        document.getElementById('widgetPokok').innerText = `${pokokCount} Item`;
        document.getElementById('widgetPembantu').innerText = `${pembantuCount} Item`;

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

    function analisisTurnoverStok(element) {
        const row = element.closest('tr');
        
        const kode = row.getAttribute('data-kode');
        const nama = row.getAttribute('data-nama');
        const stokAwal = parseFloat(row.getAttribute('data-stok-awal')) || 0;
        const masuk = parseFloat(row.getAttribute('data-masuk')) || 0;
        const keluar = parseFloat(row.getAttribute('data-keluar')) || 0;
        const stokAkhir = parseFloat(row.getAttribute('data-stok-akhir')) || 0;
        const satuan = row.getAttribute('data-satuan');

        document.getElementById('lblKode').innerText = kode;
        document.getElementById('lblNama').innerText = nama;
        document.getElementById('lblStokAwal').innerText = `${stokAwal.toFixed(2)} ${satuan}`;
        document.getElementById('lblMasuk').innerText = `${masuk.toFixed(2)} ${satuan}`;
        document.getElementById('lblKeluar').innerText = `${keluar.toFixed(2)} ${satuan}`;
        document.getElementById('lblStokAkhir').innerText = `${stokAkhir.toFixed(2)} ${satuan}`;

        const totalKetersediaan = stokAwal + masuk;
        let rasioTurnover = 0;
        if (totalKetersediaan > 0) {
            rasioTurnover = (keluar / totalKetersediaan) * 100;
        }

        document.getElementById('lblTurnover').innerText = `${rasioTurnover.toFixed(1)}% Penyerapan`;

        const boxRec = document.getElementById('boxRekomendasi');
        if (stokAkhir <= (stokAwal * 0.5) || rasioTurnover >= 50) {
            boxRec.className = "text-[11px] p-1.5 rounded mt-1 font-medium bg-rose-50 text-rose-700 border border-rose-200";
            boxRec.innerText = "⚠️ REKOMENDASI: Stok menipis kritis akibat perputaran tinggi. Segera terbitkan Purchase Order (PO) ke Supplier.";
        } else {
            boxRec.className = "text-[11px] p-1.5 rounded mt-1 font-medium bg-emerald-50 text-emerald-700 border border-emerald-200";
            boxRec.innerText = "✅ REKOMENDASI: Batas kuantitas aman (Safety Stock) terpenuhi untuk siklus produksi berjalan.";
        }

        document.getElementById('modalAnalisisStok').classList.remove('hidden');
    }

    function tutupModalStok() {
        document.getElementById('modalAnalisisStok').classList.add('hidden');
    }

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