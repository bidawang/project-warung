@extends('layouts.admin')

@section('title', 'Riwayat Operasional')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <h2 class="text-2xl font-bold text-gray-800">Riwayat Operasional</h2>
        <a href="{{ route('admin.operasional.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition flex items-center shadow-md font-bold">
            + Tambah Operasional
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead class="bg-gray-50 text-gray-600 text-xs uppercase font-bold border-b">
                <tr>
                    <th class="px-6 py-4 w-10 text-center">#</th>
                    <th class="px-6 py-4">Informasi Transaksi Induk (Grup)</th>
                    <th class="px-6 py-4 text-right">Total Transaksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 text-sm">
                @forelse($riwayatGrouped as $idTransaksiWrb => $details)
                @php
                    // Ambil info dari baris pertama dalam grup ini sebagai header
                    $header = $details->first()->transaksiAwal;
                    $totalHargaGrup = $details->sum('harga');
                @endphp
                <tr x-data="{ open: false }" class="border-b last:border-none">
                    <td colspan="3" class="p-0">
                        <div @click="open = !open" class="flex items-center px-6 py-4 cursor-pointer hover:bg-gray-50 transition group">
                            <div class="w-10">
                                <svg :class="open ? 'rotate-180 text-blue-600' : 'text-gray-400'" class="w-4 h-4 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </div>
                            <div class="flex-1 flex justify-between items-center">
                                <div>
                                    <span class="block text-gray-500 text-xs">{{ $header->created_at->format('d/m/Y H:i') }}</span>
                                    <span class="font-bold text-gray-800 group-hover:text-blue-600 transition">{{ $header->keterangan ?? 'Tanpa Keterangan' }}</span>
                                </div>
                                <div class="text-right">
                                    <span class="block text-gray-400 text-[10px] uppercase font-bold">Total Pengeluaran</span>
                                    <span class="font-black text-red-600">Rp {{ number_format((float)$totalHargaGrup, 0, ',', '.') }}</span>
                                </div>
                            </div>
                        </div>

                        <div x-show="open" x-collapse class="bg-gray-50/50 px-16 pb-4 pt-2 border-t border-gray-100">
                            <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
                                <table class="w-full text-xs">
                                    <thead class="bg-gray-100 text-gray-400 font-bold uppercase">
                                        <tr>
                                            <th class="px-4 py-2">Detail Komponen Biaya</th>
                                            <th class="px-4 py-2 text-right">Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-50">
                                        @foreach($details as $detail)
                                        <tr class="hover:bg-blue-50/30 transition">
                                            <td class="px-4 py-2 text-gray-700 font-medium">{{ $detail->keterangan }}</td>
                                            <td class="px-4 py-2 text-right text-gray-900 font-bold">
                                                Rp {{ number_format((float)($detail->harga ?? 0), 0, ',', '.') }}
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" class="px-6 py-10 text-center text-gray-400 italic font-medium">
                        Tidak ada riwayat biaya ditemukan.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection