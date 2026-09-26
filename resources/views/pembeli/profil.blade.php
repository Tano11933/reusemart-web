@extends('layouts.buyer')

@section('title', 'Profil Saya — ReuseMart')

@section('content')
    <section class="container py-4 py-lg-5">
        <nav class="rm-breadcrumb small mb-3" aria-label="Breadcrumb">
            <a href="{{ route('home_beli') }}">Home</a>
            <span class="mx-1">/</span>
            <span class="rm-text-muted">Profil Saya</span>
        </nav>

        <div class="mb-4">
            <h1 class="rm-page-title">Profil Saya</h1>
            <p class="rm-page-sub">Kelola informasi akun dan pintasan aktivitas belanjamu.</p>
        </div>

        <div class="row g-4">
            <div class="col-lg-4">
                <section class="rm-profile-summary">
                    <span class="rm-profile-avatar" aria-hidden="true"><i class="bi bi-person-fill"></i></span>
                    <h2 class="h4 fw-bold mt-3 mb-1">{{ $pembeli->nama_pembeli }}</h2>
                    <p class="rm-text-muted mb-3">{{ $pembeli->email }}</p>
                    <span class="rm-points-card">
                        <i class="bi bi-stars"></i>
                        <span>
                            <small>Poin tersedia</small>
                            <strong>{{ number_format($pembeli->poin ?? 0, 0, ',', '.') }}</strong>
                        </span>
                    </span>
                </section>

                <nav class="rm-profile-nav mt-3" aria-label="Menu akun">
                    <a href="{{ route('pembeli.history') }}"><i class="bi bi-clock-history"></i>Riwayat Pembelian<i class="bi bi-chevron-right ms-auto"></i></a>
                    <a href="{{ route('pembeli.alamat') }}"><i class="bi bi-geo-alt"></i>Kelola Alamat<i class="bi bi-chevron-right ms-auto"></i></a>
                    <a href="{{ route('batalTransaksiValid') }}"><i class="bi bi-receipt"></i>Status Transaksi<i class="bi bi-chevron-right ms-auto"></i></a>
                </nav>
            </div>

            <div class="col-lg-8">
                <section class="rm-account-card">
                    <div class="d-flex align-items-start justify-content-between gap-3 mb-4">
                        <div>
                            <h2 class="h5 fw-bold mb-1">Informasi Akun</h2>
                            <p class="rm-text-muted small mb-0">Data ini digunakan untuk transaksi dan komunikasi.</p>
                        </div>
                        <span class="rm-account-status"><i class="bi bi-patch-check-fill me-1"></i>Akun Pembeli</span>
                    </div>

                    <dl class="rm-account-details">
                        <div>
                            <dt>Nama lengkap</dt>
                            <dd>{{ $pembeli->nama_pembeli }}</dd>
                        </div>
                        <div>
                            <dt>ID Pembeli</dt>
                            <dd>{{ $pembeli->id_pembeli }}</dd>
                        </div>
                        <div>
                            <dt>Email</dt>
                            <dd>{{ $pembeli->email }}</dd>
                        </div>
                        <div>
                            <dt>Nomor telepon</dt>
                            <dd>{{ $pembeli->nomor_telepon ?: 'Belum diisi' }}</dd>
                        </div>
                        <div>
                            <dt>Jenis kelamin</dt>
                            <dd>{{ $pembeli->jenis_kelamin ?: 'Belum diisi' }}</dd>
                        </div>
                        <div>
                            <dt>Tanggal lahir</dt>
                            <dd>
                                {{ $pembeli->tgl_lahir ? \Carbon\Carbon::parse($pembeli->tgl_lahir)->translatedFormat('d F Y') : 'Belum diisi' }}
                            </dd>
                        </div>
                    </dl>

                    <div class="d-flex flex-column flex-sm-row gap-2 mt-4">
                        <a href="{{ route('pembeli.alamat') }}" class="btn rm-btn-primary">
                            <i class="bi bi-geo-alt me-1"></i>Kelola Alamat
                        </a>
                        <a href="{{ route('pembeli.history') }}" class="btn rm-btn-outline">
                            <i class="bi bi-clock-history me-1"></i>Lihat Riwayat
                        </a>
                    </div>
                </section>

                <section class="rm-account-card rm-account-danger mt-3">
                    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3">
                        <div>
                            <h2 class="h6 fw-bold mb-1">Keluar dari akun</h2>
                            <p class="rm-text-muted small mb-0">Sesi belanja akan diakhiri di perangkat ini.</p>
                        </div>
                        <form id="logoutForm" method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="btn btn-outline-danger rounded-pill px-4">
                                <i class="bi bi-box-arrow-right me-1"></i>Keluar
                            </button>
                        </form>
                    </div>
                </section>
            </div>
        </div>
    </section>
