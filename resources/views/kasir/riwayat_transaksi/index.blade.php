@extends('layouts.app')

@section('title', 'Riwayat Transaksi')

@section('content')
<div class="min-h-screen bg-[#f8fafc] pb-20">
    {{-- Header & Statistik --}}
    <div class="bg-indigo-600 pt-8 pb-16 px-4">
        <div class="max-w-7xl mx-auto">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
                <div>
                    <h3 class="text-2xl font-bold text-white flex items-center gap-2">
                        <i class="fas fa-history text-indigo-200"></i>
                        Riwayat Transaksi
                    </h3>
                    <p class="text-indigo-100 text-sm opacity-80">Pantau semua arus kas warung Anda</p>
                </div>
                
                {{-- Card Statistik Kecil --}}
                <div class="bg-white/10 backdrop-blur-md border border-white/20 p-4 rounded-2xl flex items-center gap-4">
                    <div class="bg-white/20 p-3 rounded-xl">
                        <i class="fas fa-receipt text-white"></i>
                    </div>
                    <div>
                        <p class="text-indigo-100 text-[10px] uppercase font-bold tracking-wider">Total Hari Ini</p>
                        <p class="text-white font-black text-xl">{{ $riwayatTransaksi->total() }} <span class="text-sm font-normal">Trx</span></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Content --}}
    <div class="max-w-7xl mx-auto px-4 -mt-10">
        <div class="bg-white rounded-3xl shadow-xl shadow-indigo-100 border border-gray-100 overflow-hidden">
            
            {{-- Filter Area --}}
            <div class="p-4 md:p-6 border-b border-gray-50">
                <form method="GET" class="flex flex-col md:flex-row gap-3">
                    <div class="relative flex-1">
                        <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                        <input type="text" name="search" value="{{ request('search') }}"
                               placeholder="Cari ID Ref atau deskripsi..."
                               class="w-full pl-11 pr-4 py-3 bg-gray-50 border-none rounded-2xl text-sm focus:ring-2 focus:ring-indigo-500 transition-all">
                    </div>
                    
                    <div class="flex gap-2">
                        <select name="filter_jenis" 
                                class="flex-1 md:w-48 py-3 px-4 bg-gray-50 border-none rounded-2xl text-sm font-bold text-gray-600 focus:ring-2 focus:ring-indigo-500">
                            <option value="">Semua Kategori</option>
                            <option value="Penjualan" {{ request('filter_jenis') == 'Penjualan' ? 'selected' : '' }}>Penjualan</option>
                            <option value="Piutang" {{ request('filter_jenis') == 'Piutang' ? 'selected' : '' }}>Piutang</option>
                            <option value="Keluar" {{ request('filter_jenis') == 'Keluar' ? 'selected' : '' }}>Pengeluaran</option>
                        </select>
                        
                        <button type="submit" class="bg-indigo-600 text-white px-6 py-3 rounded-2xl hover:bg-indigo-700 transition shadow-lg shadow-indigo-100">
                            <i class="fas fa-filter"></i>
                        </button>
                    </div>
                </form>
            </div>

            {{-- Mobile View (Card List) - Visible on Mobile only --}}
            <div class="block md:hidden divide-y divide-gray-100">
                @forelse($riwayatTransaksi as $trx)
                <div class="p-4 active:bg-gray-50 transition-colors" data-bs-toggle="modal" data-bs-target="#struk{{ $trx->id_ref }}">
                    <div class="flex justify-between items-start mb-2">
                        <div>
                            <span class="text-[10px] font-black text-indigo-500 bg-indigo-50 px-2 py-0.5 rounded uppercase tracking-tighter">#{{ $trx->id_ref }}</span>
                            <h4 class="font-bold text-gray-800 mt-1">{{ $trx->jenis_transaksi }}</h4>
                        </div>
                        <div class="text-right">
                            <p class="font-black {{ $trx->total >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                {{ $trx->total >= 0 ? '+' : '-' }} Rp {{ number_format(abs($trx->total),0,',','.') }}
                            </p>
                            <p class="text-[10px] text-gray-400 uppercase font-bold">{{ $trx->metode_pembayaran ?? 'CASH' }}</p>
                        </div>
                    </div>
                    
                    <div class="flex justify-between items-end">
                        <div class="flex items-center gap-2 text-gray-400">
                            <i class="far fa-clock text-xs"></i>
                            <span class="text-xs">{{ $trx->tanggal->format('d M, H:i') }} WIB</span>
                        </div>
                        <span class="text-xs text-indigo-600 font-bold">Lihat Detail <i class="fas fa-chevron-right ml-1"></i></span>
                    </div>
                </div>
                @empty
                <div class="py-20 text-center">
                    <i class="fas fa-search text-gray-200 text-5xl mb-4"></i>
                    <p class="text-gray-400">Tidak ada transaksi ditemukan</p>
                </div>
                @endforelse
            </div>

            {{-- Desktop View (Table) - Hidden on Mobile --}}
            <div class="hidden md:block">
                <div class="table-responsive">
                    <table class="w-full">
                        <thead class="bg-gray-50 text-[10px] font-bold text-gray-400 uppercase tracking-widest border-b">
                            <tr>
                                <th class="px-6 py-4 text-left">ID Ref</th>
                                <th class="px-6 py-4 text-left">Waktu</th>
                                <th class="px-6 py-4 text-left">Jenis</th>
                                <th class="px-6 py-4 text-left">Deskripsi</th>
                                <th class="px-6 py-4 text-right">Total</th>
                                <th class="px-6 py-4 text-center">Metode</th>
                                <th class="px-6 py-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($riwayatTransaksi as $trx)
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-6 py-4">
                                    <span class="font-bold text-indigo-600">#{{ $trx->id_ref }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-bold text-gray-800">{{ $trx->tanggal->format('d M Y') }}</div>
                                    <div class="text-[10px] text-gray-400 font-bold uppercase">{{ $trx->tanggal->format('H:i') }} WIB</div>
                                </td>
                                <td class="px-6 py-4">
                                    @php
                                        $style = 'bg-gray-100 text-gray-600';
                                        if(str_contains($trx->jenis_transaksi, 'Penjualan')) $style = 'bg-green-100 text-green-700';
                                        elseif(str_contains($trx->jenis_transaksi, 'Piutang')) $style = 'bg-amber-100 text-amber-700';
                                        elseif(str_contains($trx->jenis_transaksi, 'Keluar')) $style = 'bg-red-100 text-red-700';
                                    @endphp
                                    <span class="{{ $style }} text-[10px] font-black px-3 py-1 rounded-full uppercase">
                                        {{ $trx->jenis_transaksi }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500 truncate max-w-[200px]">
                                    {{ $trx->deskripsi }}
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <span class="font-black {{ $trx->total >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                        {{ $trx->total >= 0 ? '+' : '-' }} Rp {{ number_format(abs($trx->total),0,',','.') }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center uppercase text-[10px] font-bold text-gray-400">
                                    {{ $trx->metode_pembayaran ?? 'Cash' }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <button data-bs-toggle="modal" data-bs-target="#struk{{ $trx->id_ref }}"
                                            class="w-8 h-8 rounded-full bg-indigo-50 text-indigo-600 hover:bg-indigo-600 hover:text-white transition-all">
                                        <i class="fas fa-eye text-xs"></i>
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Footer Pagination --}}
            <div class="p-4 md:p-6 bg-gray-50/50 border-t border-gray-100">
                {{ $riwayatTransaksi->links() }}
            </div>
        </div>
    </div>
