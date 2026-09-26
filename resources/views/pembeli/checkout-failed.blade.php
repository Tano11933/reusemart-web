@extends('layouts.buyer')

@section('title', 'Pembayaran Belum Selesai — ReuseMart')

@section('content')
    <section class="container py-5">
        <div class="rm-result-card rm-result-failed mx-auto">
            <span class="rm-result-icon"><i class="bi bi-arrow-counterclockwise"></i></span>
            <span class="rm-result-eyebrow">Pembayaran belum selesai</span>
            <h1 class="h3 fw-bold">Pesananmu belum dapat diproses</h1>
            <p class="rm-text-muted mb-4">
                Waktu pembayaran mungkin sudah berakhir atau pesanan dibatalkan. Kamu bisa kembali ke katalog untuk memilih barang lagi.
            </p>
            <div class="d-flex flex-column flex-sm-row justify-content-center gap-2">
                <a href="{{ route('home_beli') }}" class="btn rm-btn-primary">
                    <i class="bi bi-grid me-1"></i>Kembali ke Beranda
                </a>
                <a href="{{ route('pembeli.history') }}" class="btn rm-btn-outline">Lihat Riwayat</a>
            </div>
        </div>
    </section>
@endsection

@push('styles')
    <style>
        .rm-result-card {
            max-width: 620px;
            text-align: center;
            padding: clamp(2rem, 6vw, 3.5rem);
            border: 1px solid var(--rm-line);
            border-radius: var(--rm-radius-lg);
            background: #fff;
            box-shadow: var(--rm-shadow-sm);
        }

        .rm-result-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 76px;
            height: 76px;
            margin: 0 auto 1.25rem;
            border-radius: 50%;
            background: var(--rm-danger-soft);
            color: var(--rm-danger);
            font-size: 2rem;
        }

        .rm-result-eyebrow {
            display: block;
            color: var(--rm-danger);
            font-size: 0.78rem;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            margin-bottom: 0.5rem;
        }
    </style>
@endpush
