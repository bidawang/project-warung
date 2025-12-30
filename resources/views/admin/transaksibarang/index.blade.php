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
                    {{-- Tombol Kirim Massal (Indigo) --}}
                    <button type="submit" form="formKirim" :disabled="!canSubmit()"
                        class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2.5 px-6 rounded-lg shadow-md transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed flex items-center text-sm md:text-base">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 10h11l-3-3m0 6l3-3m-3 3v7m9-14v2a2 2 0 01-2 2h-6a2 2 0 01-2-2v-2a2 2 0 012-2h6a2 2 0 012 2z" />
                        </svg>
                        Kirim Stok Terpilih
                    </button>

                    {{-- Tombol Baru (Blue) --}}
                    <a href="{{ route('admin.transaksibarang.create') }}"
                        class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2.5 px-6 rounded-lg shadow-md transition-all duration-200 flex items-center text-sm md:text-base group">
                        <svg class="w-5 h-5 mr-2 transform group-hover:rotate-90 transition-transform duration-200"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Tambah Transaksi Baru
                    </a>
                @endif

                {{-- Tombol Rencana Belanja (White/Bordered) --}}
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

        {{-- Status Tabs --}}
        <div class="mb-8 border-b border-gray-200">
            <ul class="flex flex-wrap -mb-px text-sm font-medium text-center">
                @foreach (['pending' => 'Belum Dikirim', 'kirim' => 'Sedang Dikirim', 'terima' => 'Diterima', 'tolak' => 'Ditolak'] as $key => $label)
                    <li class="mr-2">
                        <a href="{{ route('admin.transaksibarang.index', ['status' => $key]) }}"
                            class="inline-block p-4 rounded-t-lg border-b-2 transition-colors {{ $status === $key ? 'text-blue-600 border-blue-600 active' : 'text-gray-500 border-transparent hover:text-gray-600 hover:border-gray-300' }}">
                            {{ $label }}
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>

        {{-- Messages --}}
        @if (session('success'))
            <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 text-green-700 shadow-sm flex items-center">
                <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                        clip-rule="evenodd"></path>
                </svg>
                {{ session('success') }}
            </div>
        @endif

        {{-- Table --}}
        <div class="bg-white shadow-xl rounded-xl overflow-hidden border border-gray-100">
            <form id="formKirim" method="POST" action="{{ route('admin.transaksibarang.kirim.mass.proses') }}">
                @csrf
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            @if ($status === 'pending')
                                <th class="px-6 py-4 text-center">
                                    <input type="checkbox" @click="toggleAll()" :checked="isAllSelected()"
                                        class="w-4 h-4 text-blue-600 rounded border-gray-300 focus:ring-blue-500">
                                </th>
                            @endif
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Barang
                            </th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Sisa
                                Stok</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Alokasi
                                Warung & Jumlah</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Harga
                            </th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        @forelse($transaksibarangs as $trx)
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                @if ($status === 'pending')
                                    <td class="px-6 py-4 text-center">
                                        <input type="checkbox" name="transaksi_ids[]" value="{{ $trx->id }}"
                                            x-model="selectedTrx" value="{{ $trx->id }}"
                                            class="w-4 h-4 text-blue-600 rounded border-gray-300 focus:ring-blue-500">
                                    </td>
                                @endif

                                <td class="px-6 py-4">
                                    <div class="font-bold text-gray-800">{{ $trx->barang->nama_barang ?? '-' }}</div>
                                    <div class="text-xs text-gray-500">ID: #{{ $trx->id }}</div>
                                    @if ($status === 'pending')
                                        <input type="hidden" name="transaksi[{{ $trx->id }}][barang_id]"
                                            value="{{ $trx->id_barang }}">
                                    @endif
                                </td>

                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-bold"
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
                                                <div class="flex items-center gap-2 group">
                                                    <select
                                                        :name="`transaksi[{{ $trx->id }}][details][${row.id}][warung_id]`"
                                                        x-model="row.warung_id" required
                                                        class="block w-full text-sm border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
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
                                                        :max="getMaxAvailable({{ $trx->id }}, {{ $trx->jumlah }}, row
                                                            .id)"
                                                        required
                                                        class="w-20 text-sm border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 text-center">

                                                    <button type="button" @click="removeRow({{ $trx->id }}, index)"
                                                        x-show="deliveries[{{ $trx->id }}].length > 1"
                                                        class="text-red-400 hover:text-red-600 transition-colors">
                                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
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
                                        <span class="text-gray-400 italic text-sm text-center block">Centang untuk mengatur
                                            pengiriman</span>
                                    </template>
                                </td>

                                <td class="px-6 py-4 text-sm text-gray-600">
                                    Rp {{ number_format($trx->harga, 0, ',', '.') }}
                                </td>

                                <td class="px-6 py-4 text-center">
                                    @if ($status === 'pending')
                                        <button type="button" @click="addRow({{ $trx->id }})"
                                            :disabled="!isSelected({{ $trx->id }}) || getSisa({{ $trx->id }},
                                                {{ $trx->jumlah }}) <= 0"
                                            class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-bold rounded-full shadow-sm text-white bg-indigo-500 hover:bg-indigo-600 focus:outline-none disabled:opacity-30 transition-all">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 4v16m8-8H4"></path>
                                            </svg>
                                            Warung
                                        </button>
                                    @else
                                        <span
                                            class="px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600 uppercase">
                                            {{ $trx->status }}
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-gray-500 italic">
                                    Tidak ada data transaksi ditemukan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </form>
        </div>

        <div class="mt-6">
            {{ $transaksibarangs->links() }}
        </div>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('shippingManager', () => ({
                selectedTrx: [],
                deliveries: {},

                init() {
                    // Gunakan json_encode agar kompatibel dengan PHP 8.2 dan Laravel 12
                    // Kita ambil data dasar yang dikirim dari Controller
                    const rawData = {!! json_encode($transaksibarangs->items()) !!};

                    if (rawData && Array.isArray(rawData)) {
                        rawData.forEach(trx => {
                            this.deliveries[trx.id] = [{
                                id: 'row-' + trx.id + '-' + Math.random().toString(36)
                                    .substr(2, 9),
                                warung_id: '',
                                jumlah: trx.jumlah,
                                stok_asli: trx.jumlah
                            }];
                        });
                    }
                },

                isSelected(id) {
                    return this.selectedTrx.includes(id.toString());
                },

                isAllSelected() {
                    // Mencari checkbox di dalam tabel
                    const checkboxes = document.querySelectorAll('input[name="transaksi_ids[]"]');
                    return checkboxes.length > 0 && this.selectedTrx.length === checkboxes.length;
                },

                toggleAll() {
                    if (this.isAllSelected()) {
                        this.selectedTrx = [];
                    } else {
                        const ids = [];
                        document.querySelectorAll('input[name="transaksi_ids[]"]').forEach(el => {
                            ids.push(el.value);
                        });
                        this.selectedTrx = ids;
                    }
                },

                addRow(trxId) {
                    this.deliveries[trxId].push({
                        id: 'row-' + trxId + '-' + Math.random().toString(36).substr(2, 9),
                        warung_id: '',
                        jumlah: 1
                    });
                },

                removeRow(trxId, index) {
                    if (this.deliveries[trxId].length > 1) {
                        this.deliveries[trxId].splice(index, 1);
                    }
                },

                getSisa(trxId, totalStok) {
                    if (!this.isSelected(trxId) || !this.deliveries[trxId]) return totalStok;
                    const allocated = this.deliveries[trxId].reduce((sum, row) => sum + (parseInt(row
                        .jumlah) || 0), 0);
                    return totalStok - allocated;
                },

                getMaxAvailable(trxId, totalStok, currentRowId) {
                    if (!this.deliveries[trxId]) return 0;
                    const otherAllocated = this.deliveries[trxId]
                        .filter(row => row.id !== currentRowId)
                        .reduce((sum, row) => sum + (parseInt(row.jumlah) || 0), 0);
                    return totalStok - otherAllocated;
                },

                isWarungTaken(trxId, warungId, currentRowId) {
                    if (!this.deliveries[trxId]) return false;
                    return this.deliveries[trxId].some(row => row.id !== currentRowId && row
                        .warung_id == warungId);
                },

                canSubmit() {
                    if (this.selectedTrx.length === 0) return false;

                    // Validasi setiap ID yang dicentang
                    return this.selectedTrx.every(id => {
                        const rows = this.deliveries[id];
                        if (!rows) return false;

                        // Pastikan warung dipilih, jumlah > 0, dan tidak melebihi stok
                        const allRowsFilled = rows.every(r => r.warung_id && parseInt(r
                            .jumlah) > 0);

                        // Cari total alokasi (pastikan tidak melebihi stok baris tersebut)
                        const totalAllocated = rows.reduce((sum, r) => sum + (parseInt(r
                            .jumlah) || 0), 0);

                        // Kita perlu ambil stok awal untuk ID ini. 
                        // Karena kita di dalam loop JS, kita asumsikan stok_asli tersimpan di row pertama
                        const stokAwal = rows[0]?.stok_asli || 0;

                        return allRowsFilled && totalAllocated > 0 && totalAllocated <=
                        stokAwal;
                    });
                }
            }));
        });
    </script>

    <style>
        [x-cloak] {
            display: none !important;
        }

        input[type=number]::-webkit-inner-spin-button,
        input[type=number]::-webkit-outer-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }
    </style>
@endsection