</div>

{{-- MODAL STRUK (Disesuaikan agar pas di layar HP) --}}
@foreach($riwayatTransaksi as $trx)
<div class="modal fade" id="struk{{ $trx->id_ref }}" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-fullscreen-sm-down">
        <div class="modal-content border-0 shadow-2xl rounded-3xl md:rounded-[2rem] overflow-hidden">
            <div class="modal-body p-6 md:p-8">
                <div class="flex flex-col items-center mb-8">
                    <div class="w-16 h-16 bg-indigo-100 text-indigo-600 rounded-2xl flex items-center justify-center mb-4">
                        <i class="fas fa-store text-2xl"></i>
                    </div>
                    <h5 class="text-xl font-black text-gray-900 tracking-tight">STRUK DIGITAL</h5>
                    <p class="text-gray-400 text-xs font-bold uppercase tracking-widest">Warung Digital Indonesia</p>
                </div>

                <div class="space-y-3 bg-gray-50 rounded-2xl p-4 border border-gray-100 mb-6">
                    <div class="flex justify-between text-xs">
                        <span class="text-gray-400 font-bold uppercase tracking-tighter">ID Referensi</span>
                        <span class="text-gray-900 font-black">#{{ $trx->id_ref }}</span>
                    </div>
                    <div class="flex justify-between text-xs">
                        <span class="text-gray-400 font-bold uppercase tracking-tighter">Waktu</span>
                        <span class="text-gray-900 font-black">{{ $trx->tanggal->format('d/m/Y H:i') }}</span>
                    </div>
                    <div class="flex justify-between text-xs">
                        <span class="text-gray-400 font-bold uppercase tracking-tighter">Metode</span>
                        <span class="text-gray-900 font-black uppercase">{{ $trx->metode_pembayaran ?? 'Cash' }}</span>
                    </div>
                </div>

                <div class="border-t-2 border-dashed border-gray-100 my-6"></div>

                <div class="space-y-4 mb-6">
                    @foreach($trx->items ?? [] as $item)
                    <div class="flex justify-between">
                        <div>
                            <p class="text-sm font-bold text-gray-800">{{ $item->nama_barang }}</p>
                            <p class="text-[10px] text-gray-400 font-bold uppercase">{{ $item->jumlah }} x Rp {{ number_format($item->harga,0,',','.') }}</p>
                        </div>
                        <p class="text-sm font-black text-gray-900">Rp {{ number_format($item->subtotal,0,',','.') }}</p>
                    </div>
                    @endforeach
                </div>

                <div class="bg-indigo-600 rounded-2xl p-5 shadow-lg shadow-indigo-100">
                    <div class="flex justify-between items-center text-white">
                        <span class="text-xs font-bold uppercase tracking-widest opacity-80">Total Akhir</span>
                        <span class="text-xl font-black italic">Rp {{ number_format($trx->total,0,',','.') }}</span>
                    </div>
                </div>

                <div class="mt-8 flex flex-col gap-2">
                    <button class="w-full bg-gray-900 text-white py-4 rounded-2xl font-bold flex items-center justify-center gap-2 hover:bg-black transition shadow-xl" onclick="window.print()">
                        <i class="fas fa-print"></i> Cetak Struk
                    </button>
                    <button class="w-full py-4 text-gray-400 font-bold text-sm hover:text-gray-600 transition" data-bs-dismiss="modal">Tutup Nota</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endforeach

<style>
    /* Styling khusus pagination agar tidak berantakan di HP */
    .pagination { display: flex; flex-wrap: wrap; justify-content: center; gap: 4px; }
    .page-item .page-link { border: none; background: #f1f5f9; border-radius: 12px; font-weight: 800; color: #475569; padding: 10px 16px; }
    .page-item.active .page-link { background: #4f46e5; color: white; box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3); }
</style>
@endsection