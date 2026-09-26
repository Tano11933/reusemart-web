@extends('layouts.workspace')

@section('title', 'Nota Pemesanan — ReuseMart')
@section('page-title', 'Nota Pemesanan')

@section('content')
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-end gap-3 mb-4">
        <div><h1 class="rm-page-title">Nota Pemesanan</h1><p class="rm-page-sub">Unduh nota pengiriman atau pengambilan untuk pesanan yang telah dibayar.</p></div>
        <span class="rm-chip"><i class="bi bi-receipt"></i>{{ $pemesananList->count() }} nota</span>
    </div>

    <section class="rm-workspace-card">
        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3 mb-3">
            <div><h2 class="h5 fw-bold mb-1">Daftar Nota</h2><p class="small rm-text-muted mb-0">Filter berdasarkan cara penerimaan barang.</p></div>
            <div class="rm-note-filters">
                <a href="{{ route('nota.pemesanan') }}" class="{{ request('filter') === null ? 'active' : '' }}">Semua</a>
                <a href="{{ route('nota.pemesanan', ['filter' => 'dikirim']) }}" class="{{ request('filter') === 'dikirim' ? 'active' : '' }}"><i class="bi bi-truck me-1"></i>Pengiriman</a>
                <a href="{{ route('nota.pemesanan', ['filter' => 'diambil']) }}" class="{{ request('filter') === 'diambil' ? 'active' : '' }}"><i class="bi bi-shop me-1"></i>Ambil Sendiri</a>
            </div>
        </div>

        @if ($pemesananList->isEmpty())
            <div class="rm-empty py-5"><i class="bi bi-receipt d-block"></i><h2 class="h5 fw-bold">Nota belum tersedia</h2><p class="mb-0">Pesanan yang siap dibuatkan nota akan muncul di sini.</p></div>
        @else
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead><tr><th>Nomor pesanan</th><th>Pembeli</th><th>Tanggal</th><th>Penerimaan</th><th>Status</th><th class="text-end">Nota</th></tr></thead>
                    <tbody>
                        @foreach ($pemesananList as $order)
                            <tr>
                                <td><strong>{{ $order->id_pemesanan }}</strong></td>
                                <td>{{ $order->pembeli->nama_pembeli ?? 'Pembeli' }}<div class="small rm-text-muted">{{ $order->id_pembeli }}</div></td>
                                <td>{{ $order->tgl_pesan?->translatedFormat('d M Y') ?? '—' }}</td>
                                <td>{{ $order->jenis_pengantaran === 'diambil' ? 'Ambil sendiri' : 'Pengiriman' }}</td>
                                <td><span class="rm-status-pill {{ $order->status === 'transaksi selesai' ? 'is-done' : '' }}">{{ ucfirst($order->status) }}</span></td>
                                <td class="text-end"><a href="{{ route('nota.download', $order->id_pemesanan) }}" class="btn rm-btn-primary btn-sm"><i class="bi bi-download me-1"></i>Unduh Nota</a></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>
@endsection

@push('styles')
    <style>
        .rm-note-filters{display:flex;flex-wrap:wrap;gap:.35rem;padding:.25rem;border:1px solid var(--rm-line);border-radius:var(--rm-radius-pill);background:#fff}
        .rm-note-filters a{padding:.42rem .75rem;border-radius:var(--rm-radius-pill);color:var(--rm-ink-soft);font-size:.77rem;font-weight:600;white-space:nowrap}
        .rm-note-filters a:hover,.rm-note-filters a.active{background:var(--rm-primary);color:#fff}
        .rm-status-pill{display:inline-flex;padding:.32rem .65rem;border-radius:var(--rm-radius-pill);background:var(--rm-warning-soft);color:#76540f;font-size:.73rem;font-weight:700;white-space:nowrap}
        .rm-status-pill.is-done{background:var(--rm-success-soft);color:var(--rm-success)}
        @media(max-width:575.98px){.rm-note-filters{border-radius:var(--rm-radius);justify-content:center}}
    </style>
@endpush
