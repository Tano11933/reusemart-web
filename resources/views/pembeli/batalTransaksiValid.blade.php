@extends('layouts.buyer')

@section('title', 'Status Transaksi · ReuseMart')

@section('content')
    <section class="container py-4 py-lg-5">
        <nav class="rm-breadcrumb small mb-3" aria-label="Breadcrumb">
            <a href="{{ route('home_beli') }}">Home</a>
            <span class="mx-1">/</span>
            <span class="rm-text-muted">Status Transaksi</span>
        </nav>

        <div class="rm-page-head">
            <div class="rm-page-head-main">
                <span class="rm-page-head-icon" aria-hidden="true"><i class="bi bi-arrow-repeat"></i></span>
                <div>
                    <h1 class="rm-page-title">Status Transaksi</h1>
                    <p class="rm-page-sub">Pesanan yang sudah disiapkan bisa dibatalkan dari halaman ini.</p>
                </div>
            </div>
        </div>

        @if ($pemesanan->isEmpty())
            <div class="rm-empty">
                <i class="bi bi-check2-circle d-block" aria-hidden="true"></i>
                <h2 class="h5 fw-bold">Tidak ada pesanan yang perlu ditindaklanjuti</h2>
                <p class="mb-3">Pesanan siap ambil atau kirim akan muncul di sini.</p>
                <a href="{{ route('lihat_semua_beli') }}" class="btn rm-btn-primary">
                    <i class="bi bi-grid me-1" aria-hidden="true"></i>Jelajahi Produk
                </a>
            </div>
        @else
            <div class="d-grid gap-3">
                @foreach ($pemesanan as $order)
                    <article class="rm-cancel-order-card">
                        <div class="rm-cancel-order-main">
                            <span class="rm-order-icon" aria-hidden="true"><i class="bi bi-receipt"></i></span>
                            <div class="min-w-0">
                                <div class="fw-bold">Pesanan {{ $order->id_pemesanan }}</div>
                                <div class="small rm-text-muted">{{ $order->tgl_pesan?->translatedFormat('d F Y') ?? 'Tanggal tidak tersedia' }}</div>
                            </div>
                        </div>
                        <div class="rm-cancel-order-meta">
                            <span class="rm-status-pill rm-status-info"><i class="bi bi-check-circle me-1" aria-hidden="true"></i>Siap diproses</span>
                            <span class="small rm-text-muted">{{ $order->jenis_pengantaran === 'diambil' ? 'Ambil sendiri' : 'Pengiriman kurir' }}</span>
                            <strong>Rp {{ number_format($order->total_harga ?? 0, 0, ',', '.') }}</strong>
                        </div>
                        <form class="rm-cancel-form" action="{{ route('transaksi-valid.batal', ['id' => $order->id_pemesanan]) }}" method="POST">
                            @csrf
                            <input type="hidden" name="status" value="dibatalkan pembeli">
                            <button type="submit" class="btn btn-outline-danger rounded-pill">
                                <i class="bi bi-x-circle me-1" aria-hidden="true"></i>Batalkan Pesanan
                            </button>
                        </form>
                    </article>
                @endforeach
            </div>
        @endif
    </section>
@endsection

@push('styles')
    <style>
        .rm-cancel-order-card {
            display: grid;
            grid-template-columns: minmax(190px, 1fr) minmax(190px, 1fr) auto;
            align-items: center;
            gap: 1.2rem;
            padding: 1.1rem 1.25rem;
            border: 1px solid var(--rm-line);
            border-radius: var(--rm-radius-lg);
            background: #fff;
            box-shadow: var(--rm-shadow-xs);
            transition: border-color var(--rm-base), box-shadow var(--rm-base);
        }

        .rm-cancel-order-card:hover {
            border-color: var(--rm-line-strong);
            box-shadow: var(--rm-shadow-sm);
        }

        .rm-cancel-order-main {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            min-width: 0;
        }

        .rm-cancel-order-meta {
            display: grid;
            justify-items: start;
            gap: 0.35rem;
        }

        .rm-cancel-order-meta > strong {
            color: var(--rm-primary-dark);
        }

        @media (max-width: 767.98px) {
            .rm-cancel-order-card {
                grid-template-columns: minmax(0, 1fr) auto;
                gap: 0.8rem;
            }

            .rm-cancel-order-meta {
                grid-column: 1;
            }

            .rm-cancel-form {
                grid-column: 2;
                grid-row: 1 / span 2;
            }

            .rm-cancel-form .btn {
                font-size: 0.78rem;
                padding: 0.45rem 0.65rem;
            }
        }

        @media (max-width: 399.98px) {
            .rm-cancel-order-card {
                grid-template-columns: 1fr;
            }

            .rm-cancel-order-meta,
            .rm-cancel-form {
                grid-column: 1;
                grid-row: auto;
            }

            .rm-cancel-form .btn {
                width: 100%;
            }
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.querySelectorAll('.rm-cancel-form').forEach(function (form) {
            form.addEventListener('submit', function (event) {
                event.preventDefault();
                Swal.fire({
                    title: 'Batalkan pesanan?',
                    text: 'Status pesanan akan dibatalkan dan stok dikembalikan.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, batalkan',
                    cancelButtonText: 'Kembali',
                    confirmButtonColor: '#c0392b',
                }).then(result => {
                    if (result.isConfirmed) HTMLFormElement.prototype.submit.call(form);
                });
            });
        });
    </script>
@endpush
