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
        [x-cloak] { display: none !important; }
        /* Custom scrollbar untuk sidebar */
        .sidebar-scroll::-webkit-scrollbar { width: 4px; }
        .sidebar-scroll::-webkit-scrollbar-track { background: #1f2937; }
        .sidebar-scroll::-webkit-scrollbar-thumb { background: #4b5563; border-radius: 10px; }
    </style>
</head>

<body class="bg-gray-100 font-sans antialiased">
    <div class="flex h-screen overflow-hidden">
        
        {{-- Sidebar Overlay (Mobile) --}}
        <div x-show="sidebarOpen" 
             @click="sidebarOpen = false" 
             class="fixed inset-0 z-40 bg-black/50 transition-opacity lg:hidden"
             x-transition:enter="transition-opacity ease-linear duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-linear duration-300"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             x-cloak></div>

        {{-- Sidebar --}}
        <aside class="fixed inset-y-0 left-0 z-50 w-64 bg-gray-900 text-gray-300 transform transition-transform duration-300 lg:static lg:inset-0 lg:translate-x-0"
               :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">
            
            {{-- Logo Section --}}
            <div class="flex items-center justify-between px-6 py-5 bg-gray-900 border-b border-gray-800">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-blue-600 rounded-lg text-white">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    </div>
                    <span class="text-xl font-bold text-white tracking-wider uppercase">WarungApp</span>
                </div>
                <button @click="sidebarOpen = false" class="lg:hidden">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            {{-- Menu Navigation --}}
            <nav class="flex-1 px-4 py-6 overflow-y-auto sidebar-scroll space-y-2">
                
                {{-- Dashboard --}}
                <a href="/admin/dashboard" class="flex items-center px-4 py-2.5 rounded-lg transition {{ request()->is('admin/dashboard') ? 'bg-blue-600 text-white shadow-lg shadow-blue-900/20' : 'hover:bg-gray-800 hover:text-white' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1V10a1 1 0 00-1-1H7a1 1 0 00-1 1v10a1 1 0 001 1h3z"/></svg>
                    <span class="text-sm font-medium">Dashboard</span>
                </a>

                {{-- Group: Produk & Stok --}}
                <div class="pt-4 pb-2 text-[10px] font-bold text-gray-500 uppercase tracking-widest px-4">Inventaris & Barang</div>
                
                <div x-data="{ open: {{ request()->is('admin/barang*', 'admin/satuan*', 'admin/kategori*', 'admin/subkategori*', 'admin/asalbarang*') ? 'true' : 'false' }} }">
                    <button @click="open = !open" class="w-full flex items-center justify-between px-4 py-2.5 rounded-lg hover:bg-gray-800 hover:text-white transition group">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-3 text-gray-400 group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7l8 4"/></svg>
                            <span class="text-sm font-medium">Master Produk</span>
                        </div>
                        <svg class="w-4 h-4 transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </button>
                    <div x-show="open" x-cloak class="mt-1 space-y-1 ml-9 border-l border-gray-700">
                        <a href="/admin/barang" class="block px-4 py-2 text-sm {{ request()->is('admin/barang') ? 'text-blue-400' : 'hover:text-white' }}">Daftar Barang</a>
                        <a href="/admin/satuan" class="block px-4 py-2 text-sm {{ request()->is('admin/satuan*') ? 'text-blue-400' : 'hover:text-white' }}">Satuan</a>
                        <a href="/admin/kategori" class="block px-4 py-2 text-sm {{ request()->is('admin/kategori*') ? 'text-blue-400' : 'hover:text-white' }}">Kategori</a>
                        <a href="/admin/subkategori" class="block px-4 py-2 text-sm {{ request()->is('admin/subkategori*') ? 'text-blue-400' : 'hover:text-white' }}">Sub Kategori</a>
                        <a href="/admin/asalbarang" class="block px-4 py-2 text-sm {{ request()->is('admin/asalbarang*') ? 'text-blue-400' : 'hover:text-white' }}">Asal Barang</a>
                    </div>
                </div>

                <a href="/admin/stokopname" class="flex items-center px-4 py-2.5 rounded-lg transition {{ request()->is('admin/stokopname*') ? 'bg-blue-600 text-white shadow-lg shadow-blue-900/20' : 'hover:bg-gray-800 hover:text-white' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    <span class="text-sm font-medium">Stok Opname</span>
                </a>

                {{-- Group: Transaksi & Keuangan --}}
                <div class="pt-4 pb-2 text-[10px] font-bold text-gray-500 uppercase tracking-widest px-4">Transaksi & Keuangan</div>

                <div x-data="{ open: {{ request()->is('admin/rencana*', 'admin/transaksibarang*', 'admin/riwayat-transaksi*') ? 'true' : 'false' }} }">
                    <button @click="open = !open" class="w-full flex items-center justify-between px-4 py-2.5 rounded-lg hover:bg-gray-800 hover:text-white transition group">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-3 text-gray-400 group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                            <span class="text-sm font-medium">Belanja & Rencana</span>
                        </div>
                        <svg class="w-4 h-4 transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </button>
                    <div x-show="open" x-cloak class="mt-1 space-y-1 ml-9 border-l border-gray-700">
                        <a href="/admin/rencana/create" class="block px-4 py-2 text-sm {{ request()->is('admin/rencana/create') ? 'text-blue-400' : 'hover:text-white' }}">Lihat Permintaan</a>
                        <a href="/admin/rencana" class="block px-4 py-2 text-sm {{ request()->is('admin/rencana') ? 'text-blue-400' : 'hover:text-white' }}">Kirim Rencana</a>
                        <a href="/admin/transaksibarang" class="block px-4 py-2 text-sm {{ request()->is('admin/transaksibarang*') ? 'text-blue-400' : 'hover:text-white' }}">Belanja Tambahan</a>
                        <a href="/admin/riwayat-transaksi" class="block px-4 py-2 text-sm {{ request()->is('admin/riwayat-transaksi*') ? 'text-blue-400' : 'hover:text-white' }}">Riwayat Transaksi</a>
                    </div>
                </div>

                <div x-data="{ open: {{ request()->is('admin/harga-pulsa*', 'admin/saldo-pulsa*') ? 'true' : 'false' }} }">
                    <button @click="open = !open" class="w-full flex items-center justify-between px-4 py-2.5 rounded-lg hover:bg-gray-800 hover:text-white transition group">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-3 text-gray-400 group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span class="text-sm font-medium">Pulsa & PPOB</span>
                        </div>
                        <svg class="w-4 h-4 transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </button>
                    <div x-show="open" x-cloak class="mt-1 space-y-1 ml-9 border-l border-gray-700">
                        <a href="/admin/harga-pulsa" class="block px-4 py-2 text-sm {{ request()->is('admin/harga-pulsa*') ? 'text-blue-400' : 'hover:text-white' }}">Harga Pulsa</a>
                        <a href="/admin/saldo-pulsa" class="block px-4 py-2 text-sm {{ request()->is('admin/saldo-pulsa*') ? 'text-blue-400' : 'hover:text-white' }}">Saldo Pulsa</a>
                    </div>
                </div>

                <a href="/admin/hutang" class="flex items-center px-4 py-2.5 rounded-lg transition {{ request()->is('admin/hutang*') ? 'bg-red-600 text-white' : 'hover:bg-gray-800 hover:text-white' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    <span class="text-sm font-medium">Hutang Pelanggan</span>
                </a>

                {{-- Group: Wilayah & Warung --}}
                <div class="pt-4 pb-2 text-[10px] font-bold text-gray-500 uppercase tracking-widest px-4">Manajemen Area</div>
                
                <a href="/admin/area" class="flex items-center px-4 py-2.5 rounded-lg transition {{ request()->is('admin/area*') ? 'bg-gray-700 text-white' : 'hover:bg-gray-800 hover:text-white' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    <span class="text-sm font-medium">Data Area</span>
                </a>
                <a href="/admin/warung" class="flex items-center px-4 py-2.5 rounded-lg transition {{ request()->is('admin/warung*') ? 'bg-gray-700 text-white' : 'hover:bg-gray-800 hover:text-white' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    <span class="text-sm font-medium">Data Warung</span>
                </a>
                <a href="/admin/targetpencapaian" class="flex items-center px-4 py-2.5 rounded-lg transition {{ request()->is('admin/target*') ? 'bg-gray-700 text-white' : 'hover:bg-gray-800 hover:text-white' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    <span class="text-sm font-medium">Target & Capaian</span>
                </a>

                {{-- System --}}
                <div class="pt-4 pb-2 text-[10px] font-bold text-gray-500 uppercase tracking-widest px-4">Pengaturan Sistem</div>
                <a href="/admin/user" class="flex items-center px-4 py-2.5 rounded-lg transition {{ request()->is('admin/user*') ? 'bg-gray-700 text-white' : 'hover:bg-gray-800 hover:text-white' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    <span class="text-sm font-medium">Manajemen User</span>
                </a>
                
                <a href="{{ route('logout') }}"
                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                   class="flex items-center px-4 py-2.5 rounded-lg text-red-400 hover:bg-red-500/10 hover:text-red-500 transition">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
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
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                        </button>
                        <h1 class="ml-2 text-lg font-semibold text-gray-800 lg:text-xl">@yield('title', 'Dashboard')</h1>
                    </div>

                    {{-- User Profile Dropdown --}}
                    <div class="flex items-center gap-4">
                        <div class="hidden sm:flex flex-col text-right">
                            <span class="text-sm font-bold text-gray-800">{{ Auth::user()->name ?? 'Administrator' }}</span>
                            <span class="text-[10px] text-gray-500 uppercase">{{ Auth::user()->role ?? 'Super Admin' }}</span>
                        </div>
                        <div class="h-10 w-10 rounded-full bg-blue-100 border-2 border-blue-500 flex items-center justify-center text-blue-600 font-bold">
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
</body>
</html>