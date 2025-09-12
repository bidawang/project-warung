

@extends('layouts.admin')

@section('title', 'Detail Kas Warung')

@section('content')

        {{-- Main Content --}}
        <div class="flex-1 flex flex-col overflow-hidden">
            <header class="flex justify-between items-center p-6 bg-white border-b-2 border-gray-200">
                {{-- Tombol untuk menampilkan sidebar di layar kecil --}}
                <button id="openSidebarBtn" class="text-gray-500 hover:text-gray-900 lg:hidden">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Manajemen User</h1>
                </div>
                <div class="flex items-center">
                    <span class="mr-4 font-semibold hidden sm:inline">Admin User</span>
                    <div class="w-10 h-10 bg-blue-500 rounded-full"></div>
                </div>
            </header>

            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100 p-6 md:p-10">
                <div class="container mx-auto">
                <div class="md:flex justify-between items-center mb-8">
                    <h1 class="text-3xl font-bold text-gray-800 mb-4 md:mb-0">Daftar Warung</h1>

                    <form action="{{ route('admin.dashboard') }}" method="GET" class="w-full md:w-1/3">
                        <div class="relative">
                            <input type="text" name="search" value="{{ $searchKeyword ?? '' }}"
                                class="w-full bg-white border border-gray-300 rounded-full py-2 pl-10 pr-4 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                placeholder="Cari nama warung...">
                            <div class="absolute top-0 left-0 inline-flex items-center p-3">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    @forelse ($warungs as $warung)
                        <a href="{{ route('admin.warung.detail', ['id' => $warung->id]) }}" class="block group">
                            <div
                                class="bg-white rounded-lg shadow-md overflow-hidden transform group-hover:-translate-y-1 group-hover:shadow-xl transition-all duration-300">
                                <img class="h-48 w-full object-cover"
                                    src="https://via.placeholder.com/400x300.png/0077be?text={{ urlencode($warung->nama_warung) }}"
                                    alt="Gambar {{ $warung->nama_warung }}">

                                <div class="p-5">
                                    <h3
                                        class="mt-2 text-xl font-bold text-gray-800 group-hover:text-blue-600 transition-colors">
                                        {{ $warung->nama_warung }}</h3>
                                    <p class="text-gray-600 text-sm mt-1">Pemilik:
                                        {{ $warung->user->name ?? 'Tidak diketahui' }}</p>
                                    <p class="text-gray-500 text-xs mt-2 truncate">{{ $warung->keterangan }}</p>
                                </div>
                            </div>
                        </a>
                    @empty
                        <div class="col-span-full text-center py-10">
                            <p class="text-gray-500 text-lg">Warung tidak ditemukan.</p>
                        </div>
                    @endforelse
                </div>

                <div class="mt-8">
                    {{ $warungs->appends(['search' => $searchKeyword])->links() }}
                </div>
            </div>
            </main>
        </div>

@endsection
