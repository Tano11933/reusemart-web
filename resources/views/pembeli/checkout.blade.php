@extends('layouts.buyer')

@section('title', 'Checkout · ReuseMart')

@section('content')
    @php
        $grandTotal = collect($cartItems)->sum(fn ($item) => ($item['harga_barang'] ?? 0) * ($item['quantity'] ?? 1));
        $poinTersedia = (int) ($pembeli->poin ?? 0);
    @endphp

    <section class="container py-4 py-lg-5">
        <nav class="rm-breadcrumb small mb-3" aria-label="Breadcrumb">
            <a href="{{ route('home_beli') }}">Home</a>
            <span class="mx-1">/</span>
            <a href="{{ route('cart.index') }}">Keranjang</a>
            <span class="mx-1">/</span>
            <span class="rm-text-muted">Checkout</span>
        </nav>

        <div class="rm-page-head">
            <div class="rm-page-head-main">
                <span class="rm-page-head-icon" aria-hidden="true"><i class="bi bi-bag-check"></i></span>
                <div>
                    <h1 class="rm-page-title">Checkout</h1>
                    <p class="rm-page-sub">Pilih metode penerimaan dan periksa ringkasan pesanan sebelum lanjut.</p>
                </div>
            </div>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger" role="alert">
                <strong>Periksa kembali pesananmu.</strong>
                <ul class="mb-0 mt-1 ps-3">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="row g-4 align-items-start">
            <div class="col-lg-7">
                <form action="{{ route('checkout.store') }}" method="POST" id="checkoutForm">
                    @csrf

                    <section class="rm-checkout-card mb-3">
                        <div class="rm-checkout-section-heading">
                            <span class="rm-checkout-step">1</span>
                            <div>
                                <h2 class="h5 fw-bold mb-1">Metode Penerimaan</h2>
                                <p class="small rm-text-muted mb-0">Pilih cara menerima barang pesanan.</p>
                            </div>
                        </div>

                        <div class="rm-delivery-options">
                            <label class="rm-delivery-option" for="delivery-courier">
                                <input class="form-check-input" type="radio" name="jenis_pengantaran" id="delivery-courier" value="dikirim" @checked(old('jenis_pengantaran') === 'dikirim') required>
                                <span class="rm-delivery-icon"><i class="bi bi-truck"></i></span>
                                <span class="rm-delivery-copy">
                                    <strong>Diantar kurir</strong>
                                    <small>Diantar ke alamat tersimpan. Gratis di atas Rp 1,5 jt.</small>
                                </span>
                                <i class="bi bi-check-circle-fill rm-delivery-check"></i>
                            </label>
                            <label class="rm-delivery-option" for="delivery-pickup">
                                <input class="form-check-input" type="radio" name="jenis_pengantaran" id="delivery-pickup" value="diambil" @checked(old('jenis_pengantaran', 'diambil') === 'diambil') required>
                                <span class="rm-delivery-icon"><i class="bi bi-shop"></i></span>
                                <span class="rm-delivery-copy">
                                    <strong>Ambil sendiri</strong>
                                    <small>Ambil langsung di lokasi ReuseMart, tanpa ongkir.</small>
                                </span>
                                <i class="bi bi-check-circle-fill rm-delivery-check"></i>
                            </label>
                        </div>

                        <div id="alamatSection" class="mt-3 d-none">
                            <label for="alamat" class="form-label fw-semibold">Alamat Pengiriman</label>
                            <select name="alamat_pengiriman" id="alamat" class="form-select" disabled>
                                <option value="">Memuat alamat…</option>
                            </select>
                            @error('alamat_pengiriman')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                            <a href="{{ route('pembeli.alamat') }}" class="small d-inline-flex align-items-center gap-1 mt-2">
                                <i class="bi bi-plus-circle"></i>Tambah atau kelola alamat
                            </a>
                        </div>
                    </section>

                    <section class="rm-checkout-card">
                        <div class="rm-checkout-section-heading">
                            <span class="rm-checkout-step">2</span>
                            <div>
                                <h2 class="h5 fw-bold mb-1">Tukar Poin</h2>
                                <p class="small rm-text-muted mb-0">Kamu punya {{ number_format($poinTersedia, 0, ',', '.') }} poin. 1 poin bernilai Rp 10.000.</p>
                            </div>
                        </div>

                        <div class="rm-points-choice">
                            <label class="rm-points-option" for="pakai_poin_tidak">
                                <input class="form-check-input" type="radio" name="pakai_poin" id="pakai_poin_tidak" value="tidak" @checked(old('pakai_poin', 'tidak') === 'tidak') required>
                                <span><strong>Simpan poin</strong><small>Gunakan di transaksi berikutnya.</small></span>
                            </label>
                            <label class="rm-points-option" for="pakai_poin_ya">
                                <input class="form-check-input" type="radio" name="pakai_poin" id="pakai_poin_ya" value="ya" @checked(old('pakai_poin') === 'ya') @disabled($poinTersedia <= 0)>
                                <span><strong>Gunakan poin</strong><small>Potong total pembayaran dengan poin.</small></span>
                            </label>
                        </div>

                        <div id="inputPoinSection" class="mt-3 d-none">
                            <label for="tukar_poin" class="form-label fw-semibold">Jumlah poin yang digunakan</label>
                            <div class="input-group">
                                <input type="number" name="poin_ditukar" id="tukar_poin" class="form-control"
                                       min="1" max="{{ $poinTersedia }}" value="{{ old('poin_ditukar') }}" placeholder="Maksimal {{ $poinTersedia }}">
                                <span class="input-group-text">poin</span>
                            </div>
                            <div class="form-text">Nilai potongan: <strong id="pointDiscountPreview">Rp 0</strong></div>
                        </div>
                    </section>
                </form>
            </div>

            <aside class="col-lg-5">
                <section class="rm-checkout-summary">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h2 class="h5 fw-bold mb-0">Ringkasan Pesanan</h2>
                        <span class="rm-chip"><i class="bi bi-bag"></i>{{ count($cartItems) }} barang</span>
                    </div>

                    <div class="rm-checkout-items">
                        @foreach ($cartItems as $id => $item)
                            @php
                                $foto = trim((string) ($item['foto_barang'] ?? ''));
                                $img = ($foto !== '' && file_exists(public_path('image/' . ltrim($foto, '/'))))
                                    ? asset('image/' . ltrim($foto, '/'))
                                    : asset('image/placeholder.svg');
                                $lineTotal = ($item['harga_barang'] ?? 0) * ($item['quantity'] ?? 1);
                            @endphp
                            <div class="rm-checkout-item">
                                <img src="{{ $img }}" alt="{{ $item['nama_barang'] }}" onerror="this.onerror=null;this.src='{{ asset('image/placeholder.svg') }}'">
                                <div class="min-w-0">
                                    <div class="fw-semibold text-truncate">{{ $item['nama_barang'] }}</div>
                                    <div class="small rm-text-muted">Jumlah {{ $item['quantity'] ?? 1 }}</div>
                                </div>
                                <strong>Rp {{ number_format($lineTotal, 0, ',', '.') }}</strong>
                            </div>
                        @endforeach
                    </div>

                    <div class="rm-summary-lines">
                        <div><span>Subtotal</span><strong>Rp {{ number_format($grandTotal, 0, ',', '.') }}</strong></div>
                        <div><span>Pengiriman</span><strong id="shippingPreview">Pilih metode</strong></div>
                        <div id="discountRow" class="d-none"><span>Potongan poin</span><strong id="discountLine">- Rp 0</strong></div>
                    </div>

                    <div class="rm-checkout-total">
                        <span>Perkiraan total</span>
                        <strong id="totalPreview">Rp {{ number_format($grandTotal, 0, ',', '.') }}</strong>
                    </div>

                    <button type="submit" form="checkoutForm" class="btn rm-btn-primary w-100 btn-lg mt-3" id="placeOrderButton">
                        Buat Pesanan <i class="bi bi-arrow-right ms-1"></i>
                    </button>
                    <p class="small rm-text-muted text-center mt-2 mb-0">
                        <i class="bi bi-lock me-1"></i>Setelah pesanan dibuat kamu memilih pembayaran Transfer Bank atau DANA dan mengunggah bukti transfer.
                    </p>
                </section>

                <a href="{{ route('cart.index') }}" class="btn rm-btn-outline w-100 mt-3">
                    <i class="bi bi-arrow-left me-1"></i>Kembali ke Keranjang
                </a>
            </aside>
        </div>
    </section>
