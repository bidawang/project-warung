@extends('layouts.admin')

@section('title', 'Tambah Aturan Tenggat')

@section('content')

<div class="flex-1 flex flex-col overflow-hidden bg-gray-100" 
     x-data="{ 
        tanggalAwal: '{{ old('tanggal_awal', 1) }}',
        tanggalAkhir: '{{ old('tanggal_akhir', 31) }}',
        jatuhTempo: '{{ old('jatuh_tempo_hari', 7) }}',
        bunga: '{{ old('bunga', 0) }}',
        
        // Fungsi simulasi sederhana untuk UI
        getSimulasi() {
            return `Setiap piutang yang muncul antara tanggal ${this.tanggalAwal} sampai ${this.tanggalAkhir} akan jatuh tempo ${this.jatuhTempo} hari setelah tanggal ${this.tanggalAkhir}.`;
        }
     }">

    {{-- Header --}}
    <header class="flex justify-between items-center p-6 bg-white border-b sticky top-0 z-10">
        <h1 class="text-2xl font-bold text-gray-800 flex items-center">
            <i class="fas fa-calendar-plus mr-2 text-indigo-600"></i> Tambah Aturan Tenggat
        </h1>
        <a href="{{ route('admin.hutang.index') }}" 
           class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg shadow-sm hover:bg-gray-300 transition duration-150 font-semibold">
            <i class="fas fa-arrow-left mr-1"></i> Batal
        </a>
    </header>

    <main class="flex-1 overflow-y-auto p-6">
        <div class="max-w-4xl mx-auto">
            
            {{-- Form Card --}}
            <div class="bg-white shadow-xl rounded-xl overflow-hidden border-t-4 border-indigo-600">
                <form action="{{ route('admin.aturanTenggat.store') }}" method="POST" class="p-6 sm:p-8">
                    @csrf

                    <div class="space-y-8">
                        {{-- Bagian 1: Identitas --}}
                        <section>
                            <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                                <span class="bg-indigo-100 text-indigo-600 w-8 h-8 rounded-full flex items-center justify-center mr-3 text-sm">1</span>
                                Pilih Target Warung
                            </h2>
                            <div class="grid grid-cols-1 gap-4">
                                <div>
                                    <label for="id_warung" class="block text-sm font-medium text-gray-700 mb-1">Nama Warung</label>
                                    <select id="id_warung" name="id_warung" required
                                        class="block w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm p-2.5 bg-gray-50">
                                        <option value="" disabled selected>-- Pilih Warung --</option>
                                        @foreach ($warung as $item)
                                            <option value="{{ $item->id }}" {{ old('id_warung') == $item->id ? 'selected' : '' }}>
                                                {{ $item->nama_warung }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('id_warung')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </section>

                        <hr class="border-gray-100">

                        {{-- Bagian 2: Aturan Waktu --}}
                        <section>
                            <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                                <span class="bg-indigo-100 text-indigo-600 w-8 h-8 rounded-full flex items-center justify-center mr-3 text-sm">2</span>
                                Periode Tagihan & Jatuh Tempo
                            </h2>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="tanggal_awal" class="block text-sm font-medium text-gray-700">Hari Awal Siklus (1-31)</label>
                                    <input type="number" id="tanggal_awal" name="tanggal_awal" x-model="tanggalAwal" min="1" max="31" required
                                        class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm p-2.5">
                                    <p class="mt-1 text-xs text-gray-500 italic">Mulai penghitungan dari hari ke-X</p>
                                    @error('tanggal_awal') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label for="tanggal_akhir" class="block text-sm font-medium text-gray-700">Hari Akhir Siklus (1-31)</label>
                                    <input type="number" id="tanggal_akhir" name="tanggal_akhir" x-model="tanggalAkhir" min="1" max="31" required
                                        class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm p-2.5">
                                    <p class="mt-1 text-xs text-gray-500 italic">Batas akhir periode transaksi</p>
                                    @error('tanggal_akhir') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label for="jatuh_tempo_hari" class="block text-sm font-medium text-gray-700">Masa Tenggat (Hari)</label>
                                    <input type="number" id="jatuh_tempo_hari" name="jatuh_tempo_hari" x-model="jatuhTempo" min="1" required
                                        class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm p-2.5">
                                    <p class="mt-1 text-xs text-gray-500">Jarak dari Tanggal Akhir ke Jatuh Tempo</p>
                                    @error('jatuh_tempo_hari') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label for="bunga" class="block text-sm font-medium text-gray-700">Bunga Keterlambatan (%)</label>
                                    <div class="mt-1 flex rounded-lg shadow-sm">
                                        <input type="number" step="0.01" id="bunga" name="bunga" x-model="bunga" required
                                            class="flex-1 block w-full border-gray-300 rounded-l-lg focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm p-2.5">
                                        <span class="inline-flex items-center px-4 rounded-r-lg border border-l-0 border-gray-300 bg-gray-100 text-gray-600 font-bold">
                                            %
                                        </span>
                                    </div>
                                    @error('bunga') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                                </div>
                            </div>
                        </section>

                        {{-- Preview Box (Alpine.js) --}}
                        <div class="bg-indigo-50 border-l-4 border-indigo-400 p-4 rounded-r-lg">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-info-circle text-indigo-400"></i>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-bold text-indigo-800">Simulasi Aturan:</h3>
                                    <p class="text-sm text-indigo-700 mt-1" x-text="getSimulasi()"></p>
                                </div>
                            </div>
                        </div>

                        <hr class="border-gray-100">

                        {{-- Bagian 3: Keterangan --}}
                        <section>
                            <label for="keterangan" class="block text-sm font-medium text-gray-700 mb-1">Keterangan Opsional</label>
                            <textarea id="keterangan" name="keterangan" rows="3" placeholder="Contoh: Aturan khusus pelanggan grosir..."
                                class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-lg p-2.5 bg-gray-50">{{ old('keterangan') }}</textarea>
                            @error('keterangan')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </section>
                    </div>

                    {{-- Action Buttons --}}
                    <div class="mt-10 pt-6 border-t border-gray-100 flex items-center justify-between">
                        <p class="text-xs text-gray-400 italic">* Pastikan periode tanggal tidak tumpang tindih dengan aturan lain di warung yang sama.</p>
                        <div class="flex space-x-3">
                            <button type="submit"
                                class="inline-flex justify-center py-2.5 px-6 border border-transparent shadow-sm text-sm font-bold rounded-lg text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150">
                                <i class="fas fa-save mr-2"></i> Simpan Aturan
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </main>
</div>

@endsection