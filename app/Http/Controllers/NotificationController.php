<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NotificationController extends Controller
{
    /**
     * Simpan / perbarui token FCM dari aplikasi mobile.
     */
    public function storeToken(Request $request)
    {
        $validated = $request->validate([
            'id_user' => 'required|string|max:255',
            'role' => 'required|string|in:pembeli,penitip,pegawai,organisasi',
            'fcm_token' => 'required|string',
        ]);

        $existing = DB::table('fcm_tokens')
            ->where('id_user', $validated['id_user'])
            ->where('role', $validated['role'])
            ->first();

        if ($existing) {
            DB::table('fcm_tokens')->where('id', $existing->id)->update([
                'fcm_token' => $validated['fcm_token'],
                'updated_at' => now(),
            ]);
        } else {
            DB::table('fcm_tokens')->insert([
                'id_user' => $validated['id_user'],
                'role' => $validated['role'],
                'fcm_token' => $validated['fcm_token'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Token FCM berhasil disimpan.',
        ]);
    }

    /**
     * Daftar notifikasi untuk penitip (dipakai aplikasi mobile).
     */
    public function penitipNotifications($id)
    {
        $notifications = DB::table('notifications')
            ->where('recipient_type', 'penitip')
            ->where('recipient_id', $id)
            ->orderByDesc('created_at')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $notifications,
        ]);
    }
}
