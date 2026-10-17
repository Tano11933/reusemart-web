<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'ReuseMart · Barang Bekas Berkualitas')</title>
    <meta name="description" content="@yield('description', 'ReuseMart: marketplace barang bekas berkualitas. Beri barang layak pakai kesempatan kedua.')">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/reusemart.css') }}">

    @stack('styles')
</head>
<body class="rm-body d-flex flex-column min-vh-100">

    <nav class="navbar navbar-expand-lg rm-navbar sticky-top py-2">
        <div class="container">
            <a class="navbar-brand text-white" href="{{ route('guest.home') }}">
                <img src="{{ asset('image/white.png') }}" alt="Logo ReuseMart">
                <span>ReuseMart</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#guestNavbar" aria-controls="guestNavbar" aria-expanded="false" aria-label="Buka navigasi">
                <i class="bi bi-list fs-4 text-white" aria-hidden="true"></i>
            </button>

            <div class="collapse navbar-collapse" id="guestNavbar">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('guest.home') ? 'active' : '' }}" href="{{ route('guest.home') }}">
                            <i class="bi bi-house-door me-1"></i>Home
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('guest.lihat_semua', 'guest.per_kategori', 'guest.detail_barang') ? 'active' : '' }}" href="{{ route('guest.lihat_semua') }}">
                            <i class="bi bi-grid me-1"></i>Produk
                        </a>
                    </li>
                </ul>

                @unless (request()->routeIs('guest.home'))
                    <form class="rm-search d-flex me-lg-3 my-2 my-lg-0" method="GET" action="{{ route('guest.lihat_semua') }}" role="search">
                        <input class="form-control" type="search" name="query" placeholder="Cari barang…" value="{{ request('query') }}" aria-label="Cari barang">
                    </form>
                @endunless

                <div class="d-flex">
                    <a href="{{ route('login') }}" class="btn rm-btn-light">
                        <i class="bi bi-box-arrow-in-right me-1"></i>Login
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <main class="flex-grow-1">
        @yield('content')
    </main>

    <footer class="rm-footer">
        <div class="container">
            <div class="row g-4">
                <div class="col-md-5">
                    <h5 class="mb-2">ReuseMart</h5>
                    <p class="mb-0">A Second Life for Quality Goods, A Better Life for the Planet.</p>
                </div>
                <div class="col-md-3">
                    <h6 class="mb-3">Jelajahi</h6>
                    <nav class="rm-footer-links d-flex flex-column" aria-label="Jelajahi">
                        <a href="{{ route('guest.home') }}">Beranda</a>
                        <a href="{{ route('guest.lihat_semua') }}">Semua produk</a>
                        <a href="{{ route('login') }}">Masuk akun</a>
                    </nav>
                </div>
                <div class="col-md-4">
                    <h6 class="mb-3">Hubungi Kami</h6>
                    <div class="rm-footer-contact">
                        <p class="mb-1"><i class="bi bi-envelope me-2" aria-hidden="true"></i><a href="mailto:ReuseMart@gmail.com">ReuseMart@gmail.com</a></p>
                        <p class="mb-1"><i class="bi bi-telephone me-2" aria-hidden="true"></i><a href="tel:+62987654321">+62 987 654 321</a></p>
                        <p class="mb-0"><i class="bi bi-geo-alt me-2" aria-hidden="true"></i>Yogyakarta, Indonesia</p>
                    </div>
                </div>
            </div>
            <div class="rm-footer-bottom d-flex flex-column flex-md-row justify-content-between gap-2">
                <span>&copy; 2025 ReuseMart. All rights reserved.</span>
                <span>Versi 2 &middot; diperbarui 2026</span>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
