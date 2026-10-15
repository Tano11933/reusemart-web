<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Login dari aplikasi mobile (Flutter) memakai endpoint yang sama dengan web.
        // Endpoint ini harus bebas CSRF agar request JSON dari aplikasi tidak kena 419.
        $middleware->validateCsrfTokens(except: [
            'api/login',
        ]);

        // Pembatas akses: "role" untuk semua peran pengguna, "jabatan" untuk
        // pegawai (dipakai setelah "role:pegawai").
        $middleware->alias([
            'role' => \App\Http\Middleware\EnsureRole::class,
            'jabatan' => \App\Http\Middleware\EnsureJabatan::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
