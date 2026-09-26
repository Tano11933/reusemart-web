@extends('layouts.workspace')

@section('title', 'Pesanan Pengiriman — ReuseMart')
@section('page-title', 'Pesanan Pengiriman')

@section('content')
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-end gap-3 mb-4">
        <div><h1 class="rm-page-title">Pesanan Pengiriman</h1><p class="rm-page-sub">Jadwalkan pengiriman dan tugaskan kurir untuk pesanan yang valid.</p></div>
        <span class="rm-chip"><i class="bi bi-truck"></i>{{ $pemesanan->count() }} pesanan perlu diatur</span>
    </div>

    <section class="rm-workspace-card">
        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3 mb-3">
            <div><h2 class="h5 fw-bold mb-1">Daftar Pengiriman</h2><p class="small rm-text-muted mb-0">Atur tanggal lebih dulu, kemudian pilih kurir.</p></div>
            <div class="input-group rm-dispatch-search"><span class="input-group-text bg-white border-end-0"><i class="bi bi-search"></i></span><input id="dispatchSearch" class="form-control border-start-0" type="search" placeholder="Cari pesanan atau pembeli…" aria-label="Cari pesanan pengiriman"></div>
        </div>

        @if ($pemesanan->isEmpty())
            <div class="rm-empty py-5"><i class="bi bi-truck d-block"></i><h2 class="h5 fw-bold">Tidak ada pengiriman tertunda</h2><p class="mb-0">Pesanan dengan pembayaran valid akan muncul di sini.</p></div>
        @else
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead><tr><th>Pesanan</th><th>Pembeli</th><th>Tanggal pesanan</th><th>Jadwal</th><th>Rincian</th><th class="text-end">Tugaskan kurir</th></tr></thead>
                    <tbody id="dispatchRows">
                        @foreach ($pemesanan as $order)
                            <tr data-search="{{ \Illuminate\Support\Str::lower($order->id_pemesanan . ' ' . ($order->pembeli->nama_pembeli ?? '') . ' ' . $order->id_pembeli) }}">
                                <td><strong>{{ $order->id_pemesanan }}</strong><div class="small rm-text-muted">{{ $order->detailPemesanans->count() }} barang</div></td>
                                <td><strong>{{ $order->pembeli->nama_pembeli ?? 'Pembeli' }}</strong><div class="small rm-text-muted">{{ $order->id_pembeli }}</div></td>
                                <td>{{ $order->tgl_pesan?->translatedFormat('d M Y') ?? '—' }}</td>
                                <td>
                                    @if ($order->tgl_kirim)
                                        <span class="rm-scheduled-date"><i class="bi bi-calendar-check me-1"></i>{{ \Carbon\Carbon::parse($order->tgl_kirim)->translatedFormat('d M Y') }}</span>
                                    @else
                                        <form action="{{ route('transaksi.jadwalkan', $order->id_pemesanan) }}" method="POST" class="rm-date-form">
                                            @csrf
                                            <input type="date" name="tgl_kirim" class="form-control form-control-sm" min="{{ now()->toDateString() }}" required aria-label="Tanggal pengiriman">
                                            <button type="submit" class="btn rm-btn-outline btn-sm mt-1 w-100">Jadwalkan</button>
                                        </form>
                                    @endif
                                </td>
                                <td><button type="button" class="btn btn-outline-secondary btn-sm rounded-pill" data-bs-toggle="modal" data-bs-target="#detail-{{ \Illuminate\Support\Str::slug($order->id_pemesanan) }}"><i class="bi bi-eye me-1"></i>Lihat</button></td>
                                <td class="text-end">
                                    @if ($order->tgl_kirim)
                                        <form action="{{ route('transaksi.konfirmasi', $order->id_pemesanan) }}" method="POST" class="rm-assign-form d-inline-grid gap-1">
                                            @csrf
                                            <select name="id_kurir" class="form-select form-select-sm" required aria-label="Pilih kurir"><option value="">Pilih kurir</option>@foreach ($kurirList as $kurir)<option value="{{ $kurir->id_pegawai }}">{{ $kurir->nama_pegawai }}</option>@endforeach</select>
                                            <button type="submit" class="btn rm-btn-primary btn-sm"><i class="bi bi-person-check me-1"></i>Tugaskan</button>
                                        </form>
                                    @else
                                        <span class="small rm-text-muted">Jadwalkan tanggal dahulu</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>

    @foreach ($pemesanan as $order)
        <div class="modal fade" id="detail-{{ \Illuminate\Support\Str::slug($order->id_pemesanan) }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable"><div class="modal-content rm-warehouse-modal">
                <div class="modal-header"><div><h2 class="modal-title h5 fw-bold">Rincian {{ $order->id_pemesanan }}</h2><p class="small rm-text-muted mb-0">{{ $order->pembeli->nama_pembeli ?? 'Pembeli' }}</p></div><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button></div>
                <div class="modal-body"><div class="d-grid gap-2">
                    @foreach ($order->detailPemesanans as $detail)
                        @php $item = $detail->barang; $photo = trim((string) ($item->foto_barang ?? '')); $image = ($photo && file_exists(public_path('image/' . ltrim($photo, '/')))) ? asset('image/' . ltrim($photo, '/')) : asset('image/placeholder.svg'); @endphp
                        <div class="rm-order-detail-item"><img src="{{ $image }}" alt="{{ $item->nama_barang ?? 'Barang' }}"><div><strong>{{ $item->nama_barang ?? 'Barang' }}</strong><div class="small rm-text-muted">{{ $item->kategori ?? '—' }}</div></div><strong>Rp {{ number_format($detail->harga, 0, ',', '.') }}</strong></div>
                    @endforeach
                </div></div>
            </div></div>
        </div>
    @endforeach
