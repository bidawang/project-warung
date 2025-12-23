@extends('layouts.admin')
@section('title', 'Belanja Per Area')
@section('content')

    <div class="flex-1 flex flex-col overflow-hidden bg-gray-50" x-data="{ activeTab: '{{ $totalKebutuhan->keys()->first() }}' }">

        <header class="flex justify-between items-center p-4 bg-white border-b border-gray-200">
            <div>
                <h1 class="text-xl font-bold text-gray-800">🛒 Pembelian Berbasis Area</h1>
                <p class="text-xs text-gray-500">Centang barang yang dibeli, lalu masukkan total pembayaran.</p>
            </div>
            <a href="{{ route('admin.rencana.index') }}"
                class="bg-gray-100 text-gray-600 px-4 py-2 text-sm rounded-lg hover:bg-gray-200 transition">
                <i class="fas fa-arrow-left mr-2"></i>Kembali
            </a>
        </header>

        @if ($errors->any())
            <div class="m-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded shadow-sm">
                <strong class="font-bold">Terjadi kesalahan validasi:</strong>
                <ul class="mt-2 list-disc list-inside text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="flex bg-white border-b border-gray-200 overflow-x-auto no-scrollbar">
            @foreach ($totalKebutuhan as $areaName => $data)
                <button @click="activeTab = '{{ $areaName }}'"
                    :class="activeTab === '{{ $areaName }}' ? 'border-blue-600 text-blue-600 bg-blue-50' :
                        'border-transparent text-gray-500 hover:bg-gray-50'"
                    class="px-6 py-4 border-b-2 font-bold text-sm uppercase whitespace-nowrap transition-all flex items-center">
                    {{ $areaName }}
                    <span class="ml-2 bg-gray-200 text-gray-700 px-2 py-0.5 rounded-full text-[10px]">
                        {{ count($data['items']) }}
                    </span>
                </button>
            @endforeach
        </div>

        <main class="p-4 overflow-y-auto">
            <form action="{{ route('admin.rencana.store') }}" method="POST" novalidate>
                @csrf

                @php $globalIndex = 0; @endphp
                @foreach ($totalKebutuhan as $areaName => $data)
                    <div x-show="activeTab === '{{ $areaName }}'" x-transition:enter="transition ease-out duration-200">

                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50 text-[10px] uppercase text-gray-500 tracking-wider">
                                    <tr>
                                        <th class="px-4 py-3 text-left">Barang</th>
                                        <th class="px-4 py-3 text-center w-24">Qty</th>
                                        <th class="px-4 py-3 text-right">Harga (Total Bayar)</th>
                                        <th class="px-4 py-3 text-center">Expired</th>
                                        <th class="px-4 py-3 text-center">Beli?</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @foreach ($data['items'] as $barangId => $item)
                                        <tr class="hover:bg-blue-50/30 transition group" x-data="{
                                            isBought: false,
                                            qty: {{ $item['total_kebutuhan'] }},
                                            totalPrice: {{ $item['harga_awal'] * $item['total_kebutuhan'] }},
                                            recUnitPrice: {{ $item['harga_awal'] }}
                                        }"
                                            :class="!isBought ? 'opacity-50 bg-gray-50' : 'bg-white'">

                                            <td class="px-4 py-3">
                                                <span class="block font-bold text-gray-800 text-sm uppercase"
                                                    :class="!isBought ? 'text-gray-400' : ''">
                                                    {{ $item['nama_barang'] }}
                                                </span>
                                                <div class="text-[9px] text-gray-400 italic mt-1 uppercase">
                                                    @foreach ($item['detail_warung'] as $dw)
                                                        {{ $dw['warung'] }}: {{ $dw['kebutuhan'] }} |
                                                    @endforeach
                                                </div>

                                                <input type="hidden" name="items[{{ $globalIndex }}][id_barang]"
                                                    value="{{ $barangId }}">
                                                <input type="hidden" name="items[{{ $globalIndex }}][rencana_ids]"
                                                    value="{{ implode(',', $item['rencana_ids']) }}">

                                                <input type="hidden" name="items[{{ $globalIndex }}][skip]"
                                                    :value="isBought ? '0' : '1'">

                                                <input type="hidden"
                                                    name="items[{{ $globalIndex }}][purchases][0][area_pembelian_id]"
                                                    value="{{ $data['area_id'] }}">
                                                <input type="hidden"
                                                    name="items[{{ $globalIndex }}][purchases][0][harga]"
                                                    :value="qty > 0 ? (totalPrice / qty) : 0">
                                            </td>

                                            <td class="px-4 py-3 text-center">
                                                <input type="number"
                                                    name="items[{{ $globalIndex }}][purchases][0][jumlah_beli]"
                                                    x-model.number="qty" @input="totalPrice = qty * recUnitPrice"
                                                    :readonly="!isBought"
                                                    :class="!isBought ? 'bg-gray-100 border-gray-200 text-gray-400' :
                                                        'bg-white border-blue-300 text-blue-600'"
                                                    class="w-full border rounded text-center text-sm font-bold focus:ring-blue-500 transition-colors">
                                            </td>

                                            <td class="px-4 py-3">
                                            <td class="px-4 py-3">
                                                <div class="flex items-center justify-end space-x-3">
                                                    <div class="text-right" x-show="isBought">
                                                        <p class="text-[9px] text-gray-400 uppercase leading-none">Satuan
                                                        </p>
                                                        <p class="text-xs font-bold text-gray-500">
                                                            Rp <span
                                                                x-text="qty > 0 ? new Intl.NumberFormat('id-ID').format(Math.round(totalPrice / qty)) : 0"></span>
                                                        </p>
                                                    </div>

                                                    <div class="relative">
                                                        <span
                                                            class="absolute left-2 top-2 text-[10px] font-bold text-gray-400">Rp</span>
                                                        <input type="number" x-model.number="totalPrice"
                                                            :readonly="!isBought"
                                                            :class="!isBought ? 'bg-gray-100 border-gray-200 text-gray-400' :
                                                                'bg-white border-green-300 text-green-700'"
                                                            class="w-36 pl-7 pr-2 py-2 border rounded text-right text-sm font-black focus:ring-green-500 shadow-sm transition-colors">

                                                        <div class="text-right mt-1">
                                                            <p class="text-[9px] text-gray-400 italic leading-tight">
                                                                Rek. Total: <span class="font-bold text-blue-500">Rp <span
                                                                        x-text="new Intl.NumberFormat('id-ID').format(qty * recUnitPrice)"></span></span>
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            </td>

                                            <td class="px-4 py-3 text-center">
                                                <input type="date"
                                                    name="items[{{ $globalIndex }}][purchases][0][tanggal_kadaluarsa]"
                                                    :readonly="!isBought"
                                                    :class="!isBought ? 'bg-gray-100 text-gray-400' : 'bg-white text-gray-700'"
                                                    class="text-[10px] border border-gray-300 rounded p-1">
                                            </td>

                                            <td class="px-4 py-3 text-center">
                                                <button type="button" @click="isBought = !isBought"
                                                    :class="isBought ? 'bg-green-500 text-white shadow-lg scale-110' :
                                                        'bg-white border border-gray-300 text-gray-300 hover:border-blue-500 hover:text-blue-500'"
                                                    class="w-10 h-10 flex items-center justify-center rounded-xl transition-all transform">
                                                    <i class="fas"
                                                        :class="isBought ? 'fa-check' : 'fa-shopping-cart'"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        @php $globalIndex++; @endphp
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endforeach

                <div class="sticky bottom-6 mt-8 flex justify-end">
                    <button type="submit"
                        class="bg-blue-600 text-white px-8 py-4 rounded-2xl font-bold shadow-2xl hover:bg-blue-700 transition flex items-center group">
                        <div class="mr-4 text-right border-r border-blue-400 pr-4">
                            <p class="text-[10px] uppercase opacity-80">Proses</p>
                            <p class="text-xs">Hanya Item Dicentang</p>
                        </div>
                        <i class="fas fa-save text-xl"></i>
                    </button>
                </div>
            </form>
        </main>
    </div>

    <style>
        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }

        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        input[type=number]::-webkit-inner-spin-button,
        input[type=number]::-webkit-outer-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }
    </style>

@endsection
