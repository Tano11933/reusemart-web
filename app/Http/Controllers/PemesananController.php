<?php

namespace App\Http\Controllers;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Pemesanan;
use App\Models\Pegawai;
use App\Models\Pembeli;
use App\Models\Alamat;
use App\Models\DetailPemesanan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\Komisi;
use App\Notifications\NotifikasiPengambilanBarang;
use PDF;

class PemesananController extends Controller
{
    public function transaksiDiambil()
    {
        $pemesananList = Pemesanan::with(['pembeli', 'detailPemesanans.barang'])
            ->where('jenis_pengantaran', 'diambil')
            ->where('status_pembayaran', 'valid')
            ->where('status', 'disiapkan')
            ->get();

        foreach ($pemesananList as $pemesanan) {
            $batasAmbil = Carbon::parse($pemesanan->tgl_pembayaran)->addDays(3); // hari ke-3 = hari terakhir ambil
            if (now()->greaterThan($batasAmbil) && $pemesanan->status !== 'transaksi selesai' && $pemesanan->status !== 'hangus') {
                $pemesanan->status = 'hangus';
                $pemesanan->save();

                // Ubah status semua barang jadi "barang untuk donasi"
                foreach ($pemesanan->detailPemesanans as $detail) {
                if ($detail->barang) {
                    \Log::info('Barang ditemukan: ' . $detail->barang->nama_barang);
                    $detail->barang->status = 'barang untuk donasi';
                    $detail->barang->save();
                } else {
                    \Log::warning('Barang tidak ditemukan untuk detail ID: ' . $detail->id);
                }
            }

            }
        }

        return view('pegawai.gudang.transaksiDiambil', compact('pemesananList'));
    }



    public function pemesananDiambil($id)
    {
        $pemesanan = Pemesanan::with('detailPemesanans.barang')->findOrFail($id);

        $batasAmbil = Carbon::parse($pemesanan->tgl_pesan)->addDays(3);
        if (now()->lessThanOrEqualTo($batasAmbil)) {
            $pemesanan->status = 'transaksi selesai';
        } else {
            $pemesanan->status = 'hangus';

            foreach ($pemesanan->detailPemesanans as $detail) {
                if ($detail->barang) {
                    $detail->barang->status = 'barang untuk donasi';
                    $detail->barang->save();
                }
            }
        }

        $pemesanan->save();

        // 💡 Tambahkan perhitungan dan penyimpanan komisi
        foreach ($pemesanan->detailPemesanans as $detail) {
            $barang = $detail->barang;
            if (!$barang) continue;

            $harga = $detail->harga;

            // Jika belum diperpanjang dan ada hunter
            if ($barang->perpanjangan == 0 && $barang->id_hunter) {
                $komisiHunter = $harga * 0.05;
                $komisiPerusahaan = $harga * 0.15;

                Komisi::create([
                    'id_pemesanan' => $pemesanan->id_pemesanan,
                    'id_pegawai' => $barang->id_hunter,
                    'komisi_perusahaan' => $komisiPerusahaan,
                    'komisi_hunter' => $komisiHunter,
                    'bonus' => 0
                ]);
            }

            // Jika diperpanjang, perusahaan mendapat 25%
            elseif ($barang->perpanjangan == 1) {
                $komisiPerusahaan = $harga * 0.25;

                Komisi::create([
                    'id_pemesanan' => $pemesanan->id_pemesanan,
                    'id_pegawai' => null,
                    'komisi_perusahaan' => $komisiPerusahaan,
                    'komisi_hunter' => 0,
                    'bonus' => 0
                ]);
            }
        }

        return redirect()->back()->with('success', 'Status pemesanan berhasil diperbarui dan komisi tercatat.');
    }