@endsection

@push('styles')
    <style>
        .rm-delivery-options,
        .rm-points-choice {
            display: grid;
            gap: 0.65rem;
        }

        .rm-delivery-option,
        .rm-points-option {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            position: relative;
            padding: 0.85rem;
            border: 1.5px solid var(--rm-line);
            border-radius: var(--rm-radius);
            cursor: pointer;
            transition: border-color var(--rm-fast), background-color var(--rm-fast);
        }

        .rm-delivery-option:has(input:checked),
        .rm-points-option:has(input:checked) {
            border-color: var(--rm-primary);
            background: var(--rm-primary-soft-2);
        }

        .rm-delivery-option .form-check-input,
        .rm-points-option .form-check-input {
            margin: 0;
            flex: 0 0 auto;
        }

        .rm-delivery-icon {
            width: 42px;
            height: 42px;
            flex: 0 0 42px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            color: var(--rm-primary);
            background: var(--rm-primary-soft);
            font-size: 1.15rem;
        }

        .rm-delivery-copy,
        .rm-points-option span {
            display: grid;
            gap: 0.1rem;
            min-width: 0;
        }

        .rm-delivery-copy strong,
        .rm-points-option strong {
            color: var(--rm-ink);
            font-size: 0.9rem;
        }

        .rm-delivery-copy small,
        .rm-points-option small {
            color: var(--rm-muted);
            font-size: 0.76rem;
            line-height: 1.4;
        }

        .rm-delivery-check {
            display: none;
            color: var(--rm-primary);
            margin-left: auto;
        }

        .rm-delivery-option:has(input:checked) .rm-delivery-check {
            display: block;
        }

        .rm-points-option:has(input:disabled) {
            cursor: not-allowed;
            opacity: 0.55;
        }
    </style>
