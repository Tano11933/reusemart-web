@extends('layouts.partner')

@section('title', ($barang->nama_barang ?? 'Detail Barang') . ' — ReuseMart')

@section('content')
    @php
        $photos = [];
        foreach ([$barang->foto_barang ?? null, $barang->foto_barang2 ?? null] as $photo) {
            $photo = trim((string) $photo);
            if ($photo && file_exists(public_path('image/' . ltrim($photo, '/')))) $photos[] = asset('image/' . ltrim($photo, '/'));
        }
        if (!$photos) $photos[] = asset('image/placeholder.svg');
        $endDate = $barang->tgl_akhir ? \Carbon\Carbon::parse($barang->tgl_akhir) : null;
        $daysRemaining = $endDate ? now()->startOfDay()->diffInDays($endDate->copy()->startOfDay(), false) : null;
    @endphp

    <section class="container py-4 py-lg-5">
        <nav class="rm-breadcrumb small mb-3" aria-label="Breadcrumb"><a href="{{ route('penitip.barangTitipan') }}">Barang Titipan</a><span class="mx-1">/</span><span class="rm-text-muted">{{ $barang->nama_barang }}</span></nav>

        <div class="rm-partner-panel">
            <div class="row g-4">
                <div class="col-lg-6">
                    <div class="rm-consignment-photo">
                        <img id="consignmentPhoto" src="{{ $photos[0] }}" alt="{{ $barang->nama_barang }}" onerror="this.onerror=null;this.src='{{ asset('image/placeholder.svg') }}'">
                        <button type="button" class="rm-photo-zoom" id="zoomConsignment" aria-label="Perbesar foto"><i class="bi bi-arrows-angle-expand"></i></button>
                    </div>
                    @if (count($photos) > 1)
                        <div class="rm-consignment-thumbs mt-2">
                            @foreach ($photos as $index => $photo)
                                <button type="button" class="rm-consignment-thumb {{ $index === 0 ? 'active' : '' }}" data-photo="{{ $photo }}" aria-label="Tampilkan foto {{ $index + 1 }}"><img src="{{ $photo }}" alt=""></button>
                            @endforeach
                        </div>
                    @endif
                </div>

                <div class="col-lg-6">
                    <div class="d-flex flex-wrap gap-2 mb-2">
                        <span class="rm-chip"><i class="bi bi-tag"></i>{{ $barang->kategori }}</span>
                        <span class="rm-consignment-status"><i class="bi bi-circle-fill"></i>{{ ucfirst($barang->status) }}</span>
                    </div>
                    <h1 class="h2 fw-bold mb-2">{{ $barang->nama_barang }}</h1>
                    <p class="rm-text-muted mb-3">ID Barang: {{ $barang->id_barang }}</p>

                    <div class="rm-price-box mb-3">
                        <div class="small rm-text-muted">Harga jual</div>
                        <div class="rm-price">Rp {{ number_format($barang->harga_barang, 0, ',', '.') }}</div>
                    </div>

                    <dl class="row rm-detail-list small">
                        <dt class="col-5">Dititipkan</dt>
                        <dd class="col-7">{{ $barang->tgl_titip ? \Carbon\Carbon::parse($barang->tgl_titip)->translatedFormat('d F Y') : '—' }}</dd>
                        <dt class="col-5">Masa penitipan</dt>
                        <dd class="col-7">{{ $endDate?->translatedFormat('d F Y') ?? '—' }}</dd>
                        <dt class="col-5">Garansi</dt>
                        <dd class="col-7">{{ $barang->garansi ? 'Aktif' : 'Tidak ada' }}{{ $barang->garansi && $barang->tgl_garansi ? ' · sampai ' . \Carbon\Carbon::parse($barang->tgl_garansi)->translatedFormat('d M Y') : '' }}</dd>
                        <dt class="col-5">Perpanjangan</dt>
                        <dd class="col-7">{{ $barang->perpanjangan ? 'Sudah diperpanjang' : 'Belum diperpanjang' }}</dd>
                    </dl>

                    <div class="rm-consignment-action mt-3">
                        @if ($barang->status === 'barang untuk donasi')
                            <div class="alert alert-warning mb-0"><i class="bi bi-info-circle me-1"></i>Barang telah masuk daftar donasi dan tidak dapat dijadwalkan untuk diambil.</div>
                        @elseif (!$barang->perpanjangan && !is_null($daysRemaining) && $daysRemaining <= 3 && $daysRemaining >= 0)
                            <form action="{{ route('barang.perpanjangan', $barang->id_barang) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn rm-btn-primary"><i class="bi bi-calendar-plus me-1"></i>Perpanjang 30 Hari</button>
                            </form>
                        @elseif (!is_null($daysRemaining) && $daysRemaining < 0 && !$barang->tgl_ambil)
                            <button type="button" class="btn rm-btn-outline" data-bs-toggle="modal" data-bs-target="#ambilModal"><i class="bi bi-box-arrow-down me-1"></i>Jadwalkan Pengambilan</button>
                        @elseif ($barang->tgl_ambil)
                            <span class="rm-chip"><i class="bi bi-calendar-check"></i>Pengambilan {{ \Carbon\Carbon::parse($barang->tgl_ambil)->translatedFormat('d M Y') }}</span>
                        @else
                            <span class="small rm-text-muted"><i class="bi bi-info-circle me-1"></i>Perpanjangan tersedia saat masa penitipan tersisa tiga hari.</span>
                        @endif
                    </div>
                </div>
            </div>

            <hr class="my-4">
            <div class="row g-4">
                <div class="col-lg-8"><h2 class="h5 fw-bold mb-2">Deskripsi</h2><p class="mb-0" style="white-space:pre-line">{{ $barang->deskripsi_barang ?: 'Belum ada deskripsi untuk barang ini.' }}</p></div>
                <div class="col-lg-4"><div class="rm-surface-card p-3 h-100"><div class="small rm-text-muted">Status penitipan</div><strong>{{ ucfirst($barang->status) }}</strong>@if(!is_null($daysRemaining))<p class="small rm-text-muted mb-0 mt-1">{{ $daysRemaining >= 0 ? $daysRemaining . ' hari tersisa' : abs($daysRemaining) . ' hari melewati masa penitipan' }}</p>@endif</div></div>
            </div>
        </div>

        <div class="d-flex flex-wrap gap-2 mt-3">
            <a href="{{ route('penitip.barangTitipan') }}" class="btn rm-btn-outline"><i class="bi bi-arrow-left me-1"></i>Kembali ke Barang Titipan</a>
            <a href="{{ route('penitip.history-penjualan') }}" class="btn rm-btn-soft"><i class="bi bi-graph-up me-1"></i>Riwayat Penjualan</a>
        </div>
    </section>

    <div class="modal fade" id="ambilModal" tabindex="-1" aria-labelledby="ambilModalTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form action="{{ route('barang.ambil.otomatis', $barang->id_barang) }}" method="POST" class="modal-content rm-partner-modal">
                @csrf
                <div class="modal-header"><h2 class="modal-title h5 fw-bold" id="ambilModalTitle">Jadwalkan Pengambilan</h2><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button></div>
                <div class="modal-body"><p class="mb-0">Barang dijadwalkan untuk diambil setelah masa penitipan berakhir pada <strong>{{ $endDate?->copy()->addDays(7)->translatedFormat('d F Y') ?? 'tanggal yang ditentukan' }}</strong>.</p></div>
                <div class="modal-footer"><button type="button" class="btn rm-btn-outline" data-bs-dismiss="modal">Batal</button><button type="submit" class="btn rm-btn-primary">Konfirmasi</button></div>
            </form>
        </div>
    </div>

    <div class="modal fade" id="consignmentImageModal" tabindex="-1" aria-hidden="true"><div class="modal-dialog modal-dialog-centered modal-xl"><div class="modal-content bg-transparent border-0"><div class="modal-body p-0 text-center"><img id="consignmentModalImage" class="img-fluid rounded-4" alt="Foto barang diperbesar"></div></div></div></div>
