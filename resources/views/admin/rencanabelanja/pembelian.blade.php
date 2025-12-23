@extends('layouts.admin')
@section('title', 'Buat Transaksi Pembelian')
@section('content')

    <div class="p-4 bg-gray-50 min-h-screen" x-data="pembelianForm()">
        <header class="flex justify-between items-center mb-4 bg-white p-4 shadow-sm rounded-lg">
            <h1 class="text-xl font-bold text-gray-800">🛒 Pembelian Rencana</h1>
            <a href="{{ route('admin.rencana.index') }}"
                class="text-sm bg-gray-600 text-white px-3 py-1 rounded shadow">Kembali</a>
        </header>
        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <strong>Terjadi kesalahan:</strong>
                <ul class="mt-2 list-disc list-inside text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <form action="{{ route('admin.rencana.store') }}" method="POST" novalidate>
            @csrf
            <div class="space-y-4">
                @foreach ($totalKebutuhan as $g => $item)
                    <div class="bg-white border rounded-lg overflow-hidden shadow-sm transition-all"
                        :class="isSkipped ? 'opacity-75 bg-gray-50' : ''" x-data="groupItem({
                            id: '{{ $g }}',
                            target: {{ $item['total_kebutuhan'] }},
                            harga_awal: {{ $item['harga_awal'] }},
                            areas: {{ $item['valid_areas']->map(fn($a) => ['id' => $a->id, 'name' => $a->area]) }}
                        })" x-init="if (areas.length === 0) isSkipped = true;
                        
                        $watch('purchases', () => {
                            purchases.forEach(p => {
                                console.log('hargaAwal:', this.hargaAwal, 'qty:', p.qty);
                        
                                if (!p.manual_price) {
                                    p.price = (Number(p.qty) || 0) * hargaAwal;
                                }
                            });
                        }, { deep: true });
                        
                        $watch('isSkipped', () => updateGlobalValidation());
                        updateGlobalValidation();">
                        <div class="p-3 flex justify-between items-center transition-colors"
                            :class="isSkipped ? 'bg-gray-600 text-white' : (remaining < 0 ? 'bg-red-600 text-white' :
                                'bg-gray-800 text-white')">
                            <div class="flex items-center space-x-3">
                                <span class="font-bold uppercase tracking-tight">{{ $item['nama_barang'] }}</span>
                                <span class="text-[10px] bg-white/20 px-2 py-1 rounded">Target:
                                    {{ $item['total_kebutuhan'] }}</span>
                                <template x-if="areas.length === 0">
                                    <span class="text-[9px] bg-red-500 px-2 py-0.5 rounded animate-pulse">TIDAK ADA OPSI
                                        AREA</span>
                                </template>
                            </div>

                            <div class="flex items-center space-x-4">
                                <div class="text-[10px] italic opacity-80 hidden md:block">
                                    @foreach ($item['detail_warung'] as $dw)
                                        {{ $dw['warung'] }}:{{ $dw['kebutuhan'] }} |
                                    @endforeach
                                </div>
                                <label
                                    class="flex items-center text-xs font-bold cursor-pointer bg-white/10 hover:bg-white/20 px-3 py-1.5 rounded border border-white/30 transition">
                                    <input type="checkbox" x-model="isSkipped" name="items[{{ $g }}][skip]"
                                        value="1" class="mr-2 w-4 h-4 rounded border-none text-red-600 focus:ring-0">
                                    SKIP ITEM
                                </label>
                            </div>

                            <input type="hidden" name="items[{{ $g }}][id_barang]"
                                value="{{ $item['id_barang'] }}">
                            <input type="hidden" name="items[{{ $g }}][rencana_ids]"
                                value="{{ implode(',', $item['rencana_ids']) }}">
                        </div>

                        <div x-show="!isSkipped" x-collapse>
                            <table class="w-full text-xs">
                                <thead class="bg-gray-50 border-b border-gray-200 text-gray-500 uppercase">
                                    <tr>
                                        <th class="p-2 text-left w-[30%]">Area Pembelian</th>
                                        <th class="p-2 text-center w-[20%]">Qty (pcs)</th>
                                        <th class="p-2 text-right w-[20%]">Total Harga (Rp)</th>
                                        <th class="p-2 text-center w-[20%]">Exp. Date</th>
                                        <th class="p-2 text-center w-[10%]">Opsi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    <template x-for="(row, index) in purchases" :key="index">
                                        <tr class="hover:bg-blue-50/50 transition">
                                            <td class="p-2">
                                                <select :name="`items[${id}][purchases][${index}][area_pembelian_id]`"
                                                    x-model="row.area_id"
                                                    class="w-full border-gray-300 rounded text-xs focus:ring-blue-500 disabled:bg-gray-100 disabled:text-gray-400 disabled:cursor-not-allowed"
                                                    :class="!row.area_id && row.qty > 0 ? 'border-orange-500 bg-orange-50' : ''"
                                                    :disabled="areas.length === 1">

                                                    <option value="">-- Pilih Area --</option>
                                                    <template x-for="area in areas" :key="area.id">
                                                        <option :value="area.id" x-text="area.name"
                                                            :selected="areas.length === 1"
                                                            :disabled="purchases.some((p, i) => p.area_id == area.id && i !==
                                                                index)">
                                                        </option>
                                                    </template>
                                                </select>

                                                <template x-if="areas.length === 1">
                                                    <input type="hidden"
                                                        :name="`items[${id}][purchases][${index}][area_pembelian_id]`"
                                                        :value="row.area_id">
                                                </template>

                                                <template x-if="areas.length === 1">
                                                    <input type="hidden"
                                                        :name="`items[${id}][purchases][${index}][area_pembelian_id]`"
                                                        :value="row.area_id">
                                                </template>

                                            </td>
                                            <td class="p-2">
                                                <input type="number"
                                                    :name="`items[${id}][purchases][${index}][jumlah_beli]`"
                                                    x-model.number="row.qty"
                                                    class="w-full border-gray-300 rounded p-1 text-center font-bold text-blue-600 focus:ring-blue-500">
                                            </td>
                                            <td class="p-2">
                                                <input type="number" :name="`items[${id}][purchases][${index}][harga]`"
                                                    x-model.number="row.price" @input="row.manual_price = true"
                                                    class="w-full border-gray-300 bg-gray-100 text-right font-bold text-green-700 rounded px-2 py-1">

                                                <div class="text-[10px] text-gray-500 mt-1 text-right">
                                                    Harga satuan:
                                                    <span class="font-semibold text-gray-700">
                                                        Rp {{ number_format($item['harga_awal'], 0, ',', '.') }}
                                                    </span>
                                                </div>
                                            </td>

                                            <td class="p-2">
                                                <input type="date"
                                                    :name="`items[${id}][purchases][${index}][tanggal_kadaluarsa]`"
                                                    class="w-full border-gray-300 rounded px-10 text-[10px] focus:ring-blue-500 text-center">
                                            </td>
                                            <td class="p-2 text-center">
                                                <button type="button" @click="removeRow(index)"
                                                    :disabled="purchases.length === 1" class="transition"
                                                    :class="purchases.length === 1 ?
                                                        'text-gray-300 cursor-not-allowed' :
                                                        'text-red-400 hover:text-red-600'">
                                                    <i class="fas fa-trash"></i>Hapus
                                                </button>

                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>

                            <div class="bg-gray-50 p-2 flex justify-between items-center text-[11px] font-bold border-t">
                                <div class="flex space-x-4">
                                    <span class="text-gray-500 uppercase">Sisa: <span
                                            :class="remaining < 0 ? 'text-red-600 animate-pulse' : 'text-gray-700'"
                                            x-text="remaining"></span></span>
                                    <span class="text-blue-600 uppercase">Total Qty: <span x-text="totalQty"></span></span>
                                </div>
                                <div class="flex items-center space-x-3">
                                    <span class="text-green-700 uppercase">Grand Total: <span
                                            x-text="formatIDR(totalPrice)"></span></span>
                                    <button type="button" @click="addRow()" x-show="purchases.length < areas.length"
                                        class="bg-blue-50 text-blue-600 border border-blue-200 px-2 py-1 rounded hover:bg-blue-600 hover:text-white transition uppercase text-[10px]">
                                        <i class="fas fa-plus mr-1"></i> Split Area
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="sticky bottom-4 mt-8 flex flex-col items-end">
                <template x-if="validationMessage">
                    <div class="bg-orange-100 text-orange-700 px-4 py-2 rounded-t-lg text-xs font-bold border border-orange-200 border-b-0 shadow-sm transition-all"
                        x-text="validationMessage"></div>
                </template>

                <button type="submit" :disabled="!isFormValid"
                    :class="!isFormValid ? 'bg-gray-400' : (validationMessage.includes('melebihi') ? 'bg-red-600' :
                        'bg-gray-800 hover:bg-black')"
                    class="text-white px-10 py-4 rounded-full shadow-2xl font-bold transition-all flex items-center group">
                    <span x-text="isFormValid ? '✅ PROSES PEMBELIAN' : '❌ FORM BELUM LENGKAP'"></span>
                    <i class="fas fa-paper-plane ml-3 group-hover:translate-x-1 transition-transform"></i>
                </button>
            </div>
        </form>
    </div>

    <script src="//unpkg.com/alpinejs" defer></script>
    <script>
        document.addEventListener('alpine:init', () => {
            window.Alpine = Alpine;
        });
    </script>
    <script>
        function pembelianForm() {
            return {
                isFormValid: false,
                validationMessage: '',

                formatIDR(val) {
                    return new Intl.NumberFormat('id-ID').format(val);
                },

                updateGlobalValidation() {
                    const groups = Array.from(document.querySelectorAll('[x-data^="groupItem"]'));
                    let valid = true;
                    let message = '';

                    for (let el of groups) {
                        const data = Alpine.$data(el); // ✅ FIX DI SINI

                        if (!data.isSkipped) {
                            if (data.remaining < 0) {
                                valid = false;
                                message = `Barang ${data.id.toUpperCase()} Jumlah Tidak Valid!`;
                                break;
                            }

                            if (data.totalQty === 0) {
                                valid = false;
                                message = `Barang ${data.id.toUpperCase()} belum diisi jumlahnya (atau pilih SKIP).`;
                                break;
                            }

                            for (let p of data.purchases) {
                                if (p.qty > 0 && (!p.area_id || p.price <= 0)) {
                                    valid = false;
                                    message = `Lengkapi Area dan Harga untuk barang ${data.id.toUpperCase()}.`;
                                    break;
                                }
                            }

                            if (!valid) break;
                        }
                    }

                    this.isFormValid = valid;
                    this.validationMessage = message;
                }

            }
        }

        function groupItem(data) {
            return {
                id: data.id,
                target: data.target,
                hargaAwal: data.harga_awal,
                areas: data.areas,
                isSkipped: false,
                purchases: [],

                init() {
                    // Langsung tambah baris pertama
                    this.addRow();

                    // Watchers
                    this.$watch('isSkipped', () => this.updateGlobalValidation());
                    this.$watch('purchases', () => this.updateGlobalValidation(), {
                        deep: true
                    });
                },

                addRow() {
                    // 1. Logika mencari area yang belum dipakai
                    let usedAreaIds = this.purchases.map(p => String(p.area_id));
                    let nextArea = this.areas.find(a => !usedAreaIds.includes(String(a.id)));

                    // 2. Hitung sisa Qty agar split otomatis mengisi sisa target
                    let currentTotalQty = this.purchases.reduce((sum, p) => sum + (Number(p.qty) || 0), 0);
                    let suggestedQty = Math.max(0, this.target - currentTotalQty);

                    // 3. Masukkan ke array purchases
                    this.purchases.push({
                        area_id: this.areas.length === 1 ?
                            this.areas[0].id :
                            this.areas[0]?.id ?? '', // ← AUTO ISI
                        qty: suggestedQty,
                        price: suggestedQty * this.hargaAwal,
                        manual_price: false,
                    });


                },

                removeRow(index) {
                    if (this.purchases.length > 1) {
                        this.purchases.splice(index, 1);
                    }
                },

                get totalQty() {
                    return this.purchases.reduce((sum, p) => sum + (Number(p.qty) || 0), 0);
                },
                get totalPrice() {
                    return this.purchases.reduce((sum, p) => sum + (Number(p.price) || 0), 0);
                },
                get remaining() {
                    return this.target - this.totalQty;
                }
            }
        }
    </script>
@endsection
