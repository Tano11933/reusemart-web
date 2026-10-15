# Arah Desain ReuseMart

Dokumen ini merangkum arah desain yang **sudah dipakai** proyek ReuseMart sejak Maret 2025 dan disempurnakan pada v2 2026. Isinya adalah transkripsi keputusan yang sudah ada di `public/css/reusemart.css`, bukan aturan baru. Filter antislop dipakai di atas arah ini.

## Identitas

- Produk: ReuseMart, marketplace barang bekas dan barang titipan (web Laravel + aplikasi mobile Flutter).
- Karakter: ramah, tenang, bisa dipercaya. Bukan korporat dingin, bukan pula "startup neon".
- Maskot daur ulang dari proyek Juni 2025 tetap menjadi wajah brand.
- Asal warna: logo dan maskot proyek awal memakai hijau teal, jadi seluruh sistem warna diturunkan dari sana, bukan dari palet bawaan.

## Palet

| Peran | Warna | Alasan |
|---|---|---|
| Inti | `#1e5b5a` teal tua | Warna brand dari proyek 2025, dipakai pada navbar, tombol utama, dan harga. |
| Pendukung | `#0f2f2e` teal gelap | Permukaan gelap untuk footer, sidebar workspace, dan area kontras tinggi. |
| Netral | `#f1f7f6` latar, `#ffffff` kartu | Latar sejuk tipis agar kartu putih terbaca tanpa garis tebal. |
| Aksen | `#f2994a` oranye hangat | Hanya untuk penanda penting: badge garansi, garis judul seksi, hover sosial lama. |
| Status | hijau `#186b4a`, merah `#c0392b`, kuning `#b07d16`, biru `#2a6f97` | Warna fungsi untuk validasi dan status, selalu berpasangan dengan teks atau ikon. |

Tidak ada warna kelima di luar fungsi status, dan aksen tidak dipakai untuk elemen dekoratif.

## Tipografi

- Inter (400 sampai 800). Alasan: sudah dipakai proyek asli 2025, sangat terbaca untuk teks Indonesia, dan netral saat menampilkan angka harga.
- Judul halaman dan judul produk memakai ukuran fluid `clamp()` supaya tidak pecah di layar sempit.

## Dials

**ENERGY 2 / RHYTHM 2 / MOTION 2**

- ENERGY 2: hero terasa hangat karena maskot dan warna brand, sementara isi halaman tetap kalem.
- RHYTHM 2: satu pola konsisten (judul halaman, filter, grid konten) dengan variasi di hero dan halaman detail.
- MOTION 2: transisi hover/focus halus dan animasi masuk sekali jalan. Tidak ada animasi berulang tanpa akhir, termasuk pada maskot.

## Keputusan dan alasan

- **Bentuk**: kartu 1 sampai 1.25rem, input 0.65rem, tombol aksi pill. Variasi radius menjaga hierarki antara wadah, isian, dan aksi.
- **Bayangan**: hanya muncul sebagai penanda elevasi pada kartu dan tombol saat hover, bukan pada semua elemen.
- **Ikon**: Bootstrap Icons karena sudah dimuat di semua halaman utama, dan dipilih menurut makna: keranjang untuk beli, koin untuk poin, kado untuk merchandise. Ikon yang tidak punya makna jelas tidak dipakai.
- **Foto barang**: rasio 4:3 pada kartu dan 1:1 pada halaman detail, `object-fit: contain`, supaya produk tidak terpotong.
- **Tema**: hanya mode terang. Alasan: foto barang adalah konten utama marketplace, dan latar netral terang menjaga persepsi warna produk. Bila nanti ditambah mode gelap, kedua mode harus diuji penuh.
- **Bahasa**: campuran Indonesia dan istilah teknis Inggris seperlunya, mengikuti teks dan struktur dari proyek asli.

## Batasan yang dipegang

- Tidak ada karakter em dash di teks antarmuka.
- Tidak ada angka statistik, testimoni, atau klaim tanpa sumber nyata. Angka yang tampil dihitung dari basis data.
- Tidak ada tautan navigasi atau tombol yang tidak punya tujuan. Ikon sosial pada footer dihapus karena ReuseMart belum punya akun resmi; yang tersisa adalah `mailto:` dan `tel:`.
- Setiap daftar data punya keadaan kosong, sedang memuat, dan gagal, minimal salah satu pesan yang menjelaskan langkah berikutnya.
- Setiap keputusan visual harus bisa dijelaskan dalam satu baris; alasan di atas adalah catatannya.

## Aksesibilitas

- Kontras teks minimal 4.5:1 untuk teks normal dan 3:1 untuk teks besar atau ikon; diperiksa dengan skrip `contrast-check.py` dari antislop-human.
- Fokus keyboard memakai outline 3px warna brand, putih pada permukaan gelap.
- Target sentuh di mobile minimal 44x44 px, termasuk tombol kecil di dalam tabel.
