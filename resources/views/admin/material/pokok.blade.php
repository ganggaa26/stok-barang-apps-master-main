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
            <p class="text-sm text-slate-500 mt-0.5">Pengelolaan logistik Kayu Solid, Olahan Kayu, dan Pelapis Lapisan Struktur</p>
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

            {{-- Hidden input: diisi JS dari hasil kalkulasi sebelum form dikirim --}}
            <input type="hidden" name="spesifikasi" id="inputSpesifikasiPokok">
            <input type="hidden" name="satuan_input" id="inputSatuanInputPokok">
            <input type="hidden" name="kuantitas" id="inputKuantitasPokok">
            <input type="hidden" name="asal_atau_proyek" id="inputAsalProyekPokok">

            <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Sub-Kategori Bahan Pokok</label>
                    <select id="category_id" name="category_id" class="w-full bg-white border border-slate-300 rounded-lg p-2.5 text-sm font-medium text-slate-700 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none transition-all" onchange="updateItemDropdown()">
                        <option value="">-- Pilih Sub-Kategori --</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" data-slug="{{ Str::slug($cat->nama_Kategori, '_') }}">{{ $cat->nama_Kategori }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Item Barang</label>
                    <select id="itemBarang" name="material_id" class="w-full bg-white border border-slate-300 rounded-lg p-2.5 text-sm font-medium text-slate-700 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none transition-all" onchange="renderSpesifikasiForm()" disabled>
                        <option value="">-- Pilih Item --</option>
                        @foreach($materials as $item)
                            <option value="{{ $item->id }}" data-nama="{{ $item->nama_material }}" data-tipe="{{ $item->tipe_kalkulasi }}">{{ $item->nama_material }}</option>
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
                    <span>🚚</span> Status Logistik & Tujuan Proyek
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
                    <div id="boxAsal">
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Asal / Supplier</label>
                        <input type="text" id="asalBarang" placeholder="Contoh: PT. Jati Permai" class="w-full bg-white border border-slate-300 rounded-lg p-2 text-sm text-slate-700 shadow-sm placeholder:text-slate-400 focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                    </div>
                    <div id="boxProyek" class="hidden">
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Nama Proyek / Produk Jadi</label>
                        <input type="text" id="namaProyek" placeholder="Contoh: Meja Resepsionis" class="w-full bg-white border border-slate-300 rounded-lg p-2 text-sm text-slate-700 shadow-sm placeholder:text-slate-400 focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                    </div>
                </div>
            </div>

            <div class="flex justify-end pt-2">
                <button type="submit" class="w-full sm:w-auto bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-sm px-6 py-2.5 rounded-lg shadow-sm hover:shadow transition-all duration-150 flex items-center justify-center gap-2">
                    <span>💾</span> Amankan Data Stok Pokok
                </button>
            </div>
        </form>

        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5 space-y-4">
            <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider flex items-center space-x-2">
                <span>📊</span> <span>Jurnal Riwayat Transaksi Material Pokok</span>
            </h3>
            <div class="overflow-x-auto rounded-lg border border-slate-200 bg-white">
                <table class="w-full text-sm text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 text-slate-500 text-xs uppercase tracking-wider border-b border-slate-200">
                            <th class="p-3.5 font-semibold">Tanggal</th>
                            <th class="p-3.5 font-semibold">Item Material</th>
                            <th class="p-3.5 font-semibold text-center">Status</th>
                            <th class="p-3.5 font-semibold">Spesifikasi & Karakteristik</th>
                            <th class="p-3.5 font-semibold text-right">Kuantitas Log</th>
                            <th class="p-3.5 font-semibold">Pelacakan Logistik</th>
                            <th class="p-3.5 font-semibold text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="badanTabelLog" class="divide-y divide-slate-100">
                        @forelse($mutasiks as $log)
                        <tr class="hover:bg-slate-50 text-slate-700 border-b border-slate-100 transition-colors text-sm baris-log-data">
                            <td class="p-3.5 text-xs text-slate-400 whitespace-nowrap">{{ \Carbon\Carbon::parse($log->tanggal)->format('d-m-Y') }}</td>
                            <td class="p-3.5 font-medium text-slate-800">{{ $log->material->nama_material ?? 'N/A' }}</td>
                            <td class="p-3.5 text-center whitespace-nowrap">
                                <span class="px-2.5 py-0.5 text-xs rounded-full {{ $log->jenis_transaksi == 'Barang Masuk' ? 'bg-emerald-50 text-emerald-600 font-semibold' : ($log->jenis_transaksi == 'Barang Keluar' ? 'bg-rose-50 text-rose-600 font-semibold' : 'bg-indigo-50 text-indigo-600 font-semibold') }}">
                                    {{ $log->jenis_transaksi }}
                                </span>
                            </td>
                            <td class="p-3.5 text-xs text-slate-500">{{ $log->spesifikasi }}</td>
                            <td class="p-3.5 text-right font-mono font-bold text-slate-800 whitespace-nowrap">{{ $log->kuantitas }} {{ $log->satuan_input }}</td>
                            <td class="p-3.5 text-xs text-slate-500">{{ $log->asal_atau_proyek }}</td>
                            <td class="p-3.5 text-center whitespace-nowrap">
                                <div class="flex items-center justify-center space-x-1.5">
                                    {{-- Aksi Edit & Hapus Database / State --}}
                                    <button type="button" onclick="editBarisLog(this)" class="bg-amber-50 text-amber-600 hover:bg-amber-100 p-1.5 rounded transition-colors text-xs font-medium flex items-center gap-0.5 shadow-sm">
                                        ✏️ <span>Edit</span>
                                    </button>
                                    <form action="{{ route('material.pokok.destroy', $log->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="bg-rose-50 text-rose-600 hover:bg-rose-100 p-1.5 rounded transition-colors text-xs font-medium flex items-center gap-0.5 shadow-sm">
                                            🗑️ <span>Hapus</span>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr id="barisKosong">
                            <td colspan="7" class="p-8 text-center text-slate-400 italic bg-slate-50/50">
                                Belum ada mutasi material pokok yang tercatat di database.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        document.getElementById('tglTransaksi').valueAsDate = new Date();
    });

    function updateItemDropdown() {
        const catSelect = document.getElementById('category_id');
        const selectedOption = catSelect.options[catSelect.selectedIndex];
        const subSlug = selectedOption ? selectedOption.getAttribute('data-slug') : '';
        const itemSelect = document.getElementById('itemBarang');

        itemSelect.innerHTML = '<option value="">-- Pilih Item --</option>';
        document.getElementById('wrapperSpesifikasi').classList.add('hidden');

        if (!subSlug) {
            itemSelect.disabled = true;
            return;
        }

        itemSelect.disabled = false;

        @foreach($materials as $item)
            var currentItemSub = "{{ Str::slug($item->jenis_material, '_') }}";
            if (currentItemSub === subSlug) {
                itemSelect.innerHTML += `<option value="{{ $item->id }}" data-nama="{{ $item->nama_material }}" data-tipe="{{ $item->tipe_kalkulasi }}">{{ $item->nama_material }}</option>`;
            }
        @endforeach
    }

    function aturFormLogistik() {
        const jenis = document.getElementById('jenisTransaksi').value;
        const boxAsal = document.getElementById('boxAsal');
        const boxProyek = document.getElementById('boxProyek');

        if (jenis === 'Barang Keluar') {
            boxAsal.classList.add('hidden');
            boxProyek.classList.remove('hidden');
        } else {
            boxAsal.classList.remove('hidden');
            boxProyek.classList.add('hidden');
        }
    }

    function renderSpesifikasiForm() {
        const itemSelect = document.getElementById('itemBarang');
        const itemOption = itemSelect.options[itemSelect.selectedIndex];
        const tipeKalkulasi = itemOption ? itemOption.getAttribute('data-tipe') : '';

        const wrapper = document.getElementById('wrapperSpesifikasi');
        const areaForm = document.getElementById('areaFormDinamis');

        if (!itemSelect.value) { wrapper.classList.add('hidden'); return; }
        wrapper.classList.remove('hidden');
        areaForm.innerHTML = "";

        const inputClass = "w-full border border-slate-300 p-2 text-sm rounded-lg shadow-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none transition-all";
        const labelClass = "block text-xs font-medium text-slate-600 mb-1";

        if (tipeKalkulasi === 'volume_kayu') {
            areaForm.innerHTML = `
                <div><label class="${labelClass}">Tebal (cm)</label><input type="number" id="k_tebal" value="5" class="${inputClass}" oninput="hitungKubikasi()"></div>
                <div><label class="${labelClass}">Lebar (cm)</label><input type="number" id="k_lebar" value="20" class="${inputClass}" oninput="hitungKubikasi()"></div>
                <div><label class="${labelClass}">Panjang (cm)</label><input type="number" id="k_panjang" value="200" class="${inputClass}" oninput="hitungKubikasi()"></div>
                <div><label class="${labelClass}">Kualitas / Grade Kayu</label><select id="k_grade" class="${inputClass}"><option value="A">Grade A (Bagus)</option><option value="B">Grade B (Biasa)</option><option value="C">Grade C (Kurang Bagus)</option></select></div>
                <div><label class="${labelClass}">Lokasi Gudang</label><input type="text" id="k_gudang" value="Gudang A Utama" placeholder="Nama Gudang/Rak" class="${inputClass}"></div>
                <div><label class="${labelClass}">Jumlah Batang / Pcs</label><input type="number" id="k_qty" value="1" class="${inputClass}" oninput="hitungKubikasi()"></div>
                <div class="md:col-span-3 bg-slate-50 text-slate-700 p-3 rounded-lg border border-slate-200 font-mono font-semibold text-center text-xs mt-2" id="calcPreviewM3">Volume Hasil Konversi: 0.0200 M³</div>
            `;
            hitungKubikasi();
        }
        else if (tipeKalkulasi === 'lembar_board') {
            areaForm.innerHTML = `
                <div><label class="${labelClass}">Merek / Jenis Board</label><input type="text" id="b_merk" placeholder="Contoh: Mercy / Meranti" class="${inputClass}" required></div>
                <div><label class="${labelClass}">Tebal Board (MM)</label><input type="number" id="b_tebal" placeholder="Contoh: 18" class="${inputClass}" required></div>
                <div><label class="${labelClass}">Jumlah (Lembar)</label><input type="number" id="b_qty" value="1" class="${inputClass}" required></div>
            `;
        }
        else if (tipeKalkulasi === 'lembar_hpl') {
            areaForm.innerHTML = `
                <div><label class="${labelClass}">Merek HPL</label><select id="h_merk" class="${inputClass}"><option>Taco</option><option>Omega</option><option>Lamitak</option></select></div>
                <div><label class="${labelClass}">Kode Warna / Motif</label><input type="text" id="h_kode" placeholder="Contoh: TH 001 G" class="${inputClass}" required></div>
                <div><label class="${labelClass}">Jumlah (Lembar)</label><input type="number" id="h_qty" value="1" class="${inputClass}" required></div>
            `;
        }
        else if (tipeKalkulasi === 'luas_veneer') {
            areaForm.innerHTML = `
                <div><label class="${labelClass}">Jenis Kayu Veneer</label><input type="text" id="v_jenis" placeholder="Sungkai, Meranti" class="${inputClass}" required></div>
                <div><label class="${labelClass}">Nomor Bendel (Inti)</label><input type="text" id="v_bendel" placeholder="Wajib dari Supplier" class="${inputClass}" required></div>
                <div><label class="${labelClass}">Tebal Lembaran (mm)</label><input type="number" step="0.1" id="v_tebal" value="0.6" class="${inputClass}" required></div>
                <div><label class="${labelClass}">Lebar Lembaran (cm)</label><input type="number" id="v_lebar" value="20" class="${inputClass}" oninput="hitungLuasVeneer()"></div>
                <div><label class="${labelClass}">Panjang Lembaran (cm)</label><input type="number" id="v_panjang" value="120" class="${inputClass}" oninput="hitungLuasVeneer()"></div>
                <div><label class="${labelClass}">Jumlah (Lembar)</label><input type="number" id="v_qty" value="1" class="${inputClass}" oninput="hitungLuasVeneer()"></div>
                <div class="md:col-span-3 bg-slate-50 text-slate-700 p-3 rounded-lg border border-slate-200 font-mono font-semibold text-center text-xs mt-2" id="calcPreviewM2">Luas Hasil Konversi: 0.24 m²</div>
            `;
            hitungLuasVeneer();
        }
        else {
            areaForm.innerHTML = `
                <div class="md:col-span-3 bg-amber-50 text-amber-700 p-3 rounded-lg border border-amber-200 text-xs">
                    ⚠️ Item ini belum punya "tipe_kalkulasi" yang dikenali sistem.
                    Cek kolom <code>tipe_kalkulasi</code> di data master untuk item ini.
                </div>
            `;
        }
    }

    function hitungKubikasi() {
        const t = parseFloat(document.getElementById('k_tebal').value) || 0;
        const l = parseFloat(document.getElementById('k_lebar').value) || 0;
        const p = parseFloat(document.getElementById('k_panjang').value) || 0;
        const b = parseFloat(document.getElementById('k_qty').value) || 0;
        const totalM3 = (t * l * p / 1000000) * b;
        const preview = document.getElementById('calcPreviewM3');
        if(preview) preview.innerText = `Volume Hasil Konversi: ${totalM3.toFixed(4)} M³ (Berdasarkan total ${b} Batang)`;
        return totalM3;
    }

    function hitungLuasVeneer() {
        const l = parseFloat(document.getElementById('v_lebar').value) || 0;
        const p = parseFloat(document.getElementById('v_panjang').value) || 0;
        const lembar = parseFloat(document.getElementById('v_qty').value) || 0;
        const totalM2 = (l * p / 10000) * lembar;
        const preview = document.getElementById('calcPreviewM2');
        if(preview) preview.innerText = `Luas Hasil Konversi: ${totalM2.toFixed(2)} m² (Berdasarkan total ${lembar} Lembar)`;
        return totalM2;
    }

    function siapkanSubmitPokok() {
        const itemSelect = document.getElementById('itemBarang');
        if (!itemSelect.value) {
            alert('Silakan tentukan item material pokok!');
            return false;
        }

        const itemOption = itemSelect.options[itemSelect.selectedIndex];
        const tipeKalkulasi = itemOption.getAttribute('data-tipe');
        let spesifikasi = "", satuanInput = "", kuantitas = 0;

        if (tipeKalkulasi === 'volume_kayu') {
            const grade = document.getElementById('k_grade').value;
            const gudang = document.getElementById('k_gudang').value;
            kuantitas = hitungKubikasi();
            satuanInput = "M3";
            spesifikasi = `Grade: ${grade} | Lokasi: ${gudang} (${document.getElementById('k_qty').value} Batang)`;
        }
        else if (tipeKalkulasi === 'lembar_board') {
            const merk = document.getElementById('b_merk').value;
            const tebal = document.getElementById('b_tebal').value;
            kuantitas = parseFloat(document.getElementById('b_qty').value) || 0;
            satuanInput = "Lembar";
            spesifikasi = `Merek: ${merk} | Tebal: ${tebal}mm`;
        }
        else if (tipeKalkulasi === 'lembar_hpl') {
            const merk = document.getElementById('h_merk').value;
            const kode = document.getElementById('h_kode').value;
            kuantitas = parseFloat(document.getElementById('h_qty').value) || 0;
            satuanInput = "Lembar";
            spesifikasi = `Merek: ${merk} | Kode: ${kode}`;
        }
        else if (tipeKalkulasi === 'luas_veneer') {
            const jenis = document.getElementById('v_jenis').value;
            const bendel = document.getElementById('v_bendel').value;
            const tebal = document.getElementById('v_tebal').value;
            const jumlahLembar = document.getElementById('v_qty').value;
            kuantitas = hitungLuasVeneer();
            satuanInput = "M2";
            spesifikasi = `Jenis: ${jenis} | No. Bendel: ${bendel} | Tebal: ${tebal}mm | Jumlah: ${jumlahLembar} Lembar`;
        }
        else {
            alert('Item ini belum punya tipe_kalkulasi yang dikenali sistem.');
            return false;
        }

        if (!kuantitas || kuantitas <= 0) {
            alert('Kuantitas harus diisi dan lebih dari 0!');
            return false;
        }

        document.getElementById('inputSpesifikasiPokok').value = spesifikasi;
        document.getElementById('inputSatuanInputPokok').value = satuanInput;
        document.getElementById('inputKuantitasPokok').value = kuantitas;

        const jenis = document.getElementById('jenisTransaksi').value;
        const asalAtauProyek = jenis === 'Barang Keluar'
            ? document.getElementById('namaProyek').value
            : document.getElementById('asalBarang').value;
        document.getElementById('inputAsalProyekPokok').value = asalAtauProyek;

        return true;
    }

    function editBarisLog(btn) {
        alert('Fitur edit dialihkan ke pengeditan data master via database.');
        // Jika ingin mengembalikan data ke form input atas untuk re-submit, 
        // logika parsing string spesifikasi ke masing-masing input field bisa ditaruh di sini.
    }
</script>