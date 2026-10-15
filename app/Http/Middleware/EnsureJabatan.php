<?php

namespace App\Http\Middleware;

use App\Http\Middleware\Concerns\RedirectsToDashboard;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureJabatan
{
    use RedirectsToDashboard;

    /**
     * Batasi rute pegawai hanya untuk jabatan tertentu, mis. "jabatan:Gudang".
     * Dipakai setelah middleware "role:pegawai".
     */
    public function handle(Request $request, Closure $next, string ...$jabatan): Response
    {
        if (! $request->session()->get('role')) {
            return $this->denyGuest($request);
        }

        if (! in_array($request->session()->get('jabatan'), $jabatan, true)) {
            return $this->denyAccess($request, 'Jabatan kamu tidak punya akses ke halaman tersebut.');
        }

        return $next($request);
    }
}
