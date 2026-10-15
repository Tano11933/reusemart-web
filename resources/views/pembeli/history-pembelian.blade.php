@extends('layouts.buyer')

@section('title', 'Riwayat Pembelian · ReuseMart')

@section('content')
    <section class="container py-4 py-lg-5">
        <nav class="rm-breadcrumb small mb-3" aria-label="Breadcrumb">
            <a href="{{ route('home_beli') }}">Home</a>
            <span class="mx-1">/</span>
            <a href="{{ route('pembeli-profile', ['id' => $pembeli->id_pembeli]) }}">Profil Saya</a>
            <span class="mx-1">/</span>
            <span class="rm-text-muted">Riwayat Pembelian</span>
        </nav>

        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-end gap-3 mb-4">
            <div>
                <h1 class="rm-page-title">Riwayat Pembelian</h1>
                <p class="rm-page-sub">Pantau status pesanan dan beri penilaian setelah transaksi selesai.</p>
            </div>
            <span class="rm-chip"><i class="bi bi-receipt"></i>{{ $transactions->count() }} transaksi</span>
        </div>

        @if ($transactions->isEmpty())
            <div class="rm-empty">
                <i class="bi bi-bag-x d-block"></i>
                <h2 class="h5 fw-bold">Belum ada pembelian</h2>
                <p class="mb-3">Pesanan yang kamu buat akan muncul di sini.</p>
                <a href="{{ route('lihat_semua_beli') }}" class="btn rm-btn-primary">
                    <i class="bi bi-grid me-1"></i>Jelajahi Produk
                </a>
            </div>
        @else
            <div class="d-flex flex-column gap-3">
                @foreach ($transactions as $transaction)
                    @php
                        $status = strtolower((string) $transaction->status);
                        $statusClass = match (true) {
                            str_contains($status, 'selesai') => 'rm-status-success',
                            str_contains($status, 'batal'), str_contains($status, 'hangus') => 'rm-status-danger',
                            str_contains($status, 'kirim') => 'rm-status-info',
                            default => 'rm-status-pending',
                        };
                    @endphp

                    <article class="rm-order-card">
                        <header class="rm-order-header">
                            <div class="d-flex flex-wrap align-items-center gap-2">
                                <span class="rm-order-icon"><i class="bi bi-receipt"></i></span>
                                <div>
                                    <div class="fw-bold">Pesanan {{ $transaction->id_pemesanan }}</div>
                                    <div class="small rm-text-muted">
                                        {{ $transaction->tgl_pesan ? \Carbon\Carbon::parse($transaction->tgl_pesan)->translatedFormat('d F Y') : 'Tanggal tidak tersedia' }}
                                    </div>
                                </div>
                            </div>
                            <span class="rm-status-pill {{ $statusClass }}">{{ ucfirst($transaction->status ?: 'Diproses') }}</span>
                        </header>

                        <div class="rm-order-items">
                            @if ($transaction->detailPemesanans->isEmpty())
                                <div class="rm-order-empty">
                                    <i class="bi bi-info-circle"></i>
                                    Detail barang untuk pesanan ini tidak tersedia.
                                </div>
                            @endif
                            @foreach ($transaction->detailPemesanans as $detail)
                                @php
                                    $item = $detail->barang;
                                    $foto = trim((string) ($item->foto_barang ?? ''));
                                    $img = ($foto !== '' && file_exists(public_path('image/' . ltrim($foto, '/'))))
                                        ? asset('image/' . ltrim($foto, '/'))
                                        : asset('image/placeholder.svg');
                                @endphp

                                <div class="rm-history-item">
                                    <img src="{{ $img }}" alt="{{ $item->nama_barang ?? 'Barang' }}" loading="lazy"
                                         onerror="this.onerror=null;this.src='{{ asset('image/placeholder.svg') }}'">
                                    <div class="rm-history-item-main">
                                        <h3 class="h6 fw-bold mb-1">{{ $item->nama_barang ?? 'Barang tidak tersedia' }}</h3>
                                        <p class="small rm-text-muted mb-1">{{ $item->kategori ?? 'Kategori tidak tersedia' }}</p>
                                        <span class="fw-semibold">Rp {{ number_format($detail->harga ?? 0, 0, ',', '.') }}</span>
                                    </div>

                                    @if ($transaction->status === 'transaksi selesai' && $item)
                                        @if (is_null($detail->rating_diberikan))
                                            <form class="rm-rate-form" action="{{ route('barang.rate', $item->id_barang) }}" method="POST">
                                                @csrf
                                                <label class="small fw-semibold" for="rating-{{ $detail->id_detail_pemesanan }}">Penilaian</label>
                                                <select class="form-select form-select-sm" id="rating-{{ $detail->id_detail_pemesanan }}" name="rating" required>
                                                    <option value="">Pilih bintang</option>
                                                    @for ($star = 5; $star >= 1; $star--)
                                                        <option value="{{ $star }}">{{ $star }} {{ $star === 1 ? 'bintang' : 'bintang' }}</option>
                                                    @endfor
                                                </select>
                                                <button type="submit" class="btn rm-btn-outline btn-sm mt-2 w-100">
                                                    <i class="bi bi-star me-1"></i>Kirim
                                                </button>
                                            </form>
                                        @else
                                            <div class="rm-rated-label">
                                                <i class="bi bi-star-fill"></i>
                                                Dinilai {{ $detail->rating_diberikan }}/5
                                            </div>
                                        @endif
                                    @endif
                                </div>
                            @endforeach
                        </div>

                        <footer class="rm-order-footer">
                            <span class="small rm-text-muted">{{ $transaction->jenis_pengantaran === 'diambil' ? 'Ambil sendiri' : 'Pengiriman kurir' }}</span>
                            <span class="small rm-text-muted">Total pesanan</span>
                            <strong>Rp {{ number_format($transaction->total_harga ?? $transaction->total_harga_pesanan ?? 0, 0, ',', '.') }}</strong>
                        </footer>
                    </article>
                @endforeach
            </div>
        @endif
    </section>
