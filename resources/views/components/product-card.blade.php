@props(['item', 'detailRoute' => 'guest.detail_barang'])

@php
    // Tentukan foto: URL penuh, file lokal, atau placeholder bila file tidak ada.
    $foto = trim((string) ($item->foto_barang ?? ''));

    if ($foto === '') {
        $img = asset('image/placeholder.svg');
    } elseif (preg_match('#^https?://#i', $foto)) {
        $img = $foto;
    } elseif (file_exists(public_path('image/' . ltrim($foto, '/')))) {
        $img = asset('image/' . ltrim($foto, '/'));
    } else {
        $img = asset('image/placeholder.svg');
    }

    $detailUrl = route($detailRoute, ['id' => $item->id_barang]);

    // Rating berasal dari penitip (penjual), bukan dari barang.
    $rating = optional($item->penitip)->rating;
    $fullStars = $rating ? (int) floor($rating) : 0;
    $halfStar = $rating ? ($rating - $fullStars) >= 0.5 : false;
    $emptyStars = $rating ? 5 - $fullStars - ($halfStar ? 1 : 0) : 0;
@endphp

<article class="rm-product-card">
    <a href="{{ $detailUrl }}" class="rm-product-media d-block" aria-label="Lihat detail {{ $item->nama_barang }}">
        <img src="{{ $img }}" alt="{{ $item->nama_barang }}" loading="lazy">
        @if (($item->garansi ?? false))
            <span class="rm-badge rm-badge-garansi"><i class="bi bi-shield-check me-1"></i>Garansi</span>
        @endif
    </a>

    <div class="rm-product-body">
        <h3 class="rm-product-title">{{ $item->nama_barang }}</h3>
        <p class="rm-product-price">Rp {{ number_format($item->harga_barang, 0, ',', '.') }}</p>
        <p class="rm-product-meta"><i class="bi bi-tag me-1"></i>{{ $item->kategori }}</p>

        @if (!is_null($rating))
            <div class="rm-rating" aria-label="Rating penjual {{ number_format($rating, 1) }} dari 5">
                @for ($i = 0; $i < $fullStars; $i++)
                    <i class="bi bi-star-fill"></i>
                @endfor
                @if ($halfStar)
                    <i class="bi bi-star-half"></i>
                @endif
                @for ($i = 0; $i < $emptyStars; $i++)
                    <i class="bi bi-star"></i>
                @endfor
                <span class="rm-rating-value">{{ number_format($rating, 1) }}</span>
            </div>
        @else
            <p class="rm-product-meta mb-0"><i class="bi bi-person me-1"></i>Penjual baru</p>
        @endif

        <a href="{{ $detailUrl }}" class="btn rm-btn-primary w-100">Lihat Detail</a>
    </div>
</article>
