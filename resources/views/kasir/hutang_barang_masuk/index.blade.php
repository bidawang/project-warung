@extends('layouts.app')

@section('title', 'Data Hutang Barang Masuk')

@section('content')

<div class="min-h-screen bg-gray-50/50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        {{-- HEADER SECTION --}}
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-8">
            <div>
                <nav class="flex mb-2" aria-label="Breadcrumb">
                    <ol class="flex items-center space-x-2 text-xs text-gray-400">
                        <li>Manajemen Stok</li>
                        <li><i class="fas fa-chevron-right text-[10px]"></i></li>
                        <li class="text-yellow-600 font-medium">Hutang Barang</li>
                    </ol>
                </nav>
                <h3 class="text-3xl font-extrabold text-gray-900 tracking-tight">Manajemen Hutang</h3>
                <p class="text-gray-500 mt-1">Pantau dan kelola kewajiban pembayaran stok masuk.</p>
            </div>

            {{-- TOTAL CARD --}}
            <div class="relative group cursor-default">
                <div class="absolute -inset-1 bg-gradient-to-r from-yellow-400 to-orange-500 rounded-2xl blur opacity-25 group-hover:opacity-50 transition duration-1000"></div>
                <div class="relative bg-white border border-yellow-100 rounded-2xl p-5 flex items-center gap-5 shadow-sm">
                    <div class="bg-yellow-500 p-3 rounded-xl">
                        <i class="fas fa-wallet text-white text-xl"></i>
                    </div>
                    <div>
                        <span class="block text-[10px] uppercase tracking-wider font-bold text-gray-400">Total Hutang Aktif</span>
                        <h4 class="text-2xl font-black text-gray-800">
                            <span class="text-yellow-500 text-sm">Rp</span> {{ number_format($totalHutang, 0, ',', '.') }}
                        </h4>
                    </div>
                </div>
            </div>
        </div>

        {{-- FILTER & SEARCH --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-2 mb-8 flex items-center gap-2">
            <form method="GET" class="flex-1 flex items-center">
                <div class="relative flex-1 group">
                    <div class="absolute inset-y-0 left-4 flex items-center pointer-events-none text-gray-400 group-focus-within:text-yellow-500 transition-colors">
                        <i class="fas fa-search"></i>
                    </div>
                    <input 
                        type="text" 
                        name="q" 
                        value="{{ request('q') }}"
                        placeholder="Cari berdasarkan ID Nota atau Nama Barang..." 
                        class="w-full bg-transparent border-0 rounded-xl pl-12 pr-4 py-3 text-sm text-gray-700 focus:ring-0 placeholder-gray-400"
                    >
                </div>
                <button type="submit" class="bg-gray-900 hover:bg-black text-white text-sm font-bold px-6 py-2.5 rounded-xl transition duration-200 shadow-lg shadow-gray-200">
                    Cari Data
                </button>
            </form>
            @if(request('q'))
                <a href="{{ url()->current() }}" class="text-xs text-gray-400 hover:text-red-500 px-3 transition-colors">Reset</a>
            @endif
        </div>

        <div class="flex items-center justify-between mb-5 px-1">
            <h5 class="text-sm font-bold text-gray-800 flex items-center gap-2">
                <span class="w-1.5 h-5 bg-yellow-500 rounded-full"></span>
                Histori Hutang Terbaru
            </h5>
            <span class="text-[10px] font-semibold text-gray-400 bg-gray-100 px-2 py-1 rounded-md uppercase tracking-widest">Live Updates</span>
        </div>

        {{-- ACCORDION LIST --}}
        <div class="grid gap-4" x-data="{ open: null }">

            @forelse($hutangList as $hutang)
                @php $collapseId = 'hutang' . $hutang->id; @endphp

                <div 
                    class="group bg-white rounded-2xl border transition-all duration-300"
                    :class="open === '{{ $collapseId }}' ? 'ring-2 ring-yellow-400 border-transparent shadow-xl' : 'hover:border-yellow-200 shadow-sm border-gray-100'"
                >
                    {{-- HEADER --}}
                    <button 
                        @click="open === '{{ $collapseId }}' ? open = null : open = '{{ $collapseId }}'"
                        class="w-full text-left p-5 flex flex-col md:flex-row md:items-center justify-between gap-4"
                    >
                        <div class="flex items-center gap-4 flex-1">
                            <div class="bg-gray-50 group-hover:bg-yellow-50 p-3 rounded-xl transition-colors">
                                <i class="fas fa-file-invoice text-gray-400 group-hover:text-yellow-600 transition-colors"></i>
                            </div>
                            <div class="grid grid-cols-2 md:grid-cols-2 gap-x-8 gap-y-1">
                                <div>
                                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-tighter">No. Nota</p>
                                    <p class="font-bold text-gray-900">#HTG-{{ str_pad($hutang->id, 5, '0', STR_PAD_LEFT) }}</p>
                                </div>
                                <div>
                                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-tighter">Tanggal Input</p>
                                    <p class="text-sm font-medium text-gray-700">{{ $hutang->created_at->translatedFormat('d M Y') }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-between md:justify-end gap-6 border-t md:border-t-0 pt-3 md:pt-0">
                            <div class="text-right">
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-tighter">Total Kewajiban</p>
                                <p class="text-lg font-black text-yellow-600">Rp {{ number_format($hutang->total, 0, ',', '.') }}</p>
                            </div>
                            <div class="text-gray-300 transition-transform duration-300" :class="open === '{{ $collapseId }}' ? 'rotate-180 text-yellow-500' : ''">
                                <i class="fas fa-chevron-down"></i>
                            </div>
                        </div>
                    </button>

                    {{-- BODY (COLLAPSIBLE) --}}
                    <div 
                        x-show="open === '{{ $collapseId }}'" 
                        x-collapse
                        class="px-5 pb-5"
                    >
                        <div class="bg-gray-50/80 rounded-xl border border-gray-100 p-4 overflow-hidden">
                            <table class="w-full">
                                <thead class="text-[10px] font-bold text-gray-400 uppercase border-b border-gray-200">
                                    <tr>
                                        <th class="text-left pb-3 px-2">Informasi Barang</th>
                                        <th class="text-center pb-3">Kuantitas</th>
                                        <th class="text-right pb-3 px-2">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @foreach ($hutang->hutangBarangMasuk as $detail)
                                        <tr class="group/row transition-colors">
                                            <td class="py-4 px-2">
                                                <div class="font-bold text-gray-800 text-sm">
                                                    {{ $detail->barangMasuk->transaksiBarang->barang->nama_barang ?? '-' }}
                                                </div>
                                                <div class="flex items-center gap-2 mt-1">
                                                    <span class="text-[10px] bg-red-50 text-red-600 px-1.5 py-0.5 rounded font-medium border border-red-100">
                                                        Exp: {{ $detail->barangMasuk->tanggal_kadaluarsa ?? '-' }}
                                                    </span>
                                                </div>
                                            </td>
                                            <td class="text-center py-4">
                                                <span class="inline-flex items-center justify-center w-10 h-10 rounded-lg bg-white border text-sm font-bold text-gray-700 shadow-sm">
                                                    {{ $detail->barangMasuk->jumlah ?? 0 }}
                                                </span>
                                            </td>
                                            <td class="text-right py-4 px-2 font-bold text-gray-900">
                                                Rp {{ number_format($detail->total ?? 0, 0, ',', '.') }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr class="bg-gray-100/50">
                                        <td colspan="2" class="text-right py-3 px-4 text-xs font-bold text-gray-500 uppercase">Grand Total</td>
                                        <td class="text-right py-3 px-2 font-black text-gray-900 text-base">
                                            Rp {{ number_format($hutang->total, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>

            @empty
                <div class="bg-white border-2 border-dashed border-gray-200 rounded-3xl py-16 px-4 text-center">
                    <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-file-invoice-dollar text-3xl text-gray-300"></i>
                    </div>
                    <h4 class="text-lg font-bold text-gray-800">Tidak Ada Data Hutang</h4>
                    <p class="text-sm text-gray-500 max-w-xs mx-auto mt-2">Semua hutang telah dilunasi atau tidak ada data yang sesuai dengan pencarian Anda.</p>
                </div>
            @endforelse

        </div>

        {{-- PAGINATION --}}
        <div class="mt-10">
            {{ $hutangList->withQueryString()->links() }}
        </div>

    </div>
</div>

{{-- Tambahkan script x-collapse jika belum ada di app.js untuk animasi smooth --}}
<style>
    [x-cloak] { display: none !important; }
    
    /* Customizing Pagination Style */
    .pagination { @apply flex gap-2; }
    .page-item { @apply rounded-xl overflow-hidden border-0; }
    .page-link { @apply bg-white text-gray-600 px-4 py-2 border-0 shadow-sm hover:bg-yellow-500 hover:text-white transition-all font-bold text-sm; }
    .active .page-link { @apply bg-yellow-500 text-white shadow-yellow-200 shadow-lg; }
</style>

@endsection