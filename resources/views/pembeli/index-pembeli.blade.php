@extends('layouts.buyer')

@section('title', 'Kelola Alamat · ReuseMart')

@section('content')
    <section class="container py-4 py-lg-5">
        <nav class="rm-breadcrumb small mb-3" aria-label="Breadcrumb">
            <a href="{{ route('home_beli') }}">Home</a>
            <span class="mx-1">/</span>
            <a href="{{ route('pembeli-profile', ['id' => $pembeli->id_pembeli]) }}">Profil Saya</a>
            <span class="mx-1">/</span>
            <span class="rm-text-muted">Kelola Alamat</span>
        </nav>

        <div class="mb-4">
            <h1 class="rm-page-title">Kelola Alamat</h1>
            <p class="rm-page-sub">Simpan alamat agar proses checkout dan pengiriman lebih mudah.</p>
        </div>

        <div id="addressAlert" class="alert d-none" role="status" aria-live="polite"></div>

        <div class="row g-4 align-items-start">
            <div class="col-lg-5">
                <section class="rm-address-form-card">
                    <div class="d-flex align-items-center gap-3 mb-4">
                        <span class="rm-address-form-icon"><i class="bi bi-geo-alt"></i></span>
                        <div>
                            <h2 class="h5 fw-bold mb-1">Tambah Alamat Baru</h2>
                            <p class="small rm-text-muted mb-0">Isi alamat penerima dengan lengkap.</p>
                        </div>
                    </div>

                    <form id="alamatForm">
                        <div class="mb-3">
                            <label for="label_alamat" class="form-label fw-semibold">Label alamat</label>
                            <input type="text" id="label_alamat" name="label_alamat" class="form-control" placeholder="Rumah, kantor, kos…" maxlength="80" required>
                        </div>
                        <div class="row g-3">
                            <div class="col-sm-6 mb-1">
                                <label for="desa" class="form-label fw-semibold">Desa / kelurahan</label>
                                <input type="text" id="desa" name="desa" class="form-control" maxlength="120" required>
                            </div>
                            <div class="col-sm-6 mb-1">
                                <label for="kecamatan" class="form-label fw-semibold">Kecamatan</label>
                                <input type="text" id="kecamatan" name="kecamatan" class="form-control" maxlength="120" required>
                            </div>
                            <div class="col-12 mb-1">
                                <label for="kabupaten" class="form-label fw-semibold">Kabupaten / kota</label>
                                <input type="text" id="kabupaten" name="kabupaten" class="form-control" maxlength="120" required>
                            </div>
                            <div class="col-12 mb-1">
                                <label for="detail_alamat" class="form-label fw-semibold">Detail alamat</label>
                                <textarea id="detail_alamat" name="detail_alamat" class="form-control" rows="3" maxlength="500" placeholder="Nama jalan, nomor rumah, RT/RW, patokan…" required></textarea>
                            </div>
                        </div>

                        <div class="form-check form-switch my-3">
                            <input class="form-check-input" type="checkbox" role="switch" id="default_alamat" name="default_alamat" value="1">
                            <label class="form-check-label" for="default_alamat">Jadikan alamat utama</label>
                        </div>

                        <button type="submit" class="btn rm-btn-primary w-100" id="saveAddressButton">
                            <i class="bi bi-plus-lg me-1"></i>Simpan Alamat
                        </button>
                    </form>
                </section>
            </div>

            <div class="col-lg-7">
                <section>
                    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3 mb-3">
                        <div>
                            <h2 class="h5 fw-bold mb-1">Alamat Tersimpan</h2>
                            <p class="small rm-text-muted mb-0" id="addressCount">Memuat alamat…</p>
                        </div>
                        <div class="input-group rm-address-search">
                            <span class="input-group-text bg-white border-end-0"><i class="bi bi-search"></i></span>
                            <input type="search" id="searchAlamat" class="form-control border-start-0" placeholder="Cari alamat…" aria-label="Cari alamat">
                        </div>
                    </div>

                    <div id="alamatList" class="rm-address-list" aria-live="polite">
                        <div class="rm-empty py-4">
                            <span class="spinner-border spinner-border-sm text-success" role="status"></span>
                            <p class="mb-0 mt-2">Memuat alamat…</p>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </section>

    <div class="modal fade" id="editAlamatModal" tabindex="-1" aria-labelledby="editAlamatModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form id="editAlamatForm" class="modal-content rm-modal-card">
                <div class="modal-header">
                    <div>
                        <h2 class="modal-title h5 fw-bold" id="editAlamatModalLabel">Edit Alamat</h2>
                        <p class="small rm-text-muted mb-0">Perbarui detail alamat penerima.</p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="edit_id_alamat" name="id_alamat">
                    <div class="mb-3">
                        <label for="edit_label_alamat" class="form-label fw-semibold">Label alamat</label>
                        <input type="text" id="edit_label_alamat" name="label_alamat" class="form-control" maxlength="80" required>
                    </div>
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <label for="edit_desa" class="form-label fw-semibold">Desa / kelurahan</label>
                            <input type="text" id="edit_desa" name="desa" class="form-control" maxlength="120" required>
                        </div>
                        <div class="col-sm-6">
                            <label for="edit_kecamatan" class="form-label fw-semibold">Kecamatan</label>
                            <input type="text" id="edit_kecamatan" name="kecamatan" class="form-control" maxlength="120" required>
                        </div>
                        <div class="col-12">
                            <label for="edit_kabupaten" class="form-label fw-semibold">Kabupaten / kota</label>
                            <input type="text" id="edit_kabupaten" name="kabupaten" class="form-control" maxlength="120" required>
                        </div>
                        <div class="col-12">
                            <label for="edit_detail_alamat" class="form-label fw-semibold">Detail alamat</label>
                            <textarea id="edit_detail_alamat" name="detail_alamat" class="form-control" rows="3" maxlength="500" required></textarea>
                        </div>
                    </div>
                    <div class="form-check form-switch mt-3">
                        <input class="form-check-input" type="checkbox" role="switch" id="edit_default_alamat" name="default_alamat" value="1">
                        <label class="form-check-label" for="edit_default_alamat">Jadikan alamat utama</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn rm-btn-outline" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn rm-btn-primary"><i class="bi bi-check2 me-1"></i>Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .rm-address-form-card,
        .rm-address-card {
            background: #fff;
            border: 1px solid var(--rm-line);
            border-radius: var(--rm-radius-lg);
            box-shadow: var(--rm-shadow-xs);
            padding: 1.35rem;
        }

        .rm-address-form-card {
            position: sticky;
            top: 92px;
        }

        .rm-address-form-icon {
            width: 48px;
            height: 48px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex: 0 0 48px;
            border-radius: 50%;
            background: var(--rm-primary-soft);
            color: var(--rm-primary);
            font-size: 1.3rem;
        }

        .rm-address-search {
            max-width: 260px;
        }

        .rm-address-search .input-group-text {
            border-radius: var(--rm-radius-pill) 0 0 var(--rm-radius-pill);
        }

        .rm-address-search .form-control {
            border-radius: 0 var(--rm-radius-pill) var(--rm-radius-pill) 0;
        }

        .rm-address-list {
            display: grid;
            gap: 0.85rem;
        }

        .rm-address-card {
            padding: 1.1rem 1.2rem;
            transition: border-color var(--rm-fast), box-shadow var(--rm-fast);
        }

        .rm-address-card:hover {
            border-color: var(--rm-line-strong);
            box-shadow: var(--rm-shadow-sm);
        }

        .rm-address-heading {
            display: flex;
            align-items: center;
            gap: 0.6rem;
        }

        .rm-address-pin {
            width: 36px;
            height: 36px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex: 0 0 36px;
            border-radius: 50%;
            color: var(--rm-primary);
            background: var(--rm-primary-soft);
        }

        .rm-address-copy {
            color: var(--rm-ink-soft);
            font-size: 0.9rem;
            line-height: 1.5;
            margin: 0.8rem 0 0 2.8rem;
            overflow-wrap: anywhere;
        }

        .rm-address-actions {
            display: flex;
            gap: 0.4rem;
        }

        .rm-address-actions .btn {
            min-width: 40px;
            min-height: 40px;
            padding: 0.35rem 0.65rem;
        }

        .rm-modal-card {
            border: 1px solid var(--rm-line);
            border-radius: var(--rm-radius-lg);
            overflow: hidden;
        }

        .rm-modal-card .modal-header,
        .rm-modal-card .modal-footer {
            border-color: var(--rm-line);
            padding: 1rem 1.25rem;
        }

        .rm-modal-card .modal-body {
            padding: 1.25rem;
        }

        @media (max-width: 991.98px) {
            .rm-address-form-card {
                position: static;
            }
        }

        @media (max-width: 575.98px) {
            .rm-address-search {
                max-width: none;
            }

            .rm-address-card {
                padding: 1rem;
            }

            .rm-address-copy {
                margin-left: 0;
            }
        }
    </style>
