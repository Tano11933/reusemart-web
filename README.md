# ReuseMart Web

Marketplace barang bekas dan barang titipan, membawa barang layak pakai dari penitip ke pembeli yang membutuhkannya. Dibangun dengan Laravel 12 dan MySQL, serta dipakai bersama aplikasi mobile Flutter di [reusemart-mobile](https://github.com/Tano11933/reusemart-mobile).

## Fitur

**Pengunjung**
- Katalog barang tersedia dengan pencarian, filter garansi, kategori, dan halaman detail
- Foto produk, kondisi, masa garansi, dan profil penjual

**Pembeli**
- Keranjang, checkout dengan alamat pengiriman, dan pembayaran dengan unggah bukti transfer
- Riwayat pembelian dan penilaian barang
- Pembatalan transaksi, kelola alamat, profil, dan poin

**Penitip**
- Daftar barang titipan, perpanjangan masa penitipan, dan jadwal pengambilan
- Riwayat penjualan dan profil penitip

**Pegawai**
- Admin: data pegawai, jabatan, organisasi, dan merchandise
- Customer Service: data penitip, diskusi produk, verifikasi pembayaran, klaim merchandise, dan penukaran poin
- Gudang: inventaris barang, pengambilan titipan, pesanan diambil dan dikirim, nota pemesanan
- Owner: request dan riwayat donasi, laporan penjualan, komisi, stok, kategori, barang kedaluwarsa, serta transaksi penitip dalam bentuk PDF

**Organisasi**
- Dashboard request donasi: mengajukan, mengubah, dan memantau status permintaan

## Tampilan

| Beranda | Katalog |
|---|---|
| ![Beranda](docs/screenshots/after/01-home.png) | ![Katalog](docs/screenshots/after/02-lihat-semua.png) |

| Detail barang | Keranjang |
|---|---|
| ![Detail](docs/screenshots/after/04-detail.png) | ![Keranjang](docs/screenshots/after/09-cart.png) |

Screenshot lain, termasuk sisi pegawai, ada di `docs/screenshots/after`. Versi Juni 2025 tersimpan di `docs/screenshots/before`.

## Teknologi

- Laravel 12, PHP 8.2+, MySQL
- Blade + Bootstrap 5 (dimuat dari CDN, tidak perlu build asset)
- barryvdh/laravel-dompdf untuk nota dan laporan PDF
- intervention/image untuk pengolahan gambar
- Tema dan komponen ada di `public/css/reusemart.css`; arah desain ada di `DESIGN.md`

## Menjalankan

```bash
composer install
copy .env.example .env
php artisan key:generate
```

Atur koneksi MySQL di `.env`, lalu:

```bash
php artisan migrate --seed
php artisan serve
```

Seeder mengisi data demo (akun semua peran, katalog berfoto, pesanan kurir, riwayat pembelian, dan komisi hunter) dengan kata sandi `password123`:

| Peran | Jabatan | Email |
|---|---|---|
| Pegawai | Admin | admin@reusemart.test |
| Pegawai | Customer Service | cs@reusemart.test |
| Pegawai | Gudang | gudang@reusemart.test |
| Pegawai | Owner | owner@reusemart.test |
| Pegawai | Hunter | hunter@reusemart.test |
| Pegawai | Kurir | kurir@reusemart.test |
| Pembeli | - | budi@reusemart.test |
| Penitip | - | tokolestari@reusemart.test |
| Organisasi | - | panti@reusemart.test |

Catatan:

- Seluruh skema tabel aplikasi (barang, pemesanan, penitip, dan seterusnya) tersedia sebagai migrasi di `database/migrations`, jadi tidak perlu dump basis data lagi.
- Migrasi dan seeder dibuat idempoten: `php artisan migrate --seed` aman dijalankan di basis data baru maupun basis data lama yang sudah berisi data.
- `php artisan test` memakai SQLite in-memory; pastikan ekstensi `pdo_sqlite` aktif di PHP.

## Struktur

```
app/Http/Controllers     controller per domain (barang, pemesanan, penitip, donasi, laporan, ...)
app/Models               model Eloquent
resources/views          halaman Blade per peran
routes/web.php           route web dan sebagian endpoint API
routes/api.php           endpoint yang dipakai aplikasi mobile
database/migrations      skema lengkap tabel aplikasi (digenerate dari basis data lama)
database/seeders         ReuseMartSeeder: data demo untuk setup baru
public/css/reusemart.css tema dan komponen bersama
docs/screenshots         dokumentasi tampilan before dan after
```

## Kredit

Proyek kelompok mata kuliah P3L (2025), dikembangkan ulang dan dirapikan pada 2026 untuk portofolio. Repositori asli kelompok: [paniae/P3L_1829_1933_2024](https://github.com/paniae/P3L_1829_1933_2024).

Anggota tim: Tano11933, paniae.

## Changelog singkat

**v2 (2026)**
- Desain ulang seluruh halaman: layout, komponen, kontras, dan aksesibilitas keyboard
- Beranda guest dan pembeli ditata ulang sebagai storefront ringkas: kategori cepat, produk tampil lebih awal, dan pencarian tidak berulang
- Perbaikan route rusak dan duplikat, pengamanan akses alamat dan pembatalan pesanan
- Pembatasan akses per peran dan jabatan lewat middleware, plus pembatasan percobaan login
- Skema tabel aplikasi dikemas menjadi migrasi lengkap + seeder data demo; setup tanpa dump basis data
- Seluruh test kembali aktif: 22 test (login tiap peran, register, akses, migrasi, foreign key, dan data demo mobile)
- Perbaikan bug halaman klaim merchandise CS dan endpoint barang penitip di aplikasi mobile
- Checkout menyimpan alamat pengiriman dan metode pembayaran
- Endpoint mobile dirapikan, termasuk login, token FCM, dan riwayat
- Riwayat pembelian mobile menyertakan tanggal; data demo kurir dan Hunter terisi dengan rincian pesanan/komisi
- Dokumentasi: `DESIGN.md` dan screenshot before/after

**v1 (Juni 2025)**
- Versi awal proyek kelompok, tersimpan pada tag `v1.0-juni-2025`
