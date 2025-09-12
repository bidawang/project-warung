@extends('layouts.admin')

@section('title', 'Tambah Barang')

@section('content')

    {{-- Main Content --}}
    <div class="flex-1 flex flex-col overflow-hidden">
        {{-- Header --}}
        <header class="flex justify-between items-center p-6 bg-white border-b-2 border-gray-200">
            <h1 class="text-2xl font-bold text-gray-800">Tambah Barang</h1>
            <div class="flex items-center">
                <span class="mr-4 font-semibold hidden sm:inline">Admin User</span>
                <div class="w-10 h-10 bg-blue-500 rounded-full"></div>
            </div>
        </header>

        {{-- Main Content --}}
        <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100 p-6 md:p-10">
            <div class="container mx-auto">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8">
                    <h1 class="text-3xl font-bold text-gray-800 mb-4 md:mb-0">Form Tambah Barang</h1>
                </div>

                <div class="bg-white p-6 rounded-lg shadow-md">
                    <form action="{{ route('admin.barang.store') }}" method="POST">
                        @csrf

                        {{-- Input Sub Kategori --}}
                        <div class="mb-6">
                            <div class="flex justify-between items-center mb-2">
                                <label for="id_sub_kategori" class="block text-gray-700 font-semibold">Sub Kategori</label>
                                <a href="{{ admin.admin.subkategori.create') }}"
                                    class="text-blue-600 hover:text-blue-800 font-bold text-sm flex items-center transition-colors duration-200">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6">
                                        </path>
                                    </svg>
                                    Tambah Sub Kategori
                                </a>
                            </div>
                            <select name="id_sub_kategori" id="id_sub_kategori"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('id_sub_kategori') border-red-500 @enderror">
                                <option value="">Pilih Sub Kategori</option>
                                @foreach ($subkategoris as $subkategori)
                                    <option value="{{ $subkategori->id }}" {{ old('id_sub_kategori') == $subkategori->id ? 'selected' : '' }}>
                                        {{ $subkategori->nama_sub_kategori }} (Kategori: {{ $subkategori->kategori->nama_kategori }})
                                    </option>
                                @endforeach
                            </select>
                            @error('id_sub_kategori')
                                <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Input Kode Barang --}}
                        <div class="mb-6">
                            <label for="kode_barang" class="block text-gray-700 font-semibold mb-2">Kode Barang</label>
                            <input type="text" name="kode_barang" id="kode_barang"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('kode_barang') border-red-500 @enderror"
                                value="{{ old('kode_barang') }}">
                            @error('kode_barang')
                                <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Input Nama Barang --}}
                        <div class="mb-6">
                            <label for="nama_barang" class="block text-gray-700 font-semibold mb-2">Nama Barang</label>
                            <input type="text" name="nama_barang" id="nama_barang"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('nama_barang') border-red-500 @enderror"
                                value="{{ old('nama_barang') }}">
                            @error('nama_barang')
                                <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Input Keterangan --}}
                        <div class="mb-6">
                            <label for="keterangan" class="block text-gray-700 font-semibold mb-2">Keterangan (Opsional)</label>
                            <textarea name="keterangan" id="keterangan" rows="4"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('keterangan') border-red-500 @enderror">{{ old('keterangan') }}</textarea>
                            @error('keterangan')
                                <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Tombol Aksi --}}
                        <div class="flex justify-end space-x-4">
                            <a href="{{ admin.barang.index') }}"
                                class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-6 rounded-full transition-colors duration-200">
                                Batal
                            </a>
                            <button type="submit"
                                class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-full transition-colors duration-200">
                                Simpan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>

@endsection
