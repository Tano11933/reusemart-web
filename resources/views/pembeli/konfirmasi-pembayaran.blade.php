@extends('layouts.buyer')

@section('title', 'Konfirmasi Pembayaran — ReuseMart')

@section('content')
    <section class="container py-4 py-lg-5">
        <nav class="rm-breadcrumb small mb-3" aria-label="Breadcrumb">
            <a href="{{ route('home_beli') }}">Home</a>
            <span class="mx-1">/</span>
            <span class="rm-text-muted">Konfirmasi Pembayaran</span>
        </nav>

        <div class="mb-4">
            <h1 class="rm-page-title">Konfirmasi Pembayaran</h1>
            <p class="rm-page-sub">Unggah bukti transfer agar tim kami dapat memverifikasi pesananmu.</p>
        </div>

        <div class="row g-4 align-items-start">
            <div class="col-lg-7">
                <section class="rm-payment-card">
                    <div class="rm-payment-order mb-4">
                        <span class="rm-payment-icon"><i class="bi bi-receipt"></i></span>
                        <div>
                            <span class="small rm-text-muted">Nomor pesanan</span>
                            <strong>{{ $pemesanan->id_pemesanan }}</strong>
                        </div>
                        <div class="ms-auto text-end">
                            <span class="small rm-text-muted">Total pembayaran</span>
                            <strong class="rm-payment-total">Rp {{ number_format($pemesanan->total_harga, 0, ',', '.') }}</strong>
                        </div>
                    </div>

                    <form action="{{ route('checkout.payment.submit', $pemesanan->id_pemesanan) }}" method="POST" enctype="multipart/form-data" id="paymentForm">
                        @csrf

                        <fieldset class="mb-4">
                            <legend class="form-label fw-semibold mb-2">Metode transfer</legend>
                            <div class="rm-payment-methods">
                                <label class="rm-payment-method" for="payment-bank">
                                    <input class="form-check-input" type="radio" name="metode_pembayaran" id="payment-bank" value="bank" checked required>
                                    <span class="rm-payment-method-icon"><i class="bi bi-bank"></i></span>
                                    <span><strong>Transfer Bank</strong><small>Transfer melalui rekening bank.</small></span>
                                </label>
                                <label class="rm-payment-method" for="payment-ewallet">
                                    <input class="form-check-input" type="radio" name="metode_pembayaran" id="payment-ewallet" value="dana">
                                    <span class="rm-payment-method-icon"><i class="bi bi-wallet2"></i></span>
                                    <span><strong>DANA</strong><small>Transfer melalui dompet digital.</small></span>
                                </label>
                            </div>
                        </fieldset>

                        <div class="mb-3">
                            <label class="form-label fw-semibold" for="buktiTransfer">Bukti transfer</label>
                            <input type="file" name="bukti_transfer" id="buktiTransfer" accept="image/jpeg,image/png" required class="form-control">
                            <div class="form-text">Format JPG atau PNG, maksimal 2 MB.</div>
                            @error('bukti_transfer')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div id="proofPreview" class="rm-proof-preview d-none mb-3">
                            <img id="proofPreviewImage" src="" alt="Pratinjau bukti transfer">
                            <span id="proofFileName" class="small"></span>
                        </div>

                        <div class="rm-payment-deadline mb-3">
                            <i class="bi bi-clock-history"></i>
                            <span>Waktu tersisa untuk mengirim bukti</span>
                            <strong id="countdown" class="ms-auto">10:00</strong>
                        </div>

                        <button type="submit" class="btn rm-btn-primary btn-lg w-100" id="submitPaymentButton">
                            <i class="bi bi-shield-check me-1"></i>Kirim Bukti Pembayaran
                        </button>
                    </form>
                </section>
            </div>

            <aside class="col-lg-5">
                <section class="rm-payment-card">
                    <h2 class="h5 fw-bold mb-3">Ringkasan Pembayaran</h2>

                    <div class="rm-payment-order-lines">
                        @forelse ($pemesanan->detailPemesanans as $detail)
                            <div class="rm-payment-order-line">
                                <span>{{ $detail->barang->nama_barang ?? 'Barang' }}</span>
                                <strong>Rp {{ number_format($detail->harga, 0, ',', '.') }}</strong>
                            </div>
                        @empty
                            <p class="small rm-text-muted mb-0">Rincian barang tidak tersedia untuk pesanan ini.</p>
                        @endforelse
                    </div>

                    <div class="rm-payment-summary-lines">
                        <div><span>Total harga barang</span><strong>Rp {{ number_format($pemesanan->total_harga_pesanan, 0, ',', '.') }}</strong></div>
                        <div><span>Ongkos kirim</span><strong>Rp {{ number_format($pemesanan->total_ongkir, 0, ',', '.') }}</strong></div>
                        <div><span>Potongan poin</span><strong>- Rp {{ number_format($pemesanan->potongan_harga, 0, ',', '.') }}</strong></div>
                    </div>

                    <div class="rm-payment-grand-total">
                        <span>Total dibayar</span>
                        <strong>Rp {{ number_format($pemesanan->total_harga, 0, ',', '.') }}</strong>
                    </div>

                    <div class="rm-payment-points mt-3">
                        <i class="bi bi-stars"></i>
                        <span>Kamu akan mendapat <strong>{{ number_format($pemesanan->poin_pesanan ?? 0, 0, ',', '.') }} poin</strong> setelah pesanan selesai.</span>
                    </div>
                </section>

                <a href="{{ route('pembeli.history') }}" class="btn rm-btn-outline w-100 mt-3">
                    <i class="bi bi-clock-history me-1"></i>Cek Riwayat Pesanan
                </a>
            </aside>
        </div>
    </section>
