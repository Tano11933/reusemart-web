@extends('layouts.workspace')

@section('title', 'Data Organisasi · ReuseMart')
@section('page-title', 'Data Organisasi')

@section('content')
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-end gap-3 mb-4">
        <div><h1 class="rm-page-title">Data Organisasi</h1><p class="rm-page-sub">Kelola akun organisasi yang mengajukan permintaan donasi.</p></div>
        <span class="rm-chip"><i class="bi bi-building"></i>{{ $organisasi->count() }} organisasi</span>
    </div>

    <section class="rm-workspace-card">
        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3 mb-3">
            <div><h2 class="h5 fw-bold mb-1">Daftar Organisasi</h2><p class="small rm-text-muted mb-0">Informasi kontak untuk koordinasi donasi.</p></div>
            <div class="d-flex flex-column flex-sm-row gap-2">
                <div class="input-group rm-admin-search"><span class="input-group-text bg-white border-end-0"><i class="bi bi-search"></i></span><input type="search" id="organizationSearch" class="form-control border-start-0" placeholder="Cari organisasi…" aria-label="Cari organisasi"></div>
                <button type="button" class="btn rm-btn-primary text-nowrap" data-bs-toggle="modal" data-bs-target="#addOrganizationModal"><i class="bi bi-plus-lg me-1"></i>Tambah</button>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table align-middle">
                <thead><tr><th>Organisasi</th><th>Email</th><th>Nomor telepon</th><th>Alamat</th><th class="text-end">Aksi</th></tr></thead>
                <tbody id="organizationRows">
                    @forelse ($organisasi as $item)
                        <tr data-search="{{ \Illuminate\Support\Str::lower($item->id_organisasi . ' ' . $item->nama_organisasi . ' ' . $item->email) }}">
                            <td><strong>{{ $item->nama_organisasi }}</strong><div class="small rm-text-muted">{{ $item->id_organisasi }}</div></td>
                            <td>{{ $item->email }}</td>
                            <td>{{ $item->nomor_telepon ?: '-' }}</td>
                            <td>{{ $item->alamat_organisasi ?: '-' }}</td>
                            <td class="text-end"><div class="d-inline-flex gap-2">
                                <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill js-edit-organization" data-id="{{ $item->id_organisasi }}" data-email="{{ $item->email }}" data-phone="{{ $item->nomor_telepon }}" data-address="{{ $item->alamat_organisasi }}"><i class="bi bi-pencil me-1"></i>Edit</button>
                                <form action="{{ route('organisasi.destroy', $item->id_organisasi) }}" method="POST" class="js-delete-organization">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger btn-sm rounded-pill"><i class="bi bi-trash me-1"></i>Hapus</button>
                                </form>
                            </div></td>
                        </tr>
                    @empty
                        <tr><td colspan="5"><div class="rm-empty py-4"><i class="bi bi-building d-block"></i><p class="mb-2">Belum ada organisasi terdaftar.</p><button type="button" class="btn rm-btn-primary" data-bs-toggle="modal" data-bs-target="#addOrganizationModal"><i class="bi bi-plus-lg me-1"></i>Tambah Organisasi</button></div></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <div class="modal fade" id="addOrganizationModal" tabindex="-1" aria-labelledby="addOrganizationTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered"><form action="{{ route('organisasi.store') }}" method="POST" class="modal-content rm-admin-modal">
            @csrf
            <div class="modal-header"><div><h2 class="modal-title h5 fw-bold" id="addOrganizationTitle">Tambah Organisasi</h2><p class="small rm-text-muted mb-0">Buat akun akses untuk organisasi mitra.</p></div><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button></div>
            <div class="modal-body">
                <div class="mb-3"><label for="add_org_name" class="form-label fw-semibold">Nama organisasi</label><input type="text" id="add_org_name" name="nama_organisasi" class="form-control" maxlength="255" required></div>
                <div class="mb-3"><label for="add_org_email" class="form-label fw-semibold">Email</label><input type="email" id="add_org_email" name="email" class="form-control" required></div>
                <div class="mb-3"><label for="add_org_phone" class="form-label fw-semibold">Nomor telepon</label><input type="text" id="add_org_phone" name="nomor_telepon" class="form-control" maxlength="20" required></div>
                <div class="mb-3"><label for="add_org_address" class="form-label fw-semibold">Alamat</label><textarea id="add_org_address" name="alamat_organisasi" class="form-control" rows="2" maxlength="255" required></textarea></div>
                <div><label for="add_org_password" class="form-label fw-semibold">Kata sandi awal</label><input type="password" id="add_org_password" name="password" class="form-control" minlength="8" required></div>
            </div>
            <div class="modal-footer"><button type="button" class="btn rm-btn-outline" data-bs-dismiss="modal">Batal</button><button type="submit" class="btn rm-btn-primary">Buat Akun</button></div>
        </form></div>
    </div>

    <div class="modal fade" id="editOrganizationModal" tabindex="-1" aria-labelledby="editOrganizationTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered"><form id="editOrganizationForm" class="modal-content rm-admin-modal">
            <div class="modal-header"><div><h2 class="modal-title h5 fw-bold" id="editOrganizationTitle">Edit Kontak Organisasi</h2><p class="small rm-text-muted mb-0">Perbarui informasi kontak mitra.</p></div><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button></div>
            <div class="modal-body">
                <input type="hidden" id="edit_org_id">
                <div class="mb-3"><label for="edit_org_email" class="form-label fw-semibold">Email</label><input type="email" id="edit_org_email" class="form-control" required></div>
                <div class="mb-3"><label for="edit_org_phone" class="form-label fw-semibold">Nomor telepon</label><input type="text" id="edit_org_phone" class="form-control" maxlength="20" required></div>
                <div><label for="edit_org_address" class="form-label fw-semibold">Alamat</label><textarea id="edit_org_address" class="form-control" rows="2" maxlength="255" required></textarea></div>
            </div>
            <div class="modal-footer"><button type="button" class="btn rm-btn-outline" data-bs-dismiss="modal">Batal</button><button type="submit" class="btn rm-btn-primary">Simpan</button></div>
        </form></div>
    </div>
