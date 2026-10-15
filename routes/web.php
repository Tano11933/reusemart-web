<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PegawaiController;
use App\Http\Controllers\JabatanController;
use App\Http\Controllers\OrganisasiController;
use App\Http\Controllers\MerchandiseController;
use App\Http\Controllers\PembeliController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\AlamatController;
use App\Http\Controllers\PenitipController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\DonasiController;
use App\Http\Controllers\RequestDonasiController;
use App\Http\Controllers\DiskusiProdukController;
use App\Http\Controllers\KlaimMerchandiseController;
use App\Http\Controllers\PemesananController;
use App\Http\Controllers\TukarPoinController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Rute dibagi per peran memakai middleware "role" dan "jabatan" (khusus
| pegawai) agar halaman internal hanya bisa dibuka pengguna yang berhak.
| Rute publik sengaja dibiarkan terbuka untuk pengunjung yang belum masuk.
|
*/

// ---------------------------------------------------------------------------
// Publik: autentikasi, pendaftaran, dan halaman katalog pengunjung
// ---------------------------------------------------------------------------

Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:10,1')->name('login.proses');

// Login dari aplikasi mobile (Flutter) memakai endpoint yang sama dengan web.
Route::post('/api/login', [AuthController::class, 'login'])->middleware('throttle:10,1');

Route::get('/register', function () {
    return view('auth.register');
})->name('register');

Route::post('/register/pembeli', [PembeliController::class, 'store'])->name('pembeli.register');
Route::post('/register/organisasi', [OrganisasiController::class, 'store'])->name('register.organisasi');

Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/login');
})->name('logout');

Route::get('/', [BarangController::class, 'home'])->name('guest.home');
Route::get('/lihat_semua', [BarangController::class, 'lihatSemua'])->name('guest.lihat_semua');
Route::get('/detail/{id}', [BarangController::class, 'detail'])->name('guest.detail_barang');
Route::get('/kategori/{kategori}', [BarangController::class, 'showByCategory'])->name('guest.per_kategori');
Route::get('/search', [BarangController::class, 'search'])->name('guest.search');
Route::get('/barang/search', [BarangController::class, 'searchBarang']);
Route::get('/diskusi/barang/{id_barang}', [DiskusiProdukController::class, 'getByBarang'])->name('diskusi.by_barang');
Route::get('/api/penitip/check-nik/{nik}', [PenitipController::class, 'checkNIK']);

Route::get('/checkout/failed', function () {
    return view('pembeli.checkout-failed');
})->name('checkout.failed');

Route::get('/checkout/success', function () {
    return view('pembeli.checkout-success');
})->name('checkout.success');

// ---------------------------------------------------------------------------
// Pembeli
// ---------------------------------------------------------------------------

Route::middleware('role:pembeli')->group(function () {
    Route::get('/home-beli', [BarangController::class, 'homeBeli'])->name('home_beli');
    Route::get('/lihat-semua-beli', [BarangController::class, 'lihatSemuaBeli'])->name('lihat_semua_beli');
    Route::get('/detail-beli/{id}', [BarangController::class, 'detailBeli'])->name('detail_barang_beli');
    Route::post('/rate-barang/{id}', [BarangController::class, 'rate'])->name('barang.rate');

    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/add-to-cart/{id}', [CartController::class, 'addToCart'])->name('add_to_cart');
    Route::delete('/remove-from-cart/{id}', [CartController::class, 'removeFromCart'])->name('remove_from_cart');

    Route::post('/komentar', [DiskusiProdukController::class, 'store'])->name('diskusi.store');

    Route::get('/pembeli/{id}/history-data', [PembeliController::class, 'getPembeliHistory']);
    Route::get('/pembeli/history', [PembeliController::class, 'showHistoryPage'])->name('pembeli.history');
    Route::get('/pembeli-history', [PembeliController::class, 'showHistoryPage']);
    Route::get('/pembeli/profile/{id}', [PembeliController::class, 'profile'])->name('pembeli-profile');

    Route::get('/pembeli-alamat', [PembeliController::class, 'manageAddresses'])->name('pembeli.alamat');
    Route::get('pembeli/{userId}/alamat', [AlamatController::class, 'index']);
    Route::post('pembeli/{userId}/alamat', [AlamatController::class, 'store']);
    Route::put('pembeli/{userId}/alamat/{idAlamat}', [AlamatController::class, 'update']);
    Route::delete('pembeli/{userId}/alamat/{idAlamat}', [AlamatController::class, 'destroy']);

    Route::get('/checkout', [PemesananController::class, 'index'])->name('checkout.index');
    Route::post('/checkout', [PemesananController::class, 'store'])->name('checkout.store');
    Route::get('/checkout/payment/{id}', [PemesananController::class, 'showPaymentForm'])->name('checkout.payment');
    Route::post('/checkout/payment/{id}', [PemesananController::class, 'submitPayment'])->name('checkout.payment.submit');
    Route::post('/checkout/payment/cancel/{id}', [PemesananController::class, 'cancelPayment'])->name('checkout.payment.cancel');

    Route::get('/batalTransaksiValid', [PembeliController::class, 'batalTransaksiValid'])->name('batalTransaksiValid');
    Route::post('/batalTransaksiValid/batal/{id}', [PembeliController::class, 'batalTransaksiValid'])->name('transaksi-valid.batal');
});

