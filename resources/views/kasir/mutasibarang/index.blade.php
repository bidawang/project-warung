@extends('layouts.app')

@section('title', 'Manajemen Mutasi')

@section('content')

<div class="min-h-screen bg-gray-50/50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8" x-data="{ tab: 'masuk', open: null, selected: [] }">

        {{-- HEADER SECTION --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-8">
            <div>
                <h3 class="text-3xl font-extrabold text-gray-900 tracking-tight flex items-center gap-3">
                    <div class="bg-yellow-500 p-2 rounded-lg shadow-sm">
                        <i class="fas fa-exchange-alt text-white text-xl"></i>
                    </div>
                    Manajemen Mutasi
                </h3>
                <p class="text-gray-500 mt-1">Kelola perpindahan stok antar cabang warung Anda.</p>
            </div>

            <a href="{{ route('mutasibarang.create') }}"
               class="inline-flex items-center justify-center bg-gray-900 hover:bg-black text-white px-6 py-3 rounded-xl font-bold transition duration-200 shadow-lg shadow-gray-200 gap-2">
                <i class="fas fa-plus text-xs"></i>
                Buat Mutasi Baru
            </a>
        </div>

        {{-- CUSTOM TABS --}}
        <div class="bg-white p-1.5 rounded-2xl shadow-sm border border-gray-100 inline-flex mb-8 w-full md:w-auto">
            <button @click="tab='masuk'"
                :class="tab==='masuk' ? 'bg-yellow-500 text-white shadow-md' : 'text-gray-500 hover:bg-gray-50'"
                class="flex-1 md:flex-none px-8 py-2.5 rounded-xl font-bold transition-all duration-300 flex items-center justify-center gap-2">
                <i class="fas fa-download text-sm"></i>
                Penerimaan
                @if($mutasiMasuk->where('status','pending')->count() > 0)
                    <span class="flex h-5 w-5 items-center justify-center rounded-full bg-red-500 text-[10px] text-white animate-pulse">
                        {{ $mutasiMasuk->where('status','pending')->count() }}
                    </span>
                @endif
            </button>

            <button @click="tab='keluar'"
                :class="tab==='keluar' ? 'bg-yellow-500 text-white shadow-md' : 'text-gray-500 hover:bg-gray-50'"
                class="flex-1 md:flex-none px-8 py-2.5 rounded-xl font-bold transition-all duration-300 flex items-center justify-center gap-2">
                <i class="fas fa-upload text-sm"></i>
                Pengiriman
            </button>
        </div>

        {{-- ================= TAB: MASUK ================= --}}
        <div x-show="tab==='masuk'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4">
            
            <form action="{{ route('kasir.mutasibarang.konfirmasi-masal') }}" method="POST">
                @csrf
                @method('PATCH')

                <div class="grid gap-4">
                    @forelse($mutasiMasuk as $row)
                    <div class="group bg-white border border-gray-100 rounded-2xl shadow-sm hover:shadow-md transition-all p-5 flex flex-col md:flex-row md:items-center gap-6">
                        
                        {{-- Checkbox --}}
                        <div class="flex items-center">
                            @if($row->status === 'pending')
                                <div class="relative flex items-center">
                                    <input type="checkbox" name="ids[]" value="{{ $row->id }}" x-model="selected"
                                           class="w-6 h-6 rounded-lg border-gray-300 text-yellow-500 focus:ring-yellow-400 cursor-pointer">
                                </div>
                            @else
                                <div class="w-6 h-6 flex items-center justify-center rounded-lg bg-green-50 text-green-500">
                                    <i class="fas fa-check-circle text-sm"></i>
                                </div>
                            @endif
                        </div>

                        {{-- Info Grid --}}
                        <div class="flex-1 grid grid-cols-2 lg:grid-cols-4 gap-4">
                            <div>
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Nama Barang</p>
                                <p class="font-bold text-gray-800 leading-tight">{{ $row->stokWarung->barang->nama_barang ?? '-' }}</p>
                            </div>

                            <div>
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Asal Warung</p>
                                <p class="font-semibold text-gray-600 flex items-center gap-2">
                                    <i class="fas fa-store text-[10px] text-gray-400"></i>
                                    {{ $row->warungAsal->nama_warung ?? '-' }}
                                </p>
                            </div>

                            <div>
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Kuantitas</p>
                                <div class="flex items-center gap-1.5">
                                    <span class="text-lg font-black text-green-600">+{{ $row->jumlah }}</span>
                                    <span class="text-xs text-gray-400">Pcs</span>
                                </div>
                            </div>

                            <div>
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Status Konfirmasi</p>
                                @if($row->status === 'pending')
                                    <span class="inline-flex items-center gap-1.5 py-1 px-3 rounded-full bg-yellow-50 text-yellow-700 text-xs font-bold ring-1 ring-inset ring-yellow-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-yellow-500 animate-pulse"></span> Pending
                                    </span>
                                @elseif($row->status === 'terima')
                                    <span class="inline-flex items-center gap-1.5 py-1 px-3 rounded-full bg-green-50 text-green-700 text-xs font-bold ring-1 ring-inset ring-green-200">
                                        Diterima
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 py-1 px-3 rounded-full bg-red-50 text-red-700 text-xs font-bold ring-1 ring-inset ring-red-200">
                                        Ditolak
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                    @empty
                        <div class="text-center py-20 bg-white border-2 border-dashed border-gray-200 rounded-3xl">
                            <i class="fas fa-inbox text-4xl text-gray-200 mb-4"></i>
                            <p class="text-gray-500 font-medium">Kotak masuk mutasi kosong.</p>
                        </div>
                    @endforelse
                </div>

                {{-- Action Bar (Floating Style) --}}
                <div x-show="selected.length > 0" 
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="translate-y-full"
                     class="fixed bottom-8 left-1/2 -translate-x-1/2 bg-gray-900 text-white px-6 py-4 rounded-2xl shadow-2xl z-50 flex items-center gap-6 border border-gray-700 w-[90%] md:w-auto">
                    
                    <span class="text-sm font-medium whitespace-nowrap">
                        <span x-text="selected.length" class="bg-yellow-500 text-black px-2 py-0.5 rounded font-bold mr-2 text-xs"></span>
                        Item dipilih
                    </span>

                    <div class="h-8 w-px bg-gray-700"></div>

                    <div class="flex gap-2">
                        <button type="submit" name="action" value="terima"
                            class="bg-green-500 hover:bg-green-600 text-white px-5 py-2 rounded-xl text-sm font-bold transition shadow-sm">
                            Konfirmasi Terima
                        </button>
                        <button type="submit" name="action" value="tolak"
                            class="bg-red-500 hover:bg-red-600 text-white px-5 py-2 rounded-xl text-sm font-bold transition shadow-sm">
                            Tolak
                        </button>
                    </div>
                </div>
            </form>
        </div>

        {{-- ================= TAB: KELUAR ================= --}}
        <div x-show="tab==='keluar'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4">
            <div class="space-y-4">
                @forelse($mutasiKeluarGrouped as $warungTujuanId => $mutations)
                @php $id = 'keluar'.$warungTujuanId; @endphp

                <div class="bg-white border border-gray-100 rounded-2xl shadow-sm overflow-hidden transition-all"
                     :class="open === '{{ $id }}' ? 'ring-2 ring-yellow-400 border-transparent shadow-xl' : ''">

                    {{-- HEADER --}}
                    <button @click="open === '{{ $id }}' ? open=null : open='{{ $id }}'"
                        class="w-full px-6 py-5 text-left flex items-center justify-between hover:bg-gray-50 transition-colors">
                        
                        <div class="flex items-center gap-4">
                            <div class="bg-blue-50 p-3 rounded-xl">
                                <i class="fas fa-truck text-blue-500"></i>
                            </div>
                            <div>
                                <h4 class="font-bold text-gray-900">Tujuan: {{ $mutations->first()->warungTujuan->nama_warung ?? '-' }}</h4>
                                <p class="text-xs text-gray-500">Dikirim pada {{ $mutations->first()->created_at->translatedFormat('d M Y, H:i') }}</p>
                            </div>
                        </div>

                        <div class="flex items-center gap-4">
                            <span class="text-xs font-bold bg-gray-100 text-gray-600 px-3 py-1.5 rounded-full uppercase tracking-tighter">
                                {{ $mutations->count() }} Produk
                            </span>
                            <i class="fas fa-chevron-down text-gray-300 transition-transform duration-300" :class="open === '{{ $id }}' ? 'rotate-180 text-yellow-500' : ''"></i>
                        </div>
                    </button>

                    {{-- DETAIL ITEMS --}}
                    <div x-show="open==='{{ $id }}'" x-collapse>
                        <div class="px-6 pb-6 pt-2">
                            <div class="bg-gray-50 rounded-2xl border border-gray-100 p-2 overflow-hidden">
                                <table class="w-full text-sm">
                                    <thead class="text-[10px] font-bold text-gray-400 uppercase border-b border-gray-200">
                                        <tr>
                                            <th class="text-left py-3 px-4">Nama Barang</th>
                                            <th class="text-center py-3">Jumlah</th>
                                            <th class="text-right py-3 px-4">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100">
                                        @foreach($mutations as $m)
                                        <tr class="hover:bg-white transition-colors">
                                            <td class="py-4 px-4 font-bold text-gray-800">{{ $m->stokWarung->barang->nama_barang ?? '-' }}</td>
                                            <td class="py-4 text-center">
                                                <span class="bg-white border px-3 py-1 rounded-lg font-black text-red-500 shadow-sm">
                                                    -{{ $m->jumlah }}
                                                </span>
                                            </td>
                                            <td class="py-4 px-4 text-right">
                                                @if($m->status === 'pending')
                                                    <span class="text-yellow-600 font-bold text-xs"><i class="fas fa-clock mr-1"></i> Diproses</span>
                                                @elseif($m->status === 'terima')
                                                    <span class="text-green-600 font-bold text-xs"><i class="fas fa-check-circle mr-1"></i> Terkirim</span>
                                                @else
                                                    <span class="text-red-600 font-bold text-xs"><i class="fas fa-times-circle mr-1"></i> Ditolak</span>
                                                @endif
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                    <div class="text-center py-20 bg-white border-2 border-dashed border-gray-200 rounded-3xl">
                        <i class="fas fa-paper-plane text-4xl text-gray-200 mb-4"></i>
                        <p class="text-gray-500 font-medium">Belum ada riwayat pengiriman.</p>
                    </div>
                @endforelse
            </div>
        </div>

    </div>
</div>

<style>
    /* Custom Scrollbar for nicer feel */
    ::-webkit-scrollbar { width: 8px; }
    ::-webkit-scrollbar-thumb { background: #e5e7eb; border-radius: 10px; }
    ::-webkit-scrollbar-thumb:hover { background: #d1d5db; }
</style>

@endsection