@extends('layouts.admin')

@section('content')

<div class="p-6">

    <h1 class="text-2xl font-bold mb-4">Asal Barang</h1>

    {{-- NAV TAB AREA --}}
    <div class="flex space-x-4 mb-6 border-b pb-2">
        @php
            // Ambil semua query parameters kecuali area_id
            $currentFilters = request()->except(['area_id', 'page']);
        @endphp

        {{-- Link Semua Barang --}}
        <a href="{{ route('admin.asalbarang.index', $currentFilters) }}"
           class="px-4 py-2 {{ $areaId ? '' : 'border-b-2 border-blue-600 font-bold' }}">
            Semua Barang
        </a>

        {{-- Link per Area --}}
        @foreach ($areas as $a)
            <a href="{{ route('admin.asalbarang.index', array_merge($currentFilters, ['area_id' => $a->id])) }}"
               class="px-4 py-2 {{ ($areaId == $a->id) ? 'border-b-2 border-blue-600 font-bold' : '' }}">
                {{ $a->area }}
            </a>
        @endforeach
<a href="{{ route('admin.asalbarang.create') }}" class="ml-auto bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-full">Kelola Barang</a>    </div>

    {{-- FILTER FORM --}}
    <form action="{{ route('admin.asalbarang.index') }}" method="GET" class="mb-6">
        {{-- Pertahankan areaId saat submit filter lain --}}
        <input type="hidden" name="area_id" value="{{ $areaId }}">

        <div class="p-4 bg-gray-100 rounded grid grid-cols-1 md:grid-cols-4 gap-4 items-end">

            {{-- Filter Kategori --}}
            <div>
                <label for="kategori_id" class="font-semibold block mb-1">Kategori</label>
                <select name="kategori_id" id="kategori_id" class="w-full border p-2 rounded">
                    <option value="">Semua Kategori</option>
                    @foreach($kategoris as $k)
                        <option value="{{ $k->id }}"
                            {{ $kategoriId == $k->id ? 'selected' : '' }}>
                            {{ $k->kategori }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Filter Subkategori --}}
            <div>
                <label for="subkategori_id" class="font-semibold block mb-1">Subkategori</label>
                <select name="subkategori_id" id="subkategori_id" class="w-full border p-2 rounded">
                    <option value="">Semua Subkategori</option>
                    {{-- Kita akan filter ini dengan JS agar hanya menampilkan sub dari kategori terpilih --}}
                    @foreach($subkategoris as $s)
                        {{-- Catatan: Perhatikan field 'sub_kategori' sesuai model Anda --}}
                        <option value="{{ $s->id }}"
                            {{ $subkategoriId == $s->id ? 'selected' : '' }}
                            data-kategori-id="{{ $s->id_kategori }}">
                            {{ $s->sub_kategori }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Search --}}
            <div>
                <label for="search" class="font-semibold block mb-1">Cari Barang</label>
                <input type="text" name="search" id="search"
                    value="{{ $search }}"
                    placeholder="Nama Barang..."
                    class="w-full border p-2 rounded">
            </div>

            {{-- Submit/Reset Button --}}
            <div class="flex space-x-2">
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">
                    Filter
                </button>
                <a href="{{ route('admin.asalbarang.index', ['area_id' => $areaId]) }}" class="bg-gray-300 text-gray-800 px-4 py-2 rounded hover:bg-gray-400 transition">
                    Reset
                </a>
            </div>
        </div>
    </form>

    {{-- TABEL --}}
    <table class="min-w-full bg-white shadow rounded-lg overflow-hidden">
        <thead>
            <tr class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                <th class="p-3 text-left">Nama Barang</th>
                <th class="p-3 text-left">Kategori/Subkategori</th>
                <th class="p-3 text-left">Area Pembelian</th>
            </tr>
        </thead>
        <tbody class="text-gray-600 text-sm font-light">
            @forelse ($barangs as $b)
                <tr class="border-b border-gray-200 hover:bg-gray-100">
                    <td class="p-3 whitespace-nowrap font-medium">{{ $b->nama_barang }}</td>
                    <td class="p-3">
                         {{ $b->subKategori->kategori->kategori ?? 'N/A' }} /
                         {{ $b->subKategori->sub_kategori ?? 'N/A' }}
                    </td>
                    <td class="p-3">
                        @forelse ($b->areaPembelian as $ar)
                            <span class="px-2 py-1 bg-blue-100 text-blue-700 rounded text-xs font-semibold inline-block mr-1 mb-1">
                                {{ $ar->area }}
                            </span>
                        @empty
                            <span class="text-red-500 text-xs">Belum ada Area</span>
                        @endforelse
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" class="p-6 text-center text-gray-500">
                        Tidak ada barang yang ditemukan.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const kategoriSelect = document.getElementById('kategori_id');
        const subkategoriSelect = document.getElementById('subkategori_id');

        // Ambil semua opsi subkategori, termasuk yang sudah selected dari PHP
        const allSubkategoriOptions = Array.from(subkategoriSelect.options);

        // Fungsi untuk mengontrol Subkategori
        function filterSubkategoris() {
            const selectedKategoriId = kategoriSelect.value;
            const currentSubkategoriId = subkategoriSelect.value;

            // Tampilkan opsi "Semua Subkategori"
            subkategoriSelect.innerHTML = allSubkategoriOptions[0].outerHTML;

            // Jika Kategori dipilih
            if (selectedKategoriId) {
                let foundSelected = false;

                allSubkategoriOptions.forEach((option, index) => {
                    // Lewati opsi "Semua Subkategori" (index 0)
                    if (index === 0) return;

                    const kategoriId = option.getAttribute('data-kategori-id');

                    if (kategoriId === selectedKategoriId) {
                        subkategoriSelect.appendChild(option);

                        // Cek apakah opsi yang sedang aktif ada di daftar subkategori baru
                        if (option.value === currentSubkategoriId) {
                            foundSelected = true;
                        }
                    }
                });

                // Jika subkategori yang terpilih sebelumnya tidak cocok dengan kategori baru, reset subkategori
                if (!foundSelected && currentSubkategoriId !== '') {
                    subkategoriSelect.value = '';
                }

            } else {
                // Jika "Semua Kategori" dipilih, tampilkan semua subkategori
                allSubkategoriOptions.forEach((option, index) => {
                    if (index === 0) return;
                    subkategoriSelect.appendChild(option);
                });
            }
        }

        // Panggil saat kategori berubah
        kategoriSelect.addEventListener('change', filterSubkategoris);

        // Panggil saat halaman dimuat untuk memastikan dropdown Subkategori benar
        filterSubkategoris();
    });
</script>

@endsection