// ---------------------------------------------------------------------------
// Penitip
// ---------------------------------------------------------------------------

Route::middleware('role:penitip')->group(function () {
    Route::get('/penitip/barang-titipan', [PenitipController::class, 'penitipTitipan'])->name('penitip.barangTitipan');
    Route::get('/penitip/profil', [PenitipController::class, 'profil'])->name('penitip.profil_penitip');
    Route::get('/penitip/detail-barang/{id}', [PenitipController::class, 'detailBarangPenitip'])->name('penitip.detail_barang');
    Route::get('/penitip/history-penjualan', [PenitipController::class, 'historyPenjualanPage'])->name('penitip.history-penjualan');
    Route::get('/penitip/{id}/history-penjualan', [PenitipController::class, 'getHistoryPenjualan']);

    Route::post('/barang/perpanjangan/{id}', [BarangController::class, 'perpanjangan'])->name('barang.perpanjangan');
    Route::post('/barang/ambil-otomatis/{id}', [BarangController::class, 'ambilOtomatis'])->name('barang.ambil.otomatis');
});

// ---------------------------------------------------------------------------
// Organisasi
// ---------------------------------------------------------------------------

Route::middleware('role:organisasi')->group(function () {
    Route::get('/organisasi/index_organisasi', [OrganisasiController::class, 'dashboard'])->name('organisasi.dashboard');
    Route::post('/organisasi/request-donasi', [RequestDonasiController::class, 'storeForCurrentOrganization'])->name('organisasi.requestDonasi.store');
    Route::put('/organisasi/request-donasi/{id}', [RequestDonasiController::class, 'updateForCurrentOrganization'])->name('organisasi.requestDonasi.update');
    Route::delete('/organisasi/request-donasi/{id}', [RequestDonasiController::class, 'destroyForCurrentOrganization'])->name('organisasi.requestDonasi.destroy');

    Route::get('/organisasi/request_donasi', function () {
        return redirect()->route('organisasi.dashboard');
    });
});

// ---------------------------------------------------------------------------
// Pegawai: beranda bersama semua jabatan
// ---------------------------------------------------------------------------

Route::middleware('role:pegawai')->group(function () {
    Route::get('/pegawai/home', [PegawaiController::class, 'homeGudang'])->name('pegawai.home');
});

// ---------------------------------------------------------------------------
// Pegawai - Gudang
// ---------------------------------------------------------------------------