@endsection

@push('styles')
    <style>
        .rm-dispatch-search{max-width:350px}.rm-dispatch-search .input-group-text{border-radius:var(--rm-radius-pill) 0 0 var(--rm-radius-pill)}.rm-dispatch-search .form-control{border-radius:0 var(--rm-radius-pill) var(--rm-radius-pill) 0}
        .rm-date-form{width:145px}.rm-date-form .form-control{min-width:0}.rm-assign-form{min-width:145px}
        .rm-scheduled-date{display:inline-flex;align-items:center;padding:.35rem .6rem;border-radius:var(--rm-radius-pill);background:var(--rm-success-soft);color:var(--rm-success);font-size:.76rem;font-weight:700;white-space:nowrap}
        .rm-warehouse-modal{border:1px solid var(--rm-line);border-radius:var(--rm-radius-lg);overflow:hidden}.rm-warehouse-modal .modal-header{border-color:var(--rm-line);padding:1rem 1.25rem}
        .rm-order-detail-item{display:grid;grid-template-columns:58px minmax(0,1fr) auto;align-items:center;gap:.75rem;padding:.6rem;border:1px solid var(--rm-line);border-radius:var(--rm-radius-sm);background:#fff}.rm-order-detail-item img{width:58px;height:58px;object-fit:contain;padding:.2rem;border-radius:.55rem;background:var(--rm-primary-soft-2)}
        @media(max-width:575.98px){.rm-dispatch-search{max-width:none}.rm-date-form,.rm-assign-form{width:130px;min-width:130px}.rm-order-detail-item{grid-template-columns:48px minmax(0,1fr)}.rm-order-detail-item img{width:48px;height:48px}.rm-order-detail-item>strong{grid-column:2}}
    </style>
@endpush

@push('scripts')
    <script>
        document.getElementById('dispatchSearch')?.addEventListener('input', function () {
            const value = this.value.trim().toLocaleLowerCase('id-ID');
            document.querySelectorAll('#dispatchRows tr[data-search]').forEach(row => row.hidden = !row.dataset.search.includes(value));
        });

        document.querySelectorAll('.rm-assign-form').forEach(form => form.addEventListener('submit', function (event) {
            event.preventDefault();
            Swal.fire({ title: 'Tugaskan kurir?', text: 'Pesanan akan masuk ke daftar pengiriman kurir.', icon: 'question', showCancelButton: true, confirmButtonText: 'Tugaskan', cancelButtonText: 'Batal', confirmButtonColor: '#1e5b5a' }).then(result => {
                if (result.isConfirmed) HTMLFormElement.prototype.submit.call(form);
            });
        }));
    </script>
@endpush
