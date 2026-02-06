@extends('layouts.admin')

@section('title', 'Riwayat Hutang Semua Warung')

@section('content')
    @php
        $viewMode = request('view', 'detail');
        $status = request('status', '');

        // Hitung total global dari seluruh data yang terfilter
        // Karena $hutangList sudah di-paginate, untuk statistik global sebaiknya ambil dari total query
        $totalHutangGlobal = $hutangList->sum('total_awal');
        $totalSisaGlobal = $hutangList->sum('total_sisa');
        $totalLunasGlobal = $totalHutangGlobal - $totalSisaGlobal;
    @endphp

    <div class="flex-1 flex flex-col overflow-hidden bg-gray-100" x-data="{
        viewMode: '{{ $viewMode }}',
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
                <i class="fas fa-users mr-2 text-red-600"></i> Rekap Piutang Pelanggan
            </h1>

            <div class="flex space-x-2 p-1 bg-gray-200 rounded-lg">
                <button @click="toggleView('compact')"
                    :class="viewMode === 'compact' ? 'bg-white text-indigo-600 shadow' : 'text-gray-600'"
                    class="px-3 py-1 text-sm font-semibold rounded-lg transition duration-150">
                    <i class="fas fa-th-large mr-1"></i> Compact
                </button>
                <button @click="toggleView('detail')"
                    :class="viewMode === 'detail' ? 'bg-white text-red-600 shadow' : 'text-gray-600'"
                    class="px-3 py-1 text-sm font-semibold rounded-lg transition duration-150">
                    <i class="fas fa-list mr-1"></i> Tabel
                </button>
            </div>
        </header>

        <main class="flex-1 overflow-y-auto p-6 space-y-6">

            {{-- Bagian Aturan Tenggat & Statistik (Tetap Sama Seperti Sebelumnya) --}}
            {{-- MANAJEMEN ATURAN TENGGAT --}}
            <div class="bg-white shadow-xl border border-gray-200 rounded-xl p-6">
                <div class="flex justify-between items-center mb-4 pb-2 border-b-2 border-indigo-100">
                    <h2 class="text-xl font-bold text-gray-800">
                        <i class="fas fa-calendar-times mr-3 text-indigo-600"></i> Manajemen Aturan Tenggat
                    </h2>
                    <a href="{{ route('admin.aturanTenggat.create') }}"
                        class="bg-indigo-600 text-white px-4 py-2 rounded-lg shadow-md hover:bg-indigo-700 transition duration-150 font-semibold text-sm">
                        <i class="fas fa-plus mr-1"></i> Tambah Aturan
                    </a>
                </div>

                <div class="overflow-x-auto rounded-lg border">
                    <table class="min-w-full text-sm text-gray-600 border-collapse">
                        <thead class="bg-gray-100 text-gray-700 uppercase text-xs">
                            <tr>
                                <th class="py-3 px-4 text-left">Warung</th>
                                <th class="py-3 px-4 text-left">Periode</th>
                                <th class="py-3 px-4 text-center">Jatuh Tempo</th>
                                <th class="py-3 px-4 text-center">Bunga</th>
                                <th class="py-3 px-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($aturanTenggats as $aturan)
                                <tr class="border-b hover:bg-indigo-50">
                                    <td class="py-3 px-4 font-bold text-indigo-600">
                                        {{ $aturan->warung->nama_warung ?? 'N/A' }}</td>
                                    <td class="py-3 px-4">{{ $aturan->tanggal_awal }} s/d {{ $aturan->tanggal_akhir }}</td>
                                    <td class="py-3 px-4 text-center">{{ $aturan->jatuh_tempo_hari }} Hari</td>
                                    <td class="py-3 px-4 text-center font-extrabold text-red-600">{{ $aturan->bunga }}%</td>
                                    <td class="py-3 px-4 text-center space-x-3">
                                        <a href="{{ route('admin.aturanTenggat.edit', $aturan->id) }}"
                                            class="text-green-600 hover:underline">Edit</a>
                                        <form action="{{ route('admin.aturanTenggat.destroy', $aturan->id) }}"
                                            method="POST" class="inline" onsubmit="return confirm('Hapus aturan ini?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:underline">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-6 text-gray-500">Belum ada aturan aktif.</td>
                                </tr>
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
                        <div class="w-full sm:w-1/4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Cari Pelanggan</label>
                            <input type="text" name="q" value="{{ request('q') }}" placeholder="Ketik nama..."
                                class="block w-full border rounded-lg p-2 text-sm">
                        </div>
                        <div class="w-full sm:w-1/4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                            <select name="status" class="block w-full border rounded-lg p-2 text-sm">
                                <option value="">Semua</option>
                                <option value="belum_lunas" {{ $status === 'belum_lunas' ? 'selected' : '' }}>Belum Lunas
                                </option>
                                <option value="lunas" {{ $status === 'lunas' ? 'selected' : '' }}>Lunas</option>
                            </select>
                        </div>
                        <div class="flex space-x-2">
                            <button type="submit"
                                class="bg-red-600 text-white px-6 py-2 rounded-lg font-semibold">Filter</button>
                            <a href="{{ route('admin.hutang.index') }}"
                                class="text-gray-500 px-4 py-2 border rounded-lg">Reset</a>
                        </div>
                    </div>
                </form>
            </div>

            {{-- STATISTIK --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white p-5 rounded-xl shadow border-l-4 border-red-500">
                    <p class="text-sm text-gray-500 font-medium">Total Piutang</p>
                    <p class="text-2xl font-extrabold text-red-600">Rp {{ number_format($totalHutangGlobal, 0, ',', '.') }}
                    </p>
                </div>
                <div class="bg-white p-5 rounded-xl shadow border-l-4 border-yellow-500">
                    <p class="text-sm text-gray-500 font-medium">Sisa Belum Bayar</p>
                    <p class="text-2xl font-extrabold text-yellow-700">Rp
                        {{ number_format($totalSisaGlobal, 0, ',', '.') }}</p>
                </div>
                <div class="bg-white p-5 rounded-xl shadow border-l-4 border-green-500">
                    <p class="text-sm text-gray-500 font-medium">Sudah Terbayar</p>
                    <p class="text-2xl font-extrabold text-green-700">Rp
                        {{ number_format($totalLunasGlobal, 0, ',', '.') }}</p>
                </div>
            </div>

            @if ($hutangList->isEmpty())
                <div class="bg-blue-100 p-6 rounded-lg text-blue-700 text-center">Data pelanggan tidak ditemukan.</div>
            @else
                {{-- MODE COMPACT (Koleksi per User dikelompokkan per Warung di View) --}}
                <div x-show="viewMode === 'compact'" class="flex overflow-x-auto pb-4 -mx-6 px-6 space-x-6">
                    @foreach ($hutangList->groupBy('id_warung') as $warungId => $group)
                        <div
                            class="flex-shrink-0 w-80 bg-white shadow-xl border-t-8 rounded-xl border-indigo-500 flex flex-col">
                            <div class="p-4 border-b bg-gray-50">
                                <h2 class="text-lg font-bold text-gray-800">{{ $group->first()->warung->nama_warung }}</h2>
                                <p class="text-xs text-gray-500">{{ $group->count() }} Pelanggan berhutang</p>
                            </div>

                            <div class="p-4 flex-1 overflow-y-auto max-h-[500px] space-y-3">
                                @foreach ($group as $data)
                                    <div
                                        class="p-3 border rounded-lg shadow-sm transition {{ $data->total_sisa > 0 ? 'border-red-200 bg-red-50' : 'border-green-200 bg-green-50' }}">
                                        <div class="flex justify-between items-start mb-1">
                                            <span class="font-bold text-gray-800">{{ $data->user->name }}</span>
                                            <span
                                                class="text-[10px] px-2 py-0.5 bg-white rounded-full border">{{ $data->total_nota }}
                                                Bon</span>
                                        </div>
                                        <p class="text-sm text-red-700 font-bold">Rp
                                            {{ number_format($data->total_sisa, 0, ',', '.') }}</p>
                                        <a href="{{ route('admin.hutang.user_detail', $data->id_user) }}"
                                            class="text-xs text-indigo-600 font-semibold mt-2 block hover:underline">
                                            Lihat Detail &rarr;
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- MODE DETAIL (TABEL) --}}
                <div x-show="viewMode === 'detail'" class="bg-white shadow-xl border rounded-xl p-6">
                    <div class="mb-4">
                        {{ $hutangList->appends(request()->query())->links() }}
                    </div>
                    <div class="overflow-x-auto border rounded-lg">
                        <table class="min-w-full text-sm">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="py-3 px-4 text-left">Pelanggan</th>
                                    <th class="py-3 px-4 text-left">Warung</th>
                                    <th class="py-3 px-4 text-center">Nota</th>
                                    <th class="py-3 px-4 text-center">Tenggat Terdekat</th>
                                    <th class="py-3 px-4 text-right">Total Sisa (Rp)</th>
                                    <th class="py-3 px-4 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($hutangList as $data)
                                    <tr class="border-b hover:bg-gray-50">
                                        <td class="py-3 px-4">
                                            <div class="font-bold text-gray-800">{{ $data->user->name }}</div>
                                        </td>
                                        <td class="py-3 px-4 text-indigo-600 font-medium">{{ $data->warung->nama_warung }}
                                        </td>
                                        <td class="py-3 px-4 text-center">{{ $data->total_nota }} Bon</td>
                                        <td class="py-3 px-4 text-center">
                                            @if ($data->tenggat_terdekat)
                                                @php $t = \Carbon\Carbon::parse($data->tenggat_terdekat); @endphp
                                                <span
                                                    class="{{ $t->isPast() && $data->total_sisa > 0 ? 'text-red-600 font-extrabold' : '' }}">
                                                    {{ $t->format('d M Y') }}
                                                </span>
                                            @else
                                                <span class="text-gray-400">-</span>
                                            @endif
                                        </td>
                                        <td class="py-3 px-4 text-right font-bold text-red-700">
                                            {{ number_format($data->total_sisa, 0, ',', '.') }}
                                        </td>
                                        <td class="py-3 px-4 text-center">
                                            <a href="{{ route('admin.hutang.user_detail', $data->id_user) }}"
                                                class="bg-indigo-600 text-white px-3 py-1 rounded text-xs hover:bg-indigo-700 transition">
                                                Detail
                                            </a>
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
