@extends('layouts.buyer')

@section('title', 'Keranjang Belanja — ReuseMart')

@section('content')
    <section class="container py-5">
        <h1 class="h3 rm-page-title mb-4"><i class="bi bi-cart3 me-2"></i>Keranjang Belanja</h1>

        @if (count($cartItems) === 0)
            <div class="rm-empty">
                <i class="bi bi-cart-x d-block mb-2"></i>
                <p class="mb-3">Keranjang kamu masih kosong.</p>
                <a href="{{ route('lihat_semua_beli') }}" class="btn rm-btn-primary">
                    <i class="bi bi-grid me-1"></i>Mulai belanja
                </a>
            </div>
        @else
            @if ($adaBarangTerjual)
                <div class="alert alert-warning d-flex align-items-center gap-2" role="alert">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    <div>Ada barang di keranjangmu yang sudah terjual. Hapus barang tersebut untuk melanjutkan checkout.</div>
                </div>
            @endif

            <div class="rm-detail-card">
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead>
                            <tr class="rm-text-muted small text-uppercase">
                                <th>Barang</th>
                                <th class="text-end">Harga</th>
                                <th class="text-center">Jumlah</th>
                                <th class="text-end">Total</th>
                                <th class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $grandTotal = 0; @endphp
                            @foreach ($cartItems as $id => $item)
                                @php
                                    $total = $item['harga_barang'] * $item['quantity'];
                                    $grandTotal += $total;
                                    $isTerjual = ($item['status'] ?? null) === 'terjual';

                                    $foto = trim((string) ($item['foto_barang'] ?? ''));
                                    $img = ($foto !== '' && file_exists(public_path('image/' . ltrim($foto, '/'))))
                                        ? asset('image/' . ltrim($foto, '/'))
                                        : asset('image/placeholder.svg');
                                @endphp
                                <tr class="{{ $isTerjual ? 'table-danger' : '' }}">
                                    <td>
                                        <div class="d-flex align-items-center gap-3">
                                            <img src="{{ $img }}" alt="{{ $item['nama_barang'] }}"
                                                 class="rounded" style="width: 64px; height: 64px; object-fit: cover;">
                                            <div>
                                                <div class="fw-semibold">{{ $item['nama_barang'] }}</div>
                                                @if ($isTerjual)
                                                    <span class="badge bg-danger">Terjual</span>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-end">Rp {{ number_format($item['harga_barang'], 0, ',', '.') }}</td>
                                    <td class="text-center">{{ $item['quantity'] }}</td>
                                    <td class="text-end fw-semibold">Rp {{ number_format($total, 0, ',', '.') }}</td>
                                    <td class="text-end">
                                        <form action="{{ route('remove_from_cart', $id) }}" method="POST"
                                              onsubmit="return confirm('Hapus barang ini dari keranjang?');">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-outline-danger btn-sm rounded-pill px-3">
                                                <i class="bi bi-trash me-1"></i>Hapus
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="3" class="text-end fw-semibold">Grand Total</td>
                                <td class="text-end fw-bold fs-5" style="color: var(--rm-primary);">
                                    Rp {{ number_format($grandTotal, 0, ',', '.') }}
                                </td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <div class="d-flex justify-content-end mt-4">
                    <a href="{{ $adaBarangTerjual ? '#' : route('checkout.index') }}"
                       class="btn rm-btn-primary btn-lg px-4 {{ $adaBarangTerjual ? 'disabled' : '' }}"
                       @if ($adaBarangTerjual) onclick="return false;" aria-disabled="true" @endif>
                        Lanjut ke Checkout <i class="bi bi-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
        @endif
    </section>
@endsection
