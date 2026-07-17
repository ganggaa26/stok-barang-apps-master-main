@extends('layouts.admin')

@section('content')
<script src="https://cdn.tailwindcss.com"></script>

<div class="max-w-7xl mx-auto space-y-6 p-4 md:p-6 font-sans antialiased text-slate-800">

    {{-- BREADCRUMB & HEADER --}}
    <div class="space-y-1">
       <div class="text-xs text-slate-400 font-medium flex items-center gap-1.5">
           <a href="{{ route('dashboard') }}" class="text-indigo-600 hover:text-slate-400 hover:underline transition-colors">Manajemen Material</a>
            <span class="text-slate-300">/</span>
            <span class="text-slate-500">Material Pembantu</span>
        </div>
        <h1 class="text-xl font-bold text-slate-900 tracking-tight">Modul Inventaris: Bahan Pembantu & Consumables</h1>
        <p class="text-sm text-slate-500">Kelola stok perekat, paku, cairan finishing, dan amplas.</p>
    </div>

    {{-- ALERT SUCCESS --}}
    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm rounded-xl p-4 shadow-sm">
            {{ session('success') }}
        </div>
    @endif

    {{-- FORM UTAMA --}}
    <form action="{{ route('material.pembantu.store') }}" method="POST" id="formBahanPembantu" class="space-y-6">
        @csrf
        <input type="hidden" name="_method" id="methodOverride" value="POST">

        {{-- BLOCK 1: KATEGORI & ITEM BARANG --}}
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5 space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="space-y-1.5">
                    <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Sub-Kategori Bahan Pembantu</label>
                    <select id="subKategori" name="category_id" onchange="updateItemDropdown()" class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-500 transition font-medium">
                        <option value="">-- Pilih Sub-Kategori --</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->nama_Kategori }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="space-y-1.5">
                    <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Item Material</label>
                    <select id="itemBarang" name="material_pembantu_id" onchange="renderSpesifikasiForm()" class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-500 transition font-medium">
                        <option value="">-- Pilih Item --</option>
                        @foreach($materials as $item)
                            <option value="{{ $item->id }}" data-category="{{ $item->category_id ?? $item->kategori_pembantu_id }}">{{ $item->nama_material ?? $item->nama_barang }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- AREA DINAMIS SUB-FORM ATRIBUT TEKNIS --}}
            <div id="wrapperAtributTeknis" class="bg-slate-50/50 rounded-xl p-4 border border-dashed border-slate-200 min-h-[60px] flex items-center justify-center">
                <p class="text-xs font-medium text-slate-400 italic">Form spesifikasi teknis otomatis muncul setelah item dipilih</p>
            </div>
        </div>

        {{-- BLOCK 2: STATUS LOGISTIK & PELACAKAN PROYEK --}}
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5 space-y-4">
            <div class="flex items-center space-x-2 pb-2 border-b border-slate-100">
                <span class="text-sm">🚚</span>
                <h2 class="text-xs font-bold text-slate-500 uppercase tracking-wider" id="textTombol">Status Logistik & Pelacakan Proyek</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                <div class="space-y-1.5">
                    <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Jenis Transaksi</label>
                    <select name="jenis_transaksi" id="jenisTransaksi" onchange="aturFormLogistik()" class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-500 transition font-medium">
                        <option value="Stok Awal">Stok Awal</option>
                        <option value="Barang Masuk">Barang Masuk</option>
                        <option value="Barang Keluar">Barang Keluar</option>
                    </select>
                </div>

                <div class="space-y-1.5">
                    <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Tanggal</label>
                    <input type="date" name="tanggal" id="tglTransaksi" value="{{ date('Y-m-d') }}" class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-500 transition font-medium text-slate-600">
                </div>

                {{-- KOLOM DINAMIS ASAL / PROYEK --}}
                <div class="md:col-span-2" id="boxLogistik">
                    <div id="formBarangMasuk" class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Stok awal / Supplier</label>
                        <input type="text" id="asalBarang" placeholder="Contoh: Toko Bangunan Subur atau Sisa Proyek A" class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-500 transition">
                    </div>

                    <div id="formBarangKeluar" class="hidden grid grid-cols-2 gap-2">
                    <div class="space-y-1.5 col-span-1">
                        <label class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Nama Proyek / Pekerjaan</label>
                        <input type="text" id="namaProyek" placeholder="Contoh: Finishing Batch Pagi" class="w-full bg-slate-50 border border-slate-200 rounded-lg px-2 py-2 text-sm focus:outline-none focus:border-indigo-500 transition">
                    </div>
                    <div class="space-y-1.5 col-span-1">
                        <label class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Produk <span class="text-slate-400 normal-case font-normal">(Opsional)</span></label>
                        <input type="text" id="namaProduk" placeholder="Kosongkan jika tidak spesifik ke 1 produk" class="w-full bg-slate-50 border border-slate-200 rounded-lg px-2 py-2 text-sm focus:outline-none focus:border-indigo-500 transition">
                    </div>
                </div>
                </div>
            </div>

            <input type="hidden" name="spesifikasi" id="hiddenSpesifikasi">
            <input type="hidden" name="merk" id="hiddenMerk">
            <input type="hidden" name="kuantitas" id="hiddenKuantitas">
            <input type="hidden" name="satuan_input" id="hiddenSatuanInput">
            <input type="hidden" name="asal_atau_proyek" id="inputAsalAtauProyek">

            <div class="flex justify-end gap-2 pt-2">
                <div class="flex justify-end items-center gap-2 pt-2">
                <button type="button" id="btnBatalEdit" onclick="location.reload()" class="hidden bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-bold px-4 py-2.5 rounded-lg transition">Batal Perubahan</button>
                <button type="submit" onclick="return validasiDanKemasData()" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs uppercase tracking-wider px-6 py-2.5 rounded-lg shadow transition flex items-center gap-2">
                    <span id="labelSubmitPembantu">💾 Amankan Data Stok Pembantu</span>
                </button>
            </div>
        </div>
    </form>

    {{-- AREA TABLE RIWAYAT (MENGGUNAKAN $mutasiks SESUAI CONTROLLER) --}}
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3">
        <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider flex items-center space-x-2">
            <span>📊</span> <span>Jurnal Riwayat Transaksi Material Pembantu</span>
        </h3>

        <div class="relative">
            <button type="button" onclick="toggleFilterPanel()" id="btnBukaFilter" class="h-9 px-4 flex items-center gap-2 rounded-lg bg-indigo-50 border border-indigo-200 text-indigo-700 hover:bg-indigo-100 text-xs font-semibold transition-colors">
                <span>📅</span>
                <span id="labelFilterAktif">Minggu Ini</span>
                <span>▾</span>
            </button>

            <div id="panelFilter" class="hidden absolute right-0 mt-2 w-[320px] bg-white border-2 border-slate-300 rounded-xl shadow-xl z-30 p-4 space-y-4">
                <div>
                    <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Rentang Waktu</p>
                    <div class="space-y-1.5">
                        <label class="flex items-center gap-2 text-sm text-slate-700 cursor-pointer">
                            <input type="radio" name="modeRentang" value="hari_ini" class="text-indigo-600 focus:ring-indigo-500">
                            Hari Ini
                        </label>
                        <label class="flex items-center gap-2 text-sm text-slate-700 cursor-pointer">
                            <input type="radio" name="modeRentang" value="7_hari" class="text-indigo-600 focus:ring-indigo-500">
                            7 Hari Terakhir
                        </label>
                        <label class="flex items-center gap-2 text-sm text-slate-700 cursor-pointer">
                            <input type="radio" name="modeRentang" value="minggu_ini" checked class="text-indigo-600 focus:ring-indigo-500">
                            Minggu Ini
                        </label>
                        <label class="flex items-center gap-2 text-sm text-slate-700 cursor-pointer">
                            <input type="radio" name="modeRentang" value="bulan" class="text-indigo-600 focus:ring-indigo-500">
                            Pilih Bulan
                        </label>
                        <div id="wrapperPilihBulan" class="hidden pl-6">
                            <input type="month" id="inputPilihBulan" class="w-full border border-slate-300 rounded-lg px-2 py-1.5 text-sm">
                        </div>
                        <label class="flex items-center gap-2 text-sm text-slate-700 cursor-pointer">
                            <input type="radio" name="modeRentang" value="custom" class="text-indigo-600 focus:ring-indigo-500">
                            Pilih Tanggal
                        </label>
                        <div id="wrapperPilihTanggal" class="hidden pl-6 space-y-1.5">
                            <input type="date" id="inputTglDari" class="w-full border border-slate-300 rounded-lg px-2 py-1.5 text-sm">
                            <input type="date" id="inputTglSampai" class="w-full border border-slate-300 rounded-lg px-2 py-1.5 text-sm">
                        </div>
                        <label class="flex items-center gap-2 text-sm text-slate-700 cursor-pointer">
                            <input type="radio" name="modeRentang" value="semua" class="text-indigo-600 focus:ring-indigo-500">
                            Tampilkan Semua
                        </label>
                    </div>
                </div>

                <div>
                    <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Jenis Transaksi</p>
                    <select id="inputJenisTransaksiFilter" class="w-full border border-slate-300 rounded-lg px-2 py-2 text-sm">
                        <option value="semua">Semua Transaksi</option>
                        <option value="Stok Awal">Stok Awal</option>
                        <option value="Barang Masuk">Barang Masuk</option>
                        <option value="Barang Keluar">Barang Keluar</option>
                    </select>
                </div>

                <div class="flex gap-2 pt-2 border-t border-slate-100">
                    <button type="button" onclick="resetFilterJurnal()" class="w-1/2 h-9 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-semibold transition-colors">Reset</button>
                    <button type="button" onclick="terapkanFilterJurnal()" class="w-1/2 h-9 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold transition-colors">Terapkan</button>
                </div>
            </div>
        </div>
    </div>

    <p id="infoJumlahBaris" class="text-xs text-slate-400 font-medium -mt-1">-</p>

    <div class="overflow-x-auto rounded-lg border-2 border-slate-400 bg-white">
    <table class="w-full text-sm text-left border-collapse border-2 border-slate-400">
        <thead>
            <tr class="bg-slate-100 text-slate-700 text-[11px] uppercase tracking-wider border-b-2 border-slate-400">
                <th class="p-3 font-bold text-center w-12 border-r-2 border-slate-400">No.</th>
                <th class="p-3 font-bold border-r-2 border-slate-400">Tanggal</th>
                <th class="p-3 font-bold border-r-2 border-slate-400">Item Material</th>
                <th class="p-3 font-bold text-center border-r-2 border-slate-400">Status</th>
                <th class="p-3 font-bold border-r-2 border-slate-400">Detail Spesifikasi</th>
                <th class="p-3 font-bold text-right border-r-2 border-slate-400">Volume / Qty</th>
                <th class="p-3 font-bold border-r-2 border-slate-400">Proyek / Asal</th>
                <th class="p-3 font-bold text-center" id="kolomAksiTabel">Aksi</th>
            </tr>
        </thead>
        <tbody id="badanTabelLog" class="divide-y-2 divide-slate-400 bg-white">
        @forelse($mutasiks as $mutasi)
            @php
                $badgeWarna = match($mutasi->jenis_transaksi) {
                    'Barang Masuk' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                    'Barang Keluar' => 'bg-rose-50 text-rose-700 border-rose-200',
                    default => 'bg-indigo-50 text-indigo-700 border-indigo-200'
                };
            @endphp
            <tr class="hover:bg-slate-50/80 text-slate-700 transition baris-data"
                data-tanggal="{{ \Carbon\Carbon::parse($mutasi->tanggal)->format('Y-m-d') }}"
                data-kategori="{{ $mutasi->jenis_transaksi }}"
                data-item="{{ $mutasi->masterMaterialPembantu->nama_material ?? '' }}">

                <td class="p-3 text-xs text-center whitespace-nowrap border-r-2 border-slate-400">{{ $loop->iteration }}</td>
                <td class="p-3 text-xs whitespace-nowrap border-r-2 border-slate-400">{{ date('d/m/Y', strtotime($mutasi->tanggal)) }}</td>
                <td class="p-3 font-medium text-slate-900 border-r-2 border-slate-400">{{ $mutasi->masterMaterialPembantu->nama_material ?? '-' }}</td>
                <td class="p-3 text-center whitespace-nowrap border-r-2 border-slate-400">
                    <span class="px-2.5 py-0.5 text-[10px] font-bold rounded-full border {{ $badgeWarna }}">
                        {{ $mutasi->jenis_transaksi }}
                    </span>
                </td>
                <td class="p-3 text-xs text-slate-500 max-w-xs border-r-2 border-slate-400">
                    <div class="space-y-1">
                        @php
                            $adaMerk = $mutasi->merk && $mutasi->merk !== '-';
                            $adaSpek = $mutasi->spesifikasi && $mutasi->spesifikasi !== '-';
                        @endphp

                        @if($adaMerk)
                            <div class="bg-slate-50 border border-slate-100 rounded px-2 py-1 text-slate-600">
                                Merk: {{ $mutasi->merk }}
                            </div>
                        @endif

                        @if($adaSpek)
                            @foreach(explode(',', $mutasi->spesifikasi) as $bagian)
                                <div class="bg-slate-50 border border-slate-100 rounded px-2 py-1 text-slate-600">
                                    {{ trim($bagian) }}
                                </div>
                            @endforeach
                        @endif

                        @if(!$adaMerk && !$adaSpek)
                            <span>-</span>
                        @endif
                    </div>
                </td>
                <td class="p-3 text-right font-mono font-bold text-slate-900 whitespace-nowrap border-r-2 border-slate-400">
                    {{ number_format($mutasi->kuantitas, 0, ',', '.') }} {{ $mutasi->satuan_input }}
                </td>
                <td class="p-3 text-xs text-slate-600 max-w-xs truncate border-r-2 border-slate-400">
                    @if($mutasi->jenis_transaksi === 'Barang Keluar')
                        <button type="button" onclick="hitungRasioKonsumsi(this)"
                                data-proyek="{{ $mutasi->asal_atau_proyek }}"
                                data-material="{{ $mutasi->masterMaterialPembantu->nama_material ?? '-' }}"
                                data-qty="{{ $mutasi->kuantitas }}"
                                data-volume="{{ $mutasi->kuantitas }} {{ $mutasi->satuan_input }}"
                                class="text-left hover:text-indigo-600 hover:underline font-medium text-indigo-500">
                            {{ $mutasi->asal_atau_proyek ?? '-' }}
                        </button>
                    @else
                        <span>{{ $mutasi->asal_atau_proyek ?? '-' }}</span>
                    @endif
                </td>
                <td class="p-3 text-center whitespace-nowrap">
                    <div class="flex items-center justify-center gap-1">
                        <button type="button"
                                onclick="editMutasiPembantu({
                                    id: {{ $mutasi->id }},
                                    material_pembantu_id: {{ $mutasi->master_material_pembantu_id ?? $mutasi->material_pembantu_id }},
                                    jenis_material: '{{ $mutasi->masterMaterialPembantu->jenis_material ?? '' }}',
                                    jenis_transaksi: '{{ $mutasi->jenis_transaksi }}',
                                    tanggal: '{{ $mutasi->tanggal }}',
                                    kuantitas: {{ $mutasi->kuantitas }},
                                    spesifikasi: '{{ $mutasi->spesifikasi ?? '' }}',
                                    merk: '{{ $mutasi->merk ?? '' }}',
                                    satuan_input: '{{ $mutasi->satuan_input ?? '' }}',
                                    asal_atau_proyek: '{{ $mutasi->asal_atau_proyek ?? '' }}'
                                })"
                                class="text-amber-600 hover:text-amber-700 font-medium text-xs px-2 py-1">✏️</button>
                            <button type="button"
                                onclick="bukaModalHapus({{ $mutasi->id }}, '{{ $mutasi->masterMaterialPembantu->nama_material ?? '' }}')"
                                class="text-rose-600 hover:text-rose-700 font-medium text-xs px-2 py-1">🗑️</button>
                    </div>
                </td>
            </tr>
       @empty
        <tr>
            <td colspan="8" class="p-8 text-center text-slate-400 font-medium bg-slate-50 border border-slate-400">
                Belum ada riwayat transaksi bahan pembantu.
            </td>
        </tr>
    @endforelse

        <tr id="barisKosongFilter" class="hidden">
            <td colspan="8" class="p-8 text-center text-slate-400 font-medium bg-slate-50 border border-slate-400">
                Tidak ada transaksi pada rentang/filter yang dipilih.
            </td>
        </tr>
    </tbody>
    </table>
