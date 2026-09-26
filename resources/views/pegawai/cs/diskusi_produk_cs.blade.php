@extends('layouts.workspace')

@section('title', 'Diskusi Produk — ReuseMart')
@section('page-title', 'Diskusi Produk')

@section('content')
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-end gap-3 mb-4">
        <div><h1 class="rm-page-title">Diskusi Produk</h1><p class="rm-page-sub">Bantu pembeli mendapatkan informasi sebelum membeli.</p></div>
        <span class="rm-chip"><i class="bi bi-chat-dots"></i>{{ $barang->count() }} barang tersedia</span>
    </div>

    <div class="input-group rm-discussion-search mb-3">
        <span class="input-group-text bg-white border-end-0"><i class="bi bi-search"></i></span>
        <input type="search" id="productSearch" class="form-control border-start-0" placeholder="Cari nama atau kategori barang…" aria-label="Cari barang">
    </div>

    <div class="rm-cs-product-list" id="productList">
        @forelse ($barang as $item)
            @php
                $photo = trim((string) $item->foto_barang);
                $image = ($photo !== '' && file_exists(public_path('image/' . ltrim($photo, '/')))) ? asset('image/' . ltrim($photo, '/')) : asset('image/placeholder.svg');
            @endphp
            <article class="rm-cs-product-card" data-search="{{ \Illuminate\Support\Str::lower($item->nama_barang . ' ' . $item->kategori) }}" data-id="{{ $item->id_barang }}">
                <div class="rm-cs-product-summary">
                    <img src="{{ $image }}" alt="{{ $item->nama_barang }}" loading="lazy" onerror="this.onerror=null;this.src='{{ asset('image/placeholder.svg') }}'">
                    <div class="rm-cs-product-info">
                        <h2 class="h6 fw-bold mb-1">{{ $item->nama_barang }}</h2>
                        <p class="small rm-text-muted mb-1">{{ $item->kategori }}</p>
                        <strong>Rp {{ number_format($item->harga_barang, 0, ',', '.') }}</strong>
                        <p class="rm-cs-latest mb-0">
                            @if ($item->latestKomentar)
                                <span>Komentar terbaru:</span> {{ $item->latestKomentar->komentar }}
                                <small>— {{ $item->latestKomentar->pembeli?->nama_pembeli ?? $item->latestKomentar->pegawai?->nama_pegawai ?? 'Anonim' }}</small>
                            @else
                                <span>Belum ada komentar</span>
                            @endif
                        </p>
                    </div>
                    <button type="button" class="btn rm-btn-outline rm-toggle-thread" aria-expanded="false" data-id="{{ $item->id_barang }}">
                        <i class="bi bi-chat-square-text me-1"></i><span>Lihat Diskusi</span>
                    </button>
                </div>

                <section class="rm-cs-thread d-none" id="thread-{{ $item->id_barang }}" aria-label="Diskusi untuk {{ $item->nama_barang }}">
                    <div class="rm-cs-thread-heading">
                        <div><h3 class="h6 fw-bold mb-1">Diskusi Produk</h3><p class="small rm-text-muted mb-0">Komentar pembeli dan balasan customer service.</p></div>
                        <span class="rm-chip rm-thread-count">Memuat…</span>
                    </div>
                    <div class="rm-cs-comments" data-loaded="false"><div class="rm-empty py-3"><span class="spinner-border spinner-border-sm"></span><p class="mb-0 mt-2">Memuat komentar…</p></div></div>
                    <form class="rm-cs-reply-form" data-id="{{ $item->id_barang }}" data-store-url="{{ route('diskusi.store.cs') }}">
                        @csrf
                        <input type="hidden" name="id_barang" value="{{ $item->id_barang }}">
                        <label class="form-label fw-semibold" for="reply-{{ $item->id_barang }}">Balas sebagai Customer Service</label>
                        <div class="d-flex flex-column flex-sm-row gap-2">
                            <textarea class="form-control" id="reply-{{ $item->id_barang }}" name="komentar" rows="2" maxlength="1000" placeholder="Tulis jawaban yang membantu…" required></textarea>
                            <button type="submit" class="btn rm-btn-primary align-self-sm-end"><i class="bi bi-send me-1"></i>Kirim</button>
                        </div>
                    </form>
                </section>
            </article>
        @empty
            <div class="rm-empty"><i class="bi bi-box-seam d-block"></i><p class="mb-0">Belum ada barang tersedia untuk didiskusikan.</p></div>
        @endforelse
    </div>
@endsection