    public function transaksiDikirim()
    {
        $pemesanan = Pemesanan::with(['pembeli', 'detailPemesanans.barang'])
            ->where('jenis_pengantaran', 'dikirim')
            ->where('status_pembayaran', 'valid')
            ->where('status', 'disiapkan')
            ->get();

        $kurirList = Pegawai::whereHas('jabatan', function ($query) {
            $query->where('nama_jabatan', 'Kurir');
        })->get();

        return view('pegawai.gudang.transaksiDikirim', compact('pemesanan', 'kurirList'));
    }

    public function konfirmasiPengiriman(Request $request, $id)
    {
        $request->validate([
            'id_kurir' => 'required|exists:pegawai,id_pegawai',
        ]);

        // Ambil data pemesanan lengkap
        $pemesanan = Pemesanan::with('detailPemesanans.barang.penitip')->findOrFail($id);

        // Update status dan id kurir
        $pemesanan->status = 'proses pengiriman';
        $pemesanan->id_pegawai = $request->id_kurir;
        $pemesanan->save();

        $lastIdNumber = Komisi::where('id_komisi', 'like', 'K%')
            ->selectRaw("MAX(CAST(SUBSTRING(id_komisi, 2) AS UNSIGNED)) as max_id")
            ->value('max_id');
        $nextIdNumber = $lastIdNumber ? $lastIdNumber + 1 : 1;
        $counter = 0;

        foreach ($pemesanan->detailPemesanans as $detail) {
            $barang = $detail->barang;
            if ($barang) {
                $harga = $detail->harga;
                $komisiPerusahaan = 0;
                $komisiHunter = 0;
                $idPegawai = null;


                // Perhitungan saldo penitip
                $penitip = $barang->penitip;
                $bonus = 0;

                if ($penitip) {
                    if ($barang->perpanjangan == 0 && $barang->id_hunter) {
                        // jika belum diperpanjang dan ada hunter
                        $komisiPerusahaan = 0.15 * $harga;
                        $komisiHunter = 0.05 * $harga;
                        $idPegawai = $barang->id_hunter;
                        $pendapatanPenitip = $harga * 0.80;
                    } elseif ($barang->perpanjangan == 0 && !$barang->id_hunter) {
                        // belum diperpanjang tapi TIDAK ADA hunter
                        $komisiPerusahaan = 0.20 * $harga;
                        $komisiHunter = 0;
                        $pendapatanPenitip = $harga * 0.80;
                    } else if($barang->perpanjangan == 1 && $barang->id_hunter) {
                        // jika sudah diperpanjang dan ada hunter
                        $komisiPerusahaan = 0.25 * $harga;
                        $komisiHunter = 0.05 * $harga;
                        $idPegawai = $barang->id_hunter;
                        $pendapatanPenitip = $harga * 0.70;
                    }elseif ($barang->perpanjangan == 1) {
                        // jika sudah diperpanjang (apapun status hunter-nya)
                        $komisiPerusahaan = 0.30 * $harga;
                        $komisiHunter = 0;
                        $pendapatanPenitip = $harga * 0.70;
                    }
                    // Tambahkan bonus jika tgl_laku - tgl_titip < 7 hari
                    $tglTitip = $barang->tgl_titip;
                    $tglLaku = $barang->tgl_laku;
                    if ($tglTitip && $tglLaku && \Carbon\Carbon::parse($tglTitip)->diffInDays($tglLaku) < 7) {
                        $komisiPerusahaanAwal = $komisiPerusahaan;
                        $bonus = 0.10 * $komisiPerusahaanAwal;
                        $komisiPerusahaan -= $bonus;
                        $penitip->saldo += $bonus;
                    }

                    // Tambahkan pendapatan ke saldo penitip
                    $penitip->saldo += $pendapatanPenitip;
                    $penitip->save();
                }

                // Buat ID komisi otomatis
                $idKomisi = 'K' . ($nextIdNumber + $counter);
                $counter++;

                Komisi::create([
                    'id_komisi' => $idKomisi,
                    'id_pemesanan' => $pemesanan->id_pemesanan,
                    'id_barang' => $barang->id_barang,
                    'id_pegawai' => $idPegawai,
                    'komisi_perusahaan' => $komisiPerusahaan,
                    'komisi_hunter' => $komisiHunter,
                    'bonus' => $bonus
                ]);
            }
        }

        return redirect()->route('transaksi.dikirim')->with('success', 'Kurir berhasil ditugaskan dan komisi tercatat.');
    }

