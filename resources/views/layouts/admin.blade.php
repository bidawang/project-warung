<!DOCTYPE html>
<html lang="id" x-data="{ sidebarOpen: false }">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') - Admin Panel</title>

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        [x-cloak] {
            display: none !important;
        }

        .sidebar-scroll::-webkit-scrollbar {
            width: 4px;
        }

        .sidebar-scroll::-webkit-scrollbar-track {
            background: #1f2937;
        }

        .sidebar-scroll::-webkit-scrollbar-thumb {
            background: #4b5563;
            border-radius: 10px;
        }
    </style>

</head>

<body class="bg-gray-100 font-sans antialiased">
    <div class="flex h-screen overflow-hidden">

        {{-- Sidebar Overlay (Mobile) --}}
        <div x-show="sidebarOpen" @click="sidebarOpen = false"
            class="fixed inset-0 z-40 bg-black/50 transition-opacity lg:hidden"
            x-transition:enter="transition-opacity ease-linear duration-300" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" x-transition:leave="transition-opacity ease-linear duration-300"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" x-cloak></div>

        {{-- Sidebar --}}
        <aside
            class="fixed inset-y-0 left-0 z-50 w-64 bg-gray-900 text-gray-300
           transform transition-transform duration-300
           flex flex-col h-full
           lg:static lg:inset-0 lg:translate-x-0"
            :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">


            {{-- Logo Section --}}
            <div class="flex items-center justify-between px-6 py-5 bg-gray-900 border-b border-gray-800">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-blue-600 rounded-lg text-white">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>
                    <span class="text-xl font-bold text-white tracking-wider uppercase">WarungApp</span>
                </div>
                <button @click="sidebarOpen = false" class="lg:hidden">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            {{-- Menu Navigation --}}
            <nav class="flex-1 px-4 py-6 overflow-y-auto sidebar-scroll space-y-2">

                {{-- Dashboard --}}
                <a href="{{ route('admin.dashboard') }}"
                    class="flex items-center px-4 py-2.5 rounded-lg transition {{ request()->routeIs('admin.dashboard') ? 'bg-blue-600 text-white shadow-lg shadow-blue-900/20' : 'hover:bg-gray-800 hover:text-white' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1V10a1 1 0 00-1-1H7a1 1 0 00-1 1v10a1 1 0 001 1h3z" />
                    </svg>
                    <span class="text-sm font-medium">Dashboard</span>
                </a>

                {{-- Group: Produk & Stok --}}
                <div class="pt-4 pb-2 text-[10px] font-bold text-gray-500 uppercase tracking-widest px-4">Inventaris &
                    Barang</div>

                <div x-data="{ open: {{ request()->is('admin/barang*', 'admin/satuan*', 'admin/kategori*', 'admin/subkategori*', 'admin/asalbarang*', 'admin/areapembelian*') ? 'true' : 'false' }} }">
                    <button @click="open = !open"
                        class="w-full flex items-center justify-between px-4 py-2.5 rounded-lg hover:bg-gray-800 hover:text-white transition group">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-3 text-gray-400 group-hover:text-white" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7l8 4" />
                            </svg>
                            <span class="text-sm font-medium">Master Produk</span>
                        </div>
                        <svg class="w-4 h-4 transition-transform duration-200" :class="open ? 'rotate-180' : ''"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path d="M19 9l-7 7-7-7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </button>
                    <div x-show="open" x-cloak class="mt-1 space-y-1 ml-9 border-l border-gray-700">
                        <a href="{{ route('admin.barang.index') }}"
                            class="block px-4 py-2 text-sm {{ request()->routeIs('admin.barang.*') ? 'text-blue-400 font-semibold' : 'hover:text-white' }}">Daftar
                            Barang</a>
                        <a href="{{ route('admin.satuan.index') }}"
                            class="block px-4 py-2 text-sm {{ request()->routeIs('admin.satuan.*') ? 'text-blue-400 font-semibold' : 'hover:text-white' }}">Satuan
                            Utama</a>
                        <a href="{{ route('admin.satuan-barang.index') }}"
                            class="block px-4 py-2 text-sm {{ request()->routeIs('admin.satuan-barang.*') ? 'text-blue-400 font-semibold' : 'hover:text-white' }}">Satuan
                            Barang</a>
                        <a href="{{ route('admin.kategori.index') }}"
                            class="block px-4 py-2 text-sm {{ request()->routeIs('admin.kategori.*') ? 'text-blue-400 font-semibold' : 'hover:text-white' }}">Kategori</a>
                        <a href="{{ route('admin.subkategori.index') }}"
                            class="block px-4 py-2 text-sm {{ request()->routeIs('admin.subkategori.*') ? 'text-blue-400 font-semibold' : 'hover:text-white' }}">Sub
                            Kategori</a>
                        <a href="{{ route('admin.areapembelian.index') }}"
                            class="block px-4 py-2 text-sm {{ request()->routeIs('admin.areapembelian.*') ? 'text-blue-400 font-semibold' : 'hover:text-white' }}">Area
                            Pembelian</a>
                        <a href="{{ route('admin.asalbarang.index') }}"
                            class="block px-4 py-2 text-sm {{ request()->routeIs('admin.asalbarang.*') ? 'text-blue-400 font-semibold' : 'hover:text-white' }}">Asal
                            Barang</a>
                    </div>
                </div>

                <a href="{{ route('admin.stokopname.index') }}"
                    class="flex items-center px-4 py-2.5 rounded-lg transition {{ request()->routeIs('admin.stokopname.*') ? 'bg-blue-600 text-white shadow-lg' : 'hover:bg-gray-800 hover:text-white' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    <span class="text-sm font-medium">Stok Opname</span>
                </a>

                <a href="{{ route('admin.kuantitas.index') }}"
                    class="flex items-center px-4 py-2.5 rounded-lg transition {{ request()->routeIs('admin.kuantitas.*') ? 'bg-blue-600 text-white shadow-lg' : 'hover:bg-gray-800 hover:text-white' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                    </svg>
                    <span class="text-sm font-medium">Kuantitas Barang</span>
                </a>

                {{-- Group: Transaksi & Keuangan --}}
                <div class="pt-4 pb-2 text-[10px] font-bold text-gray-500 uppercase tracking-widest px-4">Transaksi &
                    Keuangan</div>

                <div x-data="{ open: {{ request()->is('admin/rencana*', 'admin/transaksibarang*', 'admin/riwayat-transaksi*') ? 'true' : 'false' }} }">
                    <button @click="open = !open"
                        class="w-full flex items-center justify-between px-4 py-2.5 rounded-lg hover:bg-gray-800 hover:text-white transition group">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-3 text-gray-400 group-hover:text-white" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                            </svg>
                            <span class="text-sm font-medium">Belanja & Rencana</span>
                        </div>
                        <svg class="w-4 h-4 transition-transform duration-200" :class="open ? 'rotate-180' : ''"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path d="M19 9l-7 7-7-7" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" />
                        </svg>
                    </button>
                    <div x-show="open" x-cloak class="mt-1 space-y-1 ml-9 border-l border-gray-700">
                        <a href="{{ route('admin.createrencanabelanja') }}"
                            class="block px-4 py-2 text-sm {{ request()->routeIs('admin.createrencanabelanja') ? 'text-blue-400 font-semibold' : 'hover:text-white' }}">Lihat
                            Permintaan</a>
                        <a href="{{ route('admin.rencana.index') }}"
                            class="block px-4 py-2 text-sm {{ request()->routeIs('admin.rencana.index') ? 'text-blue-400 font-semibold' : 'hover:text-white' }}">Kirim
                            Rencana</a>
                        <a href="{{ route('admin.transaksibarang.index') }}"
                            class="block px-4 py-2 text-sm {{ request()->routeIs('admin.transaksibarang.*') ? 'text-blue-400 font-semibold' : 'hover:text-white' }}">Belanja
                            Tambahan</a>
                        <a href="{{ route('admin.riwayat_transaksi.index') }}"
                            class="block px-4 py-2 text-sm {{ request()->routeIs('admin.riwayat_transaksi.*') ? 'text-blue-400 font-semibold' : 'hover:text-white' }}">Riwayat
                            Transaksi</a>
                    </div>
                </div>

                <div x-data="{ open: {{ request()->is('admin/harga-pulsa*', 'admin/saldo-pulsa*') ? 'true' : 'false' }} }">
                    <button @click="open = !open"
                        class="w-full flex items-center justify-between px-4 py-2.5 rounded-lg hover:bg-gray-800 hover:text-white transition group">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-3 text-gray-400 group-hover:text-white" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span class="text-sm font-medium">Pulsa & PPOB</span>
                        </div>
                        <svg class="w-4 h-4 transition-transform duration-200" :class="open ? 'rotate-180' : ''"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path d="M19 9l-7 7-7-7" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" />
                        </svg>
                    </button>
                    <div x-show="open" x-cloak class="mt-1 space-y-1 ml-9 border-l border-gray-700">
                        <a href="{{ route('admin.harga-pulsa.index') }}"
                            class="block px-4 py-2 text-sm {{ request()->routeIs('admin.harga-pulsa.*') ? 'text-blue-400 font-semibold' : 'hover:text-white' }}">Harga
                            Pulsa</a>
                        <a href="{{ route('admin.saldo-pulsa.index') }}"
                            class="block px-4 py-2 text-sm {{ request()->routeIs('admin.saldo-pulsa.*') ? 'text-blue-400 font-semibold' : 'hover:text-white' }}">Saldo
                            Pulsa</a>
                        <a href="{{ route('admin.jenis-pulsa.index') }}"
                            class="block px-4 py-2 text-sm {{ request()->routeIs('admin.jenis-pulsa.*') ? 'text-blue-400 font-semibold' : 'hover:text-white' }}">Jenis
                            Pulsa</a>
                    </div>
                </div>

                {{-- Pricing & Laba --}}
                <div x-data="{ open: {{ request()->is('admin/barang/prices*', 'admin/laba*') ? 'true' : 'false' }} }">
                    <button @click="open = !open"
                        class="w-full flex items-center justify-between px-4 py-2.5 rounded-lg hover:bg-gray-800 hover:text-white transition group">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-3 text-gray-400 group-hover:text-white" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                            </svg>
                            <span class="text-sm font-medium">Harga & Laba</span>
                        </div>
                        <svg class="w-4 h-4 transition-transform duration-200" :class="open ? 'rotate-180' : ''"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path d="M19 9l-7 7-7-7" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" />
                        </svg>
                    </button>
                    <div x-show="open" x-cloak class="mt-1 space-y-1 ml-9 border-l border-gray-700">
                        <a href="{{ route('admin.harga_jual.monitor_all_prices') }}"
                            class="block px-4 py-2 text-sm {{ request()->routeIs('admin.harga_jual.*') ? 'text-blue-400 font-semibold' : 'hover:text-white' }}">Monitor
                            Harga</a>
                        {{-- <a href="{{ route('admin.laba.index') }}" class="block px-4 py-2 text-sm {{ request()->routeIs('admin.laba.index') ? 'text-blue-400 font-semibold' : 'hover:text-white' }}">Aturan Laba</a>
                        <a href="{{ route('laba.formImport') }}" class="block px-4 py-2 text-sm {{ request()->routeIs('laba.formImport') ? 'text-blue-400 font-semibold' : 'hover:text-white' }}">Import Laba</a> --}}
                    </div>
                </div>

<<<<<<< HEAD
=======
                {{-- KASIR --}}
                <div x-data="{
                    open: {{ request()->routeIs('admin.inject-kas.*', 'admin.laporan-laba.*', 'admin.belanja-barang.*', 'admin.operasional.*') ? 'true' : 'false' }}">

                    <button @click="open = !open"
                        class="w-full flex items-center justify-between px-4 py-2.5 rounded-lg hover:bg-gray-800 hover:text-white transition group">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-3 text-gray-400 group-hover:text-white" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                            </svg>
                            <span class="text-sm font-medium">KASIR</span>
                        </div>

                        <svg class="w-4 h-4 transition-transform duration-200" :class="open ? 'rotate-180' : ''"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path d="M19 9l-7 7-7-7" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" />
                        </svg>
                    </button>

                    <div x-show="open" x-transition x-cloak class="mt-1 space-y-1 ml-9 border-l border-gray-700">
                        <a href="{{ route('admin.inject-kas.index') }}"
                            class="block px-4 py-2 text-sm 
           {{ request()->routeIs('admin.inject-kas.*') ? 'text-blue-400 font-semibold' : 'hover:text-white' }}">
                            Terima Kas
                        </a>
                        <a href="{{ route('admin.laporan-laba.index') }}"
                            class="block px-4 py-2 text-sm 
           {{ request()->routeIs('admin.laporan-laba.*') ? 'text-blue-400 font-semibold' : 'hover:text-white' }}">
                            Omset (Laporan Laba)
                        </a>
                        <a href="{{ route('admin.belanja-barang.index') }}"
                            class="block px-4 py-2 text-sm 
           {{ request()->routeIs('admin.belanja-barang.*') ? 'text-blue-400 font-semibold' : 'hover:text-white' }}">
                            Belanja Barang
                        </a>
                        <a href="{{ route('admin.operasional.index') }}"
                            class="block px-4 py-2 text-sm 
           {{ request()->routeIs('admin.operasional.*') ? 'text-blue-400 font-semibold' : 'hover:text-white' }}">
                            Biaya Operasional
                        </a>
                    </div>
                </div>


>>>>>>> 6bc1f4718250fcb9df21161307f9e402991e5be7
                <a href="{{ route('admin.hutang.index') }}"
                    class="flex items-center px-4 py-2.5 rounded-lg transition {{ request()->routeIs('admin.hutang.*') ? 'bg-red-600 text-white shadow-lg' : 'hover:bg-gray-800 hover:text-white' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    <span class="text-sm font-medium">Hutang Pelanggan</span>
                </a>

                {{-- Group: Wilayah & Warung --}}
                <div class="pt-4 pb-2 text-[10px] font-bold text-gray-500 uppercase tracking-widest px-4">Manajemen
                    Area</div>

                <a href="{{ route('admin.area.index') }}"
                    class="flex items-center px-4 py-2.5 rounded-lg transition {{ request()->routeIs('admin.area.*') ? 'bg-gray-700 text-white' : 'hover:bg-gray-800 hover:text-white' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                        <path d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <span class="text-sm font-medium">Data Area</span>
                </a>
                <a href="{{ route('admin.warung.index') }}"
                    class="flex items-center px-4 py-2.5 rounded-lg transition {{ request()->routeIs('admin.warung.*') ? 'bg-gray-700 text-white' : 'hover:bg-gray-800 hover:text-white' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                    <span class="text-sm font-medium">Data Warung</span>
                </a>
                <a href="{{ route('admin.targetpencapaian.index') }}"
                    class="flex items-center px-4 py-2.5 rounded-lg transition {{ request()->routeIs('admin.targetpencapaian.*') ? 'bg-gray-700 text-white' : 'hover:bg-gray-800 hover:text-white' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                    <span class="text-sm font-medium">Target & Capaian</span>
                </a>

                {{-- System --}}
                <div class="pt-4 pb-2 text-[10px] font-bold text-gray-500 uppercase tracking-widest px-4">Pengaturan
                    Sistem</div>

                {{-- <a href="{{ url('/import-barang') }}" class="flex items-center px-4 py-2.5 rounded-lg transition {{ request()->is('import-barang*') ? 'bg-gray-700 text-white' : 'hover:bg-gray-800 hover:text-white' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                    <span class="text-sm font-medium">Import Master Barang</span>
                </a> --}}

                <a href="{{ route('admin.user.index') }}"
                    class="flex items-center px-4 py-2.5 rounded-lg transition {{ request()->routeIs('admin.user.*') ? 'bg-gray-700 text-white' : 'hover:bg-gray-800 hover:text-white' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    <span class="text-sm font-medium">Manajemen User</span>
                </a>
<<<<<<< HEAD
                <a href="{{ route('admin.inject-kas.index') }}"
=======
                {{-- <a href="{{ route('admin.inject-kas.index') }}"
>>>>>>> 6bc1f4718250fcb9df21161307f9e402991e5be7
                    class="flex items-center px-4 py-2.5 rounded-lg transition {{ request()->routeIs('admin.inject-kas.*') ? 'bg-gray-700 text-white' : 'hover:bg-gray-800 hover:text-white' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    <span class="text-sm font-medium">Inject Kas</span>
                </a> --}}

                <a href="{{ route('logout') }}"
                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                    class="flex items-center px-4 py-2.5 rounded-lg text-red-400 hover:bg-red-500/10 hover:text-red-500 transition">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                    <span class="text-sm font-medium">Logout</span>
                </a>

                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">@csrf</form>
            </nav>
        </aside>

        {{-- Main Content Wrapper --}}
        <div class="flex-1 flex flex-col min-w-0 overflow-hidden">

            {{-- Top Navbar --}}
            <header class="bg-white border-b border-gray-200">
                <div class="flex items-center justify-between px-4 py-3 lg:px-6">
                    <div class="flex items-center">
                        <button @click="sidebarOpen = true" class="p-2 -ml-2 text-gray-500 lg:hidden">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>
                        <h1 class="ml-2 text-lg font-semibold text-gray-800 lg:text-xl">@yield('title', 'Dashboard')</h1>
                    </div>

                    {{-- User Profile Dropdown --}}
                    <div class="flex items-center gap-4">
                        <div class="hidden sm:flex flex-col text-right">
                            <span
                                class="text-sm font-bold text-gray-800">{{ Auth::user()->name ?? 'Administrator' }}</span>
                            <span
                                class="text-[10px] text-gray-500 uppercase">{{ Auth::user()->role ?? 'Super Admin' }}</span>
                        </div>
                        <div
                            class="h-10 w-10 rounded-full bg-blue-100 border-2 border-blue-500 flex items-center justify-center text-blue-600 font-bold">
                            {{ substr(Auth::user()->name ?? 'A', 0, 1) }}
                        </div>
                    </div>
                </div>
            </header>

            {{-- Main Scrollable Content --}}
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-50 p-4 lg:p-8">
                @yield('content')
            </main>
        </div>
    </div>

    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

</body>

</html>
