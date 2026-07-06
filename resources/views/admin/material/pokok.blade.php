@extends('layouts.admin')

@section('content')
<script src="https://cdn.tailwindcss.com"></script>

<div class="w-full mx-auto my-2 text-slate-700">

    <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between border-b border-slate-200 pb-4">
        <div>
            <div class="flex items-center space-x-2 text-xs text-slate-400 font-medium mb-1">
                <span>Manajemen Material</span>
                <span>/</span>
                <span class="text-indigo-600">Material Pokok</span>
            </div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-800">Modul Inventaris: Bahan Pokok Utama</h1>
            <p class="text-sm text-slate-500 mt-0.5">Pengelolaan logistik Kayu Solid, Olahan Kayu, dan Kalkulasi Konsumsi Produk Jadi</p>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm rounded-lg p-4 mb-4">
            {{ session('success') }}
        </div>
    @endif

    <div class="space-y-6">
        <form id="formBahanPokok" action="{{ route('material.pokok.store') }}" method="POST" class="space-y-6" onsubmit="return siapkanSubmitPokok()">
            @csrf
            <input type="hidden" name="_method" id="inputMethodPokok" value="POST">

            {{-- Hidden input untuk backend --}}
            <input type="hidden" name="spesifikasi" id="inputSpesifikasiPokok">
            <input type="hidden" name="satuan_input" id="inputSatuanInputPokok">
            <input type="hidden" name="kuantitas" id="inputKuantitasPokok">
            <input type="hidden" name="asal_atau_proyek" id="inputAsalProyekPokok">
            <input type="hidden" name="qty_fisik" id="inputQtyFisikPokok">
            <input type="hidden" name="satuan_fisik" id="inputSatuanFisikPokok">

            <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm grid grid-cols-1 md:grid-cols-2 gap-5">
    <div>
        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Sub-Kategori Bahan Pokok</label>
        <select id="category_id" name="category_id" onchange="updateItemDropdown()" class="w-full bg-white border border-slate-300 rounded-lg p-2.5 text-sm font-medium text-slate-700 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none transition-all" required>
            <option value="">-- Pilih Sub-Kategori --</option>
            @foreach($categories as $cat)
                {{-- Kita simpan nama_Kategori di value, dan tambahkan kelompok_material di data-kelompok --}}
                <option value="{{ $cat->nama_Kategori }}" data-kelompok="{{ $cat->kelompok_material }}">{{ $cat->nama_Kategori }}</option>
            @endforeach
        </select>
    </div>
    
    <div>
        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Item Barang</label>
        <select id="itemBarang" name="material_id" class="w-full bg-white border border-slate-300 rounded-lg p-2.5 text-sm font-medium text-slate-700 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none transition-all" onchange="renderSpesifikasiForm()" disabled>
            <option value="">-- Pilih Item --</option>
            @foreach($materials as $item)
                {{-- Tambahkan data-jenis untuk dicocokkan dengan sub-kategori --}}
                <option value="{{ $item->id }}" data-nama="{{ $item->nama_material }}" data-jenis="{{ $item->jenis_material }}" data-tipe="{{ $item->tipe_kalkulasi }}">{{ $item->nama_material }}</option>
            @endforeach
        </select>
    </div>
