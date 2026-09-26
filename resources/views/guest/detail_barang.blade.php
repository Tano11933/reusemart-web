@extends('layouts.guest')

@section('title', $item->nama_barang . ' — ReuseMart')
@section('description', \Illuminate\Support\Str::limit(strip_tags($item->deskripsi_barang), 150))

@push('styles')
    <style>
        .rm-detail-indicators {
            position: static;
            margin: 0.9rem 0 0;
        }

        .rm-detail-indicators [data-bs-target] {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background-color: #cfe0de;
            border: 0;
            opacity: 1;
        }

        .rm-detail-indicators .active {
            background-color: var(--rm-primary);
        }

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
                                <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                                    <img src="{{ $photoUrl }}"
                                         class="d-block w-100 h-100 zoom-trigger"
                                         data-full="{{ $photoUrl }}"
                                         alt="{{ $item->nama_barang }} — foto {{ $index + 1 }}">
                                </div>
                            @endforeach
                        </div>

                        @if (count($photoUrls) > 1)
                            <div class="carousel-indicators rm-detail-indicators d-flex justify-content-center gap-2">
                                @foreach ($photoUrls as $index => $photoUrl)
                                    <button type="button" data-bs-target="#productCarousel"
                                            data-bs-slide-to="{{ $index }}"
                                            class="{{ $index === 0 ? 'active' : '' }}"
                                            aria-label="Foto {{ $index + 1 }}"></button>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

                <div class="col-lg-6">
                    <span class="badge rounded-pill text-bg-light border mb-2">
                        <i class="bi bi-tag me-1"></i>{{ $item->kategori }}
                    </span>

                    <h1 class="h3 fw-bold mb-3">{{ $item->nama_barang }}</h1>

                    <div class="mb-3">
                        <span class="rm-seller-chip">
                            <i class="bi bi-person-circle"></i>
                            {{ $penitip->nama_penitip ?? 'Penitip' }}
                            @if (!is_null($rating))
                                <span class="rm-rating">
                                    <i class="bi bi-star-fill"></i>
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
                                <i class="bi bi-shield-check me-1"></i>
                                Bergaransi
                                @if ($item->tgl_garansi)
                                    s.d. {{ \Carbon\Carbon::parse($item->tgl_garansi)->translatedFormat('d F Y') }}
                                @endif
                            </div>
                        @else
                            <div class="small mt-1 rm-text-muted">
                                <i class="bi bi-shield-slash me-1"></i>Tidak bergaransi
                            </div>
                        @endif
                    </div>

                    <div class="d-grid gap-2 d-sm-flex mb-2">
                        <a href="{{ route('login') }}" class="btn rm-btn-primary btn-lg flex-fill">
                            <i class="bi bi-cart-check me-1"></i>Beli Sekarang
                        </a>
                        <a href="{{ route('login') }}" class="btn rm-btn-outline btn-lg flex-fill">
                            <i class="bi bi-cart-plus me-1"></i>Keranjang
                        </a>
                    </div>
                    <p class="rm-text-muted small">
                        <i class="bi bi-info-circle me-1"></i>
                        Login sebagai pembeli untuk membeli atau menambahkan barang ke keranjang.
                    </p>

                    <dl class="row rm-detail-list small mb-0">
                        <dt class="col-5 col-sm-4">Dititipkan sejak</dt>
                        <dd class="col-7 col-sm-8">
                            {{ $item->tgl_titip ? \Carbon\Carbon::parse($item->tgl_titip)->translatedFormat('d F Y') : '—' }}
                        </dd>
                        <dt class="col-5 col-sm-4">ID Barang</dt>
                        <dd class="col-7 col-sm-8">{{ $item->id_barang }}</dd>
                    </dl>
                </div>
            </div>

            <hr class="my-4">

            <h2 class="h5 fw-bold mb-2">Deskripsi</h2>
            <p class="mb-0" style="white-space: pre-line;">{{ $item->deskripsi_barang ?: 'Penitip belum menuliskan deskripsi untuk barang ini.' }}</p>
        </div>

        {{-- Diskusi terkunci untuk pengunjung --}}
        <div class="rm-locked-discussion mt-4">
            <i class="bi bi-lock-fill fs-3 text-warning d-block mb-2"></i>
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
    </script>
@endpush
