@extends('layouts.admin')

@section('title', 'Rencana Belanja Per Warung')
@section('content')

    <div class="flex-1 flex flex-col h-screen bg-gray-50" x-data="rencanaBelanja()" x-init="initData()">

        {{-- HEADER --}}
        <header class="flex justify-between items-center px-8 py-4 bg-white border-b shadow-sm z-10">
            <div>
                <h1 class="text-2xl font-extrabold text-gray-800 tracking-tight">Manajemen Rencana Belanja</h1>
                <p class="text-sm text-gray-500">Alokasi otomatis berdasarkan kebutuhan warung.</p>
            </div>
            <div class="flex gap-3">
                <button type="submit" form="form-rencana" :disabled="!canSubmit"
                    class="px-6 py-2 rounded-xl font-bold text-white transition-all shadow-md flex items-center gap-2"
                    :class="canSubmit ? 'bg-green-600 hover:bg-green-700' : 'bg-gray-300 cursor-not-allowed'">
                    <i class="fas fa-paper-plane"></i> Kirim Rencana
                </button>
            </div>
        </header>

        <main class="flex-1 overflow-hidden flex flex-row">
            {{-- SIDEBAR: STOK GLOBAL --}}
            <aside class="w-80 bg-white border-r flex flex-col shadow-inner">
                <div class="p-5 border-b bg-gray-50/50">
                    <h2 class="font-bold text-gray-700 flex items-center">
                        <i class="fas fa-warehouse mr-2 text-indigo-500"></i> Sisa Stok Global
                    </h2>
                </div>
                <div class="flex-1 overflow-y-auto p-4 space-y-3 custom-scrollbar">
                    <template x-for="s in sortedStock" :key="s.id">
                        <div class="p-3 rounded-xl border transition-all duration-300"
                            :class="stockSisa[s.id] <= 0 ? 'bg-gray-100 opacity-50' : 'bg-white border-gray-200 shadow-sm'">
                            <div class="flex justify-between items-start mb-1">
                                <span class="font-bold text-sm text-gray-800" x-text="s.nama_barang"></span>
                                <span class="text-[10px] bg-gray-100 px-2 py-0.5 rounded text-gray-500"
                                    x-text="'#'+s.id"></span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-xs font-black"
                                    :class="stockSisa[s.id] <= 0 ? 'text-red-500' : 'text-emerald-600'"
                                    x-text="stockSisa[s.id] + ' pcs'"></span>
                                <span class="text-[10px] text-gray-400 italic" x-text="s.area"></span>
                            </div>
                        </div>
                    </template>
                </div>
            </aside>

            {{-- CONTENT --}}
            <section class="flex-1 flex flex-col p-8 overflow-y-auto custom-scrollbar">
                <form id="form-rencana" action="{{ route('admin.transaksibarang.kirim.rencana.proses') }}" method="POST">
                    @csrf
                    <div class="mb-8 space-y-8">
                        @foreach ($rencanaBelanjaByWarung as $warungId => $items)
                            <div class="bg-white rounded-3xl border border-gray-200 shadow-sm overflow-hidden"
                                x-data="{ open: true }">
                                <div class="px-6 py-4 flex items-center justify-between transition-colors cursor-pointer"
                                    @click="open = !open"
                                    :class="isWarungChecked('{{ $warungId }}') ? 'bg-indigo-600 text-white' :
                                        'bg-gray-50 text-gray-700'">
                                    <div class="flex items-center gap-3">
                                        <i class="fas fa-store"></i>
                                        <h3 class="font-bold tracking-wide">{{ $items[0]->warung->nama_warung }}</h3>
                                    </div>
                                    <input type="checkbox" name="selected_warungs[]" value="{{ $warungId }}" @click.stop
                                        @change="toggleWarung('{{ $warungId }}')" x-model="checkedWarungs"
                                        class="w-6 h-6 rounded-lg cursor-pointer">
                                </div>

                                <div class="p-6 space-y-4" x-show="open && isWarungChecked('{{ $warungId }}')"
                                    x-transition>
                                    @foreach ($items as $i)
                                        <div id="item-row-{{ $i->id }}" data-barang-id="{{ $i->barang->id }}"
                                            data-jumlah-awal="{{ $i->jumlah_awal }}"
                                            class="p-4 rounded-2xl border border-gray-100 bg-gray-50/50 flex flex-col md:flex-row gap-4 items-start">

                                            <div class="w-full md:w-1/3">
                                                <p class="font-bold text-gray-800 uppercase text-sm">
                                                    {{ $i->barang->nama_barang }}</p>
                                                <div class="flex gap-2 mt-1">
                                                    <span class="text-[11px] font-medium text-gray-500">Butuh: <b
                                                            class="text-red-500">{{ $i->jumlah_awal }}</b></span>
                                                    <span class="text-[11px] font-medium text-gray-500">Sisa: <b
                                                            x-text="getRemainingNeed('{{ $i->id }}', {{ $i->jumlah_awal }})"></b></span>
                                                </div>
                                            </div>

                                            <div class="flex-1 space-y-3 w-full">
                                                {{-- STATUS 1: STOK TIDAK TERSEDIA DI GUDANG (KUNING) --}}
                                                <template x-if="!hasInitialStock('{{ $i->barang->id }}')">
                                                    <div
                                                        class="flex items-center gap-2 text-amber-600 bg-amber-50 px-3 py-3 rounded-xl border border-amber-200 w-full justify-center">
                                                        <i class="fas fa-exclamation-circle"></i>
                                                        <span class="text-xs font-bold uppercase">Stok Tidak Tersedia di
                                                            Gudang</span>
                                                    </div>
                                                </template>

                                                {{-- STATUS 2: STOK HABIS / TERPAKAI BARIS ATAS (MERAH) --}}
                                                <template x-if="isStockTrulyEmpty('{{ $i->barang->id }}')">
                                                    <div
                                                        class="flex items-center gap-2 text-white bg-red-500 px-3 py-3 rounded-xl border border-red-600 w-full justify-center shadow-sm">
                                                        <i class="fas fa-box-open"></i>
                                                        <span class="text-xs font-bold uppercase tracking-tight">Stok habis
                                                            terpakai
                                                        </span>
                                                    </div>
                                                </template>

                                                {{-- STATUS 3: ALOKASI AKTIF --}}
                                                <template x-if="hasInitialStock('{{ $i->barang->id }}')">
                                                    <div>
                                                        <template
                                                            x-for="(alloc, index) in allocations['{{ $i->id }}']"
                                                            :key="index">
                                                            <div class="flex items-center gap-3 mb-3">

                                                                {{-- KOLOM KIRI: KARTU SATUAN (Berbaris ke bawah) --}}
                                                                <div class="flex flex-col gap-2 min-w-[120px]">
                                                                    <template
                                                                        x-for="satuan in getValidSatuans('{{ $i->barang->id }}', alloc.jumlah)"
                                                                        :key="satuan.nama">
                                                                        <div
                                                                            class="bg-indigo-50 border-l-4 border-indigo-500 p-2 rounded-r-lg shadow-sm animate-fade-in">
                                                                            <p class="text-[10px] text-indigo-400 font-bold uppercase leading-none"
                                                                                x-text="satuan.nama"></p>
                                                                            <p class="text-sm font-black text-indigo-800">
                                                                                <span
                                                                                    x-text="alloc.jumlah / satuan.jumlah"></span>
                                                                                <span class="text-[10px]"
                                                                                    x-text="satuan.nama"></span>
                                                                            </p>
                                                                        </div>
                                                                    </template>
                                                                    {{-- Jika tidak ada satuan yang pas (modulo) atau cuma ada 1 satuan dasar --}}
                                                                    <template
                                                                        x-if="getValidSatuans('{{ $i->barang->id }}', alloc.jumlah).length === 0">
                                                                        <div
                                                                            class="bg-gray-50 border-l-4 border-gray-300 p-2 rounded-r-lg">
                                                                            <p
                                                                                class="text-[10px] text-gray-400 font-bold uppercase leading-none">
                                                                                Satuan Dasar</p>
                                                                            <p class="text-sm font-black text-gray-600"
                                                                                x-text="alloc.jumlah + ' Pcs'"></p>
                                                                        </div>
                                                                    </template>
                                                                </div>
                                                                {{-- Input Jumlah Utama --}}

                                                                <div class="relative min-w-[100px]">
                                                                    <input type="number"
                                                                        :name="'items[{{ $i->id }}][transactions][' +
                                                                        index + '][jumlah]'"
                                                                        x-model.number="alloc.jumlah"
                                                                        @input="validateJumlah('{{ $i->id }}', index)"
                                                                        class="w-full px-3 py-2 rounded-xl border-gray-300 text-sm font-bold border text-center focus:ring-2 focus:ring-indigo-500">
                                                                    
                                                                </div>


                                                                <select
                                                                    :name="'items[{{ $i->id }}][transactions][' + index
                                                                        +
                                                                        '][id_transaksi_barang]'"
                                                                    x-model="alloc.id_transaksi"
                                                                    @change="handleSelectChange('{{ $i->id }}', index, {{ $i->jumlah_awal }})"
                                                                    :disabled="getOptionsForBarang('{{ $i->barang->id }}', alloc
                                                                        .id_transaksi).count <= 1"
                                                                    class="flex-1 px-3 py-1.5 rounded-lg border-gray-200 text-xs border bg-white disabled:bg-gray-100 disabled:text-gray-900 font-semibold shadow-sm">
                                                                    <template
                                                                        x-for="s in getOptionsForBarang('{{ $i->barang->id }}', alloc.id_transaksi).items"
                                                                        :key="s.id">
                                                                        <option :value="s.id.toString()"
                                                                            :disabled="s.disabled"
                                                                            x-text="`TRX-${s.id} (${s.area}) • Sisa: ${s.sisa_display} pcs`">
                                                                        </option>
                                                                    </template>
                                                                </select>

                                                                <button type="button"
                                                                    @click="removeAllocation('{{ $i->id }}', index)"
                                                                    class="w-8 h-8 flex items-center justify-center rounded-full bg-red-50 text-red-500 hover:bg-red-500 hover:text-white transition-colors">
                                                                    <i class="fas fa-times text-xs"></i>
                                                                </button>
                                                            </div>
                                                        </template>

                                                        {{-- Tombol Tambah Sumber (Muncul jika stok global masih sisa) --}}
                                                        <template x-if="hasCurrentStock('{{ $i->barang->id }}')">
                                                            <button type="button"
                                                                @click="addAllocationManually('{{ $i->id }}', '{{ $i->barang->id }}', {{ $i->jumlah_awal }})"
                                                                class="text-[11px] font-bold text-indigo-600 hover:text-indigo-800 flex items-center gap-1 mt-1">
                                                                <i class="fas fa-plus-circle"></i> Tambah Sumber
                                                            </button>
                                                        </template>
                                                    </div>
                                                </template>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </form>
            </section>
        </main>
    </div>

    <script>
        function rencanaBelanja() {
            return {
                checkedWarungs: [],
                allTransactions: @json($allTransactionsForJs ?? []),
                stockSisa: {},
                allocations: {},
                rencanaMapping: @json($rencanaMapping ?? []),

                initData() {
                    this.recalcStok();
                    Object.values(this.rencanaMapping).flat().forEach(id => {
                        this.allocations[id.toString()] = [];
                    });
                },

                // Di dalam return function rencanaBelanja()

                getValidSatuans(barangId, inputJumlah) {
                    if (!inputJumlah || inputJumlah <= 0) return [];

                    const item = this.allTransactions.find(t => t.id_barang == barangId);
                    if (!item || !item.satuans) return [];

                    // Logika:
                    // 1. Ambil satuan yang "jumlah konversi"-nya > 1 (bukan satuan dasar seperti pcs)
                    // 2. Cek apakah inputJumlah habis dibagi (modulo 0) dengan jumlah konversi satuan tersebut
                    return item.satuans.filter(s => {
                        return s.jumlah > 1 && (inputJumlah % s.jumlah === 0);
                    });
                },

                isStockTrulyEmpty(barangId) {
                    return this.hasInitialStock(barangId) && !this.hasCurrentStock(barangId);
                },


                /* --- LOGIKA AUTO-ALLOCATE --- */
                toggleWarung(warungId) {
                    const isChecked = this.checkedWarungs.includes(warungId.toString());
                    const itemIds = this.rencanaMapping[warungId] || [];

                    if (isChecked) {
                        // Sinkronkan stok global
                        this.recalcStok();

                        itemIds.forEach(id => {
                            this.allocations[id] = [];
                            const el = document.getElementById(`item-row-${id}`);

                            if (el) {
                                const barangId = el.dataset.barangId;
                                const jumlahAwal = parseInt(el.dataset.jumlahAwal);

                                // Jalankan pengisian otomatis
                                this.autoFillItem(id, barangId, jumlahAwal);

                                // PERBAIKAN: Pindahkan pengecekan ke dalam loop ini
                                if (this.allocations[id].length === 0 && this.hasInitialStock(barangId)) {
                                    this.allocations[id].push({
                                        id_transaksi: '',
                                        jumlah: 0
                                    });
                                }
                            }
                        });
                    } else {
                        // Jika uncheck, hapus alokasi
                        itemIds.forEach(id => {
                            this.allocations[id] = [];
                        });
                    }
                    this.recalcStok();
                },

                autoFillItem(rencanaId, barangId, totalButuh) {
                    let needed = totalButuh;

                    // Ambil sumber transaksi yang masih memiliki sisa stok global
                    const sources = this.allTransactions
                        .filter(t => t.id_barang == barangId && this.stockSisa[t.id] > 0);

                    sources.forEach(src => {
                        if (needed <= 0) return;

                        const amountToTake = Math.min(needed, this.stockSisa[src.id]);
                        if (amountToTake > 0) {
                            this.allocations[rencanaId].push({
                                id_transaksi: src.id.toString(), // String agar cocok dengan value option
                                jumlah: amountToTake
                            });

                            // KURANGI LANGSUNG sisa stok agar baris berikutnya di kartu yang sama (atau kartu lain)
                            // mengetahui bahwa stok ini sudah terpakai
                            this.stockSisa[src.id] -= amountToTake;
                            needed -= amountToTake;
                        }
                    });
                    // 🔥 AUTO-SELECT JIKA HANYA ADA 1 OPSI STOK TERSISA
                    if (this.allocations[rencanaId].length === 0) {
                        const opts = this.getOptionsForBarang(barangId, '');

                        const available = opts.items.filter(o => !o.disabled);

                        if (available.length === 1) {
                            const trxId = available[0].id.toString();
                            const qty = Math.min(totalButuh, this.stockSisa[trxId]);

                            if (qty > 0) {
                                this.allocations[rencanaId].push({
                                    id_transaksi: trxId,
                                    jumlah: qty
                                });

                                this.stockSisa[trxId] -= qty;
                            }
                            return; // ⛔ STOP — jangan lanjut ke logic lain
                        }
                    }

                },

                /* --- ACTIONS --- */
                addAllocationManually(rencanaId, barangId, totalButuh) {
                    const id = rencanaId.toString();
                    this.recalcStok();

                    const opt = this.getOptionsForBarang(barangId, '');
                    const available = opt.items.filter(o => !o.disabled);

                    let trxId = '';
                    let qty = 0;

                    if (available.length > 0) {
                        // Ambil opsi pertama yang tersedia
                        trxId = available[0].id.toString();
                        qty = Math.min(this.getRemainingNeed(id, totalButuh), this.stockSisa[trxId]);
                    }

                    this.allocations[id].push({
                        id_transaksi: trxId,
                        jumlah: qty
                    });
                    this.recalcStok();
                },

                removeAllocation(rencanaId, index) {
                    this.allocations[rencanaId].splice(index, 1);
                    this.recalcStok();
                },

                handleSelectChange(rencanaId, index, totalButuh) {
                    const alloc = this.allocations[rencanaId][index];
                    if (!alloc.id_transaksi) {
                        alloc.jumlah = 0;
                    } else {
                        this.recalcStok();
                        const currentFilled = this.allocations[rencanaId]
                            .filter((_, idx) => idx !== index)
                            .reduce((sum, a) => sum + (parseInt(a.jumlah) || 0), 0);

                        const remainingNeed = Math.max(0, totalButuh - currentFilled);
                        const availableInTrx = this.stockSisa[alloc.id_transaksi] + (parseInt(alloc.jumlah) || 0);

                        alloc.jumlah = Math.min(remainingNeed, availableInTrx);
                    }
                    this.recalcStok();
                },

                validateJumlah(rencanaId, index) {
                    const alloc = this.allocations[rencanaId][index];

                    if (!alloc.id_transaksi) {
                        alloc.jumlah = 0;
                        return;
                    }

                    if (alloc.jumlah < 0) alloc.jumlah = 0;

                    this.recalcStok();
                    const currentAvailable =
                        this.stockSisa[alloc.id_transaksi] + (parseInt(alloc.jumlah) || 0);

                    if (alloc.jumlah > currentAvailable) alloc.jumlah = currentAvailable;
                    this.recalcStok();
                },


                /* --- CORE LOGIC & UTILS --- */
                recalcStok() {
                    // Reset stok sisa ke nilai awal database
                    this.allTransactions.forEach(t => this.stockSisa[t.id] = parseInt(t.jumlah));

                    // Kurangi stok berdasarkan semua baris alokasi yang sudah ada di layar
                    Object.keys(this.allocations).forEach(rId => {
                        this.allocations[rId].forEach(a => {
                            if (a.id_transaksi && a.jumlah) {
                                this.stockSisa[a.id_transaksi] -= (parseInt(a.jumlah) || 0);
                            }
                        });
                    });
                },

                getOptionsForBarang(barangId, currentTrxId) {
                    const items = this.allTransactions
                        .filter(t => t.id_barang == barangId)
                        .map(t => {
                            const sisa = this.stockSisa[t.id];
                            return {
                                ...t,
                                sisa_display: sisa,
                                isZero: sisa <= 0,
                                disabled: sisa <= 0 && (!currentTrxId || t.id.toString() !== currentTrxId.toString())
                            };
                        })
                        // 🔥 PINDAHKAN sisa 0 ke bawah TANPA sort stok
                        .sort((a, b) => a.isZero - b.isZero);

                    const activeCount = items.filter(i => !i.disabled).length;

                    return {
                        items,
                        count: activeCount
                    };
                },


                hasInitialStock(barangId) {
                    return this.allTransactions.some(t => t.id_barang == barangId && parseInt(t.jumlah) > 0);
                },

                hasCurrentStock(barangId) {
                    return this.allTransactions.some(t => t.id_barang == barangId && this.stockSisa[t.id] > 0);
                },

                getRemainingNeed(rencanaId, totalButuh) {
                    const filled = (this.allocations[rencanaId] || []).reduce((sum, a) => sum + (parseInt(a.jumlah) || 0),
                        0);
                    return Math.max(0, totalButuh - filled);
                },

                isWarungChecked(id) {
                    return this.checkedWarungs.includes(id.toString());
                },

                get sortedStock() {
                    return [...this.allTransactions].sort((a, b) => a.nama_barang.localeCompare(b.nama_barang));
                },

                get canSubmit() {
                    if (this.checkedWarungs.length === 0) return false;
                    return this.checkedWarungs.every(wId => {
                            const rIds = this.rencanaMapping[wId] || [];
                            return rIds.some(rId =>
                                (this.allocations[rId] || []).some(a => a.jumlah > 0)
                            );
                        }) && Object.values(this.allocations)
                        .flat()
                        .some(a => a.jumlah > 0);

                }
            }
        }
    </script>
@endsection
