@extends('layouts.buyer')

@section('title', $barang->nama_barang . ' — ReuseMart')

@push('styles')
    <style>
        .rm-product-gallery {
            max-width: 100%;
        }

        .rm-gallery-thumbnails {
            display: flex;
            justify-content: center;
            gap: 0.6rem;
            margin-top: 0.85rem;
        }

        .rm-gallery-thumb {
            width: 58px;
            height: 58px;
            border: 2px solid transparent;
            border-radius: 0.75rem;
            background: var(--rm-primary-soft-2);
            padding: 0.2rem;
            overflow: hidden;
            transition: border-color var(--rm-fast), background-color var(--rm-fast);
        }

        .rm-gallery-thumb.active,
        .rm-gallery-thumb:hover {
            border-color: var(--rm-primary);
            background: #fff;
        }

        .rm-gallery-thumb img {
            display: block;
            width: 100%;
            height: 100%;
            object-fit: contain;
            border-radius: 0.5rem;
        }

        .rm-discussion-card {
            border: 1px solid var(--rm-line);
            border-radius: var(--rm-radius-lg);
            background: #fff;
            box-shadow: var(--rm-shadow-xs);
            overflow: hidden;
        }

        .rm-discussion-header {
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid var(--rm-line);
            background: var(--rm-bg-soft);
        }

        .rm-discussion-list {
            display: flex;
            flex-direction: column;
            gap: 1rem;
            max-height: 440px;
            overflow-y: auto;
            padding: 1.25rem;
            scroll-behavior: smooth;
        }

        .rm-discussion-item {
            display: flex;
            align-items: flex-end;
            gap: 0.65rem;
            max-width: min(88%, 680px);
        }

        .rm-discussion-item.is-mine {
            align-self: flex-end;
            flex-direction: row-reverse;
        }

        .rm-discussion-avatar {
            width: 36px;
            height: 36px;
            flex: 0 0 36px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            background: var(--rm-primary);
            font-size: 0.9rem;
        }

        .rm-discussion-item.is-mine .rm-discussion-avatar {
            background: #bd7130;
        }

        .rm-discussion-bubble {
            min-width: 0;
            padding: 0.7rem 0.9rem;
            border: 1px solid var(--rm-line);
            border-radius: 1rem 1rem 1rem 0.25rem;
            background: var(--rm-bg-soft);
            overflow-wrap: anywhere;
        }

        .rm-discussion-item.is-mine .rm-discussion-bubble {
            border-color: #f1dfc9;
            border-radius: 1rem 1rem 0.25rem 1rem;
            background: #fff7ef;
        }

        .rm-discussion-name {
            color: var(--rm-primary-dark);
            font-size: 0.8rem;
            font-weight: 700;
            margin-bottom: 0.15rem;
        }

        .rm-discussion-text {
            color: var(--rm-ink);
            white-space: pre-wrap;
            margin: 0;
        }

        .rm-discussion-time {
            display: block;
            color: var(--rm-muted);
            font-size: 0.7rem;
            margin-top: 0.3rem;
        }

        .rm-discussion-compose {
            padding: 1rem 1.25rem;
            border-top: 1px solid var(--rm-line);
            background: #fff;
        }

        @media (max-width: 575.98px) {
            .rm-gallery-thumb {
                width: 50px;
                height: 50px;
            }

            .rm-discussion-item {
                max-width: 96%;
            }

            .rm-discussion-list {
                padding: 0.9rem;
            }
        }
    </style>
@endpush

