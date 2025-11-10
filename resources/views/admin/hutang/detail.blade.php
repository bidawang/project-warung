@extends('layouts.admin')

@section('title', 'Detail Piutang: ' . $hutang->user->name)

@section('content')

@php
    // Variabel bantu
    $isLunas = $hutang->status === 'lunas';
    $isJatuhTempo = $hutang->tenggat && $hutang->tenggat->isPast() && !$isLunas;
    $sisaHutang = $hutang->jumlah_sisa_hutang;
    $totalDibayar = $hutang->jumlah_hutang_awal - $sisaHutang;
@endphp

<div class="flex-1 flex flex-col overflow-hidden bg-gray-100">

    {{-- Header --}}
    <header class="flex justify-between items-center p-6 bg-white border-b sticky top-0 z-10">
        <h1 class="text-2xl font-bold text-gray-800 flex items-center">
            <i class="fas fa-file-invoice mr-2 text-red-600"></i> Detail Piutang
            <span class="text-xl font-medium ml-3 text-red-500">#{{ $hutang->id }}</span>
        </h1>
        <a href="{{ route('admin.hutang.index') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg shadow-sm hover:bg-gray-300 transition duration-150 font-semibold">
            <i class="fas fa-arrow-left mr-1"></i> Kembali ke Daftar Piutang
        </a>
    </header>

    <main class="flex-1 overflow-y-auto p-6 space-y-6">

        {{-- Notifikasi Status Global --}}
        @if ($isLunas)
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-lg shadow-md" role="alert">
                <p class="font-bold">STATUS PEMANTAUAN: LUNAS! 🎉</p>
                <p>Transaksi piutang ini telah dilunasi sepenuhnya. Data ini hanya untuk keperluan audit.</p>
            </div>
        @elseif ($isJatuhTempo)
            <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 rounded-lg shadow-md" role="alert">
                <p class="font-bold">STATUS PEMANTAUAN: JATUH TEMPO! ⚠️</p>
                <p>Piutang ini terlambat bayar. Sisa: **Rp. {{ number_format($sisaHutang, 0, ',', '.') }}**. Tenggat: {{ $hutang->tenggat->translatedFormat('d F Y') }}.</p>
            </div>
        @else
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-lg shadow-md" role="alert">
                <p class="font-bold">STATUS PEMANTAUAN: BELUM LUNAS</p>
                <p>Sisa piutang sebesar **Rp. {{ number_format($sisaHutang, 0, ',', '.') }}**. Tenggat: {{ $hutang->tenggat ? $hutang->tenggat->translatedFormat('d F Y') : 'Tanpa Tenggat' }}.</p>
            </div>
        @endif

        {{-- ========================================= --}}
        {{-- RINGKASAN PIUTANG DAN INFORMASI DASAR --}}
        {{-- ========================================= --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- Kolom Kiri: Informasi Piutang & Status --}}
            <div class="lg:col-span-2 bg-white shadow-xl rounded-xl p-6 space-y-6 border-t-4 border-red-500">
                <h2 class="text-xl font-bold text-gray-800 pb-2 border-b-2 border-gray-100">
                    <i class="fas fa-info-circle mr-2 text-red-600"></i> Informasi Data Piutang
                </h2>
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div class="space-y-1">
                        <p class="text-gray-500 font-medium">Warung Penjual:</p>
                        <p class="font-semibold text-indigo-700">{{ $hutang->warung->nama_warung ?? 'N/A' }}</p>
                    </div>
                    <div class="space-y-1">
                        <p class="text-gray-500 font-medium">Pelanggan (Penerima Piutang):</p>
                        <p class="font-semibold text-gray-800">{{ $hutang->user->name ?? 'User Dihapus' }}</p>
                    </div>
                    <div class="space-y-1">
                        <p class="text-gray-500 font-medium">Tanggal Transaksi:</p>
                        <p class="font-semibold text-gray-800">{{ $hutang->created_at->translatedFormat('d M Y H:i') }}</p>
                    </div>
                    <div class="space-y-1">
                        <p class="text-gray-500 font-medium">Tanggal Jatuh Tempo:</p>
                        <p class="font-semibold {{ $isJatuhTempo ? 'text-red-500' : 'text-gray-800' }}">
                            {{ $hutang->tenggat ? $hutang->tenggat->translatedFormat('d F Y') : 'Tanpa Tenggat' }}
                        </p>
                    </div>
                </div>

                {{-- Keterangan --}}
                <div class="mt-4 pt-4 border-t border-gray-100">
                    <p class="text-gray-500 font-medium mb-1">Keterangan Transaksi:</p>
                    <div class="p-3 bg-gray-50 rounded-lg border text-sm text-gray-700 italic">
                        {{ $hutang->keterangan ?? 'Tidak ada keterangan tambahan.' }}
                    </div>
                </div>
            </div>

            {{-- Kolom Kanan: Ringkasan Moneter --}}
            <div class="lg:col-span-1 space-y-4">
                <div class="bg-white shadow-xl rounded-xl p-5 border-t-4 border-indigo-500">
                    <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-calculator mr-2 text-indigo-600"></i> Ringkasan Moneter
                    </h3>
                    <div class="space-y-3">
                        <div class="flex justify-between border-b pb-2">
                            <span class="text-base font-medium text-gray-600">Total Piutang Awal:</span>
                            <span class="text-lg font-extrabold text-gray-800">
                                Rp. {{ number_format($hutang->jumlah_hutang_awal, 0, ',', '.') }}
                            </span>
                        </div>
                        <div class="flex justify-between border-b pb-2">
                            <span class="text-base font-medium text-gray-600">Total Sudah Dibayar:</span>
                            <span class="text-lg font-extrabold text-green-700">
                                Rp. {{ number_format($totalDibayar, 0, ',', '.') }}
                            </span>
                        </div>
                        <div class="flex justify-between items-center pt-2">
                            <span class="text-xl font-bold text-red-600">SISA PIUTANG:</span>
                            <span class="text-2xl font-extrabold {{ $sisaHutang > 0 ? 'text-red-700' : 'text-green-700' }}">
                                Rp. {{ number_format($sisaHutang, 0, ',', '.') }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="bg-white shadow-xl rounded-xl p-5 border-t-4 border-gray-500">
                    <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-download mr-2 text-gray-600"></i> Opsi Pemantauan
                    </h3>
                    {{-- Link untuk mencetak/mengunduh (ganti dengan route sebenarnya) --}}
                    <button type="button"
                        class="w-full flex items-center justify-center bg-gray-500 text-white px-4 py-3 rounded-lg shadow-md hover:bg-gray-600 transition duration-150 font-bold"
                        onclick="alert('Fungsi cetak/unduh sedang dikembangkan.')">
                        <i class="fas fa-print mr-2"></i> Cetak Laporan Detail
                    </button>
                </div>
            </div>
        </div>

        ---

        {{-- ========================================= --}}
        {{-- DETAIL BARANG PIUTANG --}}
        {{-- ========================================= --}}
        <div class="bg-white shadow-xl rounded-xl p-6 border-t-4 border-blue-500">
            <h2 class="text-xl font-bold text-gray-800 mb-4 pb-2 border-b-2 border-blue-100 flex items-center">
                <i class="fas fa-boxes mr-2 text-blue-600"></i> Daftar Barang yang Dipiutangkan
            </h2>

            <div class="overflow-x-auto rounded-lg border">
                <table class="min-w-full text-sm text-gray-600 border-collapse">
                    <thead class="bg-gray-100 text-gray-700 uppercase text-xs">
                        <tr>
                            <th class="py-3 px-4 text-left">Nama Barang</th>
                            <th class="py-3 px-4 text-right">Harga Jual Satuan (Rp)</th>
                            <th class="py-3 px-4 text-center">Jumlah</th>
                            <th class="py-3 px-4 text-right">Subtotal (Rp)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $totalBarang = 0; @endphp
                        @forelse ($hutang->barangHutang as $item)
                            <tr class="border-b hover:bg-blue-50 transition duration-150">
                                @php
                                    // Pastikan relasi berantai aman
                                    $barang = $item->barangKeluar->stokWarung->barang ?? null;
                                    $hargaJual = $item->barangKeluar->stokWarung->hargaJual->first() ?? null;
                                    $harga = $hargaJual ? $hargaJual->harga_jual_range_akhir : 0;
                                    $jumlah = $item->barangKeluar->jumlah ?? 0;
                                    $subtotal = $harga * $jumlah;
                                    $totalBarang += $subtotal;
                                @endphp
                                <td class="py-3 px-4 font-medium text-gray-800">{{ $barang->nama_barang ?? 'Barang Dihapus / N/A' }}</td>
                                <td class="py-3 px-4 text-right">{{ number_format($harga, 0, ',', '.') }}</td>
                                <td class="py-3 px-4 text-center font-bold">{{ $jumlah }}</td>
                                <td class="py-3 px-4 text-right font-bold">{{ number_format($subtotal, 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-center py-6 text-gray-500">Tidak ada detail barang yang terkait dengan transaksi piutang ini.</td></tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr class="bg-blue-50 border-t-2 border-blue-200">
                            <td colspan="3" class="py-3 px-4 text-right font-bold text-base text-gray-800">Total Nilai Barang (Audit):</td>
                            <td class="py-3 px-4 text-right font-extrabold text-lg text-blue-700">Rp. {{ number_format($totalBarang, 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        ---

        {{-- ========================================= --}}
        {{-- RIWAYAT PEMBAYARAN (LOG AUDIT) --}}
        {{-- ========================================= --}}
        <div class="bg-white shadow-xl rounded-xl p-6 border-t-4 border-green-500">
            <h2 class="text-xl font-bold text-gray-800 mb-4 pb-2 border-b-2 border-green-100 flex items-center">
                <i class="fas fa-history mr-2 text-green-600"></i> Riwayat Pembayaran (Log Audit)
            </h2>

            @if ($logPembayaran->isEmpty())
                <div class="bg-gray-50 text-gray-600 p-6 rounded-lg text-center">
                    <i class="fas fa-coins text-3xl mb-2 text-gray-400"></i>
                    <p class="font-medium">Belum ada riwayat pembayaran yang tercatat.</p>
                </div>
            @else
                <div class="overflow-x-auto rounded-lg border">
                    <table class="min-w-full text-sm text-gray-600 border-collapse">
                        <thead class="bg-gray-100 text-gray-700 uppercase text-xs">
                            <tr>
                                <th class="py-3 px-4 text-left">Tanggal Bayar</th>
                                <th class="py-3 px-4 text-right">Jumlah Bayar (Rp)</th>
                                <th class="py-3 px-4 text-left">Metode Pembayaran</th>
                                <th class="py-3 px-4 text-left">Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($logPembayaran as $log)
                                <tr class="border-b hover:bg-green-50 transition duration-150">
                                    <td class="py-3 px-4 font-medium text-gray-800">{{ $log->created_at->translatedFormat('d M Y H:i') }}</td>
                                    <td class="py-3 px-4 text-right font-extrabold text-green-700">{{ number_format($log->jumlah_pembayaran, 0, ',', '.') }}</td>
                                    <td class="py-3 px-4">{{ $log->metode_pembayaran ?? 'Kas' }}</td>
                                    <td class="py-3 px-4 italic text-xs truncate max-w-xs">{{ $log->keterangan ?? '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                             <tr class="bg-green-50 border-t-2 border-green-200">
                                <td colspan="1" class="py-3 px-4 text-right font-bold text-base text-gray-800">Total Pembayaran Audit:</td>
                                <td class="py-3 px-4 text-right font-extrabold text-lg text-green-700">{{ number_format($logPembayaran->sum('jumlah_pembayaran'), 0, ',', '.') }}</td>
                                <td colspan="2" class="py-3 px-4"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            @endif
        </div>

    </main>
</div>
@endsection
