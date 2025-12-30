@extends('layouts.admin')

@section('title', 'Daftar Saldo Pulsa Warung')

@section('content')

    {{-- Main Content --}}
    <div class="flex-1 flex flex-col overflow-hidden">
        
        {{-- Main Content --}}
        <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100 p-6 md:p-10">
            <div class="container mx-auto">
                {{-- Tombol Top-Up dan Search Bar --}}
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8">
                    <h1 class="text-3xl font-bold text-gray-800 mb-4 md:mb-0">Daftar Saldo Pulsa Warung</h1>
                    <div class="flex flex-col sm:flex-row space-y-4 sm:space-y-0 sm:space-x-4 w-full md:w-auto">
                        {{-- Tombol Tambah Saldo --}}
                        <a href="{{ route('admin.saldo-pulsa.create') }}"
                            class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-full transition-colors duration-200 flex items-center justify-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6">
                                </path>
                            </svg>
                            Top-Up Saldo
                        </a>
                        {{-- Form Search Bar --}}
                        <form action="{{ route('admin.saldo-pulsa.index') }}" method="GET" class="relative w-full sm:w-auto">
                            <input type="text" name="search"
                                class="w-full bg-white border border-gray-300 rounded-full py-2 pl-10 pr-4 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                placeholder="Cari nama warung...">
                            <div class="absolute top-0 left-0 inline-flex items-center p-3">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Tabel Daftar Saldo Pulsa --}}
                <div class="bg-white shadow-md rounded-lg overflow-hidden overflow-x-auto">
                    <table class="min-w-full leading-normal">
                        <thead>
                            <tr>
                                <th
                                    class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Warung
                                </th>
                                <th
                                    class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Jenis Pulsa
                                </th>
                                <th
                                    class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Saldo Saat Ini (Rp)
                                </th>
                                <th
                                    class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Terakhir Diperbarui
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($pulsas as $pulsa)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-5 py-5 border-b border-gray-200 text-sm">
                                        {{-- Asumsi model Pulsa memiliki relasi 'warung' --}}
                                        <p class="text-gray-900 whitespace-no-wrap">{{ $pulsa->warung->nama_warung ?? 'Warung Tidak Ditemukan' }}</p>
                                    </td>
                                    <td class="px-5 py-5 border-b border-gray-200 text-sm">
                                        @php
                                            $jenisLabel = $pulsa->jenis == 'hp' ? 'Handphone' : 'Listrik (Token)';
                                            $jenisColor = $pulsa->jenis == 'hp' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800';
                                        @endphp
                                        <span class="relative inline-block px-3 py-1 font-semibold leading-tight">
                                            <span aria-hidden="true" class="absolute inset-0 {{ $jenisColor }} opacity-50 rounded-full"></span>
                                            <span class="relative uppercase text-xs">{{ $jenisLabel }}</span>
                                        </span>
                                    </td>
                                    <td class="px-5 py-5 border-b border-gray-200 text-sm font-semibold">
                                        <p class="text-gray-900 whitespace-no-wrap">Rp{{ number_format($pulsa->saldo, 0, ',', '.') }}</p>
                                    </td>
                                    <td class="px-5 py-5 border-b border-gray-200 text-sm">
                                        <p class="text-gray-600 whitespace-no-wrap">{{ $pulsa->updated_at->diffForHumans() }}</p>
                                    </td>

                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-5 py-5 border-b border-gray-200 text-center text-sm text-gray-500">
                                        Tidak ada data saldo pulsa yang ditemukan.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <div class="mt-8">
                    {{-- {{ $pulsas->links() }} --}}
                </div>
            </div>
        </main>
    </div>
@endsection