@endsection

@push('styles')
    <style>
        .rm-payment-card {
            background: #fff;
            border: 1px solid var(--rm-line);
            border-radius: var(--rm-radius-lg);
            box-shadow: var(--rm-shadow-xs);
            padding: 1.4rem;
        }

        .rm-payment-order {
            display: flex;
            align-items: center;
            gap: 0.8rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid var(--rm-line);
        }

        .rm-payment-order > div:not(.ms-auto) {
            display: grid;
        }

        .rm-payment-order strong {
            color: var(--rm-ink);
            font-weight: 700;
        }

        .rm-payment-icon,
        .rm-payment-method-icon {
            width: 44px;
            height: 44px;
            flex: 0 0 44px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            color: var(--rm-primary);
            background: var(--rm-primary-soft);
            font-size: 1.15rem;
        }

        .rm-payment-total {
            color: var(--rm-primary) !important;
            font-size: 1.05rem;
        }

        .rm-payment-methods {
            display: grid;
            gap: 0.65rem;
        }

        .rm-payment-method {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            border: 1.5px solid var(--rm-line);
            border-radius: var(--rm-radius);
            padding: 0.85rem;
            cursor: pointer;
            transition: border-color var(--rm-fast), background-color var(--rm-fast);
        }

        .rm-payment-method:has(input:checked) {
            border-color: var(--rm-primary);
            background: var(--rm-primary-soft-2);
        }

        .rm-payment-method .form-check-input {
            margin: 0;
        }

        .rm-payment-method-icon {
            width: 40px;
            height: 40px;
            flex-basis: 40px;
        }

        .rm-payment-method > span:last-child {
            display: grid;
        }

        .rm-payment-method strong {
            font-size: 0.9rem;
        }

        .rm-payment-method small {
            color: var(--rm-muted);
            font-size: 0.75rem;
        }

        .rm-proof-preview {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.65rem;
            border: 1px solid var(--rm-line);
            border-radius: var(--rm-radius-sm);
            background: var(--rm-bg-soft);
        }

        .rm-proof-preview img {
            width: 58px;
            height: 58px;
            object-fit: cover;
            border-radius: 0.5rem;
        }

        .rm-proof-preview span {
            overflow-wrap: anywhere;
        }

        .rm-payment-deadline {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.7rem 0.85rem;
            border-radius: var(--rm-radius-sm);
            color: #7a560f;
            background: var(--rm-warning-soft);
            font-size: 0.82rem;
        }

        .rm-payment-deadline strong {
            font-size: 0.95rem;
            font-variant-numeric: tabular-nums;
        }

        .rm-payment-order-lines,
        .rm-payment-summary-lines {
            display: grid;
            gap: 0.65rem;
        }

        .rm-payment-order-lines {
            max-height: 240px;
            overflow: auto;
            padding-bottom: 1rem;
            border-bottom: 1px solid var(--rm-line);
        }

        .rm-payment-order-line,
        .rm-payment-summary-lines > div {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 1rem;
            font-size: 0.84rem;
        }

        .rm-payment-order-line span,
        .rm-payment-summary-lines span {
            color: var(--rm-muted);
        }

        .rm-payment-order-line strong,
        .rm-payment-summary-lines strong {
            color: var(--rm-ink);
            white-space: nowrap;
        }

        .rm-payment-summary-lines {
            padding: 1rem 0;
        }

        .rm-payment-grand-total {
            display: flex;
            justify-content: space-between;
            align-items: baseline;
            gap: 1rem;
            border-top: 1px solid var(--rm-line);
            padding-top: 1rem;
            font-weight: 700;
        }

        .rm-payment-grand-total strong {
            color: var(--rm-primary-dark);
            font-size: 1.2rem;
            text-align: right;
        }

        .rm-payment-points {
            display: flex;
            align-items: flex-start;
            gap: 0.65rem;
            padding: 0.8rem;
            border-radius: var(--rm-radius-sm);
            background: var(--rm-warning-soft);
            color: #76540f;
            font-size: 0.8rem;
        }

        @media (max-width: 575.98px) {
            .rm-payment-card {
                padding: 1rem;
            }

            .rm-payment-order {
                flex-wrap: wrap;
            }

            .rm-payment-total {
                font-size: 0.95rem;
            }
        }
    </style>
