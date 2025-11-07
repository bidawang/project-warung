@extends('layouts.admin')

@section('title', 'Riwayat Transaksi Semua Warung')

@section('content')
@php
    // Ambil mode tampilan dari request (jika ada), jika tidak, default ke 'compact'
    // JS di bawah akan mencoba membaca dari Local Storage terlebih dahulu
    $viewMode = request('view', 'compact');
    $isCompact = $viewMode === 'compact';
@endphp

<div class="flex-1 flex flex-col overflow-hidden bg-gray-100">

    {{-- Header --}}
    <header class="flex justify-between items-center p-6 bg-white border-b sticky top-0 z-10">
        <h1 class="text-2xl font-bold text-gray-800">
            <i class="fas fa-chart-line mr-2 text-indigo-600"></i> Laporan Transaksi Kas Warung
        </h1>

        {{-- Opsi Tampilan (Button Toggle) --}}
        <div class="flex space-x-2 p-1 bg-gray-200 rounded-lg">
            <button id="toggle-compact" data-mode="compact"
               class="px-3 py-1 text-sm font-semibold rounded-lg transition duration-150 view-toggle">
                <i class="fas fa-arrows-alt-h mr-1"></i> Compact (Horizontal)
            </button>
            <button id="toggle-detail" data-mode="detail"
               class="px-3 py-1 text-sm font-semibold rounded-lg transition duration-150 view-toggle">
                <i class="fas fa-list-ul mr-1"></i> Detail (Vertikal)
            </button>
        </div>
    </header>

    <main class="flex-1 overflow-y-auto p-6 space-y-6">

        {{-- ========================================= --}}
        {{-- FILTER TANGGAL --}}
        {{-- ========================================= --}}
        <div class="bg-white shadow-md rounded-lg p-4">
            <form action="{{ route('admin.riwayat_transaksi.index') }}" method="GET" id="filter-form" class="flex items-end space-x-4">
                {{-- Hidden input untuk mempertahankan mode tampilan saat filter dikirim --}}
                <input type="hidden" name="view" value="{{ $viewMode }}" id="view-mode-input">

                <div class="flex-1">
                    <label for="start_date" class="block text-sm font-medium text-gray-700">Periode Awal</label>
                    <input type="date" name="start_date" id="start_date" value="{{ request('start_date') }}"
                           class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div class="flex-1">
                    <label for="end_date" class="block text-sm font-medium text-gray-700">Periode Akhir</label>
                    <input type="date" name="end_date" id="end_date" value="{{ request('end_date') }}"
                           class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg shadow-md hover:bg-blue-700 transition duration-150">
                    <i class="fas fa-filter mr-1"></i> Terapkan Filter
                </button>
                <a href="{{ route('admin.riwayat_transaksi.index') }}" class="text-gray-500 hover:text-red-600 px-4 py-2">
                    Reset
                </a>
            </form>
        </div>

        {{-- ========================================= --}}
        {{-- KONTEN UTAMA DENGAN DUA TIPE DISPLAY --}}
        {{-- ========================================= --}}

        @if ($dataTransaksiPerWarung->isEmpty())
            <div class="bg-blue-100 border-l-4 border-blue-500 text-blue-700 p-4" role="alert">
                <p class="font-bold">Info</p>
                <p>Tidak ada warung atau transaksi yang ditemukan dalam periode ini.</p>
            </div>
        @else

            {{-- Container untuk Mode Compact (Horizontal Scroll) --}}
            <div id="container-compact" class="flex overflow-x-auto pb-4 -mx-6 px-6 space-x-6" style="{{ $isCompact ? '' : 'display: none;' }}">
                @foreach ($dataTransaksiPerWarung as $dataWarung)
                    <div class="warung-container flex-shrink-0 w-80 bg-white shadow-xl border border-gray-200 rounded-lg flex flex-col"
                         data-warung-id="{{ $dataWarung['id'] }}">

                        {{-- Header Warung & Pencarian JS --}}
                        <div class="p-4 border-b">
                            <h2 class="text-lg font-bold text-gray-800 flex items-center mb-2">
                                <i class="fas fa-store mr-2 text-green-600"></i>
                                **{{ $dataWarung['nama_warung'] }}**
                            </h2>
                            <div class="text-sm font-semibold mb-3">
                                Kas:
                                <span class="ml-1 {{ $dataWarung['total_kas_warung'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                    Rp. {{ number_format($dataWarung['total_kas_warung'], 2, ',', '.') }}
                                </span>
                            </div>

                            <input type="text" placeholder="Cari di warung ini (JS Search)"
                                   class="search-input w-full border border-gray-300 rounded-md px-3 py-1 text-sm focus:ring-blue-500 focus:border-blue-500"
                                   data-target="#transaksi-list-{{ $dataWarung['id'] }}" data-mode="compact">
                        </div>

                        {{-- List Transaksi (Scroll Vertikal) --}}
                        <div class="p-4 flex-1 overflow-y-auto max-h-[600px] space-y-3" id="transaksi-list-{{ $dataWarung['id'] }}">
                            {{-- Item Transaksi Compact --}}
                            @forelse ($dataWarung['riwayat_transaksi'] as $transaksi)
                                <div class="transaksi-item border-l-4 p-3 rounded-md shadow-sm text-sm transition duration-150 ease-in-out
                                    @if ((float)$transaksi->total >= 0)
                                        border-green-500 bg-green-50 hover:bg-green-100
                                    @else
                                        border-red-500 bg-red-50 hover:bg-red-100
                                    @endif"
                                    data-search-term="{{ strtolower($transaksi->deskripsi . ' ' . $transaksi->jenis_transaksi . ' ' . $transaksi->metode_pembayaran) }}">

                                    <div class="flex justify-between items-center mb-1">
                                        <span class="font-semibold {{ (float)$transaksi->total >= 0 ? 'text-green-700' : 'text-red-700' }}">
                                            Rp. {{ number_format((float)$transaksi->total, 2, ',', '.') }}
                                        </span>
                                        <span class="text-xs text-gray-500">{{ $transaksi->tanggal->translatedFormat('d/m H:i') }}</span>
                                    </div>
                                    <p class="font-medium text-gray-800">{{ $transaksi->deskripsi }}</p>
                                    <div class="mt-1 text-xs space-x-2">
                                        <span class="px-2 py-0.5 rounded-full bg-gray-200 text-gray-600">{{ $transaksi->jenis_transaksi }}</span>
                                        <span class="px-2 py-0.5 rounded-full bg-indigo-100 text-indigo-600">{{ $transaksi->metode_pembayaran }}</span>
                                    </div>
                                </div>
                            @empty
                                <div class="empty-row text-center py-6 text-gray-500">
                                    <i class="fas fa-box-open mb-2"></i>
                                    <p>Tidak ada transaksi dalam periode ini.</p>
                                </div>
                            @endforelse
                            <div class="not-found-message" style="display: none; text-align: center; padding: 1.5rem; color: #f59e0b; font-weight: 600;">
                                <i class="fas fa-exclamation-circle mb-2"></i>
                                <p>Transaksi tidak ditemukan.</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Container untuk Mode DETAIL (Vertikal List Penuh) --}}
        <div id="container-detail" class="space-y-8" style="{{ $isCompact ? 'display: none;' : '' }}">

            {{-- Gabungkan Semua Transaksi dari Semua Warung menjadi Satu List --}}
            @php
                $allTransactions = collect();
                foreach ($dataTransaksiPerWarung as $dataWarung) {
                    // Tambahkan nama warung ke setiap objek transaksi
                    $dataWarung['riwayat_transaksi']->each(function ($transaksi) use ($dataWarung) {
                        $transaksi->nama_warung = $dataWarung['nama_warung'];
                    });
                    $allTransactions = $allTransactions->merge($dataWarung['riwayat_transaksi']);
                }

                // Urutkan ulang berdasarkan tanggal/waktu transaksi terbaru (Descending)
                $allTransactions = $allTransactions->sortByDesc('tanggal');

                // Jika ada filter pencarian, terapkan pada koleksi ini juga
                $searchTerm = strtolower(request('search')); // Jika Anda menambahkan search utama

                /*
                // Jika Anda ingin mengaktifkan search utama (tidak hanya JS), gunakan kode ini:
                if (!empty($searchTerm)) {
                     $allTransactions = $allTransactions->filter(function($transaksi) use ($searchTerm) {
                        return str_contains(strtolower($transaksi->deskripsi), $searchTerm) ||
                               str_contains(strtolower($transaksi->jenis_transaksi), $searchTerm) ||
                               str_contains(strtolower($transaksi->metode_pembayaran), $searchTerm) ||
                               str_contains(strtolower($transaksi->nama_warung), $searchTerm);
                    });
                }
                */
            @endphp

            <div class="bg-white shadow-xl border border-gray-200 rounded-lg p-6">
                <h2 class="text-2xl font-bold text-gray-800 mb-4 pb-2 border-b">
                    <i class="fas fa-list-alt mr-3 text-indigo-600"></i> Riwayat Transaksi Global (Semua Warung)
                </h2>

                {{-- Input Pencarian Global (Opsional, jika ingin diaktifkan di Controller/Form Filter) --}}
                <input type="text" placeholder="Cari di semua transaksi (JS Search Global)"
                       class="search-input w-full border border-gray-300 rounded-md px-4 py-2 mb-4 focus:ring-blue-500 focus:border-blue-500"
                       data-target="#global-transaksi-table" data-mode="detail-global">

                {{-- Tabel Detail Transaksi --}}
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm text-gray-600 border rounded-lg overflow-hidden" id="global-transaksi-table">
                        <thead class="bg-gray-100 text-gray-700 uppercase text-xs sticky top-0">
                            <tr>
                                <th class="py-3 px-4 text-left">Tanggal/Waktu</th>
                                <th class="py-3 px-4 text-left">Nama Warung</th> {{-- Kolom Baru --}}
                                <th class="py-3 px-4 text-left">Jenis Transaksi</th>
                                <th class="py-3 px-4 text-left">Deskripsi / Keterangan</th>
                                <th class="py-3 px-4 text-center">Metode</th>
                                <th class="py-3 px-4 text-right">Total (Rp)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($allTransactions as $transaksi)
                                <tr class="transaksi-item border-b hover:bg-gray-50"
                                    data-search-term="{{ strtolower($transaksi->deskripsi . ' ' . $transaksi->jenis_transaksi . ' ' . $transaksi->metode_pembayaran . ' ' . $transaksi->nama_warung) }}">
                                    <td class="py-3 px-4 text-xs font-semibold">{{ $transaksi->tanggal->translatedFormat('d M Y H:i') }}</td>
                                    <td class="py-3 px-4 font-bold text-gray-700">{{ $transaksi->nama_warung }}</td> {{-- Nilai Warung --}}
                                    <td class="py-3 px-4">
                                        <span class="px-2 py-0.5 text-xs rounded-full font-medium bg-indigo-100 text-indigo-700">
                                            {{ $transaksi->jenis_transaksi }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-4 max-w-lg overflow-hidden text-ellipsis">{{ $transaksi->deskripsi }}</td>
                                    <td class="py-3 px-4 text-center">{{ $transaksi->metode_pembayaran }}</td>
                                    <td class="py-3 px-4 text-right font-bold {{ (float)$transaksi->total >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                        {{ number_format((float)$transaksi->total, 2, ',', '.') }}
                                    </td>
                                </tr>
                            @empty
                                <tr class="empty-row"><td colspan="6" class="text-center py-4 text-gray-500">Tidak ada transaksi dalam periode ini.</td></tr>
                            @endforelse
                            <tr class="not-found-message" style="display: none;"><td colspan="6" class="text-center py-4 text-yellow-500 font-semibold">Transaksi tidak ditemukan.</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif
    </main>
</div>

{{-- ========================================= --}}
{{-- SCRIPT JS UNTUK PENCARIAN DAN TOGGLE VIEW --}}
{{-- ========================================= --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const compactContainer = document.getElementById('container-compact');
        const detailContainer = document.getElementById('container-detail');
        const viewToggles = document.querySelectorAll('.view-toggle');
        const viewModeInput = document.getElementById('view-mode-input');
        const filterForm = document.getElementById('filter-form');
        const LS_KEY = 'riwayat_transaksi_view_mode';

        /**
         * 1. Logic Toggle View dan Local Storage
         */

        function updateView(mode) {
            // Update tampilan container
            compactContainer.style.display = mode === 'compact' ? 'flex' : 'none';
            detailContainer.style.display = mode === 'detail' ? 'block' : 'none';

            // Update button active state
            viewToggles.forEach(btn => {
                const isCurrent = btn.getAttribute('data-mode') === mode;
                btn.classList.toggle('bg-indigo-600', isCurrent);
                btn.classList.toggle('text-white', isCurrent);
                btn.classList.toggle('shadow-md', isCurrent);
                btn.classList.toggle('text-gray-700', !isCurrent);
                btn.classList.toggle('hover:bg-gray-300', !isCurrent);
            });

            // Simpan mode ke Local Storage dan update form input hidden
            localStorage.setItem(LS_KEY, mode);
            viewModeInput.value = mode;

            // Pastikan input pencarian yang tidak aktif direset/dikosongkan
            document.querySelectorAll('.search-input').forEach(input => {
                if (input.getAttribute('data-mode') !== mode) {
                    input.value = ''; // Reset input di mode yang tidak aktif
                }
            });
        }

        viewToggles.forEach(btn => {
            btn.addEventListener('click', function() {
                const mode = this.getAttribute('data-mode');
                // Mengganti view tanpa reload (kecuali saat filter)
                updateView(mode);
            });
        });

        // Inisialisasi: Baca dari Local Storage atau parameter URL
        let initialMode = viewModeInput.value;
        const storedMode = localStorage.getItem(LS_KEY);

        if (!initialMode && storedMode) {
            // Jika tidak ada parameter URL tapi ada di LS
            initialMode = storedMode;
        } else if (!initialMode) {
            // Default jika tidak ada keduanya
            initialMode = 'compact';
        }

        updateView(initialMode);


        /**
         * 2. Logic Pencarian JS Per Warung
         */
        const searchInputs = document.querySelectorAll('.search-input');

        searchInputs.forEach(input => {
            input.addEventListener('keyup', function() {
                const searchTerm = this.value.toLowerCase().trim();
                const targetId = this.getAttribute('data-target');
                const targetList = document.querySelector(targetId);

                // Cari item transaksi yang visibility-nya saat ini 'block' atau 'table-row'
                const items = targetList.querySelectorAll('.transaksi-item');
                let foundCount = 0;

                items.forEach(item => {
                    const itemText = item.getAttribute('data-search-term');
                    const isVisible = itemText && itemText.includes(searchTerm);

                    if (isVisible) {
                        // Tentukan display style: 'block' untuk compact, 'table-row' untuk detail
                        item.style.display = targetList.tagName === 'TABLE' ? 'table-row' : 'block';
                        foundCount++;
                    } else {
                        item.style.display = 'none';
                    }
                });

                // Handle pesan "Not Found"
                let notFoundMessage = targetList.querySelector('.not-found-message');
                const emptyRow = targetList.querySelector('.empty-row');

                if (emptyRow) emptyRow.style.display = 'none'; // Sembunyikan pesan empty saat searching

                if (notFoundMessage) {
                    if (foundCount === 0 && items.length > 0) {
                        notFoundMessage.style.display = targetList.tagName === 'TABLE' ? 'table-row' : 'block';
                    } else {
                        notFoundMessage.style.display = 'none';
                    }
                }
            });
        });
    });
</script>
@endsection
