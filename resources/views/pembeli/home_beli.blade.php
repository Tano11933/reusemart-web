@extends('layouts.buyer')

@section('title', 'Beranda Pembeli · ReuseMart')

@section('content')
    <section class="rm-hero">
        <div class="container">
            <div class="row align-items-center g-4">
                <div class="col-lg-7">
                    <p class="lead mb-1">Selamat datang kembali 👋</p>
                    <h1 class="display-6 fw-bold mb-3">
                        {{ $pembeli->nama_pembeli ?? 'Pembeli' }}
                    </h1>
                    <p class="lead mb-4">
                        Temukan barang titipan berkualitas hari ini. Cek dulu katalog terbaru kami.
                    </p>

                    <div class="d-flex flex-wrap gap-2">
                        @if ($pembeli)
                            <div class="rm-stat">
                                <div class="rm-stat-value">{{ number_format($pembeli->poin ?? 0, 0, ',', '.') }}</div>
                                <div class="rm-stat-label">Poin kamu</div>
                            </div>
                        @endif
                        <div class="rm-stat">
                            <div class="rm-stat-value">{{ $barang->count() }}</div>
                            <div class="rm-stat-label">Barang tersedia</div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-5 text-center">
                    <img class="rm-hero-mascot" src="{{ asset('image/b.png') }}" alt="Maskot ReuseMart">
                </div>
            </div>
        </div>
    </section>

    <section class="container py-5">
        <div class="rm-section-head">
            <h2 class="rm-section-title h4 mb-0">Barang Titipan Terbaru</h2>
            <a href="{{ route('lihat_semua_beli') }}" class="btn rm-btn-outline btn-sm px-3">
                Lihat Semua <i class="bi bi-arrow-right ms-1" aria-hidden="true"></i>
            </a>
        </div>

        @if ($barang->isEmpty())
            <div class="rm-empty">
                <i class="bi bi-box-seam d-block mb-2" aria-hidden="true"></i>
                <p class="mb-3">Belum ada barang yang tersedia. Cek kembali nanti, ya!</p>
                <a href="{{ route('lihat_semua_beli') }}" class="btn rm-btn-outline">
                    <i class="bi bi-grid me-1" aria-hidden="true"></i>Buka katalog
                </a>
            </div>
        @else
            <div class="row row-cols-2 row-cols-sm-3 row-cols-md-4 row-cols-xl-5 row-cols-xxl-6 g-2 g-md-3 justify-content-center">
                @foreach ($barang->take(8) as $item)
                    <div class="col">
                        <x-product-card :item="$item" detail-route="detail_barang_beli">
                            <x-slot:action>
                                <form method="POST" action="{{ route('add_to_cart', $item->id_barang) }}">
                                    @csrf
                                    <button type="submit" class="btn rm-btn-primary w-100">
                                        <i class="bi bi-cart-plus me-1" aria-hidden="true"></i>Beli
                                    </button>
                                </form>
                            </x-slot:action>
                        </x-product-card>
                    </div>
                @endforeach
            </div>
        @endif
    </section>
@endsection