@endsection

@push('styles')
    <style>
        .rm-admin-search{max-width:270px}.rm-admin-search .input-group-text{border-radius:var(--rm-radius-pill) 0 0 var(--rm-radius-pill)}.rm-admin-search .form-control{border-radius:0 var(--rm-radius-pill) var(--rm-radius-pill) 0}
        .rm-admin-modal{border:1px solid var(--rm-line);border-radius:var(--rm-radius-lg);overflow:hidden}.rm-admin-modal .modal-header,.rm-admin-modal .modal-footer{border-color:var(--rm-line);padding:1rem 1.25rem}.rm-admin-modal .modal-body{padding:1.25rem}
        @media(max-width:575.98px){.rm-admin-search{max-width:none}}
    </style>
@endpush

@push('scripts')
    <script>
        const organizationBase = @json(url('/organisasi'));
        const editOrganizationModal = bootstrap.Modal.getOrCreateInstance(document.getElementById('editOrganizationModal'));
        document.querySelectorAll('.js-edit-organization').forEach(button => button.addEventListener('click', function () {
            document.getElementById('edit_org_id').value = this.dataset.id;
            document.getElementById('edit_org_email').value = this.dataset.email || '';
            document.getElementById('edit_org_phone').value = this.dataset.phone || '';
            document.getElementById('edit_org_address').value = this.dataset.address || '';
            editOrganizationModal.show();
        }));

        document.getElementById('editOrganizationForm').addEventListener('submit', async function (event) {
            event.preventDefault();
            const id = document.getElementById('edit_org_id').value;
            try {
                const response = await fetch(`${organizationBase}/${encodeURIComponent(id)}`, {
                    method: 'PUT',
                    headers: { 'Accept': 'application/json', 'Content-Type': 'application/json', 'X-CSRF-TOKEN': @json(csrf_token()) },
                    body: JSON.stringify({
                        email: document.getElementById('edit_org_email').value,
                        nomor_telepon: document.getElementById('edit_org_phone').value,
                        alamat_organisasi: document.getElementById('edit_org_address').value,
                    }),
                });
                const result = await response.json();
                if (!response.ok) throw new Error(result.message || 'Gagal memperbarui organisasi.');
                location.reload();
            } catch (error) { Swal.fire({ icon: 'error', title: 'Tidak dapat menyimpan', text: error.message }); }
        });

        document.querySelectorAll('.js-delete-organization').forEach(form => form.addEventListener('submit', function (event) {
            event.preventDefault();
            Swal.fire({ title: 'Hapus organisasi?', text: 'Akun dan data terkait dapat terpengaruh.', icon: 'warning', showCancelButton: true, confirmButtonText: 'Hapus', cancelButtonText: 'Batal', confirmButtonColor: '#c0392b' }).then(result => {
                if (result.isConfirmed) HTMLFormElement.prototype.submit.call(form);
            });
        }));

        document.getElementById('organizationSearch').addEventListener('input', function () {
            const query = this.value.trim().toLocaleLowerCase('id-ID');
            document.querySelectorAll('#organizationRows tr[data-search]').forEach(row => row.hidden = !row.dataset.search.includes(query));
        });
    </script>
@endpush
