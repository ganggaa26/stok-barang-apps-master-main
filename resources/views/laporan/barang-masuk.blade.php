@extends('layouts.admin')

@section('content')
<script src="https://cdn.tailwindcss.com"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

<style>
    @media print {
        /* Sembunyikan semua komponen navigasi & tombol */
        nav, aside, .no-print, #actionContainer, #filterContainer, button, footer {
            display: none !important;
        }
        
        /* Maksimalkan area cetak utama tanpa batas margin bawaan browser */
        #areaCetakUtama {
            position: relative;
            left: 0;
            top: 0;
            width: 100% !important;
            margin: 0 !important;
            padding: 0 !important;
            display: block !important;
        }

        /* Atur tabel agar memaksa menghabiskan lebar kertas secara penuh */
        table {
            width: 100% !important;
            table-layout: fixed; /* Membagi kolom secara adil agar tidak off-screen */
        }

        /* Pastikan background warna lencana (badge) dan card tidak hilang */
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
                <h3 id="widgetTotal" class="text-2xl font-bold text-slate-800 mt-1">
                    {{ $totalTransaksiTerfilter }} Transaksi
                </h3>
            </div>
            <div class="p-3 bg-emerald-50 text-emerald-600 rounded-lg text-xl">📥</div>
        </div>
        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Mutasi Material Pokok</p>
                <h3 id="widgetPokok" class="text-2xl font-bold text-indigo-600 mt-1">
                    {{ $mutasiMaterialPokok }} Log
                </h3>
            </div>
            <div class="p-3 bg-indigo-50 text-indigo-600 rounded-lg text-xl">🪵</div>
        </div>
        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Mutasi Bahan Pembantu</p>
                <h3 id="widgetPembantu" class="text-2xl font-bold text-blue-600 mt-1">
                    {{ $mutasiBahanPembantu }} Log
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
                        <th class="p-3.5 font-semibold">Spesifikasi Teknis</th>
                        <th class="p-3.5 font-semibold text-center">Jml Batang/Lbr</th>
                        <th class="p-3.5 font-semibold text-right">Vol / Luas Akhir</th>
                        <th class="p-3.5 font-semibold">Asal / Supplier</th>
                    </tr>
                </thead>
                <tbody id="tabelMasuk" class="divide-y divide-slate-100">
                    
                 @forelse($barangMasuk as $index => $item)
                <tr class="hover:bg-slate-50 text-slate-700 transition-colors">
                <td class="p-3.5 text-center font-medium text-slate-400">{{ $index + 1 }}</td>
                <td class="p-3.5 font-mono text-xs whitespace-nowrap">
                    {{ \Carbon\Carbon::parse($item->tanggal)->format('d-m-Y') }}
                </td>
                <td class="p-3.5">
                <span class="font-bold text-slate-800 block">
                    {{ $item->material->nama_material ?? $item->materialPembantu->nama_material ?? $item->nama_barang }}
                </span>
                <span class="text-xs text-slate-400 block mt-0.5">
                    {{ $item->material->jenis_material ?? $item->materialPembantu->jenis_material ?? '-' }}
                </span>
            </td>
            <td class="p-3.5">
            <span class="px-2.5 py-0.5 text-xs font-semibold rounded-full bg-emerald-50 text-emerald-600 whitespace-nowrap">
                {{ $item->jenis_transaksi }}
            </span>
            </td>
            <td class="p-3.5 font-medium text-indigo-600">
                {{ $item->nama_proyek ?? 'Restock Umum' }}
            </td>
            <td class="p-3.5 text-xs text-slate-500 max-w-xs whitespace-normal">
                {{ $item->spesifikasi ?? '-' }}
            </td>
            <td class="p-3.5 text-center font-mono font-bold text-slate-800">
              @php
            // 1. Ambil teks nama barang yang dicetak di baris tabel secara aman
            $namaBarangTeks = strtolower($item->material->nama_material ?? $item->materialPembantu->nama_material ?? $item->nama_barang ?? '');
            $jenisBarangTeks = strtolower($item->material->jenis_material ?? $item->materialPembantu->jenis_material ?? '');

            // 2. Cek secara mandiri apakah teks mengandung kata kunci lembaran komprehensif
            $isLembaran = \Illuminate\Support\Str::contains($namaBarangTeks, 'veneer') || 
                          \Illuminate\Support\Str::contains($namaBarangTeks, 'plywood') || 
                          \Illuminate\Support\Str::contains($namaBarangTeks, 'lembar') || 
                          \Illuminate\Support\Str::contains($jenisBarangTeks, 'lembar');
            
            // 3. Jika benar, langsung paksa jadi M². Jika tidak, gunakan bawaan database/M³
            $satuanTeks = $isLembaran ? 'M²' : ($item->satuan ?? 'M³');
        @endphp

        <td class="p-3.5 text-right font-mono font-semibold text-slate-700">
            {{ number_format($item->kuantitas, 4) }}
        </td>
        
        <td class="p-3.5 text-center text-xs font-bold {{ $isLembaran ? 'text-amber-600 bg-amber-50 rounded p-1' : 'text-slate-400' }}">
            {!! $satuanTeks !!}
        </td>
            </td>

            <td class="p-3.5 text-right font-mono font-semibold text-slate-700">
            {{ number_format($item->kuantitas, 4) }}
            </td>
    @php
            // 1. Ambil nama kategori langsung dari master data categories di database
            $namaKategoriMaster = strtolower($item->material->kategori->nama_kategori ?? '');
            
            // 2. Ambil juga nama satuannya di master data (siapa tahu namanya 'Lembar' atau 'Lembaran')
            $satuanKategoriMaster = strtolower($item->material->kategori->satuan ?? '');

            // 3. Cek secara otomatis: Apakah master datanya mengandung kata 'lembar'?
            $isLembaran = \Illuminate\Support\Str::contains($namaKategoriMaster, 'lembar') || 
                          \Illuminate\Support\Str::contains($satuanKategoriMaster, 'lembar');
            
            // 4. Jika di master di-set Lembar, otomatis M². Jika tidak, gunakan bawaan transaksi.
            $satuanTeks = $isLembaran ? 'M²' : ($item->satuan ?? 'M³');
        @endphp

        <td class="p-3.5 text-right font-mono font-semibold text-slate-700">
            {{ number_format($item->kuantitas, 4) }}
        </td>
        
        <td class="p-3.5 text-center text-xs font-bold {{ $isLembaran ? 'text-amber-600 bg-amber-50 rounded p-1' : 'text-slate-400' }}">
            {!! $satuanTeks !!}
        </td>
        
        <td class="p-3.5 text-center text-xs font-bold {{ $isLembaran ? 'text-amber-600 bg-amber-50 rounded p-1' : 'text-slate-400' }}">
            {!! $satuanTeks !!}
            </td>
        </tr>
    @empty
        <tr>
            <td colspan="9" class="p-8 text-center text-slate-400 italic">
                Belum ada riwayat transaksi barang masuk yang tercatat.
            </td>
        </tr>
    @endforelse
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