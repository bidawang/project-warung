@extends('layouts.admin')

@section('title', 'Edit Barang')

@section('content')
<div class="flex-1 flex flex-col overflow-hidden">

    {{-- Header --}}
    <header class="flex justify-between items-center p-6 bg-white border-b-2 border-gray-200">
        <h1 class="text-2xl font-bold text-gray-800">Edit Barang</h1>
        <div class="flex items-center">
            <span class="mr-4 font-semibold hidden sm:inline">Admin User</span>
            <div class="w-10 h-10 bg-blue-500 rounded-full"></div>
        </div>
    </header>

    {{-- Main --}}
    <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100 p-6 md:p-10">
        <div class="container mx-auto">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8">
                <h1 class="text-3xl font-bold text-gray-800 mb-4 md:mb-0">Form Edit Barang</h1>
            </div>

            <div class="bg-white p-6 rounded-lg shadow-md">
                <form action="{{ route('admin.barang.update', $barang->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    {{-- Subkategori --}}
                    <div class="mb-6">
                        <div class="flex justify-between items-center mb-2">
                            <label class="block text-gray-700 font-semibold">Sub Kategori</label>
                        </div>
                        <select name="id_sub_kategori"
                            class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
                            @foreach ($subkategoris as $sub)
                                <option value="{{ $sub->id }}"
                                    {{ $barang->id_sub_kategori == $sub->id ? 'selected' : '' }}>
                                    {{ $sub->sub_kategori }} (Kategori: {{ $sub->kategori->kategori }})
                                </option>
                            @endforeach
                        </select>
                        @error('id_sub_kategori') <p class="text-red-500 text-sm mt-2">{{ $message }}</p> @enderror
                    </div>

                    {{-- Area Pembelian --}}
                    <div class="mb-6">
                        <label class="block text-gray-700 font-semibold mb-2">Area Pembelian</label>
                        <select name="id_area_pembelian"
                            class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">

                            @foreach ($areaPembelians as $area)
                                <option value="{{ $area->id }}"
                                    {{ $barang->asalBarang && $barang->asalBarang->id_area_pembelian == $area->id ? 'selected' : '' }}>
                                    {{ $area->area }}
                                </option>
                            @endforeach
                        </select>
                        @error('id_area_pembelian') <p class="text-red-500 text-sm mt-2">{{ $message }}</p> @enderror
                    </div>

                    {{-- Kode --}}
                    <div class="mb-6">
                        <label class="block text-gray-700 font-semibold mb-2">Kode Barang</label>
                        <input type="text" name="kode_barang"
                            value="{{ $barang->kode_barang }}"
                            class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
                        @error('kode_barang') <p class="text-red-500 text-sm mt-2">{{ $message }}</p> @enderror
                    </div>

                    {{-- Nama --}}
                    <div class="mb-6">
                        <label class="block text-gray-700 font-semibold mb-2">Nama Barang</label>
                        <input type="text" name="nama_barang"
                            value="{{ $barang->nama_barang }}"
                            class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
                        @error('nama_barang') <p class="text-red-500 text-sm mt-2">{{ $message }}</p> @enderror
                    </div>

                    {{-- Keterangan --}}
                    <div class="mb-6">
                        <label class="block text-gray-700 font-semibold mb-2">Keterangan</label>
                        <textarea name="keterangan" rows="4"
                                  class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">{{ $barang->keterangan }}</textarea>
                        @error('keterangan') <p class="text-red-500 text-sm mt-2">{{ $message }}</p> @enderror
                    </div>

                    {{-- Aksi --}}
                    <div class="flex justify-end space-x-4">
                        <a href="{{ route('admin.barang.index') }}"
                           class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-6 rounded-full">
                            Batal
                        </a>
                        <button class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-6 rounded-full">
                            Update
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </main>
</div>
@endsection
