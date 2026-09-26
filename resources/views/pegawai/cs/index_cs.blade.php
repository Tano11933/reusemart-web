@extends('layouts.workspace')

@section('title', 'Data Penitip — ReuseMart')
@section('page-title', 'Data Penitip')

@section('content')
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-end gap-3 mb-4">
        <div><h1 class="rm-page-title">Data Penitip</h1><p class="rm-page-sub">Kelola profil penitip dan dokumen identitas mereka.</p></div>
        <span class="rm-chip"><i class="bi bi-person-lines-fill"></i>{{ $penitip->count() }} penitip</span>
    </div>

    <section class="rm-workspace-card">
        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3 mb-3">
            <div><h2 class="h5 fw-bold mb-1">Daftar Penitip</h2><p class="small rm-text-muted mb-0">Foto KTP dibuka di tab baru untuk pemeriksaan.</p></div>
            <div class="d-flex flex-column flex-sm-row gap-2">
                <div class="input-group rm-cs-search"><span class="input-group-text bg-white border-end-0"><i class="bi bi-search"></i></span><input type="search" id="penitipSearch" class="form-control border-start-0" placeholder="Cari nama, email, atau NIK…" aria-label="Cari penitip"></div>
                <button type="button" class="btn rm-btn-primary text-nowrap" data-bs-toggle="modal" data-bs-target="#addPenitipModal"><i class="bi bi-person-plus me-1"></i>Tambah Penitip</button>
            </div>
        </div>

        <div id="penitipAlert" class="alert d-none" role="status" aria-live="polite"></div>

        <div class="table-responsive">
            <table class="table align-middle">
                <thead><tr><th>Penitip</th><th>Kontak</th><th>NIK</th><th>Jenis kelamin</th><th>Foto KTP</th><th class="text-end">Aksi</th></tr></thead>
                <tbody id="penitipRows">
                    @forelse ($penitip as $item)
                        @php
                            $photo = trim((string) $item->foto_ktp);
                            $photoUrl = $photo ? asset('storage/' . $photo) : null;
                        @endphp
                        <tr data-search="{{ \Illuminate\Support\Str::lower($item->id_penitip . ' ' . $item->nama_penitip . ' ' . $item->email . ' ' . $item->nik_penitip) }}">
                            <td><strong>{{ $item->nama_penitip }}</strong><div class="small rm-text-muted">{{ $item->id_penitip }}</div></td>
                            <td>{{ $item->email }}<div class="small rm-text-muted">{{ $item->nomor_telepon }}</div></td>
                            <td>{{ $item->nik_penitip }}</td>
                            <td>{{ $item->jenis_kelamin ?: '—' }}</td>
                            <td>
                                @if ($photoUrl)
                                    <a href="{{ $photoUrl }}" target="_blank" rel="noopener" class="btn rm-btn-outline btn-sm"><i class="bi bi-card-image me-1"></i>Lihat KTP</a>
                                @else
                                    <span class="small rm-text-muted">Belum tersedia</span>
                                @endif
                            </td>
                            <td class="text-end"><div class="d-inline-flex gap-2">
                                <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill js-edit-penitip"
                                        data-id="{{ $item->id_penitip }}" data-name="{{ $item->nama_penitip }}" data-email="{{ $item->email }}"
                                        data-phone="{{ $item->nomor_telepon }}" data-nik="{{ $item->nik_penitip }}"
                                        data-gender="{{ $item->jenis_kelamin }}" data-birth="{{ $item->tgl_lahir }}">
                                    <i class="bi bi-pencil me-1"></i>Edit
                                </button>
                                <button type="button" class="btn btn-outline-danger btn-sm rounded-pill js-delete-penitip" data-id="{{ $item->id_penitip }}" data-name="{{ $item->nama_penitip }}"><i class="bi bi-trash me-1"></i>Hapus</button>
                            </div></td>
                        </tr>
                    @empty
                        <tr><td colspan="6"><div class="rm-empty py-4"><i class="bi bi-person-lines-fill d-block"></i><p class="mb-0">Belum ada penitip terdaftar.</p></div></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <div class="modal fade" id="addPenitipModal" tabindex="-1" aria-labelledby="addPenitipTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg"><form id="addPenitipForm" class="modal-content rm-cs-modal" enctype="multipart/form-data">
            <div class="modal-header"><div><h2 class="modal-title h5 fw-bold" id="addPenitipTitle">Tambah Penitip</h2><p class="small rm-text-muted mb-0">Lengkapi data identitas dan unggah foto KTP.</p></div><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button></div>
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-md-6"><label for="add_nama" class="form-label fw-semibold">Nama lengkap</label><input type="text" class="form-control" id="add_nama" name="nama_penitip" maxlength="255" required></div>
                    <div class="col-md-6"><label for="add_email" class="form-label fw-semibold">Email</label><input type="email" class="form-control" id="add_email" name="email" required></div>
                    <div class="col-md-6"><label for="add_nomor_telepon" class="form-label fw-semibold">Nomor telepon</label><input type="text" class="form-control" id="add_nomor_telepon" name="nomor_telepon" maxlength="20" required></div>
                    <div class="col-md-6"><label for="add_password" class="form-label fw-semibold">Kata sandi awal</label><input type="password" class="form-control" id="add_password" name="password" minlength="8" required></div>
                    <div class="col-md-6"><label for="add_nik" class="form-label fw-semibold">NIK</label><input type="text" class="form-control" id="add_nik" name="nik_penitip" maxlength="50" required></div>
                    <div class="col-md-6"><label for="add_jenis_kelamin" class="form-label fw-semibold">Jenis kelamin</label><select class="form-select" id="add_jenis_kelamin" name="jenis_kelamin" required><option value="">Pilih jenis kelamin</option><option value="Pria">Pria</option><option value="Wanita">Wanita</option></select></div>
                    <div class="col-md-6"><label for="add_tgl_lahir" class="form-label fw-semibold">Tanggal lahir</label><input type="date" class="form-control" id="add_tgl_lahir" name="tgl_lahir" required></div>
                    <div class="col-md-6"><label for="add_foto_ktp" class="form-label fw-semibold">Foto KTP</label><input type="file" class="form-control" id="add_foto_ktp" name="foto_ktp" accept="image/jpeg,image/png" required></div>
                </div>
            </div>
            <div class="modal-footer"><button type="button" class="btn rm-btn-outline" data-bs-dismiss="modal">Batal</button><button type="submit" class="btn rm-btn-primary">Simpan Penitip</button></div>
        </form></div>
    </div>

    <div class="modal fade" id="editPenitipModal" tabindex="-1" aria-labelledby="editPenitipTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg"><form id="editPenitipForm" class="modal-content rm-cs-modal">
            <div class="modal-header"><div><h2 class="modal-title h5 fw-bold" id="editPenitipTitle">Edit Data Penitip</h2><p class="small rm-text-muted mb-0">Perbarui informasi kontak dan identitas.</p></div><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button></div>
            <div class="modal-body">
                <input type="hidden" id="edit_penitip_id">
                <div class="row g-3">
                    <div class="col-md-6"><label for="edit_nama" class="form-label fw-semibold">Nama lengkap</label><input type="text" class="form-control" id="edit_nama" required></div>
                    <div class="col-md-6"><label for="edit_email" class="form-label fw-semibold">Email</label><input type="email" class="form-control" id="edit_email" required></div>
                    <div class="col-md-6"><label for="edit_nomor_telepon" class="form-label fw-semibold">Nomor telepon</label><input type="text" class="form-control" id="edit_nomor_telepon" required></div>
                    <div class="col-md-6"><label for="edit_nik" class="form-label fw-semibold">NIK</label><input type="text" class="form-control" id="edit_nik" required></div>
                    <div class="col-md-6"><label for="edit_jenis_kelamin" class="form-label fw-semibold">Jenis kelamin</label><select class="form-select" id="edit_jenis_kelamin" required><option value="Pria">Pria</option><option value="Wanita">Wanita</option></select></div>
                    <div class="col-md-6"><label for="edit_tgl_lahir" class="form-label fw-semibold">Tanggal lahir</label><input type="date" class="form-control" id="edit_tgl_lahir" required></div>
                </div>
            </div>
            <div class="modal-footer"><button type="button" class="btn rm-btn-outline" data-bs-dismiss="modal">Batal</button><button type="submit" class="btn rm-btn-primary">Simpan Perubahan</button></div>
        </form></div>
    </div>