    public function jadwalkanPengiriman(Request $request, $id)
    {
        $request->validate([
            'tgl_kirim' => 'required|date|after_or_equal:today',
        ]);

        $pemesanan = Pemesanan::findOrFail($id);

        // Check if tgl_kirim is already set. If it's already set, do not allow further changes.
        if ($pemesanan->tgl_kirim) {
            return redirect()->route('transaksi.dikirim')->with('error', 'Tanggal pengiriman sudah dijadwalkan.');
        }

        $pemesanan->tgl_kirim = $request->tgl_kirim;
        $pemesanan->save();

        return redirect()->route('transaksi.dikirim')->with('success', 'Tanggal pengiriman berhasil dijadwalkan.');
    }



    public function tandaiDiambil($id)
    {
        $pemesanan = Pemesanan::with('pembeli', 'detailPemesanans.barang.penitip')->findOrFail($id);
        $pemesanan->status = 'transaksi selesai';
        $pemesanan->tgl_ambil = now();
        $pemesanan->save();

        // $pembeli = $pemesanan->pembeli;
        // if ($pembeli) {
        //     $pesanPembeli = 'Pesanan Anda dengan ID ' . $pemesanan->id_pemesanan . ' telah diambil.';
        //     $pembeli->notify(new NotifikasiPengambilanBarang($pesanPembeli));
        // }

        // $penitipIds = $pemesanan->detailPemesanans->pluck('barang.penitip.id')->filter()->unique();
        // foreach ($penitipIds as $idPenitip) {
        //     $penitip = \App\Models\Penitip::find($idPenitip);
        //     if ($penitip) {
        //         $pesanPenitip = 'Barang Anda pada pesanan ID ' . $pemesanan->id_pemesanan . ' telah diambil oleh pembeli.';
        //         $penitip->notify(new NotifikasiPengambilanBarang($pesanPenitip));
        //     }
        // }

        // Ambil nomor ID komisi terakhir
        $lastIdNumber = Komisi::where('id_komisi', 'like', 'K%')
            ->selectRaw("MAX(CAST(SUBSTRING(id_komisi, 2) AS UNSIGNED)) as max_id")
            ->value('max_id');
        $nextIdNumber = $lastIdNumber ? $lastIdNumber + 1 : 1;
        $counter = 0;

        foreach ($pemesanan->detailPemesanans as $detail) {
            $barang = $detail->barang;
            if ($barang) {
                $harga = $detail->harga;
                $komisiPerusahaan = 0;
                $komisiHunter = 0;
                $idPegawai = null;


                // Perhitungan saldo penitip
                $penitip = $barang->penitip;
                $bonus = 0;

                if ($penitip) {
                    if ($barang->perpanjangan == 0 && $barang->id_hunter) {
                        // jika belum diperpanjang dan ada hunter
                        $komisiPerusahaan = 0.15 * $harga;
                        $komisiHunter = 0.05 * $harga;
                        $idPegawai = $barang->id_hunter;
                        $pendapatanPenitip = $harga * 0.80;
                    } elseif ($barang->perpanjangan == 0 && !$barang->id_hunter) {
                        // belum diperpanjang tapi TIDAK ADA hunter
                        $komisiPerusahaan = 0.20 * $harga;
                        $komisiHunter = 0;
                        $pendapatanPenitip = $harga * 0.80;
                    } else if($barang->perpanjangan == 1 && $barang->id_hunter) {
                        // jika sudah diperpanjang dan ada hunter
                        $komisiPerusahaan = 0.25 * $harga;
                        $komisiHunter = 0.05 * $harga;
                        $idPegawai = $barang->id_hunter;
                        $pendapatanPenitip = $harga * 0.70;
                    }elseif ($barang->perpanjangan == 1) {
                        // jika sudah diperpanjang (apapun status hunter-nya)
                        $komisiPerusahaan = 0.30 * $harga;
                        $komisiHunter = 0;
                        $pendapatanPenitip = $harga * 0.70;
                    }
                    // Tambahkan bonus jika tgl_laku - tgl_titip < 7 hari
                    $tglTitip = $barang->tgl_titip;
                    $tglLaku = $barang->tgl_laku;
                    if ($tglTitip && $tglLaku && \Carbon\Carbon::parse($tglTitip)->diffInDays($tglLaku) < 7) {
                        $komisiPerusahaanAwal = $komisiPerusahaan;
                        $bonus = 0.10 * $komisiPerusahaanAwal;
                        $komisiPerusahaan -= $bonus;
                        $penitip->saldo += $bonus;
                    }

                    // Tambahkan pendapatan ke saldo penitip
                    $penitip->saldo += $pendapatanPenitip;
                    $penitip->save();
                }

                // Buat ID komisi otomatis
                $idKomisi = 'K' . ($nextIdNumber + $counter);
                $counter++;

                Komisi::create([
                    'id_komisi' => $idKomisi,
                    'id_pemesanan' => $pemesanan->id_pemesanan,
                    'id_barang' => $barang->id_barang,
                    'id_pegawai' => $idPegawai,
                    'komisi_perusahaan' => $komisiPerusahaan,
                    'komisi_hunter' => $komisiHunter,
                    'bonus' => $bonus
                ]);
            }
        }



        return redirect()->back()->with('success', 'Transaksi selesai dan komisi berhasil ditambahkan.');
    }


