@extends('layouts.workspace')

@section('title', 'Tukar Poin — ReuseMart')
@section('page-title', 'Penukaran Poin')

@section('content')
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-end gap-3 mb-4">
        <div><h1 class="rm-page-title">Penukaran Poin</h1><p class="rm-page-sub">Atur jadwal pengambilan dan konfirmasi merchandise yang sudah diterima.</p></div>
        <div class="rm-workspace-filters" role="group" aria-label="Filter status penukaran">
            <a href="?filter=semua" class="{{ $filter === 'semua' ? 'active' : '' }}">Semua <span>{{ $allCount }}</span></a>
            <a href="?filter=belum diambil" class="{{ $filter === 'belum diambil' ? 'active' : '' }}">Belum diambil <span>{{ $waitingCount }}</span></a>
            <a href="?filter=sudah diambil" class="{{ $filter === 'sudah diambil' ? 'active' : '' }}">Sudah diambil <span>{{ $collectedCount }}</span></a>
        </div>
    </div>

    <section class="rm-workspace-card">
        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2 mb-3">
            <div><h2 class="h5 fw-bold mb-1">Daftar Klaim Merchandise</h2><p class="small rm-text-muted mb-0">{{ $tukarPoin->count() }} transaksi pada filter ini.</p></div>
            <div class="input-group rm-redemption-search"><span class="input-group-text bg-white border-end-0"><i class="bi bi-search"></i></span><input id="redemptionSearch" class="form-control border-start-0" type="search" placeholder="Cari pembeli atau hadiah…" aria-label="Cari penukaran"></div>
        </div>

        <div class="table-responsive">
            <table class="table align-middle">
                <thead><tr><th>Penukaran</th><th>Pembeli</th><th>Hadiah</th><th>Tanggal</th><th>Pengambilan</th><th>Status</th><th class="text-end">Aksi</th></tr></thead>
                <tbody id="redemptionRows">
                    @forelse ($tukarPoin as $item)
                        <tr data-search="{{ \Illuminate\Support\Str::lower($item->id_tukar_poin . ' ' . $item->nama_pembeli . ' ' . $item->nama_merch) }}">
                            <td><strong>{{ $item->id_tukar_poin }}</strong></td>
                            <td>{{ $item->nama_pembeli }}</td>
                            <td>{{ $item->nama_merch }}<div class="small rm-text-muted">Jumlah {{ $item->jml }}</div></td>
                            <td>{{ $item->tgl_tukar ? \Carbon\Carbon::parse($item->tgl_tukar)->translatedFormat('d M Y') : '—' }}</td>
                            <td>{{ $item->tgl_ambil ? \Carbon\Carbon::parse($item->tgl_ambil)->translatedFormat('d M Y') : 'Belum dijadwalkan' }}</td>
                            <td><span class="rm-redemption-status {{ $item->status === 'sudah diambil' ? 'is-done' : 'is-waiting' }}">{{ ucfirst($item->status) }}</span></td>
                            <td class="text-end">
                                @if ($item->status === 'belum diambil' && !$item->tgl_ambil)
                                    <button type="button" class="btn rm-btn-outline btn-sm js-set-pickup" data-id="{{ $item->id_tukar_poin }}" data-buyer="{{ $item->nama_pembeli }}" data-item="{{ $item->nama_merch }}"><i class="bi bi-calendar-plus me-1"></i>Atur Tanggal</button>
                                @elseif ($item->status === 'belum diambil' && $item->tgl_ambil)
                                    <button type="button" class="btn rm-btn-primary btn-sm js-confirm-pickup" data-id="{{ $item->id_tukar_poin }}" data-buyer="{{ $item->nama_pembeli }}" data-item="{{ $item->nama_merch }}"><i class="bi bi-check2-circle me-1"></i>Konfirmasi</button>
                                @else
                                    <span class="small rm-text-muted">Selesai</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7"><div class="rm-empty py-4"><i class="bi bi-stars d-block"></i><p class="mb-0">Tidak ada data penukaran untuk filter ini.</p></div></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <div class="modal fade" id="pickupDateModal" tabindex="-1" aria-labelledby="pickupDateTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered"><form id="pickupDateForm" class="modal-content rm-workspace-modal">
            <div class="modal-header"><div><h2 class="modal-title h5 fw-bold" id="pickupDateTitle">Atur Tanggal Pengambilan</h2><p class="small rm-text-muted mb-0" id="pickupDateSubtitle"></p></div><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button></div>
            <div class="modal-body"><input type="hidden" id="pickupDateId"><label for="pickupDateInput" class="form-label fw-semibold">Tanggal pengambilan</label><input type="date" id="pickupDateInput" class="form-control" required></div>
            <div class="modal-footer"><button type="button" class="btn rm-btn-outline" data-bs-dismiss="modal">Batal</button><button type="submit" class="btn rm-btn-primary">Simpan Tanggal</button></div>
        </form></div>
    </div>
@endsection

