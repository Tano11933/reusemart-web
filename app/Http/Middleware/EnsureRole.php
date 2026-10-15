<?php

namespace App\Http\Middleware;

use App\Http\Middleware\Concerns\RedirectsToDashboard;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureRole
{
    use RedirectsToDashboard;

    /**
     * Batasi rute hanya untuk peran tertentu, mis. "role:pembeli".
     * Pengguna yang belum masuk diarahkan ke halaman login; peran yang
     * tidak cocok dikembalikan ke dasbor miliknya sendiri.
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $role = $request->session()->get('role');

        if (! $role) {
            return $this->denyGuest($request);
        }

        if (! in_array($role, $roles, true)) {
            return $this->denyAccess($request, 'Akun kamu tidak punya akses ke halaman tersebut.');
        }

        return $next($request);
    }
}