    public function notaPemesanan(Request $request)
    {
        $filter = $request->query('filter');

        $query = Pemesanan::with('pembeli')
            ->where(function ($q) {
                $q->where('status', 'proses pengiriman')
                ->orWhere('status', 'transaksi selesai');
            });

        if ($filter === 'dikirim') {
            $query->where('jenis_pengantaran', 'dikirim');
        } elseif ($filter === 'diambil') {
            $query->where('jenis_pengantaran', 'diambil');
        }

        $pemesananList = $query->get();

        return view('pegawai.gudang.notaPemesanan', compact('pemesananList'));
    }

    public function downloadNota($id)
    {
        $pemesanan = Pemesanan::with(['pembeli', 'detailPemesanans.barang'])->findOrFail($id);

        // Cek jenis pengantaran
        if ($pemesanan->jenis_pengantaran === 'diambil') {
            $pdf = PDF::loadView('nota.nota_diambil', compact('pemesanan'));
        } else {
            $pdf = PDF::loadView('nota.nota_pengiriman', compact('pemesanan'));
        }

        return $pdf->download('nota_Pemesanan' . $pemesanan->id_pemesanan . '.pdf');
    }

    public function index()
    {
        $cartItems = session('cart', []);
        $idPembeli = session('id_pembeli') ?? (session('role') === 'pembeli' ? session('user_id') : null);
        $pembeli = \App\Models\Pembeli::find($idPembeli);

        if (!$pembeli) {
            return redirect()->route('login')->with('error', 'Silakan masuk sebagai pembeli untuk checkout.');
        }

        if (empty($cartItems)) {
            return redirect()->route('cart.index')->with('error', 'Keranjang masih kosong.');
        }

        return view('pembeli.checkout', compact('cartItems', 'pembeli'));
    }

