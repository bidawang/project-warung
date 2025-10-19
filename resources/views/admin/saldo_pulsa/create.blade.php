@extends('layouts.admin')

@section('title', 'Tambah Saldo Pulsa Warung')

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
                <h1 class="text-2xl font-bold text-gray-800">Manajemen Saldo Pulsa</h1>
            </div>
            <div class="flex items-center">
                <span class="mr-4 font-semibold hidden sm:inline">Admin User</span>
                <div class="w-10 h-10 bg-blue-500 rounded-full"></div>
            </div>
        </header>

        {{-- Main Content --}}
        <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100 p-6 md:p-10">
            <div class="container mx-auto max-w-3xl">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-3xl font-bold text-gray-800">Formulir Top-Up Saldo Pulsa Warung</h2>
                    <a href="{{ route('admin.saldo-pulsa.index') }}"
                        class="text-blue-600 hover:text-blue-800 transition-colors duration-200 flex items-center">
                        <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Kembali ke Daftar Saldo
                    </a>
                </div>

                {{-- Card Form --}}
                <div class="bg-white shadow-xl rounded-lg p-6 md:p-8">
                    {{-- Form Top-Up Saldo --}}
                    <form action="{{ route('admin.saldo-pulsa.store') }}" method="POST">
                        @csrf

                        {{-- Input Warung --}}
                        <div class="mb-5">
                            <label for="id_warung" class="block text-sm font-medium text-gray-700 mb-2">Pilih Warung</label>
                            <select name="id_warung" id="id_warung"
                                class="w-full border border-gray-300 rounded-lg py-2 px-4 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('id_warung') border-red-500 @enderror"
                                required>
                                <option value="">-- Pilih Warung --</option>
                                {{-- Variabel $warungs diasumsikan berisi semua data warung yang dilewatkan dari controller --}}
                                @foreach ($warungs as $warung)
                                    <option value="{{ $warung->id }}" {{ old('id_warung') == $warung->id ? 'selected' : '' }}>
                                        {{ $warung->nama_warung }}
                                    </option>
                                @endforeach
                            </select>
                            @error('id_warung')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Input Jenis Pulsa --}}
                        <div class="mb-5">
                            <label for="jenis" class="block text-sm font-medium text-gray-700 mb-2">Jenis Saldo Pulsa</label>
                            <select name="jenis" id="jenis"
                                class="w-full border border-gray-300 rounded-lg py-2 px-4 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('jenis') border-red-500 @enderror"
                                required>
                                <option value="">-- Pilih Jenis Saldo --</option>
                                <option value="hp" {{ old('jenis') == 'hp' ? 'selected' : '' }}>Pulsa Handphone</option>
                                <option value="listrik" {{ old('jenis') == 'listrik' ? 'selected' : '' }}>Pulsa Listrik (Token)</option>
                            </select>
                            @error('jenis')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Input Nominal Saldo --}}
                        <div class="mb-5">
                            <label for="nominal" class="block text-sm font-medium text-gray-700 mb-2">Nominal Saldo yang Ditambahkan (Rp)</label>
                            <input type="number" name="nominal" id="nominal" value="{{ old('nominal') }}"
                                class="w-full border border-gray-300 rounded-lg py-2 px-4 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('nominal') border-red-500 @enderror"
                                placeholder="Masukkan jumlah saldo yang ditambahkan (misal: 100000)" required min="1">
                            @error('nominal')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-xs text-gray-500">Nilai ini akan ditambahkan ke saldo pulsa yang sudah ada di warung.</p>
                        </div>

                        {{-- Tombol Submit --}}
                        <div class="flex justify-end mt-6">
                            <button type="submit"
                                class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-lg transition-colors duration-200 flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Top-Up Saldo
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>
@endsection
