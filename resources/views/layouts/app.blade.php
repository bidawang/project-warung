<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') - Pengelolaan Data</title>
    <link rel="icon" type="image/png" href="/image/icon.png">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    @vite(['resources/js/app.js'])

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

    <div class="offcanvas offcanvas-start" tabindex="-1" id="sidebarMenu" aria-labelledby="sidebarMenuLabel">
        <div class="offcanvas-header bg-primary text-white">
            <h5 class="offcanvas-title" id="sidebarMenuLabel">Menu Utama</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"></button>
        </div>
        <div class="offcanvas-body p-0">
            <div class="list-group list-group-flush">
                <div class="list-group-item bg-light small fw-bold text-muted">OPERASIONAL</div>
                <a href="{{ url('kasir/pulsa') }}" class="list-group-item list-group-item-action border-0 py-3">
                    <i class="fas fa-signal me-3 text-primary"></i> Pulsa & PPOB
                </a>
                <a href="{{ url('/kasir/member') }}" class="list-group-item list-group-item-action border-0 py-3">
                    <i class="fas fa-address-card me-3 text-primary"></i> Manajemen Member
                </a>

                <div class="list-group-item bg-light small fw-bold text-muted">LOGISTIK & STOK</div>
                <a href="{{ url('/kasir/stok-barang/barang-masuk') }}"
                    class="list-group-item list-group-item-action border-0 py-3">
                    <i class="fas fa-file-import me-3 text-success"></i> Input Barang Masuk
                </a>
                <a href="{{ url('/kasir/hutangBarangamMasuk') }}"
                    class="list-group-item list-group-item-action border-0 py-3">
                    <i class="fas fa-file-invoice me-3 text-success"></i> Hutang ke Supplier
                </a>
                <a href="{{ route('kasir.mutasibarang.index') }}"
                    class="list-group-item list-group-item-action border-0 py-3">
                    <i class="fas fa-right-left me-3 text-success"></i> Mutasi Barang
                </a>

                <div class="list-group-item bg-light small fw-bold text-muted">LAPORAN</div>
                <a href="{{ url('/kasir/riwayat-transaksi') }}"
                    class="list-group-item list-group-item-action border-0 py-3">
                    <i class="fas fa-clock-rotate-left me-3 text-warning"></i> Riwayat Transaksi
                </a>
                <a href="{{ route('kasir.laporan-kas.index') }}"
                    class="list-group-item list-group-item-action border-0 py-3">
                    <i class="fas fa-file-invoice-dollar me-3 text-warning"></i> Laporan & Arus Kas
                </a>
            </div>
        </div>
    </div>

    <nav class="navbar navbar-expand navbar-light bg-light fixed-top headbar">
        <div class="container-fluid">
            <button class="btn border-0 me-2" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebarMenu">
                <i class="fas fa-bars fs-4 text-primary"></i>
            </button>
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
    /* Logic scroll dihapus karena 4 menu pasti muat di layar */ 
}">
    <div class="container-fluid p-0 h-100">
        <ul class="list-unstyled mb-0 d-flex w-100 h-100 align-items-center justify-content-around">

            {{-- 1. KASIR --}}
            <li class="nav-item flex-fill">
                <a href="{{ url('kasir') }}" class="nav-link @if (Request::is('kasir')) active @endif">
                    <i class="fas fa-calculator"></i>
                    <span>Kasir</span>
                </a>
            </li>

            {{-- 2. STOK --}}
            <li class="nav-item flex-fill">
                <a href="{{ url('/kasir/stok-barang') }}" class="nav-link @if (Request::is('kasir/stok-barang*')) active @endif">
                    <i class="fas fa-cubes"></i>
                    <span>Stok</span>
                </a>
            </li>

            {{-- 3. HUTANG --}}
            <li class="nav-item flex-fill">
                <a href="{{ url('/kasir/hutang') }}" class="nav-link @if (Request::is('kasir/hutang*')) active @endif">
                    <i class="fas fa-hand-holding-dollar"></i>
                    <span>Hutang</span>
                </a>
            </li>

            {{-- 4. LAPORAN --}}
            <li class="nav-item flex-fill">
                <a href="{{ route('kasir.laporan-kas.index') }}" class="nav-link @if (Request::is('kasir/laporan-kas*') || Request::is('kasir/kas*')) active @endif">
                    <i class="fas fa-file-invoice-dollar"></i>
                    <span>Laporan</span>
                </a>
            </li>

        </ul>
    </div>
</nav>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>



</html>
