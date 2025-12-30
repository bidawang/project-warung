@extends('layouts.admin')

@section('content')
<div class="p-6 bg-gray-50 min-h-screen" x-data="satuanCrud()">
    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between mb-6 gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Master Satuan</h1>
            <p class="text-sm text-gray-500">Kelola kategori dan variasi isi satuan barang.</p>
        </div>
        <div class="flex items-center gap-3">
            <div class="relative hidden sm:block">
                <input type="text" x-model="search" placeholder="Cari satuan..." 
                    class="pl-10 pr-4 py-2 border-gray-300 rounded-lg focus:ring-blue-500 text-sm shadow-sm">
                <div class="absolute left-3 top-2.5 text-gray-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
            </div>

            <button @click="openAddModal()" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-lg shadow-md flex items-center gap-2 transition-all">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                </svg>
                Tambah Satuan
            </button>
        </div>
    </div>

    {{-- Tabel --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-visible"> {{-- Ubah ke overflow-visible agar card tidak terpotong --}}
        <table class="w-full text-left border-collapse">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase">Kategori</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase">Nama Satuan (Label)</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase text-center">Isi</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase text-center">Digunakan</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($satuan as $s)
                    <tr class="hover:bg-gray-50 transition" x-show="matchesSearch('{{ strtolower($s->nama_satuan) }} {{ strtolower($s->kategori_satuan) }}')">
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 bg-blue-50 text-blue-700 rounded text-xs font-bold uppercase">{{ $s->kategori_satuan }}</span>
                        </td>
                        <td class="px-6 py-4 font-bold text-gray-800">{{ $s->nama_satuan }}</td>
                        <td class="px-6 py-4 text-center font-mono text-sm text-gray-600">{{ number_format($s->jumlah) }}</td>
                        
                        {{-- Kolom Digunakan dengan Popover Card --}}
                        <td class="px-6 py-4 text-center relative">
                            @if($s->barang_count > 0)
                                <button @click="toggleBarangList({{ $s->id }})" 
                                    class="text-xs font-bold px-3 py-1 rounded-full bg-blue-50 text-blue-600 hover:bg-blue-100 transition inline-flex items-center gap-1">
                                    {{ $s->barang_count }} Barang
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path d="M19 9l-7 7-7-7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </button>

                                {{-- Popover Card --}}
                                <div x-show="openBarangId === {{ $s->id }}" 
                                     @click.away="openBarangId = null"
                                     x-transition:enter="transition ease-out duration-200"
                                     x-transition:enter-start="opacity-0 translate-y-2"
                                     x-transition:enter-end="opacity-100 translate-y-0"
                                     class="absolute z-40 left-1/2 -translate-x-1/2 mt-2 w-56 bg-white rounded-xl shadow-xl border border-gray-100 p-4 text-left"
                                     x-cloak>
                                    <div class="flex justify-between items-center mb-2 pb-2 border-b">
                                        <span class="text-[10px] font-black uppercase text-gray-400">Daftar Barang</span>
                                        <button @click="openBarangId = null" class="text-gray-400 hover:text-gray-600">×</button>
                                    </div>
                                    <ul class="max-h-40 overflow-y-auto space-y-2">
                                        @foreach($s->barang as $b)
                                            <li class="text-xs font-medium text-gray-700 flex items-start gap-2">
                                                <span class="mt-1 h-1.5 w-1.5 rounded-full bg-blue-500 shrink-0"></span>
                                                {{ $b->nama_barang }}
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @else
                                <span class="text-xs font-bold text-gray-400">0 Barang</span>
                            @endif
                        </td>

                        <td class="px-6 py-4 text-right flex justify-end gap-2">
                            <button @click="openEditModal({{ json_encode($s) }})" class="p-2 text-yellow-600 hover:bg-yellow-50 rounded-lg">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                            </button>
                            @if($s->barang_count == 0)
                                <form action="{{ route('admin.satuan.destroy', $s->id) }}" method="POST" onsubmit="return confirm('Hapus satuan ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="p-2 text-red-600 hover:bg-red-50 rounded-lg">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-6 py-10 text-center text-gray-400 italic">Data belum tersedia.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- MODAL CRUD --}}
    <div x-show="isOpen" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 backdrop-blur-sm" x-cloak x-transition>
        <div class="bg-white rounded-2xl w-full max-w-md p-8 shadow-2xl relative" @click.away="isOpen = false">
            <h2 class="text-xl font-bold text-gray-800 mb-6" x-text="editMode ? 'Edit Satuan' : 'Tambah Satuan'"></h2>
            
            <form :action="formUrl" method="POST">
                @csrf
                <template x-if="editMode"><input type="hidden" name="_method" value="PUT"></template>

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Kategori Satuan</label>
                        <input type="text" name="kategori_satuan" x-model="formData.kategori_satuan" required list="list-kategori"
                               placeholder="Contoh: Pack, Dus, Box"
                               class="w-full border-gray-300 rounded-xl p-3 focus:ring-2 focus:ring-blue-500 shadow-sm">
                        <datalist id="list-kategori">
                            @foreach($satuan->pluck('kategori_satuan')->unique() as $kat)
                                <option value="{{ $kat }}">
                            @endforeach
                        </datalist>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Nama Satuan (Label)</label>
                        <input type="text" name="nama_satuan" x-model="formData.nama_satuan" required
                               placeholder="Contoh: Pack Isi 10"
                               class="w-full border-gray-300 rounded-xl p-3 focus:ring-2 focus:ring-blue-500 shadow-sm">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Jumlah Isi</label>
                        <input type="number" name="jumlah" x-model="formData.jumlah" required min="1"
                               class="w-full border-gray-300 rounded-xl p-3 focus:ring-2 focus:ring-blue-500 shadow-sm">
                    </div>
                </div>

                <div class="flex justify-end gap-3 mt-8">
                    <button type="button" @click="isOpen = false" class="px-5 py-2.5 text-gray-500 font-bold hover:bg-gray-100 rounded-xl">Batal</button>
                    <button type="submit" class="px-6 py-2.5 bg-blue-600 text-white font-bold rounded-xl hover:bg-blue-700 shadow-md transition-all active:scale-95">Simpan Data</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function satuanCrud() {
    return {
        isOpen: false,
        editMode: false,
        search: '',
        openBarangId: null, // Untuk melacak card barang mana yang terbuka
        formUrl: '{{ route("admin.satuan.store") }}',
        formData: { kategori_satuan: '', nama_satuan: '', jumlah: '1' },

        matchesSearch(text) {
            return text.includes(this.search.toLowerCase());
        },

        toggleBarangList(id) {
            this.openBarangId = this.openBarangId === id ? null : id;
        },

        openAddModal() {
            this.editMode = false;
            this.formUrl = '{{ route("admin.satuan.store") }}';
            this.formData = { kategori_satuan: '', nama_satuan: '', jumlah: '1' };
            this.isOpen = true;
        },

        openEditModal(data) {
            this.editMode = true;
            this.formUrl = `/admin/satuan/${data.id}`;
            this.formData = { 
                kategori_satuan: data.kategori_satuan,
                nama_satuan: data.nama_satuan, 
                jumlah: data.jumlah 
            };
            this.isOpen = true;
        }
    }
}
</script>

<style>
    /* Agar modal atau card tidak tersembunyi jika tabel ditaruh dalam kontainer kecil */
    [x-cloak] { display: none !important; }
</style>
@endsection