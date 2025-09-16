@extends('layouts.admin')

@section('title', 'Tambah Warung')

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
                <div class="bg-white shadow-md rounded-lg p-6 md:p-8">
                    <h2 class="text-2xl md:text-3xl font-bold text-center text-gray-800 mb-6">Tambah Warung Baru</h2>

                    <form action="{{ route('admin.warung.store') }}" method="POST">
                        @csrf
                        {{-- Pemilik (User) --}}
                        <div class="mb-4">
                            <label for="id_user" class="block text-gray-700 font-semibold mb-2">Pemilik (User)</label>
                            <select class="block w-full border border-gray-300 rounded-md py-2 px-3 focus:outline-none focus:ring-2 focus:ring-blue-500" id="id_user" name="id_user" required>
                                <option value="">-- Pilih User --</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ old('id_user') == $user->id ? 'selected' : '' }}>{{ $user->name }} ({{ $user->email }})</option>
                                @endforeach
                            </select>
                            @error('id_user')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Area --}}
                        <div class="mb-4">
                            <label for="id_area" class="block text-gray-700 font-semibold mb-2">Area</label>
                            <select class="block w-full border border-gray-300 rounded-md py-2 px-3 focus:outline-none focus:ring-2 focus:ring-blue-500" id="id_area" name="id_area" required>
                                <option value="">-- Pilih Area --</option>
                                @foreach($areas as $area)
                                    <option value="{{ $area->id }}" {{ old('id_area') == $area->id ? 'selected' : '' }}>{{ $area->area }}</option>
                                @endforeach
                            </select>
                            @error('id_area')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Nama Warung --}}
                        <div class="mb-4">
                            <label for="nama_warung" class="block text-gray-700 font-semibold mb-2">Nama Warung</label>
                            <input type="text" class="w-full border border-gray-300 rounded-md py-2 px-3 focus:outline-none focus:ring-2 focus:ring-blue-500" id="nama_warung" name="nama_warung" value="{{ old('nama_warung') }}" required>
                            @error('nama_warung')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Modal --}}
                        <div class="mb-4">
                            <label for="modal" class="block text-gray-700 font-semibold mb-2">Modal</label>
                            <input type="number" class="w-full border border-gray-300 rounded-md py-2 px-3 focus:outline-none focus:ring-2 focus:ring-blue-500" id="modal" name="modal" value="{{ old('modal') }}" required>
                            @error('modal')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Keterangan --}}
                        <div class="mb-6">
                            <label for="keterangan" class="block text-gray-700 font-semibold mb-2">Keterangan</label>
                            <textarea class="w-full border border-gray-300 rounded-md py-2 px-3 focus:outline-none focus:ring-2 focus:ring-blue-500" id="keterangan" name="keterangan">{{ old('keterangan') }}</textarea>
                            @error('keterangan')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Tombol Aksi --}}
                        <div class="flex justify-end space-x-4">
                            <a href="{{ route('admin.warung.index') }}" class="bg-gray-400 hover:bg-gray-500 text-white font-bold py-2 px-4 rounded-full transition-colors duration-200">
                                Batal
                            </a>
                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-full transition-colors duration-200">
                                Simpan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>
@endsection