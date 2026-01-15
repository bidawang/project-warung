@extends('layouts.admin')

@section('title', 'Riwayat Transaksi Semua Warung')

@section('content')

    {{-- 
    Alpine.js State:
    - viewMode: 'detail' (default) atau 'compact'
    - searchGlobal: teks pencarian untuk tabel detail
    - searchCompact: objek untuk menyimpan teks pencarian per warung
--}}
    <div class="flex-1 flex flex-col overflow-hidden bg-gray-100" x-data="{
        viewMode: localStorage.getItem('transaksi_view') || 'detail',
        searchGlobal: '',
        searchCompact: {},
        init() {
            $watch('viewMode', value => localStorage.setItem('transaksi_view', value))
        }
    }">

        {{-- Header --}}
        <header class="flex justify-between items-center p-6 bg-white border-b sticky top-0 z-10">
            <h1 class="text-2xl font-bold text-gray-800">
                <i class="fas fa-history mr-2 text-indigo-600"></i> Riwayat Transaksi Global
            </h1>

            {{-- Opsi Tampilan (Button Toggle) --}}
            <div class="flex space-x-2 p-1 bg-gray-200 rounded-lg">
                <button @click="viewMode = 'detail'"
                    :class="viewMode === 'detail' ? 'bg-indigo-600 text-white shadow-md' : 'text-gray-700 hover:bg-gray-300'"
                    class="px-4 py-1.5 text-sm font-semibold rounded-lg transition duration-150">
                    <i class="fas fa-list-ul mr-1"></i> Detail (Vertikal)
                </button>
                <button @click="viewMode = 'compact'"
                    :class="viewMode === 'compact' ? 'bg-indigo-600 text-white shadow-md' : 'text-gray-700 hover:bg-gray-300'"
                    class="px-4 py-1.5 text-sm font-semibold rounded-lg transition duration-150">
                    <i class="fas fa-arrows-alt-h mr-1"></i> Compact (Horizontal)
                </button>
            </div>
        </header>

        <main class="flex-1 overflow-y-auto p-6 space-y-6">

            {{-- FILTER TANGGAL --}}
            <div class="bg-white shadow-sm border border-gray-200 rounded-xl p-4">
                <form action="{{ route('admin.riwayat_transaksi.index') }}" method="GET"
                    class="flex flex-wrap items-end gap-4">
                    {{-- Input tersembunyi agar mode view tetap terjaga saat submit form --}}
                    <input type="hidden" name="view" :value="viewMode">

                    <div class="flex-1 min-w-[200px]">
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Periode Awal</label>
                        <input type="date" name="start_date" value="{{ request('start_date') }}"
                            class="block w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    </div>
                    <div class="flex-1 min-w-[200px]">
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Periode Akhir</label>
                        <input type="date" name="end_date" value="{{ request('end_date') }}"
                            class="block w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    </div>
                    <div class="flex gap-2">
                        <button type="submit"
                            class="bg-indigo-600 text-white px-6 py-2 rounded-lg font-bold hover:bg-indigo-700 transition shadow-lg shadow-indigo-200">
                            <i class="fas fa-filter mr-1"></i> Filter
                        </button>
                        <a href="{{ route('admin.riwayat_transaksi.index') }}"
                            class="bg-white border border-gray-300 text-gray-600 px-4 py-2 rounded-lg hover:bg-gray-50 transition">
                            Reset
                        </a>
                    </div>
                </form>
            </div>

            @if ($dataTransaksiPerWarung->isEmpty())
                <div class="bg-white p-12 rounded-xl border border-dashed border-gray-300 text-center">
                    <i class="fas fa-folder-open text-gray-300 text-5xl mb-4"></i>
                    <p class="text-gray-500">Tidak ada data transaksi ditemukan.</p>
                </div>
            @else
                {{-- ========================================= --}}
                {{-- MODE DETAIL (DEFAULT / VERTIKAL) --}}
                {{-- ========================================= --}}
                <div x-show="viewMode === 'detail'" x-transition class="space-y-4">
                    @php
                        $allTransactions = collect();
                        foreach ($dataTransaksiPerWarung as $dataWarung) {
                            $dataWarung['riwayat_transaksi']->each(function ($t) use ($dataWarung) {
                                $t->nama_warung = $dataWarung['nama_warung'];
                                // Buat string search_blob untuk mempermudah Alpine mencari
                                $t->search_blob = strtolower(
                                    $t->deskripsi .
                                        ' ' .
                                        $t->jenis_transaksi .
                                        ' ' .
                                        $t->metode_pembayaran .
                                        ' ' .
                                        $dataWarung['nama_warung'],
                                );
                            });
                            $allTransactions = $allTransactions->merge($dataWarung['riwayat_transaksi']);
                        }
                        $allTransactions = $allTransactions->sortByDesc('tanggal');
                    @endphp

                    <div class="bg-white shadow-xl rounded-xl border border-gray-200 overflow-hidden">
                        <div class="p-4 border-b bg-gray-50 flex justify-between items-center">
                            <h2 class="font-bold text-gray-700 uppercase tracking-wider text-sm">Tabel Transaksi Global</h2>
                            <div class="relative w-64">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                                    <i class="fas fa-search text-xs"></i>
                                </span>
                                <input type="text" x-model="searchGlobal" placeholder="Cari transaksi..."
                                    class="pl-9 w-full border-gray-300 rounded-full text-sm focus:ring-indigo-500 focus:border-indigo-500">
                            </div>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 text-sm">
                                <thead class="bg-gray-100 font-bold text-gray-600">
                                    <tr>
                                        <th class="px-4 py-3 text-left">Waktu</th>
                                        <th class="px-4 py-3 text-left">Warung</th>
                                        <th class="px-4 py-3 text-left">Tipe</th>
                                        <th class="px-4 py-3 text-left">Deskripsi</th>
                                        <th class="px-4 py-3 text-center">Metode</th>
                                        <th class="px-4 py-3 text-right">Nominal</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @foreach ($allTransactions as $transaksi)
                                        <tr x-show="'{{ $transaksi->search_blob }}'.includes(searchGlobal.toLowerCase())"
                                            class="hover:bg-indigo-50/30 transition">
                                            <td class="px-4 py-3 whitespace-nowrap text-xs text-gray-500">
                                                {{ $transaksi->tanggal->translatedFormat('d M Y, H:i') }}</td>
                                            <td class="px-4 py-3 font-bold text-gray-800">{{ $transaksi->nama_warung }}</td>
                                            <td class="px-4 py-3">
                                                <span
                                                    class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase {{ $transaksi->jenis_transaksi == 'Pemasukan' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                                    {{ $transaksi->jenis_transaksi }}
                                                </span>
                                            </td>
                                            <td class="px-4 py-3 text-gray-600 italic">"{{ $transaksi->deskripsi }}"</td>
                                            <td class="px-4 py-3 text-center text-xs text-indigo-600 font-semibold">
                                                {{ $transaksi->metode_pembayaran }}</td>
                                            <td
                                                class="px-4 py-3 text-right font-bold {{ (float) $transaksi->total >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                                Rp {{ number_format((float) $transaksi->total, 0, ',', '.') }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- ========================================= --}}
                {{-- MODE COMPACT (HORIZONTAL SCROLL) --}}
                {{-- ========================================= --}}
                <div x-show="viewMode === 'compact'" x-transition class="flex overflow-x-auto pb-6 -mx-6 px-6 space-x-6">
                    @foreach ($dataTransaksiPerWarung as $dataWarung)
                        <div
                            class="flex-shrink-0 w-85 bg-white shadow-lg rounded-xl border border-gray-200 flex flex-col overflow-hidden h-[650px]">
                            {{-- Warung Header --}}
                            <div class="p-4 bg-gradient-to-br from-indigo-50 to-white border-b">
                                <h3 class="font-black text-gray-800 flex items-center truncate">
                                    <i class="fas fa-store text-indigo-500 mr-2"></i> {{ $dataWarung['nama_warung'] }}
                                </h3>
                                <div class="flex justify-between items-center mt-2">
                                    <span class="text-[10px] text-gray-400 font-bold uppercase">Saldo Kas</span>
                                    {{-- Ganti bagian ini --}}
                                    <span
                                        class="font-bold {{ is_numeric($dataWarung['total_kas_warung']) && $dataWarung['total_kas_warung'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                        @if (is_numeric($dataWarung['total_kas_warung']))
                                            Rp {{ number_format((float) $dataWarung['total_kas_warung'], 0, ',', '.') }}
                                        @else
                                            {{ $dataWarung['total_kas_warung'] }}
                                        @endif
                                    </span>
                                </div>
                                {{-- Search per Warung --}}
                                <div class="mt-3 relative">
                                    <input type="text" x-model="searchCompact['{{ $dataWarung['id'] }}']"
                                        placeholder="Filter transaksi..."
                                        class="w-full pl-8 pr-4 py-1.5 text-xs border-gray-200 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 bg-white">
                                    <i class="fas fa-search absolute left-3 top-2.5 text-gray-300 text-[10px]"></i>
                                </div>
                            </div>

                            {{-- Scrollable List --}}
                            <div class="flex-1 overflow-y-auto p-4 space-y-3 bg-gray-50/50">
                                @foreach ($dataWarung['riwayat_transaksi'] as $transaksi)
                                    @php
                                        $blob = strtolower(
                                            $transaksi->deskripsi .
                                                ' ' .
                                                $transaksi->jenis_transaksi .
                                                ' ' .
                                                $transaksi->metode_pembayaran,
                                        );
                                    @endphp
                                    <div x-show="!searchCompact['{{ $dataWarung['id'] }}'] || '{{ $blob }}'.includes(searchCompact['{{ $dataWarung['id'] }}'].toLowerCase())"
                                        class="bg-white p-3 rounded-lg border-l-4 shadow-sm transition hover:shadow-md
                             {{ (float) $transaksi->total >= 0 ? 'border-green-500' : 'border-red-500' }}">

                                        <div class="flex justify-between items-start mb-1">
                                            <span class="text-[10px] font-bold text-gray-400 uppercase tracking-tighter">
                                                {{ $transaksi->tanggal->translatedFormat('d M, H:i') }}
                                            </span>
                                            <span
                                                class="text-[10px] font-bold text-indigo-500 bg-indigo-50 px-1.5 py-0.5 rounded">
                                                {{ $transaksi->metode_pembayaran }}
                                            </span>
                                        </div>
                                        <p
                                            class="text-xs font-semibold text-gray-700 line-clamp-2 leading-relaxed mb-2 capitalize">
                                            {{ $transaksi->deskripsi }}
                                        </p>
                                        <div class="text-right">
                                            <span
                                                class="font-black text-sm {{ (float) $transaksi->total >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                                {{ (float) $transaksi->total >= 0 ? '+' : '' }} Rp
                                                {{ number_format((float) $transaksi->total, 0, ',', '.') }}
                                            </span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>

            @endif
        </main>
    </div>

@endsection

@push('styles')
    <style>
        /* Custom Scrollbar untuk mode horizontal agar lebih cantik */
        .overflow-x-auto::-webkit-scrollbar {
            height: 8px;
        }

        .overflow-x-auto::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        .overflow-x-auto::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 10px;
        }

        .overflow-x-auto::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
    </style>
@endpush
