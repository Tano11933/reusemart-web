<?php

namespace App\Http\Controllers;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Pembeli;
use App\Models\DiskusiProduk;
use App\Models\Barang;
use App\Models\Penitip;

class BarangController extends Controller
{
    private const INVALID_PUBLIC_CATEGORIES = ['terjual', 'didonasikan', 'akan diambil', 'barang untuk donasi'];

    // ============================ GUEST ============================
    public function home()
    {
        $barang = Barang::with('penitip')
            ->where('status', 'tersedia')
            ->whereNotIn('kategori', self::INVALID_PUBLIC_CATEGORIES)
            ->orderByDesc('tgl_titip')
            ->take(8)
            ->get();

        // Hanya kategori yang benar-benar punya barang tersedia (dan bukan nilai status yang salah tempat).
        $kategoris = Barang::where('status', 'tersedia')
            ->whereNotIn('kategori', self::INVALID_PUBLIC_CATEGORIES)
            ->select('kategori')
            ->distinct()
            ->orderBy('kategori')
            ->get();

        return view('guest.home', compact('kategoris', 'barang'));
    }

    public function lihatSemua(Request $request)
    {
        $queryInput = $request->input('query');

        $barang = Barang::with('penitip')
            ->where('status', 'tersedia')
            ->whereNotIn('kategori', self::INVALID_PUBLIC_CATEGORIES);

        if ($request->has('garansi')) {
            $barang->where('garansi', $request->garansi);
        }

        if ($queryInput) {
            $barang->where(function($q) use ($queryInput) {
                $q->where('nama_barang', 'like', "%{$queryInput}%")
                ->orWhere('kategori', 'like', "%{$queryInput}%");
            });
        }

        $barang = $barang->orderByDesc('tgl_titip')->get();

        return view('guest.lihat_semua', compact('barang'));
    }

    public function barangSold()
    {
        $barang = Barang::where('status', 'terjual')->get();
        $kategoris = Barang::select('kategori')->distinct()->get();

        return view('guest.barang_terjual', compact('barang', 'kategoris'));
    }

    public function detail($id)
    {
        $item = Barang::findOrFail($id);
        $kategoris = Barang::select('kategori')->distinct()->get();

        // Barang lain di kategori yang sama untuk bagian "Barang Serupa".
        $serupa = Barang::with('penitip')
            ->where('status', 'tersedia')
            ->whereNotIn('kategori', self::INVALID_PUBLIC_CATEGORIES)
            ->where('kategori', $item->kategori)
            ->where('id_barang', '!=', $item->id_barang)
            ->orderByDesc('tgl_titip')
            ->take(4)
            ->get();

        return view('guest.detail_barang', compact('item', 'kategoris', 'serupa'));
    }

    public function showByCategory($kategori)
    {
        $barangs = Barang::with('penitip')
            ->where('status', 'tersedia')
            ->whereNotIn('kategori', self::INVALID_PUBLIC_CATEGORIES)
            ->where('kategori', $kategori)
            ->orderByDesc('tgl_titip')
            ->get();

        return view('guest.per_kategori', compact('barangs', 'kategori'));
    }

    // ============================ PEMBELI ============================
    public function homeBeli()
    {
        $barang = Barang::with('penitip')
            ->where('status', 'tersedia')
            ->whereNotIn('kategori', self::INVALID_PUBLIC_CATEGORIES)
            ->orderByDesc('tgl_titip')
            ->take(8)
            ->get();

        $pembeli = null;
        if (session()->has('user_id') && session('role') === 'pembeli') {
            $pembeli = \App\Models\Pembeli::where('id_pembeli', session('user_id'))->first();
        }

        return view('pembeli.home_beli', compact('barang', 'pembeli'));
    }

