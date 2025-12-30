@extends('layouts.admin')

@section('title', 'Tambah Sub Kategori')

@section('content')

    {{-- Main Content --}}
    <div class="flex-1 flex flex-col overflow-hidden">
        
        {{-- Main Content --}}
        <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100 p-6 md:p-10">
            <div class="container mx-auto">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8">
                    <h1 class="text-3xl font-bold text-gray-800 mb-4 md:mb-0">Form Tambah Sub Kategori</h1>
                </div>

                <div class="bg-white p-6 rounded-lg shadow-md">
                    <form action="{{ route('admin.subkategori.store') }}" method="POST">
                        @csrf

                        {{-- Input Kategori --}}
                        <div class="mb-6">
                            <div class="flex justify-between items-center mb-2">
                                <label for="id_kategori" class="block text-gray-700 font-semibold">Kategori</label>
                                <a href="{{ route('admin.kategori.create') }}"
                                    class="text-blue-600 hover:text-blue-800 font-bold text-sm flex items-center transition-colors duration-200">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6">
                                        </path>
                                    </svg>
                                    Tambah Kategori
                                </a>
                            </div>
                            <select name="id_kategori" id="id_kategori"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('id_kategori') border-red-500 @enderror">
                                <option value="">Pilih Kategori</option>
                                @foreach ($kategoris as $kategori)
                                    <option value="{{ $kategori->id }}" {{ old('id_kategori') == $kategori->id ? 'selected' : '' }}>
                                        {{ $kategori->kategori }}
                                    </option>
                                @endforeach
                            </select>
                            @error('id_kategori')
                                <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Input Nama Sub Kategori --}}
                        <div class="mb-6">
                            <label for="sub_kategori" class="block text-gray-700 font-semibold mb-2">Nama Sub Kategori</label>
                            <input type="text" name="sub_kategori" id="sub_kategori"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('sub_kategori') border-red-500 @enderror"
                                value="{{ old('sub_kategori') }}">
                            @error('sub_kategori')
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
                            <a href="{{ route('admin.subkategori.index') }}"
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
