@props(['accentBtn' => 'bg-slate-900 hover:bg-slate-800'])

<div id="floatingPaginatorWrap" class="no-print">
    <button id="floatingPaginatorBtn" onclick="toggleFloatingPaginator()"
        class="{{ $accentBtn }} fixed bottom-6 right-6 z-40 text-white rounded-full shadow-lg h-14 w-14 flex flex-col items-center justify-center transition-transform hover:scale-105">
        <span class="text-lg leading-none">&#9881;&#65039;</span>
        <span id="floatBadgeHal" class="text-[9px] font-bold font-ledger leading-none mt-0.5">1/1</span>
    </button>

    <div id="floatingPaginatorPanel" class="fixed bottom-24 right-6 z-50 w-[340px] bg-white rounded-2xl shadow-2xl border border-slate-200 overflow-hidden hidden">
        <div class="bg-slate-900 text-white px-4 py-3 flex items-center justify-between">
            <span class="text-xs font-bold uppercase tracking-wider">Pengaturan Tampilan Data</span>
            <button onclick="toggleFloatingPaginator()" class="text-white/70 hover:text-white text-xs font-bold px-2">&#10005;</button>
        </div>

        <div class="p-4 space-y-3">
            <div>
                <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1.5">Baris per halaman</label>
                <select id="pilihBarisPerHalamanFloat" onchange="gantiBarisPerHalaman(this)"
                    class="w-full px-3 h-9 border border-slate-300 rounded-lg text-sm text-slate-700 bg-white focus:ring-2 focus:outline-none">
                    <option value="25" selected>25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                    <option value="semua">Semua</option>
                </select>
            </div>

            <div>
                <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1.5">Lompat ke halaman</label>
                <div class="flex items-center gap-2">
                    <input type="number" id="inputLompatHalaman" min="1" placeholder="No. halaman"
                        class="w-full px-3 h-9 border border-slate-300 rounded-lg text-sm text-slate-700 focus:ring-2 focus:outline-none font-ledger"
                        onkeydown="if(event.key==='Enter') lompatKeHalaman()">
                    <button onclick="lompatKeHalaman()"
                        class="shrink-0 px-4 h-9 bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold rounded-lg transition-colors">
                        Ke
                    </button>
                </div>
            </div>

            <div id="infoPaginasiFloat" class="text-[11px] text-slate-500 font-ledger text-center border-t border-slate-100 pt-2.5">
                Menampilkan 0-0 dari 0 data
            </div>

            <div id="tombolHalamanFloat" class="flex flex-wrap items-center justify-center gap-1.5"></div>
        </div>
    </div>
</div>

<script>
    function toggleFloatingPaginator() {
        document.getElementById('floatingPaginatorPanel').classList.toggle('hidden');
    }

    function lompatKeHalaman() {
        const input = document.getElementById('inputLompatHalaman');
        const nomor = parseInt(input.value, 10);
        if (!nomor || nomor < 1) return;
        pindahHalaman(nomor);
        input.value = '';
    }
</script>