@extends('layouts.admin')

@section('title', 'Riwayat Hutang Semua Warung')

@section('content')
    @php
        $viewMode = request('view', 'detail');
        $status = request('status', '');

        $totalHutangGlobal = $hutangList->sum('jumlah_hutang_awal');
        $totalSisaGlobal = $hutangList->sum('jumlah_sisa_hutang');
        $totalLunasGlobal = $totalHutangGlobal - $totalSisaGlobal;

        $dataHutangPerWarung = $hutangList
            ->groupBy('warung.nama_warung')
            ->map(function ($items, $warungName) {
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
            })
            ->values();
    @endphp

    {{-- Root Element Alpine.js --}}
    <div class="flex-1 flex flex-col overflow-hidden bg-gray-100" 
         x-data="{ 
            viewMode: '{{ $viewMode }}',
            searchQuery: '',
            
            // Fungsi untuk ganti view mode
            toggleView(mode) {
                this.viewMode = mode;
                const url = new URL(window.location);
                url.searchParams.set('view', mode);
                window.history.pushState({}, '', url);
            }
         }">

        {{-- Header --}}
        <header class="flex justify-between items-center p-6 bg-white border-b sticky top-0 z-10">
            <h1 class="text-2xl font-bold text-gray-800">
                <i class="fas fa-hand-holding-usd mr-2 text-red-600"></i> Daftar Piutang Pelanggan (Admin View)
            </h1>

            {{-- Opsi Tampilan --}}
            <div class="flex space-x-2 p-1 bg-gray-200 rounded-lg shadow-inner">
                <button @click="toggleView('compact')"
                    :class="viewMode === 'compact' ? 'bg-white text-indigo-600 shadow' : 'text-gray-600 hover:bg-gray-300'"
                    class="px-3 py-1 text-sm font-semibold rounded-lg transition duration-150">
                    <i class="fas fa-columns mr-1"></i> Tampilan Compact
                </button>
                <button @click="toggleView('detail')"
                    :class="viewMode === 'detail' ? 'bg-white text-red-600 shadow' : 'text-gray-600 hover:bg-gray-300'"
                    class="px-3 py-1 text-sm font-semibold rounded-lg transition duration-150">
                    <i class="fas fa-list-ul mr-1"></i> Tampilan Detail
                </button>
            </div>
        </header>

        <main class="flex-1 overflow-y-auto p-6 space-y-6">
            {{-- Notifications --}}
            @if (session('success'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-lg shadow-md">
                    <p>{{ session('success') }}</p>
                </div>
            @endif

            {{-- MANAJEMEN ATURAN TENGGAT --}}
            <div class="bg-white shadow-xl border border-gray-200 rounded-xl p-6">
                <div class="flex justify-between items-center mb-4 pb-2 border-b-2 border-indigo-100">
                    <h2 class="text-xl font-bold text-gray-800">
                        <i class="fas fa-calendar-times mr-3 text-indigo-600"></i> Manajemen Aturan Tenggat
                    </h2>
                    <a href="{{ route('admin.aturanTenggat.create') }}"
                        class="bg-indigo-600 text-white px-4 py-2 rounded-lg shadow-md hover:bg-indigo-700 transition duration-150 font-semibold">
                        <i class="fas fa-plus mr-1"></i> Tambah Aturan
                    </a>
                </div>

                <div class="overflow-x-auto rounded-lg border">
                    <table class="min-w-full text-sm text-gray-600 border-collapse">
                        <thead class="bg-gray-100 text-gray-700 uppercase text-xs">
                            <tr>
                                <th class="py-3 px-4 text-left">Warung</th>
                                <th class="py-3 px-4 text-left">Periode Awal</th>
                                <th class="py-3 px-4 text-left">Periode Akhir</th>
                                <th class="py-3 px-4 text-center">Jatuh Tempo</th>
                                <th class="py-3 px-4 text-center">Bunga (%)</th>
                                <th class="py-3 px-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($aturanTenggats as $aturan)
                                <tr class="border-b hover:bg-indigo-50">
                                    <td class="py-3 px-4 font-bold text-indigo-600">{{ $aturan->warung->nama_warung ?? 'N/A' }}</td>
                                    <td class="py-3 px-4">{{ $aturan->tanggal_awal }}</td>
                                    <td class="py-3 px-4">{{ $aturan->tanggal_akhir }}</td>
                                    <td class="py-3 px-4 text-center">{{ $aturan->jatuh_tempo_hari }} Hari</td>
                                    <td class="py-3 px-4 text-center font-extrabold text-red-600">{{ $aturan->bunga }}%</td>
                                    <td class="py-3 px-4 text-center space-x-3">
                                        <a href="{{ route('admin.aturanTenggat.edit', $aturan->id) }}" class="text-green-600 hover:underline">Edit</a>
                                        <form action="{{ route('admin.aturanTenggat.destroy', $aturan->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus aturan ini?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:underline">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="text-center py-6 text-gray-500">Belum ada aturan.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- FILTER --}}
            <div class="bg-white shadow-lg rounded-xl p-6 border-t-4 border-red-500">
                <form action="{{ route('admin.hutang.index') }}" method="GET">
                    <input type="hidden" name="view" :value="viewMode">
                    <div class="flex flex-wrap items-end gap-4">
                        <div class="w-full sm:w-1/5">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                            <select name="status" class="block w-full border rounded-lg p-2 text-sm">
                                <option value="">Semua Status</option>
                                <option value="belum_lunas" {{ $status === 'belum_lunas' ? 'selected' : '' }}>Belum Lunas</option>
                                <option value="lunas" {{ $status === 'lunas' ? 'selected' : '' }}>Lunas</option>
                            </select>
                        </div>
                        <div class="w-full sm:w-1/5">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Cari User (Server Side)</label>
                            <input type="text" name="q" value="{{ request('q') }}" placeholder="Nama pelanggan..." class="block w-full border rounded-lg p-2 text-sm">
                        </div>
                        <div class="flex space-x-2 ml-auto">
                            <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded-lg font-semibold">Terapkan Filter</button>
                            <a href="{{ route('admin.hutang.index') }}" class="text-gray-500 px-4 py-2 border rounded-lg">Reset</a>
                        </div>
                    </div>
                </form>
            </div>

            {{-- STATISTIK GLOBAL --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white p-5 rounded-xl shadow border-l-4 border-red-500">
                    <p class="text-sm text-gray-500 font-medium">Total Seluruh Piutang</p>
                    <p class="text-2xl font-extrabold text-red-600">Rp. {{ number_format($totalHutangGlobal, 0, ',', '.') }}</p>
                </div>
                <div class="bg-white p-5 rounded-xl shadow border-l-4 border-yellow-500">
                    <p class="text-sm text-gray-500 font-medium">Sisa Piutang</p>
                    <p class="text-2xl font-extrabold text-yellow-700">Rp. {{ number_format($totalSisaGlobal, 0, ',', '.') }}</p>
                </div>
                <div class="bg-white p-5 rounded-xl shadow border-l-4 border-green-500">
                    <p class="text-sm text-gray-500 font-medium">Total Dibayar</p>
                    <p class="text-2xl font-extrabold text-green-700">Rp. {{ number_format($totalLunasGlobal, 0, ',', '.') }}</p>
                </div>
            </div>

            {{-- KONTEN UTAMA --}}
            @if ($dataHutangPerWarung->isEmpty() && $hutangList->isEmpty())
                <div class="bg-blue-100 p-6 rounded-lg text-blue-700">Data tidak ditemukan.</div>
            @else
                
                {{-- MODE COMPACT (X-SHOW) --}}
                <div x-show="viewMode === 'compact'" 
                     class="flex overflow-x-auto pb-4 -mx-6 px-6 space-x-6">
                    @foreach ($dataHutangPerWarung as $dataWarung)
                        <div class="flex-shrink-0 w-80 bg-white shadow-xl border-t-8 rounded-xl flex flex-col border-indigo-500"
                             x-data="{ localSearch: '' }">
                            
                            <div class="p-4 border-b bg-gray-50">
                                <h2 class="text-lg font-bold text-gray-800">**{{ $dataWarung['nama_warung'] }}**</h2>
                                <div class="text-sm font-bold text-red-600">
                                    Sisa: Rp. {{ number_format($dataWarung['total_hutang_warung'] - $dataWarung['total_lunas_warung'], 0, ',', '.') }}
                                </div>
                                <input type="text" x-model="localSearch" placeholder="Cari di warung ini..." 
                                       class="w-full border rounded-lg px-3 py-2 text-sm mt-3">
                            </div>

                            <div class="p-4 flex-1 overflow-y-auto max-h-[500px] space-y-3">
                                @foreach ($dataWarung['hutang_list'] as $hutang)
                                    <a href="{{ route('admin.hutang.detail', $hutang->id) }}"
                                       x-show="localSearch === '' || '{{ strtolower($hutang->user->name) }}'.includes(localSearch.toLowerCase())"
                                       class="block p-3 border-l-4 rounded-lg shadow-sm transition {{ $hutang->status === 'lunas' ? 'border-green-500 bg-white' : 'border-red-500 bg-white' }}">
                                        <div class="flex justify-between items-start">
                                            <span class="font-semibold text-gray-800 truncate">{{ $hutang->user->name }}</span>
                                        </div>
                                        <p class="font-bold text-sm">Sisa: Rp. {{ number_format($hutang->jumlah_sisa_hutang, 0, ',', '.') }}</p>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- MODE DETAIL (X-SHOW) --}}
                <div x-show="viewMode === 'detail'" class="bg-white shadow-xl border rounded-xl p-6">
                    <h2 class="text-xl font-bold mb-4"><i class="fas fa-table mr-3 text-red-600"></i> Tabel Global</h2>
                    
                    <div class="mb-4">
                        {{ $hutangList->appends(request()->query())->links() }}
                    </div>

                    <div class="overflow-x-auto border rounded-lg">
                        <table class="min-w-full text-sm">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="py-3 px-4 text-left">Warung</th>
                                    <th class="py-3 px-4 text-left">Pelanggan</th>
                                    <th class="py-3 px-4 text-left">Tenggat</th>
                                    <th class="py-3 px-4 text-right">Sisa (Rp)</th>
                                    <th class="py-3 px-4 text-center">Status</th>
                                    <th class="py-3 px-4 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($hutangList as $hutang)
                                    <tr class="border-b hover:bg-gray-50">
                                        <td class="py-3 px-4 font-bold text-indigo-600">{{ $hutang->warung->nama_warung }}</td>
                                        <td class="py-3 px-4">{{ $hutang->user->name }}</td>
                                        <td class="py-3 px-4 {{ $hutang->tenggat?->isPast() ? 'text-red-500 font-bold' : '' }}">
                                            {{ $hutang->tenggat ? $hutang->tenggat->format('d M Y') : '-' }}
                                        </td>
                                        <td class="py-3 px-4 text-right font-bold text-red-700">{{ number_format($hutang->jumlah_sisa_hutang, 0, ',', '.') }}</td>
                                        <td class="py-3 px-4 text-center">
                                            <span class="px-2 py-0.5 rounded-full text-xs font-bold {{ $hutang->status === 'lunas' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                {{ strtoupper($hutang->status) }}
                                            </span>
                                        </td>
                                        <td class="py-3 px-4 text-center">
                                            <a href="{{ route('admin.hutang.detail', $hutang->id) }}" class="text-indigo-600 hover:underline">Detail</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </main>
    </div>
@endsection