@push('styles')
    <style>
        .rm-discussion-search { max-width:480px; }
        .rm-discussion-search .input-group-text { border-radius:var(--rm-radius-pill) 0 0 var(--rm-radius-pill); }
        .rm-discussion-search .form-control { border-radius:0 var(--rm-radius-pill) var(--rm-radius-pill) 0; }
        .rm-cs-product-list { display:grid;gap:1rem; }
        .rm-cs-product-card { overflow:hidden;border:1px solid var(--rm-line);border-radius:var(--rm-radius-lg);background:#fff;box-shadow:var(--rm-shadow-xs);transition:border-color var(--rm-fast),box-shadow var(--rm-fast); }
        .rm-cs-product-card:hover { border-color:var(--rm-line-strong);box-shadow:var(--rm-shadow-sm); }
        .rm-cs-product-summary { display:grid;grid-template-columns:96px minmax(0,1fr) auto;align-items:center;gap:1rem;padding:1rem 1.15rem; }
        .rm-cs-product-summary>img { width:96px;height:76px;object-fit:contain;padding:.2rem;border-radius:.7rem;background:var(--rm-primary-soft-2); }
        .rm-cs-product-info { min-width:0; }
        .rm-cs-product-info>strong { color:var(--rm-primary);font-size:.9rem; }
        .rm-cs-latest { overflow:hidden;color:var(--rm-muted);font-size:.78rem;text-overflow:ellipsis;white-space:nowrap;margin-top:.3rem; }
        .rm-cs-latest span { color:var(--rm-ink-soft);font-weight:600; }
        .rm-cs-latest small { color:var(--rm-muted); }
        .rm-cs-thread { padding:1rem 1.15rem 1.15rem;border-top:1px solid var(--rm-line);background:var(--rm-bg-soft); }
        .rm-cs-thread-heading { display:flex;justify-content:space-between;align-items:center;gap:1rem;margin-bottom:.8rem; }
        .rm-cs-comments { display:grid;gap:.55rem;max-height:360px;overflow:auto;padding:.8rem;border:1px solid var(--rm-line);border-radius:var(--rm-radius);background:#fff; }
        .rm-cs-comment { position:relative;padding:.7rem .85rem;border:1px solid var(--rm-line);border-radius:.8rem;background:var(--rm-bg-soft);overflow-wrap:anywhere; }
        .rm-cs-comment.is-staff { border-color:#c9e2de;background:var(--rm-primary-soft-2); }
        .rm-cs-comment-head { display:flex;justify-content:space-between;gap:.7rem;color:var(--rm-primary-dark);font-size:.78rem;font-weight:700; }
        .rm-cs-comment-head time { color:var(--rm-muted);font-size:.7rem;font-weight:400;white-space:nowrap; }
        .rm-cs-comment-text { margin:.35rem 0 0;white-space:pre-wrap;color:var(--rm-ink); }
        .rm-cs-comment-delete { position:absolute;right:.6rem;bottom:.45rem;border:0;background:transparent;color:var(--rm-danger);font-size:.72rem; }
        .rm-cs-reply-form { margin-top:.9rem; }
        @media(max-width:575.98px){.rm-cs-product-summary{grid-template-columns:64px minmax(0,1fr);gap:.75rem;padding:.8rem}.rm-cs-product-summary>img{width:64px;height:64px}.rm-toggle-thread{grid-column:1/-1}.rm-cs-latest{white-space:normal;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical}.rm-cs-thread{padding:.85rem}.rm-cs-comment-head{flex-direction:column;gap:.1rem}}
    </style>
@endpush

@push('scripts')
    <script>
        function addComment(container, comment, isStaff, currentEmployeeId) {
            const wrapper = document.createElement('article');
            wrapper.className = 'rm-cs-comment' + (isStaff ? ' is-staff' : '');

            const head = document.createElement('div');
            head.className = 'rm-cs-comment-head';
            const author = document.createElement('span');
            author.textContent = (isStaff ? (comment.pegawai?.nama_pegawai || comment.nama || 'Customer Service') : (comment.pembeli?.nama_pembeli || comment.nama || 'Pembeli')) + (isStaff ? ' · CS' : '');
            const time = document.createElement('time');
            time.textContent = comment.tgl_komentar ? new Date(comment.tgl_komentar).toLocaleString('id-ID', { dateStyle: 'medium', timeStyle: 'short' }) : '';
            head.append(author, time);

            const text = document.createElement('p');
            text.className = 'rm-cs-comment-text';
            text.textContent = comment.komentar || '';
            wrapper.append(head, text);

            if (isStaff && String(comment.id_pegawai || currentEmployeeId) === String(currentEmployeeId)) {
                const remove = document.createElement('button');
                remove.type = 'button';
                remove.className = 'rm-cs-comment-delete';
                remove.textContent = 'Hapus';
                remove.dataset.id = comment.id_diskusi;
                wrapper.append(remove);
            }

            container.append(wrapper);
        }

        async function loadThread(id) {
            const thread = document.getElementById('thread-' + id);
            const container = thread.querySelector('.rm-cs-comments');
            if (container.dataset.loaded === 'true') return;

            try {
                const response = await fetch(`/diskusi-produk-cs/barang/${encodeURIComponent(id)}`, { headers: { 'Accept': 'application/json' } });
                if (!response.ok) throw new Error('Komentar gagal dimuat.');
                const comments = await response.json();
                container.replaceChildren();

                if (!comments.length) {
                    const empty = document.createElement('div');
                    empty.className = 'text-center small rm-text-muted py-3';
                    empty.textContent = 'Belum ada komentar untuk barang ini.';
                    container.append(empty);
                } else {
                    comments.forEach(comment => addComment(container, comment, Boolean(comment.id_pegawai), @json(session('id_pegawai'))));
                }

                thread.querySelector('.rm-thread-count').innerHTML = '<i class="bi bi-chat-dots"></i>' + comments.length + ' komentar';
                container.dataset.loaded = 'true';
                container.scrollTop = container.scrollHeight;
            } catch (error) {
                container.textContent = error.message;
            }
        }

        document.querySelectorAll('.rm-toggle-thread').forEach(button => button.addEventListener('click', async function () {
            const thread = document.getElementById('thread-' + this.dataset.id);
            const open = thread.classList.toggle('d-none') === false;
            this.setAttribute('aria-expanded', String(open));
            this.querySelector('span').textContent = open ? 'Sembunyikan Diskusi' : 'Lihat Diskusi';
            if (open) await loadThread(this.dataset.id);
        }));

        document.querySelectorAll('.rm-cs-reply-form').forEach(form => form.addEventListener('submit', async function (event) {
            event.preventDefault();
            const button = form.querySelector('button[type="submit"]');
            button.disabled = true;
            try {
                const response = await fetch(form.dataset.storeUrl, { method: 'POST', headers: { 'Accept': 'application/json' }, body: new FormData(form) });
                const result = await response.json();
                if (!response.ok || !result.success) throw new Error(result.message || 'Komentar gagal dikirim.');

                const container = document.getElementById('thread-' + form.dataset.id).querySelector('.rm-cs-comments');
                container.querySelector('.text-center')?.remove();
                addComment(container, result, true, @json(session('id_pegawai')));
                container.scrollTop = container.scrollHeight;
                form.querySelector('textarea').value = '';
                document.getElementById('thread-' + form.dataset.id).querySelector('.rm-thread-count').innerHTML = '<i class="bi bi-chat-dots"></i>' + container.querySelectorAll('.rm-cs-comment').length + ' komentar';
                const latest = document.querySelector(`.rm-cs-product-card[data-id="${CSS.escape(form.dataset.id)}"] .rm-cs-latest`);
                if (latest) latest.textContent = `Komentar terbaru: ${result.komentar} — ${result.nama}`;
            } catch (error) {
                Swal.fire({ icon: 'error', title: 'Tidak terkirim', text: error.message });
            } finally { button.disabled = false; }
        }));

        document.getElementById('productSearch').addEventListener('input', function () {
            const value = this.value.trim().toLocaleLowerCase('id-ID');
            document.querySelectorAll('.rm-cs-product-card[data-search]').forEach(card => card.hidden = !card.dataset.search.includes(value));
        });

        document.getElementById('productList').addEventListener('click', async function (event) {
            const button = event.target.closest('.rm-cs-comment-delete');
            if (!button) return;
            const confirmation = await Swal.fire({ title: 'Hapus komentar?', text: 'Komentar ini akan dihapus dari diskusi.', icon: 'warning', showCancelButton: true, confirmButtonText: 'Hapus', cancelButtonText: 'Batal', confirmButtonColor: '#c0392b' });
            if (!confirmation.isConfirmed) return;

            try {
                const response = await fetch(`/diskusi-produk-cs/${encodeURIComponent(button.dataset.id)}`, { method: 'DELETE', headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': @json(csrf_token()) } });
                const result = await response.json();
                if (!response.ok) throw new Error(result.message || 'Komentar gagal dihapus.');
                const thread = button.closest('.rm-cs-thread');
                button.closest('.rm-cs-comment').remove();
                thread.querySelector('.rm-thread-count').innerHTML = '<i class="bi bi-chat-dots"></i>' + thread.querySelectorAll('.rm-cs-comment').length + ' komentar';
            } catch (error) { Swal.fire({ icon: 'error', title: 'Tidak dapat menghapus', text: error.message }); }
        });
    </script>
@endpush
