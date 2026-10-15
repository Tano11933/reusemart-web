@extends('layouts.workspace')

@section('title', 'Data Jabatan · ReuseMart')
@section('page-title', 'Data Jabatan')

@section('content')
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-end gap-3 mb-4">
        <div><h1 class="rm-page-title">Data Jabatan</h1><p class="rm-page-sub">Kelola daftar jabatan dan peran kerja di ReuseMart.</p></div>
        <span class="rm-chip"><i class="bi bi-person-gear"></i>{{ $jabatan->count() }} jabatan</span>
    </div>

    <section class="rm-workspace-card">
        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3 mb-3">
            <div><h2 class="h5 fw-bold mb-1">Daftar Jabatan</h2><p class="small rm-text-muted mb-0">Nama jabatan dipakai untuk menentukan menu kerja pegawai.</p></div>
            <div class="d-flex flex-column flex-sm-row gap-2">
                <div class="input-group rm-admin-search"><span class="input-group-text bg-white border-end-0"><i class="bi bi-search"></i></span><input id="jabatanSearch" class="form-control border-start-0" type="search" placeholder="Cari jabatan…" aria-label="Cari jabatan"></div>
                <button type="button" class="btn rm-btn-primary text-nowrap" data-bs-toggle="modal" data-bs-target="#addJabatanModal"><i class="bi bi-plus-lg me-1"></i>Tambah Jabatan</button>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table align-middle">
                <thead><tr><th>ID Jabatan</th><th>Nama Jabatan</th><th class="text-end">Aksi</th></tr></thead>
                <tbody id="jabatanRows">
                    @forelse ($jabatan as $item)
                        <tr data-search="{{ \Illuminate\Support\Str::lower($item->id_jabatan . ' ' . $item->nama_jabatan) }}">
                            <td><span class="rm-chip">{{ $item->id_jabatan }}</span></td>
                            <td class="fw-semibold">{{ $item->nama_jabatan }}</td>
                            <td class="text-end">
                                <div class="d-inline-flex gap-2">
                                    <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill js-edit-jabatan" data-id="{{ $item->id_jabatan }}" data-name="{{ $item->nama_jabatan }}"><i class="bi bi-pencil me-1"></i>Edit</button>
                                    <form action="{{ route('jabatan.destroy', $item->id_jabatan) }}" method="POST" class="js-delete-jabatan-form">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger btn-sm rounded-pill"><i class="bi bi-trash me-1"></i>Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="3"><div class="rm-empty py-4"><i class="bi bi-person-gear d-block"></i><p class="mb-2">Belum ada data jabatan.</p><button type="button" class="btn rm-btn-primary" data-bs-toggle="modal" data-bs-target="#addJabatanModal"><i class="bi bi-plus-lg me-1"></i>Tambah Jabatan</button></div></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <div class="modal fade" id="addJabatanModal" tabindex="-1" aria-labelledby="addJabatanTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered"><form action="{{ route('jabatan.store') }}" method="POST" class="modal-content rm-admin-modal">
            @csrf
            <div class="modal-header"><div><h2 class="modal-title h5 fw-bold" id="addJabatanTitle">Tambah Jabatan</h2><p class="small rm-text-muted mb-0">Tambahkan nama peran baru.</p></div><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button></div>
            <div class="modal-body"><label for="nama_jabatan" class="form-label fw-semibold">Nama Jabatan</label><input type="text" class="form-control" id="nama_jabatan" name="nama_jabatan" maxlength="255" required></div>
            <div class="modal-footer"><button type="button" class="btn rm-btn-outline" data-bs-dismiss="modal">Batal</button><button type="submit" class="btn rm-btn-primary">Simpan</button></div>
        </form></div>
    </div>

    <div class="modal fade" id="editJabatanModal" tabindex="-1" aria-labelledby="editJabatanTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered"><form id="editJabatanForm" class="modal-content rm-admin-modal">
            <div class="modal-header"><div><h2 class="modal-title h5 fw-bold" id="editJabatanTitle">Edit Jabatan</h2><p class="small rm-text-muted mb-0">Perbarui nama jabatan.</p></div><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button></div>
            <div class="modal-body"><input type="hidden" id="edit_jabatan_id"><label for="edit_nama_jabatan" class="form-label fw-semibold">Nama Jabatan</label><input type="text" class="form-control" id="edit_nama_jabatan" maxlength="255" required></div>
            <div class="modal-footer"><button type="button" class="btn rm-btn-outline" data-bs-dismiss="modal">Batal</button><button type="submit" class="btn rm-btn-primary">Simpan Perubahan</button></div>
        </form></div>
    </div>
@endsection

@push('styles')
    <style>
        .rm-admin-search{max-width:280px}.rm-admin-search .input-group-text{border-radius:var(--rm-radius-pill) 0 0 var(--rm-radius-pill)}.rm-admin-search .form-control{border-radius:0 var(--rm-radius-pill) var(--rm-radius-pill) 0}
        .rm-admin-modal{border:1px solid var(--rm-line);border-radius:var(--rm-radius-lg);overflow:hidden}.rm-admin-modal .modal-header,.rm-admin-modal .modal-footer{border-color:var(--rm-line);padding:1rem 1.25rem}.rm-admin-modal .modal-body{padding:1.25rem}
        @media(max-width:575.98px){.rm-admin-search{max-width:none}}
    </style>
@endpush

@push('scripts')
    <script>
        const editJabatanModal = bootstrap.Modal.getOrCreateInstance(document.getElementById('editJabatanModal'));
        const editJabatanForm = document.getElementById('editJabatanForm');
        const jabatanBaseUrl = @json(url('/jabatan'));

        document.querySelectorAll('.js-edit-jabatan').forEach(button => button.addEventListener('click', function () {
            document.getElementById('edit_jabatan_id').value = this.dataset.id;
            document.getElementById('edit_nama_jabatan').value = this.dataset.name;
            editJabatanModal.show();
        }));

        editJabatanForm.addEventListener('submit', async function (event) {
            event.preventDefault();
            const id = document.getElementById('edit_jabatan_id').value;
            try {
                const response = await fetch(`${jabatanBaseUrl}/${encodeURIComponent(id)}`, {
                    method: 'PUT',
                    headers: { 'Accept': 'application/json', 'Content-Type': 'application/json', 'X-CSRF-TOKEN': @json(csrf_token()) },
                    body: JSON.stringify({ nama_jabatan: document.getElementById('edit_nama_jabatan').value }),
                });
                const result = await response.json();
                if (!response.ok) throw new Error(result.message || 'Gagal memperbarui jabatan.');
                location.reload();
            } catch (error) {
                Swal.fire({ icon: 'error', title: 'Tidak dapat menyimpan', text: error.message });
            }
        });

        document.querySelectorAll('.js-delete-jabatan-form').forEach(form => form.addEventListener('submit', function (event) {
            event.preventDefault();
            Swal.fire({ title: 'Hapus jabatan?', text: 'Tindakan ini tidak dapat dibatalkan.', icon: 'warning', showCancelButton: true, confirmButtonText: 'Hapus', cancelButtonText: 'Batal', confirmButtonColor: '#c0392b' }).then(result => {
                if (result.isConfirmed) HTMLFormElement.prototype.submit.call(form);
            });
        }));

        document.getElementById('jabatanSearch').addEventListener('input', function () {
            const value = this.value.trim().toLocaleLowerCase('id-ID');
            document.querySelectorAll('#jabatanRows tr[data-search]').forEach(row => row.hidden = !row.dataset.search.includes(value));
        });
    </script>
@endpush
