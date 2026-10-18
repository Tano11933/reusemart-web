@extends('layouts.buyer')

@section('title', 'Keranjang Belanja · ReuseMart')

@php
    $jumlahBarang = count($cartItems);
    $grandTotal = collect($cartItems)->sum(fn ($item) => ($item['harga_barang'] ?? 0) * ($item['quantity'] ?? 1));
    $adaMasalah = collect($cartItems)->contains(fn ($item) => ($item['status'] ?? null) !== 'tersedia');
@endphp

@section('content')
    <section class="container py-4 py-lg-5">
        <nav class="rm-breadcrumb small mb-3" aria-label="Breadcrumb">
            <a href="{{ route('home_beli') }}">Home</a>
            <span class="mx-1">/</span>
            <span class="rm-text-muted">Keranjang</span>
        </nav>

        <div class="rm-page-head">
            <div class="rm-page-head-main">
                <span class="rm-page-head-icon" aria-hidden="true"><i class="bi bi-cart3"></i></span>
                <div>
                    <h1 class="rm-page-title">Keranjang Belanja</h1>
                    <p class="rm-page-sub">{{ $jumlahBarang }} barang siap diproses.</p>
                </div>
            </div>
        </div>

        @if ($jumlahBarang === 0)
            <div class="rm-empty">
                <i class="bi bi-cart-x d-block mb-2" aria-hidden="true"></i>
                <p class="mb-3">Keranjang kamu masih kosong.</p>
                <a href="{{ route('lihat_semua_beli') }}" class="btn rm-btn-primary">
                    <i class="bi bi-grid me-1" aria-hidden="true"></i>Mulai belanja
                </a>
            </div>
        @else
            @if ($adaMasalah)
                <div class="alert alert-warning d-flex align-items-center gap-2" role="alert">
                    <i class="bi bi-exclamation-triangle-fill" aria-hidden="true"></i>
                    <div>Ada barang di keranjangmu yang sudah tidak tersedia. Hapus barang tersebut untuk melanjutkan checkout.</div>
                </div>
            @endif

            <div class="row g-4 align-items-start">
                <div class="col-lg-7">
                    <section class="rm-checkout-card">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <h2 class="h5 fw-bold mb-0">Daftar barang</h2>
                            <span class="rm-chip"><i class="bi bi-basket2"></i>{{ $jumlahBarang }} barang</span>
                        </div>

                        <div class="rm-cart-list">
                            @foreach ($cartItems as $id => $item)
                                @php
                                    $total = ($item['harga_barang'] ?? 0) * ($item['quantity'] ?? 1);
                                    $status = $item['status'] ?? 'tersedia';
                                    $tersedia = $status === 'tersedia';

                                    $foto = trim((string) ($item['foto_barang'] ?? ''));
                                    $img = ($foto !== '' && file_exists(public_path('image/' . ltrim($foto, '/'))))
                                        ? asset('image/' . ltrim($foto, '/'))
                                        : asset('image/placeholder.svg');
                                @endphp
                                <article class="rm-cart-row {{ $tersedia ? '' : 'is-sold' }}">
                                    <img src="{{ $img }}" alt="{{ $item['nama_barang'] }}" class="rm-cart-row-img">
                                    <div class="rm-cart-row-body">
                                        <div class="rm-cart-row-top">
                                            <div>
                                                <a href="{{ route('detail_barang_beli', $id) }}" class="rm-cart-row-name">{{ $item['nama_barang'] }}</a>
                                                <div class="rm-cart-row-price">
                                                    Rp {{ number_format($item['harga_barang'] ?? 0, 0, ',', '.') }} · {{ $item['quantity'] ?? 1 }} barang
                                                </div>
                                            </div>
                                            <strong class="rm-cart-row-line">Rp {{ number_format($total, 0, ',', '.') }}</strong>
                                        </div>
                                        <div class="rm-cart-row-foot">
                                            @if ($tersedia)
                                                <span class="rm-status-pill rm-status-success">
                                                    <i class="bi bi-check-circle" aria-hidden="true"></i>Tersedia
                                                </span>
                                            @else
                                                <span class="rm-status-pill rm-status-danger">
                                                    <i class="bi bi-x-circle" aria-hidden="true"></i>{{ $status === 'terjual' ? 'Terjual' : 'Tidak tersedia' }}
                                                </span>
                                            @endif
                                            <form action="{{ route('remove_from_cart', $id) }}" method="POST"
                                                  onsubmit="return confirm('Hapus barang ini dari keranjang?');">
                                                @csrf
                                                @method('DELETE')
                                                <button class="rm-cart-remove" type="submit">
                                                    <i class="bi bi-trash" aria-hidden="true"></i>Hapus
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </article>
                            @endforeach
                        </div>

                        <div class="rm-cart-note">
                            <i class="bi bi-info-circle" aria-hidden="true"></i>
                            <span>Satu barang hanya bisa dibeli oleh satu pembeli, jadi jumlah setiap barang selalu 1.</span>
                        </div>
                    </section>

                    <a href="{{ route('lihat_semua_beli') }}" class="btn rm-btn-outline mt-3">
                        <i class="bi bi-arrow-left me-1" aria-hidden="true"></i>Lanjut belanja
                    </a>
                </div>

                <aside class="col-lg-5">
                    <section class="rm-checkout-summary">
                        <h2 class="h5 fw-bold mb-0">Ringkasan Belanja</h2>

                        <div class="rm-summary-lines">
                            <div><span>Jumlah barang</span><strong>{{ $jumlahBarang }} barang</strong></div>
                            <div><span>Subtotal</span><strong>Rp {{ number_format($grandTotal, 0, ',', '.') }}</strong></div>
                            <div><span>Ongkos kirim</span><strong>Dihitung saat checkout</strong></div>
                        </div>

                        <div class="rm-checkout-total">
                            <span>Total sementara</span>
                            <strong>Rp {{ number_format($grandTotal, 0, ',', '.') }}</strong>
                        </div>

                        <a href="{{ $adaMasalah ? '#' : route('checkout.index') }}"
                           class="btn rm-btn-primary w-100 btn-lg mt-3 {{ $adaMasalah ? 'disabled' : '' }}"
                           @if ($adaMasalah) onclick="return false;" aria-disabled="true" @endif>
                            Lanjut ke Checkout <i class="bi bi-arrow-right ms-1" aria-hidden="true"></i>
                        </a>
                        @if ($adaMasalah)
                            <p class="small rm-text-muted text-center mt-2 mb-0">Hapus barang yang tidak tersedia dulu untuk melanjutkan.</p>
                        @endif
                    </section>
                </aside>
            </div>
        @endif
    </section>
@endsection