    public function lihatSemuaBeli(Request $request)
    {
        $queryInput = $request->input('query');

        $query = Barang::with('penitip')
            ->where('status', 'tersedia')
            ->whereNotIn('kategori', self::INVALID_PUBLIC_CATEGORIES);

        if ($request->garansi === '1') {
            $query->where('garansi', 1);
        }

        if ($queryInput) {
            $query->where(function ($q) use ($queryInput) {
                $q->where('nama_barang', 'like', "%{$queryInput}%")
                    ->orWhere('kategori', 'like', "%{$queryInput}%");
            });
        }

        $barang = $query->orderByDesc('tgl_titip')->get();

        $pembeli = null;
        if (session()->has('id_pembeli') && session('role') === 'pembeli') {
            $pembeli = Pembeli::where('id_pembeli', session('id_pembeli'))->first();
        }

        return view('pembeli.lihat_semua_beli', compact('barang', 'pembeli'));
    }


    public function detailBeli($id)
    {
        $barang = Barang::with('penitip')->where('id_barang', $id)
            ->where('status', 'tersedia')
            ->firstOrFail();

        $idPembeli = session('id_pembeli') ?? (session('role') === 'pembeli' ? session('user_id') : null);
        $pembeli = $idPembeli
            ? Pembeli::where('id_pembeli', $idPembeli)->first()
            : null;

        $diskusi = DiskusiProduk::where('id_barang', $id)
            ->with(['pembeli:id_pembeli,nama_pembeli', 'pegawai:id_pegawai,nama_pegawai'])
            ->orderBy('tgl_komentar', 'asc')
            ->get();

        $penitip = $barang->penitip;
        $ratingGiven = null;
        $canRate = false;

        if ($idPembeli) {
            $completedPurchase = DB::table('detail_pemesanan')
                ->join('pemesanan', 'detail_pemesanan.id_pemesanan', '=', 'pemesanan.id_pemesanan')
                ->where('pemesanan.id_pembeli', $idPembeli)
                ->where('pemesanan.status', 'transaksi selesai')
                ->where('detail_pemesanan.id_barang', $id)
                ->select('detail_pemesanan.rating_diberikan')
                ->first();

            $ratingGiven = $completedPurchase?->rating_diberikan;
            $canRate = $completedPurchase && is_null($ratingGiven);
        }

        $serupa = Barang::with('penitip')
            ->where('status', 'tersedia')
            ->whereNotIn('kategori', self::INVALID_PUBLIC_CATEGORIES)
            ->where('kategori', $barang->kategori)
            ->where('id_barang', '!=', $barang->id_barang)
            ->orderByDesc('tgl_titip')
            ->take(4)
            ->get();

        return view('pembeli.detail_barang_beli', compact(
            'barang', 'diskusi', 'pembeli', 'penitip', 'ratingGiven', 'canRate', 'serupa'
        ));
    }

