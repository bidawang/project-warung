@extends('layouts.admin')

@section('title', 'Riwayat Pengiriman Stok')

@section('content')
    <div class="container mx-auto px-4 py-8" x-data="{ tab: 'global' }">

        {{-- Header --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">Riwayat Pengiriman</h1>
                <p class="text-gray-500 mt-1">Pantau distribusi barang berdasarkan periode dan lokasi warung.</p>
            </div>
            {{-- Toggle Tampilan --}}
            <div class="flex bg-white border p-1 rounded-xl shadow-sm">
                <button @click="tab = 'global'" :class="tab === 'global' ? 'bg-indigo-600 text-white' : 'text-gray-500'"
                    class="px-4 py-2 rounded-lg text-sm font-bold transition-all">
                    Riwayat Global
                </button>
                <button @click="tab = 'warung'" :class="tab === 'warung' ? 'bg-indigo-600 text-white' : 'text-gray-500'"
                    class="px-4 py-2 rounded-lg text-sm font-bold transition-all">
                    Per Warung
                </button>
            </div>
        </div>

        {{-- ================= FILTER SECTION (SERVER SIDE) ================= --}}
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 mb-6">
            <form action="{{ route('admin.transaksibarang.riwayat') }}" method="GET">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                    {{-- Filter Tanggal --}}
                    <div>
                        <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1">Dari Tanggal</label>
                        <input type="date" name="start_date" value="{{ request('start_date') }}"
                            class="w-full border border-gray-200 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1">Sampai Tanggal</label>
                        <input type="date" name="end_date" value="{{ request('end_date') }}"
                            class="w-full border border-gray-200 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <div class="flex gap-2">
                        <button type="submit"
                            class="flex-1 bg-gray-800 text-white px-6 py-2 rounded-lg text-sm font-bold hover:bg-black transition-colors">
                            Terapkan Filter
                        </button>
                        <a href="{{ route('admin.transaksibarang.riwayat') }}"
                            class="bg-gray-100 text-gray-600 px-4 py-2 rounded-lg text-sm font-bold hover:bg-gray-200">
                            Reset
                        </a>
                    </div>
                </div>

                {{-- Tab Quick Filter Status --}}
                <div class="flex bg-gray-100 p-1 rounded-lg w-full md:w-max mt-6">
                    @foreach (['semua', 'dikirim', 'terima', 'tolak'] as $s)
                        <a href="{{ request()->fullUrlWithQuery(['status' => $s]) }}"
                            class="px-6 py-2 text-xs font-bold text-center rounded-md transition-all {{ $status === $s ? 'bg-white text-indigo-600 shadow-sm' : 'text-gray-500 hover:text-gray-700' }}">
                            {{ ucfirst($s === 'dikirim' ? 'Dikirim' : ($s === 'semua' ? 'Semua' : $s)) }}
                        </a>
                    @endforeach
                </div>
            </form>
        </div>

        {{-- ================= TAMPILAN 1: RIWAYAT GLOBAL (TABEL) ================= --}}
        <div x-show="tab === 'global'" x-transition>
            <div class="bg-white shadow-xl rounded-2xl overflow-hidden border border-gray-100">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50 text-gray-500 text-xs font-bold uppercase tracking-wider">
                        <tr>
                            <th class="px-6 py-4 text-left">Waktu & ID</th>
                            <th class="px-6 py-4 text-left">Barang</th>
                            <th class="px-6 py-4 text-left">Penyebaran</th>
                            <th class="px-6 py-4 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($transaksibarangs as $trx)
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-bold text-gray-800">{{ $trx->created_at->format('d/m/Y H:i') }}</div>
                                    <div class="text-[10px] text-gray-400 font-mono italic">#{{ $trx->id }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-bold text-gray-800">{{ $trx->barang->nama_barang ?? 'N/A' }}</div>
                                    <div class="text-xs text-gray-500 italic">Total: {{ $trx->jumlah }} unit</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-wrap gap-1">
                                        @foreach ($trx->detailTransaksiBarangMasuk as $det)
                                            <span class="text-[10px] bg-indigo-50 text-indigo-700 px-2 py-0.5 rounded border border-indigo-100 font-medium">
                                                {{ $det->stokwarung->warung->nama_warung ?? 'N/A' }} ({{ $det->jumlah }})
                                            </span>
                                        @endforeach
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @php
                                        $badge = [
                                            'dikirim' => 'bg-blue-100 text-blue-700 border-blue-200',
                                            'terima' => 'bg-green-100 text-green-700 border-green-200',
                                            'tolak' => 'bg-red-100 text-red-700 border-red-200',
                                        ][$trx->status] ?? 'bg-gray-100 text-gray-700 border-gray-200';
                                    @endphp
                                    <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase border {{ $badge }}">
                                        {{ $trx->status === 'dikirim' ? 'Dalam Pengiriman' : ucfirst($trx->status) }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center text-gray-400 italic">Data tidak ditemukan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- ================= TAMPILAN 2: PER WARUNG (CARDS) ================= --}}
        <div x-show="tab === 'warung'" x-transition>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($warungs as $warung)
                    @php
                        $warungData = [];
                        $warungCount = 0;
                        
                        // Filter data untuk warung ini berdasarkan status
                        foreach($transaksibarangs as $trx) {
                            foreach($trx->detailTransaksiBarangMasuk as $item) {
                                if($item->stokwarung && $item->stokwarung->id_warung == $warung->id) {
                                    // Filter berdasarkan status
                                    $trxStatus = $trx->status;
                                    if($status === 'semua' || $status === $trxStatus) {
                                        $warungData[] = [
                                            'item' => $item,
                                            'trx' => $trx,
                                            'nama_barang' => $trx->barang->nama_barang ?? 'N/A'
                                        ];
                                        $warungCount++;
                                    }
                                }
                            }
                        }
                    @endphp

                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 flex flex-col h-[500px] overflow-hidden">
                        {{-- Header --}}
                        <div class="p-5 border-b bg-gray-50">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="font-bold text-gray-800 tracking-tight text-lg">{{ $warung->nama_warung }}</h3>
                                <span class="text-[9px] bg-white border px-2 py-1 rounded font-bold text-gray-400 uppercase italic">
                                    Status: {{ $status === 'semua' ? 'Semua' : ucfirst($status) }}
                                </span>
                            </div>

                            {{-- Search --}}
                            <div class="relative">
                                <input type="text" placeholder="Cari barang..." 
                                    class="w-full pl-9 pr-4 py-2 text-xs border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                    onkeyup="searchInCard(this)">
                                <svg class="w-4 h-4 absolute left-3 top-2.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                        </div>

                        {{-- Content --}}
                        <div class="flex-1 overflow-y-auto p-4 space-y-3 custom-scrollbar">
                            @forelse($warungData as $data)
                                <div class="flex justify-between items-start p-3 border border-gray-100 rounded-xl hover:shadow-md hover:border-gray-200 transition-all item-row bg-white"
                                    data-item-name="{{ strtolower($data['nama_barang']) }}">

                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-bold text-gray-800 truncate">
                                            {{ $data['nama_barang'] }}
                                        </p>

                                        <p class="text-[10px] text-gray-400 mt-1">
                                            {{ $data['trx']->created_at->format('d/m/Y H:i') }} • #{{ $data['item']->id }}
                                        </p>

                                        <span class="inline-block mt-1 px-2 py-0.5 rounded-full text-[9px] font-bold uppercase
                                            {{ $data['trx']->status === 'terima' ? 'bg-green-100 text-green-700' : 
                                               ($data['trx']->status === 'tolak' ? 'bg-red-100 text-red-700' : 
                                               'bg-blue-100 text-blue-700') }}">
                                            {{ $data['trx']->status === 'dikirim' ? 'Dalam Pengiriman' : ucfirst($data['trx']->status) }}
                                        </span>
                                    </div>

                                    <div class="bg-indigo-50 px-3 py-1.5 rounded-lg text-center border border-indigo-100 ml-3 flex-shrink-0">
                                        <span class="text-sm font-black text-indigo-700 block">
                                            {{ $data['item']->jumlah }}
                                        </span>
                                        <span class="text-[8px] text-indigo-400 font-bold uppercase">Unit</span>
                                    </div>
                                </div>
                            @empty
                                <div class="flex items-center justify-center h-full opacity-30">
                                    <div class="text-center">
                                        <svg class="w-12 h-12 mx-auto text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                        </svg>
                                        <p class="text-xs italic text-gray-400">Tidak ada data untuk warung ini</p>
                                    </div>
                                </div>
                            @endforelse
                        </div>

                        {{-- Footer --}}
                        <div class="p-4 bg-gray-50 border-t flex justify-between items-center text-[10px] font-bold">
                            <span class="text-gray-400 uppercase tracking-widest">Aktivitas {{ $status === 'semua' ? '' : ucfirst($status) }}</span>
                            <span class="text-indigo-600 bg-indigo-50 px-3 py-1.5 rounded-lg font-black">
                                {{ $warungCount }} {{ $warungCount === 1 ? 'Data' : 'Data' }}
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full text-center py-12 text-gray-400">
                        <p class="text-lg">Tidak ada warung ditemukan</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
    <style>
        .custom-scrollbar::-webkit-scrollbar {
            width: 4px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f1f5f9;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 10px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
    </style>

    <script>
        function searchInCard(input) {
            const filter = input.value.toLowerCase().trim();
            const card = input.closest('.bg-white');
            const rows = card.querySelectorAll('.item-row');

            let visibleCount = 0;
            
            rows.forEach(row => {
                const itemName = row.getAttribute('data-item-name') || '';
                const isVisible = itemName.includes(filter);
                
                row.style.display = isVisible ? "flex" : "none";
                
                if (isVisible) visibleCount++;
            });

            // Update footer count (optional enhancement)
            const footerCount = card.querySelector('.text-indigo-600');
            if (footerCount && filter === '') {
                // Reset to original count when search is cleared
            }
        }
    </script>
@endsection