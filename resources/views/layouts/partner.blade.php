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
    <link rel="stylesheet" href="{{ asset('css/reusemart.css') }}?v={{ filemtime(public_path('css/reusemart.css')) }}">
    @stack('styles')
</head>
<body class="rm-body d-flex flex-column min-vh-100">
    @php($role = session('role'))
    <nav class="navbar navbar-expand-lg rm-navbar sticky-top">
        <div class="container">
            <a class="navbar-brand text-white" href="{{ $role === 'organisasi' ? url('/organisasi/index_organisasi') : route('penitip.barangTitipan') }}">
                <img src="{{ asset('image/white.png') }}" alt="Logo ReuseMart"><span>ReuseMart</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#partnerNavbar" aria-controls="partnerNavbar" aria-expanded="false" aria-label="Buka navigasi">
                <i class="bi bi-list fs-4 text-white" aria-hidden="true"></i>
            </button>
            <div class="collapse navbar-collapse" id="partnerNavbar">
                @if ($role === 'organisasi')
                    <ul class="navbar-nav me-auto">
                        <li class="nav-item"><a class="nav-link {{ request()->is('organisasi/index_organisasi') ? 'active' : '' }}" href="{{ url('/organisasi/index_organisasi') }}"><i class="bi bi-inbox me-1"></i>Request Donasi</a></li>
                    </ul>
                    <span class="text-white-50 small me-3">{{ session('nama', 'Organisasi') }}</span>
                @else
                    <ul class="navbar-nav me-auto">
                        <li class="nav-item"><a class="nav-link {{ request()->routeIs('penitip.barangTitipan') ? 'active' : '' }}" href="{{ route('penitip.barangTitipan') }}"><i class="bi bi-box-seam me-1"></i>Barang Titipan</a></li>
                        <li class="nav-item"><a class="nav-link {{ request()->routeIs('penitip.history-penjualan') ? 'active' : '' }}" href="{{ route('penitip.history-penjualan') }}"><i class="bi bi-graph-up me-1"></i>Riwayat Penjualan</a></li>
                    </ul>
                    <a href="{{ route('penitip.profil_penitip') }}" class="btn rm-btn-light me-2"><i class="bi bi-person me-1"></i>Profil</a>
                @endif
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn rm-btn-light"><i class="bi bi-box-arrow-right me-1"></i>Keluar</button>
                </form>
            </div>
        </div>
    </nav>

    <main class="flex-grow-1">
        @if (session('success'))
            <div class="container pt-3"><div class="alert alert-success" role="status">{{ session('success') }}</div></div>
        @endif
        @if (session('error'))
            <div class="container pt-3"><div class="alert alert-danger" role="alert">{{ session('error') }}</div></div>
        @endif
        @yield('content')
    </main>

    <footer class="rm-footer">
        <div class="container">
            <div class="row g-4">
                <div class="col-md-5"><h5>ReuseMart</h5><p class="mb-0">A Second Life for Quality Goods, A Better Life for the Planet.</p></div>
                <div class="col-md-3">
                    <h6>Tautan</h6>
                    @if ($role === 'organisasi')
                        <p class="mb-1"><a href="{{ url('/organisasi/index_organisasi') }}">Request donasi</a></p>
                    @else
                        <p class="mb-1"><a href="{{ route('penitip.barangTitipan') }}">Barang titipan</a></p>
                        <p class="mb-1"><a href="{{ route('penitip.history-penjualan') }}">Riwayat penjualan</a></p>
                        <p class="mb-0"><a href="{{ route('penitip.profil_penitip') }}">Profil saya</a></p>
                    @endif
                </div>
                <div class="col-md-4">
                    <h6>Hubungi Kami</h6>
                    <p class="mb-1"><i class="bi bi-envelope me-2"></i><a href="mailto:ReuseMart@gmail.com">ReuseMart@gmail.com</a></p>
                    <p class="mb-1"><i class="bi bi-telephone me-2"></i><a href="tel:+62987654321">+62 987 654 321</a></p>
                    <p class="mb-0"><i class="bi bi-geo-alt me-2"></i>Yogyakarta, Indonesia</p>
                </div>
            </div>
            <div class="rm-footer-bottom d-flex justify-content-between flex-wrap gap-2"><span>&copy; 2025 ReuseMart.</span><span>Versi 2 &middot; diperbarui 2026</span></div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @stack('scripts')
</body>
</html>
