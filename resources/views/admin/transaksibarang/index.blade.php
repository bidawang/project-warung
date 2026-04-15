@extends('layouts.admin')

@section('title', 'Manajemen Pengiriman Stok')

@section('content')
    <div x-data="shippingManager()" class="container mx-auto px-4 py-8">

        {{-- Header & Action Buttons --}}
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">Daftar Stok Pengiriman</h1>
                <p class="text-gray-500 mt-1">Kelola alokasi pengiriman barang ke warung-warung tujuan.</p>
            </div>

            <div class="flex flex-wrap gap-3">
                @if ($status === 'pending')
                    <button type="submit" form="formKirim" :disabled="!canSubmit()"
                        class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2.5 px-6 rounded-lg shadow-md transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed flex items-center text-sm md:text-base">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 10h11l-3-3m0 6l3-3m-3 3v7m9-14v2a2 2 0 01-2 2h-6a2 2 0 01-2-2v-2a2 2 0 012-2h6a2 2 0 012 2z" />
                        </svg>
                        Kirim Stok Terpilih
                    </button>

                    <a href="{{ route('admin.transaksibarang.create') }}"
                        class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2.5 px-6 rounded-lg shadow-md transition-all duration-200 flex items-center text-sm md:text-base group">
                        <svg class="w-5 h-5 mr-2 transform group-hover:rotate-90 transition-transform duration-200"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Tambah Transaksi Baru
                    </a>
                @endif

                <a href="{{ route('admin.rencana.index') }}"
                    class="bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 font-bold py-2.5 px-5 rounded-lg shadow-sm transition-all flex items-center text-sm md:text-base">
                    <svg class="w-5 h-5 mr-2 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2m-9 0a2 2 0 002 2h2m-2 0h-2m9 0h2m-2 0a2 2 0 00-2-2h-2" />
                    </svg>
                    Rencana Belanja
                </a>
            </div>
        </div>

        {{-- GRID LAYOUT --}}
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">

            {{-- LEFT: SIDEBAR RENCANA BELANJA --}}
            <aside class="lg:col-span-1 bg-white rounded-xl shadow-lg p-5 sticky top-6 h-fit border border-gray-100">
                <h2 class="font-bold text-lg mb-4 flex items-center">
                    <span class="w-2 h-6 bg-indigo-600 rounded mr-2"></span>
                    Rencana Belanja
                </h2>

                <div class="flex gap-1 mb-4 bg-gray-100 p-1 rounded-lg">
                    <button @click="view='warung'"
                        :class="view === 'warung' ? 'bg-white text-blue-600 shadow-sm' : 'text-gray-500 hover:text-gray-700'"
                        class="flex-1 py-1.5 rounded-md text-xs font-bold transition-all">
                        Warung
                    </button>
                    <button @click="view='barang'"
                        :class="view === 'barang' ? 'bg-white text-blue-600 shadow-sm' : 'text-gray-500 hover:text-gray-700'"
                        class="flex-1 py-1.5 rounded-md text-xs font-bold transition-all">
                        Barang
                    </button>
                </div>

                <input x-model="search" type="text" placeholder="Cari..."
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 mb-4 text-sm focus:ring-2 focus:ring-blue-500 outline-none">

                <div class="space-y-3 max-h-[60vh] overflow-y-auto pr-2 custom-scrollbar">
                    {{-- VIEW BY WARUNG --}}
                    <template x-if="view==='warung'">
                        <template x-for="(items, warung) in rencanaByWarung"
                            :key="warung + JSON.stringify(deliveries) + selectedTrx">
                            <div x-show="warung.toLowerCase().includes(search.toLowerCase())"
                                class="border rounded-lg p-3 bg-indigo-50/50 border-indigo-100">
                                <h3 class="font-bold text-indigo-700 text-sm mb-2 border-b border-indigo-100 pb-1"
                                    x-text="warung"></h3>
                                <ul class="space-y-2">
                                    <template x-for="i in items">
                                        <li class="text-xs">
                                            <div class="flex justify-between font-medium">
                                                <span x-text="i.barang.nama_barang"></span>
                                                <span x-text="i.jumlah_awal"></span>
                                            </div>
                                            <div :key="selectedTrx + JSON.stringify(deliveries)"
                                                :class="getStatusClass(i.id_barang, i.id_warung, i.jumlah_awal)"
                                                class="text-[10px] font-bold mt-0.5 uppercase tracking-wider">
                                                • <span
                                                    x-text="getStatusText(i.id_barang, i.id_warung, i.jumlah_awal)"></span>
                                            </div>
                                        </li>
                                    </template>
                                </ul>
                            </div>
                        </template>
                    </template>

                    {{-- VIEW BY BARANG --}}
                    <template x-if="view==='barang'">
                        <template x-for="(items, barang) in rencanaByBarang"
                            :key="barang + JSON.stringify(deliveries) + selectedTrx">
                            <div x-show="barang.toLowerCase().includes(search.toLowerCase())"
                                class="border rounded-lg p-3 bg-green-50/50 border-green-100">
                                <h3
                                    class="font-bold text-green-700 text-sm mb-2 border-b border-green-100 pb-1 flex justify-between">
                                    <span x-text="barang"></span>
                                    <span class="text-[10px] bg-green-200 px-1.5 py-0.5 rounded text-green-800"
                                        x-text="'Total: ' + totalRencanaBarang(items)"></span>
                                </h3>
                                <ul class="space-y-2">
                                    <template x-for="i in items">
                                        <li class="text-xs">
                                            <div class="flex justify-between font-medium">
                                                <span x-text="i.warung.nama_warung"></span>
                                                <span x-text="i.jumlah_awal"></span>
                                            </div>
                                            <div :class="getStatusClass(i.id_barang, i.id_warung, i.jumlah_awal)"
                                                class="text-[10px] font-bold mt-0.5 uppercase tracking-wider">
                                                • <span
                                                    x-text="getStatusText(i.id_barang, i.id_warung, i.jumlah_awal)"></span>
                                            </div>
                                        </li>
                                    </template>
                                </ul>
                            </div>
                        </template>
                    </template>
                </div>
            </aside>

            {{-- RIGHT: MAIN TABLE --}}
            <div class="lg:col-span-3 space-y-6">
                @if (session('success'))
                    <div
                        class="p-4 bg-green-50 border-l-4 border-green-500 text-green-700 shadow-sm flex items-center rounded-r-lg">
                        <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                clip-rule="evenodd"></path>
                        </svg>
                        {{ session('success') }}
                    </div>
                @endif

                <div class="bg-white shadow-xl rounded-xl overflow-hidden border border-gray-100">
                    <form id="formKirim" method="POST" action="{{ route('admin.transaksibarang.kirim.mass.proses') }}">
                        @csrf
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50 text-gray-500">
                                <tr>
                                    @if ($status === 'pending')
                                        <th class="px-4 py-4 text-center">
                                            <input type="checkbox" @click="toggleAll()" :checked="isAllSelected()"
                                                class="w-4 h-4 text-blue-600 rounded border-gray-300 focus:ring-blue-500">
                                        </th>
                                    @endif
                                    <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider">Barang</th>
                                    <th class="px-4 py-4 text-center text-xs font-bold uppercase tracking-wider">Sisa Stok
                                    </th>
                                    <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider">Alokasi
                                        Warung & Jumlah</th>
                                    <th class="px-4 py-4 text-center text-xs font-bold uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                                @forelse($transaksibarangs as $trx)
                                    <tr class="hover:bg-gray-50/50 transition-colors" :key="{{ $trx->id }}" data-jumlah="{{ $trx->jumlah }}">
                                        @if ($status === 'pending')
                                            <td class="px-4 py-4 text-center">
                                                <input type="checkbox" name="transaksi_ids[]" value="{{ $trx->id }}"
                                                    x-model="selectedTrx"
                                                    class="w-4 h-4 text-blue-600 rounded border-gray-300 focus:ring-blue-500">
                                            </td>
                                        @endif

                                        <td class="px-6 py-4">
                                            <div class="font-bold text-gray-800">{{ $trx->barang->nama_barang ?? '-' }}
                                            </div>
                                            <div class="text-[10px] text-gray-400 font-mono tracking-tighter">ID:
                                                #{{ $trx->id }}</div>
                                            @if ($status === 'pending')
                                                <input type="hidden" name="transaksi[{{ $trx->id }}][id_barang]"
                                                    value="{{ $trx->id_barang }}">
                                            @endif
                                        </td>

                                        <td class="px-4 py-4 text-center">
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold"
                                                :class="getSisa({{ $trx->id }}, {{ $trx->jumlah }}) <= 0 ?
                                                    'bg-red-100 text-red-700' : 'bg-blue-100 text-blue-700'">
                                                <span x-text="getSisa({{ $trx->id }}, {{ $trx->jumlah }})"></span>
                                            </span>
                                        </td>

                                        <td class="px-6 py-4">
                                            <template x-if="isSelected({{ $trx->id }})">
                                                <div class="space-y-3">
                                                    <template x-for="(row, index) in deliveries[{{ $trx->id }}]"
                                                        :key="row.id">
                                                        <div class="flex items-center gap-2">
                                                            <select
                                                                :name="`transaksi[{{ $trx->id }}][details][${row.id}][id_warung]`"
                                                                x-model="row.id_warung" required
                                                                class="block w-full text-sm border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 shadow-sm">
                                                                <option value="">Pilih Warung</option>
                                                                @foreach ($warungs as $warung)
                                                                    <option value="{{ $warung->id }}"
                                                                        :disabled="isWarungTaken({{ $trx->id }},
                                                                            {{ $warung->id }}, row.id)">
                                                                        {{ $warung->nama_warung }}
                                                                    </option>
                                                                @endforeach
                                                            </select>

                                                            <input type="number"
                                                                :name="`transaksi[{{ $trx->id }}][details][${row.id}][jumlah]`"
                                                                x-model.number="row.jumlah" min="1"
                                                                :max="getMaxAvailable({{ $trx->id }}, row.id)"
                                                                required
                                                                class="w-20 text-sm border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 text-center shadow-sm font-bold">

                                                            <button type="button"
                                                                @click="removeRow({{ $trx->id }}, index)"
                                                                x-show="deliveries[{{ $trx->id }}].length > 1"
                                                                class="text-red-400 hover:text-red-600">
                                                                <svg class="w-5 h-5" fill="currentColor"
                                                                    viewBox="0 0 20 20">
                                                                    <path fill-rule="evenodd"
                                                                        d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                                                                        clip-rule="evenodd"></path>
                                                                </svg>
                                                            </button>
                                                        </div>
                                                    </template>
                                                </div>
                                            </template>
                                            <template x-if="!isSelected({{ $trx->id }})">
                                                <span class="text-gray-400 italic text-xs text-center block">Centang untuk
                                                    mengatur alokasi</span>
                                            </template>
                                        </td>

                                        <td class="px-4 py-4 text-center">
                                            @if ($status === 'pending')
                                                <button type="button" @click="addRow({{ $trx->id }})"
                                                    :disabled="!isSelected({{ $trx->id }}) || getSisa({{ $trx->id }},
                                                        {{ $trx->jumlah }}) <= 0"
                                                    class="inline-flex items-center px-2 py-1 bg-indigo-500 hover:bg-indigo-600 text-white rounded-md text-[10px] font-bold shadow-sm disabled:opacity-30 transition-all uppercase">
                                                    + Warung
                                                </button>
                                            @else
                                                <span
                                                    class="px-2 py-1 rounded-full text-[10px] font-bold bg-gray-100 text-gray-600 uppercase">
                                                    {{ $trx->status }}
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-12 text-center text-gray-500 italic">Data tidak
                                            ditemukan.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </form>
                </div>
                {{ $transaksibarangs->links() }}
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('shippingManager', () => ({
                view: 'warung',
                search: '',
                selectedTrx: [],
                deliveries: {},
                rencanaByWarung: @json($rencanaBelanjaByWarung ?? []),
                rencanaByBarang: @json($rencanaBelanjaByBarang ?? []),

                // ✅ FIX: Mapping yang lebih robust
                idBarangMap: {},
                transaksiData: {}, // Cache data transaksi lengkap

                init() {
                    // ✅ Ambil data dari DOM untuk konsistensi
                    const checkboxes = document.querySelectorAll('input[name="transaksi_ids[]"]');
                    checkboxes.forEach(checkbox => {
                        const row = checkbox.closest('tr');
                        const trxId = checkbox.value;
                        const hiddenBarangId = row.querySelector(
                            `input[name="transaksi[${trxId}][id_barang]"]`);

                        if (hiddenBarangId) {
                            this.idBarangMap[trxId] = hiddenBarangId.value;
                            this.transaksiData[trxId] = {
                                id_barang: hiddenBarangId.value,
                                jumlah: parseInt(row.dataset.jumlah ||
                                    0) // Tambahkan data-jumlah di blade
                            };
                        }

                        // ✅ Initialize deliveries
                        this.deliveries[trxId] = [{
                            id: 'row-' + trxId + '-' + Date.now(),
                            id_warung: '',
                            jumlah: 0,
                            stok_asli: parseInt(row.dataset.jumlah || 0)
                        }];
                    });

                    // ✅ Force reactivity
                    this.$watch('deliveries', () => {
                        this.$nextTick(() => {});
                    });
                },

                // ✅ FIX: Logika getJumlahAlokasi yang lebih akurat
                getJumlahAlokasi(barangId, warungId) {
                    let total = 0;

                    Object.entries(this.deliveries).forEach(([trxId, rows]) => {
                        // Cek apakah transaksi ini selected DAN id_barang cocok
                        if (this.selectedTrx.includes(trxId.toString()) &&
                            this.idBarangMap[trxId] == barangId) {

                            rows.forEach(row => {
                                if (row.id_warung == warungId) {
                                    total += parseInt(row.jumlah) || 0;
                                }
                            });
                        }
                    });

                    return total;
                },

                // ✅ OPTIMIZE: Status dengan reactivity lebih baik
                getStatusText(barangId, warungId, target) {
                    const alokasi = this.getJumlahAlokasi(barangId, warungId);

                    if (alokasi === 0) return 'Belum Terpenuhi';
                    if (alokasi < target) return `Kurang ${target - alokasi}`;
                    if (alokasi === target) return '✅ Terpenuhi';
                    return `⚠️ Kelebihan ${alokasi - target}`;
                },

                getStatusClass(barangId, warungId, target) {
                    const alokasi = this.getJumlahAlokasi(barangId, warungId);

                    if (alokasi === 0) return 'text-gray-400 bg-gray-100';
                    if (alokasi < target) return 'text-amber-600 bg-amber-100 font-bold';
                    if (alokasi === target) return 'text-green-600 bg-green-100 font-bold';
                    return 'text-red-600 bg-red-100 font-bold';
                },

                // Sisanya sama...
                totalRencanaBarang(items) {
                    return items.reduce((sum, i) => sum + parseInt(i.jumlah_awal), 0);
                },

                isSelected(id) {
                    return this.selectedTrx.includes(id.toString());
                },

                isAllSelected() {
                    const checkboxes = document.querySelectorAll('input[name="transaksi_ids[]"]');
                    return checkboxes.length > 0 &&
                        this.selectedTrx.length === checkboxes.length;
                },

                toggleAll() {
                    if (this.isAllSelected()) {
                        this.selectedTrx = [];
                    } else {
                        const ids = Array.from(
                            document.querySelectorAll('input[name="transaksi_ids[]"]')
                        ).map(el => el.value);
                        this.selectedTrx = ids;
                    }
                },

                addRow(trxId) {
                    this.deliveries[trxId].push({
                        id: 'row-' + trxId + '-' + Date.now(),
                        id_warung: '',
                        jumlah: 0,
                        stok_asli: this.deliveries[trxId][0]?.stok_asli || 0
                    });
                },

                removeRow(trxId, index) {
                    if (this.deliveries[trxId].length > 1) {
                        this.deliveries[trxId].splice(index, 1);
                    }
                },

                getSisa(trxId, totalStok) {
                    if (!this.isSelected(trxId) || !this.deliveries[trxId]) return totalStok;
                    const allocated = this.deliveries[trxId]
                        .reduce((sum, row) => sum + (parseInt(row.jumlah) || 0), 0);
                    return Math.max(0, totalStok - allocated);
                },

                getMaxAvailable(trxId, currentRowId) {
                    const rows = this.deliveries[trxId] || []

                    const stokAwal = rows[0]?.stok_asli || 0

                    const totalDipakai = rows
                        .filter(row => row.id !== currentRowId)
                        .reduce((sum, row) => sum + (parseInt(row.jumlah) || 0), 0)

                    return Math.max(0, stokAwal - totalDipakai)
                },

                isWarungTaken(trxId, warungId, currentRowId) {
                    return this.deliveries[trxId].some(
                        row => row.id !== currentRowId && row.id_warung == warungId
                    );
                },

                canSubmit() {
                    if (this.selectedTrx.length === 0) return false;

                    return this.selectedTrx.every(trxId => {
                        const rows = this.deliveries[trxId] || [];
                        const allRowsFilled = rows.every(r =>
                            r.id_warung && parseInt(r.jumlah) > 0
                        );
                        const totalAllocated = rows.reduce(
                            (sum, r) => sum + (parseInt(r.jumlah) || 0), 0
                        );
                        const stokAwal = rows[0]?.stok_asli || 0;

                        return allRowsFilled && totalAllocated <= stokAwal && totalAllocated >
                            0;
                    });
                }
            }));
        });
    </script>

    <style>
        [x-cloak] {
            display: none !important;
        }

        .custom-scrollbar::-webkit-scrollbar {
            width: 4px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 10px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
    </style>
@endsection
