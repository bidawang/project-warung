@extends('layouts.admin')

@section('title', 'Stok Opname')

@section('content')
<div class="flex-1 flex flex-col overflow-hidden bg-gray-100">

    {{-- Header --}}
    <header class="flex justify-between items-center p-6 bg-white border-b">
        <h1 class="text-2xl font-bold text-gray-800">Stok Opname Barang Warung</h1>
    </header>

    <main class="flex-1 overflow-y-auto p-6 space-y-6">

        {{-- Notifikasi --}}
        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4 shadow-md">
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4 shadow-md">
                {{ session('error') }}
            </div>
        @endif
        @if (session('warning'))
            <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded mb-4 shadow-md">
                {{ session('warning') }}
            </div>
        @endif

        {{-- ========================================= --}}
        {{-- LAYAR PEMILIHAN WARUNG (Jika Admin dan belum memilih) --}}
        {{-- ========================================= --}}
        @if (Auth::user()->role === 'admin' && !$activeWarungId && $listWarung->isNotEmpty())
            <div class="bg-white shadow-xl border border-gray-200 rounded-lg p-8 max-w-lg mx-auto mt-10 text-center">
                <svg class="mx-auto h-12 w-12 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 001 1h3m-6-10v10a1 1 0 01-1 1h-3"></path>
                </svg>
                <h2 class="text-2xl font-bold mt-4 mb-2 text-gray-800">Pilih Warung untuk Stok Opname</h2>
                <p class="text-gray-600 mb-6">Silakan pilih warung yang ingin Anda periksa datanya atau masukkan opname baru.</p>

                <div class="grid gap-4">
                    @foreach ($listWarung as $warung)
                        <a href="{{ route('admin.stokopname.index', ['warung_id' => $warung->id]) }}"
                           class="flex items-center justify-between p-4 bg-gray-100 rounded-lg shadow-sm hover:bg-blue-50 hover:shadow-md transition duration-200 border border-gray-200">
                            <span class="font-semibold text-lg text-gray-800">{{ $warung->nama_warung }}</span>
                            <svg class="h-5 w-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                        </a>
                    @endforeach
                </div>
            </div>
        @elseif ($listWarung->isEmpty())
             <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4" role="alert">
                <p class="font-bold">Perhatian</p>
                <p>Tidak ada warung terdaftar. Harap daftarkan warung terlebih dahulu untuk memulai Stok Opname.</p>
            </div>
        @endif

        {{-- ========================================= --}}
        {{-- KONTEN UTAMA (Tampil jika Warung Aktif sudah ada) --}}
        {{-- ========================================= --}}
        @if ($activeWarungId)

            {{-- Nav-Tabs untuk Input dan Riwayat --}}
            <div class="flex border-b border-gray-300">
                @php
                    // Ambil parameter warung_id untuk semua link tab
                    $warungParam = ['warung_id' => $activeWarungId];

                    // Tambahkan Nav-Tab Warung di sini jika ada lebih dari 1 warung (hanya untuk Admin)
                    // Jika Admin, tampilkan tab warung kecil di atas tab Opname
                    if (Auth::user()->role === 'admin' && $listWarung->count() > 1) {
                        echo '<div class="flex space-x-2 mr-4 items-center">';
                        foreach ($listWarung as $warung) {
                            $isActiveWarung = (int)$activeWarungId === (int)$warung->id;
                            $link = route('admin.stokopname.index', array_merge(request()->except(['warung_id', 'tab', 'tanggal']), ['warung_id' => $warung->id]));
                            echo '<a href="' . $link . '" class="text-xs px-3 py-1 rounded-full font-semibold ' .
                                ($isActiveWarung ? 'bg-indigo-600 text-white shadow-md' : 'bg-gray-200 text-gray-700 hover:bg-gray-300') . '">' .
                                $warung->nama_warung .
                                '</a>';
                        }
                        echo '</div>';
                    }
                @endphp

                {{-- Tab Input --}}
                <a href="{{ route('admin.stokopname.index', array_merge($warungParam, ['tab' => 'input', 'tanggal' => null])) }}"
                   class="py-2 px-4 text-sm font-medium border-b-2
                   {{ $activeTab === 'input' && !$tanggalFilter ? 'border-indigo-600 text-indigo-600 bg-white' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}
                   transition duration-150 ease-in-out">
                    Input Opname Terbaru
                </a>

                {{-- Tab Riwayat --}}
                <a href="{{ route('admin.stokopname.index', array_merge($warungParam, ['tab' => 'riwayat'])) }}"
                   class="py-2 px-4 text-sm font-medium border-b-2
                   {{ $activeTab === 'riwayat' || $tanggalFilter ? 'border-indigo-600 text-indigo-600 bg-white' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}
                   transition duration-150 ease-in-out">
                    Riwayat Opname
                </a>

                @if ($tanggalFilter)
                    <a href="{{ route('admin.stokopname.index', array_merge($warungParam, ['tab' => 'riwayat'])) }}" class="ml-auto py-2 text-sm font-semibold text-blue-600 hover:text-blue-800">
                        &larr; Lihat Riwayat Terbaru
                    </a>
                @endif
            </div>

            {{-- ========================================= --}}
            {{-- TAB CONTENT: INPUT OPNAME BARU --}}
            {{-- ========================================= --}}
            @if ($activeTab === 'input' && !$tanggalFilter)
                <section class="bg-white shadow-xl border border-gray-200 rounded-lg p-6">
                    <h2 class="text-xl font-semibold mb-4 border-b pb-2 text-indigo-700">
                        Input Stok Opname Hari Ini
                    </h2>

                    {{-- KETERANGAN BATASAN WAKTU --}}
                    @if (!$canInputToday && $lastOpnameDate)
                        <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 mb-4" role="alert">
                            <p class="font-bold">⏳ Batasan Waktu Opname Aktif</p>
                            <p>Opname terakhir untuk warung ini dilakukan pada tanggal **{{ $lastOpnameDate->translatedFormat('d F Y') }}**.
                                Saat ini baru **{{ $daysSinceLastOpname }} hari** berlalu. Anda dapat melakukan Opname berikutnya minimal **2 hari** setelah opname terakhir.</p>
                        </div>
                    @elseif (!$canInputToday && !$lastOpnameDate)
                        <div class="bg-blue-100 border-l-4 border-blue-500 text-blue-700 p-4 mb-4" role="alert">
                             <p class="font-bold">Info</p>
                             <p>Saat ini belum ada riwayat stok opname untuk warung ini. Input opname pertama Anda sekarang!</p>
                        </div>
                    @endif

                    <form action="{{ route('admin.stokopname.store') }}" method="POST">
                        @csrf
                        <div class="overflow-x-auto">
                            <table class="min-w-full text-sm text-gray-600 border rounded-lg overflow-hidden">
                                <thead class="bg-gray-200 text-gray-700 uppercase text-xs">
                                    <tr>
                                        <th class="py-3 px-6 text-left">Nama Barang</th>
                                        <th class="py-3 px-6 text-center">Stok Sistem (Unit)</th>
                                        <th class="py-3 px-6 text-center">Input Stok Fisik (Opname)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($stokSekarang as $stok)
                                        <tr class="border-b hover:bg-gray-50">
                                            <td class="py-3 px-6 font-medium">{{ $stok['nama_barang'] }}</td>
                                            <td class="py-3 px-6 text-center font-mono text-gray-800">{{ $stok['stok_sistem'] }}</td>
                                            <td class="py-3 px-6 text-center">
                                                <input type="number" name="jumlah[{{ $stok['id_stok_warung'] }}]" min="0"
                                                    class="border border-gray-300 rounded-lg px-2 py-1 w-28 text-center
                                                           @if(!$canInputToday) bg-gray-100 cursor-not-allowed @endif focus:ring-blue-500 focus:border-blue-500"
                                                    placeholder="{{ $stok['stok_sistem'] }}"
                                                    value="{{ old('jumlah.'.$stok['id_stok_warung']) }}"
                                                    {{ $canInputToday ? '' : 'disabled' }}>
                                                <input type="hidden" name="id_stok_warung[]" value="{{ $stok['id_stok_warung'] }}">
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="3" class="text-center py-4 text-gray-500">Tidak ada data stok warung yang perlu diopname.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="flex justify-end mt-6">
                            <button type="submit"
                                class="font-bold py-2 px-6 rounded-lg shadow-md transition duration-150
                                       {{ $canInputToday ? 'bg-blue-600 hover:bg-blue-700 text-white' : 'bg-gray-400 text-gray-600 cursor-not-allowed' }}"
                                {{ $canInputToday ? '' : 'disabled' }}>
                                Simpan Hasil Opname
                            </button>
                        </div>
                    </form>
                </section>
            @endif

            {{-- ========================================= --}}
            {{-- TAB CONTENT: RIWAYAT OPNAME --}}
            {{-- ========================================= --}}
            @if ($activeTab === 'riwayat' || $tanggalFilter)
                <section class="bg-white shadow-xl border border-gray-200 rounded-lg p-6">
                    <h2 class="text-xl font-semibold mb-4 border-b pb-2">
                        Riwayat Stok Opname {{ $tanggalFilter ? 'pada Tanggal ' . \Carbon\Carbon::parse($tanggalFilter)->translatedFormat('d F Y') : '(Minus Tertinggi)' }}
                    </h2>

                    {{-- Tab tanggal --}}
                    <div class="flex flex-wrap gap-2 mb-6">
                        @forelse ($riwayatTanggal as $tgl)
                            @php $isActive = $tanggalFilter === $tgl->tanggal; @endphp
                            <a href="{{ route('admin.stokopname.index', array_merge($warungParam, ['tanggal' => $tgl->tanggal, 'tab' => 'riwayat'])) }}"
                                class="px-4 py-2 rounded-lg text-sm font-semibold {{ $isActive ? 'bg-blue-600 text-white shadow-md' : 'bg-gray-200 hover:bg-gray-300 text-gray-700' }}">
                                {{ \Carbon\Carbon::parse($tgl->tanggal)->translatedFormat('d M Y') }}
                            </a>
                        @empty
                            <p class="text-sm text-gray-500">Belum ada riwayat opname yang tersimpan untuk warung ini.</p>
                        @endforelse
                    </div>

                    {{-- Tabel riwayat gabungan --}}
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm text-gray-600 border rounded-lg overflow-hidden">
                            <thead class="bg-gray-200 text-gray-700 uppercase text-xs">
                                <tr>
                                    <th class="py-3 px-6 text-left">Nama Barang</th>
                                    <th class="py-3 px-6 text-center">Stok Sistem</th>
                                    <th class="py-3 px-6 text-center">Jumlah Opname</th>
                                    <th class="py-3 px-6 text-center">Selisih</th>
                                    <th class="py-3 px-6 text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($stokSekarang as $stok)
                                    @php
                                        $jumlahOpname = $stok['jumlah_opname'];
                                        $selisih = $stok['selisih'];

                                        if ($jumlahOpname !== null) {
                                            $selisihTampil = $selisih;
                                            $selisihColor = $selisih == 0 ? 'text-green-600' : ($selisih > 0 ? 'text-blue-600' : 'text-red-600');
                                            $statusText = '✅ Sudah dicek';
                                            $statusColor = 'bg-green-100 text-green-700';
                                        } else {
                                            $selisihTampil = 'Tidak Diperiksa';
                                            $selisihColor = 'text-gray-500';
                                            $statusText = 'Tidak Diperiksa';
                                            $statusColor = 'bg-gray-100 text-gray-500';
                                        }
                                    @endphp
                                    <tr class="border-b hover:bg-gray-50">
                                        <td class="py-3 px-6 font-medium">{{ $stok['nama_barang'] }}</td>
                                        <td class="py-3 px-6 text-center font-mono">{{ $stok['stok_sistem'] }}</td>
                                        <td class="py-3 px-6 text-center font-mono">
                                            {{ $jumlahOpname ?? 'Tidak Diperiksa' }}
                                        </td>
                                        <td class="py-3 px-6 text-center font-bold {{ $selisihColor }}">
                                            {{ is_numeric($selisihTampil) ? $selisihTampil : '-' }}
                                        </td>
                                        <td class="py-3 px-6 text-center">
                                            <span class="px-3 py-1 text-xs rounded-full font-semibold {{ $statusColor }}">
                                                {{ $statusText }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5" class="text-center py-4 text-gray-500">Tidak ada data stok warung saat ini.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </section>
            @endif
        @endif
    </main>
</div>
@endsection
