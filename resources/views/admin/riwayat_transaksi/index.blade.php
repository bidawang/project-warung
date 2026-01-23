@extends('layouts.admin')

@section('title', 'Pemantauan Transaksi Global')

@section('content')

<div class="p-6 bg-gray-100 min-h-screen"
     x-data="{
        showStruk:false,
        trx:null
     }">

    {{-- HEADER --}}
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-black text-gray-800">
            <i class="fas fa-chart-line text-indigo-600 mr-2"></i>
            Pemantauan Transaksi Global
        </h1>
    </div>

    {{-- FILTER --}}
    <div class="bg-white rounded-xl shadow-sm p-4 mb-6">
        <form method="GET" class="flex flex-wrap gap-4 items-end">
            <div>
                <label class="text-xs font-bold text-gray-500">Dari</label>
                <input type="date" name="start_date" value="{{ request('start_date') }}"
                       class="border rounded-lg px-3 py-2 text-sm">
            </div>
            <div>
                <label class="text-xs font-bold text-gray-500">Sampai</label>
                <input type="date" name="end_date" value="{{ request('end_date') }}"
                       class="border rounded-lg px-3 py-2 text-sm">
            </div>
            <button class="bg-indigo-600 text-white px-5 py-2 rounded-lg font-bold">
                Filter
            </button>
            <a href="{{ route('admin.riwayat_transaksi.index') }}"
               class="px-4 py-2 border rounded-lg text-gray-600">
                Reset
            </a>
        </form>
    </div>

    {{-- TABEL MONITORING --}}
    <div class="bg-white rounded-xl shadow overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-100 text-gray-600 uppercase text-xs">
                <tr>
                    <th class="px-4 py-3 text-left">Waktu</th>
                    <th class="px-4 py-3 text-left">Warung</th>
                    <th class="px-4 py-3 text-left">Jenis</th>
                    <th class="px-4 py-3 text-left">Deskripsi</th>
                    <th class="px-4 py-3 text-right">Nominal</th>
                    <th class="px-4 py-3 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @foreach($dataTransaksiPerWarung as $warung)
                    @foreach($warung['riwayat_transaksi'] as $trx)
                        <tr class="hover:bg-indigo-50">
                            <td class="px-4 py-3 text-xs text-gray-500">
                                {{ $trx->tanggal->translatedFormat('d M Y H:i') }}
                            </td>
                            <td class="px-4 py-3 font-bold">
                                {{ $warung['nama_warung'] }}
                            </td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold
                                    {{ (float)$trx->total >= 0 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                    {{ $trx->jenis_transaksi }}
                                </span>
                            </td>
                            <td class="px-4 py-3 italic text-gray-600">
                                {{ $trx->deskripsi }}
                            </td>
                            <td class="px-4 py-3 text-right font-black
                                {{ (float)$trx->total >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                Rp {{ number_format((float)$trx->total,0,',','.') }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                <button
                                    @click="trx = {{ json_encode($trx) }}; showStruk = true"
                                    class="px-3 py-1 text-xs bg-indigo-600 text-white rounded-lg">
                                    <i class="fas fa-receipt mr-1"></i> Struk
                                </button>
                            </td>
                        </tr>
                    @endforeach
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- ================= MODAL STRUK ================= --}}
    <div x-show="showStruk" x-transition
         class="fixed inset-0 bg-black/40 flex items-center justify-center z-50">
        <div class="bg-white w-full max-w-md rounded-xl shadow-xl p-5"
             @click.outside="showStruk=false">

            <h2 class="font-black text-lg mb-3 text-center">
                STRUK TRANSAKSI
            </h2>

            <div class="text-xs text-gray-500 mb-3 text-center">
                <div x-text="trx?.id_ref"></div>
                <div x-text="trx?.tanggal"></div>
            </div>

            <template x-if="trx?.items?.length">
                <div class="border-t border-b py-3 space-y-1 text-sm">
                    <template x-for="item in trx.items">
                        <div class="flex justify-between">
                            <div>
                                <span x-text="item.nama_barang"></span>
                                <span class="text-xs text-gray-400">x<span x-text="item.jumlah"></span></span>
                            </div>
                            <span>Rp <span x-text="item.subtotal.toLocaleString('id-ID')"></span></span>
                        </div>
                    </template>
                </div>
            </template>

            <div class="mt-3 text-sm space-y-1">
                <div class="flex justify-between font-bold">
                    <span>Total</span>
                    <span>Rp <span x-text="Number(trx.total).toLocaleString('id-ID')"></span></span>
                </div>

                <template x-if="trx.uang_dibayar">
                    <div class="flex justify-between">
                        <span>Dibayar</span>
                        <span>Rp <span x-text="Number(trx.uang_dibayar).toLocaleString('id-ID')"></span></span>
                    </div>
                </template>

                <template x-if="trx.uang_kembalian">
                    <div class="flex justify-between">
                        <span>Kembalian</span>
                        <span>Rp <span x-text="Number(trx.uang_kembalian).toLocaleString('id-ID')"></span></span>
                    </div>
                </template>
            </div>

            <div class="mt-4 text-center">
                <button @click="showStruk=false"
                        class="px-6 py-2 bg-gray-200 rounded-lg font-bold">
                    Tutup
                </button>
            </div>
        </div>
    </div>

</div>
@endsection
