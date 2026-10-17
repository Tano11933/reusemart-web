<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'ReuseMart')</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/reusemart.css') }}">

    @stack('styles')
</head>
<body class="rm-body">

    <div class="rm-auth">
        <aside class="rm-auth-brand">
            <a href="{{ route('guest.home') }}" class="rm-auth-logo">
                <span class="rm-brand-mark"><i class="bi bi-recycle" aria-hidden="true"></i></span>
                <span>ReuseMart</span>
            </a>

            <div>
                <h2 class="mb-3">@yield('brand-title', 'Beri Barang Layak Pakai Kesempatan Kedua')</h2>
                <p class="mb-4">@yield('brand-text', 'ReuseMart menghubungkan penitip, pembeli, dan organisasi supaya barang layak pakai tidak berakhir jadi sampah.')</p>
                <img class="rm-auth-mascot" src="{{ asset('image/a.png') }}" alt="Maskot ReuseMart">
            </div>

            <a href="{{ route('guest.home') }}" class="rm-auth-back">
                <i class="bi bi-arrow-left me-1"></i>Kembali ke beranda
            </a>
        </aside>

        <main class="rm-auth-panel">
            <div class="rm-auth-card">
                @yield('content')
            </div>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
