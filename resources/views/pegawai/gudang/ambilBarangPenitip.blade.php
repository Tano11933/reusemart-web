@extends('layouts.workspace')

@section('title', 'Pengambilan Titipan — ReuseMart')
@section('page-title', 'Pengambilan Titipan')

@section('content')
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-end gap-3 mb-4">
        <div><h1 class="rm-page-title">Pengambilan Barang Titipan</h1><p class="rm-page-sub">Catat barang yang diambil kembali atau dialihkan ke donasi.</p></div>
        <span class="rm-chip"><i class="bi bi-box-arrow-down"></i>{{ $barang->count() }} titipan menunggu</span>
    </div>

    <section class="rm-workspace-card">
        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3 mb-3">
            <div><h2 class="h5 fw-bold mb-1">Barang Menunggu Pengambilan</h2><p class="small rm-text-muted mb-0">Barang yang melewati masa penitipan lebih dari tujuh hari dialihkan untuk donasi.</p></div>
            <div class="input-group rm-retrieval-search"><span class="input-group-text bg-white border-end-0"><i class="bi bi-search"></i></span><input id="retrievalSearch" class="form-control border-start-0" type="search" placeholder="Cari barang atau penitip…" aria-label="Cari barang"></div>
        </div>

        @if ($barang->isEmpty())
            <div class="rm-empty py-5"><i class="bi bi-box-seam d-block"></i><h2 class="h5 fw-bold">Tidak ada titipan yang menunggu</h2><p class="mb-0">Barang yang perlu diambil akan muncul di sini.</p></div>
        @else
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead><tr><th>Barang</th><th>Penitip</th><th>Tanggal akhir</th><th>Status</th><th class="text-end">Tindakan</th></tr></thead>
                    <tbody id="retrievalRows">
                        @foreach ($barang as $item)
                            <tr data-search="{{ \Illuminate\Support\Str::lower($item->nama_barang . ' ' . ($item->penitip->nama_penitip ?? '') . ' ' . $item->id_barang) }}">
                                <td><strong>{{ $item->nama_barang }}</strong><div class="small rm-text-muted">{{ $item->id_barang }} · {{ $item->kategori }}</div></td>
                                <td>{{ $item->penitip->nama_penitip ?? 'Penitip' }}<div class="small rm-text-muted">{{ $item->id_penitip }}</div></td>
                                <td>{{ $item->tgl_akhir ? \Carbon\Carbon::parse($item->tgl_akhir)->translatedFormat('d M Y') : '—' }}</td>
                                <td><span class="rm-status-pill">{{ ucfirst($item->status) }}</span></td>
                                <td class="text-end"><div class="d-inline-flex flex-wrap justify-content-end gap-2">
                                    <form action="{{ route('gudang.barang-diambil', $item->id_barang) }}" method="POST" class="rm-retrieval-form">@csrf<input type="hidden" name="status_action" value="diambil"><button type="submit" class="btn rm-btn-primary btn-sm"><i class="bi bi-check2 me-1"></i>Sudah Diambil</button></form>
                                    <form action="{{ route('gudang.barang-tidak-diambil', $item->id_barang) }}" method="POST" class="rm-retrieval-form">@csrf<input type="hidden" name="status_action" value="donasi"><button type="submit" class="btn btn-outline-secondary btn-sm rounded-pill"><i class="bi bi-heart me-1"></i>Siapkan Donasi</button></form>
                                </div></td>
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
        .rm-retrieval-search{max-width:330px}.rm-retrieval-search .input-group-text{border-radius:var(--rm-radius-pill) 0 0 var(--rm-radius-pill)}.rm-retrieval-search .form-control{border-radius:0 var(--rm-radius-pill) var(--rm-radius-pill) 0}
        .rm-status-pill{display:inline-flex;padding:.32rem .65rem;border-radius:var(--rm-radius-pill);background:var(--rm-warning-soft);color:#76540f;font-size:.74rem;font-weight:700}
        @media(max-width:575.98px){.rm-retrieval-search{max-width:none}}
    </style>
@endpush

@push('scripts')
    <script>
        document.getElementById('retrievalSearch')?.addEventListener('input', function () {
            const query = this.value.trim().toLocaleLowerCase('id-ID');
            document.querySelectorAll('#retrievalRows tr[data-search]').forEach(row => row.hidden = !row.dataset.search.includes(query));
        });

        document.querySelectorAll('.rm-retrieval-form').forEach(form => form.addEventListener('submit', function (event) {
            event.preventDefault();
            const donation = form.querySelector('[name="status_action"]').value === 'donasi';
            Swal.fire({
                title: donation ? 'Siapkan barang untuk donasi?' : 'Konfirmasi pengambilan?',
                text: donation ? 'Status barang akan diubah menjadi siap donasi.' : 'Barang akan ditandai telah diambil kembali oleh penitip.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: donation ? 'Siapkan donasi' : 'Konfirmasi',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#1e5b5a',
            }).then(result => { if (result.isConfirmed) HTMLFormElement.prototype.submit.call(form); });
        }));
    </script>
@endpush
