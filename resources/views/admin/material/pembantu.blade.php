@extends('layouts.admin')

@section('content')
<script src="https://cdn.tailwindcss.com"></script>

<div class="max-w-7xl mx-auto space-y-6 p-4 md:p-6 font-sans antialiased text-slate-800">

    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div class="flex items-center space-x-4">
            <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center text-xl border border-blue-100 shadow-sm">
                🛠️
            </div>
            <div>
                <h1 class="text-xl font-bold text-slate-900 tracking-tight">Modul Inventaris: Bahan Pembantu & Consumables</h1>
                <p class="text-sm text-slate-500 mt-0.5">Pengelolaan logistik perekat, pengikat, cairan finishing, dan amplas multi-satuan</p>
            </div>
        </div>
        <div class="flex items-center space-x-2 self-start md:self-center">
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-50 text-blue-700 border border-blue-200">
                <span class="w-1.5 h-1.5 rounded-full bg-blue-500 mr-1.5 animate-pulse"></span>
                Logistik Aktif
            </span>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm rounded-xl p-4">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="border-b border-slate-100 px-5 py-4 bg-slate-50/50">
            <h2 class="text-sm font-bold text-slate-800 uppercase tracking-wider flex items-center gap-2">
                📥 Form Input Mutasi
            </h2>
        </div>

        <form action="{{ route('material.pembantu.store') }}" method="POST" id="formBahanPembantu" class="p-6 space-y-6">
            @csrf

            {{-- Hidden inputs: diisi otomatis oleh JS hasil kalkulasi sebelum submit --}}
            <input type="hidden" name="merk" id="inputMerk">
            <input type="hidden" name="spesifikasi" id="inputSpesifikasi">
            <input type="hidden" name="satuan_input" id="inputSatuanInput">
            <input type="hidden" name="kuantitas" id="inputKuantitas">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1.5">Sub-Kategori Bahan Pembantu</label>
                    <select id="subKategori" class="w-full bg-white border border-slate-200 rounded-xl p-3 text-sm text-slate-800 focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600 focus:outline-none transition" onchange="updateItemDropdown()">
                        <option value="">-- Pilih Sub-Kategori --</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->nama_Kategori }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1.5">Item Barang</label>
                    <select name="item_material" id="itemBarang" class="w-full bg-slate-50 border border-slate-200 rounded-xl p-3 text-sm text-slate-400 focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600 focus:outline-none transition" onchange="renderSpesifikasiForm()" disabled>
                        <option value="">-- Pilih Item --</option>
                    </select>
                </div>
            </div>

            <div id="wrapperSpesifikasi" class="hidden bg-slate-50/80 border border-slate-100 rounded-2xl p-5 space-y-3 shadow-inner">
                <div class="text-xs font-bold text-slate-700 uppercase tracking-wider flex items-center gap-1.5 border-b border-slate-200/60 pb-2">
                    📋 Atribut Teknis Material
                </div>
                <div id="areaFormDinamis" class="text-sm"></div>
            </div>

            <div class="border-t border-slate-100 pt-5 grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1.5">Jenis Transaksi</label>
                    <select name="jenis_transaksi" id="jenisTransaksi" class="w-full bg-white border border-slate-200 rounded-xl p-3 text-sm text-slate-800 focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600 focus:outline-none transition" onchange="aturFormLogistik()">
                        <option value="Stok Awal">Stok Awal Gudang</option>
                        <option value="Barang Masuk">Barang Masuk (+)</option>
                        <option value="Barang Keluar">Barang Keluar (-)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1.5">Tanggal</label>
                    <input type="date" name="tanggal" id="tglTransaksi" class="w-full bg-white border border-slate-200 rounded-xl p-3 text-sm text-slate-800 focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600 focus:outline-none transition">
                </div>

                <div id="boxAsal">
                    <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1.5">Asal / Supplier</label>
                    <input type="text" id="asalBarang" placeholder="Contoh: Toko Kimia / Sisa Project" class="w-full bg-white border border-slate-200 rounded-xl p-3 text-sm text-slate-800 placeholder-slate-400 focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600 focus:outline-none transition">
                </div>

                <div id="boxProyek" class="hidden">
                    <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1.5">Nama Proyek & Target Produk</label>
                    <input type="text" id="namaProyek" placeholder="Contoh: Project Meja Cafe / Wardrobe" class="w-full bg-white border border-slate-200 rounded-xl p-3 text-sm text-slate-800 placeholder-slate-400 focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600 focus:outline-none transition">
                </div>
            </div>

            <div class="flex justify-end pt-2">
                <button type="submit" onclick="return siapkanSubmit()" class="w-full md:w-auto bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm py-3 px-6 rounded-xl shadow-sm hover:shadow transition flex items-center justify-center gap-2">
                    <span>💾</span> Update Stok Bahan
                </button>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="border-b border-slate-100 px-5 py-4 bg-slate-50/50 flex items-center justify-between">
            <h2 class="text-sm font-bold text-slate-800 uppercase tracking-wider flex items-center gap-2">
                📊 Log Mutasi Transaksi
            </h2>
            <span class="text-xs text-slate-400 font-mono">Real-time update</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left border-collapse">
                <thead>
                    <tr class="bg-slate-900 text-slate-200 text-xs font-semibold uppercase tracking-wider border-b border-slate-800">
                        <th class="p-4 font-medium">Tanggal</th>
                        <th class="p-4 font-medium">Item Material</th>
                        <th class="p-4 font-medium text-center">Status</th>
                        <th class="p-4 font-medium">Spesifikasi Detail</th>
                        <th class="p-4 font-medium text-right">Kuantitas</th>
                        <th class="p-4 font-medium">Alokasi / Informasi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse($mutasiks as $log)
                        <tr class="hover:bg-slate-50/80 text-slate-700 transition border-b border-slate-100">
                            <td class="p-4 text-xs text-slate-500 font-mono">{{ \Carbon\Carbon::parse($log->tanggal)->format('d-m-Y') }}</td>
                            <td class="p-4 font-semibold text-slate-900">{{ $log->materialPembantu->nama_material ?? '-' }}</td>
                            <td class="p-4 text-center">
                                @php
                                    $badgeWarna = match($log->jenis_transaksi) {
                                        'Barang Masuk' => 'bg-emerald-50 text-emerald-700 border-emerald-200/80',
                                        'Barang Keluar' => 'bg-rose-50 text-rose-700 border-rose-200/80',
                                        'Stok Awal' => 'bg-blue-50 text-blue-700 border-blue-200/80',
                                        default => 'bg-slate-100 text-slate-700 border-slate-200',
                                    };
                                @endphp
                                <span class="px-2.5 py-1 text-[11px] font-bold rounded-lg border {{ $badgeWarna }}">{{ $log->jenis_transaksi }}</span>
                            </td>
                            <td class="p-4 text-xs text-slate-600">{{ $log->keterangan }}</td>
                            <td class="p-4 text-right font-mono font-bold text-slate-900">{{ $log->kuantitas }} {{ $log->materialPembantu->satuan ?? '' }}</td>
                            <td class="p-4 text-xs">
                                @if($log->jenis_transaksi === 'Barang Keluar')
                                    <span class="flex items-center gap-1 text-rose-600 font-medium">🔴 Keluar Ke: <b class="text-slate-800">{{ $log->asal_atau_proyek }}</b></span>
                                @else
                                    <span class="flex items-center gap-1 text-emerald-600 font-medium">🟢 Masuk Dari: <b class="text-slate-800">{{ $log->asal_atau_proyek }}</b></span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-8 text-center text-slate-400 italic bg-slate-50/30">
                                Belum ada pemakaian bahan pembantu yang diinput.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

