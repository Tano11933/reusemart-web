@extends('layouts.auth')

@section('title', 'Masuk · ReuseMart')
@section('brand-title', 'Selamat Datang Kembali')
@section('brand-text', 'Masuk untuk melanjutkan belanja, memantau titipan, atau mengelola operasional ReuseMart sesuai peranmu.')

@section('content')
    <h1 class="h4 fw-bold mb-1">Masuk ke akunmu</h1>
    <p class="rm-text-muted mb-4">Gunakan email dan kata sandi yang terdaftar.</p>

    <div id="alert" aria-live="polite">
        @if (session('success'))
            <div class="alert alert-success py-2 mb-3" role="alert">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger py-2 mb-3" role="alert">{{ session('error') }}</div>
        @endif
    </div>

    <form id="loginForm" method="POST" action="{{ route('login') }}">
        @csrf
        <div class="mb-3">
            <label class="form-label fw-semibold" for="email">Email</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                <input type="email" class="form-control" id="email" name="email" required autofocus
                       autocomplete="email" placeholder="nama@email.com">
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label fw-semibold" for="password">Kata Sandi</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-lock"></i></span>
                <input type="password" class="form-control" id="password" name="password" required
                       autocomplete="current-password" placeholder="••••••••">
                <button class="btn btn-outline-secondary" type="button" id="togglePassword" aria-label="Tampilkan kata sandi">
                    <i class="bi bi-eye" id="togglePasswordIcon"></i>
                </button>
            </div>
        </div>

        <button type="submit" class="btn rm-btn-primary w-100 btn-lg mt-2" id="loginButton">Masuk</button>
    </form>

    <p class="text-center rm-text-muted mt-4 mb-0">
        Belum punya akun?
        <a href="{{ route('register') }}" class="fw-semibold text-decoration-none">Daftar sekarang</a>
    </p>
@endsection

@push('scripts')
    <script>
        (function () {
            const form = document.getElementById('loginForm');
            const alertBox = document.getElementById('alert');
            const button = document.getElementById('loginButton');
            const toggle = document.getElementById('togglePassword');
            const toggleIcon = document.getElementById('togglePasswordIcon');
            const password = document.getElementById('password');

            toggle.addEventListener('click', function () {
                const show = password.type === 'password';
                password.type = show ? 'text' : 'password';
                toggleIcon.className = show ? 'bi bi-eye-slash' : 'bi bi-eye';
            });

            function showAlert(type, message) {
                alertBox.innerHTML = '<div class="alert alert-' + type + ' py-2 mb-3" role="alert">' + message + '</div>';
            }

            function resetButton() {
                button.disabled = false;
                button.textContent = 'Masuk';
            }

            form.addEventListener('submit', function (e) {
                e.preventDefault();

                button.disabled = true;
                button.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Memproses…';

                fetch('{{ url('/api/login') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    },
                    body: JSON.stringify({
                        email: document.getElementById('email').value,
                        password: password.value,
                    }),
                })
                    .then(function (res) { return res.json(); })
                    .then(function (res) {
                        if (res.success) {
                            localStorage.setItem('id_user', res.id_user);
                            localStorage.setItem('user_id', res.id_user);
                            localStorage.setItem('nama', res.nama);
                            localStorage.setItem('role', res.role);

                            if (res.id_pembeli) {
                                localStorage.setItem('id_pembeli', res.id_pembeli);
                            }

                            if (res.role === 'penitip') {
                                localStorage.setItem('id_penitip', res.id_user);
                            }

                            showAlert('success', 'Login berhasil sebagai <strong>' + res.role + '</strong>. Mengalihkan…');

                            setTimeout(function () {
                                window.location.href = res.redirect;
                            }, 900);
                        } else {
                            showAlert('danger', res.message || 'Email atau kata sandi salah.');
                            resetButton();
                        }
                    })
                    .catch(function (err) {
                        console.error('Error saat login:', err);
                        showAlert('danger', 'Terjadi kesalahan saat login. Coba lagi.');
                        resetButton();
                    });
            });
        })();
    </script>
@endpush