    public function store(Request $request)
    {
        $userId = session('id_pembeli') ?? (session('role') === 'pembeli' ? session('user_id') : null);
        if (!$userId) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $user = Pembeli::find($userId);
        if (!$user) {
            return redirect()->route('login')->with('error', 'Akun pembeli tidak ditemukan. Silakan masuk kembali.');
        }

        $cart = session('cart', []);
        if (empty($cart)) {
            return redirect()->back()->with('error', 'Keranjang kosong.');
        }

        $barangTersedia = \App\Models\Barang::whereIn('id_barang', array_keys($cart))
            ->where('status', 'tersedia')
            ->get()
            ->keyBy('id_barang');

        if ($barangTersedia->count() !== count($cart)) {
            return redirect()->route('cart.index')->with('error', 'Sebagian barang sudah tidak tersedia. Periksa kembali keranjangmu.');
        }

        // Ambil harga terbaru dari database agar nilai di session tidak bisa usang atau diubah klien.
        foreach ($cart as $idBarang => &$item) {
            $barang = $barangTersedia->get($idBarang);
            $item['nama_barang'] = $barang->nama_barang;
            $item['harga_barang'] = $barang->harga_barang;
            $item['foto_barang'] = $barang->foto_barang;
            $item['status'] = $barang->status;
            $item['quantity'] = 1;
        }
        unset($item);
        session(['cart' => $cart]);

        $request->validate([
            'jenis_pengantaran' => 'required|in:dikirim,diambil',
            'alamat_pengiriman' => 'required_if:jenis_pengantaran,dikirim|nullable|string',
            'pakai_poin' => 'required|in:ya,tidak',
            'poin_ditukar' => 'nullable|integer|min:0',
        ]);

        // Hitung total harga barang
        $totalHargaBarang = 0;
        foreach ($cart as $item) {
            $totalHargaBarang += $item['harga_barang'] * $item['quantity'];
        }

        // Jika jenis pengantaran "ambil", ongkir otomatis 0
        $jenisPengantaran = $request->input('jenis_pengantaran');
        if ($jenisPengantaran === 'dikirim' && !Alamat::where('id_alamat', $request->input('alamat_pengiriman'))
            ->where('id_pembeli', $userId)
            ->exists()) {
            return back()->withInput()->withErrors(['alamat_pengiriman' => 'Pilih alamat pengiriman yang tersimpan di akunmu.']);
        }
        $ongkir = ($jenisPengantaran === 'diambil') ? 0 : (($totalHargaBarang >= 1500000) ? 0 : 100000);

        // Hitung poin yang dipakai dan validasi batas maksimal
        $pakaiPoin = $request->input('pakai_poin');
        $poinDitukar = ($pakaiPoin === 'ya') ? (int) $request->input('poin_ditukar', 0) : 0;

        $poinUser = $user->poin ?? 0;

        if ($poinDitukar > $poinUser) {
            $poinDitukar = $poinUser;
        }

        $potonganHarga = $poinDitukar * 10000; // 1 poin = Rp 10.000

        // Hitung harga setelah ongkir dan potongan poin
        $hargaSetelahOngkir = $totalHargaBarang + $ongkir;
        $totalHargaAkhir = max(0, $hargaSetelahOngkir - $potonganHarga);

        // Hitung poin dasar: 1 poin per Rp 10.000 dari total harga barang (tidak termasuk ongkir & potongan)
        $poinDasar = floor($totalHargaBarang / 10000);

        // Bonus 20% poin jika total bayar (setelah ongkir & potongan) lebih dari 500.000
        $bonusPoin = ($totalHargaAkhir > 500000) ? floor($poinDasar * 0.2) : 0;

        // Total poin yang didapatkan dari transaksi ini
        $totalPoin = $poinDasar + $bonusPoin;

        // Generate id_pemesanan dengan format YYYY.MM.N
        $year = date('Y');
        $month = (int)date('m');

        $lastNumber = Pemesanan::whereRaw("YEAR(tgl_pesan) = ?", [$year])
            ->whereRaw("MONTH(tgl_pesan) = ?", [$month])
            ->select(DB::raw("MAX(CAST(SUBSTRING_INDEX(id_pemesanan, '.', -1) AS UNSIGNED)) as max_nomor"))
            ->value('max_nomor');

        $nextNumber = $lastNumber ? $lastNumber + 1 : 1;

        $idPemesanan = $year . '.' . $month . '.' . $nextNumber;

        $pemesanan = Pemesanan::create([
            'id_pemesanan'        => $idPemesanan,
            'id_pembeli'          => $userId,
            'tgl_pesan'           => now(),
            'status'              => 'menunggu pembayaran',
            'jenis_pengantaran'   => $jenisPengantaran,
            'total_harga_pesanan' => $totalHargaBarang,
            'total_ongkir'        => $ongkir,
            'harga_setelah_ongkir'=> $hargaSetelahOngkir,
            'potongan_harga'      => $potonganHarga,
            'total_harga'         => $totalHargaAkhir,
            'poin_pesanan'        => $totalPoin, // simpan total poin
            'alamat_pengiriman'   => $request->input('alamat_pengiriman', null),
        ]);

        foreach ($cart as $idBarang => $item) {
            DetailPemesanan::create([
                'id_detail_pemesanan' => 'DP' . strtoupper(Str::random(10)),
                'id_pemesanan'        => $pemesanan->id_pemesanan,
                'id_barang'           => $idBarang,
                'harga'               => $item['harga_barang'] * $item['quantity'],
            ]);
        }

        // *Jangan kurangi poin di sini!*

        session()->forget('cart');

        return redirect()->route('checkout.payment', $pemesanan->id_pemesanan)
                         ->with('success', 'Pemesanan berhasil dibuat. Silakan lakukan pembayaran.');
    }