Route::middleware(['role:pegawai', 'jabatan:Gudang'])->group(function () {
    Route::get('/pegawai/gudang', [PegawaiController::class, 'homeGudang'])->name('pegawai.gudang');
    Route::get('/pegawai/gudang/index_gudang', [PegawaiController::class, 'homeGudang'])->name('pegawai.gudang.index_gudang');
    Route::post('/pegawai/gudang/store', [PegawaiController::class, 'storeGudang'])->name('gudang.store');
    Route::put('/pegawai/gudang/{id}', [PegawaiController::class, 'updateGudang'])->name('gudang.update');
    Route::delete('/pegawai/gudang/{id}', [PegawaiController::class, 'destroyGudang'])->name('gudang.destroy');

    Route::get('/gudang/ambil-barang', [PegawaiController::class, 'ambilBarangList'])->name('gudang.ambil-barang');
    Route::post('/gudang/barang-diambil/{id}', [PegawaiController::class, 'setBarangDiambil'])->name('gudang.barang-diambil');
    Route::post('/gudang/barang-tidak-diambil/{id}', [PegawaiController::class, 'setBarangTidakDiambil'])->name('gudang.barang-tidak-diambil');

    Route::get('/pegawai/transaksi-diambil', [PemesananController::class, 'transaksiDiambil'])->name('transaksi.diambil');
    Route::post('/pegawai/pemesanan/{id}/diambil', [PemesananController::class, 'pemesananDiambil'])->name('pemesanan.diambil.pegawai');
    Route::post('/pemesanan/diambil/{id}', [PemesananController::class, 'tandaiDiambil'])->name('pemesanan.diambil');

    Route::get('/pegawai/transaksi-dikirim', [PemesananController::class, 'transaksiDikirim'])->name('transaksi.dikirim');
    Route::post('/pegawai/transaksi-dikirim/{id}', [PemesananController::class, 'konfirmasiPengiriman'])->name('transaksi.konfirmasiPengiriman');
    Route::post('/transaksi/konfirmasi/{id}', [PemesananController::class, 'konfirmasiPengiriman'])->name('transaksi.konfirmasi');
    Route::post('/transaksi/jadwalkan/{id}', [PemesananController::class, 'jadwalkanPengiriman'])->name('transaksi.jadwalkan');

    Route::get('/pegawai/nota-pemesanan', [PemesananController::class, 'notaPemesanan'])->name('nota.pemesanan');
    Route::get('/pegawai/nota-pemesanan/download/{id}', [PemesananController::class, 'downloadNota'])->name('nota.download');

    Route::get('/pegawai/gudang/laporan-penitip/{id}', [PenitipController::class, 'cetakLaporanPenitip'])->name('pegawai.laporanPenitip');

    Route::post('/barang/ambil/{id}', [BarangController::class, 'ambilBarang'])->name('barang.ambil');
});

// ---------------------------------------------------------------------------
// Pegawai - Customer Service
// ---------------------------------------------------------------------------

Route::middleware(['role:pegawai', 'jabatan:Customer Service'])->group(function () {
    Route::get('/pegawai/cs', [PegawaiController::class, 'index_cs'])->name('pegawai.cs');

    Route::prefix('diskusi-produk-cs')->group(function () {
        Route::get('/', [DiskusiProdukController::class, 'index'])->name('diskusi.index');
        Route::get('/barang/{id_barang}', [DiskusiProdukController::class, 'getByBarang'])->name('diskusi.getByBarang');
        Route::post('/store', [DiskusiProdukController::class, 'store'])->name('diskusi.store.cs');
        Route::delete('/{id_diskusi}', [DiskusiProdukController::class, 'destroy'])->name('diskusi.destroy.cs');
    });

    Route::prefix('verifikasi-pembayaran-cs')->group(function () {
        Route::get('/', [PegawaiController::class, 'verifikasiPembayaran'])->name('verifikasi-pembayaran.cs');
        Route::post('/validasi/{id}', [PegawaiController::class, 'verifikasiPembayaran'])->name('verifikasi-pembayaran.validasi');
    });

    Route::get('/klaim-merchandise-cs', [KlaimMerchandiseController::class, 'index'])->name('cs.klaimMerchandise');
    Route::post('/klaim-merchandise/update-tanggal-ambil/{id}', [KlaimMerchandiseController::class, 'updateTanggalAmbil'])->name('cs.klaimMerchandise.updateTanggalAmbil');

    Route::get('/tukar-poin-cs', [TukarPoinController::class, 'index'])->name('cs.tukarPoin');
    Route::post('/tukar-poin/request', [TukarPoinController::class, 'store'])->name('tukarPoin.request');
    Route::post('/tukar-poin/update-tanggal-ambil/{id}', [TukarPoinController::class, 'updateTanggalAmbil']);
    Route::post('/tukar-poin/konfirmasi-ambil/{id}', [TukarPoinController::class, 'konfirmasiAmbil']);
});

// ---------------------------------------------------------------------------
// Pegawai - Admin
// ---------------------------------------------------------------------------

