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
                                x-data="{ open: false }">
                                <div class="px-6 py-4 flex items-center justify-between transition-colors"
                                    :class="isWarungChecked('{{ $warungId }}') ? 'bg-indigo-600 text-white' :
                                        'bg-gray-50 text-gray-700'">
                                    <div class="flex items-center gap-3">
                                        <i class="fas fa-store"></i>
                                        <h3 class="font-bold tracking-wide">{{ $items[0]->warung->nama_warung }}</h3>
                                    </div>
                                    <input type="checkbox" name="selected_warungs[]" value="{{ $warungId }}"
                                        @change="toggleWarung('{{ $warungId }}')" x-model="checkedWarungs"
                                        class="w-6 h-6 rounded-lg cursor-pointer">
                                </div>

                                <div class="p-6 space-y-4" x-show="isWarungChecked('{{ $warungId }}')" x-transition>
                                    @foreach ($items as $i)
                                        <div
                                            class="p-4 rounded-2xl border border-gray-100 bg-gray-50/50 flex flex-col md:flex-row gap-4 items-start">
                                            <div class="w-full md:w-1/3">
                                                <p class="font-bold text-gray-800">{{ $i->barang->nama_barang }}</p>
                                                <div class="flex gap-2 mt-1">
                                                    <span class="text-xs font-medium text-gray-500">Butuh: <b
                                                            class="text-red-500">{{ $i->jumlah_awal }}</b></span>
                                                    <span class="text-xs font-medium text-gray-500">Sisa: <b
                                                            x-text="getRemainingNeed('{{ $i->id }}', {{ $i->jumlah_awal }})"></b></span>
                                                </div>
                                            </div>

                                            <div class="flex-1 space-y-3">
                                                {{-- 1. KONDISI: BARANG BELUM PERNAH DIBELI / TIDAK ADA DI TRANSAKSI --}}
                                                <template x-if="!hasInitialStock('{{ $i->barang->id }}')">
                                                    <div
                                                        class="flex items-center gap-2 text-amber-600 bg-amber-50 px-3 py-3 rounded-xl border border-amber-200">
                                                        <i class="fas fa-info-circle"></i>
                                                        <span class="text-xs font-bold uppercase tracking-tight">Stok tidak
                                                            tersedia atau belum dibeli</span>
                                                    </div>
                                                </template>

                                                {{-- 2. KONDISI: BARANG ADA, TAMPILKAN LIST ALOKASI --}}
                                                <template x-if="hasInitialStock('{{ $i->barang->id }}')">
                                                    <div>
                                                        <template
                                                            x-for="(alloc, index) in allocations['{{ $i->id }}']"
                                                            :key="index">
                                                            <div class="flex items-center gap-2 mb-3 animate-fadeIn">
                                                                <input type="number" min="0" step="1"
                                                                    :max="getMaxForAllocation('{{ $i->id }}', index)"
                                                                    :name="'items[{{ $i->id }}][transactions][' + index
                                                                        +
                                                                        '][jumlah]'"
                                                                    x-model.number="alloc.jumlah"
                                                                    @input="validateJumlah('{{ $i->id }}', index, {{ $i->jumlah_awal }})"
                                                                    class="w-20 px-2 py-1.5 rounded-lg border-gray-200 text-sm font-bold border text-center">


                                                                <select
                                                                    :name="'items[{{ $i->id }}][transactions][' + index
                                                                        +
                                                                        '][id_transaksi_barang]'"
                                                                    x-model="alloc.id_transaksi"
                                                                    @change="handleSelectChange('{{ $i->id }}', index, {{ $i->jumlah_awal }})"
                                                                    class="flex-1 px-3 py-1.5 rounded-lg border-gray-200 text-xs border bg-white outline-none focus:ring-2 focus:ring-indigo-500">
                                                                    <option value="">Pilih Sumber Stok</option>
                                                                    <template
                                                                        x-for="s in getOptionsForBarang('{{ $i->barang->id }}', alloc.id_transaksi)"
                                                                        :key="s.id">
                                                                        <option :value="s.id"
                                                                            :disabled="s.disabled"
                                                                            x-text="`TRX-${s.id} (${s.area}) • Sisa: ${s.sisa_display} pcs`">
                                                                        </option>
                                                                    </template>
                                                                </select>

                                                                <button type="button"
                                                                    @click="removeAllocation('{{ $i->id }}', index)"
                                                                    class="w-8 h-8 flex items-center justify-center rounded-full bg-red-50 text-red-500 hover:bg-red-500 hover:text-white transition-all">
                                                                    <i class="fas fa-times text-xs"></i>
                                                                </button>
                                                            </div>
                                                        </template>

                                                        {{-- TOMBOL TAMBAH ATAU KETERANGAN HABIS TERPAKAI --}}
                                                        <div class="mt-2">
                                                            <template x-if="hasCurrentStock('{{ $i->barang->id }}')">
                                                                <button type="button"
                                                                    @click="addAllocation('{{ $i->id }}', getRemainingNeed('{{ $i->id }}', {{ $i->jumlah_awal }}))"
                                                                    class="text-xs font-bold text-indigo-600 hover:text-indigo-800 flex items-center gap-1">
                                                                    <i class="fas fa-plus-circle"></i> Tambah Sumber
                                                                </button>
                                                            </template>
                                                            <template x-if="!hasCurrentStock('{{ $i->barang->id }}')">
                                                                <div
                                                                    class="text-[10px] font-bold text-red-500 bg-red-50 px-2 py-1 rounded inline-block border border-red-100">
                                                                    <i class="fas fa-exclamation-circle mr-1"></i> STOK
                                                                    HABIS TERPAKAI
                                                                </div>
                                                            </template>
                                                        </div>
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
                    this.allTransactions.forEach(t => {
                        this.stockSisa[t.id] = t.jumlah;
                    });

                    Object.values(this.rencanaMapping).flat().forEach(id => {
                        this.allocations[id.toString()] = [];
                    });
                },

                /* ======================
                   KONDISI STOK
                ====================== */

                hasInitialStock(barangId) {
                    return this.allTransactions.some(
                        t => t.id_barang == barangId && t.jumlah > 0
                    );
                },

                hasCurrentStock(barangId) {
                    return this.allTransactions.some(
                        t => t.id_barang == barangId && this.stockSisa[t.id] > 0
                    );
                },

                /* ======================
                   WARUNG
                ====================== */

                toggleWarung(warungId) {
                    const isChecked = this.checkedWarungs.includes(warungId.toString());
                    const items = this.rencanaMapping[warungId] || [];

                    items.forEach(id => {
                        if (isChecked) {
                            if (this.allocations[id].length === 0) {
                                this.addAllocation(id);
                            }
                        } else {
                            this.allocations[id] = [];
                        }
                    });

                    this.recalcStok();
                },

                /* ======================
                   ALOKASI
                ====================== */

                addAllocation(rencanaId) {
                    const id = rencanaId.toString();
                    if (!this.allocations[id]) this.allocations[id] = [];

                    this.allocations[id].push({
                        id_transaksi: '',
                        jumlah: 0
                    });
                },

                removeAllocation(rencanaId, index) {
                    this.allocations[rencanaId].splice(index, 1);
                    this.recalcStok();
                },

                /* ======================
                   SAAT PILIH STOK
                ====================== */

                handleSelectChange(rencanaId, index, totalButuh) {
                    const alloc = this.allocations[rencanaId][index];

                    if (!alloc.id_transaksi) {
                        alloc.jumlah = 0;
                        this.recalcStok();
                        return;
                    }

                    const trx = this.allTransactions.find(
                        t => t.id == alloc.id_transaksi
                    );
                    if (!trx) return;

                    // auto set: kebutuhan ATAU stok (mana lebih kecil)
                    alloc.jumlah = Math.min(totalButuh, trx.jumlah);

                    this.recalcStok();
                },

                /* ======================
                   VALIDASI INPUT MANUAL
                ====================== */

                validateJumlah(rencanaId, index) {
                    const alloc = this.allocations[rencanaId][index];

                    // tidak boleh minus
                    if (alloc.jumlah < 0) alloc.jumlah = 0;

                    if (!alloc.id_transaksi) return;

                    const trx = this.allTransactions.find(
                        t => t.id == alloc.id_transaksi
                    );
                    if (!trx) return;

                    let usedElsewhere = 0;
                    this.allocations[rencanaId].forEach((a, i) => {
                        if (i !== index && a.id_transaksi == alloc.id_transaksi) {
                            usedElsewhere += parseInt(a.jumlah) || 0;
                        }
                    });

                    const maxAllowed = trx.jumlah - usedElsewhere;

                    // kunci ke stok transaksi
                    alloc.jumlah = Math.min(alloc.jumlah, maxAllowed);

                    if (alloc.jumlah < 0) alloc.jumlah = 0;

                    this.recalcStok();
                },

                /* ======================
                   VALIDASI SUBMIT
                ====================== */

                hasZeroAllocation() {
                    return this.checkedWarungs.some(wId => {
                        return (this.rencanaMapping[wId] || []).some(rId => {
                            return (this.allocations[rId] || []).some(a => a.jumlah === 0);
                        });
                    });
                },

                /* ======================
                   UTIL
                ====================== */

                getRemainingNeed(rencanaId, totalButuh) {
                    const id = rencanaId.toString();
                    if (!this.allocations[id]) return totalButuh;

                    const filled = this.allocations[id].reduce(
                        (sum, a) => sum + (parseInt(a.jumlah) || 0), 0
                    );

                    return Math.max(0, totalButuh - filled);
                },

                getOptionsForBarang(barangId, currentTrxId) {
                    return this.allTransactions
                        .filter(t => t.id_barang == barangId)
                        .map(t => ({
                            ...t,
                            sisa_display: this.stockSisa[t.id],
                            disabled: this.stockSisa[t.id] <= 0 && t.id != currentTrxId
                        }));
                },

                recalcStok() {
                    this.allTransactions.forEach(
                        t => this.stockSisa[t.id] = t.jumlah
                    );

                    this.checkedWarungs.forEach(wId => {
                        (this.rencanaMapping[wId] || []).forEach(rId => {
                            (this.allocations[rId] || []).forEach(a => {
                                if (a.id_transaksi) {
                                    this.stockSisa[a.id_transaksi] -=
                                        (parseInt(a.jumlah) || 0);
                                }
                            });
                        });
                    });
                },

                /* ======================
                   COMPUTED
                ====================== */

                get sortedStock() {
                    return [...this.allTransactions].sort(
                        (a, b) => a.nama_barang.localeCompare(b.nama_barang)
                    );
                },

                isWarungChecked(id) {
                    return this.checkedWarungs.includes(id.toString());
                },

                get canSubmit() {
                    return this.checkedWarungs.length > 0 && !this.hasZeroAllocation();
                }
            }
        }
    </script>


@endsection