    public function showPaymentForm($id)
    {
        $pemesanan = Pemesanan::with('detailPemesanans.barang')->findOrFail($id);
        $idPembeli = session('id_pembeli') ?? (session('role') === 'pembeli' ? session('user_id') : null);

        if (!$idPembeli || $pemesanan->id_pembeli !== $idPembeli) {
            return redirect()->route('login')->with('error', 'Silakan masuk ke akun pembeli yang membuat pesanan ini.');
        }

        $pembeli = Pembeli::findOrFail($idPembeli);

        return view('pembeli.konfirmasi-pembayaran', compact('pemesanan', 'pembeli'));
    }

    public function submitPayment(Request $request, $id)
    {
        $pemesanan = Pemesanan::findOrFail($id);

        $idPembeli = session('id_pembeli') ?? (session('role') === 'pembeli' ? session('user_id') : null);
        if (!$idPembeli || $pemesanan->id_pembeli !== $idPembeli) {
            abort(403, 'Tidak memiliki akses ke pesanan ini.');
        }

        $user = $pemesanan->pembeli;

        $request->validate([
            'bukti_transfer' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'metode_pembayaran' => 'required|in:bank,dana',
        ]);

        $filePath = $request->file('bukti_transfer')->store('bukti_transfers', 'public');

        $pemesanan->update([
            'bukti_transfer' => $filePath,
            'metode_pembayaran' => $request->input('metode_pembayaran'),
            'tgl_pembayaran' => now(),
            'status' => 'diproses',
            'status_pembayaran' => 'menunggu verifikasi',
        ]);

        // Update status barang jadi 'diproses' dan isi tgl_laku
        foreach ($pemesanan->detailPemesanans as $detail) {
            $barang = $detail->barang;
            if ($barang) {
                $barang->status = 'diproses';
                $barang->tgl_laku = $pemesanan->tgl_pembayaran;
                $barang->save();
            }
        }

        return redirect()->route('checkout.success')->with('success', 'Pembayaran berhasil dikonfirmasi.');
    }

