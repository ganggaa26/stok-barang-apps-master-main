@extends('layouts.admin')

@section('content')
<script src="https://cdn.tailwindcss.com"></script>

<div class="max-w-7xl mx-auto space-y-6 p-4 md:p-6 font-sans antialiased text-slate-800">

    {{-- BREADCRUMB & HEADER --}}
    <div class="space-y-1">
        <div class="text-xs text-slate-400 font-medium flex items-center gap-1.5">
            <span>Manajemen Material</span>
            <span class="text-slate-300">/</span>
            <span class="text-slate-500">Material Pembantu</span>
        </div>
        <h1 class="text-xl font-bold text-slate-900 tracking-tight">Modul Inventaris: Bahan Pembantu & Consumables</h1>
        <p class="text-sm text-slate-500">Pengelolaan logistik perekat, pengikat, cairan finishing, dan amplas multi-satuan</p>
    </div>

    {{-- ALERT SUCCESS --}}
    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm rounded-xl p-4 shadow-sm">
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('material.pembantu.store') }}" method="POST" id="formBahanPembantu" class="space-y-6">
        @csrf
        <input type="hidden" name="_method" id="methodOverride" value="POST">

        {{-- Hidden inputs untuk backend --}}
        <input type="hidden" name="merk" id="inputMerk">
        <input type="hidden" name="spesifikasi" id="inputSpesifikasi">
        <input type="hidden" name="satuan_input" id="inputSatuanInput">
        <input type="hidden" name="kuantitas" id="inputKuantitas">
        <input type="hidden" name="asal_atau_proyek" id="inputAsalAtauProyek">

        {{-- CARD 1: SELEKSI ITEM (BAGIAN ATAS) --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Sub-Kategori Bahan Pembantu</label>
                    <select id="subKategori" name="kategori_id" class="w-full bg-white border border-slate-200 rounded-xl p-3 text-sm text-slate-700 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 focus:outline-none transition appearance-none pr-10" style="background-image: url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%23708090%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22%2F%3E%3C%2Fsvg%3E'); background-repeat: no-repeat; background-position: right 1rem center; background-size: 0.65em auto;" onchange="updateItemDropdown()" required>
                        <option value="">-- Pilih Sub-Kategori --</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->nama_Kategori }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Item Barang</label>
                    <select name="material_pembantu_id" id="itemBarang" class="w-full bg-slate-50 border border-slate-200 rounded-xl p-3 text-sm text-slate-400 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 focus:outline-none transition appearance-none pr-10" style="background-image: url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%23A0AEC0%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22%2F%3E%3C%2Fsvg%3E'); background-repeat: no-repeat; background-position: right 1rem center; background-size: 0.65em auto;" onchange="renderSpesifikasiForm()" disabled required>
                        <option value="">-- Pilih Item --</option>
                    </select>
                </div>
            </div>
        </div>

        {{-- SPESIFIKASI ATRIBUT TEKNIS DINAMIS --}}
        <div id="wrapperSpesifikasi" class="hidden bg-white border border-slate-100 rounded-2xl p-6 space-y-4 shadow-sm">
            <div class="text-xs font-bold text-slate-800 uppercase tracking-wider flex items-center gap-1.5 border-b border-slate-100 pb-3">
                📋 Atribut Teknis Material Pembantu
            </div>
            <div id="areaFormDinamis"></div>
        </div>

        {{-- CARD 2: STATUS LOGISTIK & MUTASI --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 space-y-6">
            <div class="text-xs font-bold text-slate-800 uppercase tracking-wider flex items-center gap-2">
                🚚 STATUS LOGISTIK & PELACAKAN PROYEK
            </div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-start">
                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Jenis Transaksi</label>
                    <select name="jenis_transaksi" id="jenisTransaksi" class="w-full bg-white border border-slate-200 rounded-xl p-3 text-sm text-slate-700 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 focus:outline-none transition appearance-none pr-10" style="background-image: url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%23708090%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22%2F%3E%3C%2Fsvg%3E'); background-repeat: no-repeat; background-position: right 1rem center; background-size: 0.65em auto;" onchange="aturFormLogistik()">
                        <option value="Barang Keluar">Barang Keluar (-)</option>
                        <option value="Barang Masuk">Barang Masuk (+)</option>
                        <option value="Stok Awal">Stok Awal Gudang</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Tanggal</label>
                    <input type="date" name="tanggal" id="tglTransaksi" class="w-full bg-white border border-slate-200 rounded-xl p-3 text-sm text-slate-700 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 focus:outline-none transition" required>
                </div>

                {{-- DUA KOLOM DINAMIS (Bisa berupa Asal Barang atau Nama Proyek) --}}
                <div id="boxAsal" class="hidden md:col-span-2 grid grid-cols-1 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Asal / Supplier</label>
                        <input type="text" id="asalBarang" placeholder="Contoh: Toko Kimia / Sisa Project" class="w-full bg-white border border-slate-200 rounded-xl p-3 text-sm text-slate-700 placeholder-slate-300 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 focus:outline-none transition">
                    </div>
                </div>

                <div id="boxProyek" class="contents">
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Nama Proyek</label>
                        <input type="text" id="namaProyek" placeholder="Contoh: Resto Namora (Kosongkan jika umum)" class="w-full bg-white border border-slate-200 rounded-xl p-3 text-sm text-slate-700 placeholder-slate-300 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 focus:outline-none transition">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Nama Produk Jadi</label>
                        <input type="text" id="namaProduk" placeholder="Contoh: Kursi Makan (Kosongkan jika umum)" class="w-full bg-white border border-slate-200 rounded-xl p-3 text-sm text-slate-700 placeholder-slate-300 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 focus:outline-none transition">
                    </div>
                </div>
            </div>

            {{-- TARGET JUMLAH UNIT --}}
            <div id="boxTargetUnit" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Target Jumlah Unit</label>
                    <div class="relative flex items-center">
                        <input type="number" id="targetUnit" value="1" min="1" class="w-full bg-white border border-slate-200 rounded-xl p-3 text-sm text-slate-700 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 focus:outline-none transition pr-12">
                        <span class="absolute right-4 text-xs font-medium text-slate-400 pointer-events-none">Unit</span>
                    </div>
                </div>
            </div>

            {{-- TOMBOL ACTION DI POJOK KANAN BAWAH --}}
            <div class="flex justify-end pt-4 border-t border-slate-100">
                <button type="submit" onclick="return siapkanSubmit()" class="w-full md:w-auto bg-[#5a43ee] hover:bg-[#4932d1] text-white font-medium text-sm py-3 px-6 rounded-xl shadow-sm transition flex items-center justify-center gap-2">
                    <span>💾</span> <span id="textTombol">Amankan Data Stok Pembantu</span>
                </button>
            </div>
        </div>
    </form>

    {{-- LOG TABEL MUTASI --}}
<div class="bg-white rounded-xl border-2 border-slate-400 shadow-md p-5 space-y-4">
    <div class="border-b-2 border-slate-400 pb-3 bg-white flex items-center justify-between">
        <h2 class="text-xs font-bold text-slate-800 uppercase tracking-wider flex items-center gap-2">
            📋 JURNAL RIWAYAT TRANSAKSI MATERIAL PEMBANTU
        </h2>
    </div>

    <div class="overflow-x-auto rounded-lg border-2 border-slate-400 bg-white">
        <table class="w-full text-sm text-left border-collapse border-2 border-slate-400">
            <thead>
                <tr class="bg-slate-100 text-slate-700 text-xs font-bold uppercase tracking-wider border-b-2 border-slate-400">
                    <th class="p-3 font-bold border-r-2 border-slate-400 text-center">Tanggal</th>
                    <th class="p-3 font-bold border-r-2 border-slate-400">Item Material</th>
                    <th class="p-3 font-bold border-r-2 border-slate-400 text-center">Status</th>
                    <th class="p-3 font-bold border-r-2 border-slate-400">Spesifikasi Detail</th>
                    <th class="p-3 font-bold border-r-2 border-slate-400 text-right">Kuantitas</th>
                    <th class="p-3 font-bold border-r-2 border-slate-400">Alokasi / Informasi</th>
                    <th class="p-3 font-bold text-center">Aksi</th>
                </tr>
            </thead>
            <tbody id="badanTabelLog" class="divide-y-2 divide-slate-400 bg-white">
                @forelse($mutasiks as $mutasi)
                    @php
                        $badgeWarna = match($mutasi->jenis_transaksi) {
                            'Barang Masuk' => 'bg-emerald-50 text-emerald-700 border-emerald-300',
                            'Barang Keluar' => 'bg-rose-50 text-rose-700 border-rose-300',
                            default => 'bg-indigo-50 text-indigo-700 border-indigo-300'
                        };
                    @endphp
                    <tr class="hover:bg-slate-50 text-slate-800 transition baris-log-pembantu text-sm">
                        
                        <td class="p-3 text-xs text-slate-700 font-medium text-center border-r-2 border-slate-400 whitespace-nowrap">
                            {{ $mutasi->tanggal }}
                        </td>
                        
                        <td>{{ $mutasi->masterMaterialPembantu->nama_material }}</td>
                        
                        <td class="p-3 text-center border-r-2 border-slate-400 whitespace-nowrap">
                            <span class="px-2.5 py-0.5 text-xs font-bold rounded border {{ $badgeWarna }}">{{ $mutasi->jenis_transaksi }}</span>
                        </td>
                        
                       <td>{{ $mutasi->spesifikasi }}</td>
                        
                        <td class="p-3 text-right font-mono font-black text-slate-950 border-r-2 border-slate-400 whitespace-nowrap bg-slate-50/30">
                            {{ $mutasi->kuantitas }} {{ $mutasi->satuan_input }}
                        </td>
                        
                        <td class="p-3 text-xs text-slate-700 border-r-2 border-slate-400">
                            {{ $mutasi->asal_atau_proyek }}
                        </td>
                        
                                            <td class="p-3 text-center whitespace-nowrap">
                        <div class="flex items-center justify-center gap-1.5">
                            <button type="button" 
                                    onclick="editMutasiPembantu({
                                        id: {{ $mutasi->id }},
                                        material_pembantu_id: {{ $mutasi->material_pembantu_id }},
                                        jenis_transaksi: '{{ $mutasi->jenis_transaksi }}',
                                        tanggal: '{{ $mutasi->tanggal }}',
                                        kuantitas: {{ $mutasi->kuantitas }},
                                        spesifikasi: '{{ $mutasi->spesifikasi ?? '' }}',
                                        merk: '{{ $mutasi->merk ?? '' }}',
                                        jenis_kimia: '{{ $mutasi->jenis_kimia ?? '' }}',
                                        grit: '{{ $mutasi->grit ?? '' }}',
                                        satuan_input: '{{ $mutasi->satuan_input ?? '' }}',
                                        asal_atau_proyek: '{{ $mutasi->asal_atau_proyek ?? '' }}'
                                    })" 
                                    class="bg-amber-50 text-amber-700 hover:bg-amber-100 border border-amber-300 p-1.5 rounded transition-colors text-xs font-semibold flex items-center gap-0.5 shadow-sm">
                                ✏️ <span>Edit</span>
                            </button>
                            <form action="{{ route('material.pembantu.destroy', $mutasi->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus log data ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="bg-rose-50 text-rose-700 hover:bg-rose-100 border border-rose-300 p-1.5 rounded transition-colors text-xs font-semibold flex items-center gap-0.5 shadow-sm">
                                    🗑️ <span>Hapus</span>
                                </button>
                            </form>
                        </div>
                    </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="p-8 text-center text-slate-400 font-medium border border-slate-400 bg-slate-50">
                            Belum ada riwayat transaksi bahan pembantu.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- JAVASCRIPT LOGIC --}}
