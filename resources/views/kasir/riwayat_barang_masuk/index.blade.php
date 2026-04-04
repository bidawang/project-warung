@extends('layouts.app')

@section('title', 'Riwayat Barang Masuk')

@section('content')

<div class="max-w-7xl mx-auto px-4 py-6">

    {{-- HEADER --}}
    <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4 mb-6">
        <div>
            <h3 class="text-2xl font-bold text-gray-800">Riwayat Barang Masuk</h3>
            <p class="text-sm text-gray-500">Daftar transaksi barang yang masuk ke stok.</p>
        </div>

        <form method="GET" class="flex gap-2 w-full md:w-auto">
            <input 
                type="text"
                name="search"
                value="{{ request('search') }}"
                placeholder="Cari barang..."
                class="w-full md:w-64 border border-gray-300 rounded-full px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400"
            >

            <button class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 rounded-full text-sm">
                Cari
            </button>

            @if(request('search'))
                <a href="{{ route('kasir.riwayat-barang-masuk.index') }}"
                   class="border border-gray-300 px-4 rounded-full text-sm flex items-center hover:bg-gray-100">
                    Reset
                </a>
            @endif
        </form>
    </div>

    <h5 class="font-bold text-gray-700 mb-4">Daftar Transaksi</h5>

    {{-- LIST --}}
    <div class="space-y-4" x-data="{ open: null }">

        @forelse($riwayatBarangMasuk as $bm)

            @php
                $barang = optional($bm->stokWarung->barang);
                $id = 'bm'.$bm->id;
            @endphp

            <div class="bg-white rounded-xl shadow-sm border overflow-hidden">

                {{-- HEADER --}}
                <button 
                    @click="open === '{{ $id }}' ? open = null : open = '{{ $id }}'"
                    class="w-full text-left px-4 py-4 hover:bg-gray-50 transition"
                >
                    <div class="grid grid-cols-2 md:grid-cols-5 gap-3 items-center">

                        <div>
                            <div class="text-xs text-gray-500">Ref</div>
                            <div class="font-semibold text-gray-800">
                                BM-{{ $bm->id }}
                            </div>
                        </div>

                        <div>
                            <div class="text-xs text-gray-500">Tanggal</div>
                            <div class="font-semibold text-gray-800">
                                {{ $bm->created_at->format('d M Y') }}
                            </div>
                        </div>

                        <div>
                            <div class="text-xs text-gray-500">Waktu</div>
                            <div class="font-semibold text-gray-800">
                                {{ $bm->created_at->format('H:i') }}
                            </div>
                        </div>

                        <div>
                            <div class="text-xs text-gray-500">Barang</div>
                            <div class="font-semibold text-gray-800">
                                {{ $barang->nama_barang ?? '-' }}
                            </div>
                        </div>

                        <div class="text-right">
                            <div class="text-xs text-gray-500">Subtotal</div>
                            <div class="font-bold text-green-600">
                                Rp {{ number_format($bm->total,0,',','.') }}
                            </div>
                        </div>

                    </div>
                </button>

                {{-- DETAIL --}}
                <div x-show="open === '{{ $id }}'" x-transition class="bg-gray-50 px-4 pb-4">

                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4 text-sm mt-3">

                        <div>
                            <div class="text-gray-500 text-xs">Jenis</div>
                            <div class="font-semibold text-green-600">
                                {{ strtoupper($bm->jenis ?? 'MASUK') }}
                            </div>
                        </div>

                        <div>
                            <div class="text-gray-500 text-xs">Jumlah</div>
                            <div class="font-semibold">
                                {{ $bm->jumlah }}
                            </div>
                        </div>

                        <div>
                            <div class="text-gray-500 text-xs">Status</div>
                            <div class="font-semibold">
                                {{ strtoupper($bm->status ?? '-') }}
                            </div>
                        </div>

                        @if($bm->tanggal_kadaluarsa)
                        <div>
                            <div class="text-gray-500 text-xs">Kadaluarsa</div>
                            <div class="font-semibold">
                                {{ \Carbon\Carbon::parse($bm->tanggal_kadaluarsa)->format('d M Y') }}
                            </div>
                        </div>
                        @endif

                    </div>

                </div>

            </div>

        @empty
            <div class="text-center py-10 bg-white rounded-xl shadow-sm border">
                <p class="text-gray-500">Tidak ada data barang masuk</p>
            </div>
        @endforelse

    </div>

    {{-- PAGINATION --}}
    <div class="mt-6">
        {{ $riwayatBarangMasuk->links() }}
    </div>

</div>

@endsection