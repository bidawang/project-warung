@extends('layouts.admin')

@section('title', 'Rencana Belanja')

@section('content')

{{-- Main Content Container --}}
<div class="flex-1 flex flex-col overflow-hidden">
    {{-- Header --}}
    <header class="flex justify-between items-center p-6 bg-white border-b-2 border-gray-200">
        <h1 class="text-2xl font-bold text-gray-800">Rencana Belanja</h1>
        <div class="flex items-center">
            <span class="mr-4 font-semibold hidden sm:inline">Admin User</span>
            <div class="w-10 h-10 bg-blue-500 rounded-full"></div>
        </div>
    </header>

    {{-- Main Content --}}
    <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100 p-6 md:p-10">
        <div class="container mx-auto">
            <h1 class="text-3xl font-bold text-gray-800 mb-6">Form Rencana Belanja</h1>

            {{-- START: Layout 2 Kolom --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                {{-- Kolom Kiri: Data Rencana Belanja (1/3 lebar) --}}
                <div class="md:col-span-1 bg-white p-6 rounded-lg shadow-md h-fit sticky top-6">
                    <h2 class="text-xl font-bold text-gray-800 mb-4">Rencana Belanja 📝</h2>

                    {{-- Tombol Opsi Tampilan --}}
                    <div class="flex justify-around mb-4 space-x-2">
                        <button type="button" data-view="warung" class="btn-view-toggle flex-1 bg-blue-600 text-white px-3 py-2 rounded-lg text-sm font-semibold transition-colors duration-200">
                            Berdasarkan Warung
                        </button>
                        <button type="button" data-view="barang" class="btn-view-toggle flex-1 bg-gray-300 text-gray-800 px-3 py-2 rounded-lg text-sm font-semibold transition-colors duration-200">
                            Berdasarkan Barang
                        </button>
                    </div>

                    {{-- Input Search JS --}}
                    <input type="text" id="searchRencana" onkeyup="filterRencanaBelanja()" placeholder="Cari Warung atau Barang..." class="w-full px-3 py-2 mb-4 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">

                    {{-- Konten Rencana Belanja --}}
                    <div id="rencanaBelanjaContainer" class="space-y-4 max-h-[70vh] overflow-y-auto pr-2">

                        {{-- Tampilan Berdasarkan Warung --}}
                        <div id="viewWarung" class="view-content space-y-4">
                            @forelse ($rencanaBelanjaByWarung as $namaWarung => $items)
                                <div class="item-block border p-3 rounded-lg bg-indigo-50" data-nama-utama="{{ $namaWarung }}">
                                    <h3 class="font-bold text-indigo-700 mb-2">{{ $namaWarung }}</h3>
                                    <ul class="list-disc list-inside text-sm text-gray-700 space-y-1">
                                        @foreach ($items as $item)
                                            <li data-item-nama="{{ $item->barang->nama_barang }}">
                                                {{ $item->barang->nama_barang }}: {{ $item->jumlah_awal}} pcs
                                                {{-- Tombol [+] DIHILANGKAN sesuai permintaan --}}
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @empty
                                <p class="text-center text-gray-500">Tidak ada rencana belanja yang tertunda.</p>
                            @endforelse
                        </div>

                        {{-- Tampilan Berdasarkan Barang (Detail Warung) --}}
                        <div id="viewBarang" class="view-content space-y-4 hidden">
                            {{-- 1. Tampilan Total Barang --}}
                            <div id="viewBarangTotal" class="border p-4 rounded-lg bg-yellow-100 mb-6">
                                <h3 class="font-bold text-lg text-yellow-800 mb-3 border-b pb-2">Total Kebutuhan Barang</h3>
                                <ul class="list-none text-sm space-y-1" id="listTotalBarang">
                                    {{-- Anda perlu menyediakan variabel $rencanaBelanjaTotalByBarang di Controller Anda --}}
                                    @isset($rencanaBelanjaTotalByBarang)
                                        @forelse ($rencanaBelanjaTotalByBarang as $namaBarang => $totalKebutuhan)
                                            <li class="flex justify-between items-center py-1 border-b border-yellow-200" data-item-nama="{{ $namaBarang }}">
                                                <span class="font-semibold">{{ $namaBarang }}</span>
                                                <span class="text-yellow-700">{{ $totalKebutuhan }} pcs</span>
                                            </li>
                                        @empty
                                            <li><p class="text-gray-500">Tidak ada total kebutuhan barang.</p></li>
                                        @endforelse
                                    @endisset
                                </ul>
                            </div>

                            {{-- 2. Tampilan Detail Per Warung (untuk keperluan search) --}}
                            @forelse ($rencanaBelanjaByBarang as $namaBarang => $items)
                                <div class="item-block border p-3 rounded-lg bg-green-50" data-nama-utama="{{ $namaBarang }}">
                                    <h3 class="font-bold text-green-700 mb-2">{{ $namaBarang }}</h3>
                                    <ul class="list-disc list-inside text-sm text-gray-700 space-y-1">
                                        @foreach ($items as $item)
                                            <li data-item-nama="{{ $item->warung->nama_warung }}">
                                                {{ $item->warung->nama_warung }}: {{ $item->jumlah_awal}} pcs
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @empty
                                <p class="text-center text-gray-500">Tidak ada rencana belanja yang tertunda.</p>
                            @endforelse
                        </div>

                    </div>
                </div>

                {{-- Kolom Kanan: Form Transaksi (2/3 lebar) --}}
                <div class="md:col-span-2 bg-white p-6 rounded-lg shadow-md">
                    {{-- ... Form Transaksi Tetap Sama ... --}}
                    <form action="{{ route('transaksibarang.store') }}" method="POST" id="formTransaksiBarang">
                        @csrf
                        {{-- Area Pembelian --}}
                        <div class="mb-6">
                            <h2 class="font-semibold mb-3 text-gray-800">Daftar Area Pembelian</h2>
                            <div id="areaContainer" class="space-y-6">
                                {{-- Area block (Template) --}}
                                <div class="area-block border rounded-lg p-4 bg-gray-50">
                                    <div class="flex justify-between items-center mb-3">
                                        <h3 class="font-semibold text-gray-700">Area Pembelian <span class="area-index-label">1</span></h3>
                                        <button type="button"
                                            class="btn-remove-area text-red-600 hover:text-red-800 font-bold text-xl leading-none">&times;</button>
                                    </div>
                                    <select name="id_area[]" class="select-area w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 mb-4" required>
                                        <option value="">-- Pilih Area --</option>
                                        @foreach($areas as $area)
                                        <option value="{{ $area->id }}">{{ $area->area }} {{$area['markup']}}%</option>
                                        @endforeach
                                    </select>

                                    {{-- Barang --}}
                                    <div>
                                        <h4 class="font-medium mb-2 text-gray-700">Barang di Area Ini</h4>
                                        <table class="w-full border-collapse border mb-3 barangTable">
                                            <thead class="bg-gray-100 text-gray-700">
                                                <tr>
                                                    <th class="border px-2 py-1 text-left">Barang</th>
                                                    <th class="border px-2 py-1 text-left">Jumlah</th>
                                                    <th class="border px-2 py-1 text-left">Total Harga (Rp)</th>
                                                    <th class="border px-2 py-1 text-left">Tgl Kadaluarsa</th>
                                                    <th class="border px-2 py-1 w-10">Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                {{-- Template Baris Barang --}}
                                                <tr>
                                                    <td class="border px-2 py-1">
                                                        <select name="id_barang[0][]" class="select-barang w-full border rounded-lg px-2 py-1" required>
                                                            <option value="">Pilih Barang</option>
                                                            @foreach($barangs as $barang)
                                                            <option value="{{ $barang->id }}" data-harga="{{ $barang->harga ?? 0 }}">
                                                                {{ $barang->nama_barang }}
                                                            </option>
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                    <td class="border px-2 py-1">
                                                        <input type="number" name="jumlah[0][]" class="input-jumlah w-full border rounded-lg px-2 py-1" min="1" required />
                                                    </td>
                                                    <td class="border px-2 py-1">
                                                        <input type="text" name="total_harga[0][]" class="total-harga w-full border rounded-lg px-2 py-1" required  />
                                                    </td>
                                                    <td class="border px-2 py-1">
                                                        <input type="date" name="tanggal_kadaluarsa[0][]" class="input-tgl-kadaluarsa w-full border rounded-lg px-2 py-1" />
                                                    </td>
                                                    <td class="border px-2 py-1 text-center">
                                                        <button type="button" class="btn-remove-row text-red-600 hover:text-red-800 font-bold text-xl leading-none">&times;</button>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                        <button type="button" class="btn-add-row bg-gray-200 hover:bg-gray-300 px-3 py-1 rounded-lg text-sm text-gray-700">+ Tambah Barang</button>
                                    </div>
                                </div>
                            </div>

                            <button type="button" id="btnAddArea" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg mt-4 font-semibold">
                                + Tambah Area Pembelian
                            </button>
                        </div>

                        {{-- Transaksi Lain-Lain --}}
                        <div class="mb-6">
                            <h2 class="font-semibold mb-3 text-gray-800">Transaksi Lain-Lain</h2>
                            <div id="lainContainer" class="space-y-3 hidden"></div> {{-- Awalnya kosong/hidden --}}

                            <button type="button" id="btnAddLain"
                                class="bg-purple-500 hover:bg-purple-600 text-white px-4 py-2 rounded-lg mt-4 font-semibold">
                                + Tambah Transaksi Lain-Lain
                            </button>
                        </div>

                        {{-- Keterangan --}}
                        <div class="mb-6">
                            <label for="keterangan" class="block text-gray-700 font-semibold mb-2">Keterangan (Opsional)</label>
                            <textarea name="keterangan" id="keterangan" rows="3"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">{{ old('keterangan') }}</textarea>
                        </div>

                        {{-- Tombol Aksi --}}
                        <div class="flex justify-end space-x-4">
                            <a href="{{ route('transaksibarang.index') }}"
                                class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-6 rounded-lg transition-colors duration-200">
                                Batal
                            </a>
                            <button type="submit"
                                class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-lg transition-colors duration-200">
                                Simpan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            {{-- END: Layout 2 Kolom --}}

        </div>
    </main>
</div>
@include('admin.transaksibarang.script-create')
@endsection