@endpush

@push('scripts')
    <script>
        (function () {
            const addressSelect = document.getElementById('alamat');
            const addressSection = document.getElementById('alamatSection');
            const addressUrl = @json(url('/pembeli/' . $pembeli->id_pembeli . '/alamat'));
            const deliveryInputs = document.querySelectorAll('input[name="jenis_pengantaran"]');
            const pointsInputs = document.querySelectorAll('input[name="pakai_poin"]');
            const pointsSection = document.getElementById('inputPoinSection');
            const pointsInput = document.getElementById('tukar_poin');
            const pointsAvailable = {{ $poinTersedia }};
            const subtotal = {{ (float) $grandTotal }};
            const shippingPreview = document.getElementById('shippingPreview');
            const totalPreview = document.getElementById('totalPreview');
            const discountRow = document.getElementById('discountRow');
            const discountLine = document.getElementById('discountLine');
            const discountPreview = document.getElementById('pointDiscountPreview');
            const money = value => 'Rp ' + Math.max(0, value).toLocaleString('id-ID');
            let addressesLoaded = false;

            async function loadAddresses() {
                addressSelect.disabled = true;
                addressSelect.innerHTML = '<option value="">Memuat alamat…</option>';

                try {
                    const response = await fetch(addressUrl, { headers: { 'Accept': 'application/json' } });
                    const data = await response.json();
                    if (!response.ok || data.success === false) throw new Error(data.message || 'Alamat gagal dimuat.');

                    addressSelect.replaceChildren();
                    const placeholder = new Option('-- Pilih alamat --', '');
                    addressSelect.append(placeholder);

                    (data.data || []).forEach(address => {
                        const parts = [address.detail_alamat, address.desa, address.kecamatan, address.kabupaten].filter(Boolean);
                        const label = `${address.label_alamat}${address.default_alamat ? ' (Utama)' : ''}: ${parts.join(', ')}`;
                        addressSelect.append(new Option(label, address.id_alamat));
                    });

                    if (!data.data?.length) {
                        addressSelect.replaceChildren(new Option('Belum ada alamat tersimpan', ''));
                    }

                    const selectedAddress = @json(old('alamat_pengiriman'));
                    if (selectedAddress) addressSelect.value = selectedAddress;
                    addressSelect.disabled = false;
                    addressesLoaded = true;
                } catch (error) {
                    addressSelect.replaceChildren(new Option(error.message, ''));
                    addressSelect.disabled = true;
                    addressesLoaded = true;
                }
            }

            function updateDelivery() {
                const selected = document.querySelector('input[name="jenis_pengantaran"]:checked')?.value;
                const isDelivery = selected === 'dikirim';
                const shippingFree = isDelivery && subtotal >= 1500000;
                addressSection.classList.toggle('d-none', !isDelivery);
                addressSelect.required = isDelivery;
                if (isDelivery && !addressesLoaded) loadAddresses();
                shippingPreview.textContent = !selected ? 'Pilih metode' : (isDelivery ? (shippingFree ? 'Gratis' : money(100000)) : money(0));
                shippingPreview.classList.toggle('rm-text-success', shippingFree);
                updateTotal();
            }

            function updatePoints() {
                const usePoints = document.getElementById('pakai_poin_ya').checked;
                pointsSection.classList.toggle('d-none', !usePoints);
                pointsInput.required = usePoints;
                pointsInput.disabled = !usePoints;
                updateTotal();
            }

            function updateTotal() {
                const delivery = document.querySelector('input[name="jenis_pengantaran"]:checked')?.value;
                const shipping = delivery === 'dikirim' && subtotal < 1500000 ? 100000 : 0;
                const usePoints = document.getElementById('pakai_poin_ya').checked;
                const points = usePoints ? Math.min(pointsAvailable, Math.max(0, parseInt(pointsInput.value || '0', 10))) : 0;
                const discount = points * 10000;

                discountPreview.textContent = money(discount);
                discountLine.textContent = '- ' + money(discount);
                discountRow.classList.toggle('d-none', discount === 0);
                totalPreview.textContent = money(subtotal + shipping - discount);
            }

            deliveryInputs.forEach(input => input.addEventListener('change', function () {
                updateDelivery();
            }));
            pointsInputs.forEach(input => input.addEventListener('change', updatePoints));
            pointsInput.addEventListener('input', updateTotal);

            updateDelivery();
            updatePoints();
        })();
    </script>
@endpush