    public function cancelPayment(Request $request, $id)
    {
        $pemesanan = Pemesanan::with('detailPemesanans')->findOrFail($id);
        $idPembeli = session('id_pembeli') ?? (session('role') === 'pembeli' ? session('user_id') : null);

        if (!$idPembeli || $pemesanan->id_pembeli !== $idPembeli) {
            abort(403, 'Tidak memiliki akses ke pesanan ini.');
        }

        // Update status jadi batal
        $pemesanan->status = 'batal';
        $pemesanan->status_pembayaran = 'tidak valid';
        $pemesanan->save();

        // Poin TIDAK dikembalikan, jadi tidak ada perubahan saldo poin pembeli di sini

        // Kembalikan status barang ke 'tersedia'
        foreach ($pemesanan->detailPemesanans as $detail) {
            $barang = Barang::find($detail->id_barang);
            if ($barang) {
                $barang->status = 'tersedia';  // update status jadi tersedia
                $barang->save();
            }
        }

        return redirect()->route('checkout.failed')->with('error', 'Pembayaran dibatalkan dan stok barang dikembalikan.');
    }

    public function markNotified($id)
    {
        $pemesanan = Pemesanan::findOrFail($id);
        $pemesanan->notified = true;
        $pemesanan->save();

        return response()->json(['message' => 'Notifikasi ditandai terkirim.']);
    }

    public function getByPembeli($id_pembeli)
    {
        $pemesanan = \App\Models\Pemesanan::where('id_pembeli', $id_pembeli)
            ->where('status', 'transaksi selesai')
            ->get();

        return response()->json($pemesanan);
    }

    public function historyPembelian($idPembeli)
    {
        $pemesanan = \App\Models\Pemesanan::with(['detailPemesanans'])->where('id_pembeli', $idPembeli)->orderBy('tgl_pesan', 'desc')->get();

        $response = $pemesanan->map(function($p) {
            return [
                'id_pemesanan' => $p->id_pemesanan,
                'status' => $p->status,
                'tgl_pesan' => $p->tgl_pesan,
                'total_harga_pesanan' => $p->total_harga_pesanan,
                'detail' => $p->detailPemesanans->map(function($d) {
                    // Join ke tabel barang
                    $barang = \App\Models\Barang::where('id_barang', $d->id_barang)->first();
                    return [
                        'id_detail_pemesanan' => $d->id_detail_pemesanan,
                        'nama_barang' => $barang->nama_barang ?? '-',  // ambil dari tabel barang
                        'harga' => $barang->harga_barang ?? $d->harga,
                        'foto_barang' => $barang->foto_barang ?? '',
                        'kategori' => $barang->kategori ?? '-',
                        'deskripsi' => $barang->deskripsi_barang ?? '-',
                        'status_garansi' => ($barang && $barang->garansi == 1) ? 'Aktif' : 'Tidak Ada',
                        'tgl_titip' => $barang ? $barang->tgl_titip : null,
                        'tgl_laku' => $barang ? $barang->tgl_laku : null, 
                    ];
                }),
            ];
        });

        return response()->json($response);
    }

    public function detailPembelian($idPembeli, $idPemesanan)
    {
        $pemesanan = Pemesanan::with(['detailPemesanans.barang'])
            ->where('id_pembeli', $idPembeli)
            ->where('id_pemesanan', $idPemesanan)
            ->first();

        if (!$pemesanan) {
            return response()->json(['error' => 'Not found'], 404);
        }

        $response = [
            'id_pemesanan' => $pemesanan->id_pemesanan,
            'tgl_pesan' => $pemesanan->tgl_pesan,
            'status' => $pemesanan->status,
            'total_harga_pesanan' => $pemesanan->total_harga_pesanan,
            'detail' => $pemesanan->detailPemesanans->map(function ($d) {
                return [
                    'id_detail_pemesanan' => $d->id_detail_pemesanan,
                    'nama_barang' => $d->barang->nama_barang ?? '-',
                    'harga' => $d->harga,
                    'foto_barang' => $d->barang->foto_barang ?? '',
                    'kategori' => $d->barang->kategori ?? '-',
                ];
            })->values(),
        ];

        return response()->json($response);
    }

