@extends('layouts.admin')

@section('title', 'Data Harga Pulsa')

@section('content')
<div class="flex-1 flex flex-col overflow-hidden bg-gray-50">
    <main class="flex-1 overflow-x-hidden overflow-y-auto p-4 md:p-8">
        <div class="container mx-auto max-w-6xl">

            {{-- Header Section --}}
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
                <div>
                    <h1 class="text-2xl font-extrabold text-gray-900 tracking-tight">
                        Daftar Harga Pulsa
                    </h1>
                    <p class="text-sm text-gray-500 mt-1">Kelola master data nominal pulsa, harga beli, dan skema harga jual kasir.</p>
                </div>

                <a href="{{ route('admin.harga-pulsa.create') }}"
                    class="inline-flex items-center bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm py-2.5 px-5 rounded-xl shadow-md hover:shadow-lg transition-all duration-200">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                    </svg>
                    Tambah Konfigurasi Harga
                </a>
            </div>

            {{-- Filter & Search Panel --}}
            <div class="bg-white border border-gray-100 shadow-sm rounded-2xl p-4 mb-6">
                <form action="{{ route('admin.harga-pulsa.index') }}" method="GET"
                    class="flex flex-col md:flex-row items-stretch md:items-center gap-3">

                    {{-- Filter Jenis Pulsa --}}
                    <div class="relative flex-1 md:max-w-xs">
                        <select name="jenis_pulsa_id"
                            class="w-full bg-gray-50 border border-gray-300 rounded-xl py-2 pl-4 pr-10 text-sm text-gray-700 focus:ring-4 focus:ring-blue-100 focus:border-blue-500 transition-all appearance-none cursor-pointer">
                            <option value="">Semua Jenis Pulsa</option>
                            @foreach ($jenisPulsa as $jp)
                                <option value="{{ $jp->id }}"
                                    {{ request('jenis_pulsa_id') == $jp->id ? 'selected' : '' }}>
                                    {{ $jp->nama_jenis }}
                                </option>
                            @endforeach
                        </select>
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-gray-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </div>
                    </div>

                    {{-- Search Input --}}
                    <div class="relative flex-1">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-gray-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                        <input type="text" name="search" value="{{ request('search') }}"
                            class="w-full bg-gray-50 border border-gray-300 rounded-xl py-2 pl-10 pr-4 text-sm focus:ring-4 focus:ring-blue-100 focus:border-blue-500 transition-all"
                            placeholder="Cari berdasarkan nominal pulsa...">
                    </div>

                    {{-- Action Filter Buttons --}}
                    <div class="flex items-center gap-2">
                        <button type="submit"
                            class="flex-1 md:flex-none bg-gray-800 hover:bg-gray-900 text-white text-sm font-semibold px-6 py-2 rounded-xl transition shadow-sm">
                            Terapkan
                        </button>

                        @if(request()->hasAny(['search', 'jenis_pulsa_id']))
                            <a href="{{ route('admin.harga-pulsa.index') }}"
                                class="flex-1 md:flex-none bg-gray-100 hover:bg-gray-200 text-center text-gray-600 text-sm font-semibold px-4 py-2 rounded-xl transition">
                                Reset
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            {{-- Table Data Content --}}
            <div class="bg-white border border-gray-100 shadow-xl rounded-2xl overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100 text-sm">
                        <thead>
                            <tr class="bg-gray-50 text-gray-500 uppercase text-xs font-bold tracking-wider">
                                <th class="px-6 py-4 text-left">Jenis Pulsa</th>
                                <th class="px-6 py-4 text-left">Jumlah Nominal</th>
                                <th class="px-6 py-4 text-left">Struktur Harga (Beli & Modal)</th>
                                <th class="px-6 py-4 text-left">Skema Harga Jual Kasir</th>
                                <th class="px-6 py-4 text-center w-28">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-gray-700">
                            @forelse ($hargaPulsas as $hargaPulsa)
                                <tr class="hover:bg-gray-50/80 transition-colors">
                                    {{-- Kolom Jenis Pulsa --}}
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center bg-gray-100 text-gray-800 text-xs font-semibold px-2.5 py-1 rounded-md border border-gray-200">
                                            {{ $hargaPulsa->jenisPulsa->nama_jenis }}
                                        </span>
                                    </td>
                                    
                                    {{-- Kolom Jumlah Pulsa --}}
                                    <td class="px-6 py-4 whitespace-nowrap font-bold text-gray-900 text-base">
                                        {{ number_format($hargaPulsa->jumlah_pulsa, 0, ',', '.') }}
                                    </td>
                                    
                                    {{-- Kolom Harga Modal & Alomogada --}}
                                    <td class="px-6 py-4 whitespace-nowrap space-y-1">
                                        <div class="text-xs text-gray-500 flex items-center">
                                            <span class="w-20 inline-block">Alomogada</span>
                                            <span class="font-medium text-gray-700">: Rp{{ number_format($hargaPulsa->harga_alomogada, 0, ',', '.') }}</span>
                                        </div>
                                        <div class="text-xs text-gray-500 flex items-center">
                                            <span class="w-20 inline-block font-semibold text-gray-600">Modal Toko</span>
                                            <span class="font-bold text-gray-800">: Rp{{ number_format($hargaPulsa->harga_modal, 0, ',', '.') }}</span>
                                        </div>
                                    </td>

                                    {{-- Kolom Skema Jual (Tunai & Hutang) --}}
                                    <td class="px-6 py-4 whitespace-nowrap space-y-1.5">
                                        <div class="inline-flex items-center bg-blue-50 border border-blue-100 text-blue-800 text-xs font-semibold px-2.5 py-0.5 rounded-full mr-2">
                                            <span class="mr-1 text-blue-400 font-normal">Cash:</span>
                                            Rp{{ number_format($hargaPulsa->harga_jual, 0, ',', '.') }}
                                        </div>
                                        <div class="inline-flex items-center bg-amber-50 border border-amber-100 text-amber-800 text-xs font-semibold px-2.5 py-0.5 rounded-full">
                                            <span class="mr-1 text-amber-500 font-normal">Hutang:</span>
                                            Rp{{ number_format($hargaPulsa->harga_hutang, 0, ',', '.') }}
                                        </div>
                                    </td>

                                    {{-- Kolom Aksi Kontrol --}}
                                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                        <div class="flex items-center justify-center gap-3">
                                            <a href="{{ route('admin.harga-pulsa.edit', $hargaPulsa->id) }}"
                                                class="text-blue-600 hover:text-blue-900 font-semibold bg-blue-50 hover:bg-blue-100 px-2.5 py-1 rounded-lg transition-colors">
                                                Edit
                                            </a>

                                            <form action="{{ route('admin.harga-pulsa.destroy', $hargaPulsa->id) }}"
                                                method="POST" class="inline"
                                                onsubmit="return confirm('Apakah Anda yakin ingin menghapus data konfigurasi harga ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="text-red-600 hover:text-red-900 font-semibold bg-red-50 hover:bg-red-100 px-2.5 py-1 rounded-lg transition-colors">
                                                    Hapus
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    {{-- Ubah colspan menjadi 5 karena ada penambahan kolom modal --}}
                                    <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                        <div class="flex flex-col items-center justify-center">
                                            <svg class="w-10 h-10 text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0a2 2 0 01-2 2H6a2 2 0 01-2-2m16 0l-3.586-3.586a2 2 0 00-2.828 0L16 14m-7.071-7.071l3.565 3.565a2 2 0 002.828 0L14 7M3 18v3a1 1 0 001 1h16a1 1 0 001-1v-3M3 18l3.586-3.586a2 2 0 012.828 0L11 18M3 18h18" />
                                            </svg>
                                            <span class="text-sm font-medium">Data konfigurasi harga pulsa tidak ditemukan</span>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </main>
</div>
@endsection