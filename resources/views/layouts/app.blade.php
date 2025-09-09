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
            /* Space for fixed headbar */
            padding-bottom: 72px;
            /* Space for fixed bottombar */
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

    <!-- Headbar (Top Navbar) -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light fixed-top headbar">
        <div class="container-fluid">
            <a class="navbar-brand d-flex align-items-center" href="{{ url('/dashboard') }}">
                <i class="fas fa-chart-bar me-2 text-primary"></i>
                <span class="fw-bold">Dashboard App</span>
            </a>
            <div class="ms-auto">
                <div class="dropdown">
                    <a href="#" class="d-flex align-items-center text-dark text-decoration-none dropdown-toggle" id="dropdownUser" data-bs-toggle="dropdown" aria-expanded="false">
                        <img src="https://via.placeholder.com/32/cccccc/333333?text=JD" alt="User Avatar" width="32" height="32" class="rounded-circle me-2">
                        <span class="d-none d-md-inline">John Doe</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0" aria-labelledby="dropdownUser">
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

    <!-- Bottom Sidebar (Fixed Navbar), now visible on all screen sizes -->
    <nav class="navbar navbar-expand navbar-light bg-light fixed-bottom bottom-sidebar">
        <div class="container-fluid">
            <ul class="navbar-nav w-100 d-flex flex-row justify-content-around">
                <li class="nav-item">
                    <a href="{{ url('/dashboard') }}" class="nav-link @if(Request::is('dashboard')) active @endif">
                        <i class="fas fa-home"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ url('/users') }}" class="nav-link @if(Request::is('users')) active @endif">
                        <i class="fas fa-users"></i>
                        <span>Pengguna</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ url('/products') }}" class="nav-link @if(Request::is('products')) active @endif">
                        <i class="fas fa-box"></i>
                        <span>Produk</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ url('/profile') }}" class="nav-link @if(Request::is('profile')) active @endif">
                        <i class="fas fa-user-circle"></i>
                        <span>Profil</span>
                    </a>
                </li>
            </ul>
        </div>
    </nav>

    <!-- Bootstrap JS CDN -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>