</div>
    </div>
</div>

{{-- MODAL TRACKING RASIO KONSUMSI --}}
<div id="modalRasioProduksi" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm hidden flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl shadow-xl border border-slate-100 max-w-lg w-full overflow-hidden">
        <div class="bg-gradient-to-r from-slate-900 to-indigo-900 px-6 py-4 flex items-center justify-between">
            <h3 class="text-sm font-bold text-white tracking-tight">Pelacakan Distribusi Bahan Pembantu</h3>
            <button onclick="tutupModalRasio()" class="text-white bg-white/10 hover:bg-white/20 p-1 rounded-lg text-xs px-2">✕</button>
        </div>
        <div class="p-6 space-y-4">
            <div class="grid grid-cols-2 gap-4 text-xs">
                <div class="col-span-2"><span class="block text-slate-400">Target Alokasi Proyek:</span> <strong id="lblTargetUnit" class="text-slate-800 text-sm">-</strong></div>
                <div><span class="block text-slate-400">Item Material:</span> <strong id="lblMaterial" class="text-slate-800">-</strong></div>
                <div><span class="block text-slate-400">Jumlah Kuantitas:</span> <strong id="lblQtyFisik" class="text-slate-800">-</strong></div>
                <div class="col-span-2"><span class="block text-slate-400">Volume Pengeluaran:</span> <strong id="lblVolumeTotal" class="text-slate-800">-</strong></div>
            </div>
            <div class="bg-indigo-50 p-3 rounded-xl text-center"><span id="lblHasilRasio" class="text-xs font-bold text-indigo-900 block">Informasi Berhasil Dimuat</span></div>
        </div>
    </div>