@push('styles')
    <style>
        .rm-workspace-filters { display:flex;flex-wrap:wrap;gap:.35rem;padding:.25rem;border:1px solid var(--rm-line);border-radius:var(--rm-radius-pill);background:#fff; }
        .rm-workspace-filters a { display:inline-flex;align-items:center;gap:.45rem;padding:.42rem .75rem;border-radius:var(--rm-radius-pill);color:var(--rm-ink-soft);font-size:.75rem;font-weight:600;white-space:nowrap; }
        .rm-workspace-filters a span { display:inline-flex;align-items:center;justify-content:center;min-width:20px;height:20px;padding:0 .3rem;border-radius:var(--rm-radius-pill);background:var(--rm-bg);font-size:.67rem; }
        .rm-workspace-filters a.active { background:var(--rm-primary);color:#fff; }
        .rm-workspace-filters a.active span { background:rgba(255,255,255,.18); }
        .rm-redemption-search { max-width:300px; }
        .rm-redemption-search .input-group-text { border-radius:var(--rm-radius-pill) 0 0 var(--rm-radius-pill); }
        .rm-redemption-search .form-control { border-radius:0 var(--rm-radius-pill) var(--rm-radius-pill) 0; }
        .rm-redemption-status { display:inline-flex;padding:.32rem .65rem;border-radius:var(--rm-radius-pill);font-size:.73rem;font-weight:700;white-space:nowrap; }
        .rm-redemption-status.is-waiting { background:var(--rm-warning-soft);color:#76540f; }
        .rm-redemption-status.is-done { background:var(--rm-success-soft);color:#176246; }
        .rm-workspace-modal { border:1px solid var(--rm-line);border-radius:var(--rm-radius-lg);overflow:hidden; }
        .rm-workspace-modal .modal-header,.rm-workspace-modal .modal-footer { border-color:var(--rm-line);padding:1rem 1.25rem; }
        .rm-workspace-modal .modal-body { padding:1.25rem; }
        @media(max-width:575.98px){.rm-redemption-search{max-width:none}.rm-workspace-filters{border-radius:var(--rm-radius);justify-content:center}}
    </style>
@endpush

@push('scripts')
    <script>
        const pickupDateModal = bootstrap.Modal.getOrCreateInstance(document.getElementById('pickupDateModal'));
        const csrf = @json(csrf_token());

        document.querySelectorAll('.js-set-pickup').forEach(button => button.addEventListener('click', function () {
            document.getElementById('pickupDateId').value = this.dataset.id;
            document.getElementById('pickupDateSubtitle').textContent = `${this.dataset.item} · ${this.dataset.buyer}`;
            const today = new Date();
            const dateValue = new Date(today.getTime() - today.getTimezoneOffset() * 60000).toISOString().slice(0, 10);
            const input = document.getElementById('pickupDateInput');
            input.value = dateValue;
            input.min = dateValue;
            pickupDateModal.show();
        }));

        document.getElementById('pickupDateForm').addEventListener('submit', async function (event) {
            event.preventDefault();
            const id = document.getElementById('pickupDateId').value;
            const date = document.getElementById('pickupDateInput').value;
            try {
                const response = await fetch(`/tukar-poin/update-tanggal-ambil/${encodeURIComponent(id)}`, {
                    method: 'POST', headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf }, body: JSON.stringify({ tgl_ambil: date }),
                });
                const result = await response.json();
                if (!response.ok || !result.success) throw new Error(result.message || 'Tanggal gagal disimpan.');
                location.reload();
            } catch (error) { Swal.fire({ icon: 'error', title: 'Tidak dapat menyimpan', text: error.message }); }
        });

        document.querySelectorAll('.js-confirm-pickup').forEach(button => button.addEventListener('click', async function () {
            const id = this.dataset.id;
            const confirmation = await Swal.fire({ title: 'Konfirmasi pengambilan?', text: `${this.dataset.item} · ${this.dataset.buyer}`, icon: 'question', showCancelButton: true, confirmButtonText: 'Ya, sudah diambil', cancelButtonText: 'Batal', confirmButtonColor: '#1e5b5a' });
            if (!confirmation.isConfirmed) return;

            try {
                const response = await fetch(`/tukar-poin/konfirmasi-ambil/${encodeURIComponent(id)}`, {
                    method: 'POST', headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf },
                });
                const result = await response.json();
                if (!response.ok || !result.success) throw new Error(result.message || 'Konfirmasi gagal.');
                Swal.fire({ icon: 'success', title: 'Pengambilan dicatat', text: result.message }).then(() => location.reload());
            } catch (error) { Swal.fire({ icon: 'error', title: 'Tidak dapat mengonfirmasi', text: error.message }); }
        }));

        document.getElementById('redemptionSearch').addEventListener('input', function () {
            const query = this.value.trim().toLocaleLowerCase('id-ID');
            document.querySelectorAll('#redemptionRows tr[data-search]').forEach(row => row.hidden = !row.dataset.search.includes(query));
        });
    </script>
@endpush
