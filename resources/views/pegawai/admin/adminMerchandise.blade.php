@extends('layouts.workspace')

@section('title', 'Data Merchandise — ReuseMart')
@section('page-title', 'Data Merchandise')

@section('content')
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-end gap-3 mb-4">
        <div><h1 class="rm-page-title">Data Merchandise</h1><p class="rm-page-sub">Kelola stok hadiah yang dapat ditukar dengan poin.</p></div>
        <span class="rm-chip"><i class="bi bi-gift"></i>{{ $merchandise->count() }} merchandise</span>
    </div>

    <section class="rm-workspace-card">
        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3 mb-3">
            <div><h2 class="h5 fw-bold mb-1">Katalog Hadiah</h2><p class="small rm-text-muted mb-0">Perbarui jumlah stok dan biaya poin.</p></div>
            <div class="d-flex flex-column flex-sm-row gap-2">
                <div class="input-group rm-admin-search"><span class="input-group-text bg-white border-end-0"><i class="bi bi-search"></i></span><input type="search" id="merchandiseSearch" class="form-control border-start-0" placeholder="Cari merchandise…" aria-label="Cari merchandise"></div>
                <button type="button" class="btn rm-btn-primary text-nowrap" data-bs-toggle="modal" data-bs-target="#addMerchandiseModal"><i class="bi bi-plus-lg me-1"></i>Tambah</button>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table align-middle">
                <thead><tr><th>Hadiah</th><th>Stok</th><th>Harga Poin</th><th class="text-end">Aksi</th></tr></thead>
                <tbody id="merchandiseRows">
                    @forelse ($merchandise as $item)
                        @php
                            $photo = trim((string) $item->gambar_merch);
                            $image = ($photo !== '' && file_exists(public_path('storage/merchandise/' . $photo))) ? asset('storage/merchandise/' . $photo) : asset('image/placeholder.svg');
                        @endphp
                        <tr data-search="{{ \Illuminate\Support\Str::lower($item->id_merch . ' ' . $item->nama_merch) }}">
                            <td><div class="d-flex align-items-center gap-3"><img class="rm-merch-thumb" src="{{ $image }}" alt="{{ $item->nama_merch }}" onerror="this.onerror=null;this.src='{{ asset('image/placeholder.svg') }}'"><div><strong>{{ $item->nama_merch }}</strong><div class="small rm-text-muted">{{ $item->id_merch }}</div></div></div></td>
                            <td><span class="rm-stock-pill {{ $item->stok > 0 ? 'has-stock' : 'out-of-stock' }}">{{ $item->stok > 0 ? $item->stok . ' tersedia' : 'Habis' }}</span></td>
                            <td class="fw-semibold">{{ number_format($item->harga_poin, 0, ',', '.') }} poin</td>
                            <td class="text-end"><div class="d-inline-flex gap-2"><button type="button" class="btn btn-outline-secondary btn-sm rounded-pill js-edit-merchandise" data-id="{{ $item->id_merch }}" data-name="{{ $item->nama_merch }}" data-stock="{{ $item->stok }}" data-points="{{ $item->harga_poin }}" data-image="{{ $image }}"><i class="bi bi-pencil me-1"></i>Edit</button><form action="{{ route('merchandise.destroy', $item->id_merch) }}" method="POST" class="js-delete-merchandise">@csrf @method('DELETE')<button type="submit" class="btn btn-outline-danger btn-sm rounded-pill"><i class="bi bi-trash me-1"></i>Hapus</button></form></div></td>
                        </tr>
                    @empty
                        <tr><td colspan="4"><div class="rm-empty py-4"><i class="bi bi-gift d-block"></i><p class="mb-0">Belum ada merchandise.</p></div></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <div class="modal fade" id="addMerchandiseModal" tabindex="-1" aria-labelledby="addMerchandiseTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered"><form action="{{ route('merchandise.store') }}" method="POST" enctype="multipart/form-data" class="modal-content rm-admin-modal">
            @csrf
            <div class="modal-header"><div><h2 class="modal-title h5 fw-bold" id="addMerchandiseTitle">Tambah Merchandise</h2><p class="small rm-text-muted mb-0">Masukkan hadiah baru ke katalog.</p></div><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button></div>
            <div class="modal-body">
                <div class="mb-3"><label for="add_merch_name" class="form-label fw-semibold">Nama hadiah</label><input id="add_merch_name" name="nama_merch" class="form-control" maxlength="255" required></div>
                <div class="row g-3 mb-3"><div class="col-6"><label for="add_merch_stock" class="form-label fw-semibold">Stok</label><input type="number" id="add_merch_stock" name="stok" class="form-control" min="0" required></div><div class="col-6"><label for="add_merch_points" class="form-label fw-semibold">Harga poin</label><input type="number" id="add_merch_points" name="harga_poin" class="form-control" min="0" required></div></div>
                <div><label for="add_merch_image" class="form-label fw-semibold">Foto hadiah</label><input type="file" id="add_merch_image" name="gambar_merch" class="form-control" accept="image/jpeg,image/png" required><div class="form-text">JPG atau PNG, maksimal 2 MB.</div></div>
            </div>
            <div class="modal-footer"><button type="button" class="btn rm-btn-outline" data-bs-dismiss="modal">Batal</button><button type="submit" class="btn rm-btn-primary">Simpan Hadiah</button></div>
        </form></div>
    </div>

    <div class="modal fade" id="editMerchandiseModal" tabindex="-1" aria-labelledby="editMerchandiseTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered"><form id="editMerchandiseForm" class="modal-content rm-admin-modal" enctype="multipart/form-data">
            <div class="modal-header"><div><h2 class="modal-title h5 fw-bold" id="editMerchandiseTitle">Edit Merchandise</h2><p class="small rm-text-muted mb-0">Ubah stok, harga poin, atau foto.</p></div><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button></div>
            <div class="modal-body">
                <input type="hidden" id="edit_merch_id">
                <div class="d-flex align-items-center gap-3 mb-3"><img id="edit_merch_preview" class="rm-merch-thumb" src="" alt="Pratinjau merchandise"><span class="small rm-text-muted">Foto saat ini</span></div>
                <div class="mb-3"><label for="edit_merch_name" class="form-label fw-semibold">Nama hadiah</label><input id="edit_merch_name" name="nama_merch" class="form-control" maxlength="255" required></div>
                <div class="row g-3 mb-3"><div class="col-6"><label for="edit_merch_stock" class="form-label fw-semibold">Stok</label><input type="number" id="edit_merch_stock" name="stok" class="form-control" min="0" required></div><div class="col-6"><label for="edit_merch_points" class="form-label fw-semibold">Harga poin</label><input type="number" id="edit_merch_points" name="harga_poin" class="form-control" min="0" required></div></div>
                <div><label for="edit_merch_image" class="form-label fw-semibold">Ganti foto (opsional)</label><input type="file" id="edit_merch_image" name="gambar_merch" class="form-control" accept="image/jpeg,image/png"></div>
            </div>
            <div class="modal-footer"><button type="button" class="btn rm-btn-outline" data-bs-dismiss="modal">Batal</button><button type="submit" class="btn rm-btn-primary">Simpan Perubahan</button></div>
        </form></div>
    </div>
