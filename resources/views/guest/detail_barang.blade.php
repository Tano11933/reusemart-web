@extends('layouts.guest')

@section('title', $item->nama_barang . ' · ReuseMart')
@section('description', \Illuminate\Support\Str::limit(strip_tags($item->deskripsi_barang), 150))

@push('styles')
    <style>
        #modalImage {
            max-height: 88vh;
            object-fit: contain;
        }
    </style>
@endpush

@section('content')
    @php
        // Kumpulkan foto yang benar-benar ada; kalau tidak ada, pakai placeholder.
        $photoUrls = [];

        foreach ([$item->foto_barang ?? null, $item->foto_barang2 ?? null] as $foto) {
            $foto = trim((string) $foto);

            if ($foto === '') {
                continue;
            }

            if (preg_match('#^https?://#i', $foto)) {
                $photoUrls[] = $foto;
            } elseif (file_exists(public_path('image/' . ltrim($foto, '/')))) {
                $photoUrls[] = asset('image/' . ltrim($foto, '/'));
            }
        }

        if (empty($photoUrls)) {
            $photoUrls[] = asset('image/placeholder.svg');
        }

        $penitip = $item->penitip;
        $rating = optional($penitip)->rating;
        $fullStars = $rating ? (int) floor($rating) : 0;
        $halfStar = $rating ? ($rating - $fullStars) >= 0.5 : false;
        $emptyStars = $rating ? 5 - $fullStars - ($halfStar ? 1 : 0) : 0;
    @endphp

    <div class="container py-5">
        <nav class="rm-breadcrumb small mb-3" aria-label="Breadcrumb">
            <a href="{{ route('guest.home') }}">Home</a>
            <span class="mx-1">/</span>
            <a href="{{ route('guest.lihat_semua') }}">Produk</a>
            <span class="mx-1">/</span>
            <a href="{{ route('guest.per_kategori', ['kategori' => $item->kategori]) }}">{{ $item->kategori }}</a>
            <span class="mx-1">/</span>
            <span class="rm-text-muted">{{ $item->nama_barang }}</span>
        </nav>

        <div class="rm-detail-card">
            <div class="row g-4 g-lg-5">
                <div class="col-lg-6">
                    <div id="productCarousel" class="carousel slide" data-bs-interval="false">
                        <div class="carousel-inner rm-detail-media">
                            @foreach ($photoUrls as $index => $photoUrl)
                                <div class="carousel-item {{ $index === 0 ? 'active' : '' }} h-100">
                                    <img src="{{ $photoUrl }}"
                                         class="d-block w-100 h-100 zoom-trigger"
                                         data-full="{{ $photoUrl }}"
                                         alt="{{ $item->nama_barang }} , foto {{ $index + 1 }}">
                                </div>
                            @endforeach
                        </div>

                        @if (count($photoUrls) > 1)
                            <button class="carousel-control-prev rm-gallery-nav" type="button"
                                    data-bs-target="#productCarousel" data-bs-slide="prev">
                                <i class="bi bi-chevron-left" aria-hidden="true"></i>
                                <span class="visually-hidden">Foto sebelumnya</span>
                            </button>
                            <button class="carousel-control-next rm-gallery-nav" type="button"
                                    data-bs-target="#productCarousel" data-bs-slide="next">
                                <i class="bi bi-chevron-right" aria-hidden="true"></i>
                                <span class="visually-hidden">Foto berikutnya</span>
                            </button>

                            <div class="rm-gallery-thumbnails" aria-label="Pilih foto produk">
                                @foreach ($photoUrls as $index => $photoUrl)
                                    <button type="button" class="rm-gallery-thumb {{ $index === 0 ? 'active' : '' }}"
                                            data-bs-target="#productCarousel" data-bs-slide-to="{{ $index }}"
                                            aria-label="Tampilkan foto {{ $index + 1 }}">
                                        <img src="{{ $photoUrl }}" alt="">
                                    </button>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

                <div class="col-lg-6 rm-detail-info">
                    <div class="d-flex flex-wrap gap-2 mb-2">
                        <span class="rm-chip">
                            <i class="bi bi-tag" aria-hidden="true"></i>{{ $item->kategori }}
                        </span>
                        @if ($item->garansi)
                            <span class="rm-chip rm-chip-success">
                                <i class="bi bi-shield-check" aria-hidden="true"></i>Bergaransi
                            </span>
                        @endif
                    </div>

                    <h1 class="h3 fw-bold mb-3">{{ $item->nama_barang }}</h1>

                    <div class="mb-3">
                        <span class="rm-seller-chip">
                            <i class="bi bi-person-circle" aria-hidden="true"></i>
                            {{ $penitip->nama_penitip ?? 'Penitip' }}
                            @if (!is_null($rating))
                                <span class="rm-rating">
                                    <i class="bi bi-star-fill" aria-hidden="true"></i>
                                    {{ number_format($rating, 1) }}
                                </span>
                            @else
                                <span class="rm-text-muted">Penjual baru</span>
                            @endif
                        </span>
                    </div>

                    <div class="rm-price-box mb-3">
                        <div class="rm-text-muted small mb-1">Harga</div>
                        <div class="rm-price">Rp {{ number_format($item->harga_barang, 0, ',', '.') }}</div>
                        @if ($item->garansi)
                            <div class="small mt-1 text-success">
                                <i class="bi bi-shield-check me-1" aria-hidden="true"></i>
                                Bergaransi
                                @if ($item->tgl_garansi)
                                    s.d. {{ \Carbon\Carbon::parse($item->tgl_garansi)->translatedFormat('d F Y') }}
                                @endif
                            </div>
                        @else
                            <div class="small mt-1 rm-text-muted">
                                <i class="bi bi-shield-slash me-1" aria-hidden="true"></i>Tidak bergaransi
                            </div>
                        @endif
                    </div>

                    <div class="d-grid gap-2 d-sm-flex mb-2">
                        <a href="{{ route('login') }}" class="btn rm-btn-primary btn-lg flex-fill">
                            <i class="bi bi-cart-check me-1" aria-hidden="true"></i>Beli Sekarang
                        </a>
                        <a href="{{ route('login') }}" class="btn rm-btn-outline btn-lg flex-fill">
                            <i class="bi bi-cart-plus me-1" aria-hidden="true"></i>Keranjang
                        </a>
                    </div>
                    <p class="rm-text-muted small">
                        <i class="bi bi-info-circle me-1" aria-hidden="true"></i>
                        Login sebagai pembeli untuk membeli atau menambahkan barang ke keranjang.
                    </p>

                    <dl class="row rm-detail-list small mb-0">
                        <dt class="col-5 col-sm-4">Dititipkan sejak</dt>
                        <dd class="col-7 col-sm-8">
                            {{ $item->tgl_titip ? \Carbon\Carbon::parse($item->tgl_titip)->translatedFormat('d F Y') : '-' }}
                        </dd>
                        <dt class="col-5 col-sm-4">ID Barang</dt>
                        <dd class="col-7 col-sm-8">{{ $item->id_barang }}</dd>
                    </dl>
                </div>
            </div>

            <div class="rm-detail-section">
                <h2 class="rm-detail-section-title">
                    <i class="bi bi-file-text" aria-hidden="true"></i>Deskripsi
                </h2>
                <p class="rm-detail-description">{{ $item->deskripsi_barang ?: 'Penitip belum menuliskan deskripsi untuk barang ini.' }}</p>
            </div>
        </div>

        @if ($serupa->isNotEmpty())
            <section class="mt-5 rm-related">
                <div class="rm-section-head">
                    <h2 class="h4 fw-bold mb-0">Barang Serupa</h2>
                    <a href="{{ route('guest.per_kategori', ['kategori' => $item->kategori]) }}" class="btn rm-btn-outline btn-sm px-3">
                        Lihat Kategori <i class="bi bi-arrow-right ms-1" aria-hidden="true"></i>
                    </a>
                </div>

                <div class="row row-cols-2 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-3 g-lg-4 justify-content-center">
                    @foreach ($serupa as $lain)
                        <div class="col">
                            <x-product-card :item="$lain" />
                        </div>
                    @endforeach
                </div>
            </section>
        @endif

        {{-- Diskusi terkunci untuk pengunjung --}}
        <div class="rm-locked-discussion mt-4">
            <span class="rm-lock-icon mb-3">
                <i class="bi bi-lock-fill text-warning fs-4"></i>
            </span>
            <h2 class="h6 fw-bold mb-2">Diskusi Produk Terkunci</h2>
            <p class="rm-text-muted mb-3">
                Untuk melihat dan ikut berdiskusi tentang produk ini, silakan login terlebih dahulu.
            </p>
            <a href="{{ route('login') }}" class="btn rm-btn-primary px-4">
                <i class="bi bi-box-arrow-in-right me-1"></i>Login Sekarang
            </a>
        </div>
    </div>

    {{-- Modal pratinjau foto --}}
    <div class="modal fade" id="imageModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content bg-transparent border-0">
                <div class="modal-body text-center p-0">
                    <img src="" id="modalImage" class="img-fluid" alt="Pratinjau foto barang">
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.querySelectorAll('.zoom-trigger').forEach(function (img) {
            img.addEventListener('click', function () {
                document.getElementById('modalImage').src = this.dataset.full;
                new bootstrap.Modal(document.getElementById('imageModal')).show();
            });
        });

        const productGallery = document.getElementById('productCarousel');
        if (productGallery) {
            productGallery.addEventListener('slide.bs.carousel', function (event) {
                productGallery.querySelectorAll('[data-bs-slide-to]').forEach(function (thumb, index) {
                    thumb.classList.toggle('active', index === event.to);
                });
            });
        }
    </script>
@endpush