@section('content')
    @php
        $photoUrls = [];

        foreach ([$barang->foto_barang ?? null, $barang->foto_barang2 ?? null] as $foto) {
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

        $cart = session('cart', []);
        $inCart = isset($cart[$barang->id_barang]);
        $sellerRating = $penitip?->rating;
        $sellerRating = is_null($sellerRating) ? null : max(0, min(5, (float) $sellerRating));
    @endphp

    <section class="container py-4 py-lg-5">
        <nav class="rm-breadcrumb small mb-3" aria-label="Breadcrumb">
            <a href="{{ route('home_beli') }}">Home</a>
            <span class="mx-1">/</span>
            <a href="{{ route('lihat_semua_beli') }}">Produk</a>
            <span class="mx-1">/</span>
            <a href="{{ route('guest.per_kategori', ['kategori' => $barang->kategori]) }}">{{ $barang->kategori }}</a>
            <span class="mx-1">/</span>
            <span class="rm-text-muted">{{ $barang->nama_barang }}</span>
        </nav>

        <article class="rm-detail-card">
            <div class="row g-4 g-xl-5">
                <div class="col-lg-6 rm-product-gallery">
                    <div id="buyerProductCarousel" class="carousel slide" data-bs-interval="false">
                        <div class="carousel-inner rm-detail-media">
                            @foreach ($photoUrls as $index => $photoUrl)
                                <div class="carousel-item {{ $index === 0 ? 'active' : '' }} h-100">
                                    <button type="button" class="w-100 h-100 border-0 bg-transparent p-0 zoom-trigger"
                                            data-full="{{ $photoUrl }}" aria-label="Perbesar foto {{ $index + 1 }}">
                                        <img src="{{ $photoUrl }}"
                                             class="d-block w-100 h-100"
                                             alt="{{ $barang->nama_barang }} — foto {{ $index + 1 }}"
                                             onerror="this.onerror=null;this.src='{{ asset('image/placeholder.svg') }}'">
                                    </button>
                                </div>
                            @endforeach
                        </div>

                        @if (count($photoUrls) > 1)
                            <div class="rm-gallery-thumbnails" aria-label="Pilih foto produk">
                                @foreach ($photoUrls as $index => $photoUrl)
                                    <button type="button" class="rm-gallery-thumb {{ $index === 0 ? 'active' : '' }}"
                                            data-bs-target="#buyerProductCarousel" data-bs-slide-to="{{ $index }}"
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
                        <span class="rm-chip"><i class="bi bi-tag"></i>{{ $barang->kategori }}</span>
                        @if ($barang->garansi)
                            <span class="rm-chip text-success"><i class="bi bi-shield-check"></i>Bergaransi</span>
                        @endif
                    </div>

                    <h1 class="h2 fw-bold mb-3">{{ $barang->nama_barang }}</h1>

                    <div class="mb-3">
                        <span class="rm-seller-chip">
                            <i class="bi bi-person-circle"></i>
                            <span>{{ $penitip->nama_penitip ?? 'Penitip' }}</span>
                            @if (!is_null($sellerRating))
                                <span class="rm-rating ms-1" aria-label="Rating {{ number_format($sellerRating, 1) }} dari 5">
                                    <i class="bi bi-star-fill"></i>{{ number_format($sellerRating, 1) }}
                                </span>
                            @else
                                <span class="rm-text-muted">Penjual baru</span>
                            @endif
                        </span>
                    </div>

                    <div class="rm-price-box mb-3">
                        <div class="rm-text-muted small">Harga</div>
                        <div class="rm-price">Rp {{ number_format($barang->harga_barang, 0, ',', '.') }}</div>
                        @if ($barang->garansi && $barang->tgl_garansi)
                            <div class="small text-success mt-1">
                                <i class="bi bi-shield-check me-1"></i>
                                Garansi berlaku sampai {{ \Carbon\Carbon::parse($barang->tgl_garansi)->translatedFormat('d F Y') }}
                            </div>
                        @else
                            <div class="small rm-text-muted mt-1"><i class="bi bi-shield-slash me-1"></i>Tidak bergaransi</div>
                        @endif
                    </div>

                    @if ($inCart)
                        <div class="alert alert-info d-flex align-items-center gap-2 mb-3">
                            <i class="bi bi-cart-check-fill"></i>
                            <span>Barang ini sudah ada di keranjang.</span>
                            <a href="{{ route('cart.index') }}" class="alert-link ms-auto">Lihat</a>
                        </div>
                    @else
                        <div class="d-grid d-sm-flex gap-2 mb-2">
                            <form action="{{ route('add_to_cart', $barang->id_barang) }}" method="POST" class="flex-fill">
                                @csrf
                                <button type="submit" class="btn rm-btn-outline w-100">
                                    <i class="bi bi-cart-plus me-1"></i>Tambah ke Keranjang
                                </button>
                            </form>
                            <form action="{{ route('add_to_cart', $barang->id_barang) }}" method="POST" class="flex-fill">
                                @csrf
                                <input type="hidden" name="buy_now" value="1">
                                <button type="submit" class="btn rm-btn-primary w-100">
                                    <i class="bi bi-lightning-charge me-1"></i>Beli Sekarang
                                </button>
                            </form>
                        </div>
                    @endif

                    <p class="rm-text-muted small mb-4">
                        <i class="bi bi-info-circle me-1"></i>
                        Barang bekas layak pakai. Periksa deskripsi dan foto sebelum checkout.
                    </p>

                    <dl class="row rm-detail-list small mb-0">
                        <dt class="col-5 col-sm-4">Kondisi</dt>
                        <dd class="col-7 col-sm-8">{{ $barang->kondisi ?? 'Baik' }}</dd>
                        <dt class="col-5 col-sm-4">Dititipkan</dt>
                        <dd class="col-7 col-sm-8">
                            {{ $barang->tgl_titip ? \Carbon\Carbon::parse($barang->tgl_titip)->translatedFormat('d F Y') : '—' }}
                        </dd>
                        <dt class="col-5 col-sm-4">ID Barang</dt>
                        <dd class="col-7 col-sm-8">{{ $barang->id_barang }}</dd>
                    </dl>
                </div>
            </div>

            <hr class="my-4">

            <div class="row g-4">
                <div class="col-lg-8">
                    <h2 class="h5 fw-bold mb-2">Tentang Barang Ini</h2>
                    <p class="mb-0" style="white-space: pre-line;">{{ $barang->deskripsi_barang ?: 'Penitip belum menambahkan deskripsi.' }}</p>
                </div>
                <div class="col-lg-4">
                    <div class="rm-surface-card p-3 h-100">
                        <div class="small rm-text-muted mb-1">Penilaian Penitip</div>
                        @if (!is_null($sellerRating))
                            <div class="d-flex align-items-center gap-2">
                                <span class="fs-3 fw-bold" style="color: var(--rm-primary);">{{ number_format($sellerRating, 1) }}</span>
                                <span class="rm-rating fs-6" aria-hidden="true">
                                    @for ($i = 1; $i <= 5; $i++)
                                        <i class="bi {{ $i <= round($sellerRating) ? 'bi-star-fill' : 'bi-star' }}"></i>
                                    @endfor
                                </span>
                            </div>
                            <p class="small rm-text-muted mb-0">Rata-rata dari penilaian pembeli.</p>
                        @else
                            <p class="mb-0 fw-semibold">Belum ada penilaian</p>
                            <p class="small rm-text-muted mb-0">Jadilah pembeli pertama yang memberi ulasan.</p>
                        @endif
                    </div>
                </div>
            </div>
        </article>

        @if ($serupa->isNotEmpty())
            <section class="mt-5 rm-related">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
                    <div>
                        <h2 class="h4 fw-bold mb-1">Mungkin Kamu Suka</h2>
                        <p class="rm-page-sub">Pilihan lain dari kategori {{ $barang->kategori }}.</p>
                    </div>
                    <a href="{{ route('guest.per_kategori', ['kategori' => $barang->kategori]) }}" class="btn rm-btn-outline btn-sm px-3">
                        Lihat Kategori <i class="bi bi-arrow-right ms-1"></i>
                    </a>
                </div>
                <div class="row row-cols-2 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-3 g-md-4 justify-content-center">
                    @foreach ($serupa as $related)
                        <div class="col">
                            <x-product-card :item="$related" detail-route="detail_barang_beli" />
                        </div>
                    @endforeach
                </div>
            </section>
        @endif

        <section class="mt-5" aria-labelledby="discussionTitle">
            <div class="d-flex flex-wrap justify-content-between align-items-end gap-2 mb-3">
                <div>
                    <h2 class="h4 fw-bold mb-1" id="discussionTitle">Diskusi Produk</h2>
                    <p class="rm-page-sub">Tanyakan hal yang ingin kamu ketahui sebelum membeli.</p>
                </div>
                <span class="rm-chip"><i class="bi bi-chat-dots"></i>{{ $diskusi->count() }} komentar</span>
            </div>

            <div class="rm-discussion-card">
                <div class="rm-discussion-header d-flex align-items-center gap-2">
                    <span class="rm-category-icon"><i class="bi bi-chat-square-text"></i></span>
                    <div>
                        <h3 class="h6 fw-bold mb-0">Tanya Jawab</h3>
                        <p class="small rm-text-muted mb-0">Tim Customer Service siap membantu.</p>
                    </div>
                </div>

                <div class="rm-discussion-list" id="commentBox" aria-live="polite">
                    @forelse ($diskusi as $comment)
                        @php
                            $isMine = $pembeli && $comment->id_pembeli === $pembeli->id_pembeli;
                            $isStaff = (bool) $comment->id_pegawai;
                            $author = $isStaff
                                ? ($comment->pegawai->nama_pegawai ?? 'Customer Service')
                                : ($comment->pembeli->nama_pembeli ?? 'Pembeli');
                        @endphp
                        <article class="rm-discussion-item {{ $isMine ? 'is-mine' : '' }}">
                            <span class="rm-discussion-avatar" aria-hidden="true">
                                <i class="bi {{ $isStaff ? 'bi-headset' : 'bi-person' }}"></i>
                            </span>
                            <div class="rm-discussion-bubble">
                                <div class="rm-discussion-name">
                                    {{ $author }}
                                    @if ($isStaff)
                                        <span class="badge rounded-pill text-bg-light ms-1">CS</span>
                                    @endif
                                </div>
                                <p class="rm-discussion-text">{{ $comment->komentar }}</p>
                                <time class="rm-discussion-time" datetime="{{ $comment->tgl_komentar }}">
                                    {{ \Carbon\Carbon::parse($comment->tgl_komentar)->translatedFormat('d M Y, H:i') }}
                                </time>
                            </div>
                        </article>
                    @empty
                        <div class="rm-empty py-4" id="emptyDiscussion">
                            <i class="bi bi-chat-square-text d-block"></i>
                            <p class="mb-0">Belum ada diskusi. Mulai percakapan dengan bertanya tentang produk ini.</p>
                        </div>
                    @endforelse
                </div>

                @if ($pembeli)
                    <form id="commentForm" class="rm-discussion-compose">
                        @csrf
                        <input type="hidden" name="id_barang" value="{{ $barang->id_barang }}">
                        <label for="komentarInput" class="form-label fw-semibold">Tulis pertanyaan</label>
                        <div class="d-flex flex-column flex-sm-row gap-2">
                            <textarea id="komentarInput" name="komentar" class="form-control" rows="2" maxlength="1000"
                                      placeholder="Contoh: Apakah barang masih berfungsi dengan baik?" required></textarea>
                            <button class="btn rm-btn-primary align-self-sm-end" id="commentSubmit" type="submit">
                                <i class="bi bi-send me-1"></i>Kirim
                            </button>
                        </div>
                        <div class="form-text">Maksimal 1.000 karakter.</div>
                    </form>
                @else
                    <div class="rm-discussion-compose d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2">
                        <p class="rm-text-muted mb-0">Masuk untuk bertanya atau ikut berdiskusi.</p>
                        <a href="{{ route('login') }}" class="btn rm-btn-primary">
                            <i class="bi bi-box-arrow-in-right me-1"></i>Login
                        </a>
                    </div>
                @endif
            </div>
        </section>
    </section>

    <div class="modal fade" id="imageModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content bg-transparent border-0">
                <div class="modal-body text-center p-0">
                    <img src="" id="modalImage" class="img-fluid" alt="Foto produk diperbesar">
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.querySelectorAll('.zoom-trigger').forEach(function (button) {
            button.addEventListener('click', function () {
                const modalImage = document.getElementById('modalImage');
                modalImage.src = this.dataset.full;
                bootstrap.Modal.getOrCreateInstance(document.getElementById('imageModal')).show();
            });
        });

        document.querySelectorAll('#buyerProductCarousel [data-bs-slide-to]').forEach(function (button) {
            button.addEventListener('click', function () {
                document.querySelectorAll('#buyerProductCarousel [data-bs-slide-to]').forEach(item => item.classList.remove('active'));
                this.classList.add('active');
            });
        });

        const commentBox = document.getElementById('commentBox');
        if (commentBox) commentBox.scrollTop = commentBox.scrollHeight;

        const commentForm = document.getElementById('commentForm');
        if (commentForm) {
            commentForm.addEventListener('submit', async function (event) {
                event.preventDefault();

                const input = document.getElementById('komentarInput');
                const submitButton = document.getElementById('commentSubmit');
                const message = input.value.trim();

                if (!message) return;

                submitButton.disabled = true;

                try {
                    const response = await fetch(@json(route('diskusi.store')), {
                        method: 'POST',
                        headers: { 'Accept': 'application/json' },
                        body: new FormData(commentForm),
                    });
                    const result = await response.json();

                    if (!response.ok || !result.success) {
                        throw new Error(result.message || 'Komentar gagal dikirim.');
                    }

                    document.getElementById('emptyDiscussion')?.remove();

                    const article = document.createElement('article');
                    article.className = 'rm-discussion-item is-mine';

                    const avatar = document.createElement('span');
                    avatar.className = 'rm-discussion-avatar';
                    avatar.setAttribute('aria-hidden', 'true');
                    avatar.innerHTML = '<i class="bi bi-person"></i>';

                    const bubble = document.createElement('div');
                    bubble.className = 'rm-discussion-bubble';

                    const author = document.createElement('div');
                    author.className = 'rm-discussion-name';
                    author.textContent = result.nama || @json($pembeli?->nama_pembeli ?? 'Pembeli');

                    const text = document.createElement('p');
                    text.className = 'rm-discussion-text';
                    text.textContent = result.komentar || message;

                    const time = document.createElement('time');
                    time.className = 'rm-discussion-time';
                    time.dateTime = result.tgl_komentar || new Date().toISOString();
                    time.textContent = new Intl.DateTimeFormat('id-ID', {
                        dateStyle: 'medium', timeStyle: 'short'
                    }).format(new Date(result.tgl_komentar || Date.now()));

                    bubble.append(author, text, time);
                    article.append(avatar, bubble);
                    commentBox.appendChild(article);
                    commentBox.scrollTop = commentBox.scrollHeight;
                    input.value = '';

                    const countChip = document.querySelector('#discussionTitle')?.closest('section')?.querySelector('.rm-chip');
                    if (countChip) countChip.innerHTML = '<i class="bi bi-chat-dots"></i>' + commentBox.querySelectorAll('.rm-discussion-item').length + ' komentar';
                } catch (error) {
                    if (window.Swal) {
                        Swal.fire({ icon: 'error', title: 'Tidak terkirim', text: error.message });
                    } else {
                        alert(error.message);
                    }
                } finally {
                    submitButton.disabled = false;
                }
            });
        }
    </script>
@endpush