@endsection

@push('styles')
    <style>
        .rm-cs-search{max-width:330px}.rm-cs-search .input-group-text{border-radius:var(--rm-radius-pill) 0 0 var(--rm-radius-pill)}.rm-cs-search .form-control{border-radius:0 var(--rm-radius-pill) var(--rm-radius-pill) 0}
        .rm-cs-modal{border:1px solid var(--rm-line);border-radius:var(--rm-radius-lg);overflow:hidden}.rm-cs-modal .modal-header,.rm-cs-modal .modal-footer{border-color:var(--rm-line);padding:1rem 1.25rem}.rm-cs-modal .modal-body{padding:1.25rem}
        @media(max-width:575.98px){.rm-cs-search{max-width:none}}
    </style>
@endpush

@push('scripts')
    <script>
        (function () {
            const apiBase = @json(url('/api'));
            const csrf = @json(csrf_token());
            const addModal = bootstrap.Modal.getOrCreateInstance(document.getElementById('addPenitipModal'));
            const editModal = bootstrap.Modal.getOrCreateInstance(document.getElementById('editPenitipModal'));

            document.querySelectorAll('.js-edit-penitip').forEach(button => button.addEventListener('click', function () {
                document.getElementById('edit_penitip_id').value = this.dataset.id;
                document.getElementById('edit_nama').value = this.dataset.name;
                document.getElementById('edit_email').value = this.dataset.email;
                document.getElementById('edit_nomor_telepon').value = this.dataset.phone;
                document.getElementById('edit_nik').value = this.dataset.nik;
                document.getElementById('edit_jenis_kelamin').value = this.dataset.gender;
                document.getElementById('edit_tgl_lahir').value = this.dataset.birth;
                editModal.show();
            }));

            document.getElementById('addPenitipForm').addEventListener('submit', async function (event) {
                event.preventDefault();
                const button = this.querySelector('button[type="submit"]');
                button.disabled = true;
                try {
                    const response = await fetch(`${apiBase}/penitip`, { method: 'POST', headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf }, body: new FormData(this) });
                    const result = await response.json();
                    if (!response.ok || !result.success) throw new Error(result.message || 'Penitip gagal ditambahkan.');
                    location.reload();
                } catch (error) { Swal.fire({ icon: 'error', title: 'Tidak dapat menyimpan', text: error.message }); }
                finally { button.disabled = false; }
            });

            document.getElementById('editPenitipForm').addEventListener('submit', async function (event) {
                event.preventDefault();
                const id = document.getElementById('edit_penitip_id').value;
                const payload = {
                    nama_penitip: document.getElementById('edit_nama').value,
                    email: document.getElementById('edit_email').value,
                    nomor_telepon: document.getElementById('edit_nomor_telepon').value,
                    nik_penitip: document.getElementById('edit_nik').value,
                    jenis_kelamin: document.getElementById('edit_jenis_kelamin').value,
                    tgl_lahir: document.getElementById('edit_tgl_lahir').value,
                };
                try {
                    const response = await fetch(`${apiBase}/penitip/${encodeURIComponent(id)}`, { method: 'PUT', headers: { 'Accept': 'application/json', 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf }, body: JSON.stringify(payload) });
                    const result = await response.json();
                    if (!response.ok || !result.success) throw new Error(result.message || 'Data penitip gagal diperbarui.');
                    location.reload();
                } catch (error) { Swal.fire({ icon: 'error', title: 'Tidak dapat menyimpan', text: error.message }); }
            });

            document.querySelectorAll('.js-delete-penitip').forEach(button => button.addEventListener('click', async function () {
                const confirmed = await Swal.fire({ title: 'Hapus penitip?', text: this.dataset.name, icon: 'warning', showCancelButton: true, confirmButtonText: 'Hapus', cancelButtonText: 'Batal', confirmButtonColor: '#c0392b' });
                if (!confirmed.isConfirmed) return;
                try {
                    const response = await fetch(`${apiBase}/penitip/${encodeURIComponent(this.dataset.id)}`, { method: 'DELETE', headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf } });
                    const result = await response.json();
                    if (!response.ok || !result.success) throw new Error(result.message || 'Penitip gagal dihapus.');
                    location.reload();
                } catch (error) { Swal.fire({ icon: 'error', title: 'Tidak dapat menghapus', text: error.message }); }
            }));

            document.getElementById('penitipSearch').addEventListener('input', function () {
                const query = this.value.trim().toLocaleLowerCase('id-ID');
                document.querySelectorAll('#penitipRows tr[data-search]').forEach(row => row.hidden = !row.dataset.search.includes(query));
            });
        })();
    </script>
@endpush
