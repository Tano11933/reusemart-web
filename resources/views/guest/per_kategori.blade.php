@extends('layouts.guest')

@section('title', $kategori . ' · ReuseMart')
@section('description', 'Daftar barang kategori ' . $kategori . ' yang tersedia di ReuseMart.')

@section('content')
    <section class="container py-4 py-lg-5">
        <nav class="rm-breadcrumb small mb-3" aria-label="Breadcrumb">
            <a href="{{ route('guest.home') }}">Home</a>
            <span class="mx-1">/</span>
            <a href="{{ route('guest.lihat_semua') }}">Produk</a>
            <span class="mx-1">/</span>
            <span class="rm-text-muted">{{ $kategori }}</span>
        </nav>

        <div class="rm-page-head">
            <div class="rm-page-head-main">
                <span class="rm-page-head-icon" aria-hidden="true">
                    <x-category-icon :kategori="$kategori" />
                </span>
                <div>
                    <h1 class="rm-page-title">{{ $kategori }}</h1>
                    <p class="rm-page-sub">{{ $barangs->count() }} barang tersedia</p>
                </div>
            </div>
            <div class="rm-page-head-aside">
                <a href="{{ route('guest.lihat_semua') }}" class="btn rm-btn-soft btn-sm">
                    <i class="bi bi-grid me-1" aria-hidden="true"></i>Semua produk
                </a>
            </div>
        </div>

        @if ($barangs->isEmpty())
            <div class="rm-empty">
                <i class="bi bi-box-seam d-block mb-2" aria-hidden="true"></i>
                <p class="mb-3">Belum ada barang tersedia di kategori ini.</p>
                <a href="{{ route('guest.lihat_semua') }}" class="btn rm-btn-outline">
                    <i class="bi bi-grid me-1" aria-hidden="true"></i>Lihat semua produk
                </a>
            </div>
        @else
            <div class="row row-cols-2 row-cols-sm-3 row-cols-md-4 row-cols-xl-5 row-cols-xxl-6 g-2 g-md-3 justify-content-center">
                @foreach ($barangs as $item)
                    <div class="col">
                        <x-product-card :item="$item" />
                    </div>
                @endforeach
            </div>
        @endif
    </section>
@endsection
