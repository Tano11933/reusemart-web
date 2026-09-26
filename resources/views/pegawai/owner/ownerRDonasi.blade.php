@extends('layouts.workspace')

@section('title', 'Request Donasi — ReuseMart')
@section('page-title', 'Request Donasi')

@section('content')
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-end gap-3 mb-4">
        <div>
            <h1 class="rm-page-title">Request Donasi</h1>
            <p class="rm-page-sub">Tinjau permintaan organisasi dan cocokkan dengan barang yang siap didonasikan.</p>
        </div>
        <span class="rm-chip"><i class="bi bi-inbox"></i>{{ $requests->count() }} menunggu</span>
    </div>

    <section class="rm-workspace-card">
        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3 mb-3">
            <div>
                <h2 class="h5 fw-bold mb-1">Permintaan Menunggu Donasi</h2>
                <p class="small rm-text-muted mb-0">Pilih request untuk mencatat penyaluran barang.</p>
            </div>
            <div class="input-group rm-workspace-search">
                <span class="input-group-text bg-white border-end-0"><i class="bi bi-search"></i></span>
                <input class="form-control border-start-0" id="requestSearch" type="search" placeholder="Cari organisasi atau barang…" aria-label="Cari request donasi">
            </div>
        </div>

        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>Request</th>
                        <th>Organisasi</th>
                        <th>Tanggal</th>
                        <th>Status</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody id="requestRows">
                    @forelse ($requests as $requestItem)
                        <tr data-search="{{ \Illuminate\Support\Str::lower($requestItem->nama_barang_request . ' ' . $requestItem->nama_organisasi . ' ' . $requestItem->id_request_donasi) }}">
                            <td>
                                <strong>{{ $requestItem->nama_barang_request }}</strong>
                                <div class="small rm-text-muted">{{ $requestItem->id_request_donasi }}</div>
                            </td>
                            <td>{{ $requestItem->nama_organisasi }}</td>
                            <td>{{ \Carbon\Carbon::parse($requestItem->tgl_request_donasi)->translatedFormat('d M Y') }}</td>
                            <td><span class="rm-status-pill rm-status-pending">{{ $requestItem->status_req ?: 'Menunggu donasi' }}</span></td>
                            <td class="text-end">
                                <button type="button" class="btn rm-btn-primary btn-sm js-open-donation"
                                        data-request-id="{{ $requestItem->id_request_donasi }}"
                                        data-request-name="{{ $requestItem->nama_barang_request }}"
                                        data-organization="{{ $requestItem->nama_organisasi }}"
                                        @disabled($barangProsesDonasi->isEmpty())>
                                    <i class="bi bi-box2-heart me-1"></i>Catat Donasi
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5"><div class="rm-empty py-4"><i class="bi bi-inbox d-block"></i><p class="mb-0">Belum ada request donasi yang menunggu.</p></div></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($requests->isNotEmpty() && $barangProsesDonasi->isEmpty())
            <div class="alert alert-info mt-3 mb-0"><i class="bi bi-info-circle me-1"></i>Belum ada barang dengan status siap donasi.</div>
        @endif
    </section>

    <div class="modal fade" id="donationModal" tabindex="-1" aria-labelledby="donationModalTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form id="donationForm" method="POST" action="" class="modal-content rm-workspace-modal">
                @csrf
                <input type="hidden" name="id_request_donasi" id="donationRequestId">
                <div class="modal-header">
                    <div>
                        <h2 class="modal-title h5 fw-bold" id="donationModalTitle">Catat Donasi</h2>
                        <p class="small rm-text-muted mb-0" id="donationRequestCaption"></p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="id_barang" class="form-label fw-semibold">Barang yang disalurkan</label>
                        <select name="id_barang" id="id_barang" class="form-select" required>
                            <option value="">Pilih barang siap donasi</option>
                            @foreach ($barangProsesDonasi as $barang)
                                <option value="{{ $barang->id_barang }}">{{ $barang->nama_barang }} &middot; {{ $barang->id_barang }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="nama_penerima" class="form-label fw-semibold">Nama penerima</label>
                        <input type="text" name="nama_penerima" id="nama_penerima" class="form-control" maxlength="255" placeholder="Nama penerima donasi" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn rm-btn-outline" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn rm-btn-primary"><i class="bi bi-check2 me-1"></i>Konfirmasi Donasi</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .rm-workspace-search { max-width: 340px; }
        .rm-workspace-search .input-group-text { border-radius: var(--rm-radius-pill) 0 0 var(--rm-radius-pill); }
        .rm-workspace-search .form-control { border-radius: 0 var(--rm-radius-pill) var(--rm-radius-pill) 0; }
        .rm-workspace-modal { border: 1px solid var(--rm-line); border-radius: var(--rm-radius-lg); overflow: hidden; }
        .rm-workspace-modal .modal-header,
        .rm-workspace-modal .modal-footer { border-color: var(--rm-line); padding: 1rem 1.25rem; }
        .rm-workspace-modal .modal-body { padding: 1.25rem; }
        .rm-status-pill { display: inline-flex; border-radius: var(--rm-radius-pill); padding: .35rem .7rem; background: var(--rm-warning-soft); color: #76540f; font-size: .76rem; font-weight: 700; }
        @media (max-width: 575.98px) { .rm-workspace-search { max-width: none; } }
    </style>
@endpush

@push('scripts')
    <script>
        const donationModal = bootstrap.Modal.getOrCreateInstance(document.getElementById('donationModal'));
        const donationForm = document.getElementById('donationForm');

        document.querySelectorAll('.js-open-donation').forEach(button => {
            button.addEventListener('click', function () {
                document.getElementById('donationRequestId').value = this.dataset.requestId;
                document.getElementById('donationRequestCaption').textContent = `${this.dataset.requestName} · ${this.dataset.organization}`;
                donationForm.action = @json(url('/donasi')) + '/' + encodeURIComponent(this.dataset.requestId);
                donationModal.show();
            });
        });

        document.getElementById('requestSearch').addEventListener('input', function () {
            const query = this.value.trim().toLocaleLowerCase('id-ID');
            document.querySelectorAll('#requestRows tr[data-search]').forEach(row => {
                row.hidden = !row.dataset.search.includes(query);
            });
        });
    </script>
@endpush