<script>
    document.getElementById('tglTransaksi').valueAsDate = new Date();

    // Data dari database: { kategori_id: [ { id, nama }, ... ] }
    const dataBahanPembantu = @json($categories->mapWithKeys(function ($cat) {
        return [$cat->id => $cat->materialPembantus->map(function ($item) {
            return ['id' => $item->id, 'nama' => $item->nama_material];
        })];
    }));

    const inputClass = "w-full border border-slate-200 p-3 text-sm rounded-xl focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600 focus:outline-none bg-white transition";

    function updateItemDropdown() {
        const sub = document.getElementById('subKategori').value;
        const itemSelect = document.getElementById('itemBarang');

        itemSelect.innerHTML = '<option value="">-- Pilih Item --</option>';
        document.getElementById('wrapperSpesifikasi').classList.add('hidden');

        if (!sub) {
            itemSelect.disabled = true;
            itemSelect.className = "w-full bg-slate-50 border border-slate-200 rounded-xl p-3 text-sm text-slate-400";
            return;
        }

        itemSelect.disabled = false;
        itemSelect.className = "w-full bg-white border border-slate-200 rounded-xl p-3 text-sm text-slate-800 focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600 focus:outline-none transition";

        (dataBahanPembantu[sub] || []).forEach(item => {
            itemSelect.innerHTML += `<option value="${item.id}" data-nama="${item.nama}">${item.nama}</option>`;
        });
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

    /**
     * Deteksi jenis kalkulasi berdasarkan nama_material dari database.
     * Pola ini meneruskan ide "sub.includes('amplas')" dari versi sebelumnya,
     * tapi diperluas untuk semua jenis item bahan pembantu.
     */
    function deteksiJenisItem(namaMaterial) {
        const nama = namaMaterial.toLowerCase();
        if (nama.includes('lem')) return 'lem';
        if (nama.includes('sekrup')) return 'sekrup';
        if (nama.includes('cat') || nama.includes('thinner') || nama.includes('h2o2')) return 'cairan';
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
                <div><label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1.5">Merek & Nama Lem</label>
                    <select id="p_nama" class="${inputClass}">
                        <option>Lem Alteco</option><option>Lem Johan</option><option>Lem Rajawali</option>
                    </select>
                </div>
                <div><label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1.5">Satuan Penggunaan</label>
                    <select id="p_satuan" class="${inputClass}">
                        <option>Kilo</option><option>Liter</option><option>Pcs</option>
                    </select>
                </div>
                <div><label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1.5">Quantity</label><input type="number" id="p_qty" value="1" min="0" step="0.01" class="${inputClass}" required></div>
            `;
        }
        else if (jenis === 'sekrup') {
            areaForm.className = "grid grid-cols-1 md:grid-cols-4 gap-4 text-sm";
            areaForm.innerHTML = `
                <div><label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1.5">Merek</label><input type="text" id="s_merk" placeholder="Contoh: Moon Lion" class="${inputClass}" required></div>
                <div><label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1.5">Ukuran Sekrup</label><input type="text" id="s_ukuran" placeholder="Contoh: 5/8 , 12x2" class="${inputClass}" required></div>
                <div><label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1.5">Satuan</label>
                    <select id="s_satuan" class="${inputClass}">
                        <option>Pcs</option><option>Kotak</option>
                    </select>
                </div>
                <div><label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1.5">Quantity</label><input type="number" id="s_qty" value="1" min="0" step="0.01" class="${inputClass}" required></div>
            `;
        }
        else if (jenis === 'cairan') {
            areaForm.className = "grid grid-cols-1 md:grid-cols-3 gap-4 text-sm";
            areaForm.innerHTML = `
                <div><label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1.5">Merek</label><input type="text" id="c_merk" placeholder="Contoh: Impra / Propan" class="${inputClass}" required></div>
                <div><label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1.5">Jenis Lapisan/Kimia</label><input type="text" id="c_jenis" placeholder="Contoh: NC Clear, PU" class="${inputClass}" required></div>
                <div><label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1.5">Volume (Liter)</label><input type="number" id="c_qty" value="1" min="0" step="0.01" class="${inputClass}" required></div>
            `;
        }
        else if (jenis === 'amplas') {
            areaForm.className = "grid grid-cols-1 md:grid-cols-4 gap-4 text-sm items-end";
            areaForm.innerHTML = `
                <div><label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1.5">Merek & Jenis</label><input type="text" id="a_merk" placeholder="Contoh: Taiyo / Ekamant" class="${inputClass}" required></div>
                <div><label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1.5">Grit Kekasaran</label>
                    <select id="a_grit" class="${inputClass}">
                        <option>60</option><option>80</option><option>100</option><option>120</option>
                    </select>
                </div>
                <div><label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1.5">Satuan Input Utama</label>
                    <select id="a_satuan" class="${inputClass}" onchange="hitungKonversiAmplas()">
                        <option value="Roll">1 Roll (50 M)</option><option value="Lembaran">Lembaran</option><option value="Pcs">Pcs</option>
                    </select>
                </div>
                <div><label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1.5">Jumlah</label><input type="number" id="a_qty" value="1" min="0" step="0.01" class="${inputClass}" oninput="hitungKonversiAmplas()" required></div>
                <div class="md:col-span-4 bg-blue-50 text-blue-900 p-3 rounded-xl border border-blue-100 font-mono text-[11px] font-semibold text-center leading-relaxed shadow-sm mt-1" id="calcPreviewAmplas">
                    Matriks Gudang: Setara dengan 50 Meter Stok Induk.
                </div>
            `;
            hitungKonversiAmplas();
        }
        else {
            // Item tanpa kalkulasi khusus: hanya kuantitas biasa
            areaForm.className = "grid grid-cols-1 md:grid-cols-2 gap-4 text-sm";
            areaForm.innerHTML = `
                <div><label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1.5">Satuan</label>
                    <input type="text" id="x_satuan" placeholder="Contoh: Pcs, Liter, Kg" class="${inputClass}">
                </div>
                <div><label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1.5">Quantity</label>
                    <input type="number" id="x_qty" value="1" min="0" step="0.01" class="${inputClass}" required>
                </div>
            `;
        }
    }

    function hitungKonversiAmplas() {
        const sat = document.getElementById('a_satuan').value;
        const val = parseFloat(document.getElementById('a_qty').value) || 0;
        const preview = document.getElementById('calcPreviewAmplas');

        if (sat === 'Roll') {
            const totalMeter = val * 50;
            preview.innerText = `Matriks Gudang: Input ${val} Roll = Otomatis kalkulasi ${totalMeter} Meter Stok Induk.`;
        } else {
            preview.innerText = `Matriks Gudang: Dicatat flat sesuai nominal unit: ${val} ${sat}.`;
        }
    }

    /**
     * Dipanggil saat tombol submit ditekan.
     * Mengisi hidden input (merk, spesifikasi, satuan_input, kuantitas)
     * berdasarkan jenis item yang aktif, sebelum form benar-benar dikirim
     * ke server via route Laravel (material.pembantu.store).
     */
    function siapkanSubmit() {
        const itemSelect = document.getElementById('itemBarang');
        if (!itemSelect.value) {
            alert('Silakan tentukan item material pembantu!');
            return false;
        }

        const namaMaterial = itemSelect.options[itemSelect.selectedIndex].getAttribute('data-nama');
        const jenis = deteksiJenisItem(namaMaterial);

        let merk = "", spesifikasi = "", satuanInput = "", kuantitas = "";

        if (jenis === 'lem') {
            merk = document.getElementById('p_nama').value;
            satuanInput = document.getElementById('p_satuan').value;
            kuantitas = document.getElementById('p_qty').value;
        }
        else if (jenis === 'sekrup') {
            merk = document.getElementById('s_merk').value;
            spesifikasi = document.getElementById('s_ukuran').value;
            satuanInput = document.getElementById('s_satuan').value;
            kuantitas = document.getElementById('s_qty').value;
        }
        else if (jenis === 'cairan') {
            merk = document.getElementById('c_merk').value;
            spesifikasi = document.getElementById('c_jenis').value;
            satuanInput = "Liter";
            kuantitas = document.getElementById('c_qty').value;
        }
        else if (jenis === 'amplas') {
            merk = document.getElementById('a_merk').value;
            spesifikasi = `Grit ${document.getElementById('a_grit').value}`;
            satuanInput = document.getElementById('a_satuan').value;

            const qtyInput = parseFloat(document.getElementById('a_qty').value) || 0;
            // Konversi: Roll selalu disimpan sebagai meter di kolom kuantitas,
            // satuan_input tetap menyimpan pilihan asli user ("Roll").
            kuantitas = satuanInput === 'Roll' ? (qtyInput * 50) : qtyInput;
        }
        else {
            satuanInput = document.getElementById('x_satuan')?.value || "";
            kuantitas = document.getElementById('x_qty')?.value || "";
        }

        if (!kuantitas || parseFloat(kuantitas) <= 0) {
            alert('Kuantitas harus diisi dan lebih dari 0!');
            return false;
        }

        document.getElementById('inputMerk').value = merk;
        document.getElementById('inputSpesifikasi').value = spesifikasi;
        document.getElementById('inputSatuanInput').value = satuanInput;
        document.getElementById('inputKuantitas').value = kuantitas;

        return true; // lanjutkan submit form ke server
    }
</script>
@endsection