@endsection

@push('styles')
    <style>
        .rm-profile-summary,
        .rm-account-card {
            background: #fff;
            border: 1px solid var(--rm-line);
            border-radius: var(--rm-radius-lg);
            box-shadow: var(--rm-shadow-xs);
            padding: 1.5rem;
        }

        .rm-profile-summary {
            text-align: center;
            padding: 2rem 1.5rem;
        }

        .rm-profile-avatar {
            width: 84px;
            height: 84px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: var(--rm-primary-soft);
            color: var(--rm-primary);
            font-size: 2.5rem;
            border: 4px solid #fff;
            box-shadow: 0 0 0 1px var(--rm-line), var(--rm-shadow-sm);
        }

        .rm-points-card {
            display: inline-flex;
            align-items: center;
            gap: 0.75rem;
            text-align: left;
            padding: 0.7rem 1rem;
            border: 1px solid #f2dfb4;
            border-radius: var(--rm-radius);
            background: var(--rm-warning-soft);
            color: #835f14;
        }

        .rm-points-card > i {
            font-size: 1.45rem;
        }

        .rm-points-card small,
        .rm-points-card strong {
            display: block;
        }

        .rm-points-card small {
            font-size: 0.72rem;
        }

        .rm-points-card strong {
            font-size: 1.1rem;
        }

        .rm-profile-nav {
            display: grid;
            gap: 0.5rem;
        }

        .rm-profile-nav a {
            display: flex;
            align-items: center;
            gap: 0.7rem;
            padding: 0.85rem 1rem;
            border: 1px solid var(--rm-line);
            border-radius: var(--rm-radius);
            color: var(--rm-ink-soft);
            background: #fff;
            font-weight: 600;
            transition: background-color var(--rm-fast), border-color var(--rm-fast), color var(--rm-fast);
        }

        .rm-profile-nav a > i:first-child {
            color: var(--rm-primary);
            font-size: 1.05rem;
        }

        .rm-profile-nav a:hover {
            border-color: var(--rm-line-strong);
            background: var(--rm-primary-soft-2);
            color: var(--rm-primary-dark);
        }

        .rm-account-status {
            white-space: nowrap;
            border: 1px solid #cbe9d9;
            border-radius: var(--rm-radius-pill);
            background: var(--rm-success-soft);
            color: var(--rm-success);
            padding: 0.35rem 0.7rem;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .rm-account-details {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 0;
            margin: 0;
            border-top: 1px solid var(--rm-line);
        }

        .rm-account-details > div {
            padding: 0.9rem 0.75rem;
            border-bottom: 1px solid var(--rm-line);
        }

        .rm-account-details dt {
            color: var(--rm-muted);
            font-size: 0.78rem;
            font-weight: 600;
            margin-bottom: 0.2rem;
        }

        .rm-account-details dd {
            color: var(--rm-ink);
            font-weight: 600;
            margin: 0;
            overflow-wrap: anywhere;
        }

        .rm-account-danger {
            border-color: #f0d9d6;
        }

        @media (max-width: 575.98px) {
            .rm-account-details {
                grid-template-columns: 1fr;
            }

            .rm-account-details > div {
                padding-left: 0;
                padding-right: 0;
            }

            .rm-account-status {
                font-size: 0;
                padding: 0.45rem 0.6rem;
            }

            .rm-account-status i {
                font-size: 0.9rem;
                margin: 0 !important;
            }
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.getElementById('logoutForm')?.addEventListener('submit', function (event) {
            event.preventDefault();

            Swal.fire({
                title: 'Keluar dari akun?',
                text: 'Sesi belanja di perangkat ini akan diakhiri.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, keluar',
                cancelButtonText: 'Tetap masuk',
                confirmButtonColor: '#c0392b',
            }).then(result => {
                if (result.isConfirmed) this.submit();
            });
        });
    </script>
@endpush