    public function pengirimanKurir($id)
    {
        $pemesanan = Pemesanan::with('detailPemesanans.barang')
            ->where('id_pegawai', $id)
            ->where('status', 'proses pengiriman')
            ->get();

        $result = $pemesanan->map(function ($p) {
            return [
                'id_pemesanan' => $p->id_pemesanan,
                'status' => $p->status,
                'total_harga_pesanan' => $p->total_harga_pesanan,
                'tgl_pesan' => $p->tgl_pesan,
                'detail' => $p->detailPemesanans->map(function ($d) {
                    $barang = $d->barang;

                    return [
                        'id_detail_pemesanan' => $d->id_detail_pemesanan,
                        'nama_barang' => $barang->nama_barang ?? '-',
                        'harga' => $d->harga,
                        'foto_barang' => $barang->foto_barang ?? '',
                        'kategori' => $barang->kategori ?? '-',
                        'deskripsi' => $barang->deskripsi_barang ?? '-',
                        'status_garansi' => ($barang->garansi ?? false) ? 'Aktif' : 'Tidak Ada',
                        'tgl_titip' => $barang?->tgl_titip?->toIso8601String() ?? '-',
                        'tgl_laku' => $barang?->tgl_laku?->toIso8601String() ?? '-',
                    ];
                })->values(),
            ];
        });

        return response()->json($result);
    }

        public function selesaikan($id)
        {
            $pemesanan = Pemesanan::findOrFail($id);
            $pemesanan->status = 'transaksi selesai';
            $pemesanan->save();

            return response()->json(['message' => 'Pesanan diselesaikan.']);
        }

        public function historyByKurir($id)
    {
        $pemesanan = Pemesanan::query()
            ->where('id_pegawai', $id)
            ->where('status', 'transaksi selesai')
            ->get();

        $result = $pemesanan->map(function ($p) {
            $details = DB::table('detail_pemesanan as detail')
                ->join('barang', 'detail.id_barang', '=', 'barang.id_barang')
                ->where('detail.id_pemesanan', $p->id_pemesanan)
                ->select(
                    'detail.id_detail_pemesanan',
                    'detail.harga as harga_detail',
                    'barang.nama_barang',
                    'barang.harga_barang',
                    'barang.foto_barang',
                    'barang.kategori',
                    'barang.deskripsi_barang',
                    'barang.garansi',
                    'barang.tgl_titip',
                    'barang.tgl_laku'
                )
                ->get()
                ->map(function ($detail) {
                    return [
                        'id_detail_pemesanan' => $detail->id_detail_pemesanan,
                        'nama_barang' => $detail->nama_barang,
                        'harga' => $detail->harga_barang ?? $detail->harga_detail,
                        'foto_barang' => $detail->foto_barang ?? '',
                        'kategori' => $detail->kategori ?? '-',
                        'deskripsi' => $detail->deskripsi_barang ?? '-',
                        'status_garansi' => $detail->garansi ? 'Aktif' : 'Tidak Ada',
                        'tanggal_titip' => $detail->tgl_titip
                            ? Carbon::parse($detail->tgl_titip)->toDateString()
                            : null,
                        'tanggal_laku' => $detail->tgl_laku
                            ? Carbon::parse($detail->tgl_laku)->toDateString()
                            : null,
                    ];
                })
                ->values();

            if ($details->isEmpty()) {
                return null;
            }

            return [
                'id_pemesanan' => $p->id_pemesanan,
                'status' => $p->status,
                'total_harga_pesanan' => $p->total_harga_pesanan,
                'tgl_pesan' => $p->tgl_pesan->toDateString(), // Format tanggal pesan ke format 'YYYY-MM-DD'
                'detail' => $details,
            ];
        })->filter()->values();

        return response()->json($result);
    }

}
