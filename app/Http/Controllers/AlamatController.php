<?php

namespace App\Http\Controllers;

use App\Models\Alamat;
use App\Models\Pembeli;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AlamatController extends Controller
{
    private function isOwner(string $userId): bool
    {
        $sessionId = session('id_pembeli') ?? (session('role') === 'pembeli' ? session('user_id') : null);

        return $sessionId !== null && hash_equals((string) $sessionId, $userId);
    }

    public function index($userId)
    {
        if (!$this->isOwner((string) $userId)) {
            return response()->json(['success' => false, 'message' => 'Tidak memiliki akses ke alamat ini.'], 403);
        }

        $alamat = Alamat::where('id_pembeli', $userId)
            ->orderByDesc('default_alamat')
            ->orderBy('label_alamat')
            ->get();

        return response()->json(['success' => true, 'data' => $alamat]);
    }

    public function store(Request $request, $userId)
    {
        if (!$this->isOwner((string) $userId)) {
            return response()->json(['success' => false, 'message' => 'Tidak memiliki akses ke alamat ini.'], 403);
        }

        $validated = $request->validate([
            'label_alamat' => 'required|string|max:80',
            'kecamatan' => 'required|string|max:120',
            'kabupaten' => 'required|string|max:120',
            'detail_alamat' => 'required|string|max:500',
            'desa' => 'required|string|max:120',
            'default_alamat' => 'required|boolean',
        ]);

        if (!Pembeli::where('id_pembeli', $userId)->exists()) {
            return response()->json(['success' => false, 'message' => 'Pembeli tidak ditemukan.'], 404);
        }

        $lastIdNumber = Alamat::query()
            ->where('id_alamat', 'like', 'A%')
            ->selectRaw('MAX(CAST(SUBSTRING(id_alamat, 2) AS UNSIGNED)) as max_id')
            ->value('max_id');
        $newIdAlamat = 'A' . (($lastIdNumber ?? 0) + 1);

        DB::transaction(function () use ($validated, $userId, $newIdAlamat) {
            if ($validated['default_alamat']) {
                Alamat::where('id_pembeli', $userId)->update(['default_alamat' => false]);
            }

            Alamat::create([
                ...$validated,
                'id_alamat' => $newIdAlamat,
                'id_pembeli' => $userId,
            ]);
        });

        return response()->json([
            'success' => true,
            'message' => 'Alamat berhasil ditambahkan.',
            'id_alamat' => $newIdAlamat,
        ]);
    }

    public function update(Request $request, $userId, $idAlamat)
    {
        if (!$this->isOwner((string) $userId)) {
            return response()->json(['success' => false, 'message' => 'Tidak memiliki akses ke alamat ini.'], 403);
        }

        $alamat = Alamat::where('id_pembeli', $userId)
            ->where('id_alamat', $idAlamat)
            ->first();

        if (!$alamat) {
            return response()->json(['success' => false, 'message' => 'Alamat tidak ditemukan.'], 404);
        }

        $validated = $request->validate([
            'label_alamat' => 'required|string|max:80',
            'kecamatan' => 'required|string|max:120',
            'kabupaten' => 'required|string|max:120',
            'detail_alamat' => 'required|string|max:500',
            'desa' => 'required|string|max:120',
            'default_alamat' => 'required|boolean',
        ]);

        DB::transaction(function () use ($alamat, $validated, $userId) {
            if ($validated['default_alamat']) {
                Alamat::where('id_pembeli', $userId)
                    ->where('id_alamat', '!=', $alamat->id_alamat)
                    ->update(['default_alamat' => false]);
            }

            $alamat->update($validated);
        });

        return response()->json(['success' => true, 'message' => 'Alamat berhasil diperbarui.']);
    }

    public function destroy($userId, $idAlamat)
    {
        if (!$this->isOwner((string) $userId)) {
            return response()->json(['success' => false, 'message' => 'Tidak memiliki akses ke alamat ini.'], 403);
        }

        $alamat = Alamat::where('id_pembeli', $userId)
            ->where('id_alamat', $idAlamat)
            ->first();

        if (!$alamat) {
            return response()->json(['success' => false, 'message' => 'Alamat tidak ditemukan.'], 404);
        }

        $wasDefault = $alamat->default_alamat;
        $alamat->delete();

        if ($wasDefault) {
            Alamat::where('id_pembeli', $userId)
                ->orderBy('id_alamat')
                ->limit(1)
                ->update(['default_alamat' => true]);
        }

        return response()->json(['success' => true, 'message' => 'Alamat berhasil dihapus.']);
    }
}
