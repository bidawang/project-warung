@extends('layouts.admin')

@section('title', 'Tambah Area Pembelian')

@section('content')

    {{-- Main Content --}}
    <div class="flex-1 flex flex-col overflow-hidden">
        {{-- Header --}}
        <header class="flex justify-between items-center p-6 bg-white border-b-2 border-gray-200">
            <h1 class="text-2xl font-bold text-gray-800">Tambah Area Pembelian</h1>
            <div class="flex items-center">
                <span class="mr-4 font-semibold hidden sm:inline">Admin User</span>
                <div class="w-10 h-10 bg-blue-500 rounded-full"></div>
            </div>
        </header>

        {{-- Main Content --}}
        <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100 p-6 md:p-10">
            <div class="container mx-auto">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8">
                    <h1 class="text-3xl font-bold text-gray-800 mb-4 md:mb-0">Form Tambah Area Pembelian</h1>
                </div>

                <div class="bg-white p-6 rounded-lg shadow-md">
                    <form action="{{ route('admin.areapembelian.store') }}" method="POST" class="space-y-6">
                        @csrf

                        {{-- Input Nama Area --}}
                        <div>
                            <label for="area" class="block text-gray-700 font-semibold mb-2">Nama Area</label>
                            <input type="text" name="area" id="area" value="{{ old('area') }}"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('area') border-red-500 @enderror"
                                required>
                            @error('area')
                                <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Input Markup --}}
                        <div>
                            <label for="markup" class="block text-gray-700 font-semibold mb-2">Markup (%)</label>
                            <input type="number" step="0.01" name="markup" id="markup" value="{{ old('markup') }}"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('markup') border-red-500 @enderror"
                                required>
                            @error('markup')
                                <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Input Keterangan --}}
                        <div>
                            <label for="keterangan" class="block text-gray-700 font-semibold mb-2">Keterangan (Opsional)</label>
                            <textarea name="keterangan" id="keterangan" rows="4"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('keterangan') border-red-500 @enderror">{{ old('keterangan') }}</textarea>
                            @error('keterangan')
                                <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Tombol Aksi --}}
                        <div class="flex justify-end space-x-4">
                            <a href="{{ route('admin.areapembelian.index') }}"
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
