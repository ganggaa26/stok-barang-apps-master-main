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

    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="border-b border-slate-100 px-5 py-4 bg-slate-50/50">
            <h2 class="text-sm font-bold text-slate-800 uppercase tracking-wider flex items-center gap-2">
                📥 Form Input Mutasi
            </h2>
        </div>
        
        <form id="formBahanPembantu" class="p-6 space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1.5">Sub-Kategori Bahan Pembantu</label>
                    <select id="subKategori" class="w-full bg-white border border-slate-200 rounded-xl p-3 text-sm text-slate-800 focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600 focus:outline-none transition" onchange="updateItemDropdown()">
                        <option value="">-- Pilih Sub-Kategori --</option>
                        <option value="perekat_pengikat">Perekat & Pengikat (Adhesives & Fasteners)</option>
                        <option value="cairan_finishing">Cairan Finishing (Chemicals & Coatings)</option>
                        <option value="pendukung_finishing">Bahan Pendukung Finishing (Consumables)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1.5">Item Barang</label>
                    <select id="itemBarang" class="w-full bg-slate-50 border border-slate-200 rounded-xl p-3 text-sm text-slate-400 focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600 focus:outline-none transition" onchange="renderSpesifikasiForm()" disabled>
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
                    <select id="jenisTransaksi" class="w-full bg-white border border-slate-200 rounded-xl p-3 text-sm text-slate-800 focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600 focus:outline-none transition" onchange="aturFormLogistik()">
                        <option value="Stok Awal">Stok Awal Gudang</option>
                        <option value="Barang Masuk">Barang Masuk (+)</option>
                        <option value="Barang Keluar">Barang Keluar (-)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1.5">Tanggal</label>
                    <input type="date" id="tglTransaksi" class="w-full bg-white border border-slate-200 rounded-xl p-3 text-sm text-slate-800 focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600 focus:outline-none transition">
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
                <button type="button" onclick="simpanKeLogSistem()" class="w-full md:w-auto bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm py-3 px-6 rounded-xl shadow-sm hover:shadow transition flex items-center justify-center gap-2">
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
                <tbody id="badanTabelLog" class="divide-y divide-slate-100 bg-white">
                    <tr id="barisKosong">
                        <td colspan="6" class="p-8 text-center text-slate-400 italic bg-slate-50/30">
                            Belum ada pemakaian bahan pembantu yang diinput.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

</div>

