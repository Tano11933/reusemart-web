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
    $ariaLabel = 'Lihat detail ' . $item->nama_barang;
    $adaAksi = isset($action);

    // Rating berasal dari penitip (penjual), bukan dari barang.
    $rating = optional($item->penitip)->rating;
    $hasRating = !is_null($rating);
    $ratingValue = $hasRating ? max(0, min(5, (float) $rating)) : null;
@endphp

<article class="rm-product-card">
    @unless ($adaAksi)
        {{-- Seluruh kartu bisa diklik seperti tile di toko Apple. --}}
        <a href="{{ $detailUrl }}" class="rm-card-link" aria-label="{{ $ariaLabel }}"></a>
    @endunless

    @if ($adaAksi)
        <a href="{{ $detailUrl }}" class="rm-product-media" aria-label="{{ $ariaLabel }}">
            <img src="{{ $img }}" alt="{{ $item->nama_barang }}" loading="lazy"
                 onerror="this.onerror=null;this.src='{{ asset('image/placeholder.svg') }}'">
            @if (($item->garansi ?? false))
                <span class="rm-badge rm-badge-garansi"><i class="bi bi-shield-check me-1"></i>Garansi</span>
            @endif
        </a>
    @else
        <div class="rm-product-media">
            <img src="{{ $img }}" alt="{{ $item->nama_barang }}" loading="lazy"
                 onerror="this.onerror=null;this.src='{{ asset('image/placeholder.svg') }}'">
            @if (($item->garansi ?? false))
                <span class="rm-badge rm-badge-garansi"><i class="bi bi-shield-check me-1"></i>Garansi</span>
            @endif
        </div>
    @endif

    <div class="rm-product-body">
        <h3 class="rm-product-title">
            @if ($adaAksi)
                <a href="{{ $detailUrl }}">{{ $item->nama_barang }}</a>
            @else
                {{ $item->nama_barang }}
            @endif
        </h3>

        <p class="rm-product-price"><span class="rm-product-currency">Rp</span> {{ number_format($item->harga_barang, 0, ',', '.') }}</p>

        <div class="rm-product-meta-row">
            <span class="rm-product-meta"><i class="bi bi-tag" aria-hidden="true"></i>{{ $item->kategori }}</span>

            @if ($hasRating)
                <span class="rm-rating-sm" aria-label="Rating penjual {{ number_format($ratingValue, 1) }} dari 5">
                    <i class="bi bi-star-fill" aria-hidden="true"></i>{{ number_format($ratingValue, 1) }}
                </span>
            @else
                <span class="rm-rating-sm rm-text-muted">Penjual baru</span>
            @endif
        </div>

        @if ($adaAksi)
            <div class="rm-product-actions">{{ $action }}</div>
        @endif
    </div>
</article>
