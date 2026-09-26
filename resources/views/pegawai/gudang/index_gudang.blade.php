@extends('layouts.workspace')

@section('title', 'Data Barang Gudang — ReuseMart')
@section('page-title', 'Data Barang Gudang')

@section('content')
    @php
        $availableCount = $barang->where('status', 'tersedia')->count();
        $soldCount = $barang->where('status', 'terjual')->count();
        $donatedCount = $barang->whereIn('status', ['didonasikan', 'barang untuk donasi'])->count();
    @endphp

    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-end gap-3 mb-4">
        <div><h1 class="rm-page-title">Data Barang Gudang</h1><p class="rm-page-sub">Kelola barang titipan, data penitip, garansi, dan status barang.</p></div>
        <span class="rm-chip"><i class="bi bi-box-seam"></i>{{ $barang->count() }} barang</span>
    </div>

    <section class="rm-workspace-card">
        <div class="d-flex flex-column gap-3 mb-3">
            <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2">
                <div><h2 class="h5 fw-bold mb-1">Inventaris</h2><p class="small rm-text-muted mb-0">Status inventaris diperbarui mengikuti proses penjualan dan donasi.</p></div>
                <button type="button" class="btn rm-btn-primary" id="openAddBarang"><i class="bi bi-plus-lg me-1"></i>Tambah Barang</button>
            </div>

            <div class="d-flex flex-column flex-lg-row justify-content-between gap-2">
                <div class="rm-inventory-filters" role="group" aria-label="Filter status barang">
                    <button type="button" data-filter="semua" class="active">Semua <span>{{ $barang->count() }}</span></button>
                    <button type="button" data-filter="tersedia">Tersedia <span>{{ $availableCount }}</span></button>
                    <button type="button" data-filter="terjual">Terjual <span>{{ $soldCount }}</span></button>
                    <button type="button" data-filter="didonasikan">Donasi <span>{{ $donatedCount }}</span></button>
                </div>
                <div class="input-group rm-inventory-search"><span class="input-group-text bg-white border-end-0"><i class="bi bi-search"></i></span><input type="search" id="inventorySearch" class="form-control border-start-0" placeholder="Cari barang atau penitip…" aria-label="Cari inventaris"></div>
            </div>
        </div>

        <div class="table-responsive rm-inventory-table-wrap">
            <table class="table align-middle">
                <thead><tr><th>Barang</th><th>Penitip</th><th>Kategori</th><th>Harga</th><th>Tanggal titip</th><th>Status</th><th>Garansi</th><th class="text-end">Aksi</th></tr></thead>
                <tbody id="inventoryRows">
                    @forelse ($barang as $item)
                        @php
                            $photo = trim((string) $item->foto_barang);
                            $image = ($photo !== '' && file_exists(public_path('image/' . ltrim($photo, '/')))) ? asset('image/' . ltrim($photo, '/')) : asset('image/placeholder.svg');
                            $status = strtolower($item->status);
                            $statusClass = match (true) {
                                $status === 'tersedia' => 'is-available',
                                $status === 'terjual' => 'is-sold',
                                str_contains($status, 'donasi') || $status === 'didonasikan' => 'is-donated',
                                default => 'is-other',
                            };
                            $filterStatus = $status === 'didonasikan' || str_contains($status, 'donasi') ? 'didonasikan' : $status;
                        @endphp
                        <tr data-status="{{ $filterStatus }}" data-search="{{ \Illuminate\Support\Str::lower($item->id_barang . ' ' . $item->nama_barang . ' ' . ($item->penitip->nama_penitip ?? '') . ' ' . $item->kategori) }}">
                            <td><div class="d-flex align-items-center gap-2"><img class="rm-inventory-thumb js-open-image" src="{{ $image }}" data-full="{{ $image }}" alt="{{ $item->nama_barang }}" loading="lazy"><div class="min-w-0"><strong>{{ $item->nama_barang }}</strong><div class="small rm-text-muted">{{ $item->id_barang }}</div></div></div></td>
                            <td>{{ $item->penitip->nama_penitip ?? '—' }}<div class="small rm-text-muted">{{ $item->id_penitip }}</div></td>
                            <td>{{ $item->kategori }}</td>
                            <td class="fw-semibold">Rp {{ number_format($item->harga_barang, 0, ',', '.') }}</td>
                            <td>{{ $item->tgl_titip?->translatedFormat('d M Y') ?? '—' }}</td>
                            <td><span class="rm-inventory-status {{ $statusClass }}">{{ ucfirst($item->status) }}</span></td>
                            <td>{{ $item->garansi ? 'Aktif' : 'Tidak' }}</td>
                            <td class="text-end"><button type="button" class="btn btn-outline-secondary btn-sm rounded-pill js-edit-barang" data-item="{{ json_encode($item) }}"><i class="bi bi-pencil me-1"></i>Edit</button></td>
                        </tr>
                    @empty
                        <tr><td colspan="8"><div class="rm-empty py-4"><i class="bi bi-box-seam d-block"></i><p class="mb-0">Belum ada barang di inventaris.</p></div></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <div class="modal fade" id="addBarangModal" tabindex="-1" aria-labelledby="addBarangTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable"><form id="addBarangForm" class="modal-content rm-warehouse-modal" action="{{ route('gudang.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="modal-header"><div><h2 class="modal-title h5 fw-bold" id="addBarangTitle">Tambah Barang Titipan</h2><p class="small rm-text-muted mb-0">Data akan ditambahkan ke inventaris dengan status tersedia.</p></div><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button></div>
            <div class="modal-body"><div class="row g-3">
                <div class="col-md-6"><label class="form-label fw-semibold" for="add_nama_barang">Nama barang</label><input id="add_nama_barang" name="nama_barang" class="form-control" maxlength="255" required></div>
                <div class="col-md-6"><label class="form-label fw-semibold" for="add_kategori">Kategori</label><select id="add_kategori" name="kategori" class="form-select" required><option value="">Pilih kategori</option>@foreach(['Elektronik & Gadget','Pakaian & Aksesori','Perabotan Rumah Tangga','Buku, Alat Tulis, & Peralatan Sekolah','Hobi, Mainan, & Koleksi','Perlengkapan Bayi & Anak','Otomotif & Aksesori','Perlengkapan Taman & Outdoor','Peralatan Kantor & Industri','Kosmetik & Perawatan Diri'] as $category)<option value="{{ $category }}">{{ $category }}</option>@endforeach</select></div>
                <div class="col-12"><label class="form-label fw-semibold" for="add_deskripsi">Deskripsi</label><textarea id="add_deskripsi" name="deskripsi_barang" class="form-control" rows="3" required></textarea></div>
                <div class="col-md-4"><label class="form-label fw-semibold" for="add_harga">Harga</label><div class="input-group"><span class="input-group-text">Rp</span><input type="number" id="add_harga" name="harga_barang" class="form-control" min="0" required></div></div>
                <div class="col-md-4"><label class="form-label fw-semibold" for="add_berat">Berat (kg)</label><input type="number" id="add_berat" name="berat_barang" class="form-control" min="0" step="0.1" required></div>
                <div class="col-md-4"><label class="form-label fw-semibold" for="add_id_penitip">Penitip</label><select id="add_id_penitip" name="id_penitip" class="form-select" required><option value="">Pilih penitip</option>@foreach($penitip as $person)<option value="{{ $person->id_penitip }}">{{ $person->id_penitip }} · {{ $person->nama_penitip }}</option>@endforeach</select></div>
                <div class="col-md-6"><label class="form-label fw-semibold" for="add_id_hunter">Hunter <span class="fw-normal rm-text-muted">(opsional)</span></label><select id="add_id_hunter" name="id_hunter" class="form-select"><option value="">Tidak ditugaskan</option>@foreach($hunter as $person)<option value="{{ $person->id_pegawai }}">{{ $person->id_pegawai }} · {{ $person->nama_pegawai }}</option>@endforeach</select></div>
                <div class="col-md-6"><label class="form-label fw-semibold" for="add_garansi">Garansi</label><select id="add_garansi" name="garansi" class="form-select"><option value="0">Tidak bergaransi</option><option value="1">Bergaransi</option></select></div>
                <div class="col-md-6 d-none" id="addWarrantyDateWrap"><label class="form-label fw-semibold" for="add_tgl_garansi">Garansi sampai</label><input type="date" id="add_tgl_garansi" name="tgl_garansi" class="form-control"></div>
                <input type="hidden" name="tgl_titip" value="{{ now()->toDateString() }}">
                <input type="hidden" name="tgl_akhir" value="{{ now()->addDays(30)->toDateString() }}">
                <input type="hidden" name="status" value="tersedia">
                <div class="col-md-6"><label class="form-label fw-semibold" for="add_foto_barang">Foto utama</label><input type="file" id="add_foto_barang" name="foto_barang" accept="image/jpeg,image/png" class="form-control"><div class="form-text">JPG/PNG, maksimal 2 MB.</div></div>
                <div class="col-md-6"><label class="form-label fw-semibold" for="add_foto_barang2">Foto kedua <span class="fw-normal rm-text-muted">(opsional)</span></label><input type="file" id="add_foto_barang2" name="foto_barang2" accept="image/jpeg,image/png" class="form-control"></div>
            </div></div>
            <div class="modal-footer"><button type="button" class="btn rm-btn-outline" data-bs-dismiss="modal">Batal</button><button type="submit" class="btn rm-btn-primary" id="addBarangSubmit"><i class="bi bi-check2 me-1"></i>Simpan Barang</button></div>
        </form></div>
    </div>

    <div class="modal fade" id="editBarangModal" tabindex="-1" aria-labelledby="editBarangTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable"><form id="editBarangForm" class="modal-content rm-warehouse-modal" method="POST" enctype="multipart/form-data">
            @csrf @method('PUT')
            <div class="modal-header"><div><h2 class="modal-title h5 fw-bold" id="editBarangTitle">Edit Barang</h2><p class="small rm-text-muted mb-0" id="editBarangSubtitle"></p></div><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button></div>
            <div class="modal-body"><div class="row g-3">
                <div class="col-md-6"><label class="form-label fw-semibold" for="edit_nama_barang">Nama barang</label><input id="edit_nama_barang" name="nama_barang" class="form-control" required></div>
                <div class="col-md-6"><label class="form-label fw-semibold" for="edit_status">Status</label><select id="edit_status" name="status" class="form-select" required><option value="tersedia">Tersedia</option><option value="terjual">Terjual</option><option value="didonasikan">Didonasikan</option><option value="barang untuk donasi">Untuk donasi</option></select></div>
                <div class="col-12"><label class="form-label fw-semibold" for="edit_deskripsi">Deskripsi</label><textarea id="edit_deskripsi" name="deskripsi_barang" class="form-control" rows="3" required></textarea></div>
                <div class="col-md-4"><label class="form-label fw-semibold" for="edit_harga">Harga</label><div class="input-group"><span class="input-group-text">Rp</span><input type="number" id="edit_harga" name="harga_barang" class="form-control" min="0" required></div></div>
                <div class="col-md-4"><label class="form-label fw-semibold" for="edit_berat">Berat (kg)</label><input type="number" id="edit_berat" name="berat_barang" class="form-control" min="0" step="0.1" required></div>
                <div class="col-md-4"><label class="form-label fw-semibold" for="edit_garansi">Garansi</label><select id="edit_garansi" name="garansi" class="form-select"><option value="0">Tidak</option><option value="1">Aktif</option></select></div>
                <div class="col-md-6 d-none" id="editWarrantyDateWrap"><label class="form-label fw-semibold" for="edit_tgl_garansi">Garansi sampai</label><input type="date" id="edit_tgl_garansi" name="tgl_garansi" class="form-control"></div>
                <div class="col-md-6"><label class="form-label fw-semibold" for="edit_foto_barang">Ganti foto utama</label><input type="file" id="edit_foto_barang" name="foto_barang" accept="image/jpeg,image/png" class="form-control"></div>
                <div class="col-md-6"><label class="form-label fw-semibold" for="edit_foto_barang2">Ganti foto kedua</label><input type="file" id="edit_foto_barang2" name="foto_barang2" accept="image/jpeg,image/png" class="form-control"></div>
            </div></div>
            <div class="modal-footer"><button type="button" class="btn rm-btn-outline" data-bs-dismiss="modal">Batal</button><button type="submit" class="btn rm-btn-primary">Simpan Perubahan</button></div>
        </form></div>
    </div>

    <div class="modal fade" id="inventoryImageModal" tabindex="-1" aria-hidden="true"><div class="modal-dialog modal-dialog-centered modal-xl"><div class="modal-content bg-transparent border-0"><div class="modal-body p-0 text-center"><img id="inventoryFullImage" class="img-fluid rounded-4" alt="Foto barang diperbesar"></div></div></div></div>