Route::middleware(['role:pegawai', 'jabatan:Admin'])->group(function () {
    Route::get('/pegawai/admin', [PegawaiController::class, 'adminPegawai'])->name('pegawai.admin');
    Route::post('/pegawai', [PegawaiController::class, 'store'])->name('pegawai.store');
    Route::put('/pegawai/{id}', [PegawaiController::class, 'update'])->name('pegawai.update');
    Route::delete('/pegawai/{id}', [PegawaiController::class, 'destroy'])->name('pegawai.destroy');

    Route::get('/jabatan', [JabatanController::class, 'adminJabatan'])->name('jabatan.admin');
    Route::post('/jabatan', [JabatanController::class, 'store'])->name('jabatan.store');
    Route::put('/jabatan/{id}', [JabatanController::class, 'update'])->name('jabatan.update');
    Route::delete('/jabatan/{id}', [JabatanController::class, 'destroy'])->name('jabatan.destroy');

    Route::get('/organisasi', [OrganisasiController::class, 'adminOrganisasi'])->name('organisasi.admin');
    Route::post('/organisasi', [OrganisasiController::class, 'store'])->name('organisasi.store');
    Route::put('/organisasi/{id}', [OrganisasiController::class, 'update'])->name('organisasi.update');
    Route::delete('/organisasi/{id}', [OrganisasiController::class, 'destroy'])->name('organisasi.destroy');

    Route::get('/merchandise', [MerchandiseController::class, 'adminMerchandise']);
    Route::post('/merchandise', [MerchandiseController::class, 'store'])->name('merchandise.store');
    Route::put('/merchandise/{id}', [MerchandiseController::class, 'update'])->name('merchandise.update');
    Route::delete('/merchandise/{id}', [MerchandiseController::class, 'destroy'])->name('merchandise.destroy');
});

// ---------------------------------------------------------------------------
// Pegawai - Owner
// ---------------------------------------------------------------------------

Route::middleware(['role:pegawai', 'jabatan:Owner'])->group(function () {
    Route::get('/pegawai/ownerRDonasi', [RequestDonasiController::class, 'ownerRDonasi'])->name('owner.requestRDonasi');
    Route::post('/approve-request-donasi/{idPegawai}/{idRequestDonasi}', [RequestDonasiController::class, 'approveRequestDonasi'])->name('approveRequestDonasi');
    Route::get('/owner/ownerHistoryDonasi', [DonasiController::class, 'ownerHistoryDonasi'])->name('owner.ownerHistoryDonasi');
    Route::get('/api/donasi/by-organisasi/{idOrganisasi}', [DonasiController::class, 'donasiByOrganisasi']);
    Route::post('/donasi/{id}', [DonasiController::class, 'donasi'])->name('donasi.action');

    Route::get('/owner/laporan-penjualan-bulan', [PegawaiController::class, 'laporanPenjualanBulanan'])->name('owner.laporanPenjualanBulanan');
    Route::get('/owner/laporan-penjualan-bulan/pdf', [PegawaiController::class, 'cetakNotaBulanan'])->name('nota.pdf');
    Route::get('/owner/laporan-komisi-bulanan', [PegawaiController::class, 'laporanKomisiBulanan'])->name('owner.laporanKomisiBulanan');
    Route::get('/owner/laporan-komisi-bulanan/pdf', [PegawaiController::class, 'cetakKomisiBulanan'])->name('komisi.pdf');
    Route::get('/owner/laporan-stok-gudang', [PegawaiController::class, 'laporanStokGudang'])->name('owner.laporanStokGudang');
    Route::get('/owner/laporan-stok-gudang/pdf', [PegawaiController::class, 'cetakStokGudang'])->name('stokgudang.pdf');
    Route::get('/owner/laporan-penjualan-kategori', [PegawaiController::class, 'laporanPenjualanKategori'])->name('owner.laporanPenjualanKategori');
    Route::get('/owner/laporan-penjualan-kategori/pdf', [PegawaiController::class, 'cetakLaporanPenjualanKategori'])->name('owner.laporanPenjualanKategori.pdf');
    Route::get('/owner/laporan-barang-expired', [PegawaiController::class, 'laporanBarangExpired'])->name('owner.laporanBarangExpired');
    Route::get('/owner/laporan-barang-expired/pdf', [PegawaiController::class, 'cetakLaporanBarangExpired'])->name('owner.laporanBarangExpired.pdf');

    Route::get('/laporan-donasi', [PegawaiController::class, 'laporanDonasiBarang'])->name('donasi.laporan');
    Route::get('/laporan-donasi/pdf', [PegawaiController::class, 'cetakDonasiBarang'])->name('donasi.pdf');
    Route::get('/laporan-request-donasi', [PegawaiController::class, 'laporanRequestDonasi'])->name('requestdonasi.laporan');
    Route::get('/laporan-request-donasi/pdf', [PegawaiController::class, 'cetakRequestDonasiPDF'])->name('requestdonasi.pdf');
    Route::get('/laporan-transaksi-penitip', [PegawaiController::class, 'laporanTransaksiPenitip'])->name('transaksi.penitip.laporan');
    Route::get('/laporan-transaksi-penitip/pdf', [PegawaiController::class, 'cetakTransaksiPenitipPDF'])->name('transaksi.penitip.pdf');
});
