@extends('layouts.workspace')

@section('title', 'Riwayat Donasi — ReuseMart')
@section('page-title', 'Riwayat Donasi')

@section('content')
    <div class="mb-4">
        <h1 class="rm-page-title">Riwayat Donasi</h1>
        <p class="rm-page-sub">Lihat organisasi penerima dan barang yang sudah disalurkan.</p>
    </div>

    <section class="rm-workspace-card">
        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3 mb-3">
            <div>
                <h2 class="h5 fw-bold mb-1">Organisasi Penerima</h2>
                <p class="small rm-text-muted mb-0">{{ $organisasi->count() }} organisasi memiliki catatan donasi.</p>
            </div>
            <div class="input-group rm-workspace-search">
                <span class="input-group-text bg-white border-end-0"><i class="bi bi-search"></i></span>
                <input type="search" id="organizationSearch" class="form-control border-start-0" placeholder="Cari organisasi…" aria-label="Cari organisasi">
            </div>
        </div>

        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>Organisasi</th>
                        <th>ID</th>
                        <th>Alamat</th>
                        <th class="text-end">Riwayat</th>
                    </tr>
                </thead>
                <tbody id="organizationRows">
                    @forelse ($organisasi as $org)
                        <tr data-search="{{ \Illuminate\Support\Str::lower($org->nama_organisasi . ' ' . $org->id_organisasi) }}">
                            <td><strong>{{ $org->nama_organisasi }}</strong></td>
                            <td><span class="rm-chip">{{ $org->id_organisasi }}</span></td>
                            <td>{{ $org->alamat_organisasi ?: '—' }}</td>
                            <td class="text-end">
                                <button type="button" class="btn rm-btn-outline btn-sm js-show-donations"
                                        data-org-id="{{ $org->id_organisasi }}" data-org-name="{{ $org->nama_organisasi }}">
                                    <i class="bi bi-eye me-1"></i>Lihat Donasi
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4"><div class="rm-empty py-4"><i class="bi bi-building d-block"></i><p class="mb-0">Belum ada organisasi penerima donasi.</p></div></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <div class="modal fade" id="donationHistoryModal" tabindex="-1" aria-labelledby="donationHistoryTitle" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content rm-workspace-modal">
                <div class="modal-header">
                    <div>
                        <h2 class="modal-title h5 fw-bold" id="donationHistoryTitle">Riwayat Donasi</h2>
                        <p class="small rm-text-muted mb-0" id="donationHistoryOrganization"></p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead><tr><th>Barang</th><th>ID Donasi</th><th>Tanggal</th><th>Penerima</th></tr></thead>
                            <tbody id="donationHistoryRows"><tr><td colspan="4" class="text-center rm-text-muted">Pilih organisasi untuk melihat donasi.</td></tr></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .rm-workspace-search { max-width: 320px; }
        .rm-workspace-search .input-group-text { border-radius: var(--rm-radius-pill) 0 0 var(--rm-radius-pill); }
        .rm-workspace-search .form-control { border-radius: 0 var(--rm-radius-pill) var(--rm-radius-pill) 0; }
        .rm-workspace-modal { border: 1px solid var(--rm-line); border-radius: var(--rm-radius-lg); overflow: hidden; }
        .rm-workspace-modal .modal-header { border-color: var(--rm-line); padding: 1rem 1.25rem; }
        @media (max-width: 575.98px) { .rm-workspace-search { max-width: none; } }
    </style>
@endpush

@push('scripts')
    <script>
        const historyModal = bootstrap.Modal.getOrCreateInstance(document.getElementById('donationHistoryModal'));
        const historyBody = document.getElementById('donationHistoryRows');

        function cell(row, value) {
            const td = document.createElement('td');
            td.textContent = value ?? '—';
            row.append(td);
        }

        document.querySelectorAll('.js-show-donations').forEach(button => {
            button.addEventListener('click', async function () {
                const orgId = this.dataset.orgId;
                document.getElementById('donationHistoryOrganization').textContent = this.dataset.orgName;
                historyBody.innerHTML = '<tr><td colspan="4" class="text-center"><span class="spinner-border spinner-border-sm" role="status"></span> Memuat riwayat…</td></tr>';
                historyModal.show();

                try {
                    const response = await fetch(`/api/donasi/by-organisasi/${encodeURIComponent(orgId)}`, {
                        headers: { 'Accept': 'application/json' },
                    });
                    const payload = await response.json();
                    if (!response.ok || !payload.success) throw new Error(payload.message || 'Riwayat donasi gagal dimuat.');

                    historyBody.replaceChildren();
                    if (!payload.data?.length) {
                        const tr = document.createElement('tr');
                        const td = document.createElement('td');
                        td.colSpan = 4;
                        td.className = 'text-center rm-text-muted py-4';
                        td.textContent = 'Belum ada donasi untuk organisasi ini.';
                        tr.append(td);
                        historyBody.append(tr);
                        return;
                    }

                    payload.data.forEach(item => {
                        const tr = document.createElement('tr');
                        cell(tr, item.nama_barang_request);
                        cell(tr, item.id_donasi);
                        cell(tr, item.tgl_donasi ? new Date(item.tgl_donasi).toLocaleDateString('id-ID') : '—');
                        cell(tr, item.nama_penerima);
                        historyBody.append(tr);
                    });
                } catch (error) {
                    historyBody.replaceChildren();
                    const tr = document.createElement('tr');
                    const td = document.createElement('td');
                    td.colSpan = 4;
                    td.className = 'text-center text-danger py-4';
                    td.textContent = error.message;
                    tr.append(td);
                    historyBody.append(tr);
                }
            });
        });

        document.getElementById('organizationSearch').addEventListener('input', function () {
            const term = this.value.trim().toLocaleLowerCase('id-ID');
            document.querySelectorAll('#organizationRows tr[data-search]').forEach(row => {
                row.hidden = !row.dataset.search.includes(term);
            });
        });
    </script>
@endpush
