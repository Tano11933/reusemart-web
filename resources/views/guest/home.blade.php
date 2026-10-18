@extends('layouts.guest')

@section('title', 'ReuseMart · Barang Bekas Berkualitas')
@section('description', 'Temukan elektronik, furnitur, fashion, dan perlengkapan lain berkualitas dengan harga bersahabat di ReuseMart.')

@section('content')
    {{-- Hero --}}
    <section class="rm-hero">
        <div class="container">
            <div class="row align-items-center g-4">
                <div class="col-lg-7">
                    <h1 class="display-5 mb-3">Beri barang layak pakai kesempatan kedua.</h1>
                    <p class="lead mb-4 rm-hero-copy">
                        Jelajahi barang titipan berkualitas: elektronik, furnitur, fashion, dan perlengkapan lain dengan harga bersahabat, sekaligus ikut mengurangi limbah.
                    </p>

                    <form class="rm-hero-search d-flex flex-column flex-sm-row gap-2 mb-4" method="GET" action="{{ route('guest.lihat_semua') }}" role="search">
                        <input type="search" name="query" class="form-control form-control-lg"
                               placeholder="Cari barang, misal: kamera, sofa, sepeda…"
                               value="{{ request('query') }}" aria-label="Cari barang">
                        <button class="btn rm-btn-light btn-lg" type="submit">
                            <i class="bi bi-search me-1"></i>Cari
                        </button>
                    </form>

                </div>
                <div class="col-lg-5 text-center">
                    <img class="rm-hero-mascot" src="{{ asset('image/a.png') }}" alt="Maskot ReuseMart">
                </div>
            </div>
        </div>
    </section>

    {{-- Kategori --}}
    <section class="container rm-home-categories py-4 py-lg-5">
        <div class="rm-section-head">
            <div>
                <h2 class="rm-section-title h4 mb-1">Kategori</h2>
                <p class="rm-page-sub">Pilih jenis barang yang sedang kamu cari.</p>
            </div>
            <a href="{{ route('guest.lihat_semua') }}" class="rm-section-link">Semua produk <i class="bi bi-arrow-right" aria-hidden="true"></i></a>
        </div>

        @if ($kategoris->isEmpty())
            <div class="rm-empty">
                <i class="bi bi-grid d-block mb-2" aria-hidden="true"></i>
                <p class="mb-3">Belum ada kategori yang tersedia saat ini.</p>
                <a href="{{ route('guest.lihat_semua') }}" class="btn rm-btn-outline">
                    <i class="bi bi-grid me-1" aria-hidden="true"></i>Lihat katalog
                </a>
            </div>
        @else
            <div class="row row-cols-2 row-cols-sm-3 row-cols-lg-4 row-cols-xxl-6 g-2 g-md-3">
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
    <section class="container rm-home-products pb-5">
        <div class="rm-section-head">
            <div>
                <h2 class="rm-section-title h4 mb-1">Barang terbaru</h2>
                <p class="rm-page-sub">Pilihan barang yang tersedia untuk dibeli.</p>
            </div>
            <a href="{{ route('guest.lihat_semua') }}" class="rm-section-link">Lihat semua <i class="bi bi-arrow-right" aria-hidden="true"></i></a>
        </div>

        @if ($barang->isEmpty())
            <div class="rm-empty">
                <i class="bi bi-box-seam d-block mb-2" aria-hidden="true"></i>
                <p class="mb-3">Belum ada barang yang tersedia. Cek kembali nanti, ya!</p>
                <a href="{{ route('guest.lihat_semua') }}" class="btn rm-btn-outline">
                    <i class="bi bi-grid me-1" aria-hidden="true"></i>Lihat katalog
                </a>
            </div>
        @else
            <div class="row row-cols-2 row-cols-sm-3 row-cols-md-4 row-cols-xl-5 row-cols-xxl-6 g-2 g-md-3">
                @foreach ($barang as $item)
                    <div class="col">
                        <x-product-card :item="$item" />
                    </div>
                @endforeach
            </div>

        @endif
    </section>
@endsection
