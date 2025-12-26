@extends('layouts.admin')
@section('title', 'Belanja Per Area')
@section('content')

    <div class="flex-1 flex flex-col h-screen bg-gray-50" 
        x-data="{
            activeTab: '{{ $totalKebutuhan->keys()->first() }}',
            canSubmit: false,
            // Fungsi validasi global yang dipicu oleh event
            checkValidation() {
                // Mencari semua baris yang memiliki atribut data-bought='true'
                let selectedRows = Array.from(document.querySelectorAll('.item-row[data-bought=\'true\']'));
                
                if (selectedRows.length === 0) {
                    this.canSubmit = false;
                    return;
                }

                // Validasi: apakah semua baris yang dipilih memenuhi kriteria
                this.canSubmit = selectedRows.every(row => {
                    let qtyInput = row.querySelector('.qty-input');
                    let priceInput = row.querySelector('.price-input');
                    
                    let qty = parseFloat(qtyInput.value) || 0;
                    let total = parseFloat(priceInput.value) || 0;
                    
                    return qty >= 1 && total >= 100;
                });
            }
        }" 
        @validation-check.window="checkValidation()">

        <header class="flex justify-between items-center px-6 py-4 bg-white border-b border-gray-200 shadow-sm z-10">
            <div>
                <h1 class="text-xl font-extrabold text-gray-800 tracking-tight flex items-center">
                    <span class="bg-blue-100 text-blue-600 p-2 rounded-lg mr-3">
                        <i class="fas fa-shopping-basket"></i>
                    </span>
                    Pembelian Berbasis Area
                </h1>
                <p class="text-sm text-gray-500 mt-1">Kelola pembelanjaan barang berdasarkan lokasi distributor/pasar.</p>
            </div>
            <a href="{{ route('admin.rencana.index') }}"
                class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-xl text-sm font-semibold text-gray-700 hover:bg-gray-50 transition-all shadow-sm">
                <i class="fas fa-arrow-left mr-2 text-gray-400"></i> Kembali
            </a>
        </header>

        @if ($errors->any())
            <div class="mx-6 mt-4 p-4 bg-red-50 border-l-4 border-red-500 rounded-r-xl shadow-sm">
                <div class="flex">
                    <i class="fas fa-exclamation-circle text-red-500 mt-1 mr-3"></i>
                    <div>
                        <h3 class="text-sm font-bold text-red-800">Mohon perbaiki kesalahan berikut:</h3>
                        <ul class="mt-1 list-disc list-inside text-xs text-red-700 space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        <nav class="flex px-6 bg-white border-b border-gray-200 overflow-x-auto no-scrollbar gap-2">
            @foreach ($totalKebutuhan as $areaName => $data)
                @php $isTanpaArea = strtolower($areaName) == 'tanpa area'; @endphp
                <button @click="activeTab = '{{ $areaName }}'"
                    :class="activeTab === '{{ $areaName }}' ? 'border-blue-600 text-blue-600 bg-blue-50/50' : 'border-transparent text-gray-500 hover:text-gray-700 hover:bg-gray-50'"
                    class="px-5 py-4 border-b-2 font-bold text-xs uppercase tracking-widest whitespace-nowrap transition-all flex items-center">
                    @if ($isTanpaArea) <i class="fas fa-exclamation-triangle mr-2 text-amber-500"></i> @endif
                    {{ $areaName }}
                    <span class="ml-2.5 px-2 py-0.5 rounded-md text-[10px] {{ $isTanpaArea ? 'bg-red-100 text-red-600' : 'bg-gray-100 text-gray-600' }}">
                        {{ count($data['items']) }}
                    </span>
                </button>
            @endforeach
        </nav>

        <main class="flex-1 overflow-y-auto p-6 scroll-smooth">
            <form action="{{ route('admin.rencana.store') }}" method="POST" @submit="if(!canSubmit) $event.preventDefault()">
                @csrf

                @php $globalIndex = 0; @endphp
                @foreach ($totalKebutuhan as $areaName => $data)
                    @php $isTanpaArea = strtolower($areaName) == 'tanpa area'; @endphp

                    <div x-show="activeTab === '{{ $areaName }}'" x-transition:enter="transition ease-out duration-300">
                        @if ($isTanpaArea)
                            <div class="mb-6 p-4 bg-amber-50 border border-amber-200 rounded-2xl flex items-start shadow-sm">
                                <div class="bg-amber-100 p-2 rounded-lg mr-4">
                                    <i class="fas fa-lock text-amber-600"></i>
                                </div>
                                <div>
                                    <h4 class="text-sm font-bold text-amber-800 uppercase tracking-tight">Data Belum Lengkap</h4>
                                    <p class="text-xs text-amber-700 mt-1">Barang ini belum memiliki <b>Area Pembelian</b>. Lengkapi di Master Barang.</p>
                                </div>
                            </div>
                        @endif

                        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                            <table class="min-w-full table-fixed divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="w-1/3 px-6 py-4 text-left text-[10px] font-bold text-gray-400 uppercase tracking-widest">Informasi Barang</th>
                                        <th class="w-24 px-6 py-4 text-center text-[10px] font-bold text-gray-400 uppercase tracking-widest">Qty</th>
                                        <th class="w-1/4 px-6 py-4 text-right text-[10px] font-bold text-gray-400 uppercase tracking-widest">Total Pembayaran</th>
                                        <th class="w-40 px-6 py-4 text-center text-[10px] font-bold text-gray-400 uppercase tracking-widest">Tgl Expired</th>
                                        <th class="w-24 px-6 py-4 text-center text-[10px] font-bold text-gray-400 uppercase tracking-widest">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 bg-white">
                                    @foreach ($data['items'] as $barangId => $item)
                                        <tr class="item-row transition-all duration-200" 
                                            x-data="{
                                                isBought: false,
                                                qty: {{ $item['total_kebutuhan'] }},
                                                totalPrice: {{ $item['harga_awal'] * $item['total_kebutuhan'] }},
                                                recUnitPrice: {{ $item['harga_awal'] }},
                                                isTanpaArea: {{ $isTanpaArea ? 'true' : 'false' }}
                                            }"
                                            :data-bought="isBought"
                                            :class="isBought ? 'bg-blue-50/20' : 'opacity-60 grayscale-[0.5]'">

                                            <td class="px-6 py-4">
                                                <div class="flex flex-col">
                                                    <span class="text-sm font-bold text-gray-800 uppercase tracking-tight" :class="isBought ? 'text-blue-900' : ''">
                                                        {{ $item['nama_barang'] }}
                                                    </span>
                                                    <div class="mt-1 flex flex-wrap gap-1">
                                                        @foreach ($item['detail_warung'] as $dw)
                                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[9px] font-medium bg-gray-100 text-gray-500 border border-gray-200">
                                                                {{ $dw['warung'] }}: {{ $dw['kebutuhan'] }}
                                                            </span>
                                                        @endforeach
                                                    </div>
                                                </div>
                                                <input type="hidden" name="items[{{ $globalIndex }}][id_barang]" value="{{ $barangId }}">
                                                <input type="hidden" name="items[{{ $globalIndex }}][rencana_ids]" value="{{ implode(',', $item['rencana_ids']) }}">
                                                <input type="hidden" name="items[{{ $globalIndex }}][skip]" :value="isBought ? '0' : '1'">
                                                <input type="hidden" name="items[{{ $globalIndex }}][purchases][0][area_pembelian_id]" value="{{ $data['area_id'] }}">
                                                <input type="hidden" name="items[{{ $globalIndex }}][purchases][0][harga]" :value="qty > 0 ? (totalPrice / qty) : 0">
                                            </td>

                                            <td class="px-6 py-4">
                                                <input type="number"
                                                    name="items[{{ $globalIndex }}][purchases][0][jumlah_beli]"
                                                    x-model.number="qty"
                                                    @input="totalPrice = qty * recUnitPrice; $dispatch('validation-check')"
                                                    :readonly="!isBought || isTanpaArea"
                                                    class="qty-input w-full h-10 border rounded-xl text-center text-sm font-bold transition-all outline-none"
                                                    :class="!isBought ? 'bg-gray-50 border-gray-200' : 'bg-white border-blue-300 focus:ring-2 focus:ring-blue-100'">
                                            </td>

                                            <td class="px-6 py-4">
                                                <div class="flex flex-col items-end space-y-1">
                                                    <div class="relative w-full max-w-[180px]">
                                                        <span class="absolute left-3 top-2.5 text-xs font-bold text-gray-400">Rp</span>
                                                        <input type="number" 
                                                            x-model.number="totalPrice"
                                                            @input="$dispatch('validation-check')" 
                                                            :readonly="!isBought || isTanpaArea"
                                                            class="price-input w-full pl-9 pr-3 py-2 border rounded-xl text-right text-sm font-black transition-all outline-none"
                                                            :class="!isBought ? 'bg-gray-50 border-gray-200' : (totalPrice < 100 ? 'bg-white border-red-500' : 'bg-white border-emerald-300 focus:ring-2 focus:ring-emerald-100')">
                                                    </div>
                                                    <div class="flex justify-between w-full max-w-[180px] px-1" x-show="isBought">
                                                        <span class="text-[9px] text-gray-400 uppercase font-bold">Satuan:</span>
                                                        <span class="text-[10px] font-bold text-gray-600">
                                                            Rp <span x-text="qty > 0 ? new Intl.NumberFormat('id-ID').format(Math.round(totalPrice / qty)) : 0"></span>
                                                        </span>
                                                    </div>
                                                </div>
                                            </td>

                                            <td class="px-6 py-4">
                                                <input type="date"
                                                    name="items[{{ $globalIndex }}][purchases][0][tanggal_kadaluarsa]"
                                                    :readonly="!isBought || isTanpaArea"
                                                    :class="isBought ? 'border-gray-300' : 'border-gray-100 bg-gray-50'"
                                                    class="w-full text-xs border rounded-xl p-2 outline-none focus:ring-2 focus:ring-gray-100 transition-all">
                                            </td>

                                            <td class="px-6 py-4 text-center">
                                                <button type="button"
                                                    @click="if(!isTanpaArea) { isBought = !isBought; $nextTick(() => $dispatch('validation-check')); }"
                                                    :disabled="isTanpaArea"
                                                    :class="isBought ? 'bg-blue-600 text-white border-black shadow-lg scale-105' : (isTanpaArea ? 'bg-gray-100 text-gray-300 border-gray-200 cursor-not-allowed' : 'bg-white border-black text-gray-400 hover:bg-gray-50')"
                                                    class="w-12 h-12 inline-flex items-center justify-center rounded-xl border-2 transition-all transform active:scale-95 shadow-sm">
                                                    <i class="fas" :class="isBought ? 'fa-check text-lg' : (isTanpaArea ? 'fa-lock' : 'fa-cart-plus')"></i>
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

                <div class="mt-8 mb-4 sticky bottom-0 bg-gray-50/80 backdrop-blur-md pt-4 pb-2">
                    <div class="flex flex-col md:flex-row items-center justify-between gap-4 bg-white p-4 rounded-3xl shadow-xl border border-gray-200">
                        <div class="flex items-center space-x-4">
                            <div class="w-10 h-10 rounded-full bg-blue-100 border-2 border-white flex items-center justify-center text-blue-600">
                                <i class="fas fa-info-circle"></i>
                            </div>
                            <p class="text-xs font-medium text-gray-600 max-w-sm">
                                <span class="block font-bold text-gray-800">Ringkasan Validasi</span>
                                <span x-show="!canSubmit" class="text-red-500">Minimal 1 barang dipilih, Qty ≥ 1, & Harga ≥ Rp 100.</span>
                                <span x-show="canSubmit" class="text-emerald-600 italic">Data siap untuk disimpan.</span>
                            </p>
                        </div>

                        <button type="submit" :disabled="!canSubmit"
                            :class="canSubmit ? 'bg-blue-600 hover:bg-blue-700 shadow-blue-200' : 'bg-gray-400 cursor-not-allowed'"
                            class="w-full md:w-auto px-10 py-4 rounded-2xl font-extrabold text-white shadow-2xl transition-all flex items-center justify-center group transform active:scale-95">
                            <div class="mr-4 text-right border-r border-white/20 pr-4 hidden sm:block">
                                <p class="text-[9px] uppercase tracking-widest opacity-80">Finalisasi</p>
                                <p class="text-xs">Simpan Data</p>
                            </div>
                            <i class="fas fa-save text-lg group-hover:rotate-12 transition-transform"></i>
                        </button>
                    </div>
                </div>
            </form>
        </main>
    </div>

    <style>
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
        main::-webkit-scrollbar { width: 6px; }
        main::-webkit-scrollbar-track { background: transparent; }
        main::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
        input[type=number]::-webkit-inner-spin-button, input[type=number]::-webkit-outer-spin-button { -webkit-appearance: none; margin: 0; }
        tr.item-row:hover:not(.opacity-60) { background-color: rgba(239, 246, 255, 0.5); }
    </style>

@endsection