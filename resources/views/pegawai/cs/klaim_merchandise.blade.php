@extends('layouts.workspace')

@section('title', 'Klaim Merchandise · ReuseMart')
@section('page-title', 'Klaim Merchandise')

@section('content')
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-end gap-3 mb-4">
        <div><h1 class="rm-page-title">Klaim Merchandise</h1><p class="rm-page-sub">Jadwalkan dan catat pengambilan hadiah yang ditukar pembeli.</p></div>
        <span class="rm-chip"><i class="bi bi-gift"></i>{{ $klaim->count() }} klaim ditampilkan</span>
    </div>

    <section class="rm-workspace-card">
        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3 mb-3">
            <div><h2 class="h5 fw-bold mb-1">Daftar Klaim</h2><p class="small rm-text-muted mb-0">Pilih tanggal pengambilan untuk menyelesaikan klaim.</p></div>
            <div class="d-flex flex-column flex-sm-row gap-2">
                <div class="rm-claim-filters">
                    <a href="{{ route('cs.klaimMerchandise') }}" class="{{ request('filter', 'semua') === 'semua' ? 'active' : '' }}">Semua</a>
                    <a href="{{ route('cs.klaimMerchandise', ['filter' => 'belum']) }}" class="{{ request('filter') === 'belum' ? 'active' : '' }}">Belum diambil</a>
                </div>
                <div class="input-group rm-claim-search"><span class="input-group-text bg-white border-end-0"><i class="bi bi-search"></i></span><input id="claimSearch" class="form-control border-start-0" type="search" placeholder="Cari pembeli atau hadiah…" aria-label="Cari klaim"></div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table align-middle">
                <thead><tr><th>Klaim</th><th>Pembeli</th><th>Hadiah</th><th>Tanggal klaim</th><th>Tanggal ambil</th><th>Status</th><th class="text-end">Aksi</th></tr></thead>
                <tbody id="claimRows">
                    @forelse ($klaim as $item)
                        <tr data-search="{{ \Illuminate\Support\Str::lower($item->id_klaim . ' ' . $item->nama_pembeli . ' ' . $item->nama_merch) }}">
                            <td><strong>#{{ $item->id_klaim }}</strong></td>
                            <td>{{ $item->nama_pembeli }}</td>
                            <td>{{ $item->nama_merch }}</td>
                            <td>{{ $item->tanggal_klaim ? \Carbon\Carbon::parse($item->tanggal_klaim)->translatedFormat('d M Y') : '-' }}</td>
                            <td>{{ $item->tanggal_ambil ? \Carbon\Carbon::parse($item->tanggal_ambil)->translatedFormat('d M Y') : 'Belum dijadwalkan' }}</td>
                            <td><span class="rm-claim-status {{ $item->status_ambil === 'sudah' ? 'is-done' : 'is-waiting' }}">{{ $item->status_ambil === 'sudah' ? 'Sudah diambil' : 'Belum diambil' }}</span></td>
                            <td class="text-end">
                                @if ($item->status_ambil !== 'sudah')
                                    <button type="button" class="btn rm-btn-primary btn-sm js-schedule-claim" data-id="{{ $item->id_klaim }}" data-name="{{ $item->nama_merch }}" data-buyer="{{ $item->nama_pembeli }}"><i class="bi bi-calendar-check me-1"></i>Catat Ambil</button>
                                @else
                                    <span class="small rm-text-muted">Selesai</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7"><div class="rm-empty py-4"><i class="bi bi-gift d-block"></i><p class="mb-2">Tidak ada klaim merchandise pada filter ini.</p>@if (request('filter') === 'belum')<a href="{{ route('cs.klaimMerchandise') }}" class="btn rm-btn-outline">Lihat semua klaim</a>@endif</div></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <div class="modal fade" id="claimDateModal" tabindex="-1" aria-labelledby="claimDateTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered"><form id="claimDateForm" class="modal-content rm-claim-modal">
            <div class="modal-header"><div><h2 class="modal-title h5 fw-bold" id="claimDateTitle">Catat Pengambilan</h2><p class="small rm-text-muted mb-0" id="claimDateSubtitle"></p></div><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button></div>
            <div class="modal-body"><input type="hidden" id="claimId"><label for="claimDate" class="form-label fw-semibold">Tanggal pengambilan</label><input type="date" id="claimDate" class="form-control" required></div>
            <div class="modal-footer"><button type="button" class="btn rm-btn-outline" data-bs-dismiss="modal">Batal</button><button type="submit" class="btn rm-btn-primary">Simpan</button></div>
        </form></div>
    </div>