@endpush

@push('scripts')
    <script>
        (function () {
            const addressUrl = @json(url('/pembeli/' . $pembeli->id_pembeli . '/alamat'));
            const csrfToken = @json(csrf_token());
            const form = document.getElementById('alamatForm');
            const list = document.getElementById('alamatList');
            const alertBox = document.getElementById('addressAlert');
            const editModal = bootstrap.Modal.getOrCreateInstance(document.getElementById('editAlamatModal'));
            const editForm = document.getElementById('editAlamatForm');
            const search = document.getElementById('searchAlamat');
            let addresses = [];

            function notify(message, type = 'success') {
                alertBox.className = 'alert alert-' + type;
                alertBox.textContent = message;
                alertBox.classList.remove('d-none');
                alertBox.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                window.setTimeout(() => alertBox.classList.add('d-none'), 4500);
            }

            async function request(url, options = {}) {
                const response = await fetch(url, {
                    ...options,
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        ...(options.body instanceof FormData ? {} : { 'Content-Type': 'application/json' }),
                        ...options.headers,
                    },
                });
                const data = await response.json();

                if (!response.ok || data.success === false) {
                    const validation = data.errors ? Object.values(data.errors).flat()[0] : null;
                    throw new Error(validation || data.message || 'Permintaan gagal. Coba lagi.');
                }

                return data;
            }

            function makeText(tag, className, value) {
                const element = document.createElement(tag);
                if (className) element.className = className;
                element.textContent = value ?? '';
                return element;
            }

            function renderAddresses() {
                const query = search.value.trim().toLocaleLowerCase('id-ID');
                const filtered = addresses.filter(address => {
                    const searchable = [address.label_alamat, address.detail_alamat, address.desa, address.kecamatan, address.kabupaten]
                        .join(' ').toLocaleLowerCase('id-ID');
                    return searchable.includes(query);
                });

                document.getElementById('addressCount').textContent = `${addresses.length} alamat tersimpan`;
                list.replaceChildren();

                if (!filtered.length) {
                    const empty = document.createElement('div');
                    empty.className = 'rm-empty py-4';
                    const icon = document.createElement('i');
                    icon.className = 'bi bi-geo-alt d-block';
                    const message = makeText('p', 'mb-0', addresses.length ? 'Tidak ada alamat yang cocok dengan pencarian.' : 'Belum ada alamat tersimpan. Tambahkan alamat penerima di samping.');
                    empty.append(icon, message);
                    list.append(empty);
                    return;
                }

                filtered.forEach(address => {
                    const card = document.createElement('article');
                    card.className = 'rm-address-card';

                    const top = document.createElement('div');
                    top.className = 'd-flex justify-content-between align-items-start gap-2';

                    const heading = document.createElement('div');
                    heading.className = 'rm-address-heading';
                    const pin = document.createElement('span');
                    pin.className = 'rm-address-pin';
                    pin.innerHTML = '<i class="bi bi-geo-alt-fill"></i>';
                    const headingText = document.createElement('div');
                    const titleRow = document.createElement('div');
                    titleRow.className = 'd-flex align-items-center flex-wrap gap-2';
                    titleRow.append(makeText('h3', 'h6 fw-bold mb-0', address.label_alamat));
                    if (address.default_alamat) {
                        const badge = makeText('span', 'badge rounded-pill text-bg-success', 'Utama');
                        titleRow.append(badge);
                    }
                    headingText.append(titleRow, makeText('div', 'small rm-text-muted', `${address.desa}, ${address.kecamatan}`));
                    heading.append(pin, headingText);

                    const actions = document.createElement('div');
                    actions.className = 'rm-address-actions';
                    const editButton = document.createElement('button');
                    editButton.type = 'button';
                    editButton.className = 'btn btn-outline-secondary btn-sm rounded-pill';
                    editButton.dataset.action = 'edit';
                    editButton.dataset.id = address.id_alamat;
                    editButton.setAttribute('aria-label', 'Edit alamat ' + address.label_alamat);
                    editButton.innerHTML = '<i class="bi bi-pencil"></i><span class="ms-1">Edit</span>';
                    const deleteButton = document.createElement('button');
                    deleteButton.type = 'button';
                    deleteButton.className = 'btn btn-outline-danger btn-sm rounded-pill';
                    deleteButton.dataset.action = 'delete';
                    deleteButton.dataset.id = address.id_alamat;
                    deleteButton.setAttribute('aria-label', 'Hapus alamat ' + address.label_alamat);
                    deleteButton.innerHTML = '<i class="bi bi-trash"></i><span class="ms-1">Hapus</span>';
                    actions.append(editButton, deleteButton);
                    top.append(heading, actions);

                    const detail = makeText('p', 'rm-address-copy', `${address.detail_alamat}, ${address.desa}, ${address.kecamatan}, ${address.kabupaten}`);
                    card.append(top, detail);
                    list.append(card);
                });
            }

            async function loadAddresses() {
                try {
                    const response = await request(addressUrl, { method: 'GET' });
                    addresses = response.data || [];
                    renderAddresses();
                } catch (error) {
                    notify(error.message, 'danger');
                    list.innerHTML = '<div class="rm-empty py-4"><i class="bi bi-exclamation-circle d-block"></i><p class="mb-0">Alamat gagal dimuat.</p></div>';
                }
            }

            function formDataToAddress(targetForm) {
                const data = new FormData(targetForm);
                return {
                    label_alamat: data.get('label_alamat'),
                    kecamatan: data.get('kecamatan'),
                    kabupaten: data.get('kabupaten'),
                    detail_alamat: data.get('detail_alamat'),
                    desa: data.get('desa'),
                    default_alamat: data.get('default_alamat') === '1',
                };
            }

            form.addEventListener('submit', async function (event) {
                event.preventDefault();
                const button = document.getElementById('saveAddressButton');
                button.disabled = true;

                try {
                    const response = await request(addressUrl, {
                        method: 'POST',
                        body: JSON.stringify(formDataToAddress(form)),
                    });
                    form.reset();
                    notify(response.message || 'Alamat berhasil ditambahkan.');
                    await loadAddresses();
                } catch (error) {
                    notify(error.message, 'danger');
                } finally {
                    button.disabled = false;
                }
            });

            editForm.addEventListener('submit', async function (event) {
                event.preventDefault();
                const id = document.getElementById('edit_id_alamat').value;
                const button = editForm.querySelector('button[type="submit"]');
                button.disabled = true;

                try {
                    const response = await request(`${addressUrl}/${encodeURIComponent(id)}`, {
                        method: 'PUT',
                        body: JSON.stringify(formDataToAddress(editForm)),
                    });
                    editModal.hide();
                    notify(response.message || 'Alamat berhasil diperbarui.');
                    await loadAddresses();
                } catch (error) {
                    notify(error.message, 'danger');
                } finally {
                    button.disabled = false;
                }
            });

            list.addEventListener('click', async function (event) {
                const button = event.target.closest('button[data-action]');
                if (!button) return;

                const address = addresses.find(item => String(item.id_alamat) === button.dataset.id);
                if (!address) return;

                if (button.dataset.action === 'edit') {
                    document.getElementById('edit_id_alamat').value = address.id_alamat;
                    document.getElementById('edit_label_alamat').value = address.label_alamat || '';
                    document.getElementById('edit_desa').value = address.desa || '';
                    document.getElementById('edit_kecamatan').value = address.kecamatan || '';
                    document.getElementById('edit_kabupaten').value = address.kabupaten || '';
                    document.getElementById('edit_detail_alamat').value = address.detail_alamat || '';
                    document.getElementById('edit_default_alamat').checked = Boolean(address.default_alamat);
                    editModal.show();
                    return;
                }

                const confirmation = await Swal.fire({
                    title: 'Hapus alamat ini?',
                    text: address.label_alamat,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Hapus alamat',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: '#c0392b',
                });

                if (!confirmation.isConfirmed) return;

                try {
                    const response = await request(`${addressUrl}/${encodeURIComponent(address.id_alamat)}`, { method: 'DELETE' });
                    notify(response.message || 'Alamat berhasil dihapus.');
                    await loadAddresses();
                } catch (error) {
                    notify(error.message, 'danger');
                }
            });

            search.addEventListener('input', renderAddresses);
            loadAddresses();
        })();
    </script>
@endpush
