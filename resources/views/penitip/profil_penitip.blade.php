@extends('layouts.partner')

@section('title', 'Profil Penitip · ReuseMart')

@section('content')
    <section class="container py-4 py-lg-5">
        <nav class="rm-breadcrumb small mb-3" aria-label="Breadcrumb">
            <a href="{{ route('penitip.barangTitipan') }}">Barang Titipan</a>
            <span class="mx-1">/</span><span class="rm-text-muted">Profil</span>
        </nav>

        <div class="mb-4"><h1 class="rm-page-title">Profil Penitip</h1><p class="rm-page-sub">Informasi akun dan ringkasan saldo penitipan.</p></div>

        <div class="row g-4">
            <div class="col-lg-4">
                <section class="rm-consignor-profile-card">
                    <span class="rm-profile-avatar"><i class="bi bi-person-fill"></i></span>
                    <h2 class="h4 fw-bold mt-3 mb-1">{{ $penitip->nama_penitip }}</h2>
                    <p class="rm-text-muted mb-3">{{ $penitip->email }}</p>
                    <div class="rm-consignor-rating">
                        <i class="bi bi-star-fill"></i>
                        <strong>{{ is_null($penitip->rating) ? '-' : number_format($penitip->rating, 1) }}</strong>
                        <span>/ 5 rating</span>
                    </div>
                    <a href="{{ route('penitip.barangTitipan') }}" class="btn rm-btn-primary w-100 mt-3"><i class="bi bi-box-seam me-1"></i>Lihat Barang Titipan</a>
                </section>
            </div>

            <div class="col-lg-8">
                <section class="rm-partner-panel">
                    <div class="d-flex justify-content-between align-items-start gap-3 mb-4">
                        <div><h2 class="h5 fw-bold mb-1">Informasi Akun</h2><p class="small rm-text-muted mb-0">Data yang tersimpan pada profil penitip.</p></div>
                        <span class="badge rounded-pill text-bg-success"><i class="bi bi-patch-check-fill me-1"></i>Penitip</span>
                    </div>

                    <dl class="rm-partner-details">
                        <div><dt>ID Penitip</dt><dd>{{ $penitip->id_penitip }}</dd></div>
                        <div><dt>Nama lengkap</dt><dd>{{ $penitip->nama_penitip }}</dd></div>
                        <div><dt>Email</dt><dd>{{ $penitip->email }}</dd></div>
                        <div><dt>Nomor telepon</dt><dd>{{ $penitip->nomor_telepon ?: 'Belum diisi' }}</dd></div>
                        <div><dt>NIK</dt><dd>{{ $penitip->nik_penitip ?: 'Belum diisi' }}</dd></div>
                        <div><dt>Jenis kelamin</dt><dd>{{ $penitip->jenis_kelamin ?: 'Belum diisi' }}</dd></div>
                        <div><dt>Tanggal lahir</dt><dd>{{ $penitip->tgl_lahir ? \Carbon\Carbon::parse($penitip->tgl_lahir)->translatedFormat('d F Y') : 'Belum diisi' }}</dd></div>
                    </dl>
                </section>

                <div class="row g-3 mt-1">
                    <div class="col-sm-6"><section class="rm-balance-card"><span><i class="bi bi-wallet2"></i></span><div><small>Saldo penitipan</small><strong>Rp {{ number_format($penitip->saldo ?? 0, 0, ',', '.') }}</strong></div></section></div>
                    <div class="col-sm-6"><section class="rm-balance-card points"><span><i class="bi bi-coin"></i></span><div><small>Poin</small><strong>{{ number_format($penitip->poin ?? 0, 0, ',', '.') }} poin</strong></div></section></div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('styles')
    <style>
        .rm-consignor-profile-card,.rm-partner-panel { border:1px solid var(--rm-line);border-radius:var(--rm-radius-lg);background:#fff;box-shadow:var(--rm-shadow-xs);padding:1.5rem; }
        .rm-consignor-profile-card { text-align:center;padding:2rem 1.5rem; }
        .rm-profile-avatar { width:84px;height:84px;display:inline-flex;align-items:center;justify-content:center;border:4px solid #fff;border-radius:50%;background:var(--rm-primary-soft);color:var(--rm-primary);box-shadow:0 0 0 1px var(--rm-line),var(--rm-shadow-sm);font-size:2.4rem; }
        .rm-consignor-rating { display:inline-flex;align-items:center;gap:.35rem;padding:.4rem .8rem;border:1px solid #f2dfb4;border-radius:var(--rm-radius-pill);background:var(--rm-warning-soft);color:#835f14;font-size:.85rem; }
        .rm-consignor-rating i { color:var(--rm-gold); }
        .rm-consignor-rating span { color:var(--rm-muted);font-size:.75rem; }
        .rm-partner-details { display:grid;grid-template-columns:repeat(2,minmax(0,1fr));margin:0;border-top:1px solid var(--rm-line); }
        .rm-partner-details>div { padding:.85rem .65rem;border-bottom:1px solid var(--rm-line); }
        .rm-partner-details dt { color:var(--rm-muted);font-size:.76rem;margin-bottom:.2rem; }
        .rm-partner-details dd { color:var(--rm-ink);font-weight:600;margin:0;overflow-wrap:anywhere; }
        .rm-balance-card { display:flex;align-items:center;gap:.8rem;height:100%;padding:1rem;border:1px solid var(--rm-line);border-radius:var(--rm-radius);background:#fff; }
        .rm-balance-card>span { width:42px;height:42px;display:flex;align-items:center;justify-content:center;border-radius:50%;background:var(--rm-primary-soft);color:var(--rm-primary);font-size:1.15rem; }
        .rm-balance-card.points>span { background:var(--rm-warning-soft);color:#835f14; }
        .rm-balance-card small,.rm-balance-card strong { display:block; }
        .rm-balance-card small { color:var(--rm-muted);font-size:.75rem; }
        .rm-balance-card strong { color:var(--rm-ink);font-size:1rem; }
        @media(max-width:575.98px){.rm-partner-details{grid-template-columns:1fr}.rm-partner-details>div{padding-left:0;padding-right:0}}
    </style>
@endpush