    public function rate(Request $request, $idBarang)
    {
        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
        ]);
        $ratingBaru = (int) $validated['rating'];
        $idPembeli = session('id_pembeli') ?? (session('role') === 'pembeli' ? session('user_id') : null);

        if (!$idPembeli) {
            return response()->json(['success' => false, 'message' => 'Silakan masuk sebagai pembeli.'], 401);
        }

        // Cari transaksi pembeli atas barang ini
        $detail = DB::table('detail_pemesanan')
            ->join('pemesanan', 'detail_pemesanan.id_pemesanan', '=', 'pemesanan.id_pemesanan')
            ->where('pemesanan.id_pembeli', $idPembeli)
            ->where('detail_pemesanan.id_barang', $idBarang)
            ->where('pemesanan.status', 'transaksi selesai')
            ->select('detail_pemesanan.id_detail_pemesanan', 'detail_pemesanan.rating_diberikan')
            ->first();

        if (!$detail) {
            return response()->json(['success' => false, 'message' => 'Anda belum menyelesaikan pembelian.']);
        }

        if (!is_null($detail->rating_diberikan)) {
            return response()->json(['success' => false, 'message' => 'Anda sudah memberi rating untuk barang ini.']);
        }

        // Simpan rating ke detail_pemesanan
        DB::table('detail_pemesanan')
            ->where('id_detail_pemesanan', $detail->id_detail_pemesanan)
            ->update(['rating_diberikan' => $ratingBaru]);

        // Hitung ulang rating penitip dari semua barang yang sudah dirating
        $barang = Barang::findOrFail($idBarang);
        $idPenitip = $barang->id_penitip;

        // Ambil semua ID barang milik penitip ini
        $barangIds = Barang::where('id_penitip', $idPenitip)->pluck('id_barang');

        // Ambil semua rating_diberikan dari detail_pemesanan yang sudah tidak null
        $ratings = DB::table('detail_pemesanan')
            ->whereIn('id_barang', $barangIds)
            ->whereNotNull('rating_diberikan')
            ->pluck('rating_diberikan');

        $avgRating = round($ratings->avg(), 2);

        // Update rating ke penitip
        Penitip::where('id_penitip', $idPenitip)->update([
            'rating' => $avgRating
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Rating berhasil diberikan.',
            'newRating' => $avgRating,
        ]);
    }

    // ============================ CS ============================
    public function homeCs()
    {
        $barang = Barang::all();
        return view('cs.home_cs', compact('barang'));
    }

    public function lihatSemuaCs()
    {
        $barang = Barang::all();
        return view('cs.lihat_semua_cs', compact('barang'));
    }

    public function detailCs($id)
    {
        $barang = Barang::findOrFail($id);
        return view('cs.detail_barang_cs', compact('barang'));
    }

    // ============================ API JSON ============================
    public function index()
    {
        $barang = Barang::where('status', 'aktif')->get();
        return response()->json($barang);
    }

    public function searchBarang(Request $request)
    {
        $query = $request->input('query'); // Ambil query dari input pencarian
        $barang = Barang::where('status', 'tersedia') // Pastikan status barangnya tersedia
                        ->where(function($q) use ($query) {
                            $q->where('nama_barang', 'like', "%$query%") 
                            ->orWhere('kategori', 'like', "%$query%")
                            ->orWhere('id_barang', 'like', "%$query%")
                            ->orWhere('tgl_titip', 'like', "%$query%")
                            ->orWhere('deskripsi_barang', 'like', "%$query%")
                            ->orWhere('status', 'like', "%$query%")
                            ->orWhere('berat_barang', 'like', "%$query%")
                            ->orWhere('harga_barang', 'like', "%$query%");
                        })
                        ->get(); // Ambil data barang yang sesuai

        return response()->json([
            'success' => true,
            'data' => $barang
        ]);
    }

    public function show($id)
    {
        $barang = Barang::findOrFail($id);
        $now = Carbon::now();
        $tglAkhir = Carbon::parse($barang->tgl_akhir);
        $batasAmbil = $tglAkhir->copy()->addDays(7);

        // Jika belum ada tgl_ambil dan perpanjangan == 0 dan sudah lewat tgl_akhir
        if ($barang->perpanjangan == 0 && !$barang->tgl_ambil && $now->gt($tglAkhir)) {
            $barang->tgl_ambil = $batasAmbil->toDateString();
            $barang->status = 'akan diambil';
            $barang->save();
        }

        // Jika sudah lewat 7 hari setelah tgl_akhir dan belum ada tgl_ambil
        if (!$barang->tgl_ambil && $now->gt($batasAmbil)) {
            $barang->status = 'barang untuk donasi';
            $barang->save();
        }

        return view('penitip.detailBarang', compact('barang'));
    }

    public function store(Request $request)
{
    try {
        $request->validate([
            'nama_barang' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'harga' => 'required|numeric|min:0',
            'stok' => 'required|integer|min:1',
            'kategori' => 'required|string',
        ]);

        $barang = new Barang();
        $barang->user_id = Auth::id(); // atau $request->user()->id jika Auth::id() null
        $barang->nama_barang = $request->nama_barang;
        $barang->deskripsi = $request->deskripsi;
        $barang->harga = $request->harga;
        $barang->stok = $request->stok;
        $barang->kategori = $request->kategori;
        $barang->status = 'menunggu';

        $barang->save();

        Log::info('Barang berhasil disimpan: ID = ' . $barang->id);

        return response()->json(['message' => 'Barang berhasil ditambahkan dan menunggu persetujuan']);
    } catch (\Exception $e) {
        Log::error('Gagal menambahkan barang: ' . $e->getMessage());
        return response()->json(['message' => 'Terjadi kesalahan saat menyimpan barang'], 500);
    }
}

    public function update(Request $request, $id)
    {
        $barang = Barang::where('id', $id)->where('user_id', Auth::id())->firstOrFail();

        $request->validate([
            'nama_barang' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'harga' => 'required|numeric|min:0',
            'stok' => 'required|integer|min:1',
            'kategori' => 'required|string',
            'id_gudang' => 'required|exists:pegawai,id_pegawai',
            // tidak perlu validasi id_hunter karena tidak ikut diupdate
        ]);

        $barang->update([
            'nama_barang' => $request->nama_barang,
            'deskripsi' => $request->deskripsi,
            'harga' => $request->harga,
            'stok' => $request->stok,
            'kategori' => $request->kategori,
            'id_gudang' => $barang->id_gudang, 
        ]);

        return response()->json(['message' => 'Barang berhasil diperbarui']);
    }

    public function destroy($id)
    {
        $barang = Barang::where('id', $id)->where('user_id', Auth::id())->firstOrFail();
        $barang->status = 'nonaktif';
        $barang->save();

        return response()->json(['message' => 'Barang berhasil dihapus']);
    }

    public function search(Request $request)
    {
        $query = $request->input('query');
        $barang = Barang::where('status', 'tersedia')
                        ->where(function($q) use ($query) {
                            $q->where('nama_barang', 'like', "%$query%")
                              ->orWhere('kategori', 'like', "%$query%")
                              ->orWhere('deskripsi_barang', 'like', "%$query%");
                        })
                        ->get();

        return response()->json($barang);
    }

    public function perpanjangan($id)
    {
        $barang = Barang::findOrFail($id);

        $batas = Carbon::parse($barang->tgl_akhir);
        $now = Carbon::now();
        $hari_terlewati = $now->diffInDays($batas, false);

        if ($hari_terlewati < -7) {
            $barang->status = 'didonasikan';
            $barang->save();
            return back()->with('error', 'Barang telah melewati masa perpanjangan dan didonasikan.');
        }

        $batasBaru = $batas->copy()->addDays(30);
        $barang->tgl_akhir = $batasBaru;
        $barang->perpanjangan = 1;
        $barang->save();

        return back()->with('success', 'Berhasil memperpanjang durasi penitipan. Tanggal akhir baru: ' . $batasBaru->translatedFormat('d F Y'));
    }



    public function ambilBarang(Request $request, $id)
    {
        $barang = Barang::findOrFail($id);
        $barang->status = 'akan diambil';
        $barang->tgl_ambil = $request->tgl_ambil;
        $barang->save();

        return redirect()->back()->with('success', 'Barang berhasil dijadwalkan untuk diambil.');
    }

    public function ambilOtomatis($id)
    {
        $barang = Barang::findOrFail($id);
        $tgl_akhir = \Carbon\Carbon::parse($barang->tgl_akhir);
        $tgl_ambil = $tgl_akhir->addDays(7);

        $barang->tgl_ambil = $tgl_ambil;
        $barang->status = 'akan diambil';
        $barang->save();

        return redirect()->back()->with('success', 'Barang dijadwalkan untuk diambil sebelum ' . $tgl_ambil->translatedFormat('d F Y'));
    }

    public function barangTersedia(Request $request)
    {
        $query = Barang::where('status', 'tersedia');

        if ($request->has('garansi')) {
            $query->where('garansi', (int) $request->garansi);
        }


        $barang = $query->get();

        return response()->json($barang);
    }

    public function getByHunter($id)
    {
        $barang = Barang::with('penitip') // ⬅️ penting: eager load penitip
                    ->where('id_hunter', $id)
                    ->get();

        return response()->json($barang);
    }




}
