@extends('layouts.admin')

@section('title', 'Manajemen Warung')

@section('content')

    {{-- Main Content --}}
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
                <h1 class="text-2xl font-bold text-gray-800">Manajemen Warung</h1>
            </div>
            <div class="flex items-center">
                <span class="mr-4 font-semibold hidden sm:inline">Admin User</span>
                <div class="w-10 h-10 bg-blue-500 rounded-full"></div>
            </div>
        </header>

        {{-- Main Content Area --}}
        <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100 p-6 md:p-10">
            <div class="container mx-auto">
                {{-- Notifikasi Sukses --}}
                @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
                @endif

                {{-- Header Konten dan Tombol Tambah --}}
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8">
                    <h1 class="text-3xl font-bold text-gray-800 mb-4 md:mb-0">Daftar Warung</h1>
                    <a href="{{ route('admin.warung.create') }}"
                        class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-full transition-colors duration-200 flex items-center justify-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6">
                            </path>
                        </svg>
                        Tambah Warung
                    </a>
                </div>

                {{-- Tabel Daftar Warung --}}
                <div class="bg-white shadow-md rounded-lg overflow-hidden overflow-x-auto">
                    <table class="min-w-full leading-normal">
                        <thead>
                            <tr>
                                <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    #
                                </th>
                                <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Nama Warung
                                </th>
                                <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Pemilik
                                </th>
                                <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Area
                                </th>
                                <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Modal
                                </th>
                                <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Keterangan
                                </th>
                                <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Aksi
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($warungs as $warung)
                            <tr class="hover:bg-gray-50">
                                <td class="px-5 py-5 border-b border-gray-200 text-sm">
                                    <p class="text-gray-900 whitespace-no-wrap">{{ $loop->iteration }}</p>
                                </td>
                                <td class="px-5 py-5 border-b border-gray-200 text-sm">
                                    <p class="text-gray-900 whitespace-no-wrap">{{ $warung->nama_warung }}</p>
                                </td>
                                <td class="px-5 py-5 border-b border-gray-200 text-sm">
                                    <p class="text-gray-900 whitespace-no-wrap">{{ $warung->user->name ?? '-' }}</p>
                                </td>
                                <td class="px-5 py-5 border-b border-gray-200 text-sm">
                                    <p class="text-gray-900 whitespace-no-wrap">{{ $warung->area->area ?? '-' }}</p>
                                </td>
                                <td class="px-5 py-5 border-b border-gray-200 text-sm">
                                    <p class="text-gray-900 whitespace-no-wrap">Rp {{ number_format($warung->modal, 0, ',', '.') }}</p>
                                </td>
                                <td class="px-5 py-5 border-b border-gray-200 text-sm">
                                    <p class="text-gray-900 whitespace-no-wrap">{{ $warung->keterangan ?? '-' }}</p>
                                </td>
                                <td class="px-5 py-5 border-b border-gray-200 text-sm text-right">
                                    <a href="{{ route('admin.warung.show', $warung->id) }}"
                                        class="inline-block px-2 py-1 text-blue-600 hover:text-blue-900 transition-colors duration-200">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                            <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                                            <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
                                        </svg>
                                    </a>
                                    <a href="{{ route('admin.warung.edit', $warung->id) }}"
                                        class="inline-block px-2 py-1 text-yellow-600 hover:text-yellow-900 transition-colors duration-200">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                            <path d="M13.586 3.586a2 2 0 112.828 2.828l-7.293 7.293a1 1 0 01-.39.293l-2 1a1 1 0 01-1.206-1.206l1-2a1 1 0 01.293-.39l7.293-7.293z" />
                                        </svg>
                                    </a>
                                    <form action="{{ route('admin.warung.destroy', $warung->id) }}" method="POST" class="inline-block ml-2" onsubmit="return confirm('Yakin hapus warung ini?');">
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
                                <td colspan="7" class="px-5 py-5 border-b border-gray-200 text-center text-sm text-gray-500">
                                    Belum ada data warung.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
@endsection