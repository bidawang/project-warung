@extends('layouts.admin')

@section('title', 'Data Area')

@section('content')
<div class="flex-1 flex flex-col overflow-hidden">
    {{-- Header --}}
    <header class="flex justify-between items-center p-6 bg-white border-b-2 border-gray-200">
        {{-- Tombol untuk menampilkan sidebar di layar kecil --}}
        <button id="openSidebarBtn" class="text-gray-500 hover:text-gray-900 lg:hidden">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
            </svg>
        </button>
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Data Area</h1>
        </div>
        <div class="flex items-center">
            <span class="mr-4 font-semibold hidden sm:inline">Admin User</span>
            <div class="w-10 h-10 bg-blue-500 rounded-full"></div>
        </div>
    </header>

    {{-- Main Content --}}
    <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100 p-6 md:p-10">
        <div class="container mx-auto">
            {{-- Tombol Tambah dan Search Bar --}}
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8">
                <h1 class="text-3xl font-bold text-gray-800 mb-4 md:mb-0">Daftar Area</h1>
                <div class="flex flex-col sm:flex-row space-y-4 sm:space-y-0 sm:space-x-4 w-full md:w-auto">
                    <a href="{{ url('/admin/area/create') }}"
                        class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-full transition-colors duration-200 text-center">
                        + Tambah Area
                    </a>
                    <form action="{{ url('/admin/area') }}" method="GET" class="relative w-full sm:w-auto">
                        <input type="text" name="search" value="{{ request('search') }}"
                            class="w-full bg-white border border-gray-300 rounded-full py-2 pl-10 pr-4 focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="Cari area...">
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

            {{-- Tabel Daftar Area (dibuat responsif dengan overflow) --}}
            <div class="bg-white shadow-md rounded-lg overflow-hidden overflow-x-auto">
                <table class="min-w-full leading-normal">
                    <thead>
                        <tr>
                            <th
                                class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Area
                            </th>
                            <th
                                class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Keterangan
                            </th>
                            <th
                                class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($areas as $area)
                            <tr class="hover:bg-gray-50">
                                <td class="px-5 py-5 border-b border-gray-200 text-sm">
                                    <p class="text-gray-900 whitespace-no-wrap">{{ $area->area }}</p>
                                </td>
                                <td class="px-5 py-5 border-b border-gray-200 text-sm">
                                    <p class="text-gray-900 whitespace-no-wrap">{{ $area->keterangan ?? '-' }}</p>
                                </td>
                                <td class="px-5 py-5 border-b border-gray-200 text-sm text-right">
                                    <a href="{{ url('/admin/area/edit', $area->id) }}"
                                        class="inline-block px-2 py-1 text-blue-600 hover:text-blue-900 transition-colors duration-200">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                            <path d="M13.586 3.586a2 2 0 112.828 2.828l-7.293 7.293a1 1 0 01-.39.293l-2 1a1 1 0 01-1.206-1.206l1-2a1 1 0 01.293-.39l7.293-7.293z" />
                                        </svg>
                                    </a>
                                    <form action="{{ url('/admin/area/destroy', $area->id) }}" method="POST"
                                        class="inline-block ml-2" onsubmit="return confirm('Apakah Anda yakin ingin menghapus area ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="inline-block px-2 py-1 text-red-600 hover:text-red-900 transition-colors duration-200">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-5 py-5 border-b border-gray-200 text-center text-sm text-gray-500">
                                    Tidak ada data area yang ditemukan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="mt-8">
                {{-- Ini akan menampilkan link pagination jika menggunakan paginate() di controller --}}
            </div>
        </div>
    </main>
</div>
@endsection
