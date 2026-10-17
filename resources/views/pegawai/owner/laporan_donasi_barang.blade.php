@extends('layouts.workspace')

@section('title', 'Laporan Donasi Barang · ReuseMart')
@section('page-title', 'Laporan Donasi Barang')

@section('content')
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-end gap-3 mb-4">
        <div>
            <h1 class="rm-page-title">Laporan Donasi Barang</h1>
            <p class="rm-page-sub">Rekap barang yang sudah disalurkan ke organisasi penerima donasi.</p>
        </div>
        <div class="d-flex flex-wrap align-items-center gap-2">
            <span class="rm-chip"><i class="bi bi-calendar3"></i>Tahun {{ $tahun }}</span>
            <a href="{{ route('donasi.pdf', ['tahun' => $tahun]) }}" target="_blank" class="btn btn-pdf">
                <i class="bi bi-printer"></i> Unduh PDF
            </a>
        </div>
    </div>

    <section class="rm-workspace-card">
        <div class="rm-report-head">
            <strong>ReUse Mart</strong>
            <span>Jl. Green Eco Park No. 456 Yogyakarta</span>
        </div>
        <h2 class="rm-report-title">Laporan Donasi Barang</h2>
        <div class="rm-report-meta">
            <span>Tahun: <b>{{ $tahun }}</b></span>
            <span>Tanggal cetak: <b>{{ $tanggalCetak }}</b></span>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered align-middle">
                <thead>
                    <tr>
                        <th>Kode Produk</th>
                        <th>Nama Produk</th>
                        <th>ID Penitip</th>
                        <th>Nama Penitip</th>
                        <th>Tanggal Donasi</th>
                        <th>Organisasi</th>
                        <th>Nama Penerima</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($data as $item)
                        <tr>
                            <td>{{ $item->kode_produk }}</td>
                            <td>{{ $item->nama_produk }}</td>
                            <td>{{ $item->id_penitip }}</td>
                            <td>{{ $item->nama_penitip }}</td>
                            <td>{{ \Carbon\Carbon::parse($item->tgl_donasi)->format('d/m/Y') }}</td>
                            <td>{{ $item->nama_organisasi }}</td>
                            <td>{{ $item->nama_penerima }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center">Tidak ada data donasi.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
@endsection

@push('styles')
    <style>
        .btn-pdf { background: #3498db; color: #fff; border: none; border-radius: var(--rm-radius-pill); font-size: .82rem; font-weight: 700; padding: .55rem 1.15rem; }
        .btn-pdf:hover { background: #2167a8; color: #fff; }
        .rm-report-head { display: flex; flex-direction: column; gap: .1rem; padding-bottom: .85rem; border-bottom: 1px solid var(--rm-line); margin-bottom: 1rem; }
        .rm-report-head strong { font-size: 1.05rem; }
        .rm-report-head span { color: var(--rm-muted); font-size: .85rem; }
        .rm-report-title { font-size: .95rem; font-weight: 700; letter-spacing: .04em; text-transform: uppercase; margin: 0 0 .6rem; }
        .rm-report-meta { display: flex; flex-wrap: wrap; gap: .3rem 1.5rem; font-size: .88rem; color: var(--rm-muted); margin-bottom: 1rem; }
        .rm-report-meta b { color: var(--rm-ink); }
    </style>
@endpush
