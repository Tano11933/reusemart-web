@extends('layouts.workspace')

@section('title', 'Laporan Penjualan Bulanan · ReuseMart')
@section('page-title', 'Laporan Penjualan Bulanan')

@section('content')
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-end gap-3 mb-4">
        <div>
            <h1 class="rm-page-title">Laporan Penjualan Bulanan</h1>
            <p class="rm-page-sub">Rekap jumlah barang terjual dan penjualan kotor ReUse Mart per bulan.</p>
        </div>
        <div class="d-flex flex-wrap align-items-center gap-2">
            <span class="rm-chip"><i class="bi bi-calendar3"></i>Tahun {{ $tahun ?? '2024' }}</span>
            <a href="{{ route('nota.pdf', ['tahun' => $tahun ?? date('Y')]) }}" target="_blank" class="btn btn-pdf">
                <i class="bi bi-printer"></i> Cetak Semua Nota Penjualan (PDF)
            </a>
        </div>
    </div>

    <section class="rm-workspace-card">
        <div class="rm-report-head">
            <strong>ReUse Mart</strong>
            <span>Jl. Green Eco Park No. 456 Yogyakarta</span>
        </div>
        <h2 class="rm-report-title">Laporan Penjualan Bulanan</h2>
        <div class="rm-report-meta">
            <span>Tahun: <b>{{ $tahun ?? '2024' }}</b></span>
            <span>Tanggal cetak: <b>{{ $tanggalCetak ?? \Carbon\Carbon::now()->format('d F Y') }}</b></span>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered align-middle">
                <thead>
                    <tr>
                        <th>Bulan</th>
                        <th>Jumlah Barang Terjual</th>
                        <th>Jumlah Penjualan Kotor</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $totalTerjual = 0;
                        $totalKotor = 0;
                    @endphp
                    @foreach ($laporanBulanan as $laporan)
                        <tr>
                            <td>{{ $laporan['bulan'] }}</td>
                            <td>{{ $laporan['jumlah_terjual'] }}</td>
                            <td>{{ number_format($laporan['penjualan_kotor'], 0, ',', '.') }}</td>
                        </tr>
                        @php
                            $totalTerjual += $laporan['jumlah_terjual'];
                            $totalKotor += $laporan['penjualan_kotor'];
                        @endphp
                    @endforeach
                    <tr class="fw-bold table-secondary">
                        <td>Total</td>
                        <td>{{ $totalTerjual }}</td>
                        <td>{{ number_format($totalKotor, 0, ',', '.') }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </section>

    <section class="rm-workspace-card">
        <h2 class="h5 fw-bold mb-1">Grafik Penjualan Kotor per Bulan</h2>
        <p class="small rm-text-muted mb-3">Perbandingan penjualan kotor tiap bulan pada tahun {{ $tahun ?? '2024' }}.</p>
        <canvas id="chartPenjualan" height="80"></canvas>
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

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const bulan = {!! json_encode(array_column($laporanBulanan, 'bulan')) !!};
        const penjualan = {!! json_encode(array_column($laporanBulanan, 'penjualan_kotor')) !!};
        const ctx = document.getElementById('chartPenjualan').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: bulan,
                datasets: [{
                    label: 'Penjualan Kotor',
                    data: penjualan,
                    borderWidth: 2,
                    backgroundColor: 'rgba(32, 83, 114, 0.65)',
                }]
            },
            options: {
                plugins: { legend: { display: false } },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function (value) {
                                return 'Rp ' + value.toLocaleString('id-ID');
                            }
                        }
                    }
                }
            }
        });
    </script>
@endpush
