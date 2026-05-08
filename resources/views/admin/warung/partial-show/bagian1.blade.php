{{-- ===================================================== --}}
{{-- ROW 1 - COMPACT VERSION --}}
{{-- ===================================================== --}}
<div class="grid grid-cols-1 xl:grid-cols-12 gap-4 mb-4">

    {{-- ===================================================== --}}
    {{-- INFO WARUNG --}}
    {{-- ===================================================== --}}
    <div class="xl:col-span-3">
        <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-4 h-full">
            {{-- HEADER --}}
            <div class="flex items-start justify-between">
                <div>
                    <div class="inline-flex items-center gap-2 bg-blue-50 text-blue-700 px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wide">
                        Informasi Warung
                    </div>
                    <h2 class="text-xl font-black text-gray-900 mt-2 leading-tight">
                        {{ $warung->nama_warung }}
                    </h2>
                    <p class="text-xs text-gray-500">
                        {{ $warung->kategori ?? 'Warung Umum' }}
                    </p>
                </div>
                {{-- Icon lebih kecil (w-12 h-12) --}}
                <div class="h-11 w-11 flex-shrink-0 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white text-lg font-black shadow-md">
                    {{ substr($warung->nama_warung, 0, 1) }}
                </div>
            </div>

            {{-- INFO --}}
            <div class="grid grid-cols-2 gap-2 mt-4">
                <div class="bg-gray-50 rounded-xl p-3">
                    <p class="text-[9px] uppercase font-bold text-gray-400 mb-0.5">Pemilik</p>
                    <p class="font-semibold text-gray-800 text-xs truncate">
                        {{ $warung->user->name ?? '-' }}
                    </p>
                </div>
                <div class="bg-gray-50 rounded-xl p-3">
                    <p class="text-[9px] uppercase font-bold text-gray-400 mb-0.5">Area</p>
                    <p class="font-semibold text-gray-800 text-xs truncate">
                        {{ $warung->area->area ?? '-' }}
                    </p>
                </div>
            </div>

            {{-- MODAL --}}
            <div class="mt-3 bg-gradient-to-r from-gray-900 to-gray-800 rounded-xl p-4 text-white">
                <p class="text-[10px] uppercase tracking-widest text-gray-400">Modal Awal</p>
                <h3 class="text-2xl font-black mt-1">
                    Rp {{ number_format($warung->modal, 0, ',', '.') }}
                </h3>
                @php $laba = explode('|', $warung->pembagian_laba ?? '0|0'); @endphp
                <div class="flex items-center gap-2 mt-3">
                    <div class="flex overflow-hidden rounded-lg shadow text-[10px]">
                        <div class="bg-green-500 px-2 py-1 font-bold">{{ $laba[0] }}%</div>
                        <div class="bg-orange-500 px-2 py-1 font-bold">{{ $laba[1] ?? 0 }}%</div>
                    </div>
                    <p class="text-[10px] text-gray-400">Pgl • Own</p>
                </div>
            </div>

            {{-- NOTE --}}
            <div class="mt-3 border-t pt-3">
                <p class="text-xs italic text-gray-500 leading-snug line-clamp-2">
                    "{{ $warung->keterangan ?? 'Tidak ada keterangan tambahan.' }}"
                </p>
            </div>
        </div>
    </div>

    {{-- ===================================================== --}}
    {{-- RINGKASAN LABA --}}
    {{-- ===================================================== --}}
    <div class="xl:col-span-5">
        <div class="bg-gradient-to-br from-indigo-700 via-blue-700 to-blue-900 rounded-2xl shadow-lg p-5 text-white h-full relative overflow-hidden">
            <div class="absolute -top-20 -right-20 w-48 h-48 bg-white/10 rounded-full blur-3xl"></div>
            
            <div class="relative z-10 flex items-center justify-between">
                <div>
                    <p class="text-[10px] uppercase tracking-[0.2em] text-blue-200">Ringkasan Keuangan</p>
                    <h3 class="text-xl font-black mt-1">Laporan Laba</h3>
                </div>
                <div class="bg-white/10 border border-white/10 px-3 py-1.5 rounded-xl backdrop-blur">
                    <p class="text-[9px] uppercase text-blue-100">Periode</p>
                    <p class="text-xs font-bold">{{ $periode->translatedFormat('F Y') }}</p>
                </div>
            </div>

            {{-- LABA BESAR --}}
            <div class="mt-4 bg-white/10 border border-white/10 rounded-2xl p-4 backdrop-blur relative z-10">
                <p class="text-[10px] uppercase tracking-widest text-blue-100">Laba Bersih Real</p>
                <h2 class="text-3xl font-black text-green-300 mt-1">
                    Rp {{ number_format($totalLabaCash, 0, ',', '.') }}
                </h2>
                <div class="grid grid-cols-2 gap-3 mt-4">
                    <div class="bg-white/5 rounded-xl p-3 border border-white/5">
                        <p class="text-[9px] uppercase text-blue-100">Omset Cash</p>
                        <h4 class="text-lg font-black mt-0.5">Rp {{ number_format($totalPenjualanCash, 0, ',', '.') }}</h4>
                    </div>
                    <div class="bg-white/5 rounded-xl p-3 border border-white/5">
                        <p class="text-[9px] uppercase text-blue-100">Modal Cash</p>
                        <h4 class="text-lg font-black mt-0.5">Rp {{ number_format($totalModalCash, 0, ',', '.') }}</h4>
                    </div>
                </div>
            </div>

            {{-- HUTANG --}}
            <div class="mt-4 grid grid-cols-2 gap-3 relative z-10">
                <div class="bg-yellow-500/10 border border-yellow-400/10 rounded-xl p-4">
                    <div class="flex items-center justify-between gap-1">
                        <div>
                            <p class="text-xs font-bold uppercase">Hutang</p>
                            <p class="text-[9px] text-yellow-100">Belum bayar</p>
                        </div>
                        <span class="text-base">📒</span>
                    </div>
                    <div class="mt-3">
                        <p class="text-[9px] uppercase text-yellow-100">Total Penjualan</p>
                        <h3 class="text-lg font-black text-yellow-300">Rp {{ number_format($totalPenjualanHutang, 0, ',', '.') }}</h3>
                    </div>
                </div>

                <div class="bg-orange-500/10 border border-orange-400/10 rounded-xl p-4">
                    <div class="flex items-center justify-between gap-1">
                        <div>
                            <p class="text-xs font-bold uppercase">Laba Hutang</p>
                            <p class="text-[9px] text-orange-100">Belum realisasi</p>
                        </div>
                        <span class="text-base">💰</span>
                    </div>
                    <div class="mt-3">
                        <p class="text-[9px] uppercase text-orange-100">Potensi Laba</p>
                        <h3 class="text-lg font-black text-orange-300">Rp {{ number_format($totalLabaHutang, 0, ',', '.') }}</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ===================================================== --}}
    {{-- ASSET --}}
    {{-- ===================================================== --}}
    <div class="xl:col-span-4">
        <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden h-full flex flex-col">
            {{-- HEADER --}}
            <div class="p-4 border-b border-gray-100 flex items-center justify-between">
                <div>
                    <h3 class="text-base font-black text-gray-800">Asset Warung</h3>
                    <p class="text-[10px] text-gray-500">Inventaris & cicilan</p>
                </div>
                <div class="bg-blue-50 text-blue-700 px-2 py-0.5 rounded-lg text-[10px] font-bold">
                    {{ $assets->count() }} Asset
                </div>
            </div>

            {{-- TABLE --}}
            <div class="overflow-auto flex-1 max-h-[500px]">
                <table class="min-w-full text-xs">
                    <thead class="bg-gray-50 sticky top-0 z-10">
                        <tr class="text-[10px] uppercase tracking-wide text-gray-400">
                            <th class="px-3 py-2 text-left">Asset</th>
                            <th class="px-3 py-2 text-left">Harga</th>
                            <th class="px-3 py-2 text-left">Progress</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse ($assets as $asset)
                            @php
                                $persen = $asset->harga_asset > 0 ? ($asset->total_dibayar / $asset->harga_asset) * 100 : 0;
                            @endphp
                            <tr class="hover:bg-blue-50/40 transition">
                                <td class="px-3 py-3">
                                    <p class="font-bold text-gray-800 leading-none text-xs">{{ $asset->nama }}</p>
                                    <p class="text-[9px] text-gray-400 mt-1">{{ \Carbon\Carbon::parse($asset->tanggal_pembelian)->format('d M y') }}</p>
                                </td>
                                <td class="px-3 py-3">
                                    <p class="font-bold text-gray-800 text-xs">Rp {{ number_format($asset->harga_asset, 0, ',', '.') }}</p>
                                    <p class="text-[9px] text-green-600">B: Rp {{ number_format($asset->total_dibayar, 0, ',', '.') }}</p>
                                </td>
                                <td class="px-3 py-3 w-[140px]">
                                    <div class="flex justify-between text-[9px] text-gray-500 mb-0.5">
                                        <span>{{ $asset->volume_pelunasan }}x</span>
                                        <span>{{ number_format($persen, 0) }}%</span>
                                    </div>
                                    <div class="w-full bg-gray-100 rounded-full h-1.5">
                                        <div class="bg-blue-600 h-1.5 rounded-full" style="width: {{ $persen }}%"></div>
                                    </div>
                                    <p class="text-[9px] text-red-500 mt-1">S: Rp {{ number_format($asset->sisa_pembayaran, 0, ',', '.') }}</p>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center py-10 text-gray-400 italic text-xs">Belum ada data asset</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>