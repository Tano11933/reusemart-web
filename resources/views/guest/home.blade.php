@extends('layouts.guest')

@section('title', 'ReuseMart — Barang Bekas Berkualitas')
@section('description', 'Temukan elektronik, furnitur, fashion, dan perlengkapan lain berkualitas dengan harga bersahabat di ReuseMart.')

@section('content')
    {{-- Hero --}}
    <section class="rm-hero">
        <div class="container">
            <div class="row align-items-center g-4">
                <div class="col-lg-7">
                    <img class="rm-hero-logo mb-3" src="{{ asset('image/white.png') }}" alt="Logo ReuseMart">
                    <h1 class="display-5 mb-3">Beri Barang Layak Pakai Kesempatan Kedua</h1>
                    <p class="lead mb-4">
                        Jelajahi barang titipan berkualitas — dari elektronik, furnitur, sampai fashion —
                        dengan harga bersahabat sekaligus mengurangi limbah.
                    </p>

                    <form class="rm-hero-search d-flex flex-column flex-sm-row gap-2 mb-4" method="GET" action="{{ route('guest.lihat_semua') }}" role="search">
                        <input type="search" name="query" class="form-control form-control-lg"
                               placeholder="Cari barang, misal: kamera, sofa, sepeda…"
                               value="{{ request('query') }}" aria-label="Cari barang">
                        <button class="btn rm-btn-light btn-lg" type="submit">
                            <i class="bi bi-search me-1"></i>Cari
                        </button>
                    </form>

                    <div class="d-flex flex-wrap gap-2">
                        <div class="rm-stat">
                            <div class="rm-stat-value">{{ $barang->count() }}</div>
                            <div class="rm-stat-label">Barang pilihan</div>
                        </div>
                        <div class="rm-stat">
                            <div class="rm-stat-value">{{ $kategoris->count() }}</div>
                            <div class="rm-stat-label">Kategori</div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-5 text-center">
                    <img class="rm-hero-mascot" src="{{ asset('image/a.png') }}" alt="Maskot ReuseMart">
                </div>
            </div>
        </div>
    </section>

    {{-- Kategori --}}
    <section class="container py-5">
        <div class="text-center">
            <img src="{{ asset('image/b.png') }}" alt="" width="64" class="mb-2">
            <h2 class="rm-section-title h4 mb-4 d-block">Kategori</h2>
        </div>

        @if ($kategoris->isEmpty())
            <div class="rm-empty">
                <i class="bi bi-grid d-block mb-2"></i>
                Belum ada kategori yang tersedia saat ini.
            </div>
        @else
            <div class="row row-cols-2 row-cols-sm-3 row-cols-md-4 row-cols-lg-5 g-3 justify-content-center">
                @foreach ($kategoris->unique('kategori') as $kategoriItem)
                    <div class="col">
                        <a class="rm-category-card" href="{{ route('guest.per_kategori', ['kategori' => $kategoriItem->kategori]) }}">
                            <span class="rm-category-icon">
                                <x-category-icon :kategori="$kategoriItem->kategori" />
                            </span>
                            <span class="rm-category-name">{{ $kategoriItem->kategori }}</span>
                        </a>
                    </div>
                @endforeach
            </div>
        @endif
    </section>

    {{-- Barang terbaru --}}
    <section class="container pb-5">
        <h2 class="rm-section-title h4 mb-4 text-center d-block">Barang Terbaru</h2>

        @if ($barang->isEmpty())
            <div class="rm-empty">
                <i class="bi bi-box-seam d-block mb-2"></i>
                Belum ada barang yang tersedia. Cek kembali nanti, ya!
            </div>
        @else
            <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4 justify-content-center">
                @foreach ($barang as $item)
                    <div class="col">
                        <x-product-card :item="$item" />
                    </div>
                @endforeach
            </div>

            <div class="text-center mt-4">
                <a href="{{ route('guest.lihat_semua') }}" class="btn rm-btn-outline px-4">
                    Lihat Semua Produk <i class="bi bi-arrow-right ms-1"></i>
                </a>
            </div>
        @endif
    </section>
@endsection
