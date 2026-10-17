@extends('layouts.workspace')

@section('title', 'Laporan Komisi Bulanan · ReuseMart')
@section('page-title', 'Laporan Komisi Bulanan')

@section('content')
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-end gap-3 mb-4">
        <div>
            <h1 class="rm-page-title">Laporan Komisi Bulanan</h1>
            <p class="rm-page-sub">Rekap komisi hunter, komisi ReUse Mart, dan bonus penitip untuk barang yang laku.</p>
        </div>
        <a href="{{ route('komisi.pdf', ['bulan' => $bulan, 'tahun' => $tahun]) }}" target="_blank" class="btn btn-pdf">
            <i class="bi bi-printer"></i> Cetak PDF Laporan Komisi Bulanan
        </a>
    </div>

    <section class="rm-workspace-card">
        <div class="rm-report-head">
            <strong>ReUse Mart</strong>
            <span>Jl. Green Eco Park No. 456 Yogyakarta</span>
        </div>
        <h2 class="rm-report-title">Laporan Komisi Bulanan</h2>
        <div class="rm-report-meta">
            <span>Bulan: <b>{{ \Carbon\Carbon::create()->month($bulan)->locale('id')->isoFormat('MMMM') }}</b></span>
            <span>Tahun: <b>{{ $tahun }}</b></span>
            <span>Tanggal cetak: <b>{{ $tanggalCetak }}</b></span>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered align-middle">
                <thead>
                    <tr>
                        <th>Kode Produk</th>
                        <th>Nama Produk</th>
                        <th>Harga Jual</th>
                        <th>Tanggal Masuk</th>
                        <th>Tanggal Laku</th>
                        <th>Komisi Hunter</th>
                        <th>Komisi ReUse Mart</th>
                        <th>Bonus Penitip</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $totalMart = $totalHunter = $totalBonus = 0;
                    @endphp
                    @forelse ($data as $d)
                        <tr>
                            <td>{{ $d['kode_produk'] }}</td>
                            <td>{{ $d['nama_produk'] }}</td>
                            <td>Rp {{ number_format($d['harga_jual'], 0, ',', '.') }}</td>
                            <td>{{ $d['tgl_masuk'] }}</td>
                            <td>{{ $d['tgl_laku'] }}</td>
                            <td>Rp {{ number_format($d['komisi_hunter'], 0, ',', '.') }}</td>
                            <td>Rp {{ number_format($d['komisi_mart'], 0, ',', '.') }}</td>
                            <td>Rp {{ number_format($d['bonus_penitip'], 0, ',', '.') }}</td>
                        </tr>
                        @php
                            $totalHunter += $d['komisi_hunter'];
                            $totalMart += $d['komisi_mart'];
                            $totalBonus += $d['bonus_penitip'];
                        @endphp
                    @empty
                        <tr>
                            <td colspan="8" class="text-center">Tidak ada data komisi untuk bulan ini.</td>
                        </tr>
                    @endforelse
                    <tr class="fw-bold table-secondary">
                        <td colspan="5" class="text-center">Total</td>
                        <td>Rp {{ number_format($totalHunter, 0, ',', '.') }}</td>
                        <td>Rp {{ number_format($totalMart, 0, ',', '.') }}</td>
                        <td>Rp {{ number_format($totalBonus, 0, ',', '.') }}</td>
                    </tr>
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
