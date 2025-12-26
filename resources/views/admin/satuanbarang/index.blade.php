@extends('layouts.admin')

@section('content')
    <div class="p-4 bg-gray-50 min-h-screen" x-data="satuanBarangManager()">
        <div class="flex flex-col md:flex-row md:items-center justify-between mb-4 gap-4">
            <h1 class="text-xl font-bold text-gray-800">Manajemen Satuan Barang</h1>
            <div class="flex gap-2">
                <a href="{{ route('admin.satuan.index') }}"
                    class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold py-2 px-6 rounded-full shadow-sm transition">
                    Master Satuan
                </a>
            </div>
        </div>

        {{-- LIVE FILTER FORM --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-3 mb-6">
            <input type="text" x-model="search" placeholder="Cari nama barang..."
                class="w-full border-gray-300 rounded-lg p-2 focus:ring-blue-500 shadow-sm">

            <select x-model="filterKategori" class="w-full border-gray-300 rounded-lg p-2 shadow-sm">
                <option value="">Semua Kategori</option>
                @foreach ($kategoris as $k)
                    <option value="{{ $k->id }}">{{ $k->kategori }}</option>
                @endforeach
            </select>
        </div>

        {{-- GRID BARANG --}}
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-3">
            @foreach ($barang as $b)
                <div class="bg-white border border-gray-100 rounded-lg shadow-sm p-3 flex flex-col justify-between hover:border-blue-300 transition"
                    x-show="matchesFilter('{{ strtolower($b->nama_barang) }}', '{{ $b->subKategori->id_kategori ?? 0 }}')">

                    <div>
                        <h3 class="font-bold text-gray-800 text-[12px] leading-tight line-clamp-2 mb-1">
                            {{ $b->nama_barang }}
                        </h3>
                        <div class="text-[10px] font-semibold text-blue-500 truncate mb-2">
                            {{ $b->subKategori->sub_kategori ?? 'Tanpa Sub' }}
                        </div>

                        {{-- List Satuan Terpasang --}}
                        <div class="flex flex-wrap gap-1 mt-2">
                            @forelse ($b->satuan as $s)
                                <div class="group relative px-1.5 py-0.5 bg-blue-50 text-blue-600 rounded text-[10px] font-bold border border-blue-100 flex items-center">
                                    {{ $s->nama_satuan }}
                                    
                                    {{-- Tombol Hapus dengan Konversi ke Form --}}
                                    <button type="button" 
                                        @click="confirmDelete('{{ route('admin.satuan-barang.destroy', $s->pivot->id) }}')"
                                        class="text-red-400 hover:text-red-600 ml-1 font-black">
                                        ×
                                    </button>
                                </div>
                            @empty
                                <span class="text-gray-400 text-[10px] italic">Belum ada satuan</span>
                            @endforelse
                        </div>
                    </div>

                    <button
                        @click="openModal({{ $b->id }}, '{{ $b->nama_barang }}', {{ json_encode($b->satuan->pluck('id')) }})"
                        class="mt-3 w-full py-1 bg-green-500 hover:bg-green-600 text-white text-[11px] font-bold rounded-md transition">
                        + Satuan
                    </button>
                </div>
            @endforeach
        </div>

        {{-- HIDDEN DELETE FORM (Untuk Keamanan) --}}
        <form id="delete-form" method="POST" class="hidden">
            @csrf @method('DELETE')
        </form>

        {{-- MODAL TAMBAH SATUAN --}}
        <div x-show="isModalOpen" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50"
            x-cloak x-transition>
            <div class="bg-white rounded-lg w-full max-w-md p-6 shadow-xl" @click.away="isModalOpen = false">
                <h2 class="text-lg font-bold mb-4 text-gray-800">Tambah Satuan: <span class="text-blue-600" x-text="selectedBarangNama"></span></h2>

                <form action="{{ route('admin.satuan-barang.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="id_barang" :value="selectedBarangId">

                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Pilih Satuan Baru</label>
                        <select name="id_satuan" class="w-full border-gray-300 rounded-lg p-2 focus:ring-blue-500" required>
                            <option value="">-- Pilih Satuan --</option>
                            @foreach ($list_satuan as $s)
                                <option value="{{ $s->id }}"
                                    x-bind:disabled="existingSatuanIds.includes({{ $s->id }})"
                                    x-bind:class="existingSatuanIds.includes({{ $s->id }}) ? 'bg-gray-100 text-gray-400' : ''">
                                    {{ $s->nama_satuan }} (Isi: {{ $s->jumlah }})
                                </option>
                            @endforeach
                        </select>
                        <p class="text-[10px] text-gray-400 mt-1">*Satuan yang sudah ada tidak dapat dipilih kembali.</p>
                    </div>

                    <div class="flex justify-end gap-2">
                        <button type="button" @click="isModalOpen = false"
                            class="px-4 py-2 text-gray-500 hover:text-gray-700 font-semibold">Batal</button>
                        <button type="submit"
                            class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-bold shadow-md transition">
                            Simpan
                        </button>
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
                isModalOpen: false,
                selectedBarangId: null,
                selectedBarangNama: '',
                existingSatuanIds: [],

                matchesFilter(nama, kategoriId) {
                    const matchSearch = nama.includes(this.search.toLowerCase());
                    const matchKat = this.filterKategori === '' || kategoriId.toString() === this.filterKategori.toString();
                    return matchSearch && matchKat;
                },

                openModal(id, nama, existingIds) {
                    this.selectedBarangId = id;
                    this.selectedBarangNama = nama;
                    // existingIds berisi array ID dari tabel satuan yang sudah dimiliki barang ini
                    this.existingSatuanIds = existingIds; 
                    this.isModalOpen = true;
                },

                confirmDelete(url) {
                    if (confirm('Apakah Anda yakin ingin menghapus satuan ini dari barang?')) {
                        const form = document.getElementById('delete-form');
                        form.action = url;
                        form.submit();
                    }
                }
            }
        }
    </script>

    <style>
        [x-cloak] { display: none !important; }
        select option:disabled { color: #9ca3af; font-style: italic; }
    </style>
@endsection