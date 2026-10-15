@extends('layouts.partner')

@section('title', 'Riwayat Penjualan · ReuseMart')

@section('content')
    @php $totalPenjualan = $transactions->sum(fn ($detail) => $detail->harga ?? 0); @endphp
    <section class="container py-4 py-lg-5">
        <nav class="rm-breadcrumb small mb-3" aria-label="Breadcrumb"><a href="{{ route('penitip.barangTitipan') }}">Barang Titipan</a><span class="mx-1">/</span><span class="rm-text-muted">Riwayat Penjualan</span></nav>

        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-end gap-3 mb-4">
            <div><h1 class="rm-page-title">Riwayat Penjualan</h1><p class="rm-page-sub">Barang titipan yang sudah berhasil terjual.</p></div>
            <div class="rm-sales-summary"><small>Total penjualan tercatat</small><strong>Rp {{ number_format($totalPenjualan, 0, ',', '.') }}</strong></div>
        </div>

        @if ($transactions->isEmpty())
            <div class="rm-empty"><i class="bi bi-graph-down d-block"></i><h2 class="h5 fw-bold">Belum ada penjualan</h2><p class="mb-0">Transaksi yang selesai akan tampil di sini.</p></div>
        @else
            <section class="rm-partner-panel">
                <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3 mb-3">
                    <div><h2 class="h5 fw-bold mb-1">Barang Terjual</h2><p class="small rm-text-muted mb-0">{{ $transactions->count() }} item terjual</p></div>
                    <div class="input-group rm-sales-search"><span class="input-group-text bg-white border-end-0"><i class="bi bi-search"></i></span><input id="salesSearch" type="search" class="form-control border-start-0" placeholder="Cari barang atau pesanan…" aria-label="Cari penjualan"></div>
                </div>

                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead><tr><th>Barang</th><th>Pesanan</th><th>Tanggal</th><th>Nilai penjualan</th><th></th></tr></thead>
                        <tbody id="salesRows">
                            @foreach ($transactions as $detail)
                                @php
                                    $item = $detail->barang;
                                    $order = $detail->pemesanan;
                                    $foto = trim((string) ($item->foto_barang ?? ''));
                                    $img = ($foto !== '' && file_exists(public_path('image/' . ltrim($foto, '/')))) ? asset('image/' . ltrim($foto, '/')) : asset('image/placeholder.svg');
                                @endphp
                                <tr data-search="{{ \Illuminate\Support\Str::lower(($item->nama_barang ?? '') . ' ' . ($order->id_pemesanan ?? '')) }}">
                                    <td><div class="d-flex align-items-center gap-3"><img class="rm-sales-thumb" src="{{ $img }}" alt="{{ $item->nama_barang ?? 'Barang' }}" onerror="this.onerror=null;this.src='{{ asset('image/placeholder.svg') }}'"><div><strong>{{ $item->nama_barang ?? 'Barang tidak tersedia' }}</strong><div class="small rm-text-muted">{{ $item->kategori ?? '-' }}</div></div></div></td>
                                    <td>{{ $order->id_pemesanan ?? '-' }}</td>
                                    <td>{{ $order?->tgl_pesan?->translatedFormat('d M Y') ?? '-' }}</td>
                                    <td class="fw-bold" style="color:var(--rm-primary)">Rp {{ number_format($detail->harga ?? 0, 0, ',', '.') }}</td>
                                    <td class="text-end"><a href="{{ $item ? route('penitip.detail_barang', ['id' => $item->id_barang]) : '#' }}" class="btn rm-btn-outline btn-sm" @disabled(!$item)>Detail</a></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </section>
        @endif
    </section>
@endsection

@push('styles')
    <style>
        .rm-sales-summary { display:grid;padding:.75rem 1rem;border:1px solid var(--rm-line);border-radius:var(--rm-radius);background:#fff;box-shadow:var(--rm-shadow-xs); }
        .rm-sales-summary small { color:var(--rm-muted);font-size:.74rem; }
        .rm-sales-summary strong { color:var(--rm-primary-dark);font-size:1.18rem; }
        .rm-sales-search { max-width:300px; }
        .rm-sales-search .input-group-text { border-radius:var(--rm-radius-pill) 0 0 var(--rm-radius-pill); }
        .rm-sales-search .form-control { border-radius:0 var(--rm-radius-pill) var(--rm-radius-pill) 0; }
        .rm-sales-thumb { width:58px;height:58px;object-fit:contain;padding:.2rem;border-radius:.65rem;background:var(--rm-primary-soft-2); }
        @media(max-width:575.98px){.rm-sales-search{max-width:none}}
    </style>
@endpush

@push('scripts')
    <script>
        document.getElementById('salesSearch')?.addEventListener('input', function () {
            const query = this.value.trim().toLocaleLowerCase('id-ID');
            document.querySelectorAll('#salesRows tr[data-search]').forEach(row => row.hidden = !row.dataset.search.includes(query));
        });
    </script>
@endpush
