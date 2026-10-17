@extends('layouts.workspace')

@section('title', 'Laporan Penjualan Kategori · ReuseMart')
@section('page-title', 'Laporan Penjualan Kategori')

@section('content')
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-end gap-3 mb-4">
        <div>
            <h1 class="rm-page-title">Laporan Penjualan Kategori</h1>
            <p class="rm-page-sub">Jumlah item yang berhasil dan gagal terjual untuk setiap kategori barang.</p>
        </div>
        <span class="rm-chip"><i class="bi bi-calendar3"></i>Tahun {{ $tahun }}</span>
    </div>

    <section class="rm-workspace-card">
        <div class="rm-report-head">
            <strong>ReUse Mart</strong>
            <span>Jl. Green Eco Park No. 456 Yogyakarta</span>
        </div>
        <h2 class="rm-report-title">Laporan Penjualan per Kategori Barang</h2>

        <form method="GET" action="{{ route('owner.laporanPenjualanKategori') }}" class="d-flex flex-wrap align-items-end gap-2 mb-3">
            <div>
                <label for="tahun" class="form-label fw-semibold small mb-1">Tahun</label>
                <select name="tahun" id="tahun" class="form-select form-select-sm" style="min-width: 120px;">
                    @for ($y = date('Y'); $y >= date('Y') - 6; $y--)
                        <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>
            <button type="submit" class="btn rm-btn-outline btn-sm">Tampilkan</button>
            <a href="{{ route('owner.laporanPenjualanKategori.pdf', ['tahun' => $tahun]) }}" target="_blank" class="btn btn-pdf btn-sm">
                <i class="bi bi-printer"></i> Cetak PDF
            </a>
        </form>

        <div class="rm-report-meta">
            <span>Tahun: <b>{{ $tahun }}</b></span>
            <span>Tanggal cetak: <b>{{ $tanggalCetak }}</b></span>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered align-middle">
                <thead>
                    <tr>
                        <th>Kategori</th>
                        <th>Jumlah item terjual</th>
                        <th>Jumlah item gagal terjual</th>
                    </tr>
                </thead>
                <tbody>
                    @php $totalTerjual = $totalGagal = 0; @endphp
                    @forelse ($data as $d)
                        <tr>
                            <td>{{ $d['kategori'] }}</td>
                            <td>{{ $d['item_terjual'] }}</td>
                            <td>{{ $d['item_gagal'] }}</td>
                        </tr>
                        @php
                            $totalTerjual += $d['item_terjual'];
                            $totalGagal += $d['item_gagal'];
                        @endphp
                    @empty
                        <tr>
                            <td colspan="3" class="text-center">Tidak ada data</td>
                        </tr>
                    @endforelse
                    <tr class="fw-bold table-secondary">
                        <td>Total</td>
                        <td>{{ $totalTerjual }}</td>
                        <td>{{ $totalGagal }}</td>
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
