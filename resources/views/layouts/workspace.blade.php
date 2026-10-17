<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Workspace · ReuseMart')</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/reusemart.css') }}">

    @stack('styles')
</head>
<body class="rm-body rm-workspace-body">
    @php
        $jabatan = session('jabatan', 'Pegawai');
        $namaPegawai = session('nama', 'Pegawai ReuseMart');
    @endphp

    <div class="rm-workspace-shell">
        <div class="rm-workspace-overlay" id="workspaceOverlay"></div>

        <aside class="rm-workspace-sidebar" id="workspaceSidebar" aria-label="Navigasi workspace">
            <div class="rm-workspace-sidebar-top">
                <a class="rm-workspace-brand" href="{{ route('guest.home') }}">
                    <span class="rm-brand-mark"><i class="bi bi-recycle" aria-hidden="true"></i></span>
                    <span><strong>ReuseMart</strong><small>Workspace</small></span>
                </a>

                <div class="rm-workspace-role">
                    <span class="rm-workspace-role-icon"><i class="bi bi-person-workspace"></i></span>
                    <span><small>Masuk sebagai</small><strong>{{ $jabatan }}</strong></span>
                </div>

                <nav class="rm-workspace-nav">
                    @if ($jabatan === 'Owner')
                        <span class="rm-workspace-nav-label">Operasional</span>
                        <a href="{{ route('owner.requestRDonasi') }}" class="{{ request()->routeIs('owner.requestRDonasi') ? 'active' : '' }}"><i class="bi bi-inbox"></i>Request Donasi</a>
                        <a href="{{ route('owner.ownerHistoryDonasi') }}" class="{{ request()->routeIs('owner.ownerHistoryDonasi') ? 'active' : '' }}"><i class="bi bi-heart-history"></i>Riwayat Donasi</a>

                        <span class="rm-workspace-nav-label mt-3">Laporan</span>
                        <a href="{{ route('owner.laporanPenjualanBulanan') }}" class="{{ request()->routeIs('owner.laporanPenjualanBulanan') ? 'active' : '' }}"><i class="bi bi-graph-up-arrow"></i>Penjualan Bulanan</a>
                        <a href="{{ route('owner.laporanKomisiBulanan') }}" class="{{ request()->routeIs('owner.laporanKomisiBulanan') ? 'active' : '' }}"><i class="bi bi-cash-coin"></i>Komisi Bulanan</a>
                        <a href="{{ route('owner.laporanStokGudang') }}" class="{{ request()->routeIs('owner.laporanStokGudang') ? 'active' : '' }}"><i class="bi bi-box-seam"></i>Stok Gudang</a>
                        <a href="{{ route('owner.laporanPenjualanKategori') }}" class="{{ request()->routeIs('owner.laporanPenjualanKategori') ? 'active' : '' }}"><i class="bi bi-pie-chart"></i>Penjualan Kategori</a>
                        <a href="{{ route('owner.laporanBarangExpired') }}" class="{{ request()->routeIs('owner.laporanBarangExpired') ? 'active' : '' }}"><i class="bi bi-hourglass-bottom"></i>Barang Kedaluwarsa</a>
                        <a href="{{ route('donasi.laporan') }}" class="{{ request()->routeIs('donasi.laporan') ? 'active' : '' }}"><i class="bi bi-box2-heart"></i>Laporan Donasi</a>
                        <a href="{{ route('requestdonasi.laporan') }}" class="{{ request()->routeIs('requestdonasi.laporan') ? 'active' : '' }}"><i class="bi bi-card-checklist"></i>Request Donasi</a>
                        <a href="{{ route('transaksi.penitip.laporan') }}" class="{{ request()->routeIs('transaksi.penitip.laporan') ? 'active' : '' }}"><i class="bi bi-person-lines-fill"></i>Transaksi Penitip</a>
                    @elseif ($jabatan === 'Admin')
                        <span class="rm-workspace-nav-label">Pengaturan</span>
                        <a href="{{ route('pegawai.admin') }}" class="{{ request()->routeIs('pegawai.admin') ? 'active' : '' }}"><i class="bi bi-people"></i>Data Pegawai</a>
                        <a href="{{ route('jabatan.admin') }}" class="{{ request()->routeIs('jabatan.admin') ? 'active' : '' }}"><i class="bi bi-person-gear"></i>Data Jabatan</a>
                        <a href="{{ route('organisasi.admin') }}" class="{{ request()->routeIs('organisasi.admin') ? 'active' : '' }}"><i class="bi bi-building"></i>Data Organisasi</a>
                        <a href="{{ url('/merchandise') }}" class="{{ request()->is('merchandise*') ? 'active' : '' }}"><i class="bi bi-gift"></i>Merchandise</a>
                    @elseif ($jabatan === 'Customer Service')
                        <span class="rm-workspace-nav-label">Layanan</span>
                        <a href="{{ route('pegawai.cs') }}" class="{{ request()->routeIs('pegawai.cs') ? 'active' : '' }}"><i class="bi bi-speedometer2"></i>Dashboard CS</a>
                        <a href="{{ route('verifikasi-pembayaran.cs') }}" class="{{ request()->routeIs('verifikasi-pembayaran.cs') ? 'active' : '' }}"><i class="bi bi-credit-card"></i>Verifikasi Pembayaran</a>
                        <a href="{{ route('diskusi.index') }}" class="{{ request()->routeIs('diskusi.index') ? 'active' : '' }}"><i class="bi bi-chat-square-text"></i>Diskusi Produk</a>
                        <a href="{{ route('cs.tukarPoin') }}" class="{{ request()->routeIs('cs.tukarPoin') ? 'active' : '' }}"><i class="bi bi-coin"></i>Penukaran Poin</a>
                        <a href="{{ route('cs.klaimMerchandise') }}" class="{{ request()->routeIs('cs.klaimMerchandise') ? 'active' : '' }}"><i class="bi bi-gift"></i>Klaim Merchandise</a>
                    @elseif ($jabatan === 'Gudang')
                        <span class="rm-workspace-nav-label">Operasional Gudang</span>
                        <a href="{{ route('pegawai.gudang') }}" class="{{ request()->routeIs('pegawai.gudang', 'pegawai.gudang.index_gudang') ? 'active' : '' }}"><i class="bi bi-box-seam"></i>Barang Gudang</a>
                        <a href="{{ route('gudang.ambil-barang') }}" class="{{ request()->routeIs('gudang.ambil-barang') ? 'active' : '' }}"><i class="bi bi-box-arrow-in-down"></i>Pengambilan Titipan</a>
                        <a href="{{ route('transaksi.diambil') }}" class="{{ request()->routeIs('transaksi.diambil') ? 'active' : '' }}"><i class="bi bi-shop"></i>Pesanan Diambil</a>
                        <a href="{{ route('transaksi.dikirim') }}" class="{{ request()->routeIs('transaksi.dikirim') ? 'active' : '' }}"><i class="bi bi-truck"></i>Pesanan Dikirim</a>
                        <a href="{{ route('nota.pemesanan') }}" class="{{ request()->routeIs('nota.pemesanan') ? 'active' : '' }}"><i class="bi bi-receipt"></i>Nota Pesanan</a>
                    @else
                        <span class="rm-workspace-nav-label">Menu</span>
                        <a href="{{ route('pegawai.home') }}" class="active"><i class="bi bi-house"></i>Beranda</a>
                    @endif
                </nav>
            </div>

            <div class="rm-workspace-sidebar-bottom">
                <a href="{{ route('guest.home') }}" class="rm-workspace-back"><i class="bi bi-arrow-left"></i>Kembali ke situs</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="rm-workspace-logout"><i class="bi bi-box-arrow-right"></i>Keluar</button>
                </form>
                <small>&copy; 2025 ReuseMart</small>
            </div>
        </aside>

        <div class="rm-workspace-main">
            <header class="rm-workspace-topbar">
                <button type="button" class="rm-workspace-toggle" id="workspaceToggle" aria-label="Buka menu" aria-expanded="false">
                    <i class="bi bi-list"></i>
                </button>
                <div class="rm-workspace-topbar-title">
                    <span>@yield('page-title', 'Workspace')</span>
                    <small>{{ $jabatan }} &middot; {{ $namaPegawai }}</small>
                </div>
                <a href="{{ route('guest.home') }}" class="rm-workspace-public-link"><i class="bi bi-box-arrow-up-right"></i><span>Lihat situs</span></a>
            </header>

            <main class="rm-workspace-content">
                @if (session('success'))
                    <div class="alert alert-success" role="status">{{ session('success') }}</div>
                @endif
                @if (session('error'))
                    <div class="alert alert-danger" role="alert">{{ session('error') }}</div>
                @endif
                @if ($errors->any())
                    <div class="alert alert-danger" role="alert">
                        <strong>Periksa kembali data yang diisi.</strong>
                        <ul class="mb-0 mt-1 ps-3">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                    </div>
                @endif
                @yield('content')
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        (function () {
            const sidebar = document.getElementById('workspaceSidebar');
            const overlay = document.getElementById('workspaceOverlay');
            const toggle = document.getElementById('workspaceToggle');

            function closeMenu() {
                sidebar.classList.remove('is-open');
                overlay.classList.remove('is-visible');
                toggle.setAttribute('aria-expanded', 'false');
                document.body.classList.remove('rm-workspace-menu-open');
            }

            toggle.addEventListener('click', function () {
                const open = sidebar.classList.toggle('is-open');
                overlay.classList.toggle('is-visible', open);
                toggle.setAttribute('aria-expanded', String(open));
                document.body.classList.toggle('rm-workspace-menu-open', open);
            });

            overlay.addEventListener('click', closeMenu);
            sidebar.querySelectorAll('a').forEach(link => link.addEventListener('click', closeMenu));
            document.addEventListener('keydown', event => {
                if (event.key === 'Escape') closeMenu();
            });
        })();
    </script>
    @stack('scripts')
</body>
</html>