<script>
    document.getElementById('tglTransaksi').valueAsDate = new Date();

    const dataBahanPembantu = {
        "perekat_pengikat": ["Lem", "Sekrup"],
        "cairan_finishing": ["Cat", "Thinner", "Cairan Kimia H2O2"],
        "pendukung_finishing": ["Amplas"]
    };

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
        
        dataBahanPembantu[sub].forEach(item => {
            itemSelect.innerHTML += `<option value="${item}">${item}</option>`;
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

    function renderSpesifikasiForm() {
        const item = document.getElementById('itemBarang').value;
        const wrapper = document.getElementById('wrapperSpesifikasi');
        const areaForm = document.getElementById('areaFormDinamis');

        if (!item) { wrapper.classList.add('hidden'); return; }
        wrapper.classList.remove('hidden');
        areaForm.innerHTML = "";

        // Menggunakan grid horizontal md:grid-cols-3 / md:grid-cols-4 agar form memanjang ke samping
        const inputClass = "w-full border border-slate-200 p-3 text-sm rounded-xl focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600 focus:outline-none bg-white transition";

        if (item === 'Lem') {
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
                <div><label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1.5">Quantity</label><input type="number" id="p_qty" value="1" class="${inputClass}" required></div>
            `;
        }
        else if (item === 'Sekrup') {
            areaForm.className = "grid grid-cols-1 md:grid-cols-4 gap-4 text-sm";
            areaForm.innerHTML = `
                <div><label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1.5">Merek</label><input type="text" id="s_merk" placeholder="Contoh: Moon Lion" class="${inputClass}" required></div>
                <div><label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1.5">Ukuran Sekrup</label><input type="text" id="s_ukuran" placeholder="Contoh: 5/8 , 12x2" class="${inputClass}" required></div>
                <div><label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1.5">Satuan</label>
                    <select id="s_satuan" class="${inputClass}">
                        <option>Pcs</option><option>Kotak</option>
                    </select>
                </div>
                <div><label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1.5">Quantity</label><input type="number" id="s_qty" value="1" class="${inputClass}" required></div>
            `;
        }
        else if (item === 'Cat' || item === 'Thinner' || item === 'Cairan Kimia H2O2') {
            areaForm.className = "grid grid-cols-1 md:grid-cols-3 gap-4 text-sm";
            areaForm.innerHTML = `
                <div><label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1.5">Merek</label><input type="text" id="c_merk" placeholder="Contoh: Impra / Propan" class="${inputClass}" required></div>
                <div><label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1.5">Jenis Lapisan/Kimia</label><input type="text" id="c_jenis" placeholder="Contoh: NC Clear, PU" class="${inputClass}" required></div>
                <div><label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1.5">Volume (Liter)</label><input type="number" id="c_qty" value="1" class="${inputClass}" required></div>
            `;
        }
        else if (item === 'Amplas') {
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
                        <option value="Roll">1 Roll (50 M)</option><option value="Lembaran">Lembaran</option><option value="Pcs</option>
                    </select>
                </div>
                <div><label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1.5">Jumlah</label><input type="number" id="a_qty" value="1" class="${inputClass}" oninput="hitungKonversiAmplas()" required></div>
                <div class="md:col-span-4 bg-blue-50 text-blue-900 p-3 rounded-xl border border-blue-100 font-mono text-[11px] font-semibold text-center leading-relaxed shadow-sm mt-1" id="calcPreviewAmplas">
                    Matriks Gudang: Setara dengan 50 Meter Stok Induk.
                </div>
            `;
            hitungKonversiAmplas();
        }
    }

    function hitungKonversiAmplas() {
        const sat = document.getElementById('a_satuan').value;
        const val = parseFloat(document.getElementById('a_qty').value) || 0;
        const preview = document.getElementById('calcPreviewAmplas');

        if (sat === 'Roll') {
            const totalMeter = val * 50;
            preview.innerText = `Matriks Gudang: Input ${val} Roll = Otomatis kalkulasi ${totalMeter} Meter Stok Induk.`;
            return `${totalMeter} Meter`;
        } else {
            preview.innerText = `Matriks Gudang: Dicatat flat sesuai nominal unit: ${val} ${sat}.`;
            return `${val} ${sat}`;
        }
    }

    function simpanKeLogSistem() {
        const item = document.getElementById('itemBarang').value;
        if (!item) { alert('Silakan tentukan item material pembantu!'); return; }

        const tgl = document.getElementById('tglTransaksi').value;
        const jenis = document.getElementById('jenisTransaksi').value;
        const emptyRow = document.getElementById('barisKosong');

        let detailKarakteristik = "";
        let kuantitasFinal = "";
        let logistikKet = "";

        if (jenis === 'Barang Keluar') {
            const proyek = document.getElementById('namaProyek').value || "Project Global";
            logistikKet = `<span class="flex items-center gap-1 text-rose-600 font-medium">🔴 Keluar Ke: <b class="text-slate-800">${proyek}</b></span>`;
        } else {
            const asal = document.getElementById('asalBarang').value || "Stok Gudang Awal";
            logistikKet = `<span class="flex items-center gap-1 text-emerald-600 font-medium">🟢 Masuk Dari: <b class="text-slate-800">${asal}</b></span>`;
        }

        if (item === 'Lem') {
            const namaLem = document.getElementById('p_nama').value;
            const sat = document.getElementById('p_satuan').value;
            const qty = document.getElementById('p_qty').value;
            kuantitasFinal = `${qty} ${sat}`;
            detailKarakteristik = `Jenis Perekat: ${namaLem}`;
        } 
        else if (item === 'Sekrup') {
            const merk = document.getElementById('s_merk').value || "-";
            const uk = document.getElementById('s_ukuran').value || "-";
            const sat = document.getElementById('s_satuan').value;
            const qty = document.getElementById('s_qty').value;
            kuantitasFinal = `${qty} ${sat}`;
            detailKarakteristik = `Merk: ${merk} | Spek: ${uk}`;
        }
        else if (item === 'Cat' || item === 'Thinner' || item === 'Cairan Kimia H2O2') {
            const merk = document.getElementById('c_merk').value || "-";
            const jns = document.getElementById('c_jenis').value || "-";
            const qty = document.getElementById('c_qty').value;
            kuantitasFinal = `${qty} Liter`;
            detailKarakteristik = `Merk: ${merk} | Spek: ${jns}`;
        }
        else if (item === 'Amplas') {
            const merk = document.getElementById('a_merk').value || "-";
            const grit = document.getElementById('a_grit').value;
            const sat = document.getElementById('a_satuan').value;
            const qty = document.getElementById('a_qty').value;

            if (sat === 'Roll') {
                kuantitasFinal = `${qty * 50} Meter`;
                detailKarakteristik = `Merek: ${merk} | Grit: ${grit} (Konversi ${qty} Roll)`;
            } else {
                kuantitasFinal = `${qty} ${sat}`;
                detailKarakteristik = `Merek: ${merk} | Grit: ${grit}`;
            }
        }

        if (emptyRow) emptyRow.remove();

        const tbody = document.getElementById('badanTabelLog');
        const row = document.createElement('tr');
        row.className = "hover:bg-slate-50/80 text-slate-700 transition border-b border-slate-100";

        let badgeWarna = "bg-slate-100 text-slate-700 border-slate-200";
        if (jenis === 'Barang Masuk') badgeWarna = "bg-emerald-50 text-emerald-700 border-emerald-200/80";
        if (jenis === 'Barang Keluar') badgeWarna = "bg-rose-50 text-rose-700 border-rose-200/80";
        if (jenis === 'Stok Awal') badgeWarna = "bg-blue-50 text-blue-700 border-blue-200/80";

        row.innerHTML = `
            <td class="p-4 text-xs text-slate-500 font-mono">${tgl}</td>
            <td class="p-4 font-semibold text-slate-900">${item}</td>
            <td class="p-4 text-center"><span class="px-2.5 py-1 text-[11px] font-bold rounded-lg border ${badgeWarna}">${jenis}</span></td>
            <td class="p-4 text-xs text-slate-600">${detailKarakteristik}</td>
            <td class="p-4 text-right font-mono font-bold text-slate-900">${kuantitasFinal}</td>
            <td class="p-4 text-xs">${logistikKet}</td>
        `;

        tbody.insertBefore(row, tbody.firstChild);

        document.getElementById('formBahanPembantu').reset();
        document.getElementById('itemBarang').disabled = true;
        document.getElementById('wrapperSpesifikasi').classList.add('hidden');
        document.getElementById('tglTransaksi').valueAsDate = new Date();
        aturFormLogistik();
    }
</script>
@endsection