@endpush

@push('scripts')
    <script>
        (function () {
            let timeLeft = 600;
            const countdown = document.getElementById('countdown');
            const paymentForm = document.getElementById('paymentForm');
            const fileInput = document.getElementById('buktiTransfer');
            const preview = document.getElementById('proofPreview');
            const previewImage = document.getElementById('proofPreviewImage');
            const fileName = document.getElementById('proofFileName');

            fileInput.addEventListener('change', function () {
                const file = this.files?.[0];
                if (!file) {
                    preview.classList.add('d-none');
                    return;
                }

                fileName.textContent = file.name;
                previewImage.src = URL.createObjectURL(file);
                preview.classList.remove('d-none');
            });

            const timer = window.setInterval(function () {
                timeLeft--;
                const minutes = String(Math.floor(timeLeft / 60)).padStart(2, '0');
                const seconds = String(timeLeft % 60).padStart(2, '0');
                countdown.textContent = `${minutes}:${seconds}`;

                if (timeLeft <= 0) {
                    window.clearInterval(timer);
                    Swal.fire({
                        icon: 'error',
                        title: 'Waktu pembayaran habis',
                        text: 'Pesanan dibatalkan karena bukti pembayaran belum dikirim.',
                        confirmButtonColor: '#1E5B5A',
                        allowOutsideClick: false,
                    }).then(function () {
                        fetch(@json(route('checkout.payment.cancel', $pemesanan->id_pemesanan)), {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': @json(csrf_token()),
                                'Accept': 'application/json',
                            },
                        }).finally(function () {
                            window.location.href = @json(route('checkout.failed'));
                        });
                    });
                }
            }, 1000);

            paymentForm.addEventListener('submit', function (event) {
                if (!fileInput.files?.length) {
                    event.preventDefault();
                    Swal.fire({
                        icon: 'warning',
                        title: 'Bukti transfer diperlukan',
                        text: 'Pilih foto bukti transfer sebelum mengirim pembayaran.',
                        confirmButtonColor: '#1E5B5A',
                    });
                }
            });
        })();
    </script>
@endpush