@endsection

@push('styles')
    <style>
        .rm-claim-filters{display:flex;gap:.25rem;padding:.25rem;border:1px solid var(--rm-line);border-radius:var(--rm-radius-pill);background:#fff}
        .rm-claim-filters a{padding:.4rem .7rem;border-radius:var(--rm-radius-pill);font-size:.73rem;font-weight:600;color:var(--rm-ink-soft);white-space:nowrap}
        .rm-claim-filters a.active,.rm-claim-filters a:hover{background:var(--rm-primary);color:#fff}
        .rm-claim-search{max-width:280px}.rm-claim-search .input-group-text{border-radius:var(--rm-radius-pill) 0 0 var(--rm-radius-pill)}.rm-claim-search .form-control{border-radius:0 var(--rm-radius-pill) var(--rm-radius-pill) 0}
        .rm-claim-status{display:inline-flex;padding:.32rem .65rem;border-radius:var(--rm-radius-pill);font-size:.73rem;font-weight:700;white-space:nowrap}.rm-claim-status.is-waiting{background:var(--rm-warning-soft);color:#76540f}.rm-claim-status.is-done{background:var(--rm-success-soft);color:var(--rm-success)}
        .rm-claim-modal{border:1px solid var(--rm-line);border-radius:var(--rm-radius-lg);overflow:hidden}.rm-claim-modal .modal-header,.rm-claim-modal .modal-footer{border-color:var(--rm-line);padding:1rem 1.25rem}.rm-claim-modal .modal-body{padding:1.25rem}
        @media(max-width:575.98px){.rm-claim-search{max-width:none}.rm-claim-filters{justify-content:center}}
    </style>
@endpush

@push('scripts')
    <script>
        const claimModal = bootstrap.Modal.getOrCreateInstance(document.getElementById('claimDateModal'));
        const claimForm = document.getElementById('claimDateForm');

        document.querySelectorAll('.js-schedule-claim').forEach(button => button.addEventListener('click', function () {
            document.getElementById('claimId').value = this.dataset.id;
            document.getElementById('claimDateSubtitle').textContent = `${this.dataset.name} · ${this.dataset.buyer}`;
            const now = new Date();
            const today = new Date(now.getTime() - now.getTimezoneOffset() * 60000).toISOString().slice(0, 10);
            document.getElementById('claimDate').value = today;
            document.getElementById('claimDate').min = today;
            claimModal.show();
        }));

        claimForm.addEventListener('submit', async function (event) {
            event.preventDefault();
            const id = document.getElementById('claimId').value;
            try {
                const response = await fetch(`/klaim-merchandise/update-tanggal-ambil/${encodeURIComponent(id)}`, {
                    method: 'POST',
                    headers: { 'Accept': 'application/json', 'Content-Type': 'application/json', 'X-CSRF-TOKEN': @json(csrf_token()) },
                    body: JSON.stringify({ tanggal_ambil: document.getElementById('claimDate').value }),
                });
                const result = await response.json();
                if (!response.ok || !result.success) throw new Error(result.message || 'Tanggal pengambilan gagal disimpan.');
                location.reload();
            } catch (error) { Swal.fire({ icon: 'error', title: 'Tidak dapat menyimpan', text: error.message }); }
        });

        document.getElementById('claimSearch').addEventListener('input', function () {
            const query = this.value.trim().toLocaleLowerCase('id-ID');
            document.querySelectorAll('#claimRows tr[data-search]').forEach(row => row.hidden = !row.dataset.search.includes(query));
        });
    </script>
@endpush
