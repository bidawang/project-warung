@extends('layouts.admin')

@section('title', 'Edit Aturan Tenggat: #' . $aturanTenggat->id)

@section('content')

<div class="flex-1 flex flex-col overflow-hidden bg-gray-100" 
     x-data="{ 
        tanggalAwal: '{{ old('tanggal_awal', $aturanTenggat->tanggal_awal) }}',
        tanggalAkhir: '{{ old('tanggal_akhir', $aturanTenggat->tanggal_akhir) }}',
        jatuhTempo: '{{ old('jatuh_tempo_hari', $aturanTenggat->jatuh_tempo_hari) }}',
        bunga: '{{ old('bunga', $aturanTenggat->bunga) }}',
        
        getSimulasi() {
            return `Setiap piutang yang muncul antara tanggal ${this.tanggalAwal} sampai ${this.tanggalAkhir} akan jatuh tempo ${this.jatuhTempo} hari setelah tanggal ${this.tanggalAkhir}.`;
        }
     }">

    {{-- Header --}}
    <header class="flex justify-between items-center p-6 bg-white border-b sticky top-0 z-10">
        <h1 class="text-2xl font-bold text-gray-800 flex items-center">
            <i class="fas fa-edit mr-2 text-orange-500"></i> Edit Aturan Tenggat
            <span class="text-xl font-medium ml-3 text-gray-400">#{{ $aturanTenggat->id }}</span>
        </h1>
        <a href="{{ route('admin.aturanTenggat.index') }}" 
           class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg shadow-sm hover:bg-gray-300 transition duration-150 font-semibold">
            <i class="fas fa-arrow-left mr-1"></i> Kembali
        </a>
    </header>
    
    <main class="flex-1 overflow-y-auto p-6">
        <div class="max-w-4xl mx-auto">
            
            <div class="bg-white shadow-xl rounded-xl overflow-hidden border-t-4 border-orange-500">
                <form action="{{ route('admin.aturanTenggat.update', $aturanTenggat->id) }}" method="POST" class="p-6 sm:p-8">
                    @csrf
                    @method('PUT')

                    <div class="space-y-8">
                        {{-- Bagian 1: Identitas Warung --}}
                        <section>
                            <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                                <span class="bg-orange-100 text-orange-600 w-8 h-8 rounded-full flex items-center justify-center mr-3 text-sm">1</span>
                                Konfigurasi Warung
                            </h2>
                            <div class="grid grid-cols-1 gap-4">
                                <div>
                                    <label for="id_warung" class="block text-sm font-medium text-gray-700 mb-1">Pilih Warung</label>
                                    <select id="id_warung" name="id_warung" required
                                        class="block w-full border-gray-300 rounded-lg shadow-sm focus:ring-orange-500 focus:border-orange-500 sm:text-sm p-2.5 bg-gray-50">
                                        @foreach ($warung as $item)
                                            <option value="{{ $item->id }}" {{ old('id_warung', $aturanTenggat->id_warung) == $item->id ? 'selected' : '' }}>
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

                        {{-- Bagian 2: Parameter Waktu --}}
                        <section>
                            <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                                <span class="bg-orange-100 text-orange-600 w-8 h-8 rounded-full flex items-center justify-center mr-3 text-sm">2</span>
                                Siklus Tagihan & Bunga
                            </h2>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="tanggal_awal" class="block text-sm font-medium text-gray-700">Hari Awal (1-31)</label>
                                    <input type="number" id="tanggal_awal" name="tanggal_awal" x-model="tanggalAwal" min="1" max="31" required
                                        class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-orange-500 focus:border-orange-500 sm:text-sm p-2.5">
                                    @error('tanggal_awal') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label for="tanggal_akhir" class="block text-sm font-medium text-gray-700">Hari Akhir (1-31)</label>
                                    <input type="number" id="tanggal_akhir" name="tanggal_akhir" x-model="tanggalAkhir" min="1" max="31" required
                                        class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-orange-500 focus:border-orange-500 sm:text-sm p-2.5">
                                    @error('tanggal_akhir') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label for="jatuh_tempo_hari" class="block text-sm font-medium text-gray-700">Jatuh Tempo (Hari)</label>
                                    <input type="number" id="jatuh_tempo_hari" name="jatuh_tempo_hari" x-model="jatuhTempo" min="1" required
                                        class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-orange-500 focus:border-orange-500 sm:text-sm p-2.5">
                                    @error('jatuh_tempo_hari') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label for="bunga" class="block text-sm font-medium text-gray-700">Bunga Terlambat (%)</label>
                                    <div class="mt-1 flex rounded-lg shadow-sm">
                                        <input type="number" step="0.01" id="bunga" name="bunga" x-model="bunga" required
                                            class="flex-1 block w-full border-gray-300 rounded-l-lg focus:ring-orange-500 focus:border-orange-500 sm:text-sm p-2.5">
                                        <span class="inline-flex items-center px-4 rounded-r-lg border border-l-0 border-gray-300 bg-gray-100 text-gray-600 font-bold">
                                            %
                                        </span>
                                    </div>
                                    @error('bunga') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                                </div>
                            </div>
                        </section>

                        {{-- Live Preview Box --}}
                        <div class="bg-orange-50 border-l-4 border-orange-400 p-4 rounded-r-lg shadow-inner">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-sync-alt text-orange-400 animate-spin-slow"></i>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-bold text-orange-800 uppercase tracking-wider">Preview Pembaruan Aturan:</h3>
                                    <p class="text-sm text-orange-700 mt-1 leading-relaxed" x-text="getSimulasi()"></p>
                                </div>
                            </div>
                        </div>

                        <hr class="border-gray-100">

                        {{-- Bagian 3: Keterangan --}}
                        <section>
                            <label for="keterangan" class="block text-sm font-medium text-gray-700 mb-1">Keterangan Perubahan</label>
                            <textarea id="keterangan" name="keterangan" rows="3" 
                                class="shadow-sm focus:ring-orange-500 focus:border-orange-500 block w-full sm:text-sm border-gray-300 rounded-lg p-2.5 bg-gray-50">{{ old('keterangan', $aturanTenggat->keterangan) }}</textarea>
                            @error('keterangan')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </section>
                    </div>

                    {{-- Action Buttons --}}
                    <div class="mt-10 pt-6 border-t border-gray-100 flex items-center justify-end space-x-4">
                        <a href="{{ route('admin.aturanTenggat.index') }}" class="text-sm font-semibold text-gray-500 hover:text-gray-700 transition">
                            Batalkan Perubahan
                        </a>
                        <button type="submit"
                            class="inline-flex justify-center py-2.5 px-8 border border-transparent shadow-lg text-sm font-bold rounded-lg text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition duration-150 transform hover:-translate-y-0.5">
                            <i class="fas fa-check-circle mr-2"></i> Update Aturan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </main>
</div>

<style>
    .animate-spin-slow {
        animation: spin 3s linear infinite;
    }
    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
</style>

@endsection