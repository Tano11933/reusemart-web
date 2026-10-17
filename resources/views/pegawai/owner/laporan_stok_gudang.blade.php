@extends('layouts.workspace')

@section('title', 'Laporan Stok Gudang · ReuseMart')
@section('page-title', 'Laporan Stok Gudang')

@section('content')
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-end gap-3 mb-4">
        <div>
            <h1 class="rm-page-title">Laporan Stok Gudang</h1>
            <p class="rm-page-sub">Daftar barang yang masih tersimpan di gudang beserta data penitip dan hunter.</p>
        </div>
        <a href="{{ route('stokgudang.pdf') }}" target="_blank" class="btn btn-pdf">
            <i class="bi bi-printer"></i> Cetak PDF Laporan Stok Gudang
        </a>
    </div>

    <section class="rm-workspace-card">
        <div class="rm-report-head">
            <strong>ReUse Mart</strong>
            <span>Jl. Green Eco Park No. 456 Yogyakarta</span>
        </div>
        <h2 class="rm-report-title">Laporan Stok Gudang</h2>
        <div class="rm-report-meta">
            <span>Tanggal cetak: <b>{{ $tanggalCetak }}</b></span>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered align-middle">
                <thead>
                    <tr>
                        <th>Kode Produk</th>
                        <th>Nama Produk</th>
                        <th>Id Penitip</th>
                        <th>Nama Penitip</th>
                        <th>Tanggal Masuk</th>
                        <th>Perpanjangan</th>
                        <th>ID Hunter</th>
                        <th>Nama Hunter</th>
                        <th>Harga</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($data as $d)
                        <tr>
                            <td>{{ $d['kode_produk'] }}</td>
                            <td>{{ $d['nama_produk'] }}</td>
                            <td>{{ $d['id_penitip'] }}</td>
                            <td>{{ $d['nama_penitip'] }}</td>
                            <td>{{ $d['tgl_masuk'] }}</td>
                            <td>{{ $d['perpanjangan'] }}</td>
                            <td>{{ $d['id_hunter'] }}</td>
                            <td>{{ $d['nama_hunter'] }}</td>
                            <td>{{ number_format($d['harga'], 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center">Tidak ada stok barang hari ini.</td>
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
