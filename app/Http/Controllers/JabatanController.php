<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Jabatan;
use Illuminate\Support\Facades\DB;

class JabatanController extends Controller
{
    public function adminJabatan()
    {
        $jabatan = Jabatan::all(); 
        return view('pegawai.admin.adminJabatan', compact('jabatan'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_jabatan' => 'required|string|max:255',
        ]);

        $lastId = DB::table('jabatan')
            ->selectRaw("MAX(CAST(SUBSTRING(id_jabatan, 2) AS UNSIGNED)) as max_id")
            ->value('max_id');

        $nextId = (int) ($lastId ?? 0) + 1;
        $newId = 'J' . $nextId;

        while (Jabatan::where('id_jabatan', $newId)->exists()) {
            $newId = 'J' . ++$nextId;
        }

        \App\Models\Jabatan::create([
            'id_jabatan' => $newId,
            'nama_jabatan' => $request->nama_jabatan
        ]);

        return redirect()->back()->with('success', 'Jabatan berhasil ditambahkan.');
    }



    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'nama_jabatan' => 'required|string|max:255',
        ]);

        $jabatan = Jabatan::where('id_jabatan', $id)->firstOrFail();
        $jabatan->update([
            'nama_jabatan' => $validated['nama_jabatan']
        ]);

        return response()->json(['message' => 'Jabatan berhasil diupdate.']);
    }

    public function destroy($id)
    {
        Jabatan::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Jabatan berhasil dihapus.');
    }
}
