@extends('layouts.admin')

@section('title', 'Daftar Target Pencapaian')

@section('content')
    <div class="flex-1 flex flex-col overflow-hidden">
        {{-- Header --}}
        <header class="flex justify-between items-center p-6 bg-white border-b-2 border-gray-200">
            <button id="openSidebarBtn" class="text-gray-500 hover:text-gray-900 lg:hidden">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 6h16M4 12h16M4 18h16"></path>
                </svg>
            </button>
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Manajemen Target Pencapaian</h1>
            </div>
            <div class="flex items-center">
                <span class="mr-4 font-semibold hidden sm:inline">Admin User</span>
                <div class="w-10 h-10 bg-blue-500 rounded-full"></div>
            </div>
        </header>

        {{-- Main Content --}}
        <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100 p-6 md:p-10">
            <div class="container mx-auto">
                {{-- Tombol Tambah --}}
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8">
                    <h1 class="text-3xl font-bold text-gray-800 mb-4 md:mb-0">Daftar Target Pencapaian</h1>
                    <div class="flex space-x-4">
                        <a href="{{ route('admin.targetpencapaian.create') }}"
                            class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-full transition-colors duration-200 flex items-center justify-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 6v6m0 0v6m0-6h6m-6 0H6">
                                </path>
                            </svg>
                            Tambah Target
                        </a>
                    </div>
                </div>

                {{-- Flash message --}}
                @if(session('success'))
                    <div class="mb-4 p-4 bg-green-100 text-green-800 rounded-lg shadow-sm">
                        {{ session('success') }}
                    </div>
                @endif

                {{-- Tabel Daftar Target --}}
                <div class="bg-white shadow-md rounded-lg overflow-hidden overflow-x-auto">
                    <table class="min-w-full leading-normal">
                        <thead>
                            <tr>
                                <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    ID
                                </th>
                                <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Warung
                                </th>
                                <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Periode
                                </th>
                                <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Target
                                </th>
                                <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Status
                                </th>
                                <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Aksi
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($target as $t)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-5 py-5 border-b border-gray-200 text-sm">
                                        <p class="text-gray-900 whitespace-no-wrap">{{ $t->id }}</p>
                                    </td>
                                    <td class="px-5 py-5 border-b border-gray-200 text-sm">
                                        <p class="text-gray-900 whitespace-no-wrap">{{ $t->warung->nama_warung ?? '-' }}</p>
                                    </td>
                                    <td class="px-5 py-5 border-b border-gray-200 text-sm">
                                        <p class="text-gray-900 whitespace-no-wrap">{{ $t->periode_awal }} s/d {{ $t->periode_akhir }}</p>
                                    </td>
                                    <td class="px-5 py-5 border-b border-gray-200 text-sm">
                                        <p class="text-gray-900 whitespace-no-wrap">Rp {{ number_format($t->target_pencapaian,0,',','.') }}</p>
                                    </td>
                                    <td class="px-5 py-5 border-b border-gray-200 text-sm">
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                            {{ $t->status_pencapaian == 'Tercapai' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                            {{ $t->status_pencapaian }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-5 border-b border-gray-200 text-sm text-right">
                                        <a href="{{ route('admin.targetpencapaian.show', $t->id) }}"
                                            class="inline-block px-2 py-1 text-blue-600 hover:text-blue-900 transition-colors duration-200">
                                            Detail
                                        </a>
                                        <a href="{{ route('admin.targetpencapaian.edit', $t->id) }}"
                                            class="inline-block px-2 py-1 text-yellow-600 hover:text-yellow-900 transition-colors duration-200 ml-2">
                                            Edit
                                        </a>
                                        <form action="{{ route('admin.targetpencapaian.destroy', $t->id) }}"
                                            method="POST"
                                            class="inline-block ml-2"
                                            onsubmit="return confirm('Hapus target ini?');">
                                            @csrf @method('DELETE')
                                            <button type="submit"
                                                class="inline-block px-2 py-1 text-red-600 hover:text-red-900 transition-colors duration-200">
                                                Hapus
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-5 py-5 border-b border-gray-200 text-center text-sm text-gray-500">
                                        Tidak ada target pencapaian yang ditemukan.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <div class="mt-8">
                    {{-- kalau pakai paginate() bisa taruh di sini --}}
                </div>
            </div>
        </main>
    </div>
@endsection
