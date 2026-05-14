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
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Kembali ke Daftar Saldo
                    </a>
                </div>

                {{-- Card Form --}}
                <div class="bg-white shadow-xl rounded-lg p-6 md:p-8">

                    {{-- ERROR VALIDASI --}}
                    @if ($errors->any())
                        <div class="mb-6 rounded-lg border border-red-300 bg-red-50 p-4">
                            <div class="flex items-center mb-2">
                                <svg class="w-5 h-5 text-red-600 mr-2" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4m0 4h.01M5.07 19h13.86c1.54 0 2.5-1.67 1.73-3L13.73 4c-.77-1.33-2.69-1.33-3.46 0L3.34 16c-.77 1.33.19 3 1.73 3z">
                                    </path>
                                </svg>

                                <h3 class="font-semibold text-red-700">
                                    Terjadi Kesalahan
                                </h3>
                            </div>

                            <ul class="list-disc list-inside text-sm text-red-700 space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    {{-- ERROR SESSION --}}
                    @if (session('error'))
                        <div class="mb-6 rounded-lg border border-red-300 bg-red-50 p-4 text-red-700">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4m0 4h.01M5.07 19h13.86c1.54 0 2.5-1.67 1.73-3L13.73 4c-.77-1.33-2.69-1.33-3.46 0L3.34 16c-.77 1.33.19 3 1.73 3z">
                                    </path>
                                </svg>

                                <span>{{ session('error') }}</span>
                            </div>
                        </div>
                    @endif

                    {{-- SUCCESS --}}
                    @if (session('success'))
                        <div class="mb-6 rounded-lg border border-green-300 bg-green-50 p-4 text-green-700">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7">
                                    </path>
                                </svg>

                                <span>{{ session('success') }}</span>
                            </div>
                        </div>
                    @endif
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
                                    <option value="{{ $warung->id }}"
                                        {{ old('id_warung') == $warung->id ? 'selected' : '' }}>
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
                            <label for="jenis_pulsa_id" class="block text-sm font-medium text-gray-700 mb-2">
                                Jenis Saldo Pulsa
                            </label>

                            <select name="jenis_pulsa_id" id="jenis_pulsa_id"
                                class="w-full border border-gray-300 rounded-lg py-2 px-4 focus:outline-none focus:ring-2 focus:ring-blue-500
        @error('jenis_pulsa_id') border-red-500 @enderror"
                                required>
                                <option value="">-- Pilih Jenis Saldo --</option>

                                @foreach ($jenisPulsa as $item)
                                    <option value="{{ $item->id }}"
                                        {{ old('jenis_pulsa_id') == $item->id ? 'selected' : '' }}>
                                        {{ $item->nama_jenis }}
                                    </option>
                                @endforeach
                            </select>

                            @error('jenis_pulsa_id')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>


                        {{-- Input Nominal Saldo --}}
                        <div class="mb-5">
                            <label for="nominal" class="block text-sm font-medium text-gray-700 mb-2">Nominal Saldo
                                (Rp)</label>
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
                            <label for="harga_beli" class="block text-sm font-medium text-gray-700 mb-2">Harga Alomogada
                                (Rp)</label>
                            <input type="number" name="harga_beli" id="harga_beli" value="{{ old('harga_beli') }}"
                                class="w-full border border-gray-300 rounded-lg py-2 px-4 focus:outline-none focus:ring-2 focus:ring-green-500 @error('harga_beli') border-red-500 @enderror"
                                placeholder="Masukkan harga beli (misal: 98500)" required min="1">
                            @error('harga_beli')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-xs text-gray-500">Biaya riil yang dibayarkan untuk mendapatkan saldo ini.
                            </p>
                        </div>


                        {{-- Input Harga Jual --}}

                        <div class="mb-5">
                            <label for="harga_jual" class="block text-sm font-medium text-gray-700 mb-2">Harga Jual
                                (Rp)</label>
                            <input type="number" name="harga_jual" id="harga_jual" value="{{ old('harga_jual') }}"
                                class="w-full border border-gray-300 rounded-lg py-2 px-4 focus:outline-none focus:ring-2 focus:ring-green-500 @error('harga_jual') border-red-500 @enderror"
                                placeholder="Masukkan harga jual (misal: 100000)" required min="1">
                            @error('harga_jual')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-xs text-gray-500">Harga jual yang akan digunakan saat menjual pulsa ke
                                warung.
                            </p>
                        </div>

                        {{-- Tombol Submit --}}
                        <div class="flex justify-end mt-6">
                            <button type="submit"
                                class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-lg transition-colors duration-200 flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
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
