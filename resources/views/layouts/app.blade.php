<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') - Pengelolaan Data</title>
    <link rel="icon" type="image/png" href="/image/icon.png">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        body {
            background-color: #f0f2f5;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            padding-top: 65px;
            padding-bottom: 90px;
        }

        .headbar {
            background-color: #fff;
            box-shadow: 0 2px 5px rgba(0, 0, 0, .08);
            z-index: 1050;
        }

        /* --- Perbaikan Bottom Navigation --- */
        .bottom-sidebar {
            background-color: #ffffff;
            border-top: 1px solid #dee2e6;
            box-shadow: 0 -4px 12px rgba(0, 0, 0, .08);
            z-index: 1050;
            height: 80px;
            padding: 0 !important;
        }

        .nav-scroll-container {
            display: flex;
            overflow-x: auto;
            overflow-y: hidden;
            white-space: nowrap;
            scrollbar-width: none;
            -ms-overflow-style: none;
            scroll-behavior: smooth;
            -webkit-overflow-scrolling: touch;
            padding: 0 15px;
            height: 100%;
            align-items: center;
            /* Memastikan item berada di tengah jika jumlahnya sedikit */
            justify-content: start;
        }

        @media (min-width: 992px) {
            .nav-scroll-container {
                justify-content: center;
                /* Tengah pada layar lebar */
            }
        }

        .nav-scroll-container::-webkit-scrollbar {
            display: none;
        }

        .bottom-sidebar .nav-item {
            flex: 0 0 auto;
            min-width: 90px;
        }

        .bottom-sidebar .nav-link {
            color: #6c757d;
            text-align: center;
            font-size: 0.7rem;
            padding: 8px 4px;
            margin: 0 2px;
            border-radius: 12px;
            transition: all 0.2s ease;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-decoration: none;
        }

        .bottom-sidebar .nav-link i {
            font-size: 1.5rem;
            margin-bottom: 4px;
            transition: transform 0.2s ease;
        }

        .bottom-sidebar .nav-link:hover {
            color: #0d6efd;
            background-color: #f0f7ff;
        }

        .bottom-sidebar .nav-link.active {
            color: #0d6efd;
            background-color: #e7f1ff;
            font-weight: 700;
        }

        .bottom-sidebar .nav-link.active i {
            transform: scale(1.1);
        }

        .scroll-btn {
            background: rgba(255, 255, 255, 0.9);
            border: none;
            height: 100%;
            width: 40px;
            position: absolute;
            z-index: 11;
            display: flex;
            align-items: center;
            justify-content: center;
            backdrop-filter: blur(4px);
        }

        .main-content {
            padding: 1.25rem;
        }
    </style>
</head>