@endsection

@push('styles')
    <style>
        .rm-inventory-filters{display:flex;flex-wrap:wrap;gap:.25rem;padding:.25rem;border:1px solid var(--rm-line);border-radius:var(--rm-radius-pill);background:#fff}
        .rm-inventory-filters button{padding:.42rem .7rem;border:0;border-radius:var(--rm-radius-pill);background:transparent;color:var(--rm-ink-soft);font-size:.74rem;font-weight:600;white-space:nowrap}
        .rm-inventory-filters button span{margin-left:.2rem;padding:.1rem .35rem;border-radius:var(--rm-radius-pill);background:var(--rm-bg);font-size:.67rem}
        .rm-inventory-filters button.active{background:var(--rm-primary);color:#fff}
        .rm-inventory-filters button.active span{background:rgba(255,255,255,.16)}
        .rm-inventory-search{max-width:360px}.rm-inventory-search .input-group-text{border-radius:var(--rm-radius-pill) 0 0 var(--rm-radius-pill)}.rm-inventory-search .form-control{border-radius:0 var(--rm-radius-pill) var(--rm-radius-pill) 0}
        .rm-inventory-table-wrap{max-height:calc(100vh - 380px);min-height:300px;overflow:auto}
        .rm-inventory-table-wrap table{min-width:1020px}
        .rm-inventory-table-wrap thead{position:sticky;top:0;z-index:2}
        .rm-inventory-thumb{width:52px;height:52px;flex:0 0 52px;object-fit:contain;padding:.15rem;border-radius:.6rem;background:var(--rm-primary-soft-2);cursor:zoom-in}
        .rm-inventory-status{display:inline-flex;padding:.3rem .6rem;border-radius:var(--rm-radius-pill);font-size:.72rem;font-weight:700;white-space:nowrap}
        .rm-inventory-status.is-available{background:var(--rm-success-soft);color:var(--rm-success)}
        .rm-inventory-status.is-sold{background:var(--rm-info-soft);color:var(--rm-info)}
        .rm-inventory-status.is-donated{background:var(--rm-warning-soft);color:#76540f}
        .rm-inventory-status.is-other{background:var(--rm-bg);color:var(--rm-ink-soft)}
        .rm-warehouse-modal{border:1px solid var(--rm-line);border-radius:var(--rm-radius-lg);overflow:hidden}.rm-warehouse-modal .modal-header,.rm-warehouse-modal .modal-footer{border-color:var(--rm-line);padding:1rem 1.25rem}.rm-warehouse-modal .modal-body{padding:1.25rem}
        @media(max-width:575.98px){.rm-inventory-search{max-width:none}.rm-inventory-filters{border-radius:var(--rm-radius);justify-content:center}.rm-inventory-table-wrap{max-height:none}}
    </style>
@endpush

@push('scripts')
    <script>
        const addBarangModal = bootstrap.Modal.getOrCreateInstance(document.getElementById('addBarangModal'));
        const editBarangModal = bootstrap.Modal.getOrCreateInstance(document.getElementById('editBarangModal'));
        const addBarangForm = document.getElementById('addBarangForm');
        const editBarangForm = document.getElementById('editBarangForm');
        const reportUrlTemplate = @json(route('pegawai.laporanPenitip', ['id' => '__id__']));
        let activeFilter = 'semua';

        document.getElementById('openAddBarang').addEventListener('click', () => addBarangModal.show());

        function applyInventoryFilters() {
            const query = document.getElementById('inventorySearch').value.trim().toLocaleLowerCase('id-ID');
            document.querySelectorAll('#inventoryRows tr[data-search]').forEach(row => {
                const matchesStatus = activeFilter === 'semua' || row.dataset.status === activeFilter;
                const matchesQuery = row.dataset.search.includes(query);
                row.hidden = !(matchesStatus && matchesQuery);
            });
        }

        document.querySelectorAll('.rm-inventory-filters button').forEach(button => button.addEventListener('click', function () {
            activeFilter = this.dataset.filter;
            document.querySelectorAll('.rm-inventory-filters button').forEach(item => item.classList.toggle('active', item === this));
            applyInventoryFilters();
        }));
        document.getElementById('inventorySearch').addEventListener('input', applyInventoryFilters);

        document.getElementById('add_garansi').addEventListener('change', function () {
            document.getElementById('addWarrantyDateWrap').classList.toggle('d-none', this.value !== '1');
            document.getElementById('add_tgl_garansi').required = this.value === '1';
        });
        document.getElementById('edit_garansi').addEventListener('change', function () {
            document.getElementById('editWarrantyDateWrap').classList.toggle('d-none', this.value !== '1');
            document.getElementById('edit_tgl_garansi').required = this.value === '1';
        });

        addBarangForm.addEventListener('submit', async function (event) {
            event.preventDefault();
            const button = document.getElementById('addBarangSubmit');
            button.disabled = true;
            try {
                const response = await fetch(addBarangForm.action, { method: 'POST', headers: { 'Accept': 'application/json' }, body: new FormData(addBarangForm) });
                const result = await response.json();
                if (!response.ok || !result.message) throw new Error(result.message || 'Barang gagal ditambahkan.');
                addBarangModal.hide();

                const download = await Swal.fire({
                    icon: 'success',
                    title: 'Barang ditambahkan',
                    text: result.message,
                    showCancelButton: true,
                    confirmButtonText: 'Unduh laporan penitip',
                    cancelButtonText: 'Selesai',
                    confirmButtonColor: '#1e5b5a',
                });

                if (download.isConfirmed && result.id_penitip) {
                    try {
                        const url = reportUrlTemplate.replace('__id__', encodeURIComponent(result.id_penitip));
                        const reportResponse = await fetch(url, { headers: { 'Accept': 'application/pdf' } });
                        if (!reportResponse.ok) throw new Error('Laporan belum dapat diunduh.');
                        const blobUrl = URL.createObjectURL(await reportResponse.blob());
                        const anchor = document.createElement('a');
                        anchor.href = blobUrl;
                        anchor.download = `Laporan_Penitip_${String(result.nama_penitip || 'penitip').replace(/[^a-z0-9_-]+/gi, '_')}.pdf`;
                        document.body.append(anchor);
                        anchor.click();
                        anchor.remove();
                        URL.revokeObjectURL(blobUrl);
                    } catch (error) {
                        Swal.fire({ icon: 'error', title: 'Laporan gagal diunduh', text: error.message });
                    }
                }

                location.reload();
            } catch (error) { Swal.fire({ icon: 'error', title: 'Tidak dapat menyimpan', text: error.message }); }
            finally { button.disabled = false; }
        });

        document.querySelectorAll('.js-edit-barang').forEach(button => button.addEventListener('click', function () {
            const item = JSON.parse(this.dataset.item);
            document.getElementById('editBarangSubtitle').textContent = `${item.id_barang} · ${item.nama_barang}`;
            document.getElementById('edit_nama_barang').value = item.nama_barang || '';
            document.getElementById('edit_deskripsi').value = item.deskripsi_barang || '';
            document.getElementById('edit_harga').value = item.harga_barang || 0;
            document.getElementById('edit_berat').value = item.berat_barang || 0;
            document.getElementById('edit_status').value = item.status || 'tersedia';
            document.getElementById('edit_garansi').value = Number(item.garansi) ? '1' : '0';
            document.getElementById('edit_tgl_garansi').value = item.tgl_garansi ? String(item.tgl_garansi).slice(0, 10) : '';
            document.getElementById('edit_tgl_garansi').required = Number(item.garansi) === 1;
            document.getElementById('editWarrantyDateWrap').classList.toggle('d-none', Number(item.garansi) !== 1);
            editBarangForm.action = @json(url('/pegawai/gudang')) + '/' + encodeURIComponent(item.id_barang);
            editBarangModal.show();
        }));

        document.getElementById('editBarangForm').addEventListener('submit', function (event) {
            event.preventDefault();
            Swal.fire({ title: 'Simpan perubahan barang?', icon: 'question', showCancelButton: true, confirmButtonText: 'Simpan', cancelButtonText: 'Batal', confirmButtonColor: '#1e5b5a' }).then(result => {
                if (result.isConfirmed) HTMLFormElement.prototype.submit.call(editBarangForm);
            });
        });

        const imageModal = bootstrap.Modal.getOrCreateInstance(document.getElementById('inventoryImageModal'));
        document.querySelectorAll('.js-open-image').forEach(image => image.addEventListener('click', function () {
            document.getElementById('inventoryFullImage').src = this.dataset.full;
            imageModal.show();
        }));
    </script>
@endpush
