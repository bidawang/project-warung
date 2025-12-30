@extends('layouts.admin')

@section('title', 'Detail Piutang: ' . $hutang->user->name)

@section('content')

@php
    $isLunas = $hutang->status === 'lunas';
    $isJatuhTempo = $hutang->tenggat && $hutang->tenggat->isPast() && !$isLunas;
    $sisaHutang = $hutang->jumlah_sisa_hutang;
    $totalDibayar = $hutang->jumlah_hutang_awal - $sisaHutang;
@endphp

{{-- Inisialisasi Alpine.js x-data --}}
<div class="flex-1 flex flex-col overflow-hidden bg-gray-100" 
     x-data="{ 
        isLoading: false,
        printReport() {
            this.isLoading = true;
            alert('Menyiapkan Laporan Detail...');
            // Simulasi proses cetak
            setTimeout(() => { this.isLoading = false; window.print(); }, 1000);
        }
     }">

    {{-- Header --}}
    <header class="flex justify-between items-center p-6 bg-white border-b sticky top-0 z-10">
        <h1 class="text-2xl font-bold text-gray-800 flex items-center">
            <i class="fas fa-file-invoice mr-2 text-red-600"></i> Detail Piutang
            <span class="text-xl font-medium ml-3 text-red-500">#{{ $hutang->id }}</span>
        </h1>
        <a href="{{ route('admin.hutang.index') }}" 
           class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg shadow-sm hover:bg-gray-300 transition duration-150 font-semibold">
            <i class="fas fa-arrow-left mr-1"></i> Kembali
        </a>
    </header>

    <main class="flex-1 overflow-y-auto p-6 space-y-6">

        {{-- Status Notifications --}}
        <template x-if="{{ $isLunas ? 'true' : 'false' }}">
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-lg shadow-md">
                <p class="font-bold uppercase italic"><i class="fas fa-check-circle mr-2"></i>Status: Lunas</p>
                <p class="text-sm">Transaksi piutang ini telah dilunasi sepenuhnya.</p>
            </div>
        </template>

        <template x-if="{{ $isJatuhTempo ? 'true' : 'false' }}">
            <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 rounded-lg shadow-md">
                <p class="font-bold"><i class="fas fa-exclamation-triangle mr-2"></i>Status: Jatuh Tempo!</p>
                <p class="text-sm">Piutang terlambat bayar. Sisa: Rp. {{ number_format($sisaHutang, 0, ',', '.') }}</p>
            </div>
        </template>

        <template x-if="{{ (!$isLunas && !$isJatuhTempo) ? 'true' : 'false' }}">
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-lg shadow-md">
                <p class="font-bold">STATUS: BELUM LUNAS</p>
                <p class="text-sm">Sisa piutang: Rp. {{ number_format($sisaHutang, 0, ',', '.') }}</p>
            </div>
        </template>

        {{-- RINGKASAN & MONETER --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 text-gray-800">

            {{-- Info Data Piutang --}}
            <div class="lg:col-span-2 bg-white shadow-xl rounded-xl p-6 border-t-4 border-red-500">
                <h2 class="text-xl font-bold pb-2 border-b-2 border-gray-100 mb-4">
                    <i class="fas fa-info-circle mr-2 text-red-600"></i> Informasi Transaksi
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-4 text-sm">
                        <div>
                            <span class="text-gray-500 block">Warung Penjual</span>
                            <span class="font-bold text-indigo-700 text-base italic">{{ $hutang->warung->nama_warung ?? 'N/A' }}</span>
                        </div>
                        <div>
                            <span class="text-gray-500 block">Pelanggan</span>
                            <span class="font-bold text-base">{{ $hutang->user->name ?? 'User Dihapus' }}</span>
                        </div>
                    </div>
                    <div class="space-y-4 text-sm">
                        <div>
                            <span class="text-gray-500 block">Tanggal Transaksi</span>
                            <span class="font-semibold">{{ $hutang->created_at->translatedFormat('d M Y H:i') }}</span>
                        </div>
                        <div>
                            <span class="text-gray-500 block">Jatuh Tempo</span>
                            <span class="font-bold {{ $isJatuhTempo ? 'text-red-600' : 'text-gray-800' }}">
                                {{ $hutang->tenggat ? $hutang->tenggat->translatedFormat('d F Y') : 'Tanpa Tenggat' }}
                            </span>
                        </div>
                    </div>
                </div>
                <div class="mt-6 pt-4 border-t border-gray-100">
                    <span class="text-gray-500 text-sm block mb-2">Keterangan:</span>
                    <div class="p-4 bg-gray-50 rounded-lg border text-sm italic">
                        {{ $hutang->keterangan ?? 'Tidak ada keterangan tambahan.' }}
                    </div>
                </div>
            </div>

            {{-- Ringkasan Moneter --}}
            <div class="lg:col-span-1 space-y-4">
                <div class="bg-white shadow-xl rounded-xl p-6 border-t-4 border-indigo-500">
                    <h3 class="text-lg font-bold mb-4 flex items-center">
                        <i class="fas fa-calculator mr-2 text-indigo-600"></i> Ringkasan Nilai
                    </h3>
                    <div class="space-y-4">
                        <div class="flex justify-between border-b pb-2">
                            <span class="text-gray-600">Awal:</span>
                            <span class="font-bold">Rp. {{ number_format($hutang->jumlah_hutang_awal, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between border-b pb-2">
                            <span class="text-gray-600">Dibayar:</span>
                            <span class="font-bold text-green-600">Rp. {{ number_format($totalDibayar, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between items-center pt-2">
                            <span class="font-bold text-red-600 text-lg uppercase italic">Sisa Piutang:</span>
                            <span class="text-2xl font-black {{ $sisaHutang > 0 ? 'text-red-700' : 'text-green-700' }}">
                                Rp. {{ number_format($sisaHutang, 0, ',', '.') }}
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Opsi dengan Alpine Loading State --}}
                <div class="bg-white shadow-xl rounded-xl p-5 border-t-4 border-gray-500">
                    <button @click="printReport()" 
                            :disabled="isLoading"
                            class="w-full flex items-center justify-center bg-gray-700 text-white px-4 py-3 rounded-lg font-bold hover:bg-gray-800 transition disabled:opacity-50">
                        <template x-if="!isLoading">
                            <span><i class="fas fa-print mr-2"></i> Cetak Laporan</span>
                        </template>
                        <template x-if="isLoading">
                            <span><i class="fas fa-spinner fa-spin mr-2"></i> Memproses...</span>
                        </template>
                    </button>
                </div>
            </div>
        </div>

        {{-- DAFTAR BARANG --}}
        <div class="bg-white shadow-xl rounded-xl p-6 border-t-4 border-blue-500" x-data="{ showTable: true }">
            <div class="flex justify-between items-center mb-4 border-b-2 border-blue-100 pb-2">
                <h2 class="text-xl font-bold text-gray-800 flex items-center">
                    <i class="fas fa-boxes mr-2 text-blue-600"></i> Detail Barang
                </h2>
                <button @click="showTable = !showTable" class="text-blue-600 text-sm font-semibold focus:outline-none">
                    <span x-text="showTable ? 'Sembunyikan' : 'Tampilkan'"></span>
                </button>
            </div>

            <div x-show="showTable" x-transition.opacity class="overflow-x-auto rounded-lg border">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-100 text-gray-700 uppercase text-xs">
                        <tr>
                            <th class="py-3 px-4 text-left">Nama Barang</th>
                            <th class="py-3 px-4 text-right">Harga Satuan (Rp)</th>
                            <th class="py-3 px-4 text-center">Jumlah</th>
                            <th class="py-3 px-4 text-right font-bold">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $totalBarang = 0; @endphp
                        @forelse ($hutang->barangHutang as $item)
                            @php
                                $barang = $item->barangKeluar->stokWarung->barang ?? null;
                                $hargaJual = $item->barangKeluar->stokWarung->hargaJual->first() ?? null;
                                $harga = $hargaJual ? $hargaJual->harga_jual_range_akhir : 0;
                                $jumlah = $item->barangKeluar->jumlah ?? 0;
                                $subtotal = $harga * $jumlah;
                                $totalBarang += $subtotal;
                            @endphp
                            <tr class="border-b hover:bg-blue-50 transition duration-150">
                                <td class="py-3 px-4 font-medium">{{ $barang->nama_barang ?? 'N/A' }}</td>
                                <td class="py-3 px-4 text-right">{{ number_format($harga, 0, ',', '.') }}</td>
                                <td class="py-3 px-4 text-center">{{ $jumlah }}</td>
                                <td class="py-3 px-4 text-right font-bold">{{ number_format($subtotal, 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-center py-8 text-gray-400">Tidak ada data barang.</td></tr>
                        @endforelse
                    </tbody>
                    <tfoot class="bg-blue-50 font-bold border-t-2 border-blue-200">
                        <tr>
                            <td colspan="3" class="py-3 px-4 text-right">Total Nilai Barang:</td>
                            <td class="py-3 px-4 text-right text-blue-700 text-lg italic">Rp. {{ number_format($totalBarang, 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        {{-- RIWAYAT PEMBAYARAN --}}
        <div class="bg-white shadow-xl rounded-xl p-6 border-t-4 border-green-500">
            <h2 class="text-xl font-bold text-gray-800 mb-4 border-b-2 border-green-100 pb-2">
                <i class="fas fa-history mr-2 text-green-600"></i> Riwayat Cicilan / Pembayaran
            </h2>

            @if ($logPembayaran->isEmpty())
                <div class="p-8 text-center bg-gray-50 rounded-lg">
                    <p class="text-gray-500 italic">Belum ada cicilan yang masuk.</p>
                </div>
            @else
                <div class="overflow-x-auto border rounded-lg">
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-100 text-xs uppercase font-bold text-gray-600">
                            <tr>
                                <th class="py-3 px-4 text-left">Tanggal</th>
                                <th class="py-3 px-4 text-right">Jumlah</th>
                                <th class="py-3 px-4 text-left">Metode</th>
                                <th class="py-3 px-4 text-left">Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($logPembayaran as $log)
                                <tr class="border-b hover:bg-green-50">
                                    <td class="py-3 px-4 font-medium">{{ $log->created_at->translatedFormat('d M Y H:i') }}</td>
                                    <td class="py-3 px-4 text-right font-bold text-green-700">Rp. {{ number_format($log->jumlah_pembayaran, 0, ',', '.') }}</td>
                                    <td class="py-3 px-4">
                                        <span class="px-2 py-0.5 bg-green-100 text-green-800 rounded-md text-xs font-bold">{{ $log->metode_pembayaran ?? 'KAS' }}</span>
                                    </td>
                                    <td class="py-3 px-4 text-xs italic text-gray-500">{{ $log->keterangan ?? '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-green-50 border-t-2 border-green-200 font-bold">
                            <tr>
                                <td class="py-3 px-4 text-right">Total Masuk:</td>
                                <td class="py-3 px-4 text-right text-green-700 text-lg italic font-black">
                                    Rp. {{ number_format($logPembayaran->sum('jumlah_pembayaran'), 0, ',', '.') }}
                                </td>
                                <td colspan="2"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            @endif
        </div>

    </main>
</div>

@endsection