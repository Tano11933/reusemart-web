@extends('layouts.workspace')

@section('title', 'Laporan Transaksi Penitip · ReuseMart')
@section('page-title', 'Laporan Transaksi Penitip')

@section('content')
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-end gap-3 mb-4">
        <div>
            <h1 class="rm-page-title">Laporan Transaksi Penitip</h1>
            <p class="rm-page-sub">Pilih penitip dan periode untuk melihat barang yang laku beserta pendapatannya.</p>
        </div>
    </div>

    <section class="rm-workspace-card">
        <h2 class="h5 fw-bold mb-3">Filter Laporan</h2>

        <form method="GET" action="{{ route('transaksi.penitip.laporan') }}" class="row g-3">
            <div class="col-md-4">
                <label for="id_penitip" class="form-label">Pilih Penitip:</label>
                <select name="id_penitip" id="id_penitip" class="form-select" required>
                    <option value="">-- Pilih --</option>
                    @php
                        $penitipList = DB::table('penitip')->get();
                    @endphp
                    @foreach ($penitipList as $p)
                        <option value="{{ $p->id_penitip }}" {{ request('id_penitip') == $p->id_penitip ? 'selected' : '' }}>
                            {{ $p->nama_penitip }} ({{ $p->id_penitip }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label for="bulan" class="form-label">Bulan:</label>
                <select name="bulan" id="bulan" class="form-select" required>
                    @for ($i = 1; $i <= 12; $i++)
                        <option value="{{ $i }}" {{ request('bulan') == $i ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::create()->month($i)->translatedFormat('F') }}
                        </option>
                    @endfor
                </select>
            </div>
            <div class="col-md-3">
                <label for="tahun" class="form-label">Tahun:</label>
                <input type="number" name="tahun" id="tahun" class="form-control" value="{{ request('tahun', date('Y')) }}" required>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn rm-btn-primary w-100">Tampilkan</button>
            </div>
        </form>
    </section>

    @isset($data)
        <section class="rm-workspace-card">
            <div class="rm-report-head">
                <strong>ReUse Mart</strong>
                <span>Jl. Green Eco Park No. 456 Yogyakarta</span>
            </div>
            <h2 class="rm-report-title">Laporan Transaksi Penitip</h2>
            <div class="rm-report-meta">
                <span>ID Penitip: <b>{{ $id_penitip }}</b></span>
                <span>Nama Penitip: <b>{{ $nama_penitip }}</b></span>
                <span>Bulan: <b>{{ $bulan }}</b></span>
                <span>Tahun: <b>{{ $tahun }}</b></span>
                <span>Tanggal cetak: <b>{{ $tanggalCetak }}</b></span>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered align-middle">
                    <thead>
                        <tr>
                            <th>Kode Produk</th>
                            <th>Nama Produk</th>
                            <th>Tanggal Masuk</th>
                            <th>Tanggal Laku</th>
                            <th>Harga Jual Bersih</th>
                            <th>Bonus</th>
                            <th>Pendapatan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($data as $item)
                            <tr>
                                <td>{{ $item['kode_produk'] }}</td>
                                <td>{{ $item['nama_produk'] }}</td>
                                <td>{{ $item['tanggal_masuk'] }}</td>
                                <td>{{ $item['tanggal_laku'] }}</td>
                                <td>{{ number_format($item['harga_bersih'], 0, ',', '.') }}</td>
                                <td>{{ number_format($item['bonus'], 0, ',', '.') }}</td>
                                <td>{{ number_format($item['pendapatan'], 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">Tidak ada transaksi untuk bulan ini.</td>
                            </tr>
                        @endforelse
                        <tr class="fw-bold table-secondary">
                            <td colspan="4">Total Harga Jual Bersih</td>
                            <td>{{ number_format($totalHargaBersih, 0, ',', '.') }}</td>
                            <td>{{ number_format($totalBonus, 0, ',', '.') }}</td>
                            <td>{{ number_format($total, 0, ',', '.') }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <a href="{{ route('transaksi.penitip.pdf', ['id_penitip' => $id_penitip, 'bulan' => request('bulan'), 'tahun' => request('tahun')]) }}" target="_blank" class="btn btn-pdf mt-3">
                <i class="bi bi-printer"></i> Unduh PDF
            </a>
        </section>
    @endisset
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
