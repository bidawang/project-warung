@extends('layouts.admin')

@section('title', 'Riwayat Hutang Semua Warung')

@section('content')
<div class="p-4 sm:p-6 lg:p-8">
    <h1 class="text-3xl font-bold text-gray-900 mb-6">
        Tambah Aturan Tenggat Hutang
    </h1>

    <div class="mt-8 flow-root">
        <div class="max-w-4xl mx-auto bg-white shadow-lg rounded-xl overflow-hidden">
            <div class="px-4 py-5 sm:p-6">
                <h2 class="text-xl font-semibold text-indigo-600 mb-4 border-b pb-2">Form Aturan Tenggat</h2>

                <form action="{{ route('admin.aturanTenggat.store') }}" method="POST">
                    @csrf

                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-6">

                        <div class="sm:col-span-6">
                            <label for="id_warung" class="block text-sm font-medium text-gray-700">Warung</label>
                            <select id="id_warung" name="id_warung" required
                                class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                <option value="" disabled selected>Pilih Warung</option>
                                {{-- Loop data warung di sini --}}
                                @foreach ($warung as $item)
                                    <option value="{{ $item->id }}">{{ $item->nama_warung }}</option>
                                @endforeach


                            </select>
                            @error('id_warung')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="mt-8 border-t border-gray-200 pt-8">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Periode Tagihan & Jatuh Tempo</h3>
                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-6">
                            <div class="sm:col-span-3">
                                <label for="tanggal_awal" class="block text-sm font-medium text-gray-700">Tanggal Awal Tagihan (Hari ke-)</label>
                                <input type="number" id="tanggal_awal" name="tanggal_awal" value="{{ old('tanggal_awal') }}" min="1" max="31" placeholder="Masukkan angka 1-31" required
                                    class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                @error('tanggal_awal')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-2 text-sm text-gray-500">Contoh: **1** untuk hari pertama bulan.</p>
                            </div>

                            <div class="sm:col-span-3">
                                <label for="tanggal_akhir" class="block text-sm font-medium text-gray-700">Tanggal Akhir Tagihan (Hari ke-)</label>
                                <input type="number" id="tanggal_akhir" name="tanggal_akhir" value="{{ old('tanggal_akhir') }}" min="1" max="31" placeholder="Masukkan angka 1-31" required
                                    class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                @error('tanggal_akhir')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-2 text-sm text-gray-500">Contoh: **31** untuk hari terakhir bulan.</p>
                            </div>

                            <div class="sm:col-span-3">
                                <label for="jatuh_tempo_hari" class="block text-sm font-medium text-gray-700">Jatuh Tempo Setelah Tagihan (Hari)</label>
                                <input type="number" id="jatuh_tempo_hari" name="jatuh_tempo_hari" value="{{ old('jatuh_tempo_hari') }}" min="1" placeholder="Misalnya 7 (untuk 7 hari)" required
                                    class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                @error('jatuh_tempo_hari')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-2 text-sm text-gray-500">Berapa hari setelah tanggal akhir periode tagihan.</p>
                            </div>

                            <div class="sm:col-span-3">
                                <label for="bunga" class="block text-sm font-medium text-gray-700">Bunga Keterlambatan (%)</label>
                                <div class="mt-1 flex rounded-md shadow-sm">
                                    <input type="text" id="bunga" name="bunga" value="{{ old('bunga') }}" placeholder="Contoh: 5" required
                                        class="focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-none rounded-l-md sm:text-sm border-gray-300">
                                    <span class="inline-flex items-center px-3 rounded-r-md border border-l-0 border-gray-300 bg-gray-50 text-gray-500 sm:text-sm">
                                        %
                                    </span>
                                </div>
                                @error('bunga')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-2 text-sm text-gray-500">Masukkan nilai bunga (contoh: **5** untuk 5%).</p>
                            </div>
                        </div>
                    </div>


                    <div class="mt-6">
                        <label for="keterangan" class="block text-sm font-medium text-gray-700">Keterangan (Opsional)</label>
                        <div class="mt-1">
                            <textarea id="keterangan" name="keterangan" rows="3"
                                class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 mt-1 block w-full sm:text-sm border border-gray-300 rounded-md">{{ old('keterangan') }}</textarea>
                        </div>
                        @error('keterangan')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-2 text-sm text-gray-500">Catatan tambahan mengenai aturan ini.</p>
                    </div>

                    <div class="mt-6 pt-5 border-t border-gray-200 flex justify-end space-x-3">
                        <a href="{{ route('admin.hutang.index') }}"
                            class="inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Batal
                        </a>
                        <button type="submit"
                            class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Simpan Aturan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
