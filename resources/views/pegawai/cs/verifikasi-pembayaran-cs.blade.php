@extends('layouts.workspace')

@section('title', 'Verifikasi Pembayaran — ReuseMart')
@section('page-title', 'Verifikasi Pembayaran')

@section('content')
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-end gap-3 mb-4">
        <div>
            <h1 class="rm-page-title">Verifikasi Pembayaran</h1>
            <p class="rm-page-sub">Periksa bukti transfer sebelum pesanan disiapkan.</p>
        </div>
        <span class="rm-chip"><i class="bi bi-hourglass-split"></i>{{ $pemesanan->count() }} menunggu pemeriksaan</span>
    </div>

    <section class="rm-workspace-card">
        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3 mb-3">
            <div>
                <h2 class="h5 fw-bold mb-1">Pesanan Menunggu Verifikasi</h2>
                <p class="small rm-text-muted mb-0">Gunakan pratinjau untuk memeriksa bukti pembayaran.</p>
            </div>
            <div class="input-group rm-verification-search">
                <span class="input-group-text bg-white border-end-0"><i class="bi bi-search"></i></span>
                <input type="search" id="verificationSearch" class="form-control border-start-0" placeholder="Cari nomor pesanan atau pembeli…" aria-label="Cari pesanan">
            </div>
        </div>

        @if ($pemesanan->isEmpty())
            <div class="rm-empty py-5">
                <i class="bi bi-check2-circle d-block"></i>
                <h2 class="h5 fw-bold">Semua pembayaran sudah diperiksa</h2>
                <p class="mb-0">Pesanan baru akan muncul di sini setelah pembeli mengirimkan bukti pembayaran.</p>
            </div>
        @else
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>Pesanan</th>
                            <th>Pembeli</th>
                            <th>Tanggal</th>
                            <th>Total</th>
                            <th>Bukti Transfer</th>
                            <th class="text-end">Keputusan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($pemesanan as $item)
                            <tr>
                                <td><strong>{{ $item->id_pemesanan }}</strong></td>
                                <td>
                                    <strong>{{ $item->pembeli->nama_pembeli ?? 'Pembeli' }}</strong>
                                    <div class="small rm-text-muted">{{ $item->id_pembeli }}</div>
                                </td>
                                <td>{{ $item->tgl_pesan?->translatedFormat('d M Y') ?? '—' }}</td>
                                <td class="fw-semibold">Rp {{ number_format($item->total_harga ?? 0, 0, ',', '.') }}</td>
                                <td>
                                    @if ($item->bukti_transfer)
                                        <a href="{{ asset('storage/' . $item->bukti_transfer) }}" target="_blank" rel="noopener" class="btn rm-btn-outline btn-sm">
                                            <i class="bi bi-image me-1"></i>Lihat Bukti
                                        </a>
                                    @else
                                        <span class="small rm-text-muted">Belum diunggah</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <div class="d-inline-flex flex-wrap justify-content-end gap-2">
                                        <form action="{{ route('verifikasi-pembayaran.validasi', ['id' => $item->id_pemesanan]) }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="status" value="disiapkan">
                                            <button type="submit" class="btn rm-btn-primary btn-sm" @disabled(!$item->bukti_transfer)>
                                                <i class="bi bi-check2 me-1"></i>Valid
                                            </button>
                                        </form>
                                        <form action="{{ route('verifikasi-pembayaran.validasi', ['id' => $item->id_pemesanan]) }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="status" value="batal">
                                            <button type="submit" class="btn btn-outline-danger btn-sm rounded-pill" @disabled(!$item->bukti_transfer)>
                                                <i class="bi bi-x-lg me-1"></i>Tolak
                                            </button>
                                        </form>
                                    </div>
                                </td>
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
        .rm-verification-search { max-width: 360px; }
        .rm-verification-search .input-group-text { border-radius: var(--rm-radius-pill) 0 0 var(--rm-radius-pill); }
        .rm-verification-search .form-control { border-radius: 0 var(--rm-radius-pill) var(--rm-radius-pill) 0; }
        @media (max-width: 575.98px) { .rm-verification-search { max-width: none; } }
    </style>
@endpush

@push('scripts')
    <script>
        document.getElementById('verificationSearch')?.addEventListener('input', function () {
            const value = this.value.trim().toLocaleLowerCase('id-ID');
            document.querySelectorAll('.rm-workspace-content tbody tr').forEach(row => {
                row.hidden = !row.textContent.toLocaleLowerCase('id-ID').includes(value);
            });
        });
    </script>
@endpush
