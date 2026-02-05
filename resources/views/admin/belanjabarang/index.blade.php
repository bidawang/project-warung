@extends('layouts.admin')

@section('title', 'Riwayat Belanja Barang')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Riwayat Belanja Barang</h2>
            <p class="text-sm text-gray-500">Dikelompokkan berdasarkan Warung dan Tanggal.</p>
        </div>
        
        <form action="{{ route('admin.belanja-barang.index') }}" method="GET" class="relative">
            <input type="text" name="search" value="{{ $search }}" 
                placeholder="Cari barang atau warung..." 
                class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 shadow-sm text-sm">
            <div class="absolute left-3 top-2.5 text-gray-400">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead class="bg-gray-50 text-gray-600 text-xs uppercase font-bold border-b">
                <tr>
                    <th class="px-6 py-4 w-10"></th>
                    <th class="px-6 py-4">Warung & Tanggal</th>
                    <th class="px-6 py-4 text-center">Item</th>
                    <th class="px-6 py-4 text-right">Total Belanja</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 text-sm">
                @forelse($groupedRiwayat as $groupName => $items)
                    @php
                        $firstItem = $items->first();
                        $totalGrup = $items->sum(function($i) {
                            return $i->transaksiBarang->first()->harga ?? 0;
                        });
                    @endphp
                    <tr x-data="{ open: false }" class="border-b last:border-none">
                        <td colspan="4" class="p-0">
                            <div @click="open = !open" class="flex items-center px-6 py-4 cursor-pointer hover:bg-gray-50 transition group">
                                <div class="w-10">
                                    <svg :class="open ? 'rotate-180 text-blue-600' : 'text-gray-400'" class="w-4 h-4 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </div>
                                <div class="flex-1 flex justify-between items-center">
                                    <div>
                                        <span class="font-bold text-gray-800 group-hover:text-blue-600">{{ $firstItem->stokWarung->warung->nama_warung ?? 'N/A' }}</span>
                                        <span class="ml-2 text-gray-500 text-xs">{{ $firstItem->created_at->format('d M Y') }}</span>
                                    </div>
                                    <div class="flex items-center space-x-8">
                                        <span class="text-xs bg-blue-50 text-blue-600 px-2 py-1 rounded-full font-bold">
                                            {{ $items->count() }} Produk
                                        </span>
                                        <span class="font-black text-gray-900">
                                            Rp {{ number_format($totalGrup, 0, ',', '.') }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div x-show="open" x-collapse class="bg-gray-50/50 px-16 pb-4 pt-2 border-t border-gray-100">
                                <div class="bg-white rounded-lg border border-gray-200 overflow-hidden shadow-sm">
                                    <table class="w-full text-xs">
                                        <thead class="bg-gray-100 text-gray-400 font-bold uppercase">
                                            <tr>
                                                <th class="px-4 py-2">Nama Barang</th>
                                                <th class="px-4 py-2 text-center">Jumlah</th>
                                                <th class="px-4 py-2 text-right">Subtotal</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-100">
                                            @foreach($items as $detail)
                                                @php
                                                    $subtotal = $detail->transaksiBarang->first()->harga ?? 0;
                                                @endphp
                                                <tr class="hover:bg-blue-50/30 transition">
                                                    <td class="px-4 py-3 font-medium text-gray-700">
                                                        {{ $detail->stokWarung->barang->nama_barang ?? 'N/A' }}
                                                    </td>
                                                    <td class="px-4 py-3 text-center">
                                                        <span class="font-bold">{{ $detail->jumlah }}</span>
                                                    </td>
                                                    <td class="px-4 py-3 text-right font-bold text-gray-900">
                                                        Rp {{ number_format($subtotal, 0, ',', '.') }}
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
                        <td colspan="4" class="px-6 py-10 text-center text-gray-400 italic">
                            Tidak ada data belanja barang.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        
        <div class="px-6 py-4 bg-gray-50 border-t">
            {{ $riwayatBelanja->appends(['search' => $search])->links() }}
        </div>
    </div>
</div>
@endsection