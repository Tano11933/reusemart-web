@extends('layouts.guest')

@section('title', $kategori . ' · ReuseMart')
@section('description', 'Daftar barang kategori ' . $kategori . ' yang tersedia di ReuseMart.')

@section('content')
    <section class="container py-5">
        <nav class="rm-breadcrumb small mb-2" aria-label="Breadcrumb">
            <a href="{{ route('guest.home') }}">Home</a>
            <span class="mx-1">/</span>
            <a href="{{ route('guest.lihat_semua') }}">Produk</a>
            <span class="mx-1">/</span>
            <span class="rm-text-muted">{{ $kategori }}</span>
        </nav>

        <div class="d-flex align-items-center gap-3 mb-4">
            <span class="rm-category-icon flex-shrink-0">
                <x-category-icon :kategori="$kategori" />
            </span>
            <div>
                <h1 class="h3 rm-page-title">{{ $kategori }}</h1>
                <p class="rm-page-sub">{{ $barangs->count() }} barang tersedia</p>
            </div>
        </div>

        @if ($barangs->isEmpty())
            <div class="rm-empty">
                <i class="bi bi-box-seam d-block mb-2"></i>
                <p class="mb-3">Belum ada barang tersedia di kategori ini.</p>
                <a href="{{ route('guest.lihat_semua') }}" class="btn rm-btn-outline">
                    <i class="bi bi-grid me-1"></i>Lihat semua produk
                </a>
            </div>
        @else
            <div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 g-3 g-md-4 justify-content-center">
                @foreach ($barangs as $item)
                    <div class="col">
                        <x-product-card :item="$item" />
                    </div>
                @endforeach
            </div>
        @endif
    </section>
@endsection
