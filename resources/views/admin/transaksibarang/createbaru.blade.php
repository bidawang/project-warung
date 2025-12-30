@extends('layouts.admin')

@section('title', 'Form Rencana Belanja')

@section('content')
    <div class="h-full overflow-y-auto" x-data="rencanaBelanjaManager()" x-init="init()">
        {{-- Page Header --}}
        <div class="bg-white border-b border-gray-200 px-6 py-4 mb-6">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800">Input Pengadaan Barang</h2>
                    <p class="text-sm text-gray-500">Buat transaksi pembelian berdasarkan area untuk memenuhi stok warung.</p>
                </div>
                <div class="flex items-center gap-3 bg-blue-50 px-4 py-2 rounded-lg border border-blue-100">
                    <div class="text-right">
                        <p class="text-[10px] uppercase tracking-wider text-blue-600 font-bold">Total Estimasi</p>
                        <p class="text-lg font-black text-blue-800 font-mono">Rp <span x-text="formatRupiah(calculateGrandTotal())"></span></p>
                    </div>
                </div>
            </div>
        </div>

        <div class="px-6 pb-24">
            @if ($errors->any())
                <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-r-xl shadow-sm animate-fade-in">
                    <div class="flex">
                        <div class="ml-3">
                            <p class="text-sm font-bold text-red-800">Terjadi Kesalahan Input:</p>
                            <ul class="mt-1 text-sm text-red-700 list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            <div class="flex flex-col lg:flex-row gap-6">
                {{-- LEFT: Reference Panel --}}
                <div class="lg:w-80 flex-shrink-0">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 sticky top-4">
                        <div class="p-4 border-b border-gray-100 bg-gray-50/50 rounded-t-xl text-center">
                            <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">Data Referensi</span>
                        </div>
                        <div class="p-4">
                            <div class="flex p-1 bg-gray-100 rounded-lg mb-4">
                                <button @click="view = 'warung'" :class="view === 'warung' ? 'bg-white shadow text-blue-600' : 'text-gray-500'" class="flex-1 py-1.5 text-xs font-bold rounded-md transition-all">Warung</button>
                                <button @click="view = 'barang'" :class="view === 'barang' ? 'bg-white shadow text-blue-600' : 'text-gray-500'" class="flex-1 py-1.5 text-xs font-bold rounded-md transition-all">Barang</button>
                            </div>
                            <input type="text" x-model="searchQuery" placeholder="Cari..." class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-xs mb-4 outline-none">
                            
                            <div class="space-y-3 max-h-[50vh] overflow-y-auto pr-1">
                                <template x-if="view === 'warung'">
                                    <div class="space-y-2">
                                        @foreach ($rencanaBelanjaByWarung as $namaWarung => $items)
                                            <div class="p-3 rounded-lg border border-gray-100 bg-gray-50/50" x-show="'{{ strtolower($namaWarung) }}'.includes(searchQuery.toLowerCase())">
                                                <p class="text-[11px] font-black text-gray-700 uppercase mb-2 border-b pb-1">{{ $namaWarung }}</p>
                                                @foreach ($items as $item)
                                                    <div class="flex justify-between text-[10px] text-gray-600">
                                                        <span>{{ $item->barang->nama_barang }}</span>
                                                        <span class="font-bold text-blue-600">{{ $item->jumlah_awal }}x</span>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endforeach
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- RIGHT: Main Form --}}
                <div class="flex-1">
                    <form action="{{ route('admin.transaksibarang.store') }}" method="POST" class="space-y-6">
                        @csrf
                        <div class="space-y-6">
                            <template x-for="(area, aIndex) in selectedAreas" :key="area.key">
                                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden animate-fade-in">
                                    {{-- Card Header --}}
                                    <div class="px-5 py-3 bg-gray-900 flex justify-between items-center">
                                        <div class="flex items-center gap-3">
                                            <div class="w-6 h-6 rounded bg-blue-500 text-white flex items-center justify-center text-xs font-bold" x-text="aIndex + 1"></div>
                                            <h3 class="text-xs font-bold text-white uppercase tracking-widest">Area Pembelian</h3>
                                        </div>
                                        <button type="button" @click="removeArea(aIndex)" class="text-gray-400 hover:text-red-400">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-4v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" stroke-width="2"/></svg>
                                        </button>
                                    </div>

                                    <div class="p-5">
                                        <div class="mb-5">
                                            <label class="text-[10px] font-bold text-gray-400 uppercase block mb-1.5 ml-1">Lokasi Toko</label>
                                            <select :name="'id_area[]'" x-model="area.id_area" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg text-sm outline-none transition-all" required>
                                                <option value="">-- Pilih Lokasi Pembelian --</option>
                                                @foreach ($areas as $a)
                                                    <option value="{{ $a->id }}" :disabled="isAreaDisabled({{ $a->id }}, aIndex)">{{ $a->area }} ({{ $a->markup }}% Markup)</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="border border-gray-100 rounded-lg overflow-hidden">
                                            <table class="w-full text-left">
                                                <thead class="bg-gray-50 border-b">
                                                    <tr class="text-[10px] font-bold text-gray-400 uppercase">
                                                        <th class="px-4 py-2">Produk</th>
                                                        <th class="px-4 py-2 w-24 text-center">Qty</th>
                                                        <th class="px-4 py-2 text-right">Subtotal (Rp)</th>
                                                        <th class="px-2 py-2 w-10"></th>
                                                    </tr>
                                                </thead>
                                                <tbody class="divide-y divide-gray-50">
                                                    <template x-for="(item, iIndex) in area.items" :key="item.key">
                                                        <tr>
                                                            <td class="px-4 py-2">
                                                                <select :name="'id_barang[' + aIndex + '][]'" x-model="item.id_barang" @change="updatePrice(item, $event)" class="w-full bg-transparent border-none text-sm font-medium focus:ring-0 p-0" required>
                                                                    <option value="">Cari Produk...</option>
                                                                    @foreach ($barangs as $b)
                                                                        <option value="{{ $b->id }}" data-harga="{{ $b->harga }}">{{ $b->nama_barang }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </td>
                                                            <td class="px-4 py-2">
                                                                <input type="number" :name="'jumlah[' + aIndex + '][]'" x-model.number="item.jumlah" @input="calculateTotal(item)" class="w-full bg-transparent border-none text-center text-sm font-bold focus:ring-0 p-0" min="1">
                                                            </td>
                                                            <td class="px-4 py-2 text-right">
                                                                <input type="number" 
                                                                       :name="'total_harga[' + aIndex + '][]'" 
                                                                       x-model.number="item.total_harga" 
                                                                       @input="calculateFromTotal(item)"
                                                                       class="w-32 bg-gray-50 border border-gray-200 rounded px-2 py-1 text-right text-sm font-bold text-blue-600 focus:ring-1 focus:ring-blue-500 outline-none">
                                                            </td>
                                                            <td class="px-2 py-2 text-center">
                                                                <button type="button" @click="removeItem(aIndex, iIndex)" class="text-gray-300 hover:text-red-500">
                                                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"/></svg>
                                                                </button>
                                                            </td>
                                                        </tr>
                                                    </template>
                                                </tbody>
                                            </table>
                                            <button type="button" @click="addItem(aIndex)" class="w-full py-2 bg-gray-50 text-[10px] font-bold text-gray-500 hover:text-blue-600 transition-all uppercase">+ Tambah Baris</button>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>

                        {{-- Action Buttons --}}
                        <div class="flex justify-center py-4">
                            <button type="button" @click="addArea()" :disabled="selectedAreas.length >= {{ count($areas) }}" class="flex items-center gap-2 px-6 py-2.5 bg-white border-2 border-dashed border-gray-300 rounded-xl text-gray-500 hover:border-blue-500 hover:text-blue-600 disabled:opacity-50 transition-all">
                                <span class="text-sm font-bold uppercase">Tambah Lokasi Belanja</span>
                            </button>
                        </div>

                        {{-- Biaya Lain-lain --}}
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                            <h4 class="text-xs font-bold text-gray-800 uppercase mb-4">Biaya Operasional</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                <template x-for="(lain, lIndex) in lainLain" :key="lain.key">
                                    <div class="flex items-center gap-2 p-2 bg-purple-50 border border-purple-100 rounded-lg">
                                        <input type="text" name="lain_keterangan[]" x-model="lain.keterangan" placeholder="Keterangan..." class="flex-1 bg-white border-gray-200 rounded px-3 py-1.5 text-xs outline-none">
                                        <input type="number" name="lain_harga[]" x-model.number="lain.harga" placeholder="Rp" class="w-24 bg-white border-gray-200 rounded px-3 py-1.5 text-xs font-bold text-purple-700 outline-none">
                                        <button type="button" @click="removeLain(lIndex)" class="text-purple-300 hover:text-red-500"><svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M10 18a8 8 0 100-16 8 8 0 000 16zM7 9a1 1 0 000 2h6a1 1 0 100-2H7z"/></svg></button>
                                    </div>
                                </template>
                            </div>
                            <button type="button" @click="addLain()" class="mt-4 text-[10px] font-black text-purple-600 uppercase">+ Tambah Pengeluaran</button>
                        </div>

                        {{-- Footer Bar --}}
                        <div class="fixed bottom-0 right-0 left-0 lg:left-64 bg-white border-t border-gray-200 p-4 z-40">
                            <div class="max-w-7xl mx-auto flex items-center justify-between">
                                <div class="hidden sm:block">
                                    <p class="text-[10px] text-gray-400 font-bold uppercase">Grand Total</p>
                                    <p class="text-xl font-black text-gray-900 font-mono">Rp <span x-text="formatRupiah(calculateGrandTotal())"></span></p>
                                </div>
                                <div class="flex gap-3 w-full sm:w-auto">
                                    <a href="{{ route('admin.transaksibarang.index') }}" class="px-6 py-2.5 text-sm font-bold text-gray-500 bg-gray-100 rounded-lg">Batal</a>
                                    <button type="submit" class="px-10 py-2.5 text-sm font-bold text-white bg-blue-600 hover:bg-blue-700 rounded-lg shadow-lg">Simpan Transaksi</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function rencanaBelanjaManager() {
            return {
                view: 'warung',
                searchQuery: '',
                selectedAreas: [],
                lainLain: [],

                init() {
                    this.addArea();
                },

                generateId() {
                    return Math.random().toString(36).substring(2, 9) + Date.now().toString(36);
                },

                addArea() {
                    this.selectedAreas.push({
                        key: 'area-' + this.generateId(),
                        id_area: '',
                        items: [{
                            key: 'item-' + this.generateId(),
                            id_barang: '',
                            jumlah: 1,
                            harga_satuan: 0,
                            total_harga: 0
                        }]
                    });
                },

                removeArea(index) {
                    if (this.selectedAreas.length > 1) this.selectedAreas.splice(index, 1);
                },

                addItem(areaIndex) {
                    this.selectedAreas[areaIndex].items.push({
                        key: 'item-' + this.generateId(),
                        id_barang: '',
                        jumlah: 1,
                        harga_satuan: 0,
                        total_harga: 0
                    });
                },

                removeItem(areaIndex, itemIndex) {
                    if (this.selectedAreas[areaIndex].items.length > 1) {
                        this.selectedAreas[areaIndex].items.splice(itemIndex, 1);
                    }
                },

                addLain() {
                    this.lainLain.push({ key: 'lain-' + this.generateId(), keterangan: '', harga: 0 });
                },

                removeLain(index) {
                    this.lainLain.splice(index, 1);
                },

                isAreaDisabled(areaId, currentIndex) {
                    return this.selectedAreas.some((a, idx) => a.id_area == areaId && idx !== currentIndex);
                },

                updatePrice(item, event) {
                    const selectedOption = event.target.options[event.target.selectedIndex];
                    item.harga_satuan = parseFloat(selectedOption.dataset.harga) || 0;
                    this.calculateTotal(item);
                },

                calculateTotal(item) {
                    item.total_harga = (item.harga_satuan || 0) * (item.jumlah || 0);
                },

                calculateFromTotal(item) {
                    // Jika user input total manual, hitung balik harga satuan (opsional)
                    if (item.jumlah > 0) {
                        item.harga_satuan = item.total_harga / item.jumlah;
                    }
                },

                calculateGrandTotal() {
                    let total = 0;
                    this.selectedAreas.forEach(a => {
                        a.items.forEach(i => {
                            total += parseFloat(i.total_harga || 0);
                        });
                    });
                    this.lainLain.forEach(l => {
                        total += parseFloat(l.harga || 0);
                    });
                    return total;
                },

                formatRupiah(number) {
                    return new Intl.NumberFormat('id-ID').format(number);
                }
            }
        }
    </script>
@endsection