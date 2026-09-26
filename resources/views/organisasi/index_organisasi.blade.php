@extends('layouts.partner')

@section('title', 'Request Donasi — ReuseMart')

@section('content')
    @php
        $waitingRequests = $requests->where('status_req', '!=', 'dapat donasi')->count();
        $fulfilledRequests = $requests->where('status_req', 'dapat donasi')->count();
    @endphp

    <section class="container py-4 py-lg-5">
        <div class="rm-organization-hero mb-4">
            <div>
                <span class="rm-org-eyebrow">Dashboard Organisasi</span>
                <h1 class="h2 fw-bold mb-2">Halo, {{ $organisasi->nama_organisasi }}</h1>
                <p class="mb-0">Kelola kebutuhan donasi organisasi dan pantau status request-mu.</p>
            </div>
            <span class="rm-org-hero-icon"><i class="bi bi-heart-fill"></i></span>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-sm-6 col-lg-4">
                <article class="rm-org-stat"><span class="rm-org-stat-icon"><i class="bi bi-inbox"></i></span><div><small>Total request</small><strong>{{ $requests->count() }}</strong></div></article>
            </div>
            <div class="col-sm-6 col-lg-4">
                <article class="rm-org-stat"><span class="rm-org-stat-icon rm-org-stat-wait"><i class="bi bi-hourglass-split"></i></span><div><small>Menunggu donasi</small><strong>{{ $waitingRequests }}</strong></div></article>
            </div>
            <div class="col-sm-6 col-lg-4">
                <article class="rm-org-stat"><span class="rm-org-stat-icon rm-org-stat-done"><i class="bi bi-check2-circle"></i></span><div><small>Terpenuhi</small><strong>{{ $fulfilledRequests }}</strong></div></article>
            </div>
        </div>

        <section class="rm-org-panel">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-3">
                <div>
                    <h2 class="h5 fw-bold mb-1">Daftar Request Donasi</h2>
                    <p class="small rm-text-muted mb-0">Tambahkan barang yang dibutuhkan atau ubah request yang masih menunggu.</p>
                </div>
                <div class="d-flex flex-column flex-sm-row gap-2">
                    <div class="input-group rm-org-search">
                        <span class="input-group-text bg-white border-end-0"><i class="bi bi-search"></i></span>
                        <input type="search" id="searchInput" class="form-control border-start-0" placeholder="Cari request…" aria-label="Cari request">
                    </div>
                    <button type="button" class="btn rm-btn-primary text-nowrap" data-bs-toggle="modal" data-bs-target="#addModal">
                        <i class="bi bi-plus-lg me-1"></i>Tambah Request
                    </button>
                </div>
            </div>

            <div id="orgAlert" class="alert d-none" role="status" aria-live="polite"></div>

            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr><th>Barang yang dibutuhkan</th><th>Tanggal request</th><th>Status</th><th class="text-end">Aksi</th></tr>
                    </thead>
                    <tbody id="donationTableBody">
                        @forelse ($requests as $requestItem)
                            @php $fulfilled = $requestItem->status_req === 'dapat donasi'; @endphp
                            <tr data-search="{{ \Illuminate\Support\Str::lower($requestItem->nama_barang_request . ' ' . $requestItem->id_request_donasi) }}">
                                <td><strong>{{ $requestItem->nama_barang_request }}</strong><div class="small rm-text-muted">{{ $requestItem->id_request_donasi }}</div></td>
                                <td>{{ \Carbon\Carbon::parse($requestItem->tgl_request_donasi)->translatedFormat('d M Y') }}</td>
                                <td><span class="rm-org-status {{ $fulfilled ? 'is-done' : 'is-waiting' }}"><i class="bi {{ $fulfilled ? 'bi-check-circle' : 'bi-hourglass-split' }} me-1"></i>{{ $fulfilled ? 'Terpenuhi' : 'Menunggu donasi' }}</span></td>
                                <td class="text-end">
                                    @if (!$fulfilled)
                                        <div class="d-inline-flex gap-2">
                                            <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill js-edit-request" data-id="{{ $requestItem->id_request_donasi }}" data-name="{{ $requestItem->nama_barang_request }}" aria-label="Edit request"><i class="bi bi-pencil"></i></button>
                                            <button type="button" class="btn btn-outline-danger btn-sm rounded-pill js-delete-request" data-id="{{ $requestItem->id_request_donasi }}" data-name="{{ $requestItem->nama_barang_request }}" aria-label="Hapus request"><i class="bi bi-trash"></i></button>
                                        </div>
                                    @else
                                        <span class="small rm-text-muted">Selesai</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr id="emptyRequests"><td colspan="4"><div class="rm-empty py-4"><i class="bi bi-inbox d-block"></i><p class="mb-0">Belum ada request donasi. Tambahkan kebutuhan pertama organisasi.</p></div></td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </section>

    <div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addModalTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form id="addForm" class="modal-content rm-org-modal">
                <div class="modal-header">
                    <div><h2 class="modal-title h5 fw-bold" id="addModalTitle">Tambah Request</h2><p class="small rm-text-muted mb-0">Tuliskan kebutuhan barang organisasi.</p></div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3"><label for="add_nama" class="form-label fw-semibold">Nama barang</label><input type="text" class="form-control" id="add_nama" name="nama_barang_request" maxlength="255" required></div>
                    <div><label for="add_tanggal" class="form-label fw-semibold">Tanggal request</label><input type="date" class="form-control" id="add_tanggal" name="tgl_request_donasi" value="{{ now()->format('Y-m-d') }}" required></div>
                </div>
                <div class="modal-footer"><button type="button" class="btn rm-btn-outline" data-bs-dismiss="modal">Batal</button><button type="submit" class="btn rm-btn-primary"><i class="bi bi-send me-1"></i>Kirim Request</button></div>
            </form>
        </div>
    </div>

    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form id="editForm" class="modal-content rm-org-modal">
                <div class="modal-header">
                    <div><h2 class="modal-title h5 fw-bold" id="editModalTitle">Ubah Request</h2><p class="small rm-text-muted mb-0">Request yang sudah terpenuhi tidak dapat diubah.</p></div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body"><input type="hidden" id="edit_id"><label for="edit_nama" class="form-label fw-semibold">Nama barang</label><input type="text" class="form-control" id="edit_nama" maxlength="255" required></div>
                <div class="modal-footer"><button type="button" class="btn rm-btn-outline" data-bs-dismiss="modal">Batal</button><button type="submit" class="btn rm-btn-primary">Simpan</button></div>
            </form>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .rm-organization-hero { display:flex; justify-content:space-between; align-items:center; gap:1.5rem; padding:clamp(1.4rem,3vw,2.2rem); border-radius:var(--rm-radius-lg); color:#fff; background:linear-gradient(125deg,var(--rm-primary-dark),var(--rm-primary),#2f7d7a); }
        .rm-organization-hero h1,.rm-organization-hero p { color:#fff; }
        .rm-organization-hero p { color:rgba(255,255,255,.78); }
        .rm-org-eyebrow { display:block; margin-bottom:.45rem; color:rgba(255,255,255,.72); font-size:.72rem; font-weight:700; letter-spacing:.1em; text-transform:uppercase; }
        .rm-org-hero-icon { width:66px;height:66px;flex:0 0 66px;display:flex;align-items:center;justify-content:center;border:1px solid rgba(255,255,255,.3);border-radius:50%;background:rgba(255,255,255,.12);font-size:1.8rem; }
        .rm-org-stat { display:flex;align-items:center;gap:.8rem;height:100%;padding:1rem;border:1px solid var(--rm-line);border-radius:var(--rm-radius);background:#fff;box-shadow:var(--rm-shadow-xs); }
        .rm-org-stat-icon { width:42px;height:42px;flex:0 0 42px;display:flex;align-items:center;justify-content:center;border-radius:50%;background:var(--rm-primary-soft);color:var(--rm-primary);font-size:1.15rem; }
        .rm-org-stat-icon.rm-org-stat-wait { background:var(--rm-warning-soft);color:var(--rm-warning); }
        .rm-org-stat-icon.rm-org-stat-done { background:var(--rm-success-soft);color:var(--rm-success); }
        .rm-org-stat small,.rm-org-stat strong { display:block; }
        .rm-org-stat small { color:var(--rm-muted);font-size:.76rem; }
        .rm-org-stat strong { color:var(--rm-ink);font-size:1.2rem; }
        .rm-org-panel { padding:clamp(1rem,2vw,1.5rem);border:1px solid var(--rm-line);border-radius:var(--rm-radius-lg);background:#fff;box-shadow:var(--rm-shadow-xs); }
        .rm-org-search { max-width:260px; }
        .rm-org-search .input-group-text { border-radius:var(--rm-radius-pill) 0 0 var(--rm-radius-pill); }
        .rm-org-search .form-control { border-radius:0 var(--rm-radius-pill) var(--rm-radius-pill) 0; }
        .rm-org-status { display:inline-flex;align-items:center;padding:.35rem .65rem;border-radius:var(--rm-radius-pill);font-size:.75rem;font-weight:700;white-space:nowrap; }
        .rm-org-status.is-waiting { color:#76540f;background:var(--rm-warning-soft); }
        .rm-org-status.is-done { color:#176246;background:var(--rm-success-soft); }
        .rm-org-modal { border:1px solid var(--rm-line);border-radius:var(--rm-radius-lg);overflow:hidden; }
        .rm-org-modal .modal-header,.rm-org-modal .modal-footer { border-color:var(--rm-line);padding:1rem 1.25rem; }
        .rm-org-modal .modal-body { padding:1.25rem; }
        @media(max-width:575.98px){.rm-org-hero-icon{display:none}.rm-org-search{max-width:none}}
    </style>
@endpush

@push('scripts')
    <script>
        (function () {
            const csrf = @json(csrf_token());
            const addModal = bootstrap.Modal.getOrCreateInstance(document.getElementById('addModal'));
            const editModal = bootstrap.Modal.getOrCreateInstance(document.getElementById('editModal'));
            const addForm = document.getElementById('addForm');
            const editForm = document.getElementById('editForm');
            const updateUrl = @json(url('/organisasi/request-donasi'));

            async function send(url, method, payload) {
                const response = await fetch(url, {
                    method,
                    headers: { 'Accept': 'application/json', 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf },
                    body: payload ? JSON.stringify(payload) : undefined,
                });
                const data = await response.json();
                if (!response.ok || !data.success) throw new Error(data.message || 'Permintaan gagal.');
                return data;
            }

            addForm.addEventListener('submit', async function (event) {
                event.preventDefault();
                const submit = addForm.querySelector('button[type="submit"]');
                submit.disabled = true;
                try {
                    const payload = Object.fromEntries(new FormData(addForm).entries());
                    const result = await send(updateUrl, 'POST', payload);
                    addModal.hide();
                    Swal.fire({ icon: 'success', title: 'Request dikirim', text: result.message }).then(() => location.reload());
                } catch (error) {
                    Swal.fire({ icon: 'error', title: 'Tidak dapat mengirim', text: error.message });
                } finally { submit.disabled = false; }
            });

            document.querySelectorAll('.js-edit-request').forEach(button => button.addEventListener('click', function () {
                document.getElementById('edit_id').value = this.dataset.id;
                document.getElementById('edit_nama').value = this.dataset.name;
                editModal.show();
            }));

            editForm.addEventListener('submit', async function (event) {
                event.preventDefault();
                const id = document.getElementById('edit_id').value;
                try {
                    const result = await send(`${updateUrl}/${encodeURIComponent(id)}`, 'PUT', {
                        nama_barang_request: document.getElementById('edit_nama').value,
                    });
                    editModal.hide();
                    Swal.fire({ icon: 'success', title: 'Request diperbarui', text: result.message }).then(() => location.reload());
                } catch (error) {
                    Swal.fire({ icon: 'error', title: 'Tidak dapat memperbarui', text: error.message });
                }
            });

            document.querySelectorAll('.js-delete-request').forEach(button => button.addEventListener('click', async function () {
                const confirmed = await Swal.fire({
                    title: 'Hapus request ini?',
                    text: this.dataset.name,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Hapus request',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: '#c0392b',
                });
                if (!confirmed.isConfirmed) return;

                try {
                    const result = await send(`${updateUrl}/${encodeURIComponent(this.dataset.id)}`, 'DELETE');
                    Swal.fire({ icon: 'success', title: 'Request dihapus', text: result.message }).then(() => location.reload());
                } catch (error) {
                    Swal.fire({ icon: 'error', title: 'Tidak dapat menghapus', text: error.message });
                }
            }));

            document.getElementById('searchInput').addEventListener('input', function () {
                const value = this.value.trim().toLocaleLowerCase('id-ID');
                document.querySelectorAll('#donationTableBody tr[data-search]').forEach(row => {
                    row.hidden = !row.dataset.search.includes(value);
                });
            });
        })();
    </script>
@endpush
