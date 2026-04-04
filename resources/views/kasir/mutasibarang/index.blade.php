@extends('layouts.app')

@section('title', 'Manajemen Mutasi')

@section('content')

<div class="max-w-7xl mx-auto px-4 py-6" 
     x-data="{ tab: 'masuk', open: null, selected: [] }">

    {{-- HEADER --}}
    <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4 mb-6">
        <h3 class="text-2xl font-bold text-gray-800">
            Manajemen Mutasi
        </h3>

        <a href="{{ route('mutasibarang.create') }}"
           class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-full text-sm">
            + Buat Mutasi
        </a>
    </div>

    {{-- TAB --}}
    <div class="flex border-b mb-6">
        <button @click="tab='masuk'"
            :class="tab==='masuk' ? 'border-yellow-500 text-yellow-600' : 'text-gray-500'"
            class="px-4 py-2 font-semibold border-b-2">
            Penerimaan
            @if($mutasiMasuk->where('status','pending')->count() > 0)
                <span class="ml-2 text-xs bg-red-500 text-white px-2 py-0.5 rounded-full">
                    {{ $mutasiMasuk->where('status','pending')->count() }}
                </span>
            @endif
        </button>

        <button @click="tab='keluar'"
            :class="tab==='keluar' ? 'border-yellow-500 text-yellow-600' : 'text-gray-500'"
            class="px-4 py-2 font-semibold border-b-2">
            Pengiriman
        </button>
    </div>

    {{-- ================= MASUK ================= --}}
    <div x-show="tab==='masuk'">

        <form action="{{ route('kasir.mutasibarang.konfirmasi-masal') }}" method="POST">
            @csrf
            @method('PATCH')

            <div class="space-y-3">

                @forelse($mutasiMasuk as $row)
                <div class="bg-white border rounded-xl shadow-sm p-4 flex flex-col md:flex-row md:items-center gap-3">

                    <div>
                        @if($row->status === 'pending')
                            <input type="checkbox"
                                name="ids[]"
                                value="{{ $row->id }}"
                                x-model="selected"
                                class="w-4 h-4">
                        @else
                            <span class="text-gray-400 text-xs">✔</span>
                        @endif
                    </div>

                    <div class="flex-1 grid grid-cols-2 md:grid-cols-4 gap-3">

                        <div>
                            <div class="text-xs text-gray-500">Barang</div>
                            <div class="font-semibold">
                                {{ $row->stokWarung->barang->nama_barang ?? '-' }}
                            </div>
                        </div>

                        <div>
                            <div class="text-xs text-gray-500">Asal</div>
                            <div class="font-semibold">
                                {{ $row->warungAsal->nama_warung ?? '-' }}
                            </div>
                        </div>

                        <div>
                            <div class="text-xs text-gray-500">Jumlah</div>
                            <div class="font-bold text-green-600">
                                +{{ $row->jumlah }}
                            </div>
                        </div>

                        <div>
                            <div class="text-xs text-gray-500">Status</div>
                            @if($row->status === 'pending')
                                <span class="text-yellow-600 text-sm font-semibold">Pending</span>
                            @elseif($row->status === 'terima')
                                <span class="text-green-600 text-sm font-semibold">Diterima</span>
                            @else
                                <span class="text-red-600 text-sm font-semibold">Ditolak</span>
                            @endif
                        </div>

                    </div>

                </div>
                @empty
                <div class="text-center py-10 bg-white border rounded-xl shadow-sm">
                    <p class="text-gray-500">Tidak ada mutasi masuk</p>
                </div>
                @endforelse

            </div>

            @if($mutasiMasuk->where('status','pending')->count() > 0)
            <div class="flex flex-wrap gap-3 items-center mt-6 bg-white border rounded-xl p-4 shadow-sm">
                <span class="text-sm text-gray-600 font-semibold">
                    Aksi untuk yang dipilih:
                </span>

                <button type="submit" name="action" value="terima"
                    class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg text-sm">
                    Terima
                </button>

                <button type="submit" name="action" value="tolak"
                    class="border border-red-500 text-red-500 px-4 py-2 rounded-lg text-sm hover:bg-red-50">
                    Tolak
                </button>
            </div>
            @endif

        </form>
    </div>

    {{-- ================= KELUAR ================= --}}
    <div x-show="tab==='keluar'" class="space-y-4">

        @forelse($mutasiKeluarGrouped as $warungTujuanId => $mutations)

        @php $id = 'keluar'.$warungTujuanId; @endphp

        <div class="bg-white border rounded-xl shadow-sm overflow-hidden">

            {{-- HEADER --}}
            <button @click="open === '{{ $id }}' ? open=null : open='{{ $id }}'"
                class="w-full px-4 py-3 text-left hover:bg-gray-50">

                <div class="flex justify-between items-center">
                    <div class="font-semibold text-gray-800">
                        Tujuan: {{ $mutations->first()->warungTujuan->nama_warung ?? '-' }}
                    </div>

                    <span class="text-sm bg-yellow-100 text-yellow-700 px-3 py-1 rounded-full">
                        {{ $mutations->count() }} item
                    </span>
                </div>

            </button>

            {{-- DETAIL LANGSUNG --}}
            <div x-show="open==='{{ $id }}'" x-transition class="bg-gray-50 px-4 pb-4">

                <div class="space-y-3 mt-3">

                    @foreach($mutations as $m)
                    <div class="bg-white border rounded-lg p-4 grid grid-cols-2 md:grid-cols-4 gap-3">

                        <div>
                            <div class="text-xs text-gray-500">Barang</div>
                            <div class="font-semibold">
                                {{ $m->stokWarung->barang->nama_barang ?? '-' }}
                            </div>
                        </div>

                        <div>
                            <div class="text-xs text-gray-500">Dari</div>
                            <div class="font-semibold">
                                {{ $m->warungAsal->nama_warung ?? '-' }}
                            </div>
                        </div>

                        <div>
                            <div class="text-xs text-gray-500">Jumlah</div>
                            <div class="font-bold text-red-600">
                                -{{ $m->jumlah }}
                            </div>
                        </div>

                        <div>
                            <div class="text-xs text-gray-500">Status</div>
                            @if($m->status === 'pending')
                                <span class="text-yellow-600 text-sm font-semibold">Proses</span>
                            @elseif($m->status === 'terima')
                                <span class="text-green-600 text-sm font-semibold">Terkirim</span>
                            @else
                                <span class="text-red-600 text-sm font-semibold">Ditolak</span>
                            @endif
                        </div>

                        {{-- <div class="col-span-2 md:col-span-4">
                            <div class="text-xs text-gray-500">Keterangan</div>
                            <div class="text-sm">
                                {{ $m->keterangan ?: '-' }}
                            </div>
                        </div> --}}

                    </div>
                    @endforeach

                </div>

            </div>

        </div>

        @empty
        <div class="text-center py-10 bg-white border rounded-xl shadow-sm">
            <p class="text-gray-500">Belum ada riwayat pengiriman</p>
        </div>
        @endforelse

    </div>

</div>

@endsection