<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') - Pengelolaan Data</title>
    <!-- Bootstrap CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome CDN -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f0f2f5;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            padding-top: 56px;
            padding-bottom: 72px;
        }

        .headbar {
            background-color: #fff;
            box-shadow: 0 2px 5px rgba(0, 0, 0, .08);
            z-index: 1050;
        }

        .bottom-sidebar {
            background-color: #ffffff;
            border-top: 1px solid #e0e0e0;
            box-shadow: 0 -2px 5px rgba(0, 0, 0, .05);
            z-index: 1050;
        }

        .bottom-sidebar .nav-link {
            color: #7f8c8d;
            flex-grow: 1;
            text-align: center;
            font-size: 0.75rem;
            padding: 0.5rem 0.25rem;
            border-radius: 0.5rem;
            transition: all 0.2s ease-in-out;
        }

        .bottom-sidebar .nav-link:hover,
        .bottom-sidebar .nav-link.active {
            color: #3498db;
            background-color: #ecf0f1;
        }

        .bottom-sidebar .nav-link .fas,
        .bottom-sidebar .nav-link .far {
            font-size: 1.5rem;
            margin-bottom: 0.25rem;
            display: block;
        }

        .main-content {
            padding: 1.5rem;
        }

        .card {
            border-radius: 1rem;
            box-shadow: 0 8px 15px rgba(0, 0, 0, .08);
            border: none;
        }
    </style>
</head>