</div>

{{-- MODAL KONFIRMASI HAPUS --}}
<div id="modalHapusPembantu" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm hidden flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl shadow-xl max-w-md w-full p-6 space-y-4">
        <div class="flex items-center space-x-2 text-rose-600 font-bold"><span class="text-lg">⚠️</span> <h3>Konfirmasi Hapus Data Log</h3></div>
        <p class="text-xs text-slate-500">Apakah Anda yakin ingin menghapus catatan log mutasi untuk material <strong id="namaMaterialHapus" class="text-slate-800"></strong>? Tindakan ini bersifat permanen.</p>
        <div class="flex justify-end space-x-2">
            <button type="button" onclick="tutupModalHapus()" class="bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-bold px-4 py-2 rounded-lg transition">Batal</button>
            <form action="" method="POST" id="formHapusPembantuModal" class="inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold px-4 py-2 rounded-lg transition shadow-sm shadow-rose-200">Ya, Hapus Data</button>
            </form>
        </div>
    </div>
</div>

<script>
    // 1. DATA MASTER
    const masterBahanGroup = @json($materials);
    const masterKategori = @json($categories);

    // ==================== FILTER JURNAL RENTANG WAKTU & JENIS TRANSAKSI (sama seperti Pokok) ====================
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('input[name="modeRentang"]').forEach(radio => {
            radio.addEventListener('change', function() {
                document.getElementById('wrapperPilihBulan').classList.toggle('hidden', this.value !== 'bulan');
                document.getElementById('wrapperPilihTanggal').classList.toggle('hidden', this.value !== 'custom');
            });
        });
        terapkanFilterJurnal();
    });

    document.addEventListener('click', function(e) {
        const panel = document.getElementById('panelFilter');
        const btn = document.getElementById('btnBukaFilter');
        if (panel && !panel.contains(e.target) && btn && !btn.contains(e.target)) {
            panel.classList.add('hidden');
        }
    });

    function toggleFilterPanel() {
        document.getElementById('panelFilter').classList.toggle('hidden');
    }

    function keFormatISO(d) {
        return d.toISOString().split('T')[0];
    }

    function hitungRentangTanggal(mode) {
        const sekarang = new Date();
        sekarang.setHours(0, 0, 0, 0);

        if (mode === 'hari_ini') {
            const iso = keFormatISO(sekarang);
            return { awal: iso, akhir: iso, label: 'Hari Ini' };
        }
        if (mode === '7_hari') {
            const awal = new Date(sekarang);
            awal.setDate(sekarang.getDate() - 6);
            return { awal: keFormatISO(awal), akhir: keFormatISO(sekarang), label: '7 Hari Terakhir' };
        }
        if (mode === 'minggu_ini') {
            const hariKe = sekarang.getDay();
            const selisihKeSenin = (hariKe === 0) ? -6 : (1 - hariKe);
            const awal = new Date(sekarang);
            awal.setDate(sekarang.getDate() + selisihKeSenin);
            const akhir = new Date(awal);
            akhir.setDate(awal.getDate() + 6);
            return { awal: keFormatISO(awal), akhir: keFormatISO(akhir), label: 'Minggu Ini' };
        }
        if (mode === 'bulan') {
            const nilai = document.getElementById('inputPilihBulan').value;
            if (!nilai) return null;
            const [tahun, bulan] = nilai.split('-').map(Number);
            const awal = new Date(tahun, bulan - 1, 1);
            const akhir = new Date(tahun, bulan, 0);
            const bulanNama = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
            return { awal: keFormatISO(awal), akhir: keFormatISO(akhir), label: `${bulanNama[bulan - 1]} ${tahun}` };
        }
        if (mode === 'custom') {
            const dari = document.getElementById('inputTglDari').value;
            const sampai = document.getElementById('inputTglSampai').value;
            if (!dari || !sampai) return null;
            return { awal: dari, akhir: sampai, label: `${dari} — ${sampai}` };
        }
        if (mode === 'semua') {
            return { awal: null, akhir: null, label: 'Semua Riwayat' };
        }
        return null;
    }

    function terapkanFilterJurnal() {
        const modeTerpilih = document.querySelector('input[name="modeRentang"]:checked').value;
        const jenisTerpilih = document.getElementById('inputJenisTransaksiFilter').value;

        const rentang = hitungRentangTanggal(modeTerpilih);
        if (!rentang) {
            alert('Lengkapi tanggal/bulan yang mau difilter dulu.');
            return;
        }

        const baris = document.querySelectorAll('#badanTabelLog .baris-data');
        const barisKosong = document.getElementById('barisKosongFilter');
        const infoJumlah = document.getElementById('infoJumlahBaris');

        let jumlahTampil = 0;

        baris.forEach(row => {
            const tglRow = row.getAttribute('data-tanggal');
            const jenisRow = row.getAttribute('data-kategori');

            let cocokTanggal = true;
            if (rentang.awal && rentang.akhir) {
                cocokTanggal = tglRow >= rentang.awal && tglRow <= rentang.akhir;
            }

            const cocokJenis = (jenisTerpilih === 'semua') || (jenisRow === jenisTerpilih);

            if (cocokTanggal && cocokJenis) {
                row.classList.remove('hidden');
                jumlahTampil++;
            } else {
                row.classList.add('hidden');
            }
        });

        document.getElementById('labelFilterAktif').innerText = rentang.label;

        const labelJenis = jenisTerpilih === 'semua' ? '' : ` — ${jenisTerpilih}`;
        infoJumlah.innerText = `Menampilkan ${jumlahTampil} transaksi (${rentang.label}${labelJenis}).`;

        if (barisKosong) {
            barisKosong.classList.toggle('hidden', jumlahTampil !== 0 || baris.length === 0);
        }

        document.getElementById('panelFilter').classList.add('hidden');
    }

    function resetFilterJurnal() {
        document.querySelector('input[name="modeRentang"][value="minggu_ini"]').checked = true;
        document.getElementById('inputPilihBulan').value = '';
        document.getElementById('inputTglDari').value = '';
        document.getElementById('inputTglSampai').value = '';
        document.getElementById('inputJenisTransaksiFilter').value = 'semua';
        document.getElementById('wrapperPilihBulan').classList.add('hidden');
        document.getElementById('wrapperPilihTanggal').classList.add('hidden');
        terapkanFilterJurnal();
    }
    // ==================== AKHIR FILTER JURNAL ====================

    // 2. FUNGSI-FUNGSI FORM (TIDAK BERUBAH)
    window.resetSpesifikasiWrapper = function() {
        const wrapper = document.getElementById('wrapperAtributTeknis');
        if (wrapper) {
            wrapper.classList.add('items-center', 'justify-center');
            wrapper.innerHTML = '<p class="text-xs font-medium text-slate-400 italic">Form spesifikasi teknis otomatis muncul setelah item dipilih</p>';
        }
    };

    window.updateItemDropdown = function() {
        const kategoriSelect = document.getElementById('subKategori');
        const itemSelect = document.getElementById('itemBarang');
        if (!kategoriSelect || !itemSelect) return;

        itemSelect.innerHTML = '<option value="">-- Pilih Item --</option>';
        if (!kategoriSelect.value) {
            itemSelect.disabled = true;
            window.resetSpesifikasiWrapper();
            return;
        }

       const kategoriTerpilih = masterKategori.find(k => k.id.toString() === kategoriSelect.value.toString());
        if (!kategoriTerpilih) {
            itemSelect.disabled = true;
            window.resetSpesifikasiWrapper();
            return;
        }

        const filtered = masterBahanGroup.filter(item => item.category_id !== null && item.category_id.toString() === kategoriTerpilih.id.toString());

        filtered.forEach(item => {
            const opt = document.createElement('option');
            opt.value = item.id;
            opt.textContent = item.nama_material || item.nama_barang || 'Tanpa Nama';
            itemSelect.appendChild(opt);
        });

        itemSelect.disabled = false;
        window.resetSpesifikasiWrapper();
    };

    window.renderSpesifikasiForm = function() {
        const itemSelect = document.getElementById('itemBarang');
        const wrapper = document.getElementById('wrapperAtributTeknis');
        if (!itemSelect || !wrapper) return;

        const itemId = itemSelect.value;
        if (!itemId) {
            window.resetSpesifikasiWrapper();
            return;
        }

        const item = masterBahanGroup.find(m => m.id.toString() === itemId.toString());
        if (!item) {
            window.resetSpesifikasiWrapper();
            return;
        }

        const tipe = item.tipe_kalkulasi;
        const satuan = item.satuan || '-';
        let html = '';

        if (tipe === 'volume_cairan') {
            html = `
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 w-full">
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Merk</label>
                        <input type="text" id="specMerk" placeholder="Contoh: Propan, Impra" class="w-full bg-white border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-500 transition">
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Kuantitas</label>
                        <input type="number" step="0.01" min="0" id="specKuantitas" placeholder="0" class="w-full bg-white border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-500 transition">
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Satuan</label>
                        <input type="text" value="${satuan}" disabled class="w-full bg-slate-100 border border-slate-200 rounded-lg px-3 py-2 text-sm text-slate-500 cursor-not-allowed">
                    </div>
                </div>`;
        } else if (tipe === 'CUSTOM_RUMUS') {
    html = `
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4 w-full">
            <div class="space-y-1.5">
                <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Merk</label>
                <input type="text" id="specMerk" placeholder="Contoh: Kanon, WBM" class="w-full bg-white border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-500 transition">
            </div>
            <div class="space-y-1.5">
                <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Ukuran Paku</label>
                <input type="text" id="specUkuran" placeholder="Contoh: 4 cm" class="w-full bg-white border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-500 transition">
            </div>
            <div class="space-y-1.5">
                <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Panjang (mm)</label>
                <input type="number" step="0.1" min="0" id="specPanjang" placeholder="0" class="w-full bg-white border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-500 transition">
            </div>
            <div class="space-y-1.5">
                <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Kuantitas</label>
                <input type="number" step="1" min="0" id="specKuantitas" placeholder="0" class="w-full bg-white border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-500 transition">
            </div>
            <div class="space-y-1.5">
                <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Satuan</label>
                <input type="text" value="${satuan}" disabled class="w-full bg-slate-100 border border-slate-200 rounded-lg px-3 py-2 text-sm text-slate-500 cursor-not-allowed">
            </div>
        </div>`;
        } else {
            html = `
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 w-full">
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Kuantitas</label>
                        <input type="number" step="0.01" min="0" id="specKuantitas" placeholder="0" class="w-full bg-white border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-500 transition">
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Satuan</label>
                        <input type="text" value="${satuan}" disabled class="w-full bg-slate-100 border border-slate-200 rounded-lg px-3 py-2 text-sm text-slate-500 cursor-not-allowed">
                    </div>
                </div>`;
        }

        wrapper.classList.remove('items-center', 'justify-center');
        wrapper.innerHTML = html;
    };

    function parseSpesifikasiCustom(spesifikasi) {
        const result = { ukuran: '', panjang: '' };
        if (!spesifikasi) return result;
        const ukuranMatch = spesifikasi.match(/Ukuran ([^,]+)/);
        const panjangMatch = spesifikasi.match(/Panjang ([\d.]+)mm/);
        if (ukuranMatch) result.ukuran = ukuranMatch[1].trim();
        if (panjangMatch) result.panjang = panjangMatch[1].trim();
        return result;
    }

    window.validasiDanKemasData = function() {
        const itemSelect = document.getElementById('itemBarang');
        const itemId = itemSelect ? itemSelect.value : '';

        if (!itemId) {
            alert('Pilih Item Material terlebih dahulu.');
            return false;
        }

        const item = masterBahanGroup.find(m => m.id.toString() === itemId.toString());
        if (!item) {
            alert('Item material tidak valid.');
            return false;
        }

        const tipe = item.tipe_kalkulasi;
        const kuantitasInput = document.getElementById('specKuantitas');
        const kuantitas = kuantitasInput ? parseFloat(kuantitasInput.value) : NaN;

        if (!kuantitas || kuantitas <= 0) {
            alert('Kuantitas harus diisi dan lebih dari 0.');
            return false;
        }

        let merk = '';
        let spesifikasi = '-';

        if (tipe === 'volume_cairan') {
            merk = document.getElementById('specMerk')?.value || '';
        } else if (tipe === 'CUSTOM_RUMUS') {
            merk = document.getElementById('specMerk')?.value || '';
            const ukuran = document.getElementById('specUkuran')?.value || '';
            const panjang = document.getElementById('specPanjang')?.value || '';
            const bagian = [];
            if (ukuran) bagian.push(`Ukuran ${ukuran}`);
            if (panjang) bagian.push(`Panjang ${panjang}mm`);
            spesifikasi = bagian.length ? bagian.join(', ') : '-';
        }
        document.getElementById('hiddenMerk').value = merk;
        document.getElementById('hiddenKuantitas').value = kuantitas;
        document.getElementById('hiddenSatuanInput').value = item.satuan || '';
        document.getElementById('hiddenSpesifikasi').value = spesifikasi;

        const jenis = document.getElementById('jenisTransaksi')?.value;
        let asalProyek = '';
        if (jenis === 'Barang Keluar') {
            const proyek = document.getElementById('namaProyek')?.value || '';
            const produk = document.getElementById('namaProduk')?.value || '';
            asalProyek = [proyek, produk].filter(Boolean).join(' - ');
        } else {
            asalProyek = document.getElementById('asalBarang')?.value || '';
        }
        document.getElementById('inputAsalAtauProyek').value = asalProyek;

        return true;
    };

    window.aturFormLogistik = function() {
        const jenis = document.getElementById('jenisTransaksi')?.value;
        const formMasuk = document.getElementById('formBarangMasuk');
        const formKeluar = document.getElementById('formBarangKeluar');
        formMasuk?.classList.toggle('hidden', jenis !== 'Barang Masuk' && jenis !== 'Stok Awal');
        formKeluar?.classList.toggle('hidden', jenis === 'Barang Masuk' || jenis === 'Stok Awal');
    };

    window.bukaModalHapus = function(id, namaMaterial) {
        document.getElementById('namaMaterialHapus').textContent = namaMaterial;
        document.getElementById('formHapusPembantuModal').action = `/material/pembantu/${id}`;
        document.getElementById('modalHapusPembantu').classList.remove('hidden');
    };

    window.tutupModalHapus = function() {
        document.getElementById('modalHapusPembantu').classList.add('hidden');
    };

    // 3. FUNGSI EDIT (tidak lagi cek "di luar minggu yang ditampilkan" karena semua data sudah tampil)
   window.editMutasiPembantu = function(data) {
    const form = document.getElementById('formBahanPembantu');
    form.action = `/material/pembantu/update/${data.id}`;
    document.getElementById('methodOverride').value = 'PUT';

    document.getElementById('subKategori').disabled = false;
    document.getElementById('itemBarang').disabled = false;

    // FIX: cocokin lewat ID (via masterBahanGroup), bukan cocokin teks jenis_material yang kosong
    const itemAsli = masterBahanGroup.find(m => m.id.toString() === data.material_pembantu_id.toString());
    const kategoriCocok = itemAsli ? masterKategori.find(k => k.id.toString() === (itemAsli.category_id ?? '').toString()) : null;
    document.getElementById('subKategori').value = kategoriCocok ? kategoriCocok.id : '';

    window.updateItemDropdown();

        setTimeout(() => {
            document.getElementById('itemBarang').value = data.material_pembantu_id;
            window.renderSpesifikasiForm();

            setTimeout(() => {
                const item = masterBahanGroup.find(m => m.id.toString() === data.material_pembantu_id.toString());
                const tipe = item ? item.tipe_kalkulasi : null;

                if (tipe === 'volume_cairan') {
                    if (document.getElementById('specMerk')) document.getElementById('specMerk').value = data.merk || '';
                    if (document.getElementById('specKuantitas')) document.getElementById('specKuantitas').value = data.kuantitas || '';
                } else if (tipe === 'CUSTOM_RUMUS') {
                    if (document.getElementById('specMerk')) document.getElementById('specMerk').value = data.merk || '';
                    const parsed = parseSpesifikasiCustom(data.spesifikasi);
                    if (document.getElementById('specUkuran')) document.getElementById('specUkuran').value = parsed.ukuran;
                    if (document.getElementById('specPanjang')) document.getElementById('specPanjang').value = parsed.panjang;
                    if (document.getElementById('specKuantitas')) document.getElementById('specKuantitas').value = data.kuantitas || '';

                } else {
                    if (document.getElementById('specKuantitas')) document.getElementById('specKuantitas').value = data.kuantitas || '';
                }

                document.getElementById('jenisTransaksi').value = data.jenis_transaksi;
                window.aturFormLogistik();
                document.getElementById('tglTransaksi').value = data.tanggal;

                if (data.jenis_transaksi === 'Barang Keluar') {
                    const parts = (data.asal_atau_proyek || '').split(' - ');
                    if (document.getElementById('namaProyek')) document.getElementById('namaProyek').value = parts[0] || '';
                    if (document.getElementById('namaProduk')) document.getElementById('namaProduk').value = parts[1] || '';
                } else {
                    if (document.getElementById('asalBarang')) document.getElementById('asalBarang').value = data.asal_atau_proyek || '';
                }
            }, 100);
        }, 200);

        document.getElementById('btnBatalEdit')?.classList.remove('hidden');
        window.scrollTo({ top: 0, behavior: 'smooth' });
    };

    // 4. INITIALIZATION
    document.addEventListener('DOMContentLoaded', function() {
        window.aturFormLogistik();

        document.getElementById('formBahanPembantu')?.addEventListener('submit', function() {
            document.getElementById('subKategori').disabled = false;
            document.getElementById('itemBarang').disabled = false;
        });
    });
</script>
@endsection