@extends('layouts.workspace')

@section('title', 'Laporan Request Donasi · ReuseMart')
@section('page-title', 'Laporan Request Donasi')

@section('content')
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-end gap-3 mb-4">
        <div>
            <h1 class="rm-page-title">Laporan Request Donasi</h1>
            <p class="rm-page-sub">Daftar permintaan donasi dari organisasi beserta barang yang diajukan.</p>
        </div>
        <a href="{{ route('requestdonasi.pdf') }}" target="_blank" class="btn btn-pdf">
            <i class="bi bi-printer"></i> Unduh PDF
        </a>
    </div>

    <section class="rm-workspace-card">
        <div class="rm-report-head">
            <strong>ReUse Mart</strong>
            <span>Jl. Green Eco Park No. 456 Yogyakarta</span>
        </div>
        <h2 class="rm-report-title">Laporan Request Donasi</h2>
        <div class="rm-report-meta">
            <span>Tanggal cetak: <b>{{ $tanggalCetak }}</b></span>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered align-middle">
                <thead>
                    <tr>
                        <th>ID Organisasi</th>
                        <th>Nama</th>
                        <th>Alamat</th>
                        <th>Request</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($data as $row)
                        <tr>
                            <td>{{ $row->id_organisasi }}</td>
                            <td>{{ $row->nama_organisasi }}</td>
                            <td>{{ $row->alamat_organisasi }}</td>
                            <td>{{ $row->nama_barang_request }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center">Tidak ada data request donasi.</td>
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
