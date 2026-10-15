@extends('layouts.workspace')

@section('title', 'Data Pegawai · ReuseMart')
@section('page-title', 'Data Pegawai')

@section('content')
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-end gap-3 mb-4">
        <div><h1 class="rm-page-title">Data Pegawai</h1><p class="rm-page-sub">Kelola akun pegawai dan akses berdasarkan jabatan.</p></div>
        <span class="rm-chip"><i class="bi bi-people"></i>{{ $pegawai->count() }} pegawai</span>
    </div>

    <section class="rm-workspace-card">
        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3 mb-3">
            <div><h2 class="h5 fw-bold mb-1">Daftar Pegawai</h2><p class="small rm-text-muted mb-0">Reset kata sandi akan menggunakan tanggal lahir pegawai (format DDMMYYYY).</p></div>
            <div class="d-flex flex-column flex-sm-row gap-2">
                <div class="input-group rm-admin-search"><span class="input-group-text bg-white border-end-0"><i class="bi bi-search"></i></span><input type="search" id="employeeSearch" class="form-control border-start-0" placeholder="Cari pegawai…" aria-label="Cari pegawai"></div>
                <button type="button" class="btn rm-btn-primary text-nowrap" data-bs-toggle="modal" data-bs-target="#addEmployeeModal"><i class="bi bi-person-plus me-1"></i>Tambah Pegawai</button>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table align-middle">
                <thead><tr><th>Pegawai</th><th>Jabatan</th><th>Kontak</th><th>Alamat</th><th class="text-end">Aksi</th></tr></thead>
                <tbody id="employeeRows">
                    @forelse ($pegawai as $item)
                        <tr data-search="{{ \Illuminate\Support\Str::lower($item->id_pegawai . ' ' . $item->nama_pegawai . ' ' . $item->email . ' ' . ($item->jabatan->nama_jabatan ?? $item->id_jabatan)) }}">
                            <td><strong>{{ $item->nama_pegawai }}</strong><div class="small rm-text-muted">{{ $item->id_pegawai }}</div></td>
                            <td><span class="rm-chip">{{ $item->jabatan->nama_jabatan ?? $item->id_jabatan }}</span></td>
                            <td>{{ $item->email }}<div class="small rm-text-muted">{{ $item->nomor_telepon }}</div></td>
                            <td>{{ $item->alamat ?: '-' }}</td>
                            <td class="text-end"><div class="d-inline-flex gap-2">
                                <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill js-edit-employee" data-id="{{ $item->id_pegawai }}" data-name="{{ $item->nama_pegawai }}" data-email="{{ $item->email }}" data-phone="{{ $item->nomor_telepon }}" data-address="{{ $item->alamat }}"><i class="bi bi-pencil me-1"></i>Edit</button>
                                <form action="{{ route('pegawai.destroy', $item->id_pegawai) }}" method="POST" class="js-delete-employee">@csrf @method('DELETE')<button type="submit" class="btn btn-outline-danger btn-sm rounded-pill"><i class="bi bi-trash me-1"></i>Hapus</button></form>
                            </div></td>
                        </tr>
                    @empty
                        <tr><td colspan="5"><div class="rm-empty py-4"><i class="bi bi-people d-block"></i><p class="mb-2">Belum ada data pegawai.</p><button type="button" class="btn rm-btn-primary" data-bs-toggle="modal" data-bs-target="#addEmployeeModal"><i class="bi bi-person-plus me-1"></i>Tambah Pegawai</button></div></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <div class="modal fade" id="addEmployeeModal" tabindex="-1" aria-labelledby="addEmployeeTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg"><form action="{{ route('pegawai.store') }}" method="POST" class="modal-content rm-admin-modal">
            @csrf
            <div class="modal-header"><div><h2 class="modal-title h5 fw-bold" id="addEmployeeTitle">Tambah Pegawai</h2><p class="small rm-text-muted mb-0">Buat akun dan pilih jabatan pegawai.</p></div><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button></div>
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-md-6"><label for="add_employee_name" class="form-label fw-semibold">Nama lengkap</label><input id="add_employee_name" name="nama_pegawai" class="form-control" maxlength="255" required></div>
                    <div class="col-md-6"><label for="add_employee_email" class="form-label fw-semibold">Email</label><input type="email" id="add_employee_email" name="email" class="form-control" required></div>
                    <div class="col-md-6"><label for="add_employee_phone" class="form-label fw-semibold">Nomor telepon</label><input id="add_employee_phone" name="nomor_telepon" class="form-control" maxlength="20" required></div>
                    <div class="col-md-6"><label for="add_employee_password" class="form-label fw-semibold">Kata sandi awal</label><input type="password" id="add_employee_password" name="password" class="form-control" minlength="8" required></div>
                    <div class="col-md-6"><label for="add_employee_dob" class="form-label fw-semibold">Tanggal lahir</label><input type="date" id="add_employee_dob" name="tgl_lahir" class="form-control" required></div>
                    <div class="col-md-6"><label for="add_employee_role" class="form-label fw-semibold">Jabatan</label><select id="add_employee_role" name="id_jabatan" class="form-select" required><option value="">Pilih jabatan</option>@foreach($jabatan as $role)<option value="{{ $role->id_jabatan }}">{{ $role->nama_jabatan }}</option>@endforeach</select></div>
                    <div class="col-12"><label for="add_employee_address" class="form-label fw-semibold">Alamat</label><textarea id="add_employee_address" name="alamat" class="form-control" rows="2" required></textarea></div>
                </div>
            </div>
            <div class="modal-footer"><button type="button" class="btn rm-btn-outline" data-bs-dismiss="modal">Batal</button><button type="submit" class="btn rm-btn-primary">Simpan Pegawai</button></div>
        </form></div>
    </div>

    <div class="modal fade" id="editEmployeeModal" tabindex="-1" aria-labelledby="editEmployeeTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered"><form id="editEmployeeForm" class="modal-content rm-admin-modal">
            <div class="modal-header"><div><h2 class="modal-title h5 fw-bold" id="editEmployeeTitle">Edit Pegawai</h2><p class="small rm-text-muted mb-0">Perbarui kontak atau reset kata sandi.</p></div><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button></div>
            <div class="modal-body">
                <input type="hidden" id="edit_employee_id">
                <div class="mb-3"><label for="edit_employee_name" class="form-label fw-semibold">Nama</label><input id="edit_employee_name" class="form-control" readonly></div>
                <div class="mb-3"><label for="edit_employee_email" class="form-label fw-semibold">Email</label><input type="email" id="edit_employee_email" class="form-control" required></div>
                <div class="mb-3"><label for="edit_employee_phone" class="form-label fw-semibold">Nomor telepon</label><input id="edit_employee_phone" class="form-control" maxlength="20" required></div>
                <div class="mb-3"><label for="edit_employee_address" class="form-label fw-semibold">Alamat</label><textarea id="edit_employee_address" class="form-control" rows="2"></textarea></div>
                <div class="form-check"><input type="checkbox" class="form-check-input" id="reset_employee_password" value="1"><label class="form-check-label" for="reset_employee_password">Reset kata sandi ke tanggal lahir (DDMMYYYY)</label></div>
            </div>
            <div class="modal-footer"><button type="button" class="btn rm-btn-outline" data-bs-dismiss="modal">Batal</button><button type="submit" class="btn rm-btn-primary">Simpan Perubahan</button></div>
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
        const employeeBase = @json(url('/pegawai'));
        const editEmployeeModal = bootstrap.Modal.getOrCreateInstance(document.getElementById('editEmployeeModal'));
        document.querySelectorAll('.js-edit-employee').forEach(button => button.addEventListener('click', function () {
            document.getElementById('edit_employee_id').value = this.dataset.id;
            document.getElementById('edit_employee_name').value = this.dataset.name;
            document.getElementById('edit_employee_email').value = this.dataset.email;
            document.getElementById('edit_employee_phone').value = this.dataset.phone;
            document.getElementById('edit_employee_address').value = this.dataset.address;
            document.getElementById('reset_employee_password').checked = false;
            editEmployeeModal.show();
        }));

        document.getElementById('editEmployeeForm').addEventListener('submit', async function (event) {
            event.preventDefault();
            const id = document.getElementById('edit_employee_id').value;
            try {
                const response = await fetch(`${employeeBase}/${encodeURIComponent(id)}`, {
                    method: 'PUT',
                    headers: { 'Accept': 'application/json', 'Content-Type': 'application/json', 'X-CSRF-TOKEN': @json(csrf_token()) },
                    body: JSON.stringify({
                        email: document.getElementById('edit_employee_email').value,
                        nomor_telepon: document.getElementById('edit_employee_phone').value,
                        alamat: document.getElementById('edit_employee_address').value,
                        reset_password: document.getElementById('reset_employee_password').checked,
                    }),
                });
                const result = await response.json();
                if (!response.ok) throw new Error(result.message || 'Gagal memperbarui pegawai.');
                location.reload();
            } catch (error) { Swal.fire({ icon: 'error', title: 'Tidak dapat menyimpan', text: error.message }); }
        });

        document.querySelectorAll('.js-delete-employee').forEach(form => form.addEventListener('submit', function (event) {
            event.preventDefault();
            Swal.fire({ title: 'Hapus akun pegawai?', text: 'Akun tidak dapat dipulihkan setelah dihapus.', icon: 'warning', showCancelButton: true, confirmButtonText: 'Hapus', cancelButtonText: 'Batal', confirmButtonColor: '#c0392b' }).then(result => {
                if (result.isConfirmed) HTMLFormElement.prototype.submit.call(form);
            });
        }));

        document.getElementById('employeeSearch').addEventListener('input', function () {
            const query = this.value.trim().toLocaleLowerCase('id-ID');
            document.querySelectorAll('#employeeRows tr[data-search]').forEach(row => row.hidden = !row.dataset.search.includes(query));
        });
    </script>
@endpush
