@extends('layouts.admin')

@section('title', 'Monitoring Warung - ' . $warung->nama_warung)

@section('content')
<div class="min-h-screen bg-gray-50 pb-12">
    <main class="p-4 md:p-8">
        <div class="max-w-7xl mx-auto">
            
            {{-- Header & Action --}}
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
                <div>
                    <nav class="flex mb-2" aria-label="Breadcrumb">
                        <ol class="inline-flex items-center space-x-1 md:space-x-3 text-sm text-gray-500 font-medium">
                            <li>Warung</li>
                            <li><svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg></li>
                            <li class="text-blue-600">Detail Monitoring</li>
                        </ol>
                    </nav>
                    <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">{{ $warung->nama_warung }}</h1>
                </div>
                <a href="{{ route('admin.warung.index') }}" class="inline-flex items-center px-4 py-2.5 bg-white border border-gray-300 rounded-lg font-semibold text-gray-700 hover:bg-gray-50 shadow-sm transition-all text-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                    Daftar Warung
                </a>
            </div>

            {{-- Top Stats: Info Warung & Laba --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
                {{-- Detail Warung Card --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-4">
                            <span class="bg-blue-50 text-blue-700 text-xs font-bold px-2.5 py-1 rounded-full uppercase tracking-wider">Informasi Bisnis</span>
                            <span class="text-gray-400"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg></span>
                        </div>
                        <div class="space-y-3">
                            <div>
                                <p class="text-xs text-gray-400 uppercase font-bold tracking-widest">Pemilik / Area</p>
                                <p class="text-gray-800 font-medium">{{ $warung->user->name ?? '-' }} <span class="text-gray-300 mx-1">|</span> {{ $warung->area->area ?? '-' }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-400 uppercase font-bold tracking-widest">Modal Awal</p>
                                <p class="text-2xl font-bold text-gray-900">Rp {{ number_format($warung->modal, 0, ',', '.') }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="mt-4 pt-4 border-t border-gray-50">
                        <p class="text-sm text-gray-500 italic">"{{ $warung->keterangan ?? 'Tidak ada keterangan tambahan.' }}"</p>
                    </div>
                </div>

                {{-- Laba Summary Card --}}
                <div class="lg:col-span-2 bg-gradient-to-br from-indigo-700 to-blue-800 rounded-2xl shadow-lg p-6 text-white relative overflow-hidden">
                    <div class="relative z-10 h-full flex flex-col justify-between">
                        <div class="flex justify-between items-start">
                            <h3 class="text-lg font-bold opacity-90 tracking-wide uppercase">Ringkasan Laba</h3>
                            <div class="bg-white/20 px-3 py-1 rounded-lg text-sm font-bold backdrop-blur-md">
                                @if ($labaKotor > 0)
                                    {{ number_format(($labaBersih / $labaKotor) * 100, 1) }}% Margin
                                @else 0% Margin @endif
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 my-4">
                            <div>
                                <p class="text-indigo-200 text-sm mb-1">Total Penjualan</p>
                                <p class="text-2xl font-bold">Rp {{ number_format($labaKotor, 0, ',', '.') }}</p>
                            </div>
                            <div>
                                <p class="text-indigo-200 text-sm mb-1">Total Modal</p>
                                <p class="text-2xl font-bold">Rp {{ number_format($totalModal, 0, ',', '.') }}</p>
                            </div>
                            <div class="bg-white/10 p-4 rounded-xl backdrop-blur-sm border border-white/10">
                                <p class="text-indigo-100 text-sm mb-1">Laba Bersih</p>
                                <p class="text-3xl font-black text-green-300">Rp {{ number_format($labaBersih, 0, ',', '.') }}</p>
                            </div>
                        </div>
                    </div>
                    {{-- Decorative Circle --}}
                    <div class="absolute -right-10 -bottom-10 w-48 h-48 bg-white/5 rounded-full"></div>
                </div>
            </div>

            {{-- Asset Warung Section --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-8">
                <div class="p-6 border-b border-gray-50 flex justify-between items-center bg-white">
                    <div>
                        <h3 class="text-xl font-bold text-gray-800">Manajemen Asset</h3>
                        <p class="text-sm text-gray-500">Daftar inventaris dan progres pelunasan asset</p>
                    </div>
                    <span class="bg-gray-100 text-gray-600 text-xs font-bold px-3 py-1.5 rounded-lg">{{ $assets->count() }} Items</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100">
                        <thead class="bg-gray-50/50">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-widest">Tgl Pembelian</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-widest">Nama Asset</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-widest">Total Harga</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-widest text-green-600">Terbayar</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-widest text-red-500">Sisa</th>
                                <th class="px-6 py-4 text-center text-xs font-bold text-gray-400 uppercase tracking-widest">Status Pelunasan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($assets as $asset)
                                @php
                                    $persen = $asset->harga_asset > 0 ? ($asset->total_dibayar / $asset->harga_asset) * 100 : 0;
                                @endphp
                                <tr class="hover:bg-blue-50/30 transition-colors">
                                    <td class="px-6 py-4 text-sm text-gray-600">{{ \Carbon\Carbon::parse($asset->tanggal_pembelian)->format('d/m/Y') }}</td>
                                    <td class="px-6 py-4 text-sm font-bold text-gray-800">{{ $asset->nama }}</td>
                                    <td class="px-6 py-4 text-sm font-medium">Rp {{ number_format($asset->harga_asset, 0, ',', '.') }}</td>
                                    <td class="px-6 py-4 text-sm font-bold text-green-600">Rp {{ number_format($asset->total_dibayar, 0, ',', '.') }}</td>
                                    <td class="px-6 py-4 text-sm font-bold text-red-500">Rp {{ number_format($asset->sisa_pembayaran, 0, ',', '.') }}</td>
                                    <td class="px-6 py-4">
                                        <div class="flex flex-col items-center min-w-[120px]">
                                            <div class="flex justify-between w-full mb-1 text-[10px] font-bold text-gray-500 uppercase">
                                                <span>{{ $asset->volume_pelunasan }}x Bayar</span>
                                                <span>{{ number_format($persen, 0) }}%</span>
                                            </div>
                                            <div class="w-full bg-gray-100 rounded-full h-1.5">
                                                <div class="bg-blue-600 h-1.5 rounded-full transition-all duration-500" style="width: {{ $persen }}%"></div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="px-6 py-10 text-center text-gray-400 italic">Belum ada data asset tercatat.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Pengeluaran Pokok Section --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
                <div class="lg:col-span-1 space-y-4">
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                        <h3 class="text-lg font-bold text-gray-800 mb-4">Filter & Summary</h3>
                        <label class="block text-xs font-bold text-gray-400 uppercase mb-2">Pilih Periode</label>
                        <input type="month" id="filter-bulan" class="w-full border-gray-200 rounded-xl shadow-sm focus:ring-blue-500 focus:border-blue-500 mb-6">
                        
                        <div class="space-y-3">
                            <div class="p-4 rounded-xl bg-gray-50 flex justify-between items-center">
                                <span class="text-sm text-gray-500 font-medium">Total Estimasi</span>
                                <span id="totalPengeluaran" class="font-bold text-gray-900">-</span>
                            </div>
                            <div class="p-4 rounded-xl bg-green-50 flex justify-between items-center">
                                <span class="text-sm text-green-700 font-medium">Terbayar</span>
                                <span id="totalTerpenuhi" class="font-bold text-green-700">-</span>
                            </div>
                            <div class="p-4 rounded-xl bg-red-50 flex justify-between items-center border border-red-100">
                                <span class="text-sm text-red-700 font-medium">Tunggakan</span>
                                <span id="totalBelum" class="font-bold text-red-700">-</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-5 border-b border-gray-50 flex items-center gap-2 font-bold text-gray-800">
                        <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                        Log Pengeluaran Pokok
                    </div>
                    <div class="overflow-x-auto max-h-[400px]">
                        <table class="min-w-full text-sm divide-y divide-gray-100">
                            <thead class="bg-gray-50/50 sticky top-0">
                                <tr>
                                    <th class="px-6 py-3 text-left font-bold text-gray-400 uppercase text-[10px]">Tanggal</th>
                                    <th class="px-6 py-3 text-left font-bold text-gray-400 uppercase text-[10px]">Keterangan/Redaksi</th>
                                    <th class="px-6 py-3 text-left font-bold text-gray-400 uppercase text-[10px]">Nominal</th>
                                    <th class="px-6 py-3 text-center font-bold text-gray-400 uppercase text-[10px]">Status</th>
                                </tr>
                            </thead>
                            <tbody id="tablePengeluaran" class="divide-y divide-gray-50">
                                <tr><td colspan="4" class="px-6 py-10 text-center text-gray-400">Memproses data...</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Inventory Section --}}
            <div x-data="{ activeTab: 'tersedia' }" class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-6 border-b border-gray-100">
                    <h3 class="text-xl font-bold text-gray-800 mb-4">Inventaris Barang</h3>
                    <nav class="flex space-x-2 bg-gray-100 p-1 rounded-xl w-fit">
                        <button @click="activeTab = 'tersedia'" :class="activeTab === 'tersedia' ? 'bg-white shadow-sm text-blue-600' : 'text-gray-500 hover:text-gray-700'" class="px-4 py-2 text-sm font-bold rounded-lg transition-all">Tersedia</button>
                        <button @click="activeTab = 'kosong'" :class="activeTab === 'kosong' ? 'bg-white shadow-sm text-red-600' : 'text-gray-500 hover:text-gray-700'" class="px-4 py-2 text-sm font-bold rounded-lg transition-all">Stok Kosong</button>
                        <button @click="activeTab = 'semua'" :class="activeTab === 'semua' ? 'bg-white shadow-sm text-gray-800' : 'text-gray-500 hover:text-gray-700'" class="px-4 py-2 text-sm font-bold rounded-lg transition-all">Semua Barang</button>
                    </nav>
                </div>

                <div class="p-0">
                    @php
                        $semua = $barangWithStok;
                        $tersedia = $barangWithStok->filter(fn($barang) => ($barang->stok_saat_ini ?? 0) > 0);
                        $kosong = $barangWithStok->filter(fn($barang) => ($barang->stok_saat_ini ?? 0) <= 0);
                        $tabs = ['semua' => $semua, 'tersedia' => $tersedia, 'kosong' => $kosong];
                    @endphp

                    @foreach ($tabs as $status => $dataList)
                    <div x-show="activeTab === '{{ $status }}'" class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-100">
                            <thead class="bg-gray-50/30">
                                <tr class="text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                    <th class="px-6 py-4 text-left">Nama Barang</th>
                                    <th class="px-6 py-4 text-left">Sisa Stok</th>
                                    <th class="px-6 py-4 text-left">Harga Modal</th>
                                    <th class="px-6 py-4 text-left">Harga Jual (Range)</th>
                                    <th class="px-6 py-4 text-left">Profit per Unit</th>
                                    <th class="px-6 py-4 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse ($dataList as $barang)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-bold text-gray-800">{{ $barang->nama_barang }}</div>
                                        <div class="text-[10px] text-gray-400">Exp: {{ $barang->tanggal_kadaluarsa ? \Carbon\Carbon::parse($barang->tanggal_kadaluarsa)->format('d/m/Y') : '-' }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="px-2.5 py-1 rounded-md text-xs font-bold {{ $barang->stok_saat_ini <= 0 ? 'bg-red-50 text-red-600' : 'bg-blue-50 text-blue-600' }}">
                                            {{ $barang->stok_saat_ini ?? 0 }} Unit
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600">Rp {{ number_format($barang->harga_satuan ?? 0, 0, ',', '.') }}</td>
                                    <td class="px-6 py-4 text-sm font-medium text-gray-800">
                                        Rp {{ number_format($barang->harga_jual_range_awal ?? 0, 0, ',', '.') }} - Rp {{ number_format($barang->harga_jual ?? 0, 0, ',', '.') }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-bold text-blue-600">{{ $barang->persentase_laba }}</div>
                                        @php
                                            $labaUnit = ($barang->harga_jual ?? 0) - ($barang->harga_satuan ?? 0);
                                        @endphp
                                        <div class="text-xs font-medium text-green-600">+Rp {{ number_format($labaUnit, 0, ',', '.') }}</div>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        @if ($barang->stok_saat_ini > 0)
                                        <a href="{{ route('admin.kuantitas.create', ['id_stok_warung' => $barang->id_stok_warung]) }}" class="text-blue-600 hover:text-blue-800 font-bold text-xs bg-blue-50 px-3 py-1.5 rounded-lg transition-colors">Atur Kuantitas</a>
                                        @else
                                        <span class="text-gray-300 text-xs italic font-medium">Stok Kosong</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="6" class="px-6 py-10 text-center text-gray-400">Tidak ada data.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </main>
</div>

<script>
    const warungId = "{{ $warung->id }}";
    const tableBody = document.getElementById('tablePengeluaran');

    function formatRupiah(number) {
        return 'Rp ' + new Intl.NumberFormat('id-ID').format(number);
    }

    // Fungsi untuk memformat tanggal ISO menjadi d/m/Y
    function formatDate(dateString) {
        if (!dateString) return '-';
        const date = new Date(dateString);
        return date.toLocaleDateString('id-ID', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric'
        });
    }

    function loadPengeluaran(bulan) {
        tableBody.innerHTML = '<tr><td colspan="4" class="px-6 py-10 text-center text-gray-400 italic">Memproses data...</td></tr>';
        
        fetch(`/admin/warung/${warungId}/pengeluaran-pokok-bulan?bulan=${bulan}`)
            .then(res => res.json())
            .then(res => {
                tableBody.innerHTML = '';
                if (res.data.length === 0) {
                    tableBody.innerHTML = `<tr><td colspan="4" class="px-6 py-10 text-center text-gray-400">Tidak ada pengeluaran pada periode ini</td></tr>`;
                }

                res.data.forEach(item => {
                    const statusBadge = item.status === 'terpenuhi' 
                        ? '<span class="px-2 py-1 bg-green-100 text-green-700 rounded-md text-[10px] font-bold uppercase">Terpenuhi</span>'
                        : '<span class="px-2 py-1 bg-red-100 text-red-600 rounded-md text-[10px] font-bold uppercase">Belum</span>';

                    tableBody.innerHTML += `
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 text-xs text-gray-500 font-medium">${formatDate(item.date)}</td>
                            <td class="px-6 py-4 text-sm font-bold text-gray-800">${item.redaksi}</td>
                            <td class="px-6 py-4 text-sm font-medium text-gray-900">${formatRupiah(item.jumlah)}</td>
                            <td class="px-6 py-4 text-center">${statusBadge}</td>
                        </tr>
                    `;
                });

                document.getElementById('totalPengeluaran').innerText = formatRupiah(res.total);
                document.getElementById('totalTerpenuhi').innerText = formatRupiah(res.terpenuhi);
                document.getElementById('totalBelum').innerText = formatRupiah(res.belum);
            });
    }

    const bulanInput = document.getElementById('filter-bulan');
    const now = new Date();
    const bulanSekarang = now.toISOString().slice(0, 7);
    bulanInput.value = bulanSekarang;

    loadPengeluaran(bulanSekarang);

    bulanInput.addEventListener('change', function() {
        loadPengeluaran(this.value);
    });
</script>

<style>
    [x-cloak] { display: none !important; }
    input[type="month"]::-webkit-calendar-picker-indicator {
        filter: invert(0.5);
        cursor: pointer;
    }
</style>
@endsection