@extends('layouts.buyer')

@section('title', 'Beranda Pembeli · ReuseMart')

@section('content')
    <section class="rm-buyer-welcome">
        <div class="container">
            <div class="rm-buyer-welcome-main">
                <div>
                    <p class="rm-buyer-eyebrow mb-1">Akun pembeli</p>
                    <h1>{{ $pembeli->nama_pembeli ?? 'Pembeli' }}</h1>
                    <p class="mb-0">Cari barang titipan yang cocok untuk rumahmu.</p>
                </div>

                @if ($pembeli)
                    <div class="rm-buyer-points" aria-label="Poin tersedia: {{ number_format($pembeli->poin ?? 0, 0, ',', '.') }}">
                        <span class="rm-buyer-points-icon"><i class="bi bi-stars" aria-hidden="true"></i></span>
                        <span><small>Poin tersedia</small><strong>{{ number_format($pembeli->poin ?? 0, 0, ',', '.') }}</strong></span>
                    </div>
                @endif
            </div>
        </div>
    </section>

    <section class="container rm-buyer-products py-4 py-lg-5">
        <div class="rm-section-head">
            <div>
                <h2 class="rm-section-title h4 mb-1">Barang tersedia</h2>
                <p class="rm-page-sub">Barang titipan yang bisa langsung kamu lihat.</p>
            </div>
            <a href="{{ route('lihat_semua_beli') }}" class="rm-section-link">Lihat katalog <i class="bi bi-arrow-right" aria-hidden="true"></i></a>
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
            <div class="row row-cols-2 row-cols-sm-3 row-cols-md-4 row-cols-xl-5 row-cols-xxl-6 g-2 g-md-3">
                @foreach ($barang->take(12) as $item)
                    <div class="col">
                        <x-product-card :item="$item" detail-route="detail_barang_beli">
                            <x-slot:action>
                                <form method="POST" action="{{ route('add_to_cart', $item->id_barang) }}">
                                    @csrf
                                    <button type="submit" class="btn rm-btn-primary w-100">
                                        <i class="bi bi-cart-plus me-1" aria-hidden="true"></i>Tambah ke keranjang
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
