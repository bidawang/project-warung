@extends('layouts.admin')

@section('title', 'Riwayat Hutang Semua Warung')

@section('content')
@php
    // Variabel yang dilewatkan dari Controller: $hutangList, $status, $aturanTenggats, $allWarungs
    $viewMode = request('view', 'detail');
    $isCompact = $viewMode === 'detail';
    $status = request('status', '');

    // Perhitungan Ringkasan Global
    $totalHutangGlobal = $hutangList->sum('jumlah_hutang_awal');
    $totalSisaGlobal = $hutangList->sum('jumlah_sisa_hutang');
    $totalLunasGlobal = $totalHutangGlobal - $totalSisaGlobal;

    // Pengelompokan Hutang Per Warung (untuk Mode Compact)
    $dataHutangPerWarung = $hutangList->groupBy('warung.nama_warung')->map(function ($items, $warungName) {
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
                class="px-3 py-1 text-sm font-semibold rounded-lg transition duration-150 view-toggle {{ $isCompact ? 'bg-white text-indigo-600 shadow' : 'text-gray-600 hover:bg-gray-300' }}">
                <i class="fas fa-columns mr-1"></i> Tampilan Compact (Per Warung)
            </button>
            <button id="toggle-detail" data-mode="detail"
                class="px-3 py-1 text-sm font-semibold rounded-lg transition duration-150 view-toggle {{ !$isCompact ? 'bg-white text-red-600 shadow' : 'text-gray-600 hover:bg-gray-300' }}">
                <i class="fas fa-list-ul mr-1"></i> Tampilan Detail (Tabel Global)
            </button>
        </div>
    </header>

    <main class="flex-1 overflow-y-auto p-6 space-y-6">

        {{-- Notifikasi Global (SUCCESS/ERROR dari Controller) --}}
        @if (session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-lg shadow-md" role="alert">
                <p>{{ session('success') }}</p>
            </div>
        @endif
        @if (session('error'))
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-lg shadow-md" role="alert">
                <p>{{ session('error') }}</p>
            </div>
        @endif
        @if ($errors->any())
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-lg shadow-md" role="alert">
                <p class="font-bold">Ada masalah validasi:</p>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- ========================================= --}}
        {{-- MANAJEMEN ATURAN TENGGAT WARUNG --}}
        {{-- ========================================= --}}
        <div class="bg-white shadow-xl border border-gray-200 rounded-xl p-6">
            <div class="flex justify-between items-center mb-4 pb-2 border-b-2 border-indigo-100">
                <h2 class="text-xl font-bold text-gray-800">
                    <i class="fas fa-calendar-times mr-3 text-indigo-600"></i> Manajemen Aturan Tenggat Warung
                </h2>
                <button type="button" onclick="openAturanModal('create')"
                    class="bg-indigo-600 text-white px-4 py-2 rounded-lg shadow-md hover:bg-indigo-700 transition duration-150 font-semibold">
                    <i class="fas fa-plus mr-1"></i> Tambah Aturan
                </button>
            </div>

            {{-- Tabel Aturan Tenggat --}}
            <div class="overflow-x-auto rounded-lg border">
                <table class="min-w-full text-sm text-gray-600 border-collapse">
                    <thead class="bg-gray-100 text-gray-700 uppercase text-xs">
                        <tr>
                            <th class="py-3 px-4 text-left whitespace-nowrap">Warung</th>
                            {{-- Ubah format tampilan tanggal --}}
                            <th class="py-3 px-4 text-left whitespace-nowrap">Periode Awal (Tgl)</th>
                            <th class="py-3 px-4 text-left whitespace-nowrap">Periode Akhir (Tgl)</th>
                            <th class="py-3 px-4 text-center whitespace-nowrap">Jatuh Tempo (Hari)</th>
                            <th class="py-3 px-4 text-center whitespace-nowrap">Bunga (%)</th>
                            <th class="py-3 px-4 text-left">Keterangan</th>
                            <th class="py-3 px-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($aturanTenggats as $aturan)
                            <tr class="border-b hover:bg-indigo-50 transition duration-150">
                                <td class="py-3 px-4 font-bold text-indigo-600">{{ $aturan->warung->nama_warung ?? 'N/A' }}</td>
                                {{-- Hanya tampilkan angka tanggal karena ini adalah aturan tgl harian --}}
                                <td class="py-3 px-4 text-gray-700">{{ $aturan->tanggal_awal }}</td>
                                <td class="py-3 px-4 text-gray-700">{{ $aturan->tanggal_akhir }}</td>
                                <td class="py-3 px-4 text-center font-medium">{{ $aturan->jatuh_tempo_hari }}</td>
                                <td class="py-3 px-4 text-center font-extrabold text-red-600">{{ $aturan->bunga }}%</td>
                                <td class="py-3 px-4 text-gray-500 text-xs">{{ $aturan->keterangan ?? '-' }}</td>
                                <td class="py-3 px-4 text-center whitespace-nowrap space-x-2">
                                    <button type="button"
                                        onclick="openAturanModal('edit', {
                                            id: {{ $aturan->id }},
                                            id_warung: {{ $aturan->id_warung }},
                                            tanggal_awal: {{ $aturan->tanggal_awal }},
                                            tanggal_akhir: {{ $aturan->tanggal_akhir }},
                                            jatuh_tempo_hari: {{ $aturan->jatuh_tempo_hari }},
                                            bunga: {{ $aturan->bunga }},
                                            keterangan: '{{ $aturan->keterangan ?? '' }}',
                                            warung: { nama_warung: '{{ $aturan->warung->nama_warung ?? 'N/A' }}' }
                                        })"
                                        class="text-green-600 hover:text-green-800 font-semibold text-sm hover:underline">
                                        <i class="fas fa-edit"></i> Edit
                                    </button>
                                    <button type="button" onclick="deleteAturan({{ $aturan->id }})"
                                        class="text-red-600 hover:text-red-800 font-semibold text-sm hover:underline ml-2">
                                        <i class="fas fa-trash"></i> Hapus
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="text-center py-6 text-gray-500">Belum ada aturan tenggat yang ditambahkan.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        {{-- ========================================= --}}
        {{-- FILTER DAN STATISTIK GLOBAL (TIDAK BERUBAH) --}}
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

                    {{-- Filter Expired --}}
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

        {{-- Kartu Statistik Ringkasan Global (TIDAK BERUBAH) --}}
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
                    Rp. {{ number_format($totalSisaGlobal, 0, ',', '.') }}
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
        {{-- KONTEN UTAMA DENGAN DUA TIPE DISPLAY (TIDAK BERUBAH) --}}
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
{{-- MODAL TAMBAH/EDIT ATURAN TENGGAT --}}
{{-- Diperbarui untuk menampilkan Error Validasi --}}
{{-- ========================================= --}}
<div id="aturan-tenggat-modal" class="fixed inset-0 bg-gray-600 bg-opacity-75 overflow-y-auto h-full w-full z-50 hidden">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-lg shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-indigo-100">
                <i class="fas fa-calendar-plus text-indigo-600"></i>
            </div>
            <h3 class="text-lg leading-6 font-medium text-gray-900 mt-2" id="modal-title">Tambah Aturan Tenggat Baru</h3>
            <div class="mt-2 px-7 py-3">
                <form id="aturan-form" method="POST" action="{{ route('admin.aturanTenggat.store') }}">
                    @csrf
                    <input type="hidden" name="_method" id="form-method" value="POST">

                    {{-- Container untuk menampilkan error server (non-field specific) --}}
                    <div id="modal-global-error" class="hidden bg-red-100 border-l-4 border-red-500 text-red-700 p-3 mb-4 text-left text-sm rounded" role="alert">
                        <p class="font-bold">Gagal Menyimpan:</p>
                        <ul id="modal-global-error-list" class="list-disc ml-5 mt-1"></ul>
                    </div>

                    <div class="mb-4 text-left">
                        <label for="id_warung" class="block text-sm font-medium text-gray-700">Warung</label>
                        <select name="id_warung" id="id_warung" required
                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">Pilih Warung...</option>
                            @foreach ($allWarungs as $warung)
                                <option value="{{ $warung->id }}">{{ $warung->nama_warung }}</option>
                            @endforeach
                        </select>
                        <p class="text-red-500 text-xs mt-1 error-message" data-field="id_warung"></p>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="mb-4 text-left">
                            <label for="tanggal_awal" class="block text-sm font-medium text-gray-700">Tanggal Awal (1-31)</label>
                            <input type="number" min="1" max="31" name="tanggal_awal" id="tanggal_awal" required
                                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <p class="text-red-500 text-xs mt-1 error-message" data-field="tanggal_awal"></p>
                        </div>

                        <div class="mb-4 text-left">
                            <label for="tanggal_akhir" class="block text-sm font-medium text-gray-700">Tanggal Akhir (1-31)</label>
                            <input type="number" min="1" max="31" name="tanggal_akhir" id="tanggal_akhir" required
                                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <p class="text-red-500 text-xs mt-1 error-message" data-field="tanggal_akhir"></p>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="mb-4 text-left">
                            <label for="jatuh_tempo_hari" class="block text-sm font-medium text-gray-700">Jatuh Tempo (Hari)</label>
                            <input type="number" name="jatuh_tempo_hari" id="jatuh_tempo_hari" required min="1"
                                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <p class="text-red-500 text-xs mt-1 error-message" data-field="jatuh_tempo_hari"></p>
                        </div>

                        <div class="mb-4 text-left">
                            <label for="bunga" class="block text-sm font-medium text-gray-700">Bunga (%)</label>
                            <input type="number" name="bunga" id="bunga" required min="0" step="0.01"
                                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <p class="text-red-500 text-xs mt-1 error-message" data-field="bunga"></p>
                        </div>
                    </div>

                    <div class="mb-4 text-left">
                        <label for="keterangan" class="block text-sm font-medium text-gray-700">Keterangan (Opsional)</label>
                        <textarea name="keterangan" id="keterangan" rows="2"
                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                        <p class="text-red-500 text-xs mt-1 error-message" data-field="keterangan"></p>
                    </div>

                    <div class="items-center px-4 py-3 border-t">
                        <button id="modal-submit-btn" type="submit"
                            class="px-4 py-2 bg-indigo-600 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition duration-150">
                            Simpan Aturan
                        </button>
                        <button type="button" onclick="closeAturanModal()"
                            class="mt-3 px-4 py-2 bg-gray-200 text-gray-700 text-base font-medium rounded-md w-full shadow-sm hover:bg-gray-300 transition duration-150">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- ========================================= --}}
{{-- SCRIPT JS UNTUK PENCARIAN, TOGGLE VIEW, DAN MODAL --}}
{{-- Diperbarui untuk menggunakan Fetch API dan menangani error validasi dari Controller --}}
{{-- ========================================= --}}
<script>
    /**
     * Fungsi untuk Toggle View Compact/Detail
     */
    document.addEventListener('DOMContentLoaded', function() {
        const viewToggles = document.querySelectorAll('.view-toggle');
        const filterForm = document.getElementById('filter-form');
        const viewModeInput = document.getElementById('view-mode-input');

        viewToggles.forEach(button => {
            button.addEventListener('click', function() {
                const mode = this.getAttribute('data-mode');
                viewModeInput.value = mode;

                // Perbarui URL query parameter tanpa memicu reload, lalu kirimkan form
                const url = new URL(window.location);
                url.searchParams.set('view', mode);
                window.history.pushState({}, '', url);

                // Ganti class button
                viewToggles.forEach(btn => btn.classList.remove('bg-white', 'shadow', 'text-indigo-600', 'text-red-600'));

                if (mode === 'compact') {
                    this.classList.add('bg-white', 'shadow', 'text-indigo-600');
                    document.getElementById('toggle-detail').classList.remove('bg-white', 'shadow', 'text-red-600');
                } else {
                    this.classList.add('bg-white', 'shadow', 'text-red-600');
                    document.getElementById('toggle-compact').classList.remove('bg-white', 'shadow', 'text-indigo-600');
                }

                // Tampilkan/Sembunyikan Container
                document.getElementById('container-compact').style.display = mode === 'compact' ? 'flex' : 'none';
                document.getElementById('container-detail').style.display = mode === 'detail' ? 'block' : 'none';
            });
        });

        // Set initial state based on $viewMode (handle Laravel's render on reload)
        const initialMode = '{{ $viewMode }}';
        if (initialMode === 'compact') {
            document.getElementById('toggle-compact').click();
        } else {
            document.getElementById('toggle-detail').click();
        }


        // Global Search for Compact Mode
        document.querySelectorAll('.compact-search').forEach(input => {
            input.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase().trim();
                const targetId = this.getAttribute('data-target');
                const listContainer = document.querySelector(targetId);
                const items = listContainer.querySelectorAll('.hutang-item');
                let found = 0;

                items.forEach(item => {
                    const itemSearchTerm = item.getAttribute('data-search-term');
                    if (itemSearchTerm.includes(searchTerm)) {
                        item.style.display = 'block';
                        found++;
                    } else {
                        item.style.display = 'none';
                    }
                });

                const notFoundMessage = listContainer.querySelector('.not-found-message');
                if (notFoundMessage) {
                    notFoundMessage.style.display = found === 0 && items.length > 0 ? 'block' : 'none';
                }
                listContainer.querySelectorAll('.empty-row').forEach(row => row.style.display = 'none');
            });
        });

    });


    /**
     * Fungsi-fungsi untuk Modal Aturan Tenggat
     */
    function clearValidationErrors() {
        document.querySelectorAll('.error-message').forEach(el => el.textContent = '');
        document.getElementById('modal-global-error').classList.add('hidden');
        document.getElementById('modal-global-error-list').innerHTML = '';
        document.querySelectorAll('#aturan-form input, #aturan-form select, #aturan-form textarea').forEach(el => {
            el.classList.remove('border-red-500');
            el.classList.add('border-gray-300');
        });
    }

    function openAturanModal(mode, data = null) {
        const modal = document.getElementById('aturan-tenggat-modal');
        const form = document.getElementById('aturan-form');
        const title = document.getElementById('modal-title');
        const formMethod = document.getElementById('form-method');
        const submitBtn = document.getElementById('modal-submit-btn');

        form.reset();
        clearValidationErrors();

        const updateRoute = (id) => `{{ url('admin/aturan-tenggat') }}/${id}`; // Menggunakan url() untuk route update
        const storeRoute = '{{ route('admin.aturanTenggat.store') }}'; // Menggunakan route() untuk route store

        if (mode === 'create') {
            title.textContent = 'Tambah Aturan Tenggat Baru';
            submitBtn.textContent = 'Simpan Aturan';
            formMethod.value = 'POST';
            form.action = storeRoute;
        } else if (mode === 'edit' && data) {
            title.textContent = `Edit Aturan Tenggat untuk ${data.warung ? data.warung.nama_warung : 'Warung #' + data.id_warung}`;
            submitBtn.textContent = 'Update Aturan';
            formMethod.value = 'PUT'; // Metode override untuk PUT
            form.action = updateRoute(data.id);

            // Isi data ke form
            document.getElementById('id_warung').value = data.id_warung;
            document.getElementById('tanggal_awal').value = data.tanggal_awal;
            document.getElementById('tanggal_akhir').value = data.tanggal_akhir;
            document.getElementById('jatuh_tempo_hari').value = data.jatuh_tempo_hari;
            document.getElementById('bunga').value = data.bunga;
            document.getElementById('keterangan').value = data.keterangan || '';
        }

        modal.classList.remove('hidden');
    }

    function closeAturanModal() {
        document.getElementById('aturan-tenggat-modal').classList.add('hidden');
        clearValidationErrors();
    }

    /**
     * Submit Form dengan Fetch API untuk menampilkan Error Validasi
     */
    document.getElementById('aturan-form').addEventListener('submit', function(e) {
        e.preventDefault();

        const form = e.target;
        const formData = new FormData(form);
        const actionUrl = form.action;
        const method = document.getElementById('form-method').value;

        // Siapkan data untuk fetch
        let data = {};
        formData.forEach((value, key) => (data[key] = value));

        clearValidationErrors();

        fetch(actionUrl, {
            method: 'POST', // Selalu POST di sini, method PUT/DELETE di-override via _method
            headers: {
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                'Content-Type': 'application/json',
                'X-HTTP-Method-Override': method // Laravel akan membaca header ini
            },
            body: JSON.stringify(data)
        })
        .then(response => {
            if (response.ok) {
                // Sukses
                alert('Aturan tenggat berhasil disimpan!');
                window.location.reload();
            } else if (response.status === 422) {
                // Error Validasi
                return response.json().then(data => {
                    handleValidationErrors(data.errors);
                });
            } else {
                // Error Server lainnya
                alert('Terjadi kesalahan server saat menyimpan data.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan koneksi saat menyimpan aturan tenggat.');
        });
    });

    /**
     * Menangani Error Validasi dari Laravel (422)
     */
    function handleValidationErrors(errors) {
        let globalErrors = [];

        for (const field in errors) {
            if (errors.hasOwnProperty(field)) {
                const message = errors[field][0];
                const errorElement = document.querySelector(`.error-message[data-field="${field}"]`);
                const inputElement = document.getElementById(field);

                if (errorElement) {
                    errorElement.textContent = message;
                } else if (inputElement) {
                    // Jika error spesifik tapi elemen error-message tidak ditemukan, tandai input
                    inputElement.classList.add('border-red-500');
                    inputElement.classList.remove('border-gray-300');
                    // Tambahkan ke daftar global jika tidak ada tempat untuk menampilkannya
                    globalErrors.push(message);
                } else {
                    // Ini menangani error seperti 'tanggal_awal' atau 'tanggal_akhir'
                    // yang memiliki pesan validasi tumpang tindih.
                    globalErrors.push(message);
                }
            }
        }

        // Tampilkan pesan error global yang mungkin terlewat atau error non-field (seperti dari validateDateOverlap)
        if (globalErrors.length > 0) {
            const globalErrorContainer = document.getElementById('modal-global-error');
            const globalErrorList = document.getElementById('modal-global-error-list');
            globalErrors.forEach(msg => {
                const li = document.createElement('li');
                li.textContent = msg;
                globalErrorList.appendChild(li);
            });
            globalErrorContainer.classList.remove('hidden');
        }
    }


    /**
     * Fungsi untuk Delete Aturan Tenggat
     */
    function deleteAturan(id) {
        if (confirm('Apakah Anda yakin ingin menghapus aturan tenggat ini? Tindakan ini tidak dapat dibatalkan.')) {
            const deleteUrl = `{{ url('admin/aturan-tenggat') }}/${id}`;

            fetch(deleteUrl, {
                method: 'POST', // Selalu POST di sini
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                    'X-HTTP-Method-Override': 'DELETE' // Kirim method override
                },
                body: JSON.stringify({})
            })
            .then(response => {
                if (response.ok) {
                    alert('Aturan tenggat berhasil dihapus!');
                    window.location.reload();
                } else {
                    response.json().then(data => {
                         alert(`Gagal menghapus aturan tenggat: ${data.message || 'Terjadi kesalahan.'}`);
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan koneksi saat menghapus aturan tenggat.');
            });
        }
    }
</script>

@endsection
