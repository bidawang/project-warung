@extends('layouts.admin')

@section('title', 'Atur Asal Barang')

@section('content')
<div class="p-4 bg-gray-50 min-h-screen">
    {{-- HEADER AREA --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between mb-6 gap-4">
        <div>
            <h1 class="text-xl font-bold text-gray-800">Atur Asal Barang</h1>
            <p class="text-sm text-gray-500">Hubungkan barang dengan area pembelian tertentu</p>
        </div>
        <a href="{{ route('admin.asalbarang.index') }}" 
           class="bg-white border border-gray-300 hover:bg-gray-100 text-gray-700 font-bold py-2 px-6 rounded-full shadow-sm transition text-center">
            Kembali
        </a>
    </div>

    <form action="{{ route('admin.asalbarang.store') }}" method="POST">
        @csrf

        {{-- AREA SELECTION CARD --}}
        <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm mb-6">
            <label class="block text-sm font-bold text-gray-700 mb-2">Area Pembelian <span class="text-red-500">*</span></label>
            <select name="id_area_pembelian" id="areaSelect"
                class="w-full md:w-1/3 border-gray-300 rounded-lg p-2.5 focus:ring-blue-500 focus:border-blue-500 shadow-sm transition" required>
                <option value="">-- Pilih Area --</option>
                @foreach ($areas as $area)
                    <option value="{{ $area->id }}" {{ $areaId == $area->id ? 'selected' : '' }}>
                        {{ $area->area }}
                    </option>
                @endforeach
            </select>
            @error('id_area_pembelian')
                <p class="text-red-500 text-xs mt-2 italic">{{ $message }}</p>
            @enderror
        </div>

        {{-- FILTER AREA (HIDDEN UNTIL AREA SELECTED) --}}
        <div id="filterContainer" class="hidden animate-fadeIn">
            <div class="mb-4 flex items-center gap-2">
                <div class="h-px bg-gray-200 flex-1"></div>
                <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">Filter Pencarian Barang</span>
                <div class="h-px bg-gray-200 flex-1"></div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1 uppercase">Kategori</label>
                    <select id="kategoriSelect" class="w-full border-gray-300 rounded-lg p-2 shadow-sm focus:ring-blue-500">
                        <option value="">Semua Kategori</option>
                        @foreach ($kategoris as $kategori)
                            <option value="{{ $kategori->id }}">{{ $kategori->kategori }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1 uppercase">Subkategori</label>
                    <select id="subkategoriSelect" class="w-full border-gray-300 rounded-lg p-2 shadow-sm focus:ring-blue-500">
                        <option value="">Semua Subkategori</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1 uppercase">Cari Nama Barang</label>
                    <div class="relative">
                        <input type="text" id="searchInput" placeholder="Ketik nama barang..."
                               class="w-full border-gray-300 rounded-lg p-2 pl-9 shadow-sm focus:ring-blue-500">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- BARANG LIST AREA --}}
        <div id="barangListContainer" class="{{ $areaId ? '' : 'hidden' }} mb-20">
            <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                <span class="w-2 h-6 bg-blue-600 rounded-full mr-2"></span>
                Daftar Barang
            </h2>
            
            <div id="barangList" class="min-h-[200px]">
                @include('admin.asalbarang.partials.barang-list', [
                    'barangs' => $barangs,
                    'selected' => $selected
                ])
            </div>
        </div>

        {{-- FLOATING ACTION BAR --}}
        <div id="saveButton" class="{{ $areaId ? 'flex' : 'hidden' }} fixed bottom-0 left-0 right-0 md:left-64 bg-white border-t border-gray-200 p-4 justify-end shadow-[0_-4px_10px_rgba(0,0,0,0.05)] z-10">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2.5 px-10 rounded-full shadow-lg transition transform hover:scale-105 active:scale-95">
                Simpan Perubahan
            </button>
        </div>
    </form>
</div>

<script>
    // JS Logic tetap sama, namun pastikan selector ID sesuai
    const allSubkategoris = @json($allSubkategoris);
    const areaSelect = document.getElementById('areaSelect');
    const filterContainer = document.getElementById('filterContainer');
    const barangListContainer = document.getElementById('barangListContainer');
    const barangList = document.getElementById('barangList');
    const kategoriSelect = document.getElementById('kategoriSelect');
    const subkategoriSelect = document.getElementById('subkategoriSelect');
    const searchInput = document.getElementById('searchInput');
    const saveButton = document.getElementById('saveButton');

    if (areaSelect.value) {
        filterContainer.classList.remove('hidden');
    }

    areaSelect.addEventListener('change', function() {
        const areaId = this.value;
        if (areaId) {
            filterContainer.classList.remove('hidden');
            barangListContainer.classList.remove('hidden');
            saveButton.classList.remove('hidden');
            saveButton.classList.add('flex');
            kategoriSelect.value = '';
            subkategoriSelect.innerHTML = '<option value="">Semua Subkategori</option>';
            searchInput.value = '';
            fetchBarang();
        } else {
            filterContainer.classList.add('hidden');
            barangListContainer.classList.add('hidden');
            saveButton.classList.add('hidden');
            saveButton.classList.remove('flex');
            barangList.innerHTML = '';
        }
    });

    kategoriSelect.addEventListener('change', function() {
        const selectedKategoriId = parseInt(this.value);
        subkategoriSelect.innerHTML = '<option value="">Semua Subkategori</option>';
        if (selectedKategoriId) {
            const filteredSubkategoris = allSubkategoris.filter(sub => sub.id_kategori === selectedKategoriId);
            filteredSubkategoris.forEach(sub => {
                const option = document.createElement('option');
                option.value = sub.id;
                option.textContent = sub.sub_kategori;
                subkategoriSelect.appendChild(option);
            });
        }
        fetchBarang();
    });

    subkategoriSelect.addEventListener('change', fetchBarang);
    
    let searchTimeout;
    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(fetchBarang, 500);
    });

    function fetchBarang() {
        const areaId = areaSelect.value;
        const kategoriId = kategoriSelect.value;
        const subkategoriId = subkategoriSelect.value;
        const search = searchInput.value;

        if (!areaId) return;

        const params = new URLSearchParams({
            area_id: areaId,
            kategori_id: kategoriId,
            subkategori_id: subkategoriId,
            search: search
        });

        barangList.innerHTML = `
            <div class="flex flex-col items-center justify-center py-12 bg-white rounded-xl border border-dashed border-gray-300">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mb-2"></div>
                <p class="text-gray-500 text-sm">Memproses data barang...</p>
            </div>
        `;

        fetch(`{{ route('admin.asalbarang.filter') }}?${params.toString()}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(response => response.text())
        .then(html => {
            barangList.innerHTML = html;
        })
        .catch(error => {
            console.error('Error:', error);
            barangList.innerHTML = '<p class="p-4 text-center text-red-500 bg-white rounded-xl border">Gagal memuat barang. Silakan coba lagi.</p>';
        });
    }

    @if($areaId)
        fetchBarang();
    @endif
</script>

<style>
    .animate-fadeIn {
        animation: fadeIn 0.3s ease-in-out;
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>
@endsection