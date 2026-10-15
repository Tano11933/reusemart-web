@extends('layouts.buyer')

@section('title', 'Pesanan Berhasil · ReuseMart')

@section('content')
    <section class="container py-5">
        <div class="rm-result-card rm-result-success mx-auto">
            <span class="rm-result-icon"><i class="bi bi-check2"></i></span>
            <span class="rm-result-eyebrow">Pembayaran dikirim</span>
            <h1 class="h3 fw-bold">Terima kasih atas pesananmu!</h1>
            <p class="rm-text-muted mb-4">
                Bukti pembayaran sudah diterima. Kami akan memeriksanya dan memperbarui status pesananmu.
            </p>
            <div class="d-flex flex-column flex-sm-row justify-content-center gap-2">
                <a href="{{ route('pembeli.history') }}" class="btn rm-btn-primary">
                    <i class="bi bi-clock-history me-1"></i>Lihat Riwayat Pesanan
                </a>
                <a href="{{ route('home_beli') }}" class="btn rm-btn-outline">Kembali Belanja</a>
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
            background: var(--rm-success-soft);
            color: var(--rm-success);
            font-size: 2.4rem;
        }

        .rm-result-eyebrow {
            display: block;
            color: var(--rm-success);
            font-size: 0.78rem;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            margin-bottom: 0.5rem;
        }
    </style>
@endpush
