<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'ReuseMart')</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/reusemart.css') }}">

    @stack('styles')
</head>
<body class="rm-body d-flex flex-column min-vh-100">

    @php
        $pembeli = $pembeli ?? null;
        $cartCount = count(session('cart', []));
    @endphp

    <nav class="navbar navbar-expand-lg rm-navbar sticky-top py-2">
        <div class="container">
            <a class="navbar-brand text-white" href="{{ route('home_beli') }}">
                <span class="rm-brand-mark"><i class="bi bi-recycle" aria-hidden="true"></i></span>
                <span>ReuseMart</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#buyerNavbar" aria-controls="buyerNavbar" aria-expanded="false" aria-label="Buka navigasi">
                <i class="bi bi-list fs-4 text-white" aria-hidden="true"></i>
            </button>

            <div class="collapse navbar-collapse" id="buyerNavbar">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('home_beli') ? 'active' : '' }}" href="{{ route('home_beli') }}">
                            <i class="bi bi-house-door me-1"></i>Home
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('lihat_semua_beli', 'detail_barang_beli') ? 'active' : '' }}" href="{{ route('lihat_semua_beli') }}">
                            <i class="bi bi-grid me-1"></i>Produk
                        </a>
                    </li>
                </ul>

                <form class="rm-search d-flex me-lg-3 my-2 my-lg-0" method="GET" action="{{ route('lihat_semua_beli') }}" role="search">
                    <input class="form-control" type="search" name="query" placeholder="Cari barang…" value="{{ request('query') }}" aria-label="Cari barang">
                </form>

                <div class="d-flex align-items-center gap-2">
                    <a href="{{ route('cart.index') }}" class="rm-cart-btn" aria-label="Keranjang belanja">
                        <i class="bi bi-cart3 fs-5"></i>
                        @if ($cartCount > 0)
                            <span class="badge rounded-pill bg-warning text-dark">{{ $cartCount }}</span>
                        @endif
                    </a>

                    @if ($pembeli)
                        <div class="dropdown">
                            <button class="btn rm-btn-light dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-person-circle me-1"></i>{{ \Illuminate\Support\Str::limit($pembeli->nama_pembeli, 14) }}
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                                <li>
                                    <a class="dropdown-item" href="{{ route('pembeli-profile', ['id' => $pembeli->id_pembeli]) }}">
                                        <i class="bi bi-person me-2"></i>Profil
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('pembeli.history') }}">
                                        <i class="bi bi-clock-history me-2"></i>Riwayat Pembelian
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('batalTransaksiValid') }}">
                                        <i class="bi bi-arrow-repeat me-2"></i>Transaksi
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="dropdown-item text-danger">
                                            <i class="bi bi-box-arrow-right me-2"></i>Keluar
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="btn rm-btn-light">
                            <i class="bi bi-box-arrow-in-right me-1"></i>Login
                        </a>
                    @endif
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
                        <a href="{{ route('home_beli') }}">Beranda</a>
                        <a href="{{ route('lihat_semua_beli') }}">Katalog produk</a>
                        <a href="{{ route('cart.index') }}">Keranjang belanja</a>
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        @if (session('success') || session('cart_success'))
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: @json(session('success') ?? session('cart_success')),
                confirmButtonColor: '#1E5B5A',
            });
        @endif

        @if (session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: @json(session('error')),
                confirmButtonColor: '#1E5B5A',
            });
        @endif
    </script>
    @stack('scripts')
</body>
</html>