@endsection

@push('styles')
    <style>
        .rm-consignment-photo { position:relative;display:flex;align-items:center;justify-content:center;aspect-ratio:4/3;overflow:hidden;border-radius:var(--rm-radius);background:var(--rm-primary-soft-2); }
        .rm-consignment-photo>img { width:100%;height:100%;object-fit:contain;padding:1rem; }
        .rm-photo-zoom { position:absolute;right:.7rem;bottom:.7rem;width:40px;height:40px;border:1px solid var(--rm-line);border-radius:50%;background:rgba(255,255,255,.94);color:var(--rm-primary); }
        .rm-consignment-thumbs { display:flex;justify-content:center;gap:.55rem; }
        .rm-consignment-thumb { width:56px;height:56px;padding:.2rem;border:2px solid transparent;border-radius:.7rem;background:var(--rm-primary-soft-2);overflow:hidden; }
        .rm-consignment-thumb.active,.rm-consignment-thumb:hover { border-color:var(--rm-primary); }
        .rm-consignment-thumb img { width:100%;height:100%;object-fit:contain; }
        .rm-consignment-status { display:inline-flex;align-items:center;gap:.35rem;padding:.35rem .7rem;border:1px solid var(--rm-line);border-radius:var(--rm-radius-pill);background:var(--rm-bg-soft);color:var(--rm-ink-soft);font-size:.76rem;font-weight:600; }
        .rm-consignment-status i { color:var(--rm-primary);font-size:.45rem; }
        .rm-partner-modal { border:1px solid var(--rm-line);border-radius:var(--rm-radius-lg);overflow:hidden; }
        .rm-partner-modal .modal-header,.rm-partner-modal .modal-footer { border-color:var(--rm-line);padding:1rem 1.25rem; }
        .rm-partner-modal .modal-body { padding:1.25rem; }
        #consignmentModalImage { max-height:88vh;object-fit:contain; }
    </style>
@endpush

@push('scripts')
    <script>
        document.querySelectorAll('.rm-consignment-thumb').forEach(button => button.addEventListener('click', function () {
            document.getElementById('consignmentPhoto').src = this.dataset.photo;
            document.querySelectorAll('.rm-consignment-thumb').forEach(thumb => thumb.classList.remove('active'));
            this.classList.add('active');
        }));
        document.getElementById('zoomConsignment').addEventListener('click', function () {
            document.getElementById('consignmentModalImage').src = document.getElementById('consignmentPhoto').src;
            bootstrap.Modal.getOrCreateInstance(document.getElementById('consignmentImageModal')).show();
        });
    </script>
@endpush
