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
                    <div
                        class="inline-flex items-center gap-2 bg-blue-50 text-blue-700 px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wide">
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
                <div
                    class="h-11 w-11 flex-shrink-0 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white text-lg font-black shadow-md">
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
        <div
            class="bg-gradient-to-br from-indigo-700 via-blue-700 to-blue-900 rounded-2xl shadow-lg p-5 text-white h-full relative overflow-hidden">
            <div class="absolute -top-20 -right-20 w-48 h-48 bg-white/10 rounded-full blur-3xl"></div>

            <div class="relative z-10 flex items-center justify-between">
                <div>
                    <p class="text-[10px] uppercase tracking-[0.2em] text-blue-200">Ringkasan Keuangan</p>
                    <h3 class="text-xl font-black mt-1">Laporan Laba</h3>
                </div>
                <div class="bg-white/10 border border-white/10 px-3 py-1.5 rounded-xl backdrop-blur">
                    <p class="text-[9px] uppercase text-blue-100">Periode</p>
                    <p class="text-xs font-bold">{{ $periode }}</p>
                </div>
            </div>

            {{-- RINGKASAN BARANG & PULSA --}}
            <div class="mt-4 grid grid-cols-1 lg:grid-cols-2 gap-4 relative z-10">

                {{-- ====================================================== --}}
                {{-- BARANG --}}
                {{-- ====================================================== --}}
                <div class="bg-white/10 border border-white/10 rounded-2xl p-4 backdrop-blur">

                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <p class="text-[10px] uppercase tracking-widest text-blue-100">
                                Statistik Barang
                            </p>

                            <h3 class="text-xl font-black text-white mt-1">
                                Penjualan Barang
                            </h3>
                        </div>

                        <div class="w-12 h-12 rounded-2xl bg-orange-400/20 flex items-center justify-center">
                            <i class="fas fa-box text-orange-300 text-lg"></i>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">

                        {{-- LABA --}}
                        <div class="bg-green-500/10 rounded-xl p-3 border border-green-400/10">
                            <p class="text-[9px] uppercase text-green-100">
                                Omset Cash
                            </p>

                            <h4 class="text-lg font-black mt-1 text-green-300">
                                Rp {{ number_format($totalPenjualanCashBarang, 0, ',', '.') }}

                            </h4>
                        </div>

                        {{-- OMSET --}}
                        <div class="bg-blue-500/10 rounded-xl p-3 border border-blue-400/10">
                            <p class="text-[9px] uppercase text-blue-100">
                                Laba Cash
                            </p>

                            <h4 class="text-lg font-black mt-1 text-blue-300">
                                Rp {{ number_format($totalLabaCashBarang, 0, ',', '.') }}
                            </h4>
                        </div>

                        {{-- HUTANG --}}
                        <div class="bg-yellow-500/10 rounded-xl p-3 border border-yellow-400/10">
                            <p class="text-[9px] uppercase text-yellow-100">
                                Hutang Barang
                            </p>

                            <h4 class="text-lg font-black mt-1 text-yellow-300">
                                Rp {{ number_format($totalPenjualanHutangBarang, 0, ',', '.') }}
                            </h4>
                        </div>

                        {{-- LABA HUTANG --}}
                        <div class="bg-pink-500/10 rounded-xl p-3 border border-pink-400/10">
                            <p class="text-[9px] uppercase text-pink-100">
                                Laba Hutang
                            </p>

                            <h4 class="text-lg font-black mt-1 text-pink-300">
                                Rp {{ number_format($totalLabaHutangBarang, 0, ',', '.') }}
                            </h4>
                        </div>

                    </div>
                </div>

                {{-- ====================================================== --}}
                {{-- PULSA --}}
                {{-- ====================================================== --}}
                <div class="bg-white/10 border border-white/10 rounded-2xl p-4 backdrop-blur">

                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <p class="text-[10px] uppercase tracking-widest text-cyan-100">
                                Statistik Pulsa
                            </p>

                            <h3 class="text-xl font-black text-white mt-1">
                                Penjualan Pulsa
                            </h3>
                        </div>

                        <div class="w-12 h-12 rounded-2xl bg-cyan-400/20 flex items-center justify-center">
                            <i class="fas fa-mobile-screen text-cyan-300 text-lg"></i>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">

                        {{-- OMSET CASH --}}
                        <div class="bg-cyan-500/10 rounded-xl p-3 border border-cyan-400/10">
                            <p class="text-[9px] uppercase text-sky-100">
                                Omset Cash
                            </p>

                            <h4 class="text-lg font-black mt-1 text-sky-300">
                                Rp {{ number_format($totalPenjualanCashPulsa, 0, ',', '.') }}
                            </h4>
                        </div>

                        {{-- LABA CASH --}}
                        <div class="bg-sky-500/10 rounded-xl p-3 border border-sky-400/10">

                            <p class="text-[9px] uppercase text-cyan-100">
                                Laba Cash
                            </p>

                            <h4 class="text-lg font-black mt-1 text-cyan-300">
                                Rp {{ number_format($totalLabaCashPulsa, 0, ',', '.') }}
                            </h4>
                        </div>

                        {{-- HUTANG PULSA --}}
                        <div class="bg-indigo-500/10 rounded-xl p-3 border border-indigo-400/10">
                            <p class="text-[9px] uppercase text-indigo-100">
                                Omset Hutang
                            </p>

                            <h4 class="text-lg font-black mt-1 text-indigo-300">
                                Rp {{ number_format($totalPenjualanHutangPulsa, 0, ',', '.') }}
                            </h4>
                        </div>

                        {{-- LABA HUTANG --}}
                        <div class="bg-purple-500/10 rounded-xl p-3 border border-purple-400/10">
                            <p class="text-[9px] uppercase text-purple-100">
                                Laba Hutang
                            </p>

                            <h4 class="text-lg font-black mt-1 text-purple-300">
                                Rp {{ number_format($totalLabaHutangPulsa, 0, ',', '.') }}
                            </h4>
                        </div>

                        {{-- ADJUSTMENT CASH --}}
                        <div class="bg-amber-500/10 rounded-xl p-3 border border-amber-400/10">
                            <p class="text-[9px] uppercase text-amber-100">
                               Laba Adjustment Cash
                            </p>

                            <h4 class="text-lg font-black mt-1 text-amber-300">
                                Rp {{ number_format($totalAdjustmentCashPulsa, 0, ',', '.') }}
                            </h4>
                        </div>

                        {{-- ADJUSTMENT HUTANG --}}
                        <div class="bg-orange-500/10 rounded-xl p-3 border border-orange-400/10">
                            <p class="text-[9px] uppercase text-orange-100">
                                Laba Adjustment Hutang
                            </p>

                            <h4 class="text-lg font-black mt-1 text-orange-300">
                                Rp {{ number_format($totalAdjustmentHutangPulsa, 0, ',', '.') }}
                            </h4>
                        </div>

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
                                $persen =
                                    $asset->harga_asset > 0 ? ($asset->total_dibayar / $asset->harga_asset) * 100 : 0;
                            @endphp
                            <tr class="hover:bg-blue-50/40 transition">
                                <td class="px-3 py-3">
                                    <p class="font-bold text-gray-800 leading-none text-xs">{{ $asset->nama }}</p>
                                    <p class="text-[9px] text-gray-400 mt-1">
                                        {{ \Carbon\Carbon::parse($asset->tanggal_pembelian)->format('d M y') }}</p>
                                </td>
                                <td class="px-3 py-3">
                                    <p class="font-bold text-gray-800 text-xs">Rp
                                        {{ number_format($asset->harga_asset, 0, ',', '.') }}</p>
                                    <p class="text-[9px] text-green-600">B: Rp
                                        {{ number_format($asset->total_dibayar, 0, ',', '.') }}</p>
                                </td>
                                <td class="px-3 py-3 w-[140px]">
                                    <div class="flex justify-between text-[9px] text-gray-500 mb-0.5">
                                        <span>{{ $asset->volume_pelunasan }}x</span>
                                        <span>{{ number_format($persen, 0) }}%</span>
                                    </div>
                                    <div class="w-full bg-gray-100 rounded-full h-1.5">
                                        <div class="bg-blue-600 h-1.5 rounded-full"
                                            style="width: {{ $persen }}%"></div>
                                    </div>
                                    <p class="text-[9px] text-red-500 mt-1">S: Rp
                                        {{ number_format($asset->sisa_pembayaran, 0, ',', '.') }}</p>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center py-10 text-gray-400 italic text-xs">Belum ada
                                    data
                                    asset</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
