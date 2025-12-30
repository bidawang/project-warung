@extends('layouts.admin')

@section('content')
    <div class="p-4 bg-gray-50 min-h-screen" x-data="satuanBarangManager()">
        <div class="flex flex-col md:flex-row md:items-center justify-between mb-4 gap-4">
            <h1 class="text-xl font-bold text-gray-800">Manajemen Satuan Barang</h1>
            <div class="flex gap-2">
                <a href="{{ route('admin.satuan.index') }}"
                    class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold py-2 px-6 rounded-full shadow-sm transition text-sm">
                    Master Satuan
                </a>
            </div>
        </div>

        {{-- LIVE FILTER FORM --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-3 mb-6">
            <div class="relative">
                <input type="text" x-model="search" placeholder="Cari nama barang..."
                    class="w-full border-gray-300 rounded-lg p-2 focus:ring-blue-500 shadow-sm">
            </div>

            <div>
                <select x-model="filterKategori" @change="filterSub = ''"
                    class="w-full border-gray-300 rounded-lg p-2 shadow-sm">
                    <option value="">Semua Kategori</option>
                    @foreach ($kategoris as $k)
                        <option value="{{ $k->id }}">{{ $k->kategori }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <select x-model="filterSub" class="w-full border-gray-300 rounded-lg p-2 shadow-sm">
                    <option value="">Semua Subkategori</option>
                    <template x-for="sub in filteredSubOptions" :key="sub.id">
                        <option :value="sub.id" x-text="sub.nama"></option>
                    </template>
                </select>
            </div>
        </div>

        {{-- GRID BARANG --}}
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-3">
            @foreach ($barang as $b)
                <div class="bg-white border border-gray-100 rounded-lg shadow-sm p-3 flex flex-col justify-between hover:border-blue-300 transition"
                    x-show="matchesFilter('{{ strtolower($b->nama_barang) }}', '{{ $b->subKategori->id_kategori ?? 0 }}', '{{ $b->id_sub_kategori ?? 0 }}')">

                    <div>
                        <div class="flex justify-between items-start mb-1">
                            {{-- TOMBOL SHOW --}}
                            <button @click="openShowModal({{ json_encode($b) }}, {{ json_encode($b->satuan) }})"
                                class="text-blue-500 hover:text-blue-700 text-[10px] font-bold">
                                SHOW
                            </button>
                        </div>

                        <h3 class="font-bold text-gray-800 text-[12px] leading-tight line-clamp-2 mb-1">
                            {{ $b->nama_barang }}
                        </h3>
                        <div class="text-[10px] font-semibold text-blue-500 truncate mb-2">
                            {{ $b->subKategori->sub_kategori ?? 'Tanpa Sub' }}
                        </div>

                        <div class="flex flex-wrap gap-1 mt-2">
                            @forelse ($b->satuan as $s)
                                <div
                                    class="px-1.5 py-0.5 bg-blue-50 text-blue-600 rounded text-[9px] font-bold border border-blue-100 flex items-center">
                                    {{ $s->nama_satuan }}
                                    <button type="button"
                                        @click="confirmDelete('{{ route('admin.satuan-barang.destroy', $s->pivot->id) }}')"
                                        class="text-red-400 hover:text-red-600 ml-1 font-black">×</button>
                                </div>
                            @empty
                                <span class="text-gray-400 text-[9px] italic">Belum ada satuan</span>
                            @endforelse
                        </div>
                    </div>

                    <button
                        @click="openModal({{ $b->id }}, '{{ $b->nama_barang }}', {{ json_encode($b->satuan->pluck('id')) }})"
                        class="mt-3 w-full py-1 bg-green-500 hover:bg-green-600 text-white text-[11px] font-bold rounded-md transition shadow-sm">
                        + Satuan
                    </button>
                </div>
            @endforeach
        </div>

        {{-- MODAL SHOW (DETAIL) --}}
        <div x-show="isShowModalOpen"
            class="fixed inset-0 z-[60] flex items-center justify-center bg-black bg-opacity-60 p-4" x-cloak x-transition>
            <div class="bg-white rounded-xl w-full max-w-sm overflow-hidden shadow-2xl">
                <div class="bg-blue-600 p-4 text-white">
                    <h2 class="font-bold text-lg" x-text="detailBarang.nama_barang"></h2>
                    <p class="text-blue-100 text-xs"
                        x-text="'Kategori: ' + (detailBarang.sub_kategori?.kategori?.kategori || '-')"></p>
                </div>
                <div class="p-4">
                    <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Daftar Satuan Konversi</h3>
                    <div class="space-y-2">
                        <template x-for="s in detailSatuans" :key="s.id">
                            <div class="flex justify-between items-center bg-gray-50 p-2 rounded-lg border border-gray-100">
                                <span class="font-bold text-gray-700" x-text="s.nama_satuan"></span>
                                <span class="text-sm text-blue-600 font-black" x-text="'Isi: ' + s.jumlah"></span>
                            </div>
                        </template>
                    </div>
                </div>
                <div class="p-4 bg-gray-50 flex justify-end">
                    <button @click="isShowModalOpen = false"
                        class="px-6 py-2 bg-gray-800 text-white rounded-lg font-bold text-sm">Tutup</button>
                </div>
            </div>
        </div>

        {{-- FORM DELETE HIDDEN --}}
        <form id="delete-form" method="POST" class="hidden">
            @csrf @method('DELETE')
        </form>

        {{-- MODAL TAMBAH SATUAN --}}
        <div x-show="isModalOpen" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50" x-cloak
            x-transition>
            <div class="bg-white rounded-lg w-full max-w-md p-6 shadow-xl" @click.away="isModalOpen = false">
                <h2 class="text-lg font-bold mb-4 text-gray-800">Tambah Satuan: <span class="text-blue-600"
                        x-text="selectedBarangNama"></span></h2>
                <form action="{{ route('admin.satuan-barang.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="id_barang" :value="selectedBarangId">
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Pilih Satuan Baru</label>
                        <select name="id_satuan" class="w-full border-gray-300 rounded-lg p-2 focus:ring-blue-500" required>
                            <option value="">-- Pilih Satuan --</option>
                            @foreach ($list_satuan as $s)
                                <option value="{{ $s->id }}"
                                    x-bind:disabled="existingSatuanIds.includes({{ $s->id }})">
                                    {{ $s->nama_satuan }} (Isi: {{ $s->jumlah }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex justify-end gap-2">
                        <button type="button" @click="isModalOpen = false"
                            class="px-4 py-2 text-gray-500 font-semibold">Batal</button>
                        <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg font-bold">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function satuanBarangManager() {
            return {
                search: '',
                filterKategori: '',
                filterSub: '',
                isModalOpen: false,
                isShowModalOpen: false,
                selectedBarangId: null,
                selectedBarangNama: '',
                existingSatuanIds: [],
                detailBarang: {},
                detailSatuans: [],

                // Data Subkategori dari Backend ke JS
                allSubOptions: [
                    @foreach ($subkategoris as $s)
                        {
                            id: '{{ $s->id }}',
                            nama: '{{ $s->sub_kategori }}',
                            id_kat: '{{ $s->id_kategori }}'
                        },
                    @endforeach
                ],

                get filteredSubOptions() {
                    if (!this.filterKategori) return this.allSubOptions;
                    return this.allSubOptions.filter(s => s.id_kat == this.filterKategori);
                },

                matchesFilter(nama, kategoriId, subId) {
                    const matchSearch = nama.includes(this.search.toLowerCase());
                    const matchKat = !this.filterKategori || kategoriId.toString() === this.filterKategori.toString();
                    const matchSub = !this.filterSub || subId.toString() === this.filterSub.toString();
                    return matchSearch && matchKat && matchSub;
                },

                openModal(id, nama, existingIds) {
                    this.selectedBarangId = id;
                    this.selectedBarangNama = nama;
                    this.existingSatuanIds = existingIds;
                    this.isModalOpen = true;
                },

                openShowModal(barang, satuans) {
                    this.detailBarang = barang;
                    this.detailSatuans = satuans;
                    this.isShowModalOpen = true;
                },

                confirmDelete(url) {
                    if (confirm('Hapus satuan ini?')) {
                        const form = document.getElementById('delete-form');
                        form.action = url;
                        form.submit();
                    }
                }
            }
        }
    </script>
@endsection
