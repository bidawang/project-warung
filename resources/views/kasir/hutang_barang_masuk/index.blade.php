@extends('layouts.app')

@section('title', 'Data Hutang Barang Masuk')

@section('content')

<div class="max-w-7xl mx-auto px-4 py-6">

    {{-- HEADER --}}
    <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4 mb-6">
        <div>
            <h3 class="text-2xl font-bold text-gray-800">Manajemen Hutang</h3>
            <p class="text-sm text-gray-500">Nota hutang per pengiriman stok barang.</p>
        </div>

        <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4">
            <small class="block text-yellow-700 font-semibold">Total Hutang Aktif</small>
            <h4 class="text-xl font-black text-yellow-600">
                Rp {{ number_format($totalHutang, 0, ',', '.') }}
            </h4>
        </div>
    </div>

    {{-- FILTER --}}
    <div class="bg-white shadow-sm rounded-xl border p-4 mb-6">
        <form method="GET" class="flex gap-2">
            <input 
                type="text" 
                name="q" 
                value="{{ request('q') }}"
                placeholder="Cari ID Nota..."
                class="w-full border border-gray-300 rounded-full px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400"
            >

            <button class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 rounded-full">
                <i class="fas fa-search"></i>
            </button>
        </form>
    </div>

    <h5 class="font-bold text-gray-700 mb-4">Histori Hutang</h5>

    {{-- ACCORDION --}}
    <div class="space-y-4" x-data="{ open: null }">

        @forelse($hutangList as $hutang)

            @php
                $collapseId = 'hutang' . $hutang->id;
            @endphp

            <div class="bg-white rounded-xl shadow-sm border overflow-hidden">

                {{-- HEADER --}}
                <button 
                    @click="open === '{{ $collapseId }}' ? open = null : open = '{{ $collapseId }}'"
                    class="w-full text-left px-4 py-4 flex flex-col md:flex-row md:items-center md:justify-between hover:bg-gray-50 transition"
                >
                    <div class="grid grid-cols-1 md:grid-cols-3 w-full gap-3">

                        <div>
                            <div class="text-xs text-gray-500">Nota</div>
                            <div class="font-semibold text-gray-800">
                                #HTG-{{ str_pad($hutang->id, 5, '0', STR_PAD_LEFT) }}
                            </div>
                        </div>

                        <div>
                            <div class="text-xs text-gray-500">Tanggal</div>
                            <div class="font-semibold text-gray-800">
                                {{ $hutang->created_at->translatedFormat('d M Y') }}
                            </div>
                        </div>

                        <div class="md:text-right">
                            <div class="text-xs text-gray-500">Total</div>
                            <div class="font-bold text-yellow-600">
                                Rp {{ number_format($hutang->total, 0, ',', '.') }}
                            </div>
                        </div>

                    </div>
                </button>

                {{-- BODY --}}
                <div x-show="open === '{{ $collapseId }}'" x-transition class="bg-gray-50 px-4 pb-4">

                    <div class="overflow-x-auto">
                        <table class="w-full text-sm mt-2">
                            <thead class="text-xs text-gray-500 uppercase border-b">
                                <tr>
                                    <th class="text-left py-2">Barang</th>
                                    <th class="text-center py-2">Qty</th>
                                    <th class="text-right py-2">Subtotal</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach ($hutang->hutangBarangMasuk as $detail)
                                    <tr class="border-b last:border-0">
                                        <td class="py-2">
                                            <div class="font-semibold text-gray-800">
                                                {{ $detail->barangMasuk->transaksiBarang->barang->nama_barang ?? '-' }}
                                            </div>
                                            <div class="text-xs text-gray-500">
                                                Exp: {{ $detail->barangMasuk->tanggal_kadaluarsa ?? '-' }}
                                            </div>
                                        </td>

                                        <td class="text-center">
                                            {{ $detail->barangMasuk->jumlah ?? 0 }}
                                        </td>

                                        <td class="text-right font-semibold">
                                            Rp {{ number_format($detail->total ?? 0, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>

                            <tfoot>
                                <tr class="border-t">
                                    <td colspan="2" class="text-right text-xs text-gray-500 py-2">
                                        Total
                                    </td>
                                    <td class="text-right font-bold py-2">
                                        Rp {{ number_format($hutang->total, 0, ',', '.') }}
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                </div>
            </div>

        @empty
            <div class="text-center py-10 bg-white rounded-xl shadow-sm border">
                <i class="fas fa-file-invoice-dollar text-4xl text-gray-300 mb-3"></i>
                <p class="text-gray-500">Tidak ada data hutang</p>
            </div>
        @endforelse

    </div>

    {{-- PAGINATION --}}
    <div class="mt-6">
        {{ $hutangList->withQueryString()->links() }}
    </div>

</div>

@endsection