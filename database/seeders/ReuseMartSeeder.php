<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 * Data demo ReuseMart untuk setup baru: peran, jabatan, akun semua peran,
 * merchandise, barang titipan, dan satu transaksi selesai.
 *
 * Setiap bagian hanya berjalan kalau tabelnya masih kosong, jadi seeder aman
 * dijalankan berulang dan tidak menimpa data yang sudah ada.
 */
class ReuseMartSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();
        $password = Hash::make('password123');

        if (DB::table('role')->count() === 0) {
            DB::table('role')->insert([
                ['id_role' => 1, 'nama_role' => 'penitip'],
                ['id_role' => 2, 'nama_role' => 'pembeli'],
                ['id_role' => 3, 'nama_role' => 'organisasi'],
                ['id_role' => 4, 'nama_role' => 'pegawai'],
            ]);
        }

        if (DB::table('jabatan')->count() === 0) {
            DB::table('jabatan')->insert([
                ['id_jabatan' => 'J1', 'nama_jabatan' => 'Customer Service'],
                ['id_jabatan' => 'J2', 'nama_jabatan' => 'Gudang'],
                ['id_jabatan' => 'J3', 'nama_jabatan' => 'Hunter'],
                ['id_jabatan' => 'J4', 'nama_jabatan' => 'Kurir'],
                ['id_jabatan' => 'J5', 'nama_jabatan' => 'Admin'],
                ['id_jabatan' => 'J6', 'nama_jabatan' => 'Owner'],
                ['id_jabatan' => 'J7', 'nama_jabatan' => 'Cleaning Service'],
            ]);
        }

        $akunPegawai = [
            ['J5', 'Admin ReuseMart', 'admin@reusemart.test'],
            ['J1', 'Citra Layanan', 'cs@reusemart.test'],
            ['J2', 'Gunawan Gudang', 'gudang@reusemart.test'],
            ['J6', 'Omar Pemilik', 'owner@reusemart.test'],
            ['J3', 'Hendra Hunter', 'hunter@reusemart.test'],
            ['J4', 'Kurnia Kurir', 'kurir@reusemart.test'],
        ];

        foreach ($akunPegawai as $i => [$idJabatan, $nama, $email]) {
            if ($this->emailAda('pegawai', $email)) {
                continue;
            }

            DB::table('pegawai')->insert([
                'id_pegawai' => $this->idBaru('pegawai', 'id_pegawai', 'P'),
                'id_jabatan' => $idJabatan,
                'id_role' => 4,
                'email' => $email,
                'password' => $password,
                'nama_pegawai' => $nama,
                'tgl_lahir' => Carbon::parse('1995-01-0' . ($i + 1))->toDateString(),
                'nomor_telepon' => '0812000000' . str_pad((string) ($i + 1), 2, '0', STR_PAD_LEFT),
                'alamat' => 'Jl. Sadang Serang No. ' . ($i + 1) . ', Bandung',
            ]);
        }

        $akunPembeli = [
            ['Budi Santoso', 'Pria', '2000-03-12', '081211111111', 'budi@reusemart.test', 150],
            ['Sari Dewi', 'Wanita', '2001-07-25', '081222222222', 'sari@reusemart.test', 0],
        ];

        foreach ($akunPembeli as [$nama, $jenisKelamin, $tglLahir, $telepon, $email, $poin]) {
            if ($this->emailAda('pembeli', $email)) {
                continue;
            }

            DB::table('pembeli')->insert([
                'id_pembeli' => $this->idBaru('pembeli', 'id_pembeli', 'PEM'),
                'id_role' => 2,
                'nama_pembeli' => $nama,
                'jenis_kelamin' => $jenisKelamin,
                'tgl_lahir' => $tglLahir,
                'nomor_telepon' => $telepon,
                'email' => $email,
                'password' => $password,
                'poin' => $poin,
            ]);
        }

        $akunPenitip = [
            ['Toko Lestari', '3273010101010001', 'Wanita', '1990-02-14', '082211111111', 4.8, 20000, 'tokolestari@reusemart.test'],
            ['Toko Maju', '3273010101010002', 'Pria', '1988-09-03', '082222222222', 4.6, 10000, 'tokomaju@reusemart.test'],
        ];

        foreach ($akunPenitip as [$nama, $nik, $jenisKelamin, $tglLahir, $telepon, $rating, $saldo, $email]) {
            if ($this->emailAda('penitip', $email)) {
                continue;
            }

            DB::table('penitip')->insert([
                'id_penitip' => $this->idBaru('penitip', 'id_penitip', 'T'),
                'id_role' => 1,
                'nama_penitip' => $nama,
                'nik_penitip' => $nik,
                'jenis_kelamin' => $jenisKelamin,
                'tgl_lahir' => $tglLahir,
                'nomor_telepon' => $telepon,
                'rating' => $rating,
                'saldo' => $saldo,
                'email' => $email,
                'password' => $password,
                'poin' => 0,
                'foto_ktp' => null,
            ]);
        }

        if (! $this->emailAda('organisasi', 'panti@reusemart.test')) {
            DB::table('organisasi')->insert([
                'id_organisasi' => $this->idBaru('organisasi', 'id_organisasi', 'ORG'),
                'id_role' => 3,
                'nama_organisasi' => 'Panti Asuhan Kasih',
                'nomor_telepon' => '083233333333',
                'email' => 'panti@reusemart.test',
                'password' => $password,
                'alamat_organisasi' => 'Jl. Sukamulya No. 7, Bandung',
            ]);
        }

        if (DB::table('merchandise')->count() === 0) {
            DB::table('merchandise')->insert([
                ['id_merch' => 'M1', 'id_pegawai' => 'P1', 'nama_merch' => 'Tumbler ReuseMart', 'gambar_merch' => null, 'stok' => 25, 'harga_poin' => 100],
                ['id_merch' => 'M2', 'id_pegawai' => 'P1', 'nama_merch' => 'Totebag Kanvas', 'gambar_merch' => null, 'stok' => 40, 'harga_poin' => 60],
                ['id_merch' => 'M3', 'id_pegawai' => 'P1', 'nama_merch' => 'Payung Lipat', 'gambar_merch' => null, 'stok' => 20, 'harga_poin' => 80],
            ]);
        }

        if (DB::table('alamat')->count() === 0) {
            DB::table('alamat')->insert([
                [
                    'id_alamat' => 'A1', 'id_pembeli' => 'PEM1', 'label_alamat' => 'Rumah',
                    'kecamatan' => 'Coblong', 'kabupaten' => 'Kota Bandung',
                    'detail_alamat' => 'Jl. Dago Asri No. 12', 'desa' => 'Dago', 'default_alamat' => 1,
                ],
                [
                    'id_alamat' => 'A2', 'id_pembeli' => 'PEM2', 'label_alamat' => 'Rumah',
                    'kecamatan' => 'Sukajadi', 'kabupaten' => 'Kota Bandung',
                    'detail_alamat' => 'Jl. Cihampelas No. 88', 'desa' => 'Cipaganti', 'default_alamat' => 1,
                ],
            ]);
        }

        if (DB::table('barang')->count() === 0) {
            $tglTitip = $now->copy()->subDays(14)->toDateTimeString();
            $tglAkhir = $now->copy()->addDays(16)->toDateString();

            DB::table('barang')->insert([
                [
                    'id_barang' => 'B1', 'id_penitip' => 'T1', 'id_gudang' => 'P3', 'id_hunter' => null,
                    'nama_barang' => 'Box Bayi IKEA', 'deskripsi_barang' => 'Kayu kokoh, kondisi mulus',
                    'kategori' => 'Perlengkapan Bayi & Anak', 'harga_barang' => 700000, 'berat_barang' => 10,
                    'tgl_titip' => $tglTitip, 'tgl_garansi' => null, 'tgl_laku' => null,
                    'tgl_akhir' => $tglAkhir, 'tgl_ambil' => null, 'garansi' => 0, 'perpanjangan' => 0,
                    'status' => 'tersedia', 'foto_barang' => 'bayiikea.jpg', 'foto_barang2' => 'bayiikea1.jpg',
                ],
                [
                    'id_barang' => 'G1', 'id_penitip' => 'T2', 'id_gudang' => 'P3', 'id_hunter' => 'P5',
                    'nama_barang' => 'Gitar Akustik Yamaha', 'deskripsi_barang' => 'Suara masih jernih, senar baru',
                    'kategori' => 'Hobi, Mainan, & Koleksi', 'harga_barang' => 400000, 'berat_barang' => 3,
                    'tgl_titip' => $tglTitip, 'tgl_garansi' => null, 'tgl_laku' => null,
                    'tgl_akhir' => $tglAkhir, 'tgl_ambil' => null, 'garansi' => 0, 'perpanjangan' => 0,
                    'status' => 'tersedia', 'foto_barang' => 'gitaryamaha.jpg', 'foto_barang2' => 'gitaryamaha1.jpg',
                ],
                [
                    'id_barang' => 'A1', 'id_penitip' => 'T1', 'id_gudang' => 'P3', 'id_hunter' => null,
                    'nama_barang' => 'Alat Makeup Set', 'deskripsi_barang' => 'Belum dipakai, lengkap',
                    'kategori' => 'Kosmetik & Perawatan Diri', 'harga_barang' => 300000, 'berat_barang' => 2,
                    'tgl_titip' => $tglTitip, 'tgl_garansi' => null, 'tgl_laku' => null,
                    'tgl_akhir' => $tglAkhir, 'tgl_ambil' => null, 'garansi' => 0, 'perpanjangan' => 0,
                    'status' => 'tersedia', 'foto_barang' => 'setmakeup.jpg', 'foto_barang2' => 'setmakeup1.jpg',
                ],
                [
                    'id_barang' => 'B2', 'id_penitip' => 'T2', 'id_gudang' => 'P3', 'id_hunter' => null,
                    'nama_barang' => 'Ban Motor IRC', 'deskripsi_barang' => 'Ukuran 80/90-14, masih layak',
                    'kategori' => 'Otomotif & Aksesori', 'harga_barang' => 200000, 'berat_barang' => 8,
                    'tgl_titip' => $tglTitip, 'tgl_garansi' => null,
                    'tgl_laku' => $now->copy()->subDays(9)->toDateString(),
                    'tgl_akhir' => $tglAkhir, 'tgl_ambil' => null, 'garansi' => 0, 'perpanjangan' => 0,
                    'status' => 'terjual', 'foto_barang' => 'banmotorirc.jpg', 'foto_barang2' => 'banmotorirc1.jpg',
                ],
            ]);
        }

        if (DB::table('pemesanan')->count() === 0) {
            $tglPesan = $now->copy()->subDays(10);
            $idPemesanan = $tglPesan->format('Y') . '.' . (int) $tglPesan->format('n') . '.1';

            DB::table('pemesanan')->insert([
                'id_pemesanan' => $idPemesanan,
                'id_pembeli' => 'PEM1',
                'id_pegawai' => 'P2',
                'tgl_pesan' => $tglPesan->toDateTimeString(),
                'tgl_kirim' => null,
                'tgl_ambil' => $now->copy()->subDays(9)->toDateString(),
                'poin_pesanan' => 20,
                'status' => 'transaksi selesai',
                'jenis_pengantaran' => 'diambil',
                'alamat_pengiriman' => 'A1',
                'tgl_pembayaran' => $tglPesan->copy()->addHours(2)->toDateTimeString(),
                'total_harga_pesanan' => 200000,
                'total_ongkir' => 0,
                'harga_setelah_ongkir' => 200000,
                'potongan_harga' => 0,
                'total_harga' => 200000,
                'bukti_transfer' => null,
                'metode_pembayaran' => 'transfer',
                'status_pembayaran' => 'valid',
            ]);

            DB::table('detail_pemesanan')->insert([
                'id_detail_pemesanan' => 'DP-DEMO-1',
                'rating_diberikan' => 5,
                'id_barang' => 'B2',
                'id_pemesanan' => $idPemesanan,
                'harga' => 200000,
            ]);

            DB::table('komisi')->insert([
                'id_komisi' => 'K1',
                'id_barang' => 'B2',
                'id_pemesanan' => $idPemesanan,
                'id_pegawai' => 'P5',
                'komisi_perusahaan' => 20000,
                'komisi_hunter' => 10000,
                'bonus' => 0,
            ]);
        }

        if (DB::table('diskusi_produk')->count() === 0) {
            DB::table('diskusi_produk')->insert([
                [
                    'id_diskusi' => 'D1', 'id_pegawai' => null, 'id_pembeli' => 'PEM1',
                    'id_barang' => 'B1', 'komentar' => 'Apakah masih tersedia dan bisa dikirim ke Bandung?',
                    'tgl_komentar' => $now->copy()->subDays(3)->toDateString(),
                ],
                [
                    'id_diskusi' => 'D2', 'id_pegawai' => 'P2', 'id_pembeli' => null,
                    'id_barang' => 'B1', 'komentar' => 'Masih tersedia, silakan langsung diorder ya.',
                    'tgl_komentar' => $now->copy()->subDays(2)->toDateString(),
                ],
            ]);
        }

        if (DB::table('request_donasi')->count() === 0) {
            DB::table('request_donasi')->insert([
                'id_request_donasi' => 'R1',
                'id_organisasi' => 'ORG1',
                'tgl_request_donasi' => $now->copy()->subDays(5)->toDateString(),
                'nama_barang_request' => 'Alat tulis untuk kegiatan belajar',
                'status_req' => null,
            ]);
        }

        if (DB::table('tukar_poin')->count() === 0) {
            DB::table('tukar_poin')->insert([
                'id_tukar_poin' => 'TP1',
                'id_pembeli' => 'PEM1',
                'id_merch' => 'M2',
                'id_pegawai' => null,
                'tgl_tukar' => $now->copy()->subDays(2)->toDateString(),
                'tgl_ambil' => null,
                'jml' => 1,
                'status' => 'belum diambil',
            ]);
        }
    }

    private function emailAda(string $tabel, string $email): bool
    {
        return DB::table($tabel)->where('email', $email)->exists();
    }

    /**
     * Cari id baru berpola prefix + angka (PEM1, T1, P1, ORG1) yang belum terpakai,
     * supaya akun demo bisa disisipkan ke database yang sudah berisi data.
     */
    private function idBaru(string $tabel, string $kolom, string $prefix): string
    {
        $max = 0;

        foreach (DB::table($tabel)->pluck($kolom) as $id) {
            if (preg_match('/^' . preg_quote($prefix, '/') . '0*(\d+)$/i', (string) $id, $m)) {
                $max = max($max, (int) $m[1]);
            }
        }

        do {
            $max++;
            $kandidat = $prefix . $max;
        } while (DB::table($tabel)->where($kolom, $kandidat)->exists());

        return $kandidat;
    }
}
