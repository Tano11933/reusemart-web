@extends('layouts.workspace')

@section('title', 'Pesanan Ambil Sendiri — ReuseMart')
@section('page-title', 'Pesanan Ambil Sendiri')

@section('content')
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-end gap-3 mb-4">
        <div><h1 class="rm-page-title">Pesanan Ambil Sendiri</h1><p class="rm-page-sub">Konfirmasi pesanan yang sudah diterima pembeli di lokasi.</p></div>
        <span class="rm-chip"><i class="bi bi-shop"></i>{{ $pemesananList->count() }} pesanan siap diambil</span>
    </div>

    <section class="rm-workspace-card">
        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3 mb-3">
            <div><h2 class="h5 fw-bold mb-1">Menunggu Pengambilan</h2><p class="small rm-text-muted mb-0">Batas pengambilan adalah tiga hari dari tanggal pesanan.</p></div>
            <div class="input-group rm-pickup-search"><span class="input-group-text bg-white border-end-0"><i class="bi bi-search"></i></span><input id="pickupSearch" class="form-control border-start-0" type="search" placeholder="Cari nomor pesanan atau pembeli…" aria-label="Cari pesanan"></div>
        </div>

        @if ($pemesananList->isEmpty())
            <div class="rm-empty py-5"><i class="bi bi-check2-circle d-block"></i><h2 class="h5 fw-bold">Tidak ada pesanan menunggu</h2><p class="mb-0">Pesanan yang siap diambil akan muncul di sini.</p></div>
        @else
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead><tr><th>Pesanan</th><th>Pembeli</th><th>Tanggal pesanan</th><th>Items</th><th>Total</th><th class="text-end">Aksi</th></tr></thead>
                    <tbody id="pickupRows">
                        @foreach ($pemesananList as $order)
                            <tr data-search="{{ \Illuminate\Support\Str::lower($order->id_pemesanan . ' ' . ($order->pembeli->nama_pembeli ?? '') . ' ' . $order->id_pembeli) }}">
                                <td><strong>{{ $order->id_pemesanan }}</strong><div class="small rm-text-muted">Pembayaran {{ $order->status_pembayaran }}</div></td>
                                <td><strong>{{ $order->pembeli->nama_pembeli ?? 'Pembeli' }}</strong><div class="small rm-text-muted">{{ $order->id_pembeli }}</div></td>
                                <td>{{ $order->tgl_pesan?->translatedFormat('d M Y') ?? '—' }}</td>
                                <td><button type="button" class="btn btn-outline-secondary btn-sm rounded-pill" data-bs-toggle="modal" data-bs-target="#detail-{{ \Illuminate\Support\Str::slug($order->id_pemesanan) }}"><i class="bi bi-eye me-1"></i>{{ $order->detailPemesanans->count() }} barang</button></td>
                                <td class="fw-semibold">Rp {{ number_format($order->total_harga ?? $order->total_harga_pesanan ?? 0, 0, ',', '.') }}</td>
                                <td class="text-end">
                                    <form class="rm-complete-pickup-form" action="{{ route('pemesanan.diambil.pegawai', $order->id_pemesanan) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn rm-btn-primary btn-sm"><i class="bi bi-check2-circle me-1"></i>Konfirmasi Diambil</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>

    @foreach ($pemesananList as $order)
        <div class="modal fade" id="detail-{{ \Illuminate\Support\Str::slug($order->id_pemesanan) }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable"><div class="modal-content rm-warehouse-modal">
                <div class="modal-header"><div><h2 class="modal-title h5 fw-bold">Rincian Pesanan {{ $order->id_pemesanan }}</h2><p class="small rm-text-muted mb-0">{{ $order->detailPemesanans->count() }} barang dalam pesanan.</p></div><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button></div>
                <div class="modal-body">
                    <div class="d-grid gap-2">
                        @foreach ($order->detailPemesanans as $detail)
                            @php
                                $item = $detail->barang;
                                $photo = trim((string) ($item->foto_barang ?? ''));
                                $image = ($photo !== '' && file_exists(public_path('image/' . ltrim($photo, '/')))) ? asset('image/' . ltrim($photo, '/')) : asset('image/placeholder.svg');
                            @endphp
                            <div class="rm-order-detail-item"><img src="{{ $image }}" alt="{{ $item->nama_barang ?? 'Barang' }}" onerror="this.onerror=null;this.src='{{ asset('image/placeholder.svg') }}'"><div class="min-w-0"><strong>{{ $item->nama_barang ?? 'Barang tidak tersedia' }}</strong><div class="small rm-text-muted">Berat {{ $item->berat_barang ?? '—' }} kg &middot; {{ $item->kategori ?? '—' }}</div></div><span class="fw-semibold">Rp {{ number_format($detail->harga, 0, ',', '.') }}</span></div>
                        @endforeach
                    </div>
                </div>
            </div></div>
        </div>
    @endforeach
@endsection

@push('styles')
    <style>
        .rm-pickup-search{max-width:350px}.rm-pickup-search .input-group-text{border-radius:var(--rm-radius-pill) 0 0 var(--rm-radius-pill)}.rm-pickup-search .form-control{border-radius:0 var(--rm-radius-pill) var(--rm-radius-pill) 0}
        .rm-warehouse-modal{border:1px solid var(--rm-line);border-radius:var(--rm-radius-lg);overflow:hidden}.rm-warehouse-modal .modal-header{border-color:var(--rm-line);padding:1rem 1.25rem}
        .rm-order-detail-item{display:grid;grid-template-columns:58px minmax(0,1fr) auto;align-items:center;gap:.75rem;padding:.6rem;border:1px solid var(--rm-line);border-radius:var(--rm-radius-sm);background:#fff}
        .rm-order-detail-item img{width:58px;height:58px;object-fit:contain;padding:.2rem;border-radius:.55rem;background:var(--rm-primary-soft-2)}
        @media(max-width:575.98px){.rm-pickup-search{max-width:none}.rm-order-detail-item{grid-template-columns:48px minmax(0,1fr)}.rm-order-detail-item img{width:48px;height:48px}.rm-order-detail-item>span{grid-column:2}}
    </style>
@endpush

@push('scripts')
    <script>
        document.getElementById('pickupSearch')?.addEventListener('input', function () {
            const value = this.value.trim().toLocaleLowerCase('id-ID');
            document.querySelectorAll('#pickupRows tr[data-search]').forEach(row => row.hidden = !row.dataset.search.includes(value));
        });

        document.querySelectorAll('.rm-complete-pickup-form').forEach(form => form.addEventListener('submit', function (event) {
            event.preventDefault();
            Swal.fire({ title: 'Konfirmasi pesanan diambil?', text: 'Status pesanan akan menjadi selesai.', icon: 'question', showCancelButton: true, confirmButtonText: 'Ya, sudah diambil', cancelButtonText: 'Batal', confirmButtonColor: '#1e5b5a' }).then(result => {
                if (result.isConfirmed) HTMLFormElement.prototype.submit.call(form);
            });
        }));
    </script>
@endpush
