@extends('layouts.partner')

@section('title', 'Barang Titipan Saya — ReuseMart')

@section('content')
    <section class="container py-4 py-lg-5">
        <div class="rm-consignor-hero mb-4">
            <div>
                <span class="rm-org-eyebrow">Dashboard Penitip</span>
                <h1 class="h2 fw-bold mb-2">Halo, {{ $penitip->nama_penitip }}</h1>
                <p class="mb-0">Pantau status barang titipan dan kelola masa penitipanmu.</p>
            </div>
            <span class="rm-consignor-avatar"><i class="bi bi-person-heart"></i></span>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-sm-6 col-lg-4"><article class="rm-consignor-stat"><span><i class="bi bi-box-seam"></i></span><div><small>Total barang titipan</small><strong>{{ $penitip->barang()->count() }}</strong></div></article></div>
            <div class="col-sm-6 col-lg-4"><article class="rm-consignor-stat"><span class="rating"><i class="bi bi-star-fill"></i></span><div><small>Rating penitip</small><strong>{{ is_null($penitip->rating) ? '—' : number_format($penitip->rating, 1) . '/5' }}</strong></div></article></div>
            <div class="col-sm-6 col-lg-4"><article class="rm-consignor-stat"><span class="points"><i class="bi bi-stars"></i></span><div><small>Poin tersedia</small><strong>{{ number_format($penitip->poin ?? 0, 0, ',', '.') }}</strong></div></article></div>
        </div>

        <section class="rm-partner-panel">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-end gap-3 mb-4">
                <div>
                    <h2 class="h4 fw-bold mb-1">Barang Titipan Saya</h2>
                    <p class="rm-page-sub">{{ $barang->count() }} barang ditampilkan</p>
                </div>
                <form method="GET" action="{{ route('penitip.barangTitipan') }}" class="rm-partner-search">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0"><i class="bi bi-search"></i></span>
                        <input class="form-control border-start-0" type="search" name="query" value="{{ request('query') }}" placeholder="Cari barang atau kategori…" aria-label="Cari barang titipan">
                        <button class="btn rm-btn-primary" type="submit">Cari</button>
                    </div>
                </form>
            </div>

            @if ($barang->isEmpty())
                <div class="rm-empty">
                    <i class="bi bi-box-seam d-block"></i>
                    <p class="mb-0">{{ request('query') ? 'Tidak ada barang yang cocok dengan pencarian.' : 'Kamu belum memiliki barang titipan.' }}</p>
                </div>
            @else
                <div class="row row-cols-2 row-cols-md-3 row-cols-xl-4 g-3 g-md-4">
                    @foreach ($barang as $item)
                        <div class="col">
                            <x-product-card :item="$item" detail-route="penitip.detail_barang">
                                <x-slot:action>
                                    <div class="d-flex align-items-center justify-content-between gap-2 mb-2">
                                        <span class="rm-item-status"><i class="bi bi-circle-fill"></i>{{ ucfirst($item->status) }}</span>
                                    </div>
                                    <a href="{{ route('penitip.detail_barang', ['id' => $item->id_barang]) }}" class="btn rm-btn-primary w-100">
                                        <i class="bi bi-gear me-1"></i>Kelola Barang
                                    </a>
                                </x-slot:action>
                            </x-product-card>
                        </div>
                    @endforeach
                </div>
            @endif
        </section>
    </section>
@endsection

@push('styles')
    <style>
        .rm-consignor-hero { display:flex; justify-content:space-between; align-items:center; gap:1.5rem; padding:clamp(1.4rem,3vw,2.2rem); border-radius:var(--rm-radius-lg); color:#fff; background:linear-gradient(125deg,var(--rm-primary-dark),var(--rm-primary),#2f7d7a); }
        .rm-consignor-hero h1,.rm-consignor-hero p { color:#fff; }
        .rm-consignor-hero p { color:rgba(255,255,255,.8); }
        .rm-org-eyebrow { display:block; margin-bottom:.45rem; color:rgba(255,255,255,.72); font-size:.72rem; font-weight:700; letter-spacing:.1em; text-transform:uppercase; }
        .rm-consignor-avatar { width:68px;height:68px;flex:0 0 68px;display:flex;align-items:center;justify-content:center;border:1px solid rgba(255,255,255,.25);border-radius:50%;background:rgba(255,255,255,.12);font-size:1.8rem; }
        .rm-consignor-stat { display:flex;align-items:center;gap:.8rem;height:100%;padding:1rem;border:1px solid var(--rm-line);border-radius:var(--rm-radius);background:#fff;box-shadow:var(--rm-shadow-xs); }
        .rm-consignor-stat > span { width:42px;height:42px;flex:0 0 42px;display:flex;align-items:center;justify-content:center;border-radius:50%;background:var(--rm-primary-soft);color:var(--rm-primary);font-size:1.15rem; }
        .rm-consignor-stat > span.rating { color:#aa7700;background:var(--rm-warning-soft); }
        .rm-consignor-stat > span.points { color:#835f14;background:#fff5df; }
        .rm-consignor-stat small,.rm-consignor-stat strong { display:block; }
        .rm-consignor-stat small { color:var(--rm-muted);font-size:.76rem; }
        .rm-consignor-stat strong { color:var(--rm-ink);font-size:1.15rem; }
        .rm-partner-panel { padding:clamp(1rem,2vw,1.5rem);border:1px solid var(--rm-line);border-radius:var(--rm-radius-lg);background:#fff;box-shadow:var(--rm-shadow-xs); }
        .rm-partner-search { width:min(100%,390px); }
        .rm-partner-search .input-group-text { border-radius:var(--rm-radius-pill) 0 0 var(--rm-radius-pill); }
        .rm-partner-search .form-control { border-radius:0; }
        .rm-partner-search button { border-radius:0 var(--rm-radius-pill) var(--rm-radius-pill) 0; }
        .rm-item-status { display:inline-flex;align-items:center;gap:.35rem;color:var(--rm-muted);font-size:.72rem; }
        .rm-item-status i { font-size:.46rem;color:var(--rm-primary); }
        @media(max-width:575.98px){.rm-consignor-avatar{display:none}.rm-partner-search{width:100%}}
    </style>
@endpush
