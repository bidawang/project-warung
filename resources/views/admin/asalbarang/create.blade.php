@extends('layouts.admin')

@section('content')
<div class="p-6">
    <h1 class="text-2xl font-bold mb-4">Atur Asal Barang</h1>
    <a href="{{ route('admin.asalbarang.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold py-2 px-6 rounded-full transition-colors duration-200">Kembali</a>

    <form action="{{ route('admin.asalbarang.store') }}" method="POST">
        @csrf

        {{-- Area Pembelian --}}
        <label class="font-semibold">Area Pembelian <span class="text-red-500">*</span></label>
        <select name="id_area_pembelian" id="areaSelect"
            class="w-full border p-2 rounded mt-2 mb-6" required>
            <option value="">-- Pilih Area --</option>
            @foreach ($areas as $area)
                <option value="{{ $area->id }}"
                    {{ $areaId == $area->id ? 'selected' : '' }}>
                    {{ $area->area }}
                </option>
            @endforeach
        </select>
        @error('id_area_pembelian')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
        @enderror

        {{-- FILTER (TERSEMBUNYI HINGGA AREA DIPILIH) --}}
        <div id="filterContainer" class="hidden">
            <hr class="my-4">
            <h2 class="text-xl font-semibold mb-3">Filter Barang</h2>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                {{-- Kategori Filter --}}
                <div>
                    <label for="kategoriSelect" class="block font-semibold mb-1">Kategori</label>
                    <select id="kategoriSelect" class="w-full border p-2 rounded">
                        <option value="">Semua Kategori</option>
                        @foreach ($kategoris as $kategori)
                            <option value="{{ $kategori->id }}">{{ $kategori->kategori }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Subkategori Filter --}}
                <div>
                    <label for="subkategoriSelect" class="block font-semibold mb-1">Subkategori</label>
                    <select id="subkategoriSelect" class="w-full border p-2 rounded">
                        <option value="">Semua Subkategori</option>
                        {{-- Opsi subkategori akan diisi oleh JS --}}
                    </select>
                </div>

                {{-- Search Filter --}}
                <div>
                    <label for="searchInput" class="block font-semibold mb-1">Cari Barang</label>
                    <input type="text" id="searchInput" placeholder="Nama Barang..."
                           class="w-full border p-2 rounded">
                </div>
            </div>
            <hr class="my-4">
        </div>


        {{-- TABEL BARANG (TERSEMBUNYI HINGGA AREA DIPILIH) --}}
        <div id="barangListContainer" class="{{ $areaId ? '' : 'hidden' }}">
            <h2 class="text-xl font-semibold mb-3">Daftar Barang</h2>
            <div id="barangList">
                {{-- Konten awal dimuat jika areaId sudah ada di request --}}
                @include('admin.asalbarang.partials.barang-list', [
                    'barangs' => $barangs,
                    'selected' => $selected
                ])
            </div>
        </div>

        <button id="saveButton" class="mt-6 bg-blue-600 text-white px-6 py-2 rounded {{ $areaId ? '' : 'hidden' }}">
            Simpan
        </button>
    </form>
</div>

<script>
    // Data Subkategori dari PHP untuk JS
    const allSubkategoris = @json($allSubkategoris);

    const areaSelect = document.getElementById('areaSelect');
    const filterContainer = document.getElementById('filterContainer');
    const barangListContainer = document.getElementById('barangListContainer');
    const barangList = document.getElementById('barangList');
    const kategoriSelect = document.getElementById('kategoriSelect');
    const subkategoriSelect = document.getElementById('subkategoriSelect');
    const searchInput = document.getElementById('searchInput');
    const saveButton = document.getElementById('saveButton');

    // Cek status awal (jika area_id sudah ada di URL)
    if (areaSelect.value) {
        filterContainer.classList.remove('hidden');
    }

    // 1. Logika Pemilihan Area
    areaSelect.addEventListener('change', function() {
        const areaId = this.value;

        if (areaId) {
            // Tampilkan Filter dan Daftar Barang
            filterContainer.classList.remove('hidden');
            barangListContainer.classList.remove('hidden');
            saveButton.classList.remove('hidden');

            // Reset Filter saat area berubah
            kategoriSelect.value = '';
            subkategoriSelect.innerHTML = '<option value="">Semua Subkategori</option>';
            searchInput.value = '';

            // Lakukan pemuatan barang awal
            fetchBarang();

            // Set ID Area ke input tersembunyi jika perlu, tapi kita bisa ambil langsung dari areaSelect
            // Update: Karena form menggunakan input 'id_area_pembelian', areaSelect sudah benar.
        } else {
            // Sembunyikan jika Area Pembelian di-reset
            filterContainer.classList.add('hidden');
            barangListContainer.classList.add('hidden');
            saveButton.classList.add('hidden');
            barangList.innerHTML = '';
        }
    });

    // 2. Logika Dynamic Subkategori
    kategoriSelect.addEventListener('change', function() {
        const selectedKategoriId = parseInt(this.value);
        subkategoriSelect.innerHTML = '<option value="">Semua Subkategori</option>'; // Reset

        if (selectedKategoriId) {
            // Filter subkategori berdasarkan kategori yang dipilih
            const filteredSubkategoris = allSubkategoris.filter(sub => sub.id_kategori === selectedKategoriId);

            filteredSubkategoris.forEach(sub => {
                const option = document.createElement('option');
                option.value = sub.id;
                option.textContent = sub.sub_kategori;
                subkategoriSelect.appendChild(option);
            });
        }

        // Panggil filter barang setelah kategori berubah
        fetchBarang();
    });

    // 3. Logika Filtering Barang (AJAX)
    const filterElements = [subkategoriSelect, searchInput];

    filterElements.forEach(element => {
        element.addEventListener('change', fetchBarang);
    });

    // Debounce untuk input pencarian agar tidak memanggil AJAX terlalu cepat
    let searchTimeout;
    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(fetchBarang, 500); // Tunggu 500ms setelah user berhenti mengetik
    });

    function fetchBarang() {
        const areaId = areaSelect.value;
        const kategoriId = kategoriSelect.value;
        const subkategoriId = subkategoriSelect.value;
        const search = searchInput.value;

        if (!areaId) return; // Jangan panggil jika area belum dipilih

        // Sediakan Query Params
        const params = new URLSearchParams({
            area_id: areaId,
            kategori_id: kategoriId,
            subkategori_id: subkategoriId,
            search: search
        });

        // Tampilkan loading state
        barangList.innerHTML = '<p class="p-4 text-center">Memuat daftar barang...</p>';

        // Panggil endpoint AJAX
        fetch(`{{ route('admin.asalbarang.filter') }}?${params.toString()}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.text())
        .then(html => {
            // Update konten daftar barang
            barangList.innerHTML = html;
        })
        .catch(error => {
            console.error('Error fetching barang:', error);
            barangList.innerHTML = '<p class="p-4 text-center text-red-500">Gagal memuat barang.</p>';
        });
    }

    // Panggil sekali saat load jika areaId sudah terpilih di awal
    @if($areaId)
        // Inisialisasi Subkategori setelah halaman dimuat (jika ada data lama di form)
        // Karena kita sudah mereset di 'change' listener, ini mungkin tidak diperlukan
        // jika areaId selalu diambil dari URL saat load.
        fetchBarang();
    @endif
</script>
@endsection
