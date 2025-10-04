@extends('layouts.admin')

@section('title', 'Manajemen Varian Kuantitas')

@section('content')
<div class="flex-1 flex flex-col overflow-hidden">
    {{-- Header (Menyesuaikan dari View TransaksiBarang) --}}
    <header class="flex justify-between items-center p-6 bg-white border-b-2 border-gray-200 shadow-sm">
        <button id="openSidebarBtn" class="text-gray-500 hover:text-gray-900 lg:hidden">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M4 6h16M4 12h16M4 18h16"></path>
            </svg>
        </button>
        <h1 class="text-2xl font-bold text-gray-800">Manajemen Varian Kuantitas Barang</h1>
        <div class="flex items-center">
            <span class="mr-4 font-semibold hidden sm:inline">Admin User</span>
            <div class="w-10 h-10 bg-blue-500 rounded-full"></div>
        </div>
    </header>

    {{-- Notifikasi Error --}}
    @if ($errors->any())
    <div class="bg-red-100 border border-red-400 text-red-700 px-6 py-3">
        <ul class="list-disc list-inside text-sm">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    {{-- Notifikasi Sukses --}}
    @if(session('success'))
    <div class="px-6 py-3 bg-green-100 border border-green-300 text-green-700 shadow-sm">
        {{ session('success') }}
    </div>
    @endif

    {{-- Main Content - Split View --}}
    <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-50 p-6 md:p-10">
        <div class="container mx-auto grid grid-cols-1 lg:grid-cols-3 gap-8">

            {{-- Kolom Kiri: Daftar Kuantitas Per Barang (2/3) --}}
            <div class="lg:col-span-2">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-bold text-gray-800">Riwayat Varian Kuantitas</h2>
                </div>

                <div class="space-y-6">
                    {{-- Loop Utama: Mengelompokkan berdasarkan Barang --}}
                    @forelse($groupedKuantitas as $barangId => $kuantitasCollection)
                        @php
                            $barang = $kuantitasCollection->first()->stokWarung->barang;
                            $barangName = $barang->nama_barang ?? 'Barang Tidak Ditemukan';
                            $totalVarian = $kuantitasCollection->count();
                        @endphp

                        <div class="border border-gray-300 rounded-xl p-5 bg-white shadow-md hover:shadow-lg transition duration-200">
                            {{-- Header Barang --}}
                            <h3 class="font-bold text-xl text-indigo-700 mb-3 border-b-2 border-indigo-100 pb-2 flex justify-between items-center">
                                <span>{{ $barangName }}</span>
                                <span class="text-base font-medium text-gray-500">{{ $totalVarian }} Varian</span>
                            </h3>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                {{-- Loop Kedua: Menampilkan setiap Kuantitas dari Barang tersebut --}}
                                @foreach($kuantitasCollection->sortByDesc('created_at') as $kuantitas)
                                    @php
                                        $hargaTotal = $kuantitas->jumlah * $kuantitas->harga_jual;
                                    @endphp
                                    <div class="p-3 rounded-lg border border-indigo-200 bg-indigo-50 shadow-sm">
                                        <p class="font-semibold text-base text-gray-900">
                                            Kuantitas: <span class="text-indigo-600">{{ number_format($kuantitas->jumlah) }} pcs</span>
                                        </p>
                                        <div class="text-xs text-gray-700 mt-1 space-y-0.5">
                                            <p>Harga Satuan: **Rp {{ number_format($kuantitas->harga_jual, 0, ',', '.') }}**</p>
                                            <p>Harga Total: **Rp {{ number_format($hargaTotal, 0, ',', '.') }}**</p>
                                            <p class="italic text-gray-500">Dibuat di: {{ $kuantitas->stokWarung->warung->nama_warung ?? 'N/A' }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @empty
                        <div class="p-5 bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 rounded-lg">
                            <p class="text-sm italic font-medium">⚠️ Tidak ada data Kuantitas yang tersimpan untuk barang yang dipilih, atau belum ada stok warung yang dipilih.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- Kolom Kanan: Form Penambahan Kuantitas (1/3) --}}
            <div class="lg:col-span-1 bg-white p-6 rounded-xl shadow-xl h-fit sticky top-6 border border-gray-100">
                <h2 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
                    <svg class="w-6 h-6 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Tambah Kuantitas Baru
                </h2>

                <form action="{{ route('kasir.kuantitas.store') }}" method="POST">
                    @csrf

                    {{-- Pilihan Stok Warung (Wajib) --}}
                    <div class="mb-6">
                        <label for="id_stok_warung" class="block text-sm font-semibold text-gray-700 mb-2">
                            Pilih Stok Warung
                        </label>
                        @if(isset($selectedStokWarung))
                            <div class="p-3 border border-green-300 rounded-lg bg-green-50 text-green-800 font-semibold text-sm shadow-inner">
                                <span class="block">Barang: {{ $selectedStokWarung->barang->nama_barang }}</span>
                                <span class="block mt-1">Warung: {{ $selectedStokWarung->warung->nama_warung }} (Stok: {{ number_format($selectedStokWarung->stok) }})</span>
                            </div>
                            <input type="hidden" name="id_stok_warung" value="{{ $selectedStokWarung->id }}">
                            <a href="{{ route('kasir.kuantitas.create') }}" class="mt-2 inline-block text-xs text-blue-600 hover:text-blue-800 transition duration-150">Ganti Stok Warung</a>
                        @else
                            <select name="id_stok_warung" id="id_stok_warung" class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-white focus:ring-indigo-500 focus:border-indigo-500 transition duration-150 ease-in-out shadow-sm" required
                                onchange="window.location.href = '{{ route('kasir.kuantitas.create') }}?id_stok_warung=' + this.value">
                                <option value="">-- Pilih Stok Warung untuk Aktifkan Form --</option>
                                @foreach($stokWarung as $s)
                                <option value="{{ $s->id }}" {{ old('id_stok_warung') == $s->id ? 'selected' : '' }}>
                                    {{ $s->barang->nama_barang }} - {{ $s->warung->nama_warung }} (Stok: {{ number_format($s->stok) }})
                                </option>
                                @endforeach
                            </select>
                            <p class="mt-2 text-xs text-red-500 italic">Pilih stok warung untuk mengaktifkan input kuantitas dan harga.</p>
                        @endif
                    </div>

                    @if(isset($selectedStokWarung))
                    {{-- Form Input --}}
                    <div class="space-y-5">
                        <div>
                            <label for="jumlah" class="block text-sm font-medium text-gray-700 mb-1">Jumlah per Kuantitas (pcs)</label>
                            <input type="number" name="jumlah" id="jumlah" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 shadow-sm" value="{{ old('jumlah', 2) }}" min="2" required>
                            <small class="text-xs text-gray-500 mt-1 block">Minimal 2 pcs per kuantitas.</small>
                        </div>

                        <div>
                            <label for="harga_jual" class="block text-sm font-medium text-gray-700 mb-1">Harga Jual Satuan (Rp)</label>
                            <input type="number" name="harga_jual" id="harga_jual" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 shadow-sm" min="0" required
                                value="{{ old('harga_jual', $hargaJualSatuanDasar) }}">

                            @if($hargaJualSatuanDasar > 0)
                            <small class="mt-2 text-xs text-green-600 font-medium block p-2 bg-green-50 border border-green-200 rounded">
                                Harga Satuan Dasar Disarankan: **Rp {{ number_format($hargaJualSatuanDasar, 0, ',', '.') }}**
                            </small>
                            @else
                            <small class="mt-2 text-xs text-yellow-600 block p-2 bg-yellow-50 border border-yellow-200 rounded">
                                Harga dasar belum terhitung. Input harga jual secara manual.
                            </small>
                            @endif
                        </div>
                    </div>

                    {{-- Tombol Aksi --}}
                    <div class="flex items-center mt-8 pt-5 border-t border-gray-200">
                        <button type="submit" class="w-full px-6 py-2 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition duration-150 ease-in-out shadow-md hover:shadow-lg">
                            Simpan Kuantitas Baru
                        </button>
                    </div>
                    @endif
                </form>
            </div>
        </div>
    </main>
</div>
@endsection