</div>  

            <div id="wrapperSpesifikasi" class="hidden bg-white p-5 rounded-xl border border-slate-200 shadow-sm">
                <h3 class="text-xs font-bold text-slate-700 uppercase tracking-wider mb-4 flex items-center gap-2">
                    <span>📐</span> Parameter Spesifikasi Fisik
                </h3>
                <div id="areaFormDinamis" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4"></div>
            </div>

            <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm space-y-5">
                <h3 class="text-xs font-bold text-slate-700 uppercase tracking-wider flex items-center gap-2">
                    <span>🚚</span> Status Logistik & Pelacakan Proyek
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Jenis Transaksi</label>
                        <select id="jenisTransaksi" name="jenis_transaksi" class="w-full bg-white border border-slate-300 rounded-lg p-2 text-sm text-slate-700 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none" onchange="aturFormLogistik()">
                            <option value="Stok Awal">Stok Awal Gudang</option>
                            <option value="Barang Masuk">Barang Masuk (+)</option>
                            <option value="Barang Keluar">Barang Keluar (-)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Tanggal</label>
                        <input type="date" id="tglTransaksi" name="tanggal" class="w-full bg-white border border-slate-300 rounded-lg p-2 text-sm text-slate-700 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Nama Proyek</label>
                        <input type="text" id="namaProyek" name="nama_proyek" placeholder="Contoh: Resto Namora / General" class="w-full bg-white border border-slate-300 rounded-lg p-2 text-sm text-slate-700 shadow-sm placeholder:text-slate-400 focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                    </div>
                    <div id="boxAsal">
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Asal / Supplier</label>
                        <input type="text" id="inputAsalProyekPokok" name="asal_supplier" placeholder="Contoh: PT. Jati Permai" class="w-full bg-white border border-slate-300 rounded-lg p-2 text-sm text-slate-700 shadow-sm placeholder:text-slate-400 focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                    </div>
                    <div id="boxNamaProduk" class="hidden">
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Nama Produk Jadi</label>
                        <input type="text" id="namaProduk" name="nama_produk_jadi" placeholder="Contoh: Kursi Makan / Meja Bar" class="w-full bg-white border border-slate-300 rounded-lg p-2 text-sm text-slate-700 shadow-sm placeholder:text-slate-400 focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                    </div>
                    <div id="boxQtyProduksi" class="hidden">
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Target Jumlah Unit</label>
                        <div class="flex items-center space-x-2">
                            <input type="number" id="qtyProduksi" name="qty_produksi" min="1" value="1" class="w-full bg-white border border-slate-300 rounded-lg p-2 text-sm text-slate-700 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                            <span class="text-xs text-slate-400 font-medium">Unit</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex justify-end items-center gap-3 pt-2">
                <button type="button" id="btnBatalEdit" onclick="batalkanEdit()" class="hidden text-sm font-medium text-slate-500 hover:text-slate-700 px-4 py-2.5">
                    Batal Edit
                </button>
                <button type="submit" id="btnSubmitPokok" class="w-full sm:w-auto bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-sm px-6 py-2.5 rounded-lg shadow-sm hover:shadow transition-all duration-150 flex items-center justify-center gap-2">
                    <span>💾</span> <span id="labelSubmitPokok">Amankan Data Stok Pokok</span>
                </button>
            </div>
        </form>

        <div class="bg-white rounded-xl border-2 border-slate-400 shadow-md p-5 space-y-4">
            <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider flex items-center space-x-2">
                <span>📊</span> <span>Jurnal Riwayat Transaksi Material Pokok</span>
            </h3>
        
            <div class="overflow-x-auto rounded-lg border-2 border-slate-400 bg-white">
                <table class="w-full text-sm text-left border-collapse border-2 border-slate-400">
                    <thead>
                        <tr class="bg-slate-100 text-slate-700 text-xs uppercase tracking-wider border-b-2 border-slate-400">
                            <th class="p-3 font-bold border-r-2 border-slate-400 text-center">Tanggal</th>
                            <th class="p-3 font-bold border-r-2 border-slate-400">Item Material</th>
                            <th class="p-3 font-bold border-r-2 border-slate-400 text-center">Status</th>
                            <th class="p-3 font-bold border-r-2 border-slate-400 text-center">Proyek</th>
                            <th class="p-3 font-bold border-r-2 border-slate-400 text-center">Tebal</th>
                            <th class="p-3 font-bold border-r-2 border-slate-400 text-center">Lebar</th>
                            <th class="p-3 font-bold border-r-2 border-slate-400 text-center">Panjang</th>
                            <th class="p-3 font-bold border-r-2 border-slate-400 text-center">Lokasi</th>
                            <th class="p-3 font-bold border-r-2 border-slate-400 text-center">Qty Fisik</th>
                            <th class="p-3 font-bold border-r-2 border-slate-400 text-right">Vol / Luas Akhir</th>
                            <th class="p-3 font-bold border-r-2 border-slate-400">Pelacakan Alokasi</th>
                            <th class="p-3 font-bold text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="badanTabelLog" class="divide-y-2 divide-slate-400">
                    @forelse($mutasiks as $log)
                    <tr class="hover:bg-slate-50 text-slate-800 transition-colors text-sm baris-log-data"
                        data-id="{{ $log->id }}"
                        data-material-id="{{ $log->material_id }}"
                        data-jenis-material="{{ $log->kategori_material }}"
                        data-jenis-transaksi="{{ $log->jenis_transaksi }}"
                        data-tanggal="{{ \Carbon\Carbon::parse($log->tanggal)->format('Y-m-d') }}"
                        data-nama-proyek="{{ $log->nama_proyek }}"
                        data-asal-supplier="{{ $log->asal_supplier }}"
                        data-nama-produk-jadi="{{ $log->nama_produk_jadi }}"
                        data-qty-produksi="{{ $log->qty_produksi }}"
                        data-spesifikasi="{{ $log->spesifikasi_lokasi }}"
                        data-satuan-input="{{ $log->satuan_input }}"
                        data-kuantitas="{{ $log->kuantitas }}"
                        data-qty-fisik="{{ $log->qty_fisik }}"
                        data-satuan-fisik="{{ $log->satuan_fisik }}"
                        data-tebal="{{ $log->tebal }}"
                        data-lebar="{{ $log->lebar }}"
                        data-panjang="{{ $log->panjang }}"
                        data-tipe-kalkulasi="{{ $log->material ? ($log->material->tipe_kalkulasi ?? ($log->material->kategori->tipe_kalkulasi ?? '')) : '' }}">
                        
                        <td class="p-3 text-xs text-slate-700 font-medium text-center border-r-2 border-slate-400 whitespace-nowrap">
                            {{ \Carbon\Carbon::parse($log->tanggal)->format('d-m-Y') }}
                        </td>
                        
                        <td class="p-3 font-semibold text-slate-900 border-r-2 border-slate-400">
                            {{ $log->material->nama_material ?? 'N/A' }}
                        </td>
                        
                        <td class="p-3 text-center border-r-2 border-slate-400 whitespace-nowrap">
                            <span class="px-2.5 py-0.5 text-xs rounded border {{ $log->jenis_transaksi == 'Barang Masuk' ? 'bg-emerald-50 text-emerald-700 border-emerald-300 font-bold' : ($log->jenis_transaksi == 'Barang Keluar' ? 'bg-rose-50 text-rose-700 border-rose-300 font-bold' : 'bg-indigo-50 text-indigo-700 border-indigo-300 font-bold') }}">
                                {{ $log->jenis_transaksi }}
                            </span>
                        </td>

                        <td class="p-3 text-center border-r-2 border-slate-400 font-semibold text-indigo-900 whitespace-nowrap">
                            @if($log->jenis_transaksi === 'Barang Keluar')
                                {{ $log->nama_proyek ?? 'General' }}
                            @else
                                -
                            @endif
                        </td>

                        <td class="p-3 text-center border-r-2 border-slate-400 font-mono text-xs">{{ $log->tebal ?: '-' }}</td>
                        
                        <td class="p-3 text-center border-r-2 border-slate-400 font-mono text-xs">{{ $log->lebar ?: '-' }}</td>
                        
                        <td class="p-3 text-center border-r-2 border-slate-400 font-mono text-xs">{{ $log->panjang ?: '-' }}</td>
                        
                        <td class="p-3 text-xs text-slate-700 border-r-2 border-slate-400 min-w-[280px] max-w-[350px]">
                        <div class="flex flex-col gap-1 text-left">
                            @if(!empty($log->spesifikasi_lokasi) && $log->spesifikasi_lokasi !== '-')
                                @foreach(explode('|', $log->spesifikasi_lokasi) as $info)
                                    <span class="inline-block bg-slate-100 text-slate-800 px-2 py-1 rounded border border-slate-200 shadow-sm font-medium">
                                        {{ trim($info) }}
                                    </span>
                                @endforeach
                            @else
                                <span class="text-slate-400 italic text-center w-full block">-</span>
                            @endif
                        </div>
                    </td>
                        <td class="p-3 text-center border-r-2 border-slate-400 whitespace-nowrap font-medium">
                            @if(!empty($log->qty_fisik) && $log->qty_fisik > 0)
                                {{ number_format($log->qty_fisik, 0) }} {{ $log->satuan_fisik ?? 'Pcs' }}
                            @else
                                -
                            @endif
                        </td>
                        
                        @php
                        // 1. Ambil nama material secara aman dari variabel perulangan $log kamu
                        $namaBarangPokok = strtolower($log->material->nama_material ?? $log->nama_barang ?? '');
                        $jenisBarangPokok = strtolower($log->material->jenis_material ?? '');

                        // 2. Deteksi secara mandiri apakah teks mengandung unsur kata lembaran furniture
                        $isLembaranPokok = \Illuminate\Support\Str::contains($namaBarangPokok, 'veneer') || 
                                        \Illuminate\Support\Str::contains($namaBarangPokok, 'plywood') || 
                                        \Illuminate\Support\Str::contains($namaBarangPokok, 'lembar') || 
                                        \Illuminate\Support\Str::contains($jenisBarangPokok, 'lembar');
                        
                        // 3. Tentukan teks satuannya: jika lembaran paksa M², jika bukan gunakan bawaan atau M³
                        $satuanTampil = $isLembaranPokok ? 'M²' : ($log->satuan_input ?? 'M³');
                    @endphp
                    <td class="p-3 text-right font-mono font-bold text-slate-900 border-r-2 border-slate-400 whitespace-nowrap">
                        {{ number_format($log->kuantitas, 4) }} {{ $satuanTampil }}
                    </td>
                        <td class="p-3 text-xs text-slate-700 border-r-2 border-slate-400">
                            @if($log->jenis_transaksi === 'Barang Keluar')
                                <span class="text-rose-600 font-semibold">🏭 Manufaktur:</span>
                                <span class="font-semibold text-slate-800">{{ $log->nama_produk_jadi ?: '-' }}</span>
                                @if($log->qty_produksi)
                                    <span class="text-xs text-slate-400 font-normal">({{ $log->qty_produksi }} Unit)</span>
                                @endif
                            @else
                                <span class="text-emerald-600 font-semibold">📦 Logistik:</span>
                                <span class="font-semibold text-slate-800">
                                    {{ (!empty($log->asal_supplier) && $log->asal_supplier !== '-') ? $log->asal_supplier : (($log->jenis_transaksi === 'Stok Awal' || $log->jenis_transaksi === 'Stok Awal Gudang') ? 'Restock Stok Awal' : 'Restock Umum') }}
                                </span>
                            @endif
                        </td>

                        <td class="p-3 text-center whitespace-nowrap">
                            <div class="flex items-center justify-center space-x-1.5">
                                <button type="button" onclick="editBarisLog(this)" class="bg-amber-50 text-amber-700 hover:bg-amber-100 border border-amber-300 p-1.5 rounded transition-colors text-xs font-semibold flex items-center gap-0.5 shadow-sm">
                                    ✏️ <span>Edit</span>
                                </button>
                                <button type="button" 
                                onclick="openDeleteModal('{{ route('material.pokok.destroy', $log->id) }}')" 
                                class="bg-rose-50 text-rose-700 hover:bg-rose-100 border border-rose-300 p-1.5 rounded transition-colors text-xs font-semibold flex items-center gap-0.5 shadow-sm">
                                🗑️ <span>Hapus</span>
                            </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="12" class="p-8 text-center text-slate-400 bg-slate-50 font-medium border border-slate-400">
                            Belum ada riwayat transaksi material pokok.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
                </table>
            </div>
        </div>
    </div>

    <div id="deleteModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black bg-opacity-50 backdrop-blur-sm transition-opacity duration-300">
    <div class="bg-white rounded-xl shadow-2xl max-w-md w-full p-6 transform scale-95 transition-transform duration-300 border border-rose-100 animate-fade-in">
        
        <div class="mx-auto flex items-center justify-center h-14 w-14 rounded-full bg-rose-50 border border-rose-200 mb-4">
            <span class="text-rose-600 text-2xl">⚠️</span>
        </div>

        <div class="text-center mb-6">
            <h3 class="text-lg font-bold text-slate-900 mb-2">Hapus Riwayat Transaksi?</h3>
            <p class="text-sm text-slate-500 leading-relaxed">
                Anda akan menghapus data riwayat ini. Tindakan ini akan <span class="text-rose-600 font-semibold">mengalkulasi ulang stok master</span> material secara otomatis.
            </p>
        </div>

        <form id="deleteForm" method="POST" action="">
            @csrf
            @method('DELETE')
            <div class="flex space-x-3">
                <button type="button" onclick="closeDeleteModal()" class="w-1/2 px-4 py-2.5 bg-slate-100 hover:bg-slate-200 border border-slate-300 text-slate-700 font-semibold text-sm rounded-lg transition-colors shadow-sm">
                    Tidak, batalkan
                </button>
                <button type="submit" class="w-1/2 px-4 py-2.5 bg-rose-600 hover:bg-rose-700 text-white font-semibold text-sm rounded-lg transition-colors shadow-sm focus:ring-2 focus:ring-rose-500 focus:ring-offset-2">
                    Ya, Hapus Data!
                </button>
            </div>
        </form>
    </div>
</div>
</div>

<script>
     function openDeleteModal(routeUrl) {
        const modal = document.getElementById('deleteModal');
        const form = document.getElementById('deleteForm');
        
        // Pasang route tujuan ke action form modal
        form.action = routeUrl;
        
        // Tampilkan modal dengan flex layout
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeDeleteModal() {
        const modal = document.getElementById('deleteModal');
        // Sembunyikan kembali modal
        modal.classList.remove('flex');
        modal.classList.add('hidden');
    }
    document.addEventListener("DOMContentLoaded", function() {
        if(document.getElementById('tglTransaksi')) {
            const hariIni = new Date().toISOString().split('T')[0];
            document.getElementById('tglTransaksi').value = hariIni;
        }
    });

    // FUNGSI UTAMA 1: Mengatur visibilitas logistik berdasarkan jenis transaksi
    function aturFormLogistik() {
        const jenis = document.getElementById('jenisTransaksi').value;
        const boxAsal = document.getElementById('boxAsal');
        const boxNamaProduk = document.getElementById('boxNamaProduk');
        const boxQtyProduksi = document.getElementById('boxQtyProduksi');

        if (jenis === 'Barang Keluar') {
            if(boxAsal) boxAsal.classList.add('hidden');
            if(boxNamaProduk) boxNamaProduk.classList.remove('hidden');
            if(boxQtyProduksi) boxQtyProduksi.classList.remove('hidden');
        } else {
            if(boxAsal) boxAsal.classList.remove('hidden');
            if(boxNamaProduk) boxNamaProduk.classList.add('hidden');
            if(boxQtyProduksi) boxQtyProduksi.classList.add('hidden');
        }
    }

    // FUNGSI UTAMA 2: Menghitung volume kubikasi kayu (M3) secara riil
    function hitungKubikasi() {
        const t = parseFloat(document.getElementById('k_tebal')?.value) || 0;
        const l = parseFloat(document.getElementById('k_lebar')?.value) || 0;
        const p = parseFloat(document.getElementById('k_panjang')?.value) || 0;
        const qty = parseFloat(document.getElementById('k_qty')?.value) || 0;

        const hasil = (t * l * p * qty) / 1000000;
        const preview = document.getElementById('calcPreviewM3');
        if (preview) preview.innerText = `Volume Hasil Konversi: ${hasil.toFixed(4)} M³`;
        return hasil;
    }

    // FUNGSI UTAMA 3: Menghitung luas veneer (M2) secara riil
    function hitungLuasVeneer() {
        const lebar = parseFloat(document.getElementById('v_lebar')?.value) || 0;
        const panjang = parseFloat(document.getElementById('v_panjang')?.value) || 0;
        const qty = parseFloat(document.getElementById('v_qty')?.value) || 0;

        const hasilLuas = (lebar / 100) * (panjang / 100) * qty;

        const preview = document.getElementById('calcPreviewM2');
        if (preview) {
            preview.innerText = `Luas Hasil Konversi: ${hasilLuas.toFixed(4)} m²`;
        }
        
        return hasilLuas;
    }

    // FUNGSI UTAMA 4: Memfilter dropdown item barang berdasarkan sub-kategori terpilih
    function updateItemDropdown() {
        const categorySelect = document.getElementById('category_id');
        const itemSelect = document.getElementById('itemBarang');
        const wrapper = document.getElementById('wrapperSpesifikasi');
        const areaForm = document.getElementById('areaFormDinamis');
        
        const selectedCategory = categorySelect.value; 

        itemSelect.innerHTML = '<option value="">-- Pilih Item --</option>';
        itemSelect.disabled = true;
        areaForm.innerHTML = '';
        wrapper.classList.add('hidden');

        if (selectedCategory === "") return;

        const allMaterials = @json($materials); 
        let hasItems = false;
        
        allMaterials.forEach(item => {
            if (item.jenis_material === selectedCategory) {
                const option = document.createElement('option');
                option.value = item.id;
                option.textContent = item.nama_material;
                option.setAttribute('data-tipe', item.tipe_kalkulasi);
                itemSelect.appendChild(option);
                hasItems = true;
            }
        });

        if (hasItems) {
            itemSelect.disabled = false;
        }
    }

    // FUNGSI UTAMA 5: Merender form parameter spesifikasi fisik secara dinamis
    function renderSpesifikasiForm() {
        const itemSelect = document.getElementById('itemBarang');
        const itemOption = itemSelect.options[itemSelect.selectedIndex];
        const tipeKalkulasi = itemOption ? itemOption.getAttribute('data-tipe') : '';

        const wrapper = document.getElementById('wrapperSpesifikasi');
        const areaForm = document.getElementById('areaFormDinamis');

        areaForm.innerHTML = "";

        if (!itemSelect.value || !tipeKalkulasi || tipeKalkulasi === 'null') { 
            wrapper.classList.add('hidden'); 
            return; 
        }
        
        wrapper.classList.remove('hidden');

        const inputClass = "w-full border border-slate-300 p-2 text-sm rounded-lg shadow-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none transition-all";
        const labelClass = "block text-xs font-medium text-slate-600 mb-1";

        if (tipeKalkulasi === 'volume_kayu') {
            areaForm.innerHTML = `
                <div><label class="${labelClass}">Tebal (cm)</label><input type="number" step="0.01" id="k_tebal" value="5" class="${inputClass}" oninput="hitungKubikasi()"></div>
                <div><label class="${labelClass}">Lebar (cm)</label><input type="number" step="0.01" id="k_lebar" value="20" class="${inputClass}" oninput="hitungKubikasi()"></div>
                <div><label class="${labelClass}">Panjang (cm)</label><input type="number" step="0.01" id="k_panjang" value="200" class="${inputClass}" oninput="hitungKubikasi()"></div>
                <div><label class="${labelClass}">Kualitas / Grade Kayu</label><select id="k_grade" class="${inputClass}"><option value="Grade A (Bagus)">Grade A (Bagus)</option><option value="Grade B">Grade B</option><option value="Grade C">Grade C</option></select></div>
                <div><label class="${labelClass}">Lokasi Gudang</label><input type="text" id="k_gudang" value="Gudang A Utama" placeholder="Nama Gudang/Rak" class="${inputClass}"></div>
                <div><label class="${labelClass}">Jumlah Batang / Pcs</label><input type="number" id="k_qty" value="1" class="${inputClass}" oninput="hitungKubikasi()"></div>
                <div class="md:col-span-3 bg-slate-50 text-slate-700 p-3 rounded-lg border border-slate-200 font-mono font-semibold text-center text-xs mt-2" id="calcPreviewM3">Volume Hasil Konversi: 0.0200 M³</div>
            `;
            hitungKubikasi();
        }
        else if (tipeKalkulasi === 'lembar_board') {
            areaForm.innerHTML = `
                <div><label class="${labelClass}">Merek / Jenis Board</label><input type="text" id="b_merk" placeholder="Contoh: Mercy / Meranti" class="${inputClass}" required></div>
                <div><label class="${labelClass}">Tebal Board (MM)</label><input type="number" step="0.01" id="b_tebal" placeholder="Contoh: 18" class="${inputClass}" required></div>
                <div><label class="${labelClass}">Jumlah (Lembar)</label><input type="number" id="b_qty" value="1" class="${inputClass}"></div>
            `;
        }
        else if (tipeKalkulasi === 'lembar_hpl') {
            areaForm.innerHTML = `
                <div><label class="${labelClass}">Merek HPL</label><select id="h_merk" class="${inputClass}"><option>Taco</option><option>Omega</option><option>Lamitak</option></select></div>
                <div><label class="${labelClass}">Kode Warna / Motif</label><input type="text" id="h_kode" placeholder="Contoh: TH 001 G" class="${inputClass}" required></div>
                <div><label class="${labelClass}">Jumlah (Lembar)</label><input type="number" id="h_qty" value="1" class="${inputClass}"></div>
            `;
        }
        else if (tipeKalkulasi === 'luas_veneer') {
            areaForm.innerHTML = `
                <div><label class="${labelClass}">Jenis Kayu Veneer</label><input type="text" id="v_jenis" placeholder="Sungkai, Meranti" class="${inputClass}" required></div>
                <div><label class="${labelClass}">Nomor Bendel (Inti)</label><input type="text" id="v_bendel" placeholder="Wajib dari Supplier" class="${inputClass}" required></div>
                <div>
                    <label class="${labelClass}">Jenis Serat Kain/Kayu</label>
                    <select id="v_jenis_serat" class="${inputClass}" required>
                        <option value="Serat Lurus">Serat Lurus</option>
                        <option value="Serat Kembang">Serat Kembang</option>
                    </select>
                </div>
                <div><label class="${labelClass}">Tebal Lembaran (mm)</label><input type="number" step="0.1" id="v_tebal" value="0.6" class="${inputClass}" required></div>
                <div><label class="${labelClass}">Lebar Lembaran (cm)</label><input type="number" step="0.01" id="v_lebar" value="20" class="${inputClass}" oninput="hitungLuasVeneer()"></div>
                <div><label class="${labelClass}">Panjang Lembaran (cm)</label><input type="number" step="0.01" id="v_panjang" value="120" class="${inputClass}" oninput="hitungLuasVeneer()"></div>
                <div><label class="${labelClass}">Jumlah (Lembar)</label><input type="number" id="v_qty" value="1" class="${inputClass}" oninput="hitungLuasVeneer()"></div>
                <div class="md:col-span-3 bg-slate-50 text-slate-700 p-3 rounded-lg border border-slate-200 font-mono font-semibold text-center text-xs mt-2" id="calcPreviewM2">Luas Hasil Konversi: 0.24 m²</div>
            `;
            hitungLuasVeneer();
        }
        else {
            wrapper.classList.add('hidden');
        }
    }

    // FUNGSI UTAMA 6: Validasi enkapsulasi data sebelum dikirimkan ke Controller backend
    function siapkanSubmitPokok() {
        const itemSelect = document.getElementById('itemBarang');
        if (!itemSelect.value) {
            alert('Silakan tentukan item material pokok!');
            return false;
        }

        const itemOption = itemSelect.options[itemSelect.selectedIndex];
        const tipeKalkulasi = itemOption.getAttribute('data-tipe');
        
        const supplierInput = document.getElementById('asalBarang') || document.getElementById('asal_barang') || document.getElementsByName('asal_barang')[0];
        const supplier = supplierInput && supplierInput.value.trim() !== "" ? supplierInput.value.trim() : 'Tanpa Supplier';
        
        let spesifikasi = "", satuanInput = "", kuantitas = 0;
        let qtyFisik = 0, satuanFisik = "";
        let tebal = null, lebar = null, panjang = null;
        let spesifikasiLokasiLengkap = "-";

        if (tipeKalkulasi === 'volume_kayu') {
            const grade = document.getElementById('k_grade').value;
            const gudang = document.getElementById('k_gudang').value;
            tebal = parseFloat(document.getElementById('k_tebal').value) || 0;
            lebar = parseFloat(document.getElementById('k_lebar').value) || 0;
            panjang = parseFloat(document.getElementById('k_panjang').value) || 0;
            
            qtyFisik = parseFloat(document.getElementById('k_qty').value) || 1;
            kuantitas = hitungKubikasi();
            satuanInput = "M3";
            
            spesifikasi = `Supplier: ${supplier} | Grade: ${grade} | T: ${tebal}cm x L: ${lebar}cm x P: ${panjang}cm`;
            satuanFisik = "Batang";
            spesifikasiLokasiLengkap = `${gudang} - Supplier: ${supplier}`; 
        }
        else if (tipeKalkulasi === 'lembar_board') {
            const merk = document.getElementById('b_merk').value;
            tebal = parseFloat(document.getElementById('b_tebal').value) || 0;
            kuantitas = parseFloat(document.getElementById('b_qty').value) || 1;
            
            qtyFisik = kuantitas;
            satuanInput = "Lembar";
            satuanFisik = "Lembar";
            spesifikasi = `Supplier: ${supplier} | Merek: ${merk} | Tebal: ${tebal}mm`;
            spesifikasiLokasiLengkap = `${merk} - Supplier: ${supplier}`;
        }
        else if (tipeKalkulasi === 'lembar_hpl') {
            const merk = document.getElementById('h_merk').value;
            const kode = document.getElementById('h_kode').value;
            kuantitas = parseFloat(document.getElementById('h_qty').value) || 1;
            
            qtyFisik = kuantitas;
            satuanInput = "Lembar";
            satuanFisik = "Lembar";
            spesifikasi = `Supplier: ${supplier} | Merek: ${merk} | Kode: ${kode}`;
            spesifikasiLokasiLengkap = `${kode} - Supplier: ${supplier}`;
        }
        else if (tipeKalkulasi === 'luas_veneer') {
            const jenis = document.getElementById('v_jenis').value;
            const bendel = document.getElementById('v_bendel').value;
            const serat = document.getElementById('v_jenis_serat')?.value || 'Serat Lurus';
            
            tebal = parseFloat(document.getElementById('v_tebal').value) || 0;
            lebar = parseFloat(document.getElementById('v_lebar').value) || 0;
            panjang = parseFloat(document.getElementById('v_panjang').value) || 0;
            qtyFisik = parseFloat(document.getElementById('v_qty').value) || 1;

            kuantitas = (lebar / 100) * (panjang / 100) * qtyFisik; 
            satuanInput = "M2";
            satuanFisik = "Lembar";
            spesifikasi = `Supplier: ${supplier} | Jenis: ${jenis} | No. Bendel: ${bendel} | Serat: ${serat} | Tebal: ${tebal}mm`;
            spesifikasiLokasiLengkap = `${jenis} / Bendel: ${bendel} (${serat}) - Supplier: ${supplier}`;
        }

        if (!kuantitas || kuantitas <= 0) {
            alert('Kuantitas harus diisi dan lebih dari 0!');
            return false;
        }

        document.getElementById('inputSpesifikasiPokok').value = spesifikasi;
        document.getElementById('inputSatuanInputPokok').value = satuanInput;
        document.getElementById('inputKuantitasPokok').value = kuantitas;
        document.getElementById('inputQtyFisikPokok').value = qtyFisik;
        document.getElementById('inputSatuanFisikPokok').value = satuanFisik;

        suntikHiddenInput('tebal', tebal);
        suntikHiddenInput('lebar', lebar);
        suntikHiddenInput('panjang', panjang);
        suntikHiddenInput('spesifikasi_lokasi', spesifikasiLokasiLengkap);

        const jenisTrans = document.getElementById('jenisTransaksi').value;
        let asalAtauProyek = "-";

        if (jenisTrans === 'Barang Keluar') {
            asalAtauProyek = document.getElementById('namaProyek').value || 'General';
        } else if (jenisTrans === 'Stok Awal') {
            const supplierInput = document.getElementById('asalBarang').value.trim();
            asalAtauProyek = supplierInput ? `Stok Awal: ${supplierInput}` : 'Stok Awal Gudang';
        } else if (jenisTrans === 'Barang Masuk') {
            const supplierInput = document.getElementById('asalBarang').value.trim();
            asalAtauProyek = supplierInput ? `Restock dari: ${supplierInput}` : 'Restock';
        }

        // --- AMANKAN SUPALIER & PROYEK DARI DATA-ATTRIBUTE SEBELUM SUBMIT ---
        const form = document.getElementById('formBahanPokok');
        // Jika input di layar kosong (akibat trigger reset), ambil paksa dari apa yang ada di form aksi edit
        let supplierFinal = document.getElementById('asalBarang')?.value.trim();
        let proyekFinal = document.getElementById('namaProyek')?.value.trim();

        if (jenisTrans === 'Barang Keluar') {
            asalAtauProyek = proyekFinal ? proyekFinal : 'General';
        } else if (jenisTrans === 'Stok Awal') {
            asalAtauProyek = supplierFinal ? `Stok Awal: ${supplierFinal}` : 'Stok Awal Gudang';
        } else if (jenisTrans === 'Barang Masuk') {
            asalAtauProyek = supplierFinal ? `Restock dari: ${supplierFinal}` : 'Restock';
        }

        // Tembak mati ke input hidden utama yang ditunggu oleh Controller Laravel-mu!
        document.getElementById('inputAsalProyekPokok').value = asalAtauProyek;
        
        // Cadangan input hidden untuk form biasa
        suntikHiddenInput('asal_barang', supplierFinal);
        suntikHiddenInput('nama_proyek', proyekFinal);
        suntikHiddenInput('asal_atau_proyek', asalAtauProyek);
        
        return true;
    }

    function suntikHiddenInput(nama, nilai) {
        let inputHidden = document.getElementById('runtime_hidden_' + nama);
        if(!inputHidden) {
            inputHidden = document.createElement('input');
            inputHidden.type = 'hidden';
            inputHidden.name = nama;
            inputHidden.id = 'runtime_hidden_' + nama;
            document.getElementById('formBahanPokok').appendChild(inputHidden);
        }
        inputHidden.value = nilai !== null ? nilai : '';
    }

    // FUNGSI UTAMA 7: Mengisi otomatis data form saat baris log tabel di-klik Edit
    function editBarisLog(btn) {
        const baris = btn.closest('tr');
        const d = baris.dataset;

        const form = document.getElementById('formBahanPokok');
        form.action = `/material/pokok/${d.id}`;
        document.getElementById('inputMethodPokok').value = 'PUT';
        document.getElementById('btnBatalEdit').classList.remove('hidden');
        document.getElementById('labelSubmitPokok').innerText = 'Perbarui Data Transaksi';

        // Amankan Jenis Transaksi
        if(document.getElementById('jenisTransaksi')) {
            document.getElementById('jenisTransaksi').value = d.jenisTransaksi || 'Stok Awal';
        }
        aturFormLogistik();

        if(document.getElementById('tglTransaksi')) document.getElementById('tglTransaksi').value = d.tanggal;
        if(document.getElementById('namaProyek')) document.getElementById('namaProyek').value = d.namaProyek || '';

        if (d.jenisTransaksi === 'Barang Keluar') {
            if(document.getElementById('namaProduk')) document.getElementById('namaProduk').value = d.namaProdukJadi || '';
            if(document.getElementById('qtyProduksi')) document.getElementById('qtyProduksi').value = d.qtyProduksi || 1;
        } else {
            if(document.getElementById('asalBarang')) {
                // 1. Coba ambil dari dataset dulu
                let supplierMurni = d.asalSupplier || '';
                
                // 2. JALUR DARURAT: Jika dataset kosong, kita bedah teks dari kolom spesifikasi/keterangan di tabel (biasanya cell ke-4 atau ke-5)
                if (!supplierMurni || supplierMurni === 'null' || supplierMurni === '-') {
                    // Kita cari string di semua cell baris ini yang mengandung kata 'Supplier:'
                    for (let i = 0; i < baris.cells.length; i++) {
                        let textCell = baris.cells[i].innerText;
                        if (textCell.includes('Supplier:')) {
                            // Ambil teks setelah 'Supplier:' sampai batas pipa '|' atau akhir teks
                            let bagianSupplier = textCell.split('Supplier:')[1];
                            if (bagianSupplier) {
                                supplierMurni = bagianSupplier.split('|')[0].trim();
                            }
                            break;
                        }
                    }
                }

                // Bersihkan embel-embel teks jika masih terbawa
                supplierMurni = supplierMurni.replace('Restock dari: ', '').replace('Stok Awal: ', '').replace('Tanpa Supplier', '');
                document.getElementById('asalBarang').value = supplierMurni;
            }
        }
     

        const catSelect = document.getElementById('category_id');
        if(catSelect) {
            catSelect.value = d.jenisMaterial || ''; 
            updateItemDropdown();
        }

        const itemSelect = document.getElementById('itemBarang');
        if(itemSelect) {
            itemSelect.value = d.materialId;
            
            if (itemSelect.value !== d.materialId) {
                const opsiBaru = document.createElement('option');
                opsiBaru.value = d.materialId;
                opsiBaru.text = baris.cells[1].innerText.trim();
                opsiBaru.setAttribute('data-tipe', d.tipeKalkulasi);
                itemSelect.add(opsiBaru);
                itemSelect.value = d.materialId;
            }
            if(itemSelect.selectedIndex >= 0) {
                itemSelect.options[itemSelect.selectedIndex].setAttribute('data-tipe', d.tipeKalkulasi);
            }
        }
        
        renderSpesifikasiForm();

        // Mapping nilai spesifikasi fisik berdasarkan tipe kalkulasi
        if (d.tipeKalkulasi === 'volume_kayu') {
            if(document.getElementById('k_tebal')) document.getElementById('k_tebal').value = d.tebal || '';
            if(document.getElementById('k_lebar')) document.getElementById('k_lebar').value = d.lebar || '';
            if(document.getElementById('k_panjang')) document.getElementById('k_panjang').value = d.panjang || '';
            if(document.getElementById('k_qty')) document.getElementById('k_qty').value = d.qtyFisik || 1;
            
            let lokasiMurni = d.spesifikasi && d.spesifikasi.includes('Lokasi:') ? d.spesifikasi.split('|')[1].replace('Lokasi:', '').trim() : 'Gudang A Utama';
            if(document.getElementById('k_gudang')) document.getElementById('k_gudang').value = lokasiMurni;
            hitungKubikasi();
        }
        else if (d.tipeKalkulasi === 'lembar_board') {
            let merkMurni = d.spesifikasi && d.spesifikasi.includes('Merek:') ? d.spesifikasi.split('|')[1].replace('Merek:', '').trim() : d.spesifikasi;
            if(document.getElementById('b_merk')) document.getElementById('b_merk').value = merkMurni || '';
            if(document.getElementById('b_tebal')) document.getElementById('b_tebal').value = d.tebal || '';
            if(document.getElementById('b_qty')) document.getElementById('b_qty').value = d.qtyFisik || 1;
        }
        else if (d.tipeKalkulasi === 'lembar_hpl') {
            let merekLama = d.spesifikasi && d.spesifikasi.includes('Merek:') ? d.spesifikasi.split('|')[1].replace('Merek:', '').trim() : 'Taco';
            let kodeLama = d.spesifikasi && d.spesifikasi.includes('Kode:') ? d.spesifikasi.split('|')[2].replace('Kode:', '').trim() : d.spesifikasi;
            
            if(document.getElementById('h_merk')) document.getElementById('h_merk').value = merekLama; 
            if(document.getElementById('h_kode')) document.getElementById('h_kode').value = kodeLama || '';
            if(document.getElementById('h_qty')) document.getElementById('h_qty').value = d.qtyFisik || 1;
        }
        else if (d.tipeKalkulasi === 'luas_veneer') {
            let jenisLama = d.spesifikasi && d.spesifikasi.includes('Jenis:') ? d.spesifikasi.split('|')[1].replace('Jenis:', '').trim() : '';
            let bendelLama = d.spesifikasi && d.spesifikasi.includes('No. Bendel:') ? d.spesifikasi.split('|')[2].replace('No. Bendel:', '').trim() : '';

            if(document.getElementById('v_jenis')) document.getElementById('v_jenis').value = jenisLama;
            if(document.getElementById('v_bendel')) document.getElementById('v_bendel').value = bendelLama;
            if(document.getElementById('v_tebal')) document.getElementById('v_tebal').value = d.tebal || '0.6';
            if(document.getElementById('v_lebar')) document.getElementById('v_lebar').value = d.lebar || '';
            if(document.getElementById('v_panjang')) document.getElementById('v_panjang').value = d.panjang || '';
            if(document.getElementById('v_qty')) document.getElementById('v_qty').value = d.qtyFisik || 1;
            hitungLuasVeneer();
        }

        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    // FUNGSI UTAMA 8: Membersihkan form jika tombol batal ditekan
    function batalkanEdit() {
        const form = document.getElementById('formBahanPokok');
        form.action = "{{ route('material.pokok.store') }}";
        document.getElementById('inputMethodPokok').value = 'POST';
        document.getElementById('btnBatalEdit').classList.add('hidden');
        document.getElementById('labelSubmitPokok').innerText = 'Amankan Data Stok Pokok';

        form.reset();
        document.getElementById('itemBarang').disabled = true;
        document.getElementById('wrapperSpesifikasi').classList.add('hidden');
        
        const hariIni = new Date().toISOString().split('T')[0];
        document.getElementById('tglTransaksi').value = hariIni;
        
        aturFormLogistik();
        
        ['tebal', 'lebar', 'panjang', 'spesifikasi_lokasi'].forEach(nama => {
            const el = document.getElementById('runtime_hidden_' + nama);
            if(el) el.remove();
        });
    }
</script>
@endsection