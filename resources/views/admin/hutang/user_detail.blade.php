@extends('layouts.admin')

@section('title', 'Detail Hutang Pelanggan')

@section('content')
    <div class="flex-1 flex flex-col bg-gray-100 overflow-y-auto">
        {{-- Header --}}
        <header class="p-6 bg-white border-b flex justify-between items-center sticky top-0 z-10">
            <div>
                <a href="{{ route('admin.hutang.index') }}"
                    class="text-indigo-600 hover:text-indigo-800 text-sm font-semibold">
                    <i class="fas fa-arrow-left mr-1"></i> Kembali ke Daftar
                </a>
                <h1 class="text-2xl font-bold text-gray-800 mt-1">Rincian Hutang: {{ $user->name }}</h1>
            </div>
            <div class="text-right">
                <p class="text-sm text-gray-500 font-medium">Total Sisa Piutang</p>
                <p class="text-2xl font-extrabold text-red-600">Rp {{ number_format($totalSisa, 0, ',', '.') }}</p>
            </div>
        </header>

        <main class="p-6 space-y-6">
            {{-- Ringkasan --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-white p-4 rounded-xl shadow-sm border">
                    <p class="text-xs text-gray-500 uppercase">Total Hutang Awal</p>
                    <p class="text-lg font-bold text-gray-800">Rp {{ number_format($totalHutang, 0, ',', '.') }}</p>
                </div>
                <div class="bg-white p-4 rounded-xl shadow-sm border">
                    <p class="text-xs text-gray-500 uppercase">Jumlah Nota</p>
                    <p class="text-lg font-bold text-gray-800">{{ $hutangList->count() }} Transaksi</p>
                </div>
                <div class="bg-white p-4 rounded-xl shadow-sm border">
                    <p class="text-xs text-gray-500 uppercase">Status Global</p>
                    @if ($totalSisa <= 0)
                        <span class="text-green-600 font-bold"><i class="fas fa-check-circle"></i> LUNAS</span>
                    @else
                        <span class="text-red-600 font-bold"><i class="fas fa-exclamation-circle"></i> BELUM LUNAS</span>
                    @endif
                </div>
            </div>

            {{-- Tabel Rincian Nota --}}
            <div class="bg-white shadow-xl rounded-xl overflow-hidden border">
                <div class="p-4 bg-gray-50 border-b">
                    <h2 class="font-bold text-gray-700"><i class="fas fa-file-invoice-dollar mr-2"></i> Daftar Nota/Bon</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-100 text-gray-600 uppercase text-xs">
                            <tr>
                                <th class="py-3 px-4 text-left">Tanggal Nota</th>
                                <th class="py-3 px-4 text-left">Warung</th>
                                <th class="py-3 px-4 text-right">Awal (Rp)</th>
                                <th class="py-3 px-4 text-right">Sisa (Rp)</th>
                                <th class="py-3 px-4 text-center">Status</th>
                                <th class="py-3 px-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach ($hutangList as $h)
                                {{-- Di dalam loop @foreach ($hutangList as $h) --}}
                                <tr class="hover:bg-gray-50">
                                    <td class="py-3 px-4 font-medium">{{ $h->created_at->format('d/m/Y') }}</td>
                                    <td class="py-3 px-4">{{ $h->warung->nama_warung }}</td>
                                    <td class="py-3 px-4 text-right">
                                        {{-- Menghitung total cicilan dari log_pembayaran_hutang --}}
                                        <span class="text-xs text-green-600 block">Dibayar:</span>
                                        Rp {{ number_format($h->pembayarans->sum('jumlah_pembayaran'), 0, ',', '.') }}
                                    </td>
                                    <td class="py-3 px-4 text-right font-bold text-red-600">
                                        <span class="text-xs text-gray-400 block font-normal">Sisa Tagihan:</span>
                                        Rp {{ number_format($h->jumlah_sisa_hutang, 0, ',', '.') }}
                                    </td>
                                    <td class="py-3 px-4 text-center">
                                        <span
                                            class="px-2 py-1 rounded-full text-[10px] font-bold {{ $h->status === 'lunas' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                            {{ strtoupper($h->status) }}
                                        </span>
                                    </td>
                                    {{-- status --}}

                                    <td class="py-3 px-4 text-center">
                                        <a href="{{ route('admin.hutang.detail', $h->id) }}"
                                            class="bg-gray-800 text-white px-3 py-1 rounded text-xs">
                                            Cek Log
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
@endsection
