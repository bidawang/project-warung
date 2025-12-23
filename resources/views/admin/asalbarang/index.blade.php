@extends('layouts.admin')

@section('content')
<div class="p-4 bg-gray-50 min-h-screen">
    <div class="flex flex-col md:flex-row md:items-center justify-between mb-4 gap-4">
        <h1 class="text-xl font-bold text-gray-800">Asal Barang</h1>
        <a href="{{ route('admin.asalbarang.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-full shadow-sm transition">
            Kelola Barang
        </a>
    </div>

    {{-- NAV TAB AREA --}}
    <div class="flex space-x-2 mb-4 border-b overflow-x-auto pb-1 italic text-xs">
        <a href="{{ route('admin.asalbarang.index') }}" 
           class="px-3 py-1 rounded-t-lg {{ !$areaId ? 'bg-white border-t border-x font-bold text-blue-600' : 'text-gray-500' }}">
           Semua Barang
        </a>
        @foreach ($areas as $a)
            <a href="{{ route('admin.asalbarang.index', ['area_id' => $a->id]) }}" 
               class="px-3 py-1 rounded-t-lg {{ ($areaId == $a->id) ? 'bg-white border-t border-x font-bold text-blue-600' : 'text-gray-500' }}">
                {{ $a->area }}
            </a>
        @endforeach
    </div>

    {{-- LIVE FILTER FORM --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-3 mb-6">
        <div class="relative">
            <input type="text" id="js-search" placeholder="Cari nama barang..." 
                   class="w-full border-gray-300 rounded-lg p-2 focus:ring-blue-500 focus:border-blue-500 shadow-sm">
        </div>
        <div>
            <select id="js-kategori" class="w-full border-gray-300 rounded-lg p-2 shadow-sm">
                <option value="">Semua Kategori</option>
                @foreach($kategoris as $k)
                    <option value="{{ $k->id }}">{{ $k->kategori }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <select id="js-subkategori" class="w-full border-gray-300 rounded-lg p-2 shadow-sm">
                <option value="">Semua Subkategori</option>
                @foreach($subkategoris as $s)
                    <option value="{{ $s->id }}" data-kategori="{{ $s->id_kategori }}">{{ $s->sub_kategori }}</option>
                @endforeach
            </select>
        </div>
    </div>

    {{-- GRID COMPACT --}}
    <div id="barang-grid" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-3">
        @forelse ($barangs as $b)
            <div class="barang-item bg-white border border-gray-100 rounded-lg shadow-sm p-3 flex flex-col justify-between hover:border-blue-300 transition"
                 data-nama="{{ strtolower($b->nama_barang) }}"
                 data-kategori="{{ $b->subKategori->id_kategori ?? 0 }}"
                 data-subkategori="{{ $b->id_sub_kategori ?? 0 }}">
                
                <div>
                    <h3 class="font-bold text-gray-800 text-[12px] leading-tight line-clamp-2 mb-1" title="{{ $b->nama_barang }}">
                        {{ $b->nama_barang }}
                    </h3>
                    <div class="text-[10px] text-gray-400 leading-none">
                        {{ $b->subKategori->kategori->kategori ?? 'N/A' }}
                    </div>
                    <div class="text-[10px] font-semibold text-blue-500 truncate mb-2">
                        {{ $b->subKategori->sub_kategori ?? 'N/A' }}
                    </div>
                </div>

                <div class="pt-2 border-t border-gray-50 mt-1">
                    <div class="flex flex-wrap gap-1">
                        @forelse ($b->areaPembelian as $ar)
                            <span class="px-1.5 py-0.5 bg-gray-100 text-gray-600 rounded text-[12px] font-medium border border-gray-200">
                                {{ $ar->area }}
                            </span>
                        @empty
                            <span class="text-red-400 text-[12px] italic">No Area</span>
                        @endforelse
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full py-10 text-center text-gray-400 italic">
                Data barang kosong.
            </div>
        @endforelse
    </div>

    {{-- No Results Message --}}
    <div id="no-results" class="hidden col-span-full py-10 text-center text-gray-500">
        Tidak ada barang yang cocok dengan filter.
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('js-search');
    const kategoriSelect = document.getElementById('js-kategori');
    const subkategoriSelect = document.getElementById('js-subkategori');
    const barangItems = document.querySelectorAll('.barang-item');
    const subOptions = Array.from(subkategoriSelect.options);
    const noResults = document.getElementById('no-results');

    function filterData() {
        const searchValue = searchInput.value.toLowerCase();
        const katValue = kategoriSelect.value;
        const subValue = subkategoriSelect.value;
        let visibleCount = 0;

        barangItems.forEach(item => {
            const matchesSearch = item.dataset.nama.includes(searchValue);
            const matchesKat = !katValue || item.dataset.kategori === katValue;
            const matchesSub = !subValue || item.dataset.subkategori === subValue;

            if (matchesSearch && matchesKat && matchesSub) {
                item.style.display = 'flex';
                visibleCount++;
            } else {
                item.style.display = 'none';
            }
        });

        noResults.classList.toggle('hidden', visibleCount > 0);
    }

    // Dynamic Subkategori Dropdown logic
    kategoriSelect.addEventListener('change', function() {
        const selectedKat = this.value;
        subkategoriSelect.innerHTML = '<option value="">Semua Subkategori</option>';
        
        subOptions.forEach(opt => {
            if (opt.value === "" ) return;
            if (!selectedKat || opt.dataset.kategori === selectedKat) {
                subkategoriSelect.appendChild(opt);
            }
        });
        filterData();
    });

    // Listeners for Live Filter
    searchInput.addEventListener('input', filterData);
    subkategoriSelect.addEventListener('change', filterData);
});
</script>
@endsection