@extends('layouts.admin')

@section('title', 'Riwayat Hutang Semua Warung')

@section('content')
@php
    // Inisialisasi variabel yang mungkin belum ada di query string atau dari controller
    // Default mode adalah 'compact' jika tidak ada di URL
    $viewMode = request('view', 'compact');
    $isCompact = $viewMode === 'compact';
    $status = request('status', '');

    // Data dummy untuk pengembangan jika belum ada dari controller
    // AdminHutangController hanya mengirim $hutangList dan $status.
    // Kita perlu menyesuaikan controller atau membuat data ini di sini.
    // Untuk tujuan visualisasi, saya asumsikan data ini ada:
    $totalHutangGlobal = $totalHutangGlobal ?? $hutangList->sum('jumlah_hutang_awal'); // Total seluruh hutang awal
    $totalLunasGlobal = $totalLunasGlobal ?? ($totalHutangGlobal - $hutangList->sum('jumlah_sisa_hutang')); // Total yang sudah dibayar

    // Untuk mode compact, data harus dikelompokkan.
    // Di lingkungan nyata, ini harus dilakukan di Controller untuk efisiensi.
    // Untuk demo view, kita buat dummy grouping sederhana:
    if (!isset($dataHutangPerWarung)) {
        $dataHutangPerWarung = $hutangList->groupBy('warung.nama')->map(function ($items, $warungName) {
            $warungId = $items->first()->id_warung;
            $totalHutangWarung = $items->sum('jumlah_hutang_awal');
            $totalSisaWarung = $items->sum('jumlah_sisa_hutang');
            return [
                'id' => $warungId,
                'nama_warung' => $warungName,
                'total_hutang_warung' => $totalHutangWarung,
                'total_lunas_warung' => $totalHutangWarung - $totalSisaWarung,
                'hutang_list' => $items,
            ];
        })->values();
    }
@endphp

<div class="flex-1 flex flex-col overflow-hidden bg-gray-100">

    {{-- Header --}}
    <header class="flex justify-between items-center p-6 bg-white border-b sticky top-0 z-10">
        <h1 class="text-2xl font-bold text-gray-800">
            <i class="fas fa-hand-holding-usd mr-2 text-red-600"></i> Daftar Piutang Pelanggan (Admin View)
        </h1>

        {{-- Opsi Tampilan (Button Toggle) --}}
        <div class="flex space-x-2 p-1 bg-gray-200 rounded-lg shadow-inner">
            <button id="toggle-compact" data-mode="compact"
                class="px-3 py-1 text-sm font-semibold rounded-lg transition duration-150 view-toggle">
                <i class="fas fa-columns mr-1"></i> Tampilan Compact (Per Warung)
            </button>
            <button id="toggle-detail" data-mode="detail"
                class="px-3 py-1 text-sm font-semibold rounded-lg transition duration-150 view-toggle">
                <i class="fas fa-list-ul mr-1"></i> Tampilan Detail (Tabel Global)
            </button>
        </div>
    </header>

    <main class="flex-1 overflow-y-auto p-6 space-y-6">

        {{-- ========================================= --}}
        {{-- FILTER DAN STATISTIK GLOBAL --}}
        {{-- ========================================= --}}
        <div class="bg-white shadow-lg rounded-xl p-6 border-t-4 border-red-500">
            <form action="{{ route('admin.hutang.index') }}" method="GET" id="filter-form">
                {{-- Hidden input untuk mempertahankan mode tampilan saat filter dikirim --}}
                <input type="hidden" name="view" value="{{ $viewMode }}" id="view-mode-input">

                <div class="flex flex-wrap items-end gap-4">
                    {{-- Filter Status --}}
                    <div class="w-full sm:w-1/5">
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status Hutang</label>
                        <select name="status" id="status"
                            class="block w-full border border-gray-300 rounded-lg shadow-sm p-2 text-sm focus:ring-red-500 focus:border-red-500">
                            <option value="">Semua Status</option>
                            <option value="belum_lunas" {{ $status === 'belum_lunas' ? 'selected' : '' }}>Belum Lunas</option>
                            <option value="lunas" {{ $status === 'lunas' ? 'selected' : '' }}>Lunas</option>
                        </select>
                    </div>

                    {{-- Search Global --}}
                    <div class="w-full sm:w-1/5">
                        <label for="q" class="block text-sm font-medium text-gray-700 mb-1">Cari User</label>
                        <input type="text" name="q" id="q" value="{{ request('q') }}" placeholder="Nama pelanggan..."
                            class="block w-full border border-gray-300 rounded-lg shadow-sm p-2 text-sm focus:ring-red-500 focus:border-red-500">
                    </div>

                    {{-- Filter Expired (Untuk kebutuhan backend, ini perlu ditambahkan ke Controller) --}}
                    <div class="flex items-center h-full pt-6">
                        <input type="checkbox" name="expired" id="expired" value="1" {{ request('expired') ? 'checked' : '' }}
                                class="h-4 w-4 text-red-600 border-gray-300 rounded focus:ring-red-500">
                        <label for="expired" class="ml-2 block text-sm font-medium text-gray-700">Hanya Jatuh Tempo</label>
                    </div>

                    <div class="flex space-x-2 ml-auto">
                        <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded-lg shadow-md hover:bg-red-700 transition duration-150 font-semibold">
                            <i class="fas fa-filter mr-1"></i> Terapkan Filter
                        </button>
                        <a href="{{ route('admin.hutang.index') }}" class="text-gray-500 hover:text-red-600 px-4 py-2 border border-gray-300 rounded-lg shadow-sm transition duration-150">
                            Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>

        {{-- Kartu Statistik Ringkasan Global --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white p-5 rounded-xl shadow-lg border-l-4 border-red-500">
                <p class="text-sm text-gray-500 font-medium">Total Seluruh Piutang</p>
                <p class="text-2xl font-extrabold text-red-600 mt-1">
                    Rp. {{ number_format($totalHutangGlobal, 0, ',', '.') }}
                </p>
            </div>
            <div class="bg-white p-5 rounded-xl shadow-lg border-l-4 border-yellow-500">
                <p class="text-sm text-gray-500 font-medium">Sisa Piutang (Belum Lunas)</p>
                <p class="text-2xl font-extrabold text-yellow-700 mt-1">
                    Rp. {{ number_format($totalHutangGlobal - $totalLunasGlobal, 0, ',', '.') }}
                </p>
            </div>
            <div class="bg-white p-5 rounded-xl shadow-lg border-l-4 border-green-500">
                <p class="text-sm text-gray-500 font-medium">Total Dibayar</p>
                <p class="text-2xl font-extrabold text-green-700 mt-1">
                    Rp. {{ number_format($totalLunasGlobal, 0, ',', '.') }}
                </p>
            </div>
        </div>


        {{-- ========================================= --}}
        {{-- KONTEN UTAMA DENGAN DUA TIPE DISPLAY --}}
        {{-- ========================================= --}}

        @if ($dataHutangPerWarung->isEmpty() && $hutangList->isEmpty())
            <div class="bg-blue-100 border-l-4 border-blue-500 text-blue-700 p-6 rounded-lg shadow-md" role="alert">
                <p class="font-bold text-xl mb-1">Informasi</p>
                <p>Tidak ada data hutang yang ditemukan dengan filter ini.</p>
            </div>
        @else

            {{-- Container untuk Mode Compact (Horizontal Scroll) --}}
            <div id="container-compact" class="flex overflow-x-auto pb-4 -mx-6 px-6 space-x-6 scrollbar-thin scrollbar-thumb-red-300 scrollbar-track-red-100" style="{{ $isCompact ? '' : 'display: none;' }}">
                @forelse ($dataHutangPerWarung as $dataWarung)
                    @php
                        $sisaWarung = $dataWarung['total_hutang_warung'] - $dataWarung['total_lunas_warung'];
                        $isExpired = $dataWarung['hutang_list']->where('tenggat', '<', now())->where('status', 'belum_lunas')->count() > 0;
                    @endphp
                    <div class="warung-container flex-shrink-0 w-80 bg-white shadow-xl border-t-8 rounded-xl flex flex-col transition-all duration-300
                         {{ $isExpired ? 'border-yellow-500' : 'border-indigo-500' }}"
                          data-warung-id="{{ $dataWarung['id'] }}">

                        {{-- Header Warung --}}
                        <div class="p-4 border-b bg-gray-50">
                            <h2 class="text-lg font-bold text-gray-800 flex items-center mb-1">
                                <i class="fas fa-store mr-2 {{ $isExpired ? 'text-yellow-600' : 'text-indigo-600' }}"></i>
                                **{{ $dataWarung['nama_warung'] }}**
                            </h2>
                            <p class="text-xs text-gray-500 mb-3">Total {{ $dataWarung['hutang_list']->count() }} transaksi hutang</p>

                            <div class="text-base font-extrabold">
                                Sisa Piutang:
                                <span class="ml-1 {{ $sisaWarung > 0 ? 'text-red-600' : 'text-green-600' }}">
                                    Rp. {{ number_format($sisaWarung, 0, ',', '.') }}
                                </span>
                            </div>

                            <input type="text" placeholder="Cari user di warung ini (JS Search)"
                                    class="search-input compact-search w-full border border-gray-300 rounded-lg px-3 py-2 text-sm mt-3 focus:ring-indigo-500 focus:border-indigo-500"
                                    data-target="#hutang-list-{{ $dataWarung['id'] }}" data-mode="compact">
                        </div>

                        {{-- List Hutang (Scroll Vertikal) --}}
                        <div class="p-4 flex-1 overflow-y-auto max-h-[500px] space-y-3 custom-scrollbar" id="hutang-list-{{ $dataWarung['id'] }}">
                            {{-- Item Hutang Compact --}}
                            @forelse ($dataWarung['hutang_list'] as $hutang)
                                <a href="{{ route('admin.hutang.detail', $hutang->id) }}"
                                   class="hutang-item border-l-4 p-3 rounded-lg shadow-sm bg-white block transition duration-150 ease-in-out hover:shadow-md
                                       @if ($hutang->status === 'lunas')
                                           border-green-500 hover:bg-green-50
                                       @elseif ($hutang->tenggat && $hutang->tenggat->isPast() && $hutang->status === 'belum_lunas')
                                           border-yellow-500 hover:bg-yellow-50
                                       @else
                                           border-red-500 hover:bg-red-50
                                       @endif"
                                    data-search-term="{{ strtolower($hutang->user->name . ' ' . $hutang->status . ' ' . ($hutang->keterangan ?? '')) }}">

                                    <div class="flex justify-between items-start mb-1">
                                        <span class="font-semibold text-gray-800 text-base truncate">
                                            {{ $hutang->user->name }}
                                        </span>
                                        <span class="text-xs text-gray-500 flex-shrink-0 ml-2">
                                            <i class="fas fa-calendar-alt mr-1"></i>
                                            {{ $hutang->tenggat ? $hutang->tenggat->translatedFormat('d M Y') : 'Tanpa Tenggat' }}
                                        </span>
                                    </div>
                                    <p class="font-bold text-lg {{ $hutang->jumlah_sisa_hutang > 0 ? 'text-red-700' : 'text-green-700' }}">
                                        Sisa: Rp. {{ number_format($hutang->jumlah_sisa_hutang, 0, ',', '.') }}
                                    </p>
                                    <div class="mt-1 text-xs space-x-2">
                                        <span class="px-2 py-0.5 rounded-full font-medium {{ $hutang->status === 'lunas' ? 'bg-green-200 text-green-800' : 'bg-red-200 text-red-800' }}">
                                            {{ $hutang->status === 'lunas' ? 'LUNAS' : 'BELUM LUNAS' }}
                                        </span>
                                        @if ($hutang->tenggat && $hutang->tenggat->isPast() && $hutang->status === 'belum_lunas')
                                            <span class="px-2 py-0.5 rounded-full bg-yellow-400 text-yellow-900 font-bold shadow-sm">JATUH TEMPO</span>
                                        @endif
                                    </div>
                                </a>
                            @empty
                                <div class="empty-row text-center py-6 text-gray-500">
                                    <i class="fas fa-box-open text-2xl mb-2"></i>
                                    <p>Warung ini bersih dari hutang (dengan filter saat ini).</p>
                                </div>
                            @endforelse
                            <div class="not-found-message text-center py-6 text-yellow-600 font-semibold" style="display: none;">
                                <i class="fas fa-exclamation-circle mb-2"></i>
                                <p>Hutang tidak ditemukan di warung ini.</p>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="w-full bg-blue-100 border-l-4 border-blue-500 text-blue-700 p-6 rounded-lg shadow-md -mx-2" role="alert">
                        <p class="font-bold text-xl mb-1">Informasi</p>
                        <p>Tidak ada warung yang memiliki hutang dengan filter yang diterapkan.</p>
                    </div>
                @endforelse
            </div>

            {{-- Container untuk Mode DETAIL (Vertikal List Penuh) --}}
            <div id="container-detail" class="space-y-6" style="{{ $isCompact ? 'display: none;' : '' }}">

                <div class="bg-white shadow-xl border border-gray-200 rounded-xl p-6">
                    <h2 class="text-xl font-bold text-gray-800 mb-4 pb-2 border-b-2 border-red-100">
                        <i class="fas fa-table mr-3 text-red-600"></i> Tabel Riwayat Hutang Global
                    </h2>

                    {{-- Pagination (Laravel Pagination Links) --}}
                    <div class="mb-4">
                        {{ $hutangList->appends(request()->query())->links() }}
                    </div>

                    {{-- Tabel Detail Hutang --}}
                    <div class="overflow-x-auto rounded-lg border">
                        <table class="min-w-full text-sm text-gray-600 border-collapse" id="global-transaksi-table">
                            <thead class="bg-gray-100 text-gray-700 uppercase text-xs sticky top-0 shadow-sm">
                                <tr>
                                    <th class="py-3 px-4 text-left whitespace-nowrap">Warung</th>
                                    <th class="py-3 px-4 text-left whitespace-nowrap">Pelanggan</th>
                                    <th class="py-3 px-4 text-left whitespace-nowrap">Tanggal Tenggat</th>
                                    <th class="py-3 px-4 text-right whitespace-nowrap">Hutang Awal (Rp)</th>
                                    <th class="py-3 px-4 text-right whitespace-nowrap">Sisa Hutang (Rp)</th>
                                    <th class="py-3 px-4 text-center whitespace-nowrap">Status</th>
                                    <th class="py-3 px-4 text-center whitespace-nowrap">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($hutangList as $hutang)
                                    <tr class="transaksi-item border-b hover:bg-red-50 transition duration-150"
                                        data-search-term="{{ strtolower($hutang->user->name . ' ' . $hutang->warung->nama_warung . ' ' . $hutang->status) }}">
                                        <td class="py-3 px-4 font-bold text-indigo-600">{{ $hutang->warung->nama_warung ?? 'N/A' }}</td>
                                        <td class="py-3 px-4 text-gray-700 font-medium">{{ $hutang->user->name ?? 'User Dihapus' }}</td>
                                        <td class="py-3 px-4 text-xs font-semibold whitespace-nowrap">
                                            @if ($hutang->tenggat)
                                                <span class="{{ $hutang->tenggat->isPast() && $hutang->jumlah_sisa_hutang > 0 ? 'text-red-500 font-bold' : 'text-gray-600' }}">
                                                    {{ $hutang->tenggat->translatedFormat('d M Y') }}
                                                </span>
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td class="py-3 px-4 text-right text-gray-800">{{ number_format($hutang->jumlah_hutang_awal, 0, ',', '.') }}</td>
                                        <td class="py-3 px-4 text-right font-extrabold whitespace-nowrap {{ $hutang->jumlah_sisa_hutang > 0 ? 'text-red-700' : 'text-green-700' }}">
                                            {{ number_format($hutang->jumlah_sisa_hutang, 0, ',', '.') }}
                                        </td>
                                        <td class="py-3 px-4 text-center">
                                            <span class="px-2 py-0.5 text-xs rounded-full font-bold
                                                {{ $hutang->status === 'lunas' ? 'bg-green-200 text-green-800' : 'bg-red-200 text-red-800' }}">
                                                {{ strtoupper(str_replace('_', ' ', $hutang->status)) }}
                                            </span>
                                        </td>
                                        <td class="py-3 px-4 text-center">
                                            <a href="{{ route('admin.hutang.detail', $hutang->id) }}" class="text-indigo-600 hover:text-indigo-800 font-semibold text-sm hover:underline">
                                                <i class="fas fa-info-circle"></i> Detail
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr class="empty-row"><td colspan="7" class="text-center py-6 text-gray-500">Tidak ada hutang yang ditemukan dengan filter ini.</td></tr>
                                @endforelse
                                <tr class="not-found-message" style="display: none;"><td colspan="7" class="text-center py-4 text-yellow-600 font-semibold">Hasil pencarian tidak ditemukan.</td></tr>
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
        const LS_KEY = 'admin_hutang_view_mode';

        // --- Styles for custom scrollbar (optional but good for UX) ---
        const style = document.createElement('style');
        style.textContent = `
            .custom-scrollbar::-webkit-scrollbar {
                width: 8px;
            }
            .custom-scrollbar::-webkit-scrollbar-thumb {
                background-color: #fca5a5; /* red-300 */
                border-radius: 4px;
            }
            .custom-scrollbar::-webkit-scrollbar-track {
                background-color: #fef2f2; /* red-50 */
            }
        `;
        document.head.append(style);

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
                // Menggunakan kelas merah/Red untuk tema hutang
                btn.classList.toggle('bg-red-600', isCurrent);
                btn.classList.toggle('text-white', isCurrent);
                btn.classList.toggle('shadow-md', isCurrent);
                btn.classList.toggle('text-gray-700', !isCurrent);
                btn.classList.toggle('hover:bg-gray-300', !isCurrent);
            });

            // Simpan mode ke Local Storage dan update form input hidden
            localStorage.setItem(LS_KEY, mode);
            viewModeInput.value = mode;
        }

        viewToggles.forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const mode = this.getAttribute('data-mode');

                // Construct new URL with the new view mode
                const url = new URL(window.location.href);
                url.searchParams.set('view', mode);

                // Reload the page to apply the filter/view changes and re-render with server data
                window.location.href = url.toString();
            });
        });

        // Inisialisasi: Baca dari URL, jika tidak ada, gunakan Local Storage, jika tidak ada, gunakan default 'compact'
        let initialMode = viewModeInput.value;
        const storedMode = localStorage.getItem(LS_KEY);

        if (!initialMode) {
            initialMode = storedMode || 'compact';
        }

        // Memastikan tampilan sesuai parameter URL atau Local Storage saat pertama load
        updateView(initialMode);


        /**
         * 2. Logic Pencarian JS Per Warung (Compact Mode Only)
         */
        document.querySelectorAll('.compact-search').forEach(input => {
            input.addEventListener('keyup', function() {
                const searchTerm = this.value.toLowerCase().trim();
                const targetId = this.getAttribute('data-target');
                const targetList = document.querySelector(targetId);

                // Cari item hutang
                const items = targetList.querySelectorAll('.hutang-item');
                let foundCount = 0;

                items.forEach(item => {
                    const itemText = item.getAttribute('data-search-term');
                    const isVisible = itemText && itemText.includes(searchTerm);

                    if (isVisible) {
                        item.style.display = 'block';
                        foundCount++;
                    } else {
                        item.style.display = 'none';
                    }
                });

                // Handle pesan "Not Found" di dalam warung
                const notFoundMessage = targetList.querySelector('.not-found-message');
                const emptyRow = targetList.querySelector('.empty-row'); // Pesan 'Tidak ada hutang' awal

                if (emptyRow) emptyRow.style.display = 'none'; // Sembunyikan pesan empty saat searching

                if (notFoundMessage) {
                    if (foundCount === 0 && items.length > 0) {
                        notFoundMessage.style.display = 'block';
                    } else {
                        notFoundMessage.style.display = 'none';
                        // Tampilkan kembali pesan empty jika search term kosong dan tidak ada filter lain
                        if (searchTerm === '' && items.length === 0) {
                             if (emptyRow) emptyRow.style.display = 'block';
                        }
                    }
                }
            });
        });
    });
</script>
@endsection
