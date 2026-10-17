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

            $barang = [
                ['B1', 'T1', null, 'Box Bayi IKEA', 'Kayu kokoh, kondisi mulus', 'Perlengkapan Bayi & Anak', 700000, 10, 'bayiikea.jpg', 'bayiikea1.jpg', 'tersedia'],
                ['G1', 'T2', 'P5', 'Gitar Akustik Yamaha', 'Suara masih jernih, senar baru', 'Hobi, Mainan, & Koleksi', 400000, 3, 'gitaryamaha.jpg', 'gitaryamaha1.jpg', 'tersedia'],
                ['A1', 'T1', null, 'Alat Makeup Set', 'Belum dipakai, lengkap', 'Kosmetik & Perawatan Diri', 300000, 2, 'setmakeup.jpg', 'setmakeup1.jpg', 'tersedia'],
                ['A2', 'T2', null, 'Blender Philips 600W', 'Mata pisau tajam, wadah lengkap, siap pakai', 'Perabotan Rumah Tangga', 320000, 2, 'blender.jpg', 'blender1.jpg', 'tersedia'],
                ['T3', 'T1', null, 'TV LED LG 32 Inch', 'Kondisi seperti baru, gambar jernih', 'Elektronik & Gadget', 1700000, 5, 'tvledlg.jpg', 'tvledlg1.jpg', 'tersedia'],
                ['H1', 'T2', null, 'Helm Full Face KYT', 'Double visor, busa masih tebal', 'Otomotif & Aksesori', 500000, 2, 'helmkyt.jpg', 'helmkyt1.jpg', 'tersedia'],
                ['K1', 'T1', null, 'Komik Naruto Vol. 1-5', 'Kondisi bagus, lengkap lima volume', 'Hobi, Mainan, & Koleksi', 100000, 2, 'komiknaruto.jpg', 'komiknaruto1.jpg', 'tersedia'],
                ['L2', 'T2', 'P5', 'Laptop Asus Vivobook', 'RAM 8GB, SSD 256GB, siap pakai', 'Elektronik & Gadget', 3500000, 2, 'vivobookseken.jpg', 'vivobookseken1.jpg', 'tersedia'],
                ['M2', 'T1', null, 'Meja Kantor Minimalis', 'Kondisi sangat baik, kokoh', 'Perabotan Rumah Tangga', 600000, 12, 'mejakantorminimalis.jpg', 'mejakantorminimalis1.jpg', 'tersedia'],
                ['S4', 'T2', null, 'Smartwatch Xiaomi', 'Tampilan mulus, baterai awet', 'Elektronik & Gadget', 450000, 1, 'smartwatchxiaomi.jpg', 'smartwatchxiaomi1.jpg', 'diproses'],
                ['T2', 'T1', null, 'Tenda Camping Kapasitas 4', 'Waterproof, lengkap dengan tas', 'Perlengkapan Taman & Outdoor', 900000, 4, 'tendacamping.jpg', 'tendacamping1.jpg', 'tersedia'],
                ['P1', 'T2', null, 'Printer Epson L360', 'Masih bisa print dan scan', 'Elektronik & Gadget', 750000, 6, 'printerepson.jpg', 'printerepson1.jpg', 'tersedia'],
                ['B2', 'T2', null, 'Ban Motor IRC', 'Ukuran 80/90-14, masih layak', 'Otomotif & Aksesori', 200000, 8, 'banmotorirc.jpg', 'banmotorirc1.jpg', 'terjual'],
                ['R2', 'T1', 'P5', 'Rice Cooker Philips', 'Masih berfungsi normal, panci anti lengket, lengkap', 'Elektronik & Gadget', 275000, 3, 'ricecooker.jpg', 'ricecooker2.jpg', 'tersedia'],
                ['K4', 'T1', null, 'Kipas Angin Cosmos', 'Tiga kecepatan, baling-baling bersih, angin dingin', 'Elektronik & Gadget', 160000, 3, 'kipasangin.png', 'kipasangin2.png', 'tersedia'],
                ['S6', 'T1', null, 'Setrika Philips', 'Panas stabil, alas anti lengket, kabel masih bagus', 'Elektronik & Gadget', 130000, 1, 'setrika.jpg', 'setrika2.png', 'tersedia'],
                ['S7', 'T2', null, 'Speaker Bluetooth JBL', 'Suara jernih, baterai tahan lama, tombol normal', 'Elektronik & Gadget', 450000, 1, 'speaker.jpg', 'speaker2.jpg', 'tersedia'],
                ['K5', 'T1', null, 'Keyboard Mechanical', 'Tombol responsif, lampu berfungsi, kabel USB lengkap', 'Elektronik & Gadget', 250000, 1, 'keyboard.jpg', 'keyboard2.jpg', 'tersedia'],
                ['S8', 'T2', 'P5', 'Sepatu Sneakers Vans', 'Ukuran 42, pemakaian ringan, sol masih tebal', 'Pakaian & Aksesori', 350000, 1, 'sepatu.jpg', 'sepatu2.jpg', 'tersedia'],
                ['T4', 'T1', null, 'Tas Ransel Eiger', 'Banyak kompartemen, jahitan kuat, resleting lancar', 'Pakaian & Aksesori', 280000, 1, 'ransel.jpg', 'ransel2.jpg', 'tersedia'],
                ['J4', 'T2', 'P5', 'Jaket Denim', 'Ukuran L, bahan tebal, warna masih pekat', 'Pakaian & Aksesori', 220000, 1, 'jaketdenim.jpg', 'jaketdenim2.jpg', 'tersedia'],
                ['R3', 'T1', null, 'Rak Sepatu Kayu', 'Tiga tingkat, kayu kokoh, muat enam pasang', 'Perabotan Rumah Tangga', 185000, 4, 'raksepatu.jpg', 'raksepatu2.jpg', 'tersedia'],
                ['L3', 'T2', null, 'Lampu Meja Belajar', 'Lampu LED hemat energi, leher bisa diatur', 'Perabotan Rumah Tangga', 120000, 1, 'lampumeja.jpg', 'lampumeja2.jpg', 'tersedia'],
            ];

            foreach ($barang as [$id, $idPenitip, $idHunter, $nama, $deskripsi, $kategori, $harga, $berat, $foto, $foto2, $status]) {
                DB::table('barang')->insert([
                    'id_barang' => $id,
                    'id_penitip' => $idPenitip,
                    'id_gudang' => 'P3',
                    'id_hunter' => $idHunter,
                    'nama_barang' => $nama,
                    'deskripsi_barang' => $deskripsi,
                    'kategori' => $kategori,
                    'harga_barang' => $harga,
                    'berat_barang' => $berat,
                    'tgl_titip' => $tglTitip,
                    'tgl_garansi' => null,
                    'tgl_laku' => $status === 'terjual' ? $now->copy()->subDays(9)->toDateString() : null,
                    'tgl_akhir' => $tglAkhir,
                    'tgl_ambil' => null,
                    'garansi' => 0,
                    'perpanjangan' => 0,
                    'status' => $status,
                    'foto_barang' => $foto,
                    'foto_barang2' => $foto2,
                ]);
            }
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

            // Pesanan kedua: sedang dikirim kurir demo supaya layar kurir ada isinya.
            $tglPesan2 = $now->copy()->subDays(2);
            $idPemesanan2 = $tglPesan2->format('Y') . '.' . (int) $tglPesan2->format('n') . '.2';

            DB::table('pemesanan')->insert([
                'id_pemesanan' => $idPemesanan2,
                'id_pembeli' => 'PEM2',
                'id_pegawai' => 'P6',
                'tgl_pesan' => $tglPesan2->toDateTimeString(),
                'tgl_kirim' => $now->copy()->addDay()->toDateString(),
                'tgl_ambil' => null,
                'poin_pesanan' => 45,
                'status' => 'proses pengiriman',
                'jenis_pengantaran' => 'dikirim',
                'alamat_pengiriman' => 'A2',
                'tgl_pembayaran' => $tglPesan2->copy()->addHour()->toDateTimeString(),
                'total_harga_pesanan' => 450000,
                'total_ongkir' => 20000,
                'harga_setelah_ongkir' => 470000,
                'potongan_harga' => 0,
                'total_harga' => 470000,
                'bukti_transfer' => null,
                'metode_pembayaran' => 'transfer',
                'status_pembayaran' => 'valid',
            ]);

            DB::table('detail_pemesanan')->insert([
                'id_detail_pemesanan' => 'DP-DEMO-2',
                'rating_diberikan' => null,
                'id_barang' => 'S4',
                'id_pemesanan' => $idPemesanan2,
                'harga' => 450000,
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

        $this->seedMobileDemoDelivery($now);
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

    /**
     * Satu pesanan selesai dengan rincian dan komisi agar halaman riwayat
     * kurir, riwayat pembeli, serta rincian komisi memiliki contoh nyata.
     */
    private function seedMobileDemoDelivery(Carbon $now): void
    {
        $kurirId = DB::table('pegawai')->where('email', 'kurir@reusemart.test')->value('id_pegawai');
        $hunterId = DB::table('pegawai')->where('email', 'hunter@reusemart.test')->value('id_pegawai');
        $pembeliId = DB::table('pembeli')->where('email', 'budi@reusemart.test')->value('id_pembeli');
        $barang = DB::table('barang')
            ->where('nama_barang', 'Rice Cooker Philips')
            ->first();

        if ($barang && DB::table('detail_pemesanan')->where('id_barang', $barang->id_barang)->exists()) {
            $barang = DB::table('barang')
                ->where('status', 'tersedia')
                ->whereNotIn('id_barang', DB::table('detail_pemesanan')->select('id_barang'))
                ->orderBy('id_barang')
                ->first();
        }

        if (! $kurirId || ! $hunterId || ! $pembeliId || ! $barang) {
            return;
        }

        $idPemesanan = 'DEMO-KURIR-' . $now->format('Ym');
        if (DB::table('pemesanan')->where('id_pemesanan', $idPemesanan)->exists()) {
            return;
        }

        $harga = (int) $barang->harga_barang;
        $tglPesan = $now->copy()->subDays(2);

        DB::table('pemesanan')->insert([
            'id_pemesanan' => $idPemesanan,
            'id_pembeli' => $pembeliId,
            'id_pegawai' => $kurirId,
            'tgl_pesan' => $tglPesan->toDateTimeString(),
            'tgl_kirim' => $now->copy()->subDay()->toDateString(),
            'tgl_ambil' => null,
            'poin_pesanan' => (int) floor($harga / 10000),
            'status' => 'transaksi selesai',
            'jenis_pengantaran' => 'dikirim',
            'alamat_pengiriman' => null,
            'tgl_pembayaran' => $tglPesan->copy()->addHour()->toDateTimeString(),
            'total_harga_pesanan' => $harga,
            'total_ongkir' => 20000,
            'harga_setelah_ongkir' => $harga + 20000,
            'potongan_harga' => 0,
            'total_harga' => $harga + 20000,
            'bukti_transfer' => null,
            'metode_pembayaran' => 'transfer',
            'status_pembayaran' => 'valid',
        ]);

        DB::table('detail_pemesanan')->insert([
            'id_detail_pemesanan' => 'DP-' . $idPemesanan,
            'rating_diberikan' => 5,
            'id_barang' => $barang->id_barang,
            'id_pemesanan' => $idPemesanan,
            'harga' => $harga,
        ]);

        DB::table('barang')->where('id_barang', $barang->id_barang)->update([
            'id_hunter' => $hunterId,
            'status' => 'terjual',
            'tgl_laku' => $now->toDateString(),
        ]);

        $nomorKomisi = 0;
        foreach (DB::table('komisi')->pluck('id_komisi') as $idKomisi) {
            if (preg_match('/^K0*(\d+)$/i', (string) $idKomisi, $matches)) {
                $nomorKomisi = max($nomorKomisi, (int) $matches[1]);
            }
        }

        DB::table('komisi')->insert([
            'id_komisi' => 'K' . ($nomorKomisi + 1),
            'id_barang' => $barang->id_barang,
            'id_pemesanan' => $idPemesanan,
            'id_pegawai' => $hunterId,
            'komisi_perusahaan' => $harga * 0.15,
            'komisi_hunter' => $harga * 0.05,
            'bonus' => 0,
        ]);
    }
}
