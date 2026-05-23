@extends('layouts.app')

@section('title', 'Riwayat Transaksi')

@section('content')
<div class="min-h-screen bg-[#f8fafc] pb-20">
    {{-- Header & Statistik --}}
    <div class="bg-indigo-600 pt-8 pb-24 px-4">
        <div class="max-w-7xl mx-auto">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-xl md:text-2xl font-bold text-white flex items-center gap-2">
                        <i class="fas fa-chart-pie text-indigo-200"></i>
                        Riwayat Transaksi
                    </h3>
                    <p class="text-indigo-100 text-[11px] opacity-80 uppercase tracking-wider font-semibold">
                        Periode: {{ $startDate->format('d M') }} - {{ $endDate->format('d M Y') }}
                    </p>
                </div>
                <div class="text-right">
                    <span class="text-indigo-200 text-[10px] block uppercase font-bold tracking-tighter">Total Trx</span>
                    <span class="text-white font-black text-xl">{{ $riwayatTransaksi->total() }}</span>
                </div>
            </div>

            {{-- Statistik Container (Scrollable on Mobile, Grid on Desktop) --}}
            <div class="flex overflow-x-auto pb-4 gap-4 snap-x md:grid md:grid-cols-3 md:overflow-visible">
                
                {{-- Card Omset --}}
                <div class="min-w-[280px] snap-center bg-white rounded-2xl p-5 shadow-lg flex-shrink-0">
                    <div class="flex justify-between items-start mb-2">
                        <span class="text-gray-400 text-[10px] font-bold uppercase tracking-widest">Total Omset</span>
                        <i class="fas fa-money-bill-wave text-indigo-500"></i>
                    </div>
                    <p class="text-2xl font-black text-gray-900 mb-3">Rp {{ number_format($totalOmsetPeriode, 0, ',', '.') }}</p>
                    <div class="grid grid-cols-2 gap-2 pt-3 border-t border-gray-50">
                        <div>
                            <span class="text-[9px] text-gray-400 block font-bold uppercase">Barang</span>
                            <span class="text-xs font-bold text-gray-700">Rp {{ number_format($totalOmsetBarang, 0, ',', '.') }}</span>
                        </div>
                        <div>
                            <span class="text-[9px] text-gray-400 block font-bold uppercase">Pulsa</span>
                            <span class="text-xs font-bold text-gray-700">Rp {{ number_format($totalOmsetPulsa, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

                {{-- Card Laba --}}
                <div class="min-w-[280px] snap-center bg-emerald-500 rounded-2xl p-5 shadow-lg text-white flex-shrink-0">
                    <div class="flex justify-between items-start mb-2">
                        <span class="text-emerald-100 text-[10px] font-bold uppercase tracking-widest">Estimasi Laba</span>
                        <i class="fas fa-vault text-emerald-200"></i>
                    </div>
                    <p class="text-2xl font-black text-white mb-3">Rp {{ number_format($totalLabaPeriode, 0, ',', '.') }}</p>
                    <div class="grid grid-cols-2 gap-2 pt-3 border-t border-emerald-400/30">
                        <div>
                            <span class="text-[9px] text-emerald-100 block font-bold uppercase">Laba Barang</span>
                            <span class="text-xs font-bold">Rp {{ number_format($totalLabaBarang, 0, ',', '.') }}</span>
                        </div>
                        <div>
                            <span class="text-[9px] text-emerald-100 block font-bold uppercase">Laba Pulsa</span>
                            <span class="text-xs font-bold">Rp {{ number_format($totalLabaPulsa, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

                {{-- Card Efisiensi/Margin --}}
                <div class="min-w-[280px] snap-center bg-indigo-800 rounded-2xl p-5 shadow-lg text-white flex-shrink-0">
                    <div class="flex justify-between items-start mb-2">
                        <span class="text-indigo-200 text-[10px] font-bold uppercase tracking-widest">Margin Keuntungan</span>
                        <i class="fas fa-percentage text-indigo-300"></i>
                    </div>
                    @php $margin = $totalOmsetPeriode > 0 ? ($totalLabaPeriode / $totalOmsetPeriode) * 100 : 0; @endphp
                    <p class="text-2xl font-black text-white mb-3">{{ number_format($margin, 1) }}%</p>
                    <p class="text-[10px] text-indigo-300 italic font-medium leading-tight">Keuntungan bersih rata-rata per transaksi.</p>
                </div>

            </div>
        </div>
    </div>

    {{-- Main Content --}}
    <div class="max-w-7xl mx-auto px-4 -mt-12">
        <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden">
            
            {{-- Filter Area --}}
            <div class="p-4 md:p-6 border-b border-gray-50 bg-gray-50/30">
                <form method="GET" class="space-y-4">
                    <div class="flex flex-col md:flex-row gap-3">
                        <div class="relative flex-1">
                            <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                            <input type="text" name="search" value="{{ $search }}"
                                   placeholder="Cari ID transaksi atau barang..."
                                   class="w-full pl-11 pr-4 py-3.5 bg-white border-2 border-gray-100 rounded-2xl text-sm focus:border-indigo-500 focus:ring-0 transition-all shadow-sm">
                        </div>

                        <div class="flex gap-2">
                            <select name="month" class="flex-1 md:w-36 py-3.5 px-4 bg-white border-2 border-gray-100 rounded-2xl text-sm font-bold text-gray-600 focus:border-indigo-500 shadow-sm">
                                @for($m=1; $m<=12; $m++)
                                    <option value="{{ sprintf('%02d', $m) }}" {{ $selectedMonth == $m ? 'selected' : '' }}>
                                        {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                                    </option>
                                @endfor
                            </select>

                            <select name="year" class="w-28 py-3.5 px-4 bg-white border-2 border-gray-100 rounded-2xl text-sm font-bold text-gray-600 focus:border-indigo-500 shadow-sm">
                                @for($y=date('Y'); $y>=2023; $y--)
                                    <option value="{{ $y }}" {{ $selectedYear == $y ? 'selected' : '' }}>{{ $y }}</option>
                                @endfor
                            </select>
                            
                            <button type="submit" class="bg-indigo-600 text-white w-14 rounded-2xl hover:bg-indigo-700 shadow-indigo-200 shadow-lg transition-all">
                                <i class="fas fa-filter"></i>
                            </button>
                        </div>
                    </div>
                    <p class="text-[10px] text-gray-400 font-bold px-2 italic text-center md:text-left">* Menampilkan data siklus tanggal 7 ke tanggal 6</p>
                </form>
            </div>

            {{-- Mobile List View (Visible on Mobile) --}}
            <div class="md:hidden divide-y divide-gray-50">
                @forelse($riwayatTransaksi as $trx)
                <div class="p-4 active:bg-gray-50 transition-colors" data-bs-toggle="modal" data-bs-target="#struk{{ $trx->id_ref }}">
                    <div class="flex justify-between items-start mb-2">
                        <div class="flex flex-col">
                            <span class="text-[10px] font-black text-indigo-500 uppercase">#{{ $trx->id_ref }}</span>
                            <span class="text-[10px] text-gray-400 font-bold">{{ $trx->tanggal->format('d M Y | H:i') }}</span>
                        </div>
                        <span class="text-[9px] font-black px-2 py-1 rounded-lg uppercase bg-indigo-50 text-indigo-600">
                            {{ $trx->jenis_transaksi }}
                        </span>
                    </div>
                    
                    <div class="flex justify-between items-end">
                        <div class="flex-1 pr-4">
                            <p class="text-sm font-bold text-gray-800 line-clamp-1 mb-1">{{ $trx->deskripsi }}</p>
                            <span class="text-[10px] font-bold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded">Laba: Rp {{ number_format($trx->laba_kasir, 0, ',', '.') }}</span>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-black {{ $trx->total >= 0 ? 'text-gray-900' : 'text-red-600' }}">
                                {{ $trx->total >= 0 ? '+' : '' }} Rp {{ number_format($trx->total, 0, ',', '.') }}
                            </p>
                        </div>
                    </div>
                </div>
                @empty
                <div class="p-12 text-center">
                    <i class="fas fa-folder-open text-4xl text-gray-200 mb-3"></i>
                    <p class="text-sm text-gray-400 font-bold">Data transaksi tidak ditemukan</p>
                </div>
                @endforelse
            </div>

            {{-- Desktop Table View (Hidden on Mobile) --}}
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 text-[10px] font-bold text-gray-400 uppercase tracking-widest border-b">
                        <tr>
                            <th class="px-6 py-4 text-left">Waktu & ID</th>
                            <th class="px-6 py-4 text-left">Kategori</th>
                            <th class="px-6 py-4 text-left">Deskripsi</th>
                            <th class="px-6 py-4 text-right">Laba</th>
                            <th class="px-6 py-4 text-right">Total Akhir</th>
                            <th class="px-6 py-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($riwayatTransaksi as $trx)
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="text-sm font-black text-indigo-600">#{{ $trx->id_ref }}</div>
                                <div class="text-[10px] text-gray-400 font-bold uppercase">{{ $trx->tanggal->format('d M Y | H:i') }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-[10px] font-black px-3 py-1 rounded-full uppercase bg-indigo-50 text-indigo-600">
                                    {{ $trx->jenis_transaksi }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-xs text-gray-500 truncate max-w-[200px]">
                                {{ $trx->deskripsi }}
                            </td>
                            <td class="px-6 py-4 text-right font-bold text-emerald-600">
                                Rp {{ number_format($trx->laba_kasir, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 text-right font-black {{ $trx->total >= 0 ? 'text-gray-900' : 'text-red-600' }}">
                                {{ $trx->total >= 0 ? '+' : '' }} Rp {{ number_format($trx->total, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                <button data-bs-toggle="modal" data-bs-target="#struk{{ $trx->id_ref }}"
                                        class="w-8 h-8 rounded-full bg-gray-100 text-gray-600 hover:bg-indigo-600 hover:text-white transition-all">
                                    <i class="fas fa-eye text-xs"></i>
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="p-4 md:p-6 bg-gray-50/50 border-t border-gray-100">
                {{ $riwayatTransaksi->appends(request()->input())->links() }}
            </div>
        </div>
    </div>
</div>

{{-- MODAL STRUK (Bottom Sheet Style on Mobile) --}}
@foreach($riwayatTransaksi as $trx)
<div class="modal fade px-0 md:px-4" id="struk{{ $trx->id_ref }}" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-fullscreen-sm-down">
        <div class="modal-content !border-0 shadow-2xl rounded-t-[2.5rem] md:rounded-[2.5rem] overflow-hidden">
            <div class="modal-body p-6 md:p-10">
                {{-- Handle Bar for Mobile --}}
                <div class="w-12 h-1.5 bg-gray-200 rounded-full mx-auto mb-6 md:hidden"></div>
                
                <div class="text-center mb-8">
                    <div class="w-16 h-16 bg-indigo-50 text-indigo-600 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-receipt text-2xl"></i>
                    </div>
                    <h5 class="text-xl font-black text-gray-900 uppercase tracking-tight">Detail Transaksi</h5>
                    <p class="text-xs text-gray-400 font-bold tracking-widest mt-1">NO. REF #{{ $trx->id_ref }}</p>
                </div>

                <div class="space-y-3 mb-8 bg-gray-50 p-4 rounded-2xl">
                    <div class="flex justify-between items-center">
                        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Waktu Transaksi</span>
                        <span class="text-xs font-black text-gray-800">{{ $trx->tanggal->format('d/m/Y H:i') }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Metode Bayar</span>
                        <span class="text-xs font-black text-indigo-600 uppercase">{{ $trx->metode_pembayaran ?? 'Tunai' }}</span>
                    </div>
                </div>

                <div class="space-y-4 mb-8">
                    <p class="text-[10px] font-black text-gray-300 uppercase tracking-[0.2em]">Item Transaksi</p>
                    @forelse($trx->items ?? [] as $item)
                    <div class="flex justify-between items-start">
                        <div class="flex-1 pr-4">
                            <p class="text-sm font-bold text-gray-800 leading-tight mb-1">{{ $item->nama_barang }}</p>
                            <p class="text-[10px] text-gray-400 font-bold uppercase">{{ $item->jumlah }} x {{ number_format($item->harga, 0, ',', '.') }}</p>
                        </div>
                        <p class="text-sm font-black text-gray-900">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</p>
                    </div>
                    @empty
                    <p class="text-xs text-gray-500 italic">{{ $trx->deskripsi }}</p>
                    @endforelse
                </div>

                <div class="border-t-2 border-dashed border-gray-100 pt-6 space-y-4 mb-8">
                    <div class="flex justify-between items-center">
                        <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">Laba Bersih</span>
                        <span class="text-sm font-black text-emerald-600">Rp {{ number_format($trx->laba_kasir, 0, ',', '.') }}</span>
                    </div>
                    <div class="bg-indigo-600 p-5 rounded-2xl shadow-xl shadow-indigo-100 flex justify-between items-center text-white">
                        <span class="text-xs font-bold uppercase tracking-widest opacity-80">Total Bayar</span>
                        <span class="text-2xl font-black">Rp {{ number_format($trx->total, 0, ',', '.') }}</span>
                    </div>
                </div>

                <button class="w-full bg-gray-100 text-gray-500 py-4 rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-gray-200 transition-all" data-bs-dismiss="modal">
                    Tutup Rincian
                </button>
            </div>
        </div>
    </div>
</div>
@endforeach
@endsection