<body>

    <nav class="navbar navbar-expand navbar-light bg-light fixed-top headbar">
        <div class="container-fluid">
            <a class="navbar-brand d-flex align-items-center" href="{{ url('/dashboard') }}">
                <i class="fas fa-store me-2 text-primary"></i>
                @php
                    $warung = \App\Models\Warung::find(session('id_warung'));
                @endphp
                <span class="fw-bold fs-6">
                    {{ $warung?->nama_warung ?? 'Smart Kasir' }}
                </span>
            </a>

            <div class="ms-auto d-flex align-items-center">
                <div class="dropdown">
                    <a href="#" class="d-flex align-items-center text-dark text-decoration-none dropdown-toggle"
                        data-bs-toggle="dropdown">
                        <img src="/image/foto-profil.jpg" alt="User" width="32" height="32"
                            class="rounded-circle me-1 border shadow-sm">
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2">
                        <li>
                            <h6 class="dropdown-header">Petugas Kasir</h6>
                        </li>
                        <li><a class="dropdown-item" href="{{ url('/kasir/profil') }}"><i
                                    class="fas fa-user-gear me-2"></i>Profil</a></li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger">
                                    <i class="fas fa-power-off me-2"></i>Keluar
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <div class="main-content">
        @if ($errors->any())
            <div class="alert alert-danger shadow-sm border-0 mb-4">
                <ul class="mb-0 small font-weight-bold">
                    @foreach ($errors->all() as $e)
                        <li>{{ $e }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </div>

    <nav class="navbar fixed-bottom bottom-sidebar" x-data="{
        canScrollLeft: false,
        canScrollRight: true,
        updateArrows() {
            const el = this.$refs.container;
            this.canScrollLeft = el.scrollLeft > 10;
            this.canScrollRight = el.scrollLeft < (el.scrollWidth - el.clientWidth - 10);
        },
        scroll(direction) {
            const amount = 250;
            this.$refs.container.scrollBy({ left: direction === 'next' ? amount : -amount, behavior: 'smooth' });
        }
    }" x-init="setTimeout(() => updateArrows(), 500)"
        @resize.window="updateArrows()">

        <div class="container-fluid p-0 h-100 position-relative">

            <button x-show="canScrollLeft" x-transition @click="scroll('prev')" class="scroll-btn start-0 border-end">
                <i class="fas fa-chevron-left text-primary"></i>
            </button>

            <ul class="nav-scroll-container w-100 list-unstyled mb-0" x-ref="container"
                @scroll.debounce.50ms="updateArrows()">

                <li class="nav-item">
                    <a href="{{ url('kasir') }}" class="nav-link @if (Request::is('kasir')) active @endif">
                        <i class="fas fa-calculator"></i>
                        <span>Kasir</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ url('/kasir/member') }}"
                        class="nav-link @if (Request::is('kasir/member*')) active @endif">
                        <i class="fas fa-address-card"></i>
                        <span>Member</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ url('/kasir/stok-barang') }}"
                        class="nav-link @if (Request::is('kasir/stok-barang')) active @endif">
                        <i class="fas fa-cubes"></i>
                        <span>Stok Barang</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ url('/kasir/stok-barang/barang-masuk') }}"
                        class="nav-link @if (Request::is('kasir/stok-barang/barang-masuk')) active @endif">
                        <i class="fas fa-file-import"></i>
                        <span>Barang Masuk</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('kasir.rencanabelanja.index') }}"
                        class="nav-link @if (Request::is('*rencana-belanja*')) active @endif">
                        <i class="fas fa-clipboard-list"></i>
                        <span>Rencana Belanja</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ url('kasir/pulsa') }}"
                        class="nav-link @if (Request::is('kasir/pulsa*')) active @endif">
                        <i class="fas fa-signal"></i>
                        <span>Pulsa</span>
                    </a>
                </li>

                @auth
                    <li class="nav-item">
                        <a href="{{ url('/kasir/kas') }}" class="nav-link @if (Request::is('kasir/kas*')) active @endif">
                            <i class="fas fa-vault"></i>
                            <span>Kas Warung</span>
                        </a>
                    </li>
                @endauth

                <li class="nav-item">
                    <a href="{{ url('/kasir/hutang') }}"
                        class="nav-link @if (Request::is('kasir/hutang')) active @endif">
                        <i class="fas fa-hand-holding-dollar"></i>
                        <span>Hutang Pelanggan</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ url('/kasir/hutangBarangamMasuk') }}"
                        class="nav-link @if (Request::is('kasir/hutangBarangamMasuk*')) active @endif">
                        <i class="fas fa-file-invoice"></i>
                        <span>Hutang Barang Masuk</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ url('/kasir/riwayat-transaksi') }}"
                        class="nav-link @if (Request::is('kasir/riwayat-transaksi*')) active @endif">
                        <i class="fas fa-clock-rotate-left"></i>
                        <span>Riwayat Transaksi</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ url('/kasir/riwayat-barang-masuk') }}"
                        class="nav-link @if (Request::is('kasir/riwayat-barang-masuk*')) active @endif">
                        <i class="fas fa-clock-rotate-left"></i>
                        <span>Riwayat Transaksi Barang Masuk</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('kasir.laporan-kas.index') }}"
                        class="nav-link @if (Request::is('kasir/laporan-kas*')) active @endif">
                        <i class="fas fa-file-invoice-dollar"></i>
                        <span>Laporan Kas</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('kasir.mutasibarang.index') }}"
                        class="nav-link @if (Request::is('kasir/mutasibarang*')) active @endif">
                        <i class="fas fa-right-left"></i>
                        <span>Mutasi Barang</span>
                    </a>
                </li>
            </ul>

            <button x-show="canScrollRight" x-transition @click="scroll('next')"
                class="scroll-btn end-0 border-start">
                <i class="fas fa-chevron-right text-primary"></i>
            </button>
        </div>
    </nav>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