@endsection

@push('styles')
    <style>
        .rm-admin-search{max-width:270px}.rm-admin-search .input-group-text{border-radius:var(--rm-radius-pill) 0 0 var(--rm-radius-pill)}.rm-admin-search .form-control{border-radius:0 var(--rm-radius-pill) var(--rm-radius-pill) 0}
        .rm-admin-modal{border:1px solid var(--rm-line);border-radius:var(--rm-radius-lg);overflow:hidden}.rm-admin-modal .modal-header,.rm-admin-modal .modal-footer{border-color:var(--rm-line);padding:1rem 1.25rem}.rm-admin-modal .modal-body{padding:1.25rem}
        .rm-merch-thumb{width:58px;height:58px;object-fit:contain;padding:.2rem;border-radius:.65rem;background:var(--rm-primary-soft-2)}
        .rm-stock-pill{display:inline-flex;padding:.32rem .65rem;border-radius:var(--rm-radius-pill);font-size:.74rem;font-weight:700;white-space:nowrap}.rm-stock-pill.has-stock{background:var(--rm-success-soft);color:var(--rm-success)}.rm-stock-pill.out-of-stock{background:var(--rm-danger-soft);color:var(--rm-danger)}
        @media(max-width:575.98px){.rm-admin-search{max-width:none}}
    </style>
@endpush

@push('scripts')
    <script>
        const editMerchModal = bootstrap.Modal.getOrCreateInstance(document.getElementById('editMerchandiseModal'));
        document.querySelectorAll('.js-edit-merchandise').forEach(button => button.addEventListener('click', function () {
            document.getElementById('edit_merch_id').value = this.dataset.id;
            document.getElementById('edit_merch_name').value = this.dataset.name;
            document.getElementById('edit_merch_stock').value = this.dataset.stock;
            document.getElementById('edit_merch_points').value = this.dataset.points;
            document.getElementById('edit_merch_preview').src = this.dataset.image;
            editMerchModal.show();
        }));

        document.getElementById('editMerchandiseForm').addEventListener('submit', async function (event) {
            event.preventDefault();
            const id = document.getElementById('edit_merch_id').value;
            const payload = new FormData(this);
            payload.append('_method', 'PUT');
            try {
                const response = await fetch(@json(url('/merchandise')) + '/' + encodeURIComponent(id), {
                    method: 'POST',
                    headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': @json(csrf_token()) },
                    body: payload,
                });
                const result = await response.json();
                if (!response.ok) throw new Error(result.message || 'Gagal memperbarui merchandise.');
                location.reload();
            } catch (error) { Swal.fire({ icon: 'error', title: 'Tidak dapat menyimpan', text: error.message }); }
        });

        document.querySelectorAll('.js-delete-merchandise').forEach(form => form.addEventListener('submit', function (event) {
            event.preventDefault();
            Swal.fire({ title: 'Hapus merchandise?', text: 'Barang ini tidak lagi dapat ditukar.', icon: 'warning', showCancelButton: true, confirmButtonText: 'Hapus', cancelButtonText: 'Batal', confirmButtonColor: '#c0392b' }).then(result => {
                if (result.isConfirmed) HTMLFormElement.prototype.submit.call(form);
            });
        }));

        document.getElementById('merchandiseSearch').addEventListener('input', function () {
            const query = this.value.trim().toLocaleLowerCase('id-ID');
            document.querySelectorAll('#merchandiseRows tr[data-search]').forEach(row => row.hidden = !row.dataset.search.includes(query));
        });
    </script>
@endpush