<script>
    document.getElementById('tglTransaksi').valueAsDate = new Date();

    const dataBahanPembantu = @json($categories->mapWithKeys(function ($cat) {
        return [$cat->id => $cat->materialPembantus->map(function ($item) {
            return ['id' => $item->id, 'nama' => $item->nama_material];
        })];
    }));

    const inputClass = "w-full border border-slate-200 p-3 text-sm rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 focus:outline-none bg-white transition";

    function updateItemDropdown() {
        const sub = document.getElementById('subKategori').value;
        const itemSelect = document.getElementById('itemBarang');

        itemSelect.innerHTML = '<option value="">-- Pilih Item --</option>';
        document.getElementById('wrapperSpesifikasi').classList.add('hidden');

        if (!sub) {
            itemSelect.disabled = true;
            itemSelect.className = "w-full bg-slate-50 border border-slate-200 rounded-xl p-3 text-sm text-slate-400 appearance-none pr-10";
            return;
        }

        itemSelect.disabled = false;
        itemSelect.className = "w-full bg-white border border-slate-200 rounded-xl p-3 text-sm text-slate-700 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 focus:outline-none transition appearance-none pr-10";

        (dataBahanPembantu[sub] || []).forEach(item => {
            itemSelect.innerHTML += `<option value="${item.id}" data-nama="${item.nama}">${item.nama}</option>`;
        });
    }

    function aturFormLogistik() {
        const jenis = document.getElementById('jenisTransaksi').value;
        const boxAsal = document.getElementById('boxAsal');
        const boxProyek = document.getElementById('boxProyek');
        const boxTargetUnit = document.getElementById('boxTargetUnit');

        if (jenis === 'Barang Keluar') {
            boxAsal.classList.add('hidden');
            boxProyek.classList.remove('hidden');
            boxTargetUnit.classList.remove('hidden');
        } else {
            boxAsal.classList.remove('hidden');
            boxProyek.classList.add('hidden');
            boxTargetUnit.classList.add('hidden');
        }
    }

    function deteksiJenisItem(namaMaterial) {
        const nama = namaMaterial.toLowerCase();
        if (nama.includes('lem')) return 'lem';
        if (nama.includes('sekrup')) return 'sekrup';
        if (nama.includes('cat') || nama.includes('thinner') || nama.includes('h2o2') || nama.includes('cairan')) return 'cairan';
        if (nama.includes('amplas')) return 'amplas';
        return null;
    }

    function renderSpesifikasiForm() {
        const itemSelect = document.getElementById('itemBarang');
        const wrapper = document.getElementById('wrapperSpesifikasi');
        const areaForm = document.getElementById('areaFormDinamis');

        if (!itemSelect.value) { wrapper.classList.add('hidden'); return; }

        const namaMaterial = itemSelect.options[itemSelect.selectedIndex].getAttribute('data-nama');
        const jenis = deteksiJenisItem(namaMaterial);

        wrapper.classList.remove('hidden');
        areaForm.innerHTML = "";

        if (jenis === 'lem') {
            areaForm.className = "grid grid-cols-1 md:grid-cols-3 gap-4 text-sm";
            areaForm.innerHTML = `
                <div><label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Merek & Nama Lem</label>
                    <select id="p_nama" class="${inputClass}"><option>Lem Alteco</option><option>Lem Johan</option><option>Lem Rajawali</option></select>
                </div>
                <div><label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Satuan Penggunaan</label>
                    <select id="p_satuan" class="${inputClass}"><option>Kilo</option><option>Liter</option><option>Pcs</option></select>
                </div>
                <div><label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Quantity</label><input type="number" id="p_qty" value="1" min="0" step="0.01" class="${inputClass}" required></div>
            `;
        }
        else if (jenis === 'sekrup') {
            areaForm.className = "grid grid-cols-1 md:grid-cols-4 gap-4 text-sm";
            areaForm.innerHTML = `
                <div><label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Merek</label><input type="text" id="s_merk" placeholder="Contoh: Moon Lion" class="${inputClass}" required></div>
                <div><label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Ukuran Sekrup</label><input type="text" id="s_ukuran" placeholder="Contoh: 5/8 , 12x2" class="${inputClass}" required></div>
                <div><label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Satuan</label>
                    <select id="s_satuan" class="${inputClass}"><option>Pcs</option><option>Kotak</option></select>
                </div>
                <div><label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Quantity</label><input type="number" id="s_qty" value="1" min="0" step="0.01" class="${inputClass}" required></div>
            `;
        }
        else if (jenis === 'cairan') {
            areaForm.className = "grid grid-cols-1 md:grid-cols-3 gap-4 text-sm";
            areaForm.innerHTML = `
                <div><label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Merek</label><input type="text" id="c_merk" placeholder="Contoh: Impra / Propan" class="${inputClass}" required></div>
                <div><label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Jenis Lapisan/Kimia</label><input type="text" id="c_jenis" placeholder="Contoh: NC Clear, PU" class="${inputClass}" required></div>
                <div><label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Volume (Liter)</label><input type="number" id="c_qty" value="1" min="0" step="0.01" class="${inputClass}" required></div>
            `;
        }
        else if (jenis === 'amplas') {
            areaForm.className = "grid grid-cols-1 md:grid-cols-4 gap-4 text-sm items-end";
            areaForm.innerHTML = `
                <div><label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Merek & Jenis</label><input type="text" id="a_merk" placeholder="Contoh: Taiyo" class="${inputClass}" required></div>
                <div><label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Grit Kekasaran</label>
                    <select id="a_grit" class="${inputClass}"><option>120</option><option>240</option><option>400</option></select>
                </div>
                <div><label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Satuan</label>
                    <select id="a_satuan" class="${inputClass}" onchange="hitungKonversiAmplas()"><option value="Roll">Roll (50 M)</option><option value="Pcs">Pcs</option></select>
                </div>
                <div><label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Jumlah</label><input type="number" id="a_qty" value="1" min="0" step="0.01" class="${inputClass}" oninput="hitungKonversiAmplas()" required></div>
                <div class="md:col-span-4 bg-slate-50 text-slate-500 p-3 rounded-xl border border-slate-100 font-mono text-[11px] text-center" id="calcPreviewAmplas">
                    Kalkulasi konversi otomatis gudang.
                </div>
            `;
            hitungKonversiAmplas();
        }
        else {
            areaForm.className = "grid grid-cols-1 md:grid-cols-3 gap-4 text-sm";
            areaForm.innerHTML = `
                <div><label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Merek / Deskripsi</label><input type="text" id="x_merk" placeholder="General" class="${inputClass}"></div>
                <div><label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Satuan</label><input type="text" id="x_satuan" placeholder="Pcs, Pack" class="${inputClass}"></div>
                <div><label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Quantity</label><input type="number" id="x_qty" value="1" min="0" step="0.01" class="${inputClass}" required></div>
            `;
        }
    }

    function hitungKonversiAmplas() {
        const sat = document.getElementById('a_satuan').value;
        const val = parseFloat(document.getElementById('a_qty').value) || 0;
        const preview = document.getElementById('calcPreviewAmplas');

        if (sat === 'Roll') {
            const totalMeter = val * 50;
            preview.innerText = `Matriks Konversi: Input ${val} Roll = Otomatis dihitung ${totalMeter} Meter Stok Induk.`;
            return totalMeter;
        } else {
            preview.innerText = `Matriks Konversi: Dicatat langsung sebesar ${val} Pcs.`;
            return val;
        }
    }

    function siapkanSubmit() {
        const itemSelect = document.getElementById('itemBarang');
        if (!itemSelect.value) { alert('Silakan tentukan item material pembantu!'); return false; }

        const namaMaterial = itemSelect.options[itemSelect.selectedIndex].getAttribute('data-nama');
        const jenis = deteksiJenisItem(namaMaterial);

        let merk = "", spesifikasi = "", satuan = "", kuantitas = 0;

        if (jenis === 'lem') {
            merk = document.getElementById('p_nama').value;
            spesifikasi = "Bahan Perekat Produksi";
            satuan = document.getElementById('p_satuan').value;
            kuantitas = document.getElementById('p_qty').value;
        } 
        else if (jenis === 'sekrup') {
            merk = document.getElementById('s_merk').value;
            spesifikasi = "Ukuran: " + document.getElementById('s_ukuran').value;
            satuan = document.getElementById('s_satuan').value;
            kuantitas = document.getElementById('s_qty').value;
        }
        else if (jenis === 'cairan') {
            merk = document.getElementById('c_merk').value;
            spesifikasi = "Jenis Kimia: " + document.getElementById('c_jenis').value;
            satuan = "Liter";
            kuantitas = document.getElementById('c_qty').value;
        }
        else if (jenis === 'amplas') {
            merk = document.getElementById('a_merk').value;
            spesifikasi = "Grit: " + document.getElementById('a_grit').value;
            satuan = document.getElementById('a_satuan').value;
            if (satuan === 'Roll') {
                kuantitas = parseFloat(document.getElementById('a_qty').value) * 50; 
                satuan = "Meter"; 
            } else {
                kuantitas = document.getElementById('a_qty').value;
            }
        }
        else {
            merk = document.getElementById('x_merk').value || "General Merek";
            spesifikasi = "Bahan Pembantu Umum";
            satuan = document.getElementById('x_satuan').value || "Pcs";
            kuantitas = document.getElementById('x_qty').value;
        }

        const jenisTransaksi = document.getElementById('jenisTransaksi').value;
        let asalAtauProyekVal = "";

        if (jenisTransaksi === 'Barang Keluar') {
            const pProyek = document.getElementById('namaProyek').value.trim();
            const pProduk = document.getElementById('namaProduk').value.trim();
            const tUnit = document.getElementById('targetUnit').value || "1";
            
            // Penggabungan string opsional jika input kosong
            let infoAlokasi = [];
            if (pProyek) infoAlokasi.push(`Proyek: ${pProyek}`);
            if (pProduk) infoAlokasi.push(`Produk: ${pProduk}`);
            
            if (infoAlokasi.length > 0) {
                infoAlokasi.push(`(${tUnit} Unit)`);
                asalAtauProyekVal = infoAlokasi.join(' | ');
            } else {
                asalAtauProyekVal = `Penggunaan Umum / General (${tUnit} Unit)`;
            }
        } else {
            asalAtauProyekVal = document.getElementById('asalBarang').value || "Gudang Utama";
        }

        document.getElementById('inputMerk').value = merk;
        document.getElementById('inputSpesifikasi').value = spesifikasi;
        document.getElementById('inputSatuanInput').value = satuan;
        document.getElementById('inputKuantitas').value = kuantitas;
        document.getElementById('inputAsalAtauProyek').value = asalAtauProyekVal;

        return true;
    }
    function editMutasiPembantu(data) {
    // 1. Ubah action form modal edit agar mengarah ke route update pembantu
    const formEdit = document.getElementById('form-edit-mutasi'); // Sesuaikan ID form modal editmu
    formEdit.action = `/material/pembantu/update/${data.id}`;

    // 2. Set isi tiap input dropdown dan text di dalam modal edit
    document.getElementById('edit_material_pembantu_id').value = data.material_pembantu_id;
    document.getElementById('edit_jenis_transaksi').value = data.jenis_transaksi;
    document.getElementById('edit_tanggal').value = data.tanggal;
    document.getElementById('edit_kuantitas').value = data.kuantitas;
    document.getElementById('edit_spesifikasi').value = data.spesifikasi;
    document.getElementById('edit_merk').value = data.merk;
    
    // Jika ada input spesifik thinner atau amplas di modal edit:
    if(document.getElementById('edit_jenis_kimia')) {
        document.getElementById('edit_jenis_kimia').value = data.jenis_kimia;
    }
    if(document.getElementById('edit_grit')) {
        document.getElementById('edit_grit').value = data.grit;
    }

    // 3. Tampilkan modal editnya
    // (Panggil fungsi pembuka modal bawaan template kamu, misal: $('#modal-edit').modal('show') atau sejenisnya)
}

    // Inisialisasi awal saat halaman dimuat
    aturFormLogistik();
</script>
@endsection