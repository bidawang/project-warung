@extends('layouts.admin')

@section('title', 'Monitoring Warung - ' . $warung->nama_warung)

@section('content')
    <div class="min-h-screen bg-gray-50 pb-12">
        <main class="p-4 md:p-8">
            <div class="max-w-7xl mx-auto">

                {{-- Header & Action --}}
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
                    <div>
                        <nav class="flex mb-2" aria-label="Breadcrumb">
                            <ol class="inline-flex items-center space-x-1 md:space-x-3 text-sm text-gray-500 font-medium">
                                <li>Warung</li>
                                <li><svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                            clip-rule="evenodd"></path>
                                    </svg></li>
                                <li class="text-blue-600">Detail Monitoring</li>
                            </ol>
                        </nav>
                        <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">{{ $warung->nama_warung }}</h1>
                    </div>
                    <a href="{{ route('admin.warung.index') }}"
                        class="inline-flex items-center px-4 py-2.5 bg-white border border-gray-300 rounded-lg font-semibold text-gray-700 hover:bg-gray-50 shadow-sm transition-all text-sm">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Daftar Warung
                    </a>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">

                    {{-- ===================== --}}
                    {{-- DETAIL WARUNG --}}
                    {{-- ===================== --}}
                    <div class="bg-white rounded-2xl shadow-sm border p-6 flex flex-col justify-between">

                        <div>
                            <span class="bg-blue-50 text-blue-700 text-xs font-bold px-3 py-1 rounded-full uppercase">
                                Informasi Bisnis
                            </span>

                            <div class="mt-4 space-y-4">

                                <div>
                                    <p class="text-xs text-gray-400 uppercase font-semibold">Pemilik / Area</p>
                                    <p class="text-gray-800 font-semibold text-sm">
                                        {{ $warung->user->name ?? '-' }}
                                        <span class="text-gray-300 mx-1">•</span>
                                        {{ $warung->area->area ?? '-' }}
                                    </p>
                                </div>

                                <div>
                                    <p class="text-xs text-gray-400 uppercase font-semibold">Modal Awal</p>
                                    <p class="text-2xl font-bold text-gray-900">
                                        Rp {{ number_format($warung->modal, 0, ',', '.') }}
                                    </p>
                                </div>

                            </div>
                        </div>

                        <div class="mt-6 pt-4 border-t">
                            <p class="text-sm text-gray-500 italic leading-relaxed">
                                "{{ $warung->keterangan ?? 'Tidak ada keterangan tambahan.' }}"
                            </p>
                        </div>

                    </div>


                    {{-- ===================== --}}
                    {{-- LABA SUMMARY --}}
                    {{-- ===================== --}}
                    @php
                        $margin = $labaKotor > 0 ? ($labaBersih / $labaKotor) * 100 : 0;

                        $status = 'Buruk';
                        $color = 'text-red-300';

                        if ($margin > 30) {
                            $status = 'Sehat';
                            $color = 'text-green-300';
                        } elseif ($margin > 15) {
                            $status = 'Cukup';
                            $color = 'text-yellow-300';
                        }
                    @endphp

                    <div
                        class="lg:col-span-2 bg-gradient-to-br from-indigo-700 to-blue-800 rounded-2xl shadow-lg p-6 text-white">

                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 h-full">

                            {{-- ===================== --}}
                            {{-- KIRI --}}
                            {{-- ===================== --}}
                            <div class="flex flex-col justify-between">

                                {{-- HEADER --}}
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h3 class="text-lg font-bold uppercase tracking-wide">
                                            Ringkasan Laba
                                        </h3>
                                        <p class="text-xs opacity-70">Realtime dari transaksi</p>
                                    </div>

                                    <div class="text-right">
                                        <div class="bg-white/20 px-3 py-1 rounded text-sm font-bold">
                                            {{ number_format($margin, 1) }}%
                                        </div>
                                        <p class="text-xs {{ $color }}">{{ $status }}</p>
                                    </div>
                                </div>

                                {{-- ANGKA --}}
                                <div class="mt-6 space-y-5">

                                    <div>
                                        <p class="text-indigo-200 text-xs">Total Penjualan</p>
                                        <p class="text-2xl font-bold tracking-wide">
                                            Rp {{ number_format($labaKotor, 0, ',', '.') }}
                                        </p>
                                    </div>

                                    <div>
                                        <p class="text-indigo-200 text-xs">Total Modal</p>
                                        <p class="text-xl font-semibold">
                                            Rp {{ number_format($totalModal, 0, ',', '.') }}
                                        </p>
                                    </div>

                                    <div class="bg-white/10 p-4 rounded-xl border border-white/10">
                                        <p class="text-indigo-100 text-xs">Laba Bersih</p>
                                        <p class="text-3xl font-black text-green-300">
                                            Rp {{ number_format($labaBersih, 0, ',', '.') }}
                                        </p>
                                    </div>

                                </div>

                            </div>


                            {{-- ===================== --}}
                            {{-- KANAN: HISTORY --}}
                            {{-- ===================== --}}
                            <div class="bg-white/10 rounded-xl p-4 border border-white/10 flex flex-col">

                                <div class="flex justify-between items-center mb-3">
                                    <h4 class="text-sm font-bold">History Laba</h4>
                                    <span class="text-xs opacity-70">Live</span>
                                </div>

                                {{-- LIST --}}
                                <div class="overflow-y-auto max-h-64 pr-2 space-y-3" id="history-data">

                                    @forelse ($historyLaba as $item)
                                        <div
                                            class="flex items-center justify-between p-2.5 bg-white/5 hover:bg-white/10 border-b border-white/5 transition-all last:border-0">
                                            {{-- Sisi Kiri: Nama & Waktu --}}
                                            <div class="flex flex-col min-w-0">
                                                <span class="text-sm font-bold text-white truncate">
                                                    {{ $item->stokWarung->barang->nama_barang ?? '-' }}
                                                </span>
                                                <span class="text-[10px] text-white/50 uppercase tracking-tighter">
                                                    {{ $item->created_at->format('d/m/y • H:i') }}
                                                </span>
                                            </div>

                                            {{-- Sisi Kanan: Nominal Laba --}}
                                            <div class="flex flex-col items-end shrink-0 ml-3">
                                                <span
                                                    class="text-[10px] text-green-400 font-black uppercase mb-0.5">Laba</span>
                                                <div
                                                    class="inline-flex items-center px-2 py-0.5 bg-green-500/10 border border-green-500/20 rounded-md">
                                                    <span class="text-sm font-black text-green-400">
                                                        +{{ number_format($item->laba_bersih, 0, ',', '.') }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    @empty
                                        <p class="text-center text-xs opacity-70">Belum ada transaksi</p>
                                    @endforelse

                                </div>

                                {{-- LOADING --}}
                                <div id="ajax-load-status" class="hidden text-center py-2 text-xs opacity-70">
                                    <div
                                        class="animate-spin inline-block w-3 h-3 border-2 border-current border-t-transparent rounded-full mr-1">
                                    </div>
                                    Loading...
                                </div>

                            </div>

                        </div>
                    </div>

                </div>

                <div class="grid grid-cols-1 xl:grid-cols-3 gap-6 mb-8">

                    {{-- ===================== --}}
                    {{-- ASSET (KIRI BESAR) --}}
                    {{-- ===================== --}}
                    <div class="xl:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">

                        <div class="p-5 border-b border-gray-50 flex justify-between items-center">
                            <div>
                                <h3 class="text-lg font-bold text-gray-800">Manajemen Asset</h3>
                                <p class="text-xs text-gray-500">Inventaris & progres cicilan</p>
                            </div>
                            <span class="bg-gray-100 text-gray-600 text-xs font-bold px-3 py-1 rounded-lg">
                                {{ $assets->count() }} Items
                            </span>
                        </div>

                        <div class="overflow-auto max-h-[420px]">
                            <table class="min-w-full text-sm divide-y divide-gray-100">

                                <thead class="bg-gray-50 sticky top-0 text-[11px] uppercase text-gray-400">
                                    <tr>
                                        <th class="px-4 py-3 text-left">Tanggal</th>
                                        <th class="px-4 py-3 text-left">Asset</th>
                                        <th class="px-4 py-3 text-left">Harga</th>
                                        <th class="px-4 py-3 text-left text-green-600">Bayar</th>
                                        <th class="px-4 py-3 text-left text-red-500">Sisa</th>
                                        <th class="px-4 py-3 text-center">Progress</th>
                                    </tr>
                                </thead>

                                <tbody class="divide-y divide-gray-50">
                                    @forelse ($assets as $asset)
                                        @php
                                            $persen =
                                                $asset->harga_asset > 0
                                                    ? ($asset->total_dibayar / $asset->harga_asset) * 100
                                                    : 0;
                                        @endphp

                                        <tr class="hover:bg-blue-50/30">

                                            <td class="px-4 py-3 text-gray-500">
                                                {{ \Carbon\Carbon::parse($asset->tanggal_pembelian)->format('d/m/Y') }}
                                            </td>

                                            <td class="px-4 py-3 font-semibold text-gray-800">
                                                {{ $asset->nama }}
                                            </td>

                                            <td class="px-4 py-3 text-gray-700">
                                                Rp {{ number_format($asset->harga_asset, 0, ',', '.') }}
                                            </td>

                                            <td class="px-4 py-3 text-green-600 font-bold">
                                                Rp {{ number_format($asset->total_dibayar, 0, ',', '.') }}
                                            </td>

                                            <td class="px-4 py-3 text-red-500 font-bold">
                                                Rp {{ number_format($asset->sisa_pembayaran, 0, ',', '.') }}
                                            </td>

                                            <td class="px-4 py-3 w-[150px]">
                                                <div class="text-[10px] flex justify-between mb-1 text-gray-500">
                                                    <span>{{ $asset->volume_pelunasan }}x</span>
                                                    <span>{{ number_format($persen, 0) }}%</span>
                                                </div>
                                                <div class="w-full bg-gray-100 h-1.5 rounded-full">
                                                    <div class="bg-blue-600 h-1.5 rounded-full"
                                                        style="width: {{ $persen }}%">
                                                    </div>
                                                </div>
                                            </td>

                                        </tr>

                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center py-10 text-gray-400 italic">
                                                Belum ada data asset
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>

                            </table>
                        </div>
                    </div>


                    {{-- ===================== --}}
                    {{-- PENGELUARAN (KANAN) --}}
                    {{-- ===================== --}}
                    <div class="xl:col-span-1 flex flex-col gap-4">

                        {{-- FILTER + SUMMARY --}}
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">

                            <h3 class="text-sm font-bold text-gray-700 mb-3">Filter</h3>

                            <input type="text" id="filter-bulan"
                                class="w-full border-gray-200 rounded-xl text-sm shadow-sm focus:ring-blue-500 focus:border-blue-500 mb-4"
                                placeholder="Pilih bulan & tahun">

                            <div class="space-y-2 text-sm">

                                <div class="flex justify-between">
                                    <span class="text-gray-500">Estimasi</span>
                                    <span id="totalPengeluaran" class="font-bold text-gray-800">-</span>
                                </div>

                                <div class="flex justify-between">
                                    <span class="text-green-600">Terbayar</span>
                                    <span id="totalTerpenuhi" class="font-bold text-green-600">-</span>
                                </div>

                                <div class="flex justify-between">
                                    <span class="text-red-500">Tunggakan</span>
                                    <span id="totalBelum" class="font-bold text-red-500">-</span>
                                </div>

                            </div>

                        </div>

                        {{-- TABLE --}}
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden flex-1">

                            <div class="p-4 border-b text-sm font-bold text-gray-700">
                                Log Pengeluaran
                            </div>

                            <div class="overflow-auto max-h-[250px]">
                                <table class="min-w-full text-xs divide-y divide-gray-100">

                                    <thead class="bg-gray-50 sticky top-0 text-gray-400 uppercase">
                                        <tr>
                                            <th class="px-4 py-2 text-left">Tgl</th>
                                            <th class="px-4 py-2 text-left">Ket</th>
                                            <th class="px-4 py-2 text-left">Nominal</th>
                                            <th class="px-4 py-2 text-center">Status</th>
                                        </tr>
                                    </thead>

                                    <tbody id="tablePengeluaran" class="divide-y divide-gray-50">
                                        <tr>
                                            <td colspan="4" class="text-center py-8 text-gray-400">
                                                Memproses...
                                            </td>
                                        </tr>
                                    </tbody>

                                </table>
                            </div>

                        </div>

                    </div>

                </div>

                {{-- Inventory Section --}}
                <div x-data="{ activeTab: 'tersedia' }"
                    class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-6 border-b border-gray-100">
                        <h3 class="text-xl font-bold text-gray-800 mb-4">Inventaris Barang</h3>
                        <nav class="flex space-x-2 bg-gray-100 p-1 rounded-xl w-fit">
                            <button @click="activeTab = 'tersedia'"
                                :class="activeTab === 'tersedia' ? 'bg-white shadow-sm text-blue-600' :
                                    'text-gray-500 hover:text-gray-700'"
                                class="px-4 py-2 text-sm font-bold rounded-lg transition-all">Tersedia</button>
                            <button @click="activeTab = 'kosong'"
                                :class="activeTab === 'kosong' ? 'bg-white shadow-sm text-red-600' :
                                    'text-gray-500 hover:text-gray-700'"
                                class="px-4 py-2 text-sm font-bold rounded-lg transition-all">Stok Kosong</button>
                            <button @click="activeTab = 'semua'"
                                :class="activeTab === 'semua' ? 'bg-white shadow-sm text-gray-800' :
                                    'text-gray-500 hover:text-gray-700'"
                                class="px-4 py-2 text-sm font-bold rounded-lg transition-all">Semua Barang</button>
                        </nav>
                    </div>

                    <div class="p-0">
                        @php
                            $semua = $barangWithStok;
                            $tersedia = $barangWithStok->filter(fn($barang) => ($barang->stok_saat_ini ?? 0) > 0);
                            $kosong = $barangWithStok->filter(fn($barang) => ($barang->stok_saat_ini ?? 0) <= 0);
                            $tabs = ['semua' => $semua, 'tersedia' => $tersedia, 'kosong' => $kosong];
                        @endphp

                        @foreach ($tabs as $status => $dataList)
                            <div x-show="activeTab === '{{ $status }}'" class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-100">
                                    <thead class="bg-gray-50/30">
                                        <tr class="text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                            <th class="px-6 py-4 text-left">Nama Barang</th>
                                            <th class="px-6 py-4 text-left">Sisa Stok</th>
                                            <th class="px-6 py-4 text-left">Harga Modal</th>
                                            <th class="px-6 py-4 text-left">Harga Jual (Range)</th>
                                            <th class="px-6 py-4 text-left">Profit per Unit</th>
                                            <th class="px-6 py-4 text-left">Inflasi Laba</th>
                                            <th class="px-6 py-4 text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100">
                                        @forelse ($dataList as $barang)
                                            <tr class="hover:bg-gray-50 transition-colors">
                                                <td class="px-6 py-4">
                                                    <div class="text-sm font-bold text-gray-800">
                                                        {{ $barang->nama_barang }}</div>
                                                    <div class="text-[10px] text-gray-400">Exp:
                                                        {{ $barang->tanggal_kadaluarsa ? \Carbon\Carbon::parse($barang->tanggal_kadaluarsa)->format('d/m/Y') : '-' }}
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4">
                                                    <span
                                                        class="px-2.5 py-1 rounded-md text-xs font-bold {{ $barang->stok_saat_ini <= 0 ? 'bg-red-50 text-red-600' : 'bg-blue-50 text-blue-600' }}">
                                                        {{ $barang->stok_saat_ini ?? 0 }} Unit
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4 text-sm text-gray-600">Rp
                                                    {{ number_format($barang->harga_satuan ?? 0, 0, ',', '.') }}</td>
                                                <td class="px-6 py-4 text-sm font-medium text-gray-800">
                                                    Rp
                                                    {{ number_format($barang->harga_jual_range_awal ?? 0, 0, ',', '.') }} -
                                                    Rp {{ number_format($barang->harga_jual ?? 0, 0, ',', '.') }}
                                                </td>
                                                <td class="px-6 py-4">
                                                    <div class="text-sm font-bold text-blue-600">
                                                        {{ $barang->persentase_laba }}</div>
                                                    @php
                                                        $labaUnit =
                                                            ($barang->harga_jual ?? 0) - ($barang->harga_satuan ?? 0);
                                                    @endphp
                                                    <div class="text-xs font-medium text-green-600">+Rp
                                                        {{ number_format($labaUnit, 0, ',', '.') }}</div>
                                                </td>
                                                <td class="px-6 py-4 relative">

                                                    <div onclick="toggleInflasi('inflasi-{{ $status }}-{{ $barang->id }}')"
                                                        class="cursor-pointer inline-block">

                                                        @if ($barang->inflasi_laba > 0)
                                                            <span
                                                                class="px-2 py-1 bg-green-50 text-green-600 text-xs font-bold rounded-md">
                                                                ▲ {{ number_format($barang->inflasi_laba, 1) }}%
                                                            </span>
                                                        @elseif($barang->inflasi_laba < 0)
                                                            <span
                                                                class="px-2 py-1 bg-red-50 text-red-600 text-xs font-bold rounded-md">
                                                                ▼ {{ number_format(abs($barang->inflasi_laba), 1) }}%
                                                            </span>
                                                        @else
                                                            <span
                                                                class="px-2 py-1 bg-gray-100 text-gray-500 text-xs font-bold rounded-md">
                                                                0%
                                                            </span>
                                                        @endif

                                                    </div>

                                                    {{-- Popup Chat Bubble --}}
                                                    <div id="inflasi-{{ $status }}-{{ $barang->id }}"
                                                        class="hidden absolute z-50 mt-2 w-64 bg-white border border-gray-200 rounded-lg shadow-lg p-3 text-xs">

                                                        <div class="font-bold text-gray-700 mb-1">
                                                            Analisis Margin
                                                        </div>

                                                        <div class="text-gray-500">
                                                            Harga modal:
                                                            <span class="font-semibold text-gray-800">
                                                                Rp
                                                                {{ number_format($barang->harga_satuan ?? 0, 0, ',', '.') }}
                                                            </span>
                                                        </div>

                                                        <div class="text-gray-500">
                                                            Harga jual:
                                                            <span class="font-semibold text-gray-800">
                                                                Rp
                                                                {{ number_format($barang->harga_jual ?? 0, 0, ',', '.') }}
                                                            </span>
                                                        </div>

                                                        <div class="mt-2 text-gray-600">
                                                            @if ($barang->inflasi_laba > 0)
                                                                Margin naik
                                                                <b>{{ number_format($barang->inflasi_laba, 1) }}%</b>.
                                                                Potensi laba meningkat.
                                                            @elseif($barang->inflasi_laba < 0)
                                                                Margin turun
                                                                <b>{{ number_format(abs($barang->inflasi_laba), 1) }}%</b>.
                                                                Perlu evaluasi harga jual.
                                                            @else
                                                                Margin stabil.
                                                            @endif
                                                        </div>

                                                    </div>

                                                </td>


                                                <td class="px-6 py-4 text-center">
                                                    @if ($barang->stok_saat_ini > 0)
                                                        <a href="{{ route('admin.kuantitas.create', ['id_stok_warung' => $barang->id_stok_warung]) }}"
                                                            class="text-blue-600 hover:text-blue-800 font-bold text-xs bg-blue-50 px-3 py-1.5 rounded-lg transition-colors">Atur
                                                            Kuantitas</a>
                                                    @else
                                                        <span class="text-gray-300 text-xs italic font-medium">Stok
                                                            Kosong</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="px-6 py-10 text-center text-gray-400">Tidak ada
                                                    data.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        const warungId = "{{ $warung->id }}";
        const tableBody = document.getElementById('tablePengeluaran');

        function formatRupiah(number) {
            return 'Rp ' + new Intl.NumberFormat('id-ID').format(number);
        }

        // Fungsi untuk memformat tanggal ISO menjadi d/m/Y
        function formatDate(dateString) {
            if (!dateString) return '-';
            const date = new Date(dateString);
            return date.toLocaleDateString('id-ID', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric'
            });
        }

        function loadPengeluaran(bulan) {
            tableBody.innerHTML =
                '<tr><td colspan="4" class="px-6 py-10 text-center text-gray-400 italic">Memproses data...</td></tr>';

            fetch(`/admin/warung/${warungId}/pengeluaran-pokok-bulan?bulan=${bulan}`)
                .then(res => res.json())
                .then(res => {
                    tableBody.innerHTML = '';
                    if (res.data.length === 0) {
                        tableBody.innerHTML =
                            `<tr><td colspan="4" class="px-6 py-10 text-center text-gray-400">Tidak ada pengeluaran pada periode ini</td></tr>`;
                    }

                    res.data.forEach(item => {
                        const statusBadge = item.status === 'terpenuhi' ?
                            '<span class="px-2 py-1 bg-green-100 text-green-700 rounded-md text-[10px] font-bold uppercase">Terpenuhi</span>' :
                            '<span class="px-2 py-1 bg-red-100 text-red-600 rounded-md text-[10px] font-bold uppercase">Belum</span>';

                        tableBody.innerHTML += `
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 text-xs text-gray-500 font-medium">${formatDate(item.date)}</td>
                            <td class="px-6 py-4 text-sm font-bold text-gray-800">${item.redaksi}</td>
                            <td class="px-6 py-4 text-sm font-medium text-gray-900">${formatRupiah(item.jumlah)}</td>
                            <td class="px-6 py-4 text-center">${statusBadge}</td>
                        </tr>
                    `;
                    });

                    document.getElementById('totalPengeluaran').innerText = formatRupiah(res.total);
                    document.getElementById('totalTerpenuhi').innerText = formatRupiah(res.terpenuhi);
                    document.getElementById('totalBelum').innerText = formatRupiah(res.belum);
                });
        }

        const bulanInput = document.getElementById('filter-bulan');
        const now = new Date();
        const bulanSekarang = now.toISOString().slice(0, 7);
        bulanInput.value = bulanSekarang;

        loadPengeluaran(bulanSekarang);

        bulanInput.addEventListener('change', function() {
            loadPengeluaran(this.value);
        });

        function toggleInflasi(id) {

            document.querySelectorAll('[id^="inflasi-"]').forEach(el => {
                if (el.id !== id) {
                    el.classList.add('hidden')
                }
            })

            const el = document.getElementById(id)
            el.classList.toggle('hidden')
        }
        flatpickr("#filter-bulan", {
            plugins: [
                new monthSelectPlugin({
                    shorthand: true, // Jan, Feb
                    dateFormat: "Y-m", // hasil: 2026-03
                    altFormat: "F Y" // tampil: Maret 2026
                })
            ]
        });
    </script>
@endsection
