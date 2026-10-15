<?php

namespace App\Http\Middleware\Concerns;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * Logika bersama middleware akses: mengarahkan pengguna ke halaman yang sesuai
 * dengan perannya, atau membalas JSON untuk permintaan fetch/klien mobile.
 */
trait RedirectsToDashboard
{
    protected function denyGuest(Request $request): JsonResponse|RedirectResponse
    {
        if ($request->expectsJson()) {
            return response()->json(['message' => 'Silakan masuk terlebih dahulu.'], 401);
        }

        return redirect()->route('login')->with('error', 'Silakan masuk terlebih dahulu untuk membuka halaman itu.');
    }

    protected function denyAccess(Request $request, string $message): JsonResponse|RedirectResponse
    {
        if ($request->expectsJson()) {
            return response()->json(['message' => $message], 403);
        }

        return redirect()->to($this->dashboardUrl($request))->with('error', $message);
    }

    protected function dashboardUrl(Request $request): string
    {
        return match ($request->session()->get('role')) {
            'pembeli' => route('home_beli'),
            'penitip' => route('penitip.barangTitipan'),
            'organisasi' => route('organisasi.dashboard'),
            'pegawai' => match ($request->session()->get('jabatan')) {
                'Admin' => route('pegawai.admin'),
                'Customer Service' => route('pegawai.cs'),
                'Owner' => route('owner.requestRDonasi'),
                default => route('pegawai.home'),
            },
            default => url('/'),
        };
    }
}
