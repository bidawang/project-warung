@extends('layouts.admin')

@section('title', 'Tambah Barang')

@section('content')
    <div class="flex-1 flex flex-col overflow-hidden bg-gray-100">

        {{-- Main --}}
        <main class="flex-1 overflow-y-auto p-6 md:p-10">

            <div class="max-w-5xl mx-auto">

                {{-- Card --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">

                    {{-- Top --}}
                    <div class="px-8 py-6 border-b bg-gradient-to-r from-blue-50 to-indigo-50">
                        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                            <div>
                                <h2 class="text-xl font-bold text-gray-800">
                                    Form Input Barang
                                </h2>
                                <p class="text-sm text-gray-500 mt-1">
                                    Isi informasi barang dengan lengkap
                                </p>
                            </div>

                            <a href="{{ route('admin.barang.index') }}"
                                class="inline-flex items-center justify-center px-5 py-2.5 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold transition">

                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mr-2" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 19l-7-7 7-7" />
                                </svg>

                                Kembali
                            </a>
                        </div>
                    </div>

                    {{-- Form --}}
                    <form action="{{ route('admin.barang.store') }}" method="POST" class="p-8" x-data="{ loading: false }"
                        @submit="loading = true">

                        @csrf

                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                            {{-- Subkategori --}}
                            <div class="lg:col-span-2">

                                <div class="flex justify-between items-center mb-2">
                                    <label class="text-sm font-semibold text-gray-700">
                                        Sub Kategori
                                    </label>

                                    <a href="{{ route('admin.subkategori.create') }}"
                                        class="inline-flex items-center text-sm font-semibold text-blue-600 hover:text-blue-800 transition">

                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 6v12m6-6H6" />
                                        </svg>

                                        Tambah Sub Kategori
                                    </a>
                                </div>

                                <select name="id_sub_kategori" id="id_sub_kategori" class="select2 w-full">

                                    <option value="">
                                        Pilih Sub Kategori
                                    </option>

                                    @foreach ($subkategoris as $sub)
                                        <option value="{{ $sub->id }}"
                                            {{ old('id_sub_kategori') == $sub->id ? 'selected' : '' }}>
                                            {{ $sub->sub_kategori }}
                                            ({{ $sub->kategori->kategori }})
                                        </option>
                                    @endforeach
                                </select>

                                @error('id_sub_kategori')
                                    <p class="mt-2 text-sm text-red-500">
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            {{-- Kode Barang --}}
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    Kode Barang
                                </label>

                                <input type="text" name="kode_barang" value="{{ old('kode_barang') }}"
                                    placeholder="Contoh: BRG-001"
                                    class="w-full rounded-xl border border-gray-300 px-4 py-3 focus:ring-4 focus:ring-blue-100 focus:border-blue-500 transition">

                                @error('kode_barang')
                                    <p class="mt-2 text-sm text-red-500">
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            {{-- Nama Barang --}}
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    Nama Barang
                                </label>

                                <input type="text" name="nama_barang" value="{{ old('nama_barang') }}"
                                    placeholder="Masukkan nama barang"
                                    class="w-full rounded-xl border border-gray-300 px-4 py-3 focus:ring-4 focus:ring-blue-100 focus:border-blue-500 transition">

                                @error('nama_barang')
                                    <p class="mt-2 text-sm text-red-500">
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            {{-- Keterangan --}}
                            <div class="lg:col-span-2">
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    Keterangan
                                </label>

                                <textarea name="keterangan" rows="5" placeholder="Tambahkan catatan atau deskripsi barang..."
                                    class="w-full rounded-xl border border-gray-300 px-4 py-3 focus:ring-4 focus:ring-blue-100 focus:border-blue-500 transition resize-none">{{ old('keterangan') }}</textarea>

                                @error('keterangan')
                                    <p class="mt-2 text-sm text-red-500">
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                        </div>

                        {{-- Action --}}
                        <div class="flex flex-col sm:flex-row justify-end gap-3 mt-10">

                            <a href="{{ route('admin.barang.index') }}"
                                class="px-6 py-3 rounded-xl bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold text-center transition">
                                Batal
                            </a>

                            <button type="submit" :disabled="loading"
                                class="px-6 py-3 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-semibold shadow-lg shadow-blue-200 transition flex items-center justify-center gap-2">

                                <svg x-show="loading" class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg"
                                    fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                        stroke-width="4"></circle>

                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                                </svg>

                                <span x-text="loading ? 'Menyimpan...' : 'Simpan Barang'"></span>
                            </button>

                        </div>

                    </form>

                </div>
            </div>
        </main>
    </div>

<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        window.$ = window.jQuery = jQuery;

        $(document).ready(function() {

            $('#id_sub_kategori').select2({
                placeholder: 'Pilih Sub Kategori',
                width: '100%'
            });

        });
    </script>

@endsection