<body>

    <!-- Headbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light fixed-top headbar">
        <div class="container-fluid">
            <a class="navbar-brand d-flex align-items-center" href="{{ url('/dashboard') }}">
                <i class="fas fa-chart-bar me-2 text-primary"></i>
                <span class="fw-bold">Dashboard App</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#topNavbar"
                aria-controls="topNavbar" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="topNavbar">
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0">

                    <!-- Master Data -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-database"></i> Master Data
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="{{ route('kategori.index') }}">Kategori</a></li>
                            <li><a class="dropdown-item" href="{{ route('subkategori.index') }}">Sub Kategori</a></li>
                            <li><a class="dropdown-item" href="{{ route('barang.index') }}">Barang</a></li>
                            <li><a class="dropdown-item" href="{{ route('admin.warung.index') }}">Warung</a></li>
                            <li><a class="dropdown-item" href="{{ route('area.index') }}">Area</a></li>
                            <li><a class="dropdown-item" href="{{ route('user.index') }}">Pengguna</a></li>

                        </ul>
                    </li>

                    <!-- Transaksi -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-exchange-alt"></i> Transaksi
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="{{ route('kaswarung.index') }}">Kas Warung</a></li>
                            <li><a class="dropdown-item" href="{{ route('detailkaswarung.index') }}">Detail Kas
                                    Warung</a></li>
                            <li><a class="dropdown-item" href="{{ route('transaksikas.index') }}">Transaksi Kas</a></li>
                            <li><a class="dropdown-item" href="{{ route('detailtransaksi.index') }}">Detail
                                    Transaksi</a></li>
                            <li><a class="dropdown-item" href="{{ route('transaksibarang.index') }}">Transaksi
                                    Barang</a></li>
                            <li><a class="dropdown-item" href="{{ route('stokwarung.index') }}">Stok Warung</a></li>
                            <li><a class="dropdown-item" href="{{ route('barangmasuk.index') }}">Barang Masuk</a></li>
                            <li><a class="dropdown-item" href="{{ route('mutasibarang.index') }}">Mutasi Barang</a>
                            </li>
                            <li><a class="dropdown-item" href="{{ route('hutang.index') }}">Hutang</a></li>
                            <li><a class="dropdown-item" href="{{ route('bunga.index') }}">Bunga</a></li>
                            <li><a class="dropdown-item" href="{{ route('pembayaranhutang.index') }}">Pembayaran
                                    Hutang</a></li>
                            <li><a class="dropdown-item" href="{{ route('baranghutang.index') }}">Barang Hutang</a>
                            </li>
                            <li><a class="dropdown-item" href="{{ route('kuantitas.index') }}">Kuantitas</a></li>
                            <li><a class="dropdown-item" href="{{ route('admin.targetpencapaian.index') }}">Target
                                    Pencapaian</a></li>
                            <li><a class="dropdown-item" href="{{ route('aturantenggat.index') }}">Aturan Tenggat</a>
                            </li>
                            <li>
                                <form action="{{ route('logout') }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger">
                                        <i class="bi bi-box-arrow-right"></i> Logout
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </li>

                </ul>

                <!-- User -->
                <div class="dropdown ms-3">
                    <a href="#" class="d-flex align-items-center text-dark text-decoration-none dropdown-toggle"
                        id="dropdownUser" data-bs-toggle="dropdown">
                        <img src="https://via.placeholder.com/32/cccccc/333333?text=JD" alt="User Avatar" width="32"
                            height="32" class="rounded-circle me-2">
                        <span class="d-none d-md-inline">John Doe</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0">
                        <li><a class="dropdown-item" href="{{ url('/profile') }}">Profil</a></li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li><a class="dropdown-item" href="#">Keluar</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="main-content">
        @yield('content')
    </div>

    <!-- Bottom Sidebar -->
    <nav class="navbar navbar-expand navbar-light bg-light fixed-bottom bottom-sidebar">
        <div class="container-fluid">
            <ul class="navbar-nav w-100 d-flex flex-row justify-content-around">
                <li class="nav-item">
                    @auth
                    @if (Auth::user()->role === 'kasir')
                    <a href="{{ route('admin.warung.show', session('id_warung')) }}"
                        class="nav-link @if (Request::is('dashboard')) active @endif">
                        <i class="fas fa-home"></i>
                        <span>Dashboard</span>
                    </a>
                    @endif
                    @endauth


                    @guest
                    <a href="{{ route('home') }}"
                        class="nav-link @if (Request::is('/')) active @endif">
                        <i class="fas fa-home"></i>
                        <span>Dashboard</span>
                    </a>
                    @endguest
                </li>

                <li class="nav-item">
                    <a href="{{ route('stokbarang.index') }}"
                        class="nav-link @if (Request::is('stokbarang*')) active @endif">
                        <i class="fas fa-boxes"></i>
                        <span>Stok Barang</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('barangmasuk.index') }}"
                        class="nav-link @if (Request::is('barangmasuk*')) active @endif">
                        <i class="fas fa-cart-plus"></i>
                        <span>Barang Masuk</span>
                    </a>
                </li>


                <li class="nav-item">
                    <a href="{{ route('barangkeluar.index') }}"
                        class="nav-link @if (Request::is('barangkeluar*')) active @endif">
                        <i class="fas fa-dolly"></i>
                        <span>Barang Keluar</span>
                    </a>
                </li>
                @auth
                    @if (Auth::user()->role === 'kasir')
                <li class="nav-item">
                    <a href="{{ route('kaswarung.show', session('id_warung')) }}"
                        class="nav-link @if (Request::is('kaswarung*')) active @endif">
                        <i class="fas fa-wallet"></i>
                        <span>Kas</span>
                    </a>
                </li>
                @endif
                @endauth
                <!-- 🔹 Tambahan Menu Hutang -->
                <li class="nav-item">
                    <a href="{{ route('hutang.index') }}"
                        class="nav-link @if (Request::is('hutang*')) active @endif">
                        <i class="fas fa-file-invoice-dollar"></i>
                        <span>Hutang</span>
                    </a>
                </li>
                <!-- 🔹 End Tambahan -->
                {{-- <li class="nav-item">
                    <a href="{{ route('transaksikas.index', 1) }}"
                class="nav-link @if (Request::is('transaksikas*')) active @endif">
                <i class="fas fa-receipt"></i>
                <span>Transaksi</span>
                </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('transaksibarang.index', 1) }}"
                        class="nav-link @if (Request::is('transaksibarang*')) active @endif">
                        <i class="fas fa-exchange-alt"></i>
                        <span>Transaksi Barang</span>
                    </a>
                </li> --}}
                <li class="nav-item">
                    <a href="{{ route('mutasibarang.index', 1) }}"
                        class="nav-link @if (Request::is('mutasibarang*')) active @endif">
                        <i class="fas fa-random"></i>
                        <span>Mutasi</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ url('/profile') }}" class="nav-link @if (Request::is('profile')) active @endif">
                        <i class="fas fa-user-circle"></i>
                        <span>Profil</span>
                    </a>
                </li>
            </ul>

        </div>
    </nav>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>