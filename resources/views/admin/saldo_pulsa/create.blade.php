@extends('layouts.admin')

@section('title', 'Tambah Saldo Pulsa Warung')

@section('content')

    {{-- Main Content --}}
    <div class="flex-1 flex flex-col overflow-hidden">
        
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
                            <label for="nominal" class="block text-sm font-medium text-gray-700 mb-2">Nominal Saldo (Rp)</label>
                            <input type="number" name="nominal" id="nominal" value="{{ old('nominal') }}"
                                class="w-full border border-gray-300 rounded-lg py-2 px-4 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('nominal') border-red-500 @enderror"
                                placeholder="Masukkan nominal saldo (misal: 100000)" required min="1">
                            @error('nominal')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-xs text-gray-500">Nilai saldo yang akan masuk ke sistem warung.</p>
                        </div>

                        {{-- Input Harga Beli (MODAL) --}}
                        <div class="mb-5">
                            <label for="harga_beli" class="block text-sm font-medium text-gray-700 mb-2">Harga Beli / Modal (Rp)</label>
                            <input type="number" name="harga_beli" id="harga_beli" value="{{ old('harga_beli') }}"
                                class="w-full border border-gray-300 rounded-lg py-2 px-4 focus:outline-none focus:ring-2 focus:ring-green-500 @error('harga_beli') border-red-500 @enderror"
                                placeholder="Masukkan harga beli (misal: 98500)" required min="1">
                            @error('harga_beli')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-xs text-gray-500">Biaya riil yang dibayarkan untuk mendapatkan saldo ini.</p>
                        </div>

                        {{-- Tombol Submit --}}
                        <div class="flex justify-end mt-6">
                            <button type="submit"
                                class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-lg transition-colors duration-200 flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Simpan Top-Up
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>
@endsection