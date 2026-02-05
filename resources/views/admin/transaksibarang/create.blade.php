@extends('layouts.admin')

@section('title', 'Rencana Belanja')

@section('content')
<div
    x-data="transaksiBarang()"
    class="flex-1 flex flex-col overflow-hidden"
>

    {{-- HEADER --}}
    <header class="flex justify-between items-center p-6 bg-white border-b">
        <h1 class="text-2xl font-bold text-gray-800">Rencana Belanja</h1>
        <div class="flex items-center gap-3">
            <span class="font-semibold text-gray-600">Admin</span>
            <div class="w-10 h-10 bg-blue-600 rounded-full"></div>
        </div>
    </header>

    {{-- MAIN --}}
    <main class="flex-1 overflow-y-auto bg-gray-100 p-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- LEFT --}}
            <aside class="bg-white rounded-xl shadow p-6 sticky top-6 h-fit">
                <h2 class="font-bold text-lg mb-4">Rencana Belanja</h2>

                <div class="flex gap-2 mb-4">
                    <button @click="view='warung'"
                        :class="view==='warung'?'bg-blue-600 text-white':'bg-gray-200'"
                        class="flex-1 py-2 rounded-lg text-sm font-semibold">
                        Warung
                    </button>
                    <button @click="view='barang'"
                        :class="view==='barang'?'bg-blue-600 text-white':'bg-gray-200'"
                        class="flex-1 py-2 rounded-lg text-sm font-semibold">
                        Barang
                    </button>
                </div>

                <input x-model="search" type="text" placeholder="Cari..."
                    class="w-full border rounded-lg px-3 py-2 mb-4">

                <template x-if="view==='warung'">
                    <div class="space-y-3 max-h-[65vh] overflow-y-auto">
                        <template x-for="(items, warung) in rencanaByWarung">
                            <div x-show="warung.toLowerCase().includes(search.toLowerCase())"
                                class="border rounded-lg p-3 bg-indigo-50">
                                <h3 class="font-bold text-indigo-700" x-text="warung"></h3>
                                <ul class="text-sm list-disc list-inside">
                                    <template x-for="i in items">
                                        <li x-text="i.barang.nama_barang + ' : ' + i.jumlah_awal"></li>
                                    </template>
                                </ul>
                            </div>
                        </template>
                    </div>
                </template>

                <template x-if="view==='barang'">
                    <div class="space-y-3 max-h-[65vh] overflow-y-auto">
                        <template x-for="(items, barang) in rencanaByBarang">
                            <div x-show="barang.toLowerCase().includes(search.toLowerCase())"
                                class="border rounded-lg p-3 bg-green-50">
                                <h3 class="font-bold text-green-700" x-text="barang"></h3>
                                <ul class="text-sm list-disc list-inside">
                                    <template x-for="i in items">
                                        <li x-text="i.warung.nama_warung + ' : ' + i.jumlah_awal"></li>
                                    </template>
                                </ul>
                            </div>
                        </template>
                    </div>
                </template>
            </aside>

            {{-- RIGHT --}}
            <section class="lg:col-span-2 bg-white rounded-xl shadow p-6">
                <form method="POST" action="{{ route('admin.transaksibarang.store') }}">
                    @csrf

                    <template x-for="(area, aIndex) in transaksi" :key="aIndex">
                        <div class="border rounded-lg p-4 mb-6 bg-gray-50">

                            <div class="flex justify-between mb-3">
                                <h3 class="font-semibold">Area <span x-text="aIndex+1"></span></h3>
                                <button type="button" @click="removeArea(aIndex)"
                                    class="text-red-600 font-bold">×</button>
                            </div>

                            <select x-model="area.area_id" name="id_area[]"
                                class="w-full border rounded-lg px-3 py-2 mb-4">
                                <option value="">Pilih Area</option>
                                <template x-for="a in areas">
                                    <option :value="a.id" x-text="a.area"></option>
                                </template>
                            </select>

                            <table class="w-full border text-sm">
                                <thead class="bg-gray-100">
                                    <tr>
                                        <th class="border p-2">Barang</th>
                                        <th class="border p-2">Jumlah</th>
                                        <th class="border p-2">Total</th>
                                        <th class="border p-2">Kadaluarsa</th>
                                        <th class="border p-2">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <template x-for="(item, iIndex) in area.items" :key="iIndex">
                                        <tr>
                                            {{-- BARANG SEARCH --}}
                                            <td class="border p-1 relative">
                                                <input type="hidden"
                                                    :name="`id_barang[${aIndex}][]`"
                                                    :value="item.barang_id">

                                                <div @click="item.open=!item.open"
                                                    class="border rounded px-2 py-1 cursor-pointer bg-white">
                                                    <span x-text="item.barang_nama || 'Pilih Barang'"></span>
                                                </div>

                                                <div x-show="item.open"
                                                    @click.outside="item.open=false"
                                                    class="absolute z-50 bg-white border rounded shadow mt-1 w-full max-h-56 overflow-y-auto">
                                                    <input x-model="item.searchBarang"
                                                        placeholder="Cari barang..."
                                                        class="w-full px-2 py-1 border-b">
                                                    <template x-for="b in (barangByArea[area.area_id] ?? [])
                                                        .filter(x => x.nama.toLowerCase().includes(item.searchBarang.toLowerCase()))">
                                                        <div @click="pilihBarang(aIndex,iIndex,b)"
                                                            class="px-3 py-2 hover:bg-blue-100 cursor-pointer"
                                                            x-text="b.nama"></div>
                                                    </template>
                                                </div>
                                            </td>

                                            {{-- JUMLAH --}}
                                            <td class="border p-1">
                                                <input type="number" min="1"
                                                    x-model.number="item.jumlah"
                                                    @input="hitungTotal(aIndex,iIndex)"
                                                    :name="`jumlah[${aIndex}][]`"
                                                    class="w-full border rounded px-2 py-1">
                                            </td>

                                            {{-- TOTAL (EDITABLE) --}}
                                            <td class="border p-1">
                                                <input type="number" min="0"
                                                    x-model.number="item.harga"
                                                    @input="hitungJumlahDariTotal(aIndex,iIndex)"
                                                    :name="`total_harga[${aIndex}][]`"
                                                    class="w-full border rounded px-2 py-1">
                                            </td>

                                            {{-- EXPIRED --}}
                                            <td class="border p-1">
                                                <input type="date"
                                                    x-model="item.expired"
                                                    :name="`tanggal_kadaluarsa[${aIndex}][]`"
                                                    class="w-full border rounded px-2 py-1">
                                            </td>

                                            <td class="border p-1 text-center">
                                                <button type="button"
                                                    @click="removeItem(aIndex,iIndex)"
                                                    class="text-red-600 font-bold">×</button>
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>

                            <button type="button" @click="addItem(aIndex)"
                                class="mt-2 text-sm bg-gray-200 px-3 py-1 rounded">
                                + Tambah Barang
                            </button>
                        </div>
                    </template>

                    <button type="button" @click="addArea()"
                        class="bg-green-600 text-white px-4 py-2 rounded-lg">
                        + Tambah Area
                    </button>

                    <div class="flex justify-end gap-3 mt-6">
                        <a href="{{ route('admin.transaksibarang.index') }}"
                            class="bg-gray-300 px-5 py-2 rounded-lg">Batal</a>
                        <button type="submit"
                            class="bg-blue-600 text-white px-6 py-2 rounded-lg font-semibold">
                            Simpan
                        </button>
                    </div>
                </form>
            </section>

        </div>
    </main>
</div>

<script>
function transaksiBarang() {
    return {
        view: 'warung',
        search: '',
        rencanaByWarung: @json($rencanaBelanjaByWarung),
        rencanaByBarang: @json($rencanaBelanjaByBarang),
        areas: @json($areas),
        barangByArea: @json($barangByArea),

        transaksi: [{
            area_id: '',
            items: [{
                barang_id: '',
                barang_nama: '',
                jumlah: 1,
                harga: 0,
                expired: '',
                open: false,
                searchBarang: ''
            }]
        }],

        addArea() {
            this.transaksi.push({
                area_id: '',
                items: [{
                    barang_id: '',
                    barang_nama: '',
                    jumlah: 1,
                    harga: 0,
                    expired: '',
                    open: false,
                    searchBarang: ''
                }]
            });
        },

        addItem(a) {
            this.transaksi[a].items.push({
                barang_id: '',
                barang_nama: '',
                jumlah: 1,
                harga: 0,
                expired: '',
                open: false,
                searchBarang: ''
            });
        },

        removeArea(i) {
            if (this.transaksi.length > 1) this.transaksi.splice(i, 1);
        },

        removeItem(a,i) {
            if (this.transaksi[a].items.length > 1)
                this.transaksi[a].items.splice(i, 1);
        },

        pilihBarang(a,i,b) {
            const item = this.transaksi[a].items[i];
            item.barang_id = b.id;
            item.barang_nama = b.nama;
            item.open = false;
            this.hitungTotal(a,i);
        },

        hitungTotal(a,i) {
            const item = this.transaksi[a].items[i];
            const b = this.barangByArea[this.transaksi[a].area_id]
                ?.find(x => x.id == item.barang_id);
            if (b) item.harga = b.harga * item.jumlah;
        },

        hitungJumlahDariTotal(a,i) {
            const item = this.transaksi[a].items[i];
            const b = this.barangByArea[this.transaksi[a].area_id]
                ?.find(x => x.id == item.barang_id);
            if (b && b.harga > 0)
                item.jumlah = Math.max(1, Math.round(item.harga / b.harga));
        }
    }
}
</script>
@endsection
