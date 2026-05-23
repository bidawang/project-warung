@extends('layouts.admin')

@section('title', 'Monitoring Warung - ' . $warung->nama_warung)

@section('content')
    <div class="min-h-screen bg-gray-50 pb-12" x-data="{ activeTab: 'tersedia', searchQuery: '' }">
        <main class="p-4 md:p-8">
            <div class="mx-auto">

                {{-- Header & Action --}}
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
                    <div>
                        {{-- BREADCRUMB --}}
                        <nav class="flex mb-2" aria-label="Breadcrumb">
                            <ol class="inline-flex items-center gap-2 text-sm text-gray-400 font-medium">
                                <li class="hover:text-gray-600 transition cursor-pointer">Warung</li>
                                <li>
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                </li>
                                <li class="text-blue-600 font-semibold">Detail Monitoring</li>
                            </ol>
                        </nav>

                        {{-- TITLE + PERIODE --}}
                        <div class="flex flex-col md:flex-row md:items-center gap-3">
                            <h1 class="text-3xl font-black tracking-tight text-gray-900">
                                {{ $warung->nama_warung }}
                            </h1>
                            <div
                                class="flex items-center gap-2 px-3 py-1.5 rounded-xl bg-blue-50 border border-blue-100 w-fit">
                                <div class="w-2 h-2 rounded-full bg-blue-500 animate-pulse"></div>
                                <span class="text-sm font-semibold text-blue-700">
                                    {{ $periode }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                        {{-- FILTER FORM --}}
                        <div class="bg-white border border-gray-200 rounded-2xl shadow-sm px-4 py-2">
                            <form method="GET" class="flex items-center gap-3">
                                <input type="month" name="periode"
                                    value="{{ request('periode', $periode) }}"
                                    class="border-none focus:ring-0 text-sm font-bold text-gray-700 p-0">
                                <button type="submit"
                                    class="text-blue-600 hover:text-blue-800 font-bold text-sm">Terapkan</button>
                            </form>
                        </div>

                        <a href="{{ route('admin.warung.index') }}"
                            class="inline-flex items-center px-4 py-2.5 bg-white border border-gray-300 rounded-xl font-semibold text-gray-700 hover:bg-gray-50 shadow-sm transition-all text-sm">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                            </svg>
                            Kembali
                        </a>
                    </div>
                </div>

                {{-- PARTIALS --}}
                <div class="space-y-6">
                    @include('admin.warung.partial-show.bagian1')
                    {{-- @include('admin.warung.partial-show.bagian2') --}}
                    @include('admin.warung.partial-show.bagian3')
                    @include('admin.warung.partial-show.bagian4')
                </div>

                {{-- INVENTORY SECTION --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mt-8">
                    <div
                        class="p-6 border-b border-gray-100 flex flex-col md:flex-row md:items-center justify-between gap-4">
                        <div>
                            <h3 class="text-xl font-bold text-gray-800">Inventaris Barang</h3>
                            <p class="text-sm text-gray-500">Pantau stok dan margin laba secara real-time.</p>
                        </div>

                        <div class="flex flex-col sm:flex-row gap-3">
                            {{-- Search Input --}}
                            <div class="relative">
                                <input type="text" x-model="searchQuery" placeholder="Cari nama barang..."
                                    class="pl-10 pr-4 py-2 rounded-xl border-gray-200 text-sm focus:ring-blue-500 focus:border-blue-500 w-full">
                                <svg class="w-4 h-4 text-gray-400 absolute left-3 top-3" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>

                            <nav class="flex space-x-1 bg-gray-100 p-1 rounded-xl w-fit">
                                <button @click="activeTab = 'tersedia'"
                                    :class="activeTab === 'tersedia' ? 'bg-white shadow-sm text-blue-600' : 'text-gray-500'"
                                    class="px-4 py-2 text-xs font-bold rounded-lg transition-all">Tersedia</button>
                                <button @click="activeTab = 'kosong'"
                                    :class="activeTab === 'kosong' ? 'bg-white shadow-sm text-red-600' : 'text-gray-500'"
                                    class="px-4 py-2 text-xs font-bold rounded-lg transition-all">Stok Kosong</button>
                                <button @click="activeTab = 'semua'"
                                    :class="activeTab === 'semua' ? 'bg-white shadow-sm text-gray-800' : 'text-gray-500'"
                                    class="px-4 py-2 text-xs font-bold rounded-lg transition-all">Semua</button>
                            </nav>
                        </div>
                    </div>

                    <div class="p-0">
                        @php
                            $tersedia = $barangWithStok->filter(fn($b) => ($b->stok_saat_ini ?? 0) > 0);
                            $kosong = $barangWithStok->filter(fn($b) => ($b->stok_saat_ini ?? 0) <= 0);
                            $tabs = ['semua' => $barangWithStok, 'tersedia' => $tersedia, 'kosong' => $kosong];
                        @endphp

                        @foreach ($tabs as $status => $dataList)
                            <div x-show="activeTab === '{{ $status }}'" class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-100">
                                    <thead class="bg-gray-50/50">
                                        <tr class="text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                            <th class="px-6 py-4 text-left">Barang</th>
                                            <th class="px-6 py-4 text-left">Stok</th>
                                            <th class="px-6 py-4 text-left">Modal</th>
                                            <th class="px-6 py-4 text-left">Harga Jual</th>
                                            <th class="px-6 py-4 text-left">Profit / Unit</th>
                                            <th class="px-6 py-4 text-left">Inflasi</th>
                                            <th class="px-6 py-4 text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100">
                                        @forelse ($dataList as $barang)
                                            <tr class="hover:bg-gray-50/80 transition-colors"
                                                x-show="searchQuery === '' || '{{ strtolower($barang->nama_barang) }}'.includes(searchQuery.toLowerCase())">
                                                <td class="px-6 py-4">
                                                    <div class="text-sm font-bold text-gray-800">{{ $barang->nama_barang }}
                                                    </div>
                                                    <div class="text-[10px] text-gray-400 font-medium">Exp:
                                                        {{ $barang->tanggal_kadaluarsa ? \Carbon\Carbon::parse($barang->tanggal_kadaluarsa)->format('d M Y') : '-' }}
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4">
                                                    <span
                                                        class="px-2.5 py-1 rounded-md text-xs font-bold {{ $barang->stok_saat_ini <= 0 ? 'bg-red-50 text-red-600' : 'bg-blue-50 text-blue-600' }}">
                                                        {{ $barang->stok_saat_ini ?? 0 }} Unit
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4 text-sm text-gray-600">
                                                    Rp{{ number_format($barang->harga_satuan ?? 0, 0, ',', '.') }}</td>
                                                <td class="px-6 py-4 text-sm font-medium text-gray-800">
                                                    Rp{{ number_format($barang->harga_jual_range_awal ?? 0, 0, ',', '.') }}
                                                    @if ($barang->harga_jual_range_awal != $barang->harga_jual_range_akhir)
                                                        -
                                                        Rp{{ number_format($barang->harga_jual_range_akhir ?? 0, 0, ',', '.') }}
                                                    @endif
                                                </td>
                                                <td class="px-6 py-4">
                                                    @php
                                                        $modal = $barang->harga_satuan ?? 0;
                                                        $jualAwal = $barang->harga_jual_range_awal ?? 0;
                                                        $laba = $jualAwal - $modal;
                                                        $persen = $jualAwal > 0 ? ($laba / $jualAwal) * 100 : 0;
                                                    @endphp
                                                    <div class="text-sm font-bold text-blue-600">
                                                        {{ number_format($persen, 1) }}%</div>
                                                    <div class="text-[10px] font-bold text-green-600">
                                                        +Rp{{ number_format($laba, 0, ',', '.') }}</div>
                                                </td>
                                                <td class="px-6 py-4 relative">
                                                    <div @click="toggleInflasi('inflasi-{{ $status }}-{{ $barang->id }}')"
                                                        class="cursor-pointer inline-block group">
                                                        @if ($barang->inflasi_laba > 0)
                                                            <span
                                                                class="px-2 py-1 bg-green-50 text-green-600 text-xs font-bold rounded-md ring-1 ring-green-100 group-hover:bg-green-100 transition">
                                                                ▲ {{ number_format($barang->inflasi_laba, 1) }}%
                                                            </span>
                                                        @elseif($barang->inflasi_laba < 0)
                                                            <span
                                                                class="px-2 py-1 bg-red-50 text-red-600 text-xs font-bold rounded-md ring-1 ring-red-100 group-hover:bg-red-100 transition">
                                                                ▼ {{ number_format(abs($barang->inflasi_laba), 1) }}%
                                                            </span>
                                                        @else
                                                            <span
                                                                class="px-2 py-1 bg-gray-50 text-gray-400 text-xs font-bold rounded-md">0%</span>
                                                        @endif
                                                    </div>

                                                    {{-- Popup Chat Bubble --}}
                                                    <div id="inflasi-{{ $status }}-{{ $barang->id }}"
                                                        class="hidden absolute z-50 mt-2 w-72 bg-white border border-gray-200 rounded-2xl shadow-xl p-4 animate-in fade-in zoom-in duration-200">
                                                        <div class="flex justify-between items-center mb-3">
                                                            <span class="font-black text-gray-800 text-sm">Riwayat
                                                                Margin</span>
                                                            <button
                                                                @click="toggleInflasi('inflasi-{{ $status }}-{{ $barang->id }}')"
                                                                class="text-gray-400 hover:text-gray-600">×</button>
                                                        </div>
                                                        <div class="space-y-2 max-h-60 overflow-y-auto">
                                                            @foreach ($barang->riwayat_harga as $item)
                                                                <div
                                                                    class="p-2 rounded-xl {{ $loop->last ? 'bg-blue-50 border border-blue-100' : 'bg-gray-50' }}">
                                                                    <div
                                                                        class="flex justify-between text-[10px] font-bold mb-1">
                                                                        <span
                                                                            class="text-gray-400 uppercase">{{ $loop->last ? 'Saat Ini' : 'Sblmnya' }}</span>
                                                                        <span
                                                                            class="text-blue-600">{{ number_format($item->harga_jual_range_awal > 0 ? (($item->harga_jual_range_awal - $item->harga_modal) / $item->harga_jual_range_awal) * 100 : 0, 1) }}%</span>
                                                                    </div>
                                                                    <div class="flex justify-between text-xs">
                                                                        <span class="text-gray-500">M:
                                                                            Rp{{ number_format($item->harga_modal, 0, ',', '.') }}</span>
                                                                        <span class="font-bold text-gray-700">J:
                                                                            Rp{{ number_format($item->harga_jual_range_awal, 0, ',', '.') }}</span>
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4 text-center">
                                                    @if ($barang->stok_saat_ini > 0)
                                                        <a href="{{ route('admin.kuantitas.create', ['id_stok_warung' => $barang->id_stok_warung]) }}"
                                                            class="inline-block bg-gray-900 text-white text-[10px] font-bold px-4 py-2 rounded-lg hover:bg-blue-600 transition-all">
                                                            UPDATE
                                                        </a>
                                                    @else
                                                        <span class="text-gray-300 text-[10px] font-black italic">OUT OF
                                                            STOCK</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7" class="px-6 py-20 text-center">
                                                    <div class="flex flex-col items-center">
                                                        <div
                                                            class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mb-4 text-gray-300">
                                                            <svg class="w-8 h-8" fill="none" stroke="currentColor"
                                                                viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0a2 2 0 01-2 2H6a2 2 0 01-2-2m16 0V5a2 2 0 00-2-2H6a2 2 0 00-2 2v8m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                                            </svg>
                                                        </div>
                                                        <p class="text-gray-400 font-medium">Belum ada data barang.</p>
                                                    </div>
                                                </td>
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

        function formatRupiah(n) {
            return 'Rp ' + new Intl.NumberFormat('id-ID').format(n || 0);
        }

        function formatDate(s) {
            return s ? new Date(s).toLocaleDateString('id-ID', {
                day: '2-digit',
                month: 'short',
                year: 'numeric'
            }) : '-';
        }

        function loadPengeluaran(bulan) {

            if (!tableBody) return;

            tableBody.innerHTML = `
        <tr>
            <td colspan="4"
                class="px-6 py-14 text-center text-gray-400 italic">
                Memproses...
            </td>
        </tr>
    `;

            fetch(`/admin/warung/${warungId}/pengeluaran-pokok-bulan?bulan=${bulan}`)
                .then(res => res.json())
                .then(res => {

                    tableBody.innerHTML = '';

                    // =====================================================
                    // EMPTY
                    // =====================================================
                    if (res.data.length === 0) {

                        tableBody.innerHTML = `
                    <tr>
                        <td colspan="4"
                            class="px-6 py-14 text-center text-gray-400 italic">
                            Tidak ada pengeluaran.
                        </td>
                    </tr>
                `;

                    }

                    // =====================================================
                    // LOOP
                    // =====================================================
                    res.data.forEach(item => {

                        const isLunas = item.status === 'terpenuhi';

                        const badge = isLunas ?
                            `
                        <span class="px-2 py-1 rounded-full bg-green-100 text-green-700 text-[9px] font-black uppercase">
                            ● Lunas
                        </span>
                    ` :
                            `
                        <span class="px-2 py-1 rounded-full bg-red-100 text-red-600 text-[9px] font-black uppercase">
                            ● Belum
                        </span>
                    `;

                        const tombolAksi = isLunas ?
                            `
<div class="flex justify-center">
    <span class="px-2 py-1 rounded-lg bg-green-50 text-green-600 text-[10px] font-bold">
        Selesai
    </span>
</div>
` :
                            `
<div class="flex justify-center">
    <button
        type="button"
        onclick='window.dispatchEvent(
            new CustomEvent("open-konfirmasi-pengeluaran", {
                detail: ${JSON.stringify(item)}
            })
        )'
        class="px-3 py-1 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-[10px] font-black transition">

        Penuhi

    </button>
</div>
`;
                        tableBody.innerHTML += `
                    <tr class="hover:bg-gray-50 transition">

                        {{-- TANGGAL --}}
                        <td class="px-4 py-4 align-top">

                            <div class="text-xs font-bold text-gray-700 whitespace-nowrap">
                                ${formatDate(item.date)}
                            </div>

                        </td>

                        {{-- REDAKSI --}}
                        <td class="px-4 py-4 w-full">

                            <div class="font-bold text-gray-800 text-sm leading-tight">
                                ${item.redaksi}
                            </div>

                            <div class="mt-2">
                                ${badge}
                            </div>

                        </td>

                        {{-- NOMINAL --}}
                        <td class="px-4 py-4 text-right align-top">

                            <div class="font-black text-sm text-gray-900 whitespace-nowrap">
                                ${formatRupiah(item.jumlah)}
                            </div>

                        </td>

                        {{-- AKSI --}}
                        <td class="px-4 py-4 align-top">
                            ${tombolAksi}
                        </td>

                    </tr>
                `;
                    });

                    // =====================================================
                    // SUMMARY
                    // =====================================================
                    document.getElementById('totalPengeluaran').innerText =
                        formatRupiah(res.total);

                    document.getElementById('totalTerpenuhi').innerText =
                        formatRupiah(res.terpenuhi);

                    document.getElementById('totalBelum').innerText =
                        formatRupiah(res.belum);
                });
        }

        // Toggle Inflasi Popup
        function toggleInflasi(id) {
            const el = document.getElementById(id);
            const allPopups = document.querySelectorAll('[id^="inflasi-"]');

            allPopups.forEach(p => {
                if (p.id !== id) p.classList.add('hidden');
            });
            if (el) el.classList.toggle('hidden');
        }

        // Close popups when clicking outside
        document.addEventListener('click', (e) => {
            if (!e.target.closest('[id^="inflasi-"]') && !e.target.closest('.cursor-pointer')) {
                document.querySelectorAll('[id^="inflasi-"]').forEach(p => p.classList.add('hidden'));
            }
        });



        // Init
        const pInput = document.querySelector('[name="periode"]');
        if (pInput) {
            loadPengeluaran(pInput.value);
            pInput.addEventListener('change', (e) => loadPengeluaran(e.target.value));
        }
    </script>
@endsection
