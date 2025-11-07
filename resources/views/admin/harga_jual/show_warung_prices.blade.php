@extends('layouts.admin')

@section('title', 'Monitor Harga Barang: ' . $barang->nama_barang)

@section('content')

<div class="flex-1 flex flex-col overflow-hidden">
    <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100 p-6 md:p-10">
        <div class="container mx-auto">

            {{-- Header halaman --}}
            <div class="mb-6 flex justify-between items-center">
                <h1 class="text-3xl font-bold text-gray-800">
                    Harga Barang: <span class="text-blue-600">{{ $barang->nama_barang }}</span> di Semua Warung
                </h1>
                {{-- Ganti 'admin.barang.index' dengan rute daftar barang Anda --}}
                <a href="{{ route('admin.barang.index') }}"
                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Kembali ke Daftar Barang
                </a>
            </div>

            {{-- Card detail barang --}}
            <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
                <h2 class="text-xl font-extrabold text-gray-900 mb-4">Informasi Barang</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                    <p class="text-gray-600"><span class="font-semibold text-gray-800">ID:</span> {{ $barang->id }}</p>
                    <p class="text-gray-600"><span class="font-semibold text-gray-800">Nama:</span> {{ $barang->nama_barang }}</p>
                    <p class="text-gray-600"><span class="font-semibold text-gray-800">Keterangan:</span> {{ $barang->keterangan ?? '-' }}</p>
                </div>
            </div>

            {{-- Tabel Perbandingan Harga di Warung --}}
            <div class="mt-6">
                <h2 class="text-2xl font-bold text-gray-800 mb-4">Perbandingan Harga dan Stok per Warung</h2>
                <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">#</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Warung</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Area</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Stok Saat Ini</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Harga Modal</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Harga Jual Range</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Persentase Laba</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Laba Absolut</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($warungWithPriceData as $warung)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 text-sm font-medium">{{ $loop->iteration }}</td>
                                        <td class="px-6 py-4 text-sm font-semibold">
                                            {{-- Link ke detail warung (asumsi rute admin.warung.show sudah ada) --}}
                                            <a href="{{ route('admin.warung.show', $warung->id) }}" class="text-blue-600 hover:text-blue-800">
                                                {{ $warung->nama_warung }}
                                            </a>
                                            <div class="text-xs text-gray-500 mt-1">
                                                Pemilik: {{ $warung->user->name ?? '-' }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-sm">{{ $warung->area->area ?? '-' }}</td>
                                        <td class="px-6 py-4 text-sm {{ $warung->stok_saat_ini <= 0 ? 'text-red-600 font-bold' : '' }}">
                                            {{ $warung->stok_saat_ini }}
                                        </td>
                                        <td class="px-6 py-4 text-sm">
                                            Rp {{ number_format($warung->harga_modal, 0, ',', '.') }}
                                        </td>
                                        <td class="px-6 py-4 text-sm">
                                            @if ($warung->harga_jual_range_awal > 0)
                                                Rp {{ number_format($warung->harga_jual_range_awal, 0, ',', '.') }} - Rp {{ number_format($warung->harga_jual, 0, ',', '.') }}
                                            @else
                                                <span class="text-gray-400 italic">Harga Jual Belum Diatur</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-sm font-bold">
                                            @php
                                                // Gunakan string manipulasi untuk menentukan warna
                                                $isNegative = str_contains($warung->persentase_laba, '-') || (float) filter_var($warung->persentase_laba, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION) < 0;
                                            @endphp
                                            <span class="{{ $isNegative ? 'text-red-600' : 'text-green-600' }}">
                                                {{ $warung->persentase_laba }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-700">
                                            @php
                                                // Cek jika laba terkecil di range negatif
                                                $labaParts = explode(' - ', $warung->laba_range);
                                                $labaAwal = (int) str_replace(['Rp ', '.'], '', $labaParts[0]);
                                                $isNegativeLaba = $labaAwal < 0;
                                            @endphp
                                            <span class="{{ $isNegativeLaba ? 'text-red-600' : 'text-green-600' }} font-bold">
                                                Rp {{ $warung->laba_range }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="px-6 py-4 text-center text-sm text-gray-500">
                                            Tidak ada warung yang memiliki data harga atau stok untuk barang ini.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            {{-- End Tabel --}}
        </div>
    </main>
</div>

@endsection
