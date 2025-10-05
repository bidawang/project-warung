@extends('layouts.admin')

@section('title', 'Detail Warung')

@section('content')

<div class="flex-1 flex flex-col overflow-hidden">
    <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100 p-6 md:p-10">
        <div class="container mx-auto">

            {{-- Header halaman --}}
            <div class="mb-6 flex justify-between items-center">
                <h1 class="text-3xl font-bold text-gray-800">Detail Warung</h1>
                <a href="{{ route('admin.warung.index') }}"
                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Kembali ke Daftar Warung
                </a>
            </div>

            {{-- Card detail warung --}}
            <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h2 class="text-3xl font-extrabold text-gray-900 mb-2">{{ $warung->nama_warung }}</h2>
                        <p class="text-gray-600 text-sm mb-1">Pemilik:
                            <span class="font-semibold">{{ $warung->user->name ?? '-' }}</span>
                        </p>
                        <p class="text-gray-600 text-sm mb-4">Area:
                            <span class="font-semibold">{{ $warung->area->area ?? '-' }}</span>
                        </p>

                        <div class="border-t border-gray-200 pt-4 mb-4">
                            <div class="flex items-center">
                                <h5 class="text-lg font-medium text-gray-700 mr-2">Modal:</h5>
                                <h4 class="text-2xl font-bold text-green-600">
                                    Rp {{ number_format($warung->modal, 0, ',', '.') }}
                                </h4>
                            </div>
                        </div>

                        <div>
                            <h5 class="text-lg font-medium text-gray-700 mb-2">Keterangan:</h5>
                            <p class="text-gray-600 italic">{{ $warung->keterangan ?? 'Tidak ada keterangan.' }}</p>
                        </div>
                    </div>

                    <div class="flex items-center justify-center">
                        <svg class="w-24 h-24 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                    </div>
                </div>
            </div>

            {{-- Tabs barang --}}
            <div x-data="{ activeTab: 'tersedia' }"> {{-- Mengubah default tab ke 'tersedia' --}}
                <div class="border-b border-gray-200">
                    <nav class="-mb-px flex space-x-8">

                        {{-- Tab yang sudah ada --}}
                        <button @click="activeTab = 'tersedia'"
                            :class="{'border-blue-500 text-blue-600': activeTab === 'tersedia'}"
                            class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                            Barang Tersedia
                        </button>
                        <button @click="activeTab = 'kosong'"
                            :class="{'border-blue-500 text-blue-600': activeTab === 'kosong'}"
                            class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                            Barang Kosong
                        </button>
                        <button @click="activeTab = 'semua'"
                            :class="{'border-blue-500 text-blue-600': activeTab === 'semua'}"
                            class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                            Semua Barang
                        </button>

                    </nav>
                </div>

                <div class="mt-6">
                    @php
                        // Catatan: Koleksi $barangWithStok sudah terurut berdasarkan persentase laba terkecil (dari Controller)
                        $semua      = $barangWithStok; // Semua barang
                        $tersedia = $barangWithStok->filter(fn($barang) => ($barang->stok_saat_ini ?? 0) > 0);
                        $kosong   = $barangWithStok->filter(fn($barang) => ($barang->stok_saat_ini ?? 0) <= 0);

                        // Definisikan daftar tab dan data yang sesuai
                        $tabs = [
                            'semua'     => ['list' => $semua, 'label' => 'yang tercatat'],
                            'tersedia'  => ['list' => $tersedia, 'label' => 'tersedia'],
                            'kosong'    => ['list' => $kosong, 'label' => 'kosong'],
                        ];
                    @endphp

                    {{-- Loop tabel, fungsi render ulang --}}
                    @foreach ($tabs as $status => $tabData)
                        <div x-show="activeTab === '{{ $status }}'" x-cloak>
                            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">#</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Barang</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Stok</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Harga Beli Awal</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Harga Modal</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Harga Jual</th>
                                                {{-- KOLOM BARU UNTUK PERSENTASE LABA --}}
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Persentase Laba & Laba</th>
                                                {{-- END KOLOM BARU --}}
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kuantitas</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kadaluarsa</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            @forelse ($tabData['list'] as $barang)
                                                <tr class="hover:bg-gray-50">
                                                    <td class="px-6 py-4 text-sm font-medium">{{ $loop->iteration }}</td>
                                                    <td class="px-6 py-4 text-sm">{{ $barang->nama_barang ?? '-' }}</td>
                                                    <td class="px-6 py-4 text-sm {{ ($status==='kosong' || ($status==='semua' && ($barang->stok_saat_ini ?? 0) <= 0)) ? 'font-bold text-red-500' : '' }}">
                                                        {{ $barang->stok_saat_ini ?? 0 }}
                                                    </td>
                                                    <td class="px-6 py-4 text-sm">Rp {{ number_format($barang->harga_sebelum_markup ?? 0, 0, ',', '.') }}</td>
                                                    <td class="px-6 py-4 text-sm font-semibold">Rp {{ number_format($barang->harga_satuan ?? 0, 0, ',', '.') }}</td>
                                                    <td class="px-6 py-4 text-sm">Rp {{ number_format($barang->harga_jual_range_awal ?? 0, 0, ',', '.') }} - Rp {{ number_format($barang->harga_jual ?? 0, 0, ',', '.') }}</td>

                                                    {{-- DATA PERSENTASE LABA + LABA ABSOLUT (MODIFIKASI DI SINI) --}}
                                                    <td class="px-6 py-4 text-sm font-semibold">
                                                        {{-- Persentase Laba --}}
                                                        <span class="{{ str_contains($barang->persentase_laba, '-') ? 'text-red-600' : 'text-blue-600' }}">
                                                            {{ $barang->persentase_laba ?? '-' }}
                                                        </span>

                                                        {{-- LOGIKA BARU UNTUK JUMLAH LABA (di bawah persentase) --}}
                                                        @if (($barang->harga_satuan ?? 0) > 0)
                                                            @php
                                                                // Laba Awal = Harga Jual Awal - Harga Modal
                                                                $labaAwal = ($barang->harga_jual_range_awal ?? 0) - $barang->harga_satuan;
                                                                // Laba Akhir = Harga Jual Akhir - Harga Modal (Harga Jual di Controller diset dari range akhir)
                                                                $labaAkhir = ($barang->harga_jual ?? 0) - $barang->harga_satuan;
                                                            @endphp

                                                            <div class="mt-1 text-xs font-normal text-gray-500 leading-tight">
                                                                <span class="{{ $labaAwal < 0 ? 'text-red-600' : 'text-green-600' }} font-bold">
                                                                    Rp {{ number_format($labaAwal, 0, ',', '.') }}
                                                                </span>
                                                                @if ($labaAkhir !== $labaAwal)
                                                                    -
                                                                    <span class="{{ $labaAkhir < 0 ? 'text-red-600' : 'text-green-600' }} font-bold">
                                                                        Rp {{ number_format($labaAkhir, 0, ',', '.') }}
                                                                    </span>
                                                                @endif
                                                            </div>
                                                        @else
                                                            <div class="mt-1 text-xs font-normal text-gray-400 italic">
                                                                Harga modal belum diatur
                                                            </div>
                                                        @endif
                                                        {{-- END LOGIKA LABA --}}
                                                    </td>
                                                    {{-- END DATA PERSENTASE LABA + LABA ABSOLUT --}}

                                                    {{-- KOLOM KUANTITAS BARU DENGAN TOMBOL EDIT/HAPUS/TAMBAH --}}
                                                    <td class="px-6 py-4 text-sm">
                                                        <ul class="list-none space-y-2 mb-2">
                                                            @forelse($barang->kuantitas as $kuantitas)
                                                                <li class="flex justify-between items-center text-xs p-1 rounded-md bg-gray-50 hover:bg-gray-100 transition">
                                                                    {{-- Informasi Kuantitas --}}
                                                                    <span class="truncate pr-2">
                                                                        {{ $kuantitas->jumlah }} unit:
                                                                        <span class="font-semibold text-gray-700">Rp {{ number_format($kuantitas->harga_jual, 0, ',', '.') }}</span>
                                                                    </span>
                                                                </li>
                                                            @empty
                                                                <li class="text-xs italic">-</li>
                                                            @endforelse
                                                        </ul>

                                                        {{-- Button Tambah Kuantitas (hanya jika stok > 0) --}}
                                                        @if($barang->stok_saat_ini > 0)
                                                            <form action="{{ route('admin.kuantitas.create', ['id_stok_warung' => $barang->id_stok_warung]) }}" method="GET" class="mt-2">
                                                                @csrf
                                                                <input type="hidden" name="id_stok_warung" value="{{ $barang->id_stok_warung }}">
                                                                <button type="submit"
                                                                    class="inline-flex items-center px-2 py-1 border border-transparent text-xs font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 disabled:opacity-50 transition-colors"
                                                                    @if(!$barang->id_stok_warung) disabled @endif>
                                                                    Atur Kuantitas
                                                                </button>
                                                            </form>
                                                        @endif
                                                    </td>
                                                    <td class="px-6 py-4 text-sm">
                                                        {{ $barang->tanggal_kadaluarsa ? \Carbon\Carbon::parse($barang->tanggal_kadaluarsa)->format('d-m-Y') : 'Tidak Ada' }}
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    {{-- COLSPAN TOTAL ADALAH 11 (dihitung ulang) --}}
                                                    <td colspan="9" class="px-6 py-4 text-center text-sm text-gray-500">
                                                        Tidak ada barang {{ $tabData['label'] }} untuk warung ini.
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            {{-- End Tabs --}}
        </div>
    </main>
</div>

@endsection
