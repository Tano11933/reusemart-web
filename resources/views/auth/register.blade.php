@extends('layouts.auth')

@section('title', 'Daftar Akun — ReuseMart')
@section('brand-title', 'Gabung ke ReuseMart')
@section('brand-text', 'Daftar sebagai pembeli untuk berbelanja barang titipan, atau sebagai organisasi untuk mengajukan donasi barang layak pakai.')

@section('content')
    <h1 class="h4 fw-bold mb-1">Daftar Akun Baru</h1>
    <p class="rm-text-muted mb-4">Isi data di bawah ini dengan benar.</p>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0 ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" id="registerForm" enctype="multipart/form-data" action="#">
        @csrf

        <div class="mb-3">
            <label class="form-label fw-semibold" for="role">Daftar Sebagai</label>
            <select class="form-select" name="role" id="role" required>
                <option value="">-- Pilih Peran --</option>
                <option value="pembeli" @selected(old('role') === 'pembeli')>Pembeli</option>
                <option value="organisasi" @selected(old('role') === 'organisasi')>Organisasi</option>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label fw-semibold" for="nama">Nama</label>
            <input type="text" id="nama" name="nama" value="{{ old('nama') }}"
                   class="form-control @error('nama_pembeli') is-invalid @enderror @error('nama_organisasi') is-invalid @enderror"
                   required>
        </div>

        <div class="mb-3">
            <label class="form-label fw-semibold" for="nomor_telepon">Nomor Telepon</label>
            <input type="text" id="nomor_telepon" name="nomor_telepon" value="{{ old('nomor_telepon') }}"
                   class="form-control @error('nomor_telepon') is-invalid @enderror" maxlength="15" required>
            <span id="telepon-error" class="text-danger small" style="display: none;">Nomor telepon terlalu panjang (maks. 15 karakter)</span>
        </div>

        <div class="mb-3" id="form-jk" style="display: none;">
            <label class="form-label fw-semibold" for="jenis_kelamin">Jenis Kelamin</label>
            <select class="form-select @error('jenis_kelamin') is-invalid @enderror" name="jenis_kelamin" id="jenis_kelamin">
                <option value="">-- Pilih --</option>
                <option value="Wanita" @selected(old('jenis_kelamin') === 'Wanita')>Wanita</option>
                <option value="Pria" @selected(old('jenis_kelamin') === 'Pria')>Pria</option>
            </select>
        </div>

        <div class="mb-3" id="form-tgl-lahir" style="display: none;">
            <label class="form-label fw-semibold" for="tgl_lahir">Tanggal Lahir</label>
            <input type="date" id="tgl_lahir" name="tgl_lahir" value="{{ old('tgl_lahir') }}"
                   class="form-control @error('tgl_lahir') is-invalid @enderror">
        </div>

        <div class="mb-3" id="form-alamat" style="display: none;">
            <label class="form-label fw-semibold" for="alamat">Alamat</label>
            <input type="text" id="alamat" name="alamat" value="{{ old('alamat') }}"
                   class="form-control @error('alamat_organisasi') is-invalid @enderror">
        </div>

        <div class="mb-3">
            <label class="form-label fw-semibold" for="email">Email</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}"
                   class="form-control @error('email') is-invalid @enderror" required>
        </div>

        <div class="mb-3">
            <label class="form-label fw-semibold" for="password">Kata Sandi</label>
            <input type="password" id="password" name="password"
                   class="form-control @error('password') is-invalid @enderror" required>
            <span id="password-error" class="text-danger small" style="display: none;">Minimal 8 karakter</span>
        </div>

        <div class="mb-3">
            <label class="form-label fw-semibold" for="password_confirmation">Konfirmasi Kata Sandi</label>
            <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" required>
            <span id="konfirmasi-error" class="text-danger small" style="display: none;">Kata sandi harus sama dengan yang di atas</span>
        </div>

        <button type="submit" class="btn rm-btn-primary w-100 btn-lg mt-2">Daftar</button>
    </form>

    <p class="text-center rm-text-muted mt-4 mb-0">
        Sudah punya akun?
        <a href="{{ route('login') }}" class="fw-semibold text-decoration-none">Masuk di sini</a>
    </p>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const roleSelect = document.getElementById('role');
            const form = document.getElementById('registerForm');
            const nomorTeleponInput = document.getElementById('nomor_telepon');
            const teleponError = document.getElementById('telepon-error');

            const passwordInput = document.getElementById('password');
            const passwordError = document.getElementById('password-error');
            const passwordConfirmationInput = document.getElementById('password_confirmation');
            const konfirmasiError = document.getElementById('konfirmasi-error');

            function toggleFields() {
                const role = roleSelect.value;
                document.getElementById('form-alamat').style.display = role === 'organisasi' ? 'block' : 'none';
                document.getElementById('form-jk').style.display = role === 'pembeli' ? 'block' : 'none';
                document.getElementById('form-tgl-lahir').style.display = (role !== 'organisasi' && role !== '') ? 'block' : 'none';
            }

            function updateFormAction() {
                const routes = {
                    pembeli: '{{ route('pembeli.register') }}',
                    organisasi: '{{ route('register.organisasi') }}',
                };
                const selectedRole = roleSelect.value;
                form.action = routes[selectedRole] || '#';
            }

            // Validasi nomor telepon maksimal 15 karakter
            nomorTeleponInput.addEventListener('input', function () {
                if (nomorTeleponInput.value.length > 15) {
                    nomorTeleponInput.classList.add('is-invalid');
                    teleponError.style.display = 'block';
                } else {
                    nomorTeleponInput.classList.remove('is-invalid');
                    teleponError.style.display = 'none';
                }
            });

            // Validasi password minimal 8 karakter
            passwordInput.addEventListener('input', function () {
                if (passwordInput.value.length < 8) {
                    passwordInput.classList.add('is-invalid');
                    passwordError.style.display = 'block';
                } else {
                    passwordInput.classList.remove('is-invalid');
                    passwordError.style.display = 'none';
                }
            });

            // Validasi konfirmasi password
            passwordConfirmationInput.addEventListener('input', function () {
                if (passwordConfirmationInput.value !== passwordInput.value) {
                    passwordConfirmationInput.classList.add('is-invalid');
                    konfirmasiError.style.display = 'block';
                } else {
                    passwordConfirmationInput.classList.remove('is-invalid');
                    konfirmasiError.style.display = 'none';
                }
            });

            // Validasi sebelum submit
            form.addEventListener('submit', function (e) {
                let valid = true;

                if (nomorTeleponInput.value.length > 15) {
                    nomorTeleponInput.classList.add('is-invalid');
                    teleponError.style.display = 'block';
                    valid = false;
                }

                if (passwordInput.value.length < 8) {
                    passwordInput.classList.add('is-invalid');
                    passwordError.style.display = 'block';
                    valid = false;
                }

                if (passwordConfirmationInput.value !== passwordInput.value) {
                    passwordConfirmationInput.classList.add('is-invalid');
                    konfirmasiError.style.display = 'block';
                    valid = false;
                }

                if (!valid) {
                    e.preventDefault();
                }
            });

            roleSelect.addEventListener('change', function () {
                toggleFields();
                updateFormAction();
            });

            toggleFields();
            updateFormAction();
        });
    </script>
@endpush