@endsection

@push('styles')
    <style>
        .rm-order-card {
            background: #fff;
            border: 1px solid var(--rm-line);
            border-radius: var(--rm-radius-lg);
            box-shadow: var(--rm-shadow-xs);
            overflow: hidden;
        }

        .rm-order-header,
        .rm-order-footer {
            display: flex;
            align-items: center;
            gap: 0.8rem;
            padding: 1rem 1.25rem;
            background: var(--rm-bg-soft);
        }

        .rm-order-header {
            justify-content: space-between;
            border-bottom: 1px solid var(--rm-line);
        }

        .rm-order-icon {
            width: 40px;
            height: 40px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            background: var(--rm-primary-soft);
            color: var(--rm-primary);
            font-size: 1.1rem;
        }

        .rm-status-pill {
            display: inline-flex;
            align-items: center;
            border-radius: var(--rm-radius-pill);
            padding: 0.35rem 0.75rem;
            font-size: 0.76rem;
            line-height: 1.3;
            font-weight: 700;
        }

        .rm-status-success { color: #176246; background: var(--rm-success-soft); }
        .rm-status-danger { color: #8e2a20; background: var(--rm-danger-soft); }
        .rm-status-info { color: #1f5573; background: var(--rm-info-soft); }
        .rm-status-pending { color: #76540f; background: var(--rm-warning-soft); }

        .rm-order-items {
            padding: 0 1.25rem;
        }

        .rm-order-empty {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 1rem 0;
            color: var(--rm-muted);
            font-size: 0.85rem;
        }

        .rm-order-empty i {
            color: var(--rm-info);
        }

        .rm-history-item {
            display: grid;
            grid-template-columns: 72px minmax(0, 1fr) 140px;
            align-items: center;
            gap: 1rem;
            padding: 1rem 0;
            border-bottom: 1px solid var(--rm-line);
        }

        .rm-history-item:last-child {
            border-bottom: 0;
        }

        .rm-history-item > img {
            width: 72px;
            height: 72px;
            object-fit: contain;
            padding: 0.25rem;
            background: var(--rm-primary-soft-2);
            border-radius: var(--rm-radius-sm);
        }

        .rm-history-item-main {
            min-width: 0;
            overflow-wrap: anywhere;
        }

        .rm-rate-form {
            width: 140px;
        }

        .rm-rated-label {
            justify-self: end;
            color: #835f14;
            background: var(--rm-warning-soft);
            border-radius: var(--rm-radius-pill);
            padding: 0.45rem 0.7rem;
            white-space: nowrap;
            font-size: 0.78rem;
            font-weight: 700;
        }

        .rm-rated-label i {
            color: var(--rm-gold);
        }

        .rm-order-footer {
            border-top: 1px solid var(--rm-line);
            justify-content: flex-end;
        }

        .rm-order-footer strong {
            color: var(--rm-primary-dark);
            font-size: 1.05rem;
            margin-left: 0.25rem;
        }

        @media (max-width: 575.98px) {
            .rm-order-header,
            .rm-order-footer {
                padding: 0.85rem;
            }

            .rm-order-items {
                padding: 0 0.85rem;
            }

            .rm-history-item {
                grid-template-columns: 56px minmax(0, 1fr);
                gap: 0.75rem;
            }

            .rm-history-item > img {
                width: 56px;
                height: 56px;
            }

            .rm-rate-form,
            .rm-rated-label {
                grid-column: 2;
                justify-self: start;
                width: 100%;
                max-width: 220px;
            }

            .rm-order-footer {
                flex-wrap: wrap;
                gap: 0.35rem 0.6rem;
            }
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.querySelectorAll('.rm-rate-form').forEach(function (form) {
            form.addEventListener('submit', async function (event) {
                event.preventDefault();
                const button = form.querySelector('button[type="submit"]');
                button.disabled = true;

                try {
                    const response = await fetch(form.action, {
                        method: 'POST',
                        headers: { 'Accept': 'application/json' },
                        body: new FormData(form),
                    });
                    const result = await response.json();

                    if (!response.ok || !result.success) {
                        throw new Error(result.message || 'Rating gagal disimpan.');
                    }

                    form.outerHTML = '<span class="rm-rated-label"><i class="bi bi-star-fill"></i> Terima kasih atas penilaianmu</span>';
                    Swal.fire({ icon: 'success', title: 'Terima kasih!', text: result.message });
                } catch (error) {
                    button.disabled = false;
                    Swal.fire({ icon: 'error', title: 'Rating gagal', text: error.message });
                }
            });
        });
    </script>
@endpush
