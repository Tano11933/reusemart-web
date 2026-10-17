@extends('layouts.workspace')

@section('title', 'Barang Expired Penitipan · ReuseMart')
@section('page-title', 'Barang Expired Penitipan')

@section('content')
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-end gap-3 mb-4">
        <div>
            <h1 class="rm-page-title">Barang Expired Penitipan</h1>
            <p class="rm-page-sub">Barang yang masa penitipannya sudah habis dan menunggu tindak lanjut.</p>
        </div>
        <a href="{{ route('owner.laporanBarangExpired.pdf', ['tanggal' => now()->format('Y-m-d')]) }}" target="_blank" class="btn btn-pdf">
            <i class="bi bi-printer"></i> Cetak PDF
        </a>
    </div>

    <section class="rm-workspace-card">
        <div class="rm-report-head">
            <strong>ReUse Mart</strong>
            <span>Jl. Green Eco Park No. 456 Yogyakarta</span>
        </div>
        <h2 class="rm-report-title">Laporan Barang yang Masa Penitipannya Sudah Habis</h2>
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
                        <th>Tanggal Akhir</th>
                        <th>Batas Ambil</th>
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
                            <td>{{ $d['tgl_akhir'] }}</td>
                            <td>{{ $d['batas_ambil'] }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center">Tidak ada data barang expired.</td>
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
