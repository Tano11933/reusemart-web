@extends('layouts.buyer')

@section('title', 'Katalog Produk — ReuseMart')

@section('content')
    <section class="container py-5">
        <nav class="rm-breadcrumb small mb-2" aria-label="Breadcrumb">
            <a href="{{ route('home_beli') }}">Home</a>
            <span class="mx-1">/</span>
            <span class="rm-text-muted">Produk</span>
        </nav>

        <div class="mb-3">
            <h1 class="h3 rm-page-title">Katalog Barang</h1>
            <p class="rm-page-sub">
                {{ $barang->count() }} barang tersedia
                @if (request('query'))
                    untuk pencarian &ldquo;{{ request('query') }}&rdquo;
                @endif
            </p>
        </div>

        <form method="GET" action="{{ route('lihat_semua_beli') }}" class="rm-filter-bar card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-body d-flex flex-column flex-lg-row align-items-stretch align-items-lg-center gap-3">
                <div class="input-group flex-grow-1">
                    <span class="input-group-text bg-white border-end-0 ps-3">
                        <i class="bi bi-search rm-text-muted"></i>
                    </span>
                    <input type="search" name="query" value="{{ request('query') }}"
                           class="form-control border-start-0"
                           placeholder="Cari nama atau kategori…" aria-label="Cari barang">
                </div>

                <div class="form-check form-switch d-flex align-items-center gap-2 mb-0 ps-0 rm-nowrap">
                    <input class="form-check-input ms-0" type="checkbox" role="switch"
                           id="garansiSwitch" name="garansi" value="1"
                           @checked(request('garansi') === '1') onchange="this.form.submit()">
                    <label class="form-check-label" for="garansiSwitch">
                        <i class="bi bi-shield-check me-1"></i>Bergaransi
                    </label>
                </div>

                <button type="submit" class="btn rm-btn-primary px-4">Cari</button>
            </div>
        </form>

        @if ($barang->isEmpty())
            <div class="rm-empty">
                <i class="bi bi-search d-block mb-2"></i>
                <p class="mb-3">Tidak ada barang yang cocok dengan filter kamu.</p>
                <a href="{{ route('lihat_semua_beli') }}" class="btn rm-btn-outline">
                    <i class="bi bi-arrow-counterclockwise me-1"></i>Reset pencarian
                </a>
            </div>
        @else
            <div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 g-3 g-md-4 justify-content-center">
                @foreach ($barang as $item)
                    <div class="col">
                        <x-product-card :item="$item" detail-route="detail_barang_beli">
                            <x-slot:action>
                                <form method="POST" action="{{ route('add_to_cart', $item->id_barang) }}">
                                    @csrf
                                    <button type="submit" class="btn rm-btn-primary w-100">
                                        <i class="bi bi-cart-plus me-1"></i>Beli
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
