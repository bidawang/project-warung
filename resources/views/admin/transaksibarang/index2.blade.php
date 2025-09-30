@extends('layouts.admin')

@section('title', 'Daftar Transaksi Barang')

@section('content')
<div class="flex-1 flex flex-col overflow-hidden">
    {{-- Header (Tidak Berubah) --}}
    <header class="flex justify-between items-center p-6 bg-white border-b-2 border-gray-200 shadow-sm">
        <button id="openSidebarBtn" class="text-gray-500 hover:text-gray-900 lg:hidden">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M4 6h16M4 12h16M4 18h16"></path>
            </svg>
        </button>
        <h1 class="text-2xl font-bold text-gray-800">Manajemen Transaksi Barang</h1>
        <div class="flex items-center">
            <span class="mr-4 font-semibold hidden sm:inline">Admin User</span>
            <div class="w-10 h-10 bg-blue-500 rounded-full"></div>
        </div>
    </header>

    {{-- Main Content --}}
    <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-50 p-6 md:p-10">
        <div class="container mx-auto grid grid-cols-1 md:grid-cols-3 gap-8">

            {{-- Kolom Kiri: Daftar Stok Sumber Pengiriman (2/3) --}}
            <div class="md:col-span-2">

                {{-- Tombol Aksi & Header --}}
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
                    <h1 class="text-3xl font-bold text-gray-800 mb-4 md:mb-0">Daftar Stok Sumber Pengiriman</h1>
                    <div class="flex space-x-4">
                        @if($status === 'pending')
                        {{-- Tombol untuk Kolom Kiri (Pengiriman Massal Standar) --}}
                        <button type="submit" form="formKirim" id="btnSubmitMassal" disabled
                            class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-lg shadow transition-colors duration-200 disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center text-sm">
                            <svg class="w-5 h-5 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h11l-3-3m0 6l3-3m-3 3v7m9-14v2a2 2 0 01-2 2h-6a2 2 0 01-2-2v-2a2 2 0 012-2h6a2 2 0 012 2z" />
                            </svg>
                            Kirim Stok Terpilih
                        </button>
                        {{-- Tombol untuk Kolom Kanan (Pengiriman Rencana Belanja) --}}
                        <button type="submit" form="formKirimRencana" id="btnSubmitRencana" disabled
                            class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg shadow transition-colors duration-200 disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center text-sm">
                            <svg class="w-5 h-5 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                            Kirim Rencana Terpilih
                        </button>
                        @endif
                        <a href="{{ route('transaksibarang.create') }}"
                            class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg shadow transition-colors duration-200 flex items-center justify-center text-sm">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            Baru
                        </a>
                    </div>
                </div>

                {{-- Nav Tabs (Tidak Berubah) --}}
                <nav class="mb-6 border-b border-gray-300">
                    @php
                    $tabs = [
                        'pending' => 'Belum Dikirim',
                        'kirim' => 'Dikirim',
                        'terima' => 'Sudah Diterima',
                        'tolak' => 'Ditolak'
                    ];
                    @endphp
                    <ul class="flex space-x-4">
                        @foreach($tabs as $key => $label)
                        <li>
                            <a href="{{ route('transaksibarang.index',['status'=>$key]) }}"
                                class="inline-block px-4 py-2 rounded-t-lg font-semibold transition-colors
                                {{ $status === $key ? 'bg-white border border-b-0 border-gray-300 text-blue-600' : 'text-gray-600 hover:text-blue-600' }}">
                                {{ $label }}
                            </a>
                        </li>
                        @endforeach
                    </ul>
                </nav>

                {{-- Alert (Tidak Berubah) --}}
                @if(session('success'))
                <div class="mb-4 p-4 bg-green-100 border border-green-300 text-green-700 rounded-lg shadow-sm">
                    {{ session('success') }}
                </div>
                @endif

                {{-- Tabel Transaksi (Stok Sumber) --}}
                <div class="bg-white shadow-xl rounded-lg overflow-hidden overflow-x-auto">
                    {{-- Form ini untuk pengiriman massal dari Stok Sumber (Kolom Kiri) --}}
                    <form id="formKirim" method="POST" action="{{ route('admin.transaksibarang.kirim.mass.proses') }}">
                        @csrf
                        <table class="min-w-full leading-normal">
                            <thead class="bg-gray-100">
                                <tr>
                                    @if($status === 'pending')
                                    <th class="px-4 py-3 border-b-2 border-gray-200 w-[4%] text-center">
                                        <input type="checkbox" id="checkAll" class="cursor-pointer"/>
                                    </th>
                                    @endif
                                    <th class="px-4 py-3 border-b-2 text-left text-xs font-semibold text-gray-600 uppercase w-1/5">Barang</th>
                                    <th class="px-4 py-3 border-b-2 text-center text-xs font-semibold text-gray-600 uppercase w-[10%]">Sisa Jml.</th>
                                    <th class="px-4 py-3 border-b-2 text-left text-xs font-semibold text-gray-600 uppercase w-2/5">Tujuan & Jumlah Kirim</th>
                                    <th class="px-4 py-3 border-b-2 text-left text-xs font-semibold text-gray-600 uppercase w-[10%]">Harga Satuan</th>
                                    <th class="px-4 py-3 border-b-2 text-center text-xs font-semibold text-gray-600 uppercase w-[10%]">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($transaksibarangs as $trx)
                                <tr class="hover:bg-gray-50 border-t border-gray-200" id="trx-{{ $trx->id }}">
                                    @if($status === 'pending')
                                    <td class="px-4 py-4 border-b text-sm text-center">
                                        <input type="checkbox" class="chk-trx cursor-pointer" data-id="{{ $trx->id }}"
                                            name="transaksi_ids[]" value="{{ $trx->id }}" data-valid="false">
                                    </td>
                                    @endif

                                    <td class="px-4 py-4 border-b text-sm font-semibold text-gray-700">
                                        {{ $trx->barang->nama_barang ?? '-' }}
                                    </td>

                                    <td class="px-4 py-4 border-b text-sm text-center">
                                        {{-- Visual sisa stok diambil dari state global JavaScript --}}
                                        <span class="font-bold text-lg text-blue-600" id="sisa-{{ $trx->id }}" data-max="{{ $trx->jumlah }}">{{ $trx->jumlah }}</span>
                                    </td>

                                    <td class="px-4 py-4 border-b text-sm">
                                        {{-- Container untuk input pengiriman Warung & Qty (Awalnya kosong) --}}
                                        <div id="deliveries-{{ $trx->id }}" data-id="{{ $trx->id }}" class="space-y-2"></div>
                                        <input type="hidden" name="transaksi[{{ $trx->id }}][barang_id]" value="{{ $trx->barang_id }}">
                                    </td>

                                    <td class="px-4 py-4 border-b text-sm text-gray-600">{{ number_format($trx->harga, 0, ',', '.') }}</td>

                                    <td class="px-4 py-4 border-b text-sm text-center">
                                        @if($status === 'pending')
                                        <button type="button" class="btn-add text-xs bg-indigo-500 hover:bg-indigo-600 text-white font-semibold py-1 px-3 rounded-full transition duration-150 disabled:opacity-50 disabled:cursor-not-allowed"
                                                data-id="{{ $trx->id }}" disabled>+ Warung Lain</button>
                                        @else
                                        <span class="text-gray-500">{{ ucfirst($trx->status) }}</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="{{ $status === 'pending' ? 6 : 5 }}" class="px-5 py-5 border-b text-center text-gray-500">
                                        Tidak ada transaksi untuk status **{{ ucfirst($status) }}**.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </form>
                </div>

                <div class="mt-6">
                    {{ $transaksibarangs->links() }}
                </div>
            </div>

            {{-- Kolom Kanan: Rencana Belanja (1/3) --}}
            <div class="md:col-span-1 bg-white p-6 rounded-xl shadow-xl h-fit sticky top-6 border border-gray-100">
                {{-- <form id="formKirimRencana" method="POST" action="{{ route('admin.transaksibarang.kirim.rencana.proses') }}"> --}}
                <form id="formKirimRencana" method="POST" action="#">
                    @csrf
                    <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                        <svg class="w-6 h-6 mr-2 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2m-9 0a2 2 0 002 2h2m-2 0h-2m9 0h2m-2 0a2 2 0 00-2-2h-2" />
                        </svg>
                        Rencana Belanja (Per Warung)
                    </h2>

                    {{-- Search --}}
                    <input type="text" id="searchRencana" class="w-full mb-4 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-blue-500 focus:border-blue-500"
                            placeholder="Cari Warung...">

                    {{-- Container Rencana Belanja per Warung --}}
                    <div id="rencanaContainer" class="space-y-4 max-h-[70vh] overflow-y-auto pr-2">
                        @forelse($rencanaBelanjaByWarung as $warungId => $items)
                        <div class="p-4 rounded-lg bg-indigo-50 border border-indigo-200 item-block rencana-warung-block"
                            data-warung-id="{{ $warungId }}" data-nama-warung="{{ $items[0]->warung->nama_warung }}">
                            <h3 class="font-bold text-indigo-700 text-base mb-2 border-b border-indigo-300 pb-1 flex justify-between items-center">
                                <span>{{ $items[0]->warung->nama_warung }}</span>
                                <input type="checkbox" class="chk-rencana-warung cursor-pointer" data-warung-id="{{ $warungId }}" data-valid="false"/>
                            </h3>
                            <ul class="list-none text-sm text-gray-700 space-y-2">
                                @foreach($items as $i)
                                @php
                                    $rencanaId = $i->id;
                                    $barangId = $i->barang_id;
                                    $namaBarang = $i->barang->nama_barang;
                                    $jumlahKebutuhan = $i->jumlah_awal - $i->jumlah_dibeli;
                                @endphp
                                <li class="rencana-item flex flex-col space-y-1 p-2 border-l-4 border-indigo-400 bg-white shadow-sm"
                                    data-rencana-id="{{ $rencanaId }}" data-barang-id="{{ $barangId }}" data-kebutuhan="{{ $jumlahKebutuhan }}">
                                    <span class="font-semibold">{{ $namaBarang }}</span>
                                    <div class="flex items-center space-x-2">
                                        <span class="text-xs text-gray-500 w-16">Kebutuhan:</span>
                                        <span class="font-bold text-sm text-red-600 w-10">{{ $jumlahKebutuhan }}</span>
                                        <input type="hidden" name="rencana[{{ $warungId }}][{{ $rencanaId }}][rencana_id]" value="{{ $rencanaId }}">
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <span class="text-xs text-gray-500 w-16">Jml. Kirim:</span>
                                        <input type="number" name="rencana[{{ $warungId }}][{{ $rencanaId }}][jumlah_kirim]" value="{{ $jumlahKebutuhan }}"
                                            min="0" max="{{ $jumlahKebutuhan }}" disabled required
                                            class="rencana-qty-input border border-gray-300 rounded px-2 py-1 text-xs w-12 text-center focus:ring-blue-500 focus:border-blue-500 disabled:bg-gray-100 transition duration-150"/>
                                        <span class="text-xs text-gray-500">pcs</span>
                                        <input type="hidden" name="rencana[{{ $warungId }}][{{ $rencanaId }}][barang_id]" value="{{ $barangId }}">
                                        {{-- Dropdown untuk memilih sumber pengiriman (stok) --}}
                                        <select
    name="rencana[{{ $warungId }}][{{ $rencanaId }}][transaksi_id]"
    disabled required
    class="rencana-trx-select border border-gray-300 rounded px-2 py-1 text-xs flex-1 focus:ring-blue-500 focus:border-blue-500 bg-white disabled:bg-gray-100 transition duration-150"
    data-barang-id="{{ $i->id_barang }}"> {{-- Bukan $barangId dari relasi --}}
    <option value="" disabled selected>Pilih Sumber Stok...</option>
</select>

                                    </div>
                                </li>
                                @endforeach
                            </ul>
                        </div>
                        @empty
                        <p class="text-center text-gray-500 py-4">Tidak ada rencana belanja yang tertunda.</p>
                        @endforelse
                    </div>
                </form>
            </div>

        </div>
    </main>
</div>

@include('admin.transaksibarang.script')

@endsection
