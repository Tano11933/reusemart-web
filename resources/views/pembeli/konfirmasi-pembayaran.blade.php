@extends('layouts.buyer')

@section('title', 'Konfirmasi Pembayaran · ReuseMart')

@php
    $rekening = [
        'bank' => [
            ['nama' => 'BCA', 'nomor' => '8730 1234 5678', 'pemilik' => 'PT ReuseMart Nusantara'],
            ['nama' => 'Mandiri', 'nomor' => '1400 0987 6543 21', 'pemilik' => 'PT ReuseMart Nusantara'],
        ],
        'dana' => [
            ['nama' => 'DANA', 'nomor' => '0812 3456 7890', 'pemilik' => 'ReuseMart'],
        ],
    ];
@endphp

@section('content')
    <section class="container py-4 py-lg-5">
        <nav class="rm-breadcrumb small mb-3" aria-label="Breadcrumb">
            <a href="{{ route('home_beli') }}">Home</a>
            <span class="mx-1">/</span>
            <span class="rm-text-muted">Konfirmasi Pembayaran</span>
        </nav>

        <div class="rm-page-head">
            <div class="rm-page-head-main">
                <span class="rm-page-head-icon" aria-hidden="true"><i class="bi bi-credit-card-2-front"></i></span>
                <div>
                    <h1 class="rm-page-title">Konfirmasi Pembayaran</h1>
                    <p class="rm-page-sub">Pilih metode pembayaran, transfer sesuai total, lalu unggah buktinya.</p>
                </div>
            </div>
        </div>

        <div class="row g-4 align-items-start">
            <div class="col-lg-7">
                <section class="rm-checkout-card">
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

                    <form action="{{ route('checkout.payment.submit', $pemesanan->id_pemesanan) }}" method="POST" enctype="multipart/form-data" id="paymentForm" novalidate>
                        @csrf

                        <div class="rm-checkout-section-heading">
                            <span class="rm-checkout-step">1</span>
                            <div>
                                <h2 class="h5 fw-bold mb-1">Pilih Metode Pembayaran</h2>
                                <p class="small rm-text-muted mb-0">Transfer melalui bank atau dompet digital DANA.</p>
                            </div>
                        </div>

                        <fieldset class="mb-3">
                            <legend class="visually-hidden">Metode pembayaran</legend>
                            <div class="rm-payment-methods">
                                <label class="rm-payment-method" for="payment-bank">
                                    <input class="form-check-input" type="radio" name="metode_pembayaran" id="payment-bank" value="bank" checked required>
                                    <span class="rm-payment-method-icon"><i class="bi bi-bank"></i></span>
                                    <span class="rm-payment-method-copy">
                                        <strong>Transfer Bank</strong>
                                        <small>BCA atau Mandiri</small>
                                    </span>
                                    <i class="bi bi-check-circle-fill rm-payment-method-check"></i>
                                </label>
                                <label class="rm-payment-method" for="payment-ewallet">
                                    <input class="form-check-input" type="radio" name="metode_pembayaran" id="payment-ewallet" value="dana">
                                    <span class="rm-payment-method-icon"><i class="bi bi-wallet2"></i></span>
                                    <span class="rm-payment-method-copy">
                                        <strong>DANA</strong>
                                        <small>Dompet digital</small>
                                    </span>
                                    <i class="bi bi-check-circle-fill rm-payment-method-check"></i>
                                </label>
                            </div>
                            @error('metode_pembayaran')
                                <div class="text-danger small mt-2">{{ $message }}</div>
                            @enderror
                        </fieldset>

                        <div class="rm-bank-details" id="panel-bank">
                            @foreach ($rekening['bank'] as $akun)
                                <div class="rm-bank-row">
                                    <span class="rm-bank-badge">{{ $akun['nama'] }}</span>
                                    <div class="rm-bank-info">
                                        <strong>{{ $akun['nomor'] }}</strong>
                                        <small>a/n {{ $akun['pemilik'] }}</small>
                                    </div>
                                    <button type="button" class="rm-copy-btn" data-copy="{{ $akun['nomor'] }}">
                                        <i class="bi bi-clipboard" aria-hidden="true"></i>Salin
                                    </button>
                                </div>
                            @endforeach
                        </div>

                        <div class="rm-bank-details d-none" id="panel-dana">
                            @foreach ($rekening['dana'] as $akun)
                                <div class="rm-bank-row">
                                    <span class="rm-bank-badge">DANA</span>
                                    <div class="rm-bank-info">
                                        <strong>{{ $akun['nomor'] }}</strong>
                                        <small>a/n {{ $akun['pemilik'] }}</small>
                                    </div>
                                    <button type="button" class="rm-copy-btn" data-copy="{{ $akun['nomor'] }}">
                                        <i class="bi bi-clipboard" aria-hidden="true"></i>Salin
                                    </button>
                                </div>
                            @endforeach
                        </div>

                        <p class="rm-bank-note">
                            <i class="bi bi-info-circle" aria-hidden="true"></i>
                            <span>Transfer tepat sebesar <strong>Rp {{ number_format($pemesanan->total_harga, 0, ',', '.') }}</strong> dan cantumkan nomor pesanan <strong>{{ $pemesanan->id_pemesanan }}</strong> pada berita transfer.</span>
                        </p>

                        <hr class="rm-payment-divider">

                        <div class="rm-checkout-section-heading">
                            <span class="rm-checkout-step">2</span>
                            <div>
                                <h2 class="h5 fw-bold mb-1">Unggah Bukti Transfer</h2>
                                <p class="small rm-text-muted mb-0">Format JPG atau PNG, maksimal 2 MB.</p>
                            </div>
                        </div>

                        <div class="rm-upload" id="uploadZone">
                            <input type="file" name="bukti_transfer" id="buktiTransfer" accept="image/jpeg,image/png" required class="rm-upload-input">
                            <label class="rm-upload-empty" for="buktiTransfer" id="uploadEmpty">
                                <i class="bi bi-cloud-arrow-up" aria-hidden="true"></i>
                                <strong>Klik atau seret bukti transfer ke sini</strong>
                                <span>JPG atau PNG, maksimal 2 MB</span>
                            </label>
                            <div class="rm-upload-preview d-none" id="uploadPreview">
                                <img id="proofPreviewImage" src="" alt="Pratinjau bukti transfer">
                                <div class="min-w-0 flex-grow-1">
                                    <strong id="proofFileName" class="d-block text-truncate">-</strong>
                                    <span class="small rm-text-muted" id="proofFileSize">-</span>
                                </div>
                                <button type="button" class="rm-copy-btn" id="uploadReset">
                                    <i class="bi bi-arrow-repeat" aria-hidden="true"></i>Ganti
                                </button>
                            </div>
                        </div>
                        @error('bukti_transfer')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                        <div class="rm-upload-error d-none" id="uploadError" role="alert"></div>

                        <div class="rm-payment-deadline mt-3" id="deadlineBox">
                            <i class="bi bi-clock-history" aria-hidden="true"></i>
                            <span>Sisa waktu mengirim bukti</span>
                            <strong id="countdown" class="ms-auto">10:00</strong>
                        </div>

                        <button type="submit" class="btn rm-btn-primary btn-lg w-100 mt-3" id="submitPaymentButton">
                            <i class="bi bi-shield-check me-1"></i>Kirim Bukti Pembayaran
                        </button>
                        <p class="small rm-text-muted text-center mt-2 mb-0">
                            <i class="bi bi-patch-check me-1"></i>Bukti diverifikasi manual oleh tim ReuseMart. Status bisa dicek di riwayat pesanan.
                        </p>
                    </form>
                </section>
            </div>

            <aside class="col-lg-5">
                <section class="rm-checkout-summary">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h2 class="h5 fw-bold mb-0">Ringkasan Pembayaran</h2>
                        <span class="rm-chip"><i class="bi bi-bag"></i>{{ $pemesanan->detailPemesanans->count() }} barang</span>
                    </div>

                    <div class="rm-checkout-items">
                        @forelse ($pemesanan->detailPemesanans as $detail)
                            @php
                                $foto = trim((string) ($detail->barang->foto_barang ?? ''));
                                $img = ($foto !== '' && file_exists(public_path('image/' . ltrim($foto, '/'))))
                                    ? asset('image/' . ltrim($foto, '/'))
                                    : asset('image/placeholder.svg');
                            @endphp
                            <div class="rm-checkout-item">
                                <img src="{{ $img }}" alt="{{ $detail->barang->nama_barang ?? 'Barang' }}" onerror="this.onerror=null;this.src='{{ asset('image/placeholder.svg') }}'">
                                <div class="min-w-0">
                                    <div class="fw-semibold text-truncate">{{ $detail->barang->nama_barang ?? 'Barang' }}</div>
                                    <div class="small rm-text-muted">Jumlah 1</div>
                                </div>
                                <strong>Rp {{ number_format($detail->harga, 0, ',', '.') }}</strong>
                            </div>
                        @empty
                            <p class="small rm-text-muted mb-0">Rincian barang tidak tersedia untuk pesanan ini.</p>
                        @endforelse
                    </div>

                    <div class="rm-summary-lines">
                        <div><span>Total harga barang</span><strong>Rp {{ number_format($pemesanan->total_harga_pesanan, 0, ',', '.') }}</strong></div>
                        <div>
                            <span>Ongkos kirim</span>
                            @if ($pemesanan->total_ongkir > 0)
                                <strong>Rp {{ number_format($pemesanan->total_ongkir, 0, ',', '.') }}</strong>
                            @else
                                <strong class="rm-text-success">Gratis</strong>
                            @endif
                        </div>
                        <div><span>Potongan poin</span><strong>- Rp {{ number_format($pemesanan->potongan_harga, 0, ',', '.') }}</strong></div>
                    </div>

                    <div class="rm-checkout-total">
                        <span>Total dibayar</span>
                        <strong>Rp {{ number_format($pemesanan->total_harga, 0, ',', '.') }}</strong>
                    </div>

                    <div class="rm-payment-points mt-3">
                        <i class="bi bi-coin" aria-hidden="true"></i>
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
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 0.65rem;
        }

        .rm-payment-method {
            display: flex;
            align-items: center;
            gap: 0.7rem;
            padding: 0.85rem;
            border: 1.5px solid var(--rm-line);
            border-radius: var(--rm-radius);
            cursor: pointer;
            transition: border-color var(--rm-fast), background-color var(--rm-fast);
        }

        .rm-payment-method:has(input:checked) {
            border-color: var(--rm-primary);
            background: var(--rm-primary-soft-2);
        }

        .rm-payment-method:focus-within {
            border-color: var(--rm-primary);
            box-shadow: var(--rm-focus-ring);
        }

        .rm-payment-method .form-check-input {
            margin: 0;
            flex: 0 0 auto;
        }

        .rm-payment-method-icon {
            width: 40px;
            height: 40px;
            flex-basis: 40px;
        }

        .rm-payment-method-copy {
            display: grid;
            gap: 0.05rem;
            min-width: 0;
        }

        .rm-payment-method-copy strong {
            color: var(--rm-ink);
            font-size: 0.9rem;
        }

        .rm-payment-method-copy small {
            color: var(--rm-muted);
            font-size: 0.75rem;
        }

        .rm-payment-method-check {
            display: none;
            color: var(--rm-primary);
            margin-left: auto;
        }

        .rm-payment-method:has(input:checked) .rm-payment-method-check {
            display: block;
        }

        .rm-bank-details {
            display: grid;
            gap: 0.55rem;
            padding: 0.7rem;
            border: 1px dashed #c7dedb;
            border-radius: var(--rm-radius);
            background: var(--rm-bg-soft);
        }

        .rm-bank-row {
            display: flex;
            align-items: center;
            gap: 0.7rem;
            padding: 0.5rem 0.7rem;
            border: 1px solid var(--rm-line);
            border-radius: var(--rm-radius-sm);
            background: #fff;
        }

        .rm-bank-badge {
            flex: 0 0 auto;
            padding: 0.28rem 0.6rem;
            border-radius: 6px;
            background: var(--rm-primary);
            color: #fff;
            font-size: 0.72rem;
            font-weight: 700;
            letter-spacing: 0.02em;
        }

        .rm-bank-info {
            display: grid;
            gap: 0.05rem;
            min-width: 0;
        }

        .rm-bank-info strong {
            color: var(--rm-ink);
            font-size: 0.95rem;
            letter-spacing: 0.03em;
        }

        .rm-bank-info small {
            color: var(--rm-muted);
            font-size: 0.74rem;
        }

        .rm-copy-btn {
            margin-left: auto;
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
            padding: 0.32rem 0.7rem;
            border: 1px solid var(--rm-line);
            border-radius: var(--rm-radius-sm);
            background: #fff;
            color: var(--rm-primary-dark);
            font-size: 0.78rem;
            font-weight: 600;
            white-space: nowrap;
            transition: border-color var(--rm-fast), background-color var(--rm-fast), color var(--rm-fast);
        }

        .rm-copy-btn:hover,
        .rm-copy-btn:focus-visible {
            border-color: var(--rm-primary);
            background: var(--rm-primary-soft-2);
            color: var(--rm-primary-dark);
        }

        .rm-bank-note {
            display: flex;
            align-items: flex-start;
            gap: 0.55rem;
            margin: 0.75rem 0 0;
            padding: 0.7rem 0.85rem;
            border-radius: var(--rm-radius-sm);
            background: var(--rm-warning-soft);
            color: #76540f;
            font-size: 0.8rem;
        }

        .rm-payment-divider {
            margin: 1.35rem 0;
            border-top: 1px solid var(--rm-line);
            opacity: 1;
        }

        .rm-upload {
            position: relative;
        }

        .rm-upload-input {
            position: absolute;
            width: 1px;
            height: 1px;
            padding: 0;
            margin: -1px;
            overflow: hidden;
            clip: rect(0 0 0 0);
            white-space: nowrap;
            border: 0;
        }

        .rm-upload-empty {
            display: grid;
            justify-items: center;
            gap: 0.3rem;
            padding: 1.5rem 1rem;
            border: 1.5px dashed #c7dedb;
            border-radius: var(--rm-radius);
            background: var(--rm-bg-soft);
            text-align: center;
            cursor: pointer;
            transition: border-color var(--rm-fast), background-color var(--rm-fast);
        }

        .rm-upload-empty .bi {
            font-size: 1.6rem;
            color: var(--rm-primary);
        }

        .rm-upload-empty strong {
            color: var(--rm-ink);
            font-size: 0.9rem;
        }

        .rm-upload-empty span {
            color: var(--rm-muted);
            font-size: 0.78rem;
        }

        .rm-upload-empty:hover,
        .rm-upload.is-dragover .rm-upload-empty {
            border-color: var(--rm-primary);
            background: var(--rm-primary-soft-2);
        }

        .rm-upload:focus-within .rm-upload-empty {
            border-color: var(--rm-primary);
            box-shadow: var(--rm-focus-ring);
        }

        .rm-upload-preview {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.65rem;
            border: 1px solid var(--rm-line);
            border-radius: var(--rm-radius);
            background: #fff;
        }

        .rm-upload-preview img {
            width: 64px;
            height: 64px;
            object-fit: cover;
            border-radius: 0.5rem;
            border: 1px solid var(--rm-line);
        }

        .rm-upload-error {
            margin-top: 0.5rem;
            padding: 0.6rem 0.75rem;
            border: 1px solid #f3cfcb;
            border-radius: var(--rm-radius-sm);
            background: var(--rm-danger-soft);
            color: #8e2a20;
            font-size: 0.8rem;
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

        .rm-payment-deadline.is-critical {
            color: #8e2a20;
            background: var(--rm-danger-soft);
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
            .rm-payment-order {
                flex-wrap: wrap;
            }

            .rm-payment-total {
                font-size: 0.95rem;
            }

            .rm-bank-row {
                flex-wrap: wrap;
            }

            .rm-copy-btn {
                margin-left: 0;
            }
        }
    </style>
@endpush

@push('scripts')
    <script>
        (function () {
            // Pilih metode pembayaran lalu tampilkan rekening yang sesuai.
            const methodInputs = document.querySelectorAll('input[name="metode_pembayaran"]');
            const panels = {
                bank: document.getElementById('panel-bank'),
                dana: document.getElementById('panel-dana'),
            };

            function updateMethod() {
                const selected = document.querySelector('input[name="metode_pembayaran"]:checked')?.value || 'bank';
                Object.entries(panels).forEach(([key, panel]) => panel?.classList.toggle('d-none', key !== selected));
            }

            methodInputs.forEach(input => input.addEventListener('change', updateMethod));
            updateMethod();

            // Tombol salin nomor rekening.
            document.querySelectorAll('.rm-copy-btn[data-copy]').forEach(button => {
                button.addEventListener('click', async function () {
                    const value = this.dataset.copy;

                    try {
                        await navigator.clipboard.writeText(value.replace(/\s+/g, ''));
                        Swal.fire({
                            icon: 'success',
                            title: 'Nomor disalin',
                            text: value,
                            toast: true,
                            position: 'top-end',
                            timer: 1800,
                            showConfirmButton: false,
                            timerProgressBar: true,
                        });
                    } catch (error) {
                        Swal.fire({
                            icon: 'info',
                            title: 'Salin manual',
                            text: value,
                            confirmButtonColor: '#1E5B5A',
                        });
                    }
                });
            });

            // Unggah bukti transfer: pratinjau, validasi, dan seret-lepas.
            const uploadZone = document.getElementById('uploadZone');
            const fileInput = document.getElementById('buktiTransfer');
            const uploadEmpty = document.getElementById('uploadEmpty');
            const uploadPreview = document.getElementById('uploadPreview');
            const uploadError = document.getElementById('uploadError');
            const previewImage = document.getElementById('proofPreviewImage');
            const fileName = document.getElementById('proofFileName');
            const fileSize = document.getElementById('proofFileSize');
            const resetButton = document.getElementById('uploadReset');
            const allowedTypes = ['image/jpeg', 'image/png'];
            const maxSize = 2 * 1024 * 1024;

            function showUploadError(message) {
                uploadError.textContent = message;
                uploadError.classList.remove('d-none');
            }

            function hideUploadError() {
                uploadError.textContent = '';
                uploadError.classList.add('d-none');
            }

            function resetUpload() {
                fileInput.value = '';
                previewImage.src = '';
                uploadPreview.classList.add('d-none');
                uploadEmpty.classList.remove('d-none');
            }

            function previewUpload(file) {
                if (!file) {
                    resetUpload();
                    return;
                }

                if (!allowedTypes.includes(file.type)) {
                    showUploadError('Format file harus JPG atau PNG.');
                    resetUpload();
                    return;
                }

                if (file.size > maxSize) {
                    showUploadError('Ukuran file maksimal 2 MB. File kamu ' + (file.size / 1048576).toFixed(2) + ' MB.');
                    resetUpload();
                    return;
                }

                hideUploadError();
                fileName.textContent = file.name;
                fileSize.textContent = (file.size / 1024).toFixed(0) + ' KB';
                previewImage.src = URL.createObjectURL(file);
                uploadEmpty.classList.add('d-none');
                uploadPreview.classList.remove('d-none');
            }

            fileInput.addEventListener('change', function () {
                previewUpload(this.files?.[0] ?? null);
            });

            ['dragenter', 'dragover'].forEach(eventName => {
                uploadZone.addEventListener(eventName, function (event) {
                    event.preventDefault();
                    uploadZone.classList.add('is-dragover');
                });
            });

            ['dragleave', 'drop'].forEach(eventName => {
                uploadZone.addEventListener(eventName, function (event) {
                    event.preventDefault();
                    uploadZone.classList.remove('is-dragover');
                });
            });

            uploadZone.addEventListener('drop', function (event) {
                const file = event.dataTransfer?.files?.[0];
                if (!file) return;

                const transfer = new DataTransfer();
                transfer.items.add(file);
                fileInput.files = transfer.files;
                previewUpload(file);
            });

            resetButton.addEventListener('click', function () {
                resetUpload();
                hideUploadError();
                fileInput.click();
            });

            // Hitung mundur batas waktu pembayaran.
            let timeLeft = 600;
            const countdown = document.getElementById('countdown');
            const deadlineBox = document.getElementById('deadlineBox');
            const paymentForm = document.getElementById('paymentForm');

            const timer = window.setInterval(function () {
                timeLeft--;
                const minutes = String(Math.floor(timeLeft / 60)).padStart(2, '0');
                const seconds = String(timeLeft % 60).padStart(2, '0');
                countdown.textContent = `${minutes}:${seconds}`;
                deadlineBox.classList.toggle('is-critical', timeLeft <= 60);

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
                    return;
                }

                const submitButton = document.getElementById('submitPaymentButton');
                submitButton.disabled = true;
                submitButton.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>Mengirim…';
            });
        })();
    </script>
@endpush
