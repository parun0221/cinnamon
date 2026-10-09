# CINNAMON — Sistem Informasi Inventaris dan Transaksi Produk Rempah-Rempah

**Studi Kasus: Toko Wahana Eka Sentral**

CINNAMON merupakan aplikasi berbasis web yang dikembangkan untuk membantu pengelolaan inventaris dan transaksi produk rempah-rempah pada Toko Wahana Eka Sentral. Sistem ini menyediakan pencatatan barang masuk, pengelolaan persediaan, transaksi barang keluar, penerapan metode *First In First Out* (FIFO), serta laporan penjualan dan keuntungan berdasarkan data transaksi.

Aplikasi ini dikembangkan menggunakan Laravel sebagai framework backend dan MySQL sebagai sistem manajemen basis data. Sistem dirancang untuk membantu proses pencatatan persediaan menjadi lebih terstruktur, memudahkan pemantauan stok, serta mendukung penyajian informasi penjualan dan laba berdasarkan Harga Pokok Penjualan (HPP).

---

## Daftar Isi

- [Tentang Project](#tentang-project)
- [Fitur Utama](#fitur-utama)
- [Teknologi yang Digunakan](#teknologi-yang-digunakan)
- [Persyaratan Sistem](#persyaratan-sistem)
- [Instalasi](#instalasi)
- [Konfigurasi Environment](#konfigurasi-environment)
- [Konfigurasi Database](#konfigurasi-database)
- [Menjalankan Aplikasi](#menjalankan-aplikasi)
- [Alur Bisnis Sistem](#alur-bisnis-sistem)
- [Metode FIFO dan Pengelolaan Batch](#metode-fifo-dan-pengelolaan-batch)
- [Perhitungan HPP dan Laba](#perhitungan-hpp-dan-laba)
- [Struktur Direktori](#struktur-direktori)
- [Pengujian](#pengujian)
- [Keamanan Aplikasi](#keamanan-aplikasi)
- [Troubleshooting](#troubleshooting)
- [Pengembangan Selanjutnya](#pengembangan-selanjutnya)
- [Lisensi](#lisensi)

---

## Tentang Project

### Latar Belakang

Pengelolaan persediaan produk rempah-rempah membutuhkan pencatatan barang masuk, barang keluar, dan perubahan stok secara konsisten. Pencatatan yang kurang terstruktur dapat menyulitkan pemantauan ketersediaan barang, penelusuran asal persediaan, dan penghitungan keuntungan penjualan.

CINNAMON dikembangkan untuk mendukung kebutuhan tersebut melalui pengelolaan persediaan berbasis web. Sistem menyimpan data transaksi dan persediaan dalam basis data sehingga informasi stok serta hasil penjualan dapat ditelusuri kembali berdasarkan catatan yang tersedia.

### Tujuan

1. Mempermudah pencatatan barang masuk dan barang keluar.
2. Membantu pemantauan stok siap dan stok pending.
3. Mengelompokkan persediaan ke dalam batch berdasarkan periode penerimaan.
4. Menerapkan metode FIFO pada pengeluaran persediaan.
5. Menghitung HPP dan laba dari transaksi barang keluar.
6. Menyediakan laporan penjualan dan keuntungan berdasarkan rentang tanggal.
7. Menyediakan riwayat stok untuk membantu pemeriksaan dan koreksi persediaan.

---

## Fitur Utama

### 1. Manajemen Produk

- Pencatatan data produk rempah-rempah.
- Pengelolaan kategori produk.
- Penyimpanan informasi satuan, harga, dan gambar produk.
- Pemantauan stok siap dan stok pending.

### 2. Pencatatan Barang Masuk

- Pencatatan transaksi penerimaan barang.
- Penyimpanan detail produk, berat, dan harga modal.
- Pemeriksaan serta persetujuan barang masuk.
- Penambahan stok siap setelah proses persetujuan sesuai aturan aplikasi.
- Pencatatan riwayat perubahan stok.

### 3. Pengelolaan Batch Stok

- Pengelompokan stok berdasarkan periode penerimaan.
- Pencatatan total stok dan jumlah stok yang telah keluar.
- Pemantauan sisa stok setiap batch.
- Pencatatan harga modal untuk kebutuhan perhitungan HPP.
- Dukungan koreksi stok batch.

### 4. Transaksi Barang Keluar

- Pencatatan transaksi barang keluar.
- Pengurangan stok berdasarkan jumlah barang yang dikeluarkan.
- Penerapan urutan pengeluaran stok menggunakan FIFO.
- Penyimpanan subtotal transaksi.
- Pencatatan HPP dan laba pada detail transaksi apabila telah diimplementasikan pada versi aplikasi yang digunakan.
- Pencatatan riwayat perubahan stok.

### 5. Laporan Penjualan dan Laba

- Penyaringan laporan berdasarkan rentang tanggal.
- Rekapitulasi penjualan produk.
- Rekapitulasi HPP berdasarkan transaksi.
- Perhitungan laba berdasarkan selisih nilai penjualan dan HPP.
- Pengelompokan laporan berdasarkan produk dan kategori apabila tersedia pada antarmuka laporan.

### 6. Riwayat dan Koreksi Stok

- Pencatatan aktivitas stok masuk, stok keluar, dan penyesuaian.
- Pemantauan perubahan stok siap dan stok pending.
- Koreksi stok dengan pencatatan selisih dan keterangan.
- Pemeriksaan sisa stok berdasarkan data batch.

---

## Teknologi yang Digunakan

| Teknologi | Kegunaan |
|---|---|
| PHP | Bahasa pemrograman backend |
| Laravel 12 | Framework aplikasi web |
| MySQL | Sistem manajemen basis data |
| Vite 6 | Pengelolaan aset frontend |
| Blade | Template antarmuka Laravel |
| Bootstrap/CSS | Komponen dan styling antarmuka, sesuai implementasi project |
| JavaScript | Interaksi antarmuka |
| Composer | Pengelolaan dependensi PHP |
| Node.js dan npm | Pengelolaan dependensi frontend |

> Catatan: versi PHP, MySQL, Node.js, npm, Bootstrap, dan dependensi lain harus disesuaikan dengan konfigurasi aktual project.

---

## Persyaratan Sistem

Pastikan perangkat pengembangan telah memiliki:

- PHP dengan versi yang memenuhi persyaratan Laravel 12 dan dependensi project.
- Composer.
- MySQL atau MariaDB yang kompatibel.
- Node.js dan npm.
- Git, jika project diambil dari repository.
- Web browser modern.

Untuk memeriksa instalasi perangkat lunak, jalankan:

```bash
php -v
composer --version
node -v
npm -v
```

Pastikan layanan MySQL berjalan sebelum menjalankan aplikasi.

---

## Instalasi

### 1. Clone Repository

Jika project telah disimpan di repository Git:

```bash
git clone <URL_REPOSITORY>
cd <NAMA_FOLDER_PROJECT>
```

Ganti `<URL_REPOSITORY>` dengan alamat repository dan `<NAMA_FOLDER_PROJECT>` dengan nama direktori project yang sebenarnya.

Jika project sudah tersedia secara lokal, buka terminal pada direktori root project.

### 2. Instal Dependensi Backend

Jalankan:

```bash
composer install
```

Perintah tersebut memasang dependensi PHP berdasarkan file `composer.lock`.

### 3. Instal Dependensi Frontend

Jalankan:

```bash
npm install
```

Perintah tersebut memasang dependensi frontend yang didefinisikan dalam `package.json`.

### 4. Buat File Environment

Salin file konfigurasi contoh:

```bash
cp .env.example .env
```

Pada Windows Command Prompt, jika diperlukan, gunakan:

```bat
copy .env.example .env
```

### 5. Generate Application Key

Jalankan:

```bash
php artisan key:generate
```

Laravel akan mengisi `APP_KEY` pada file `.env`.

### 6. Buat Database

Buka MySQL melalui phpMyAdmin, MySQL Workbench, atau terminal MySQL. Buat database dengan nama yang sesuai, misalnya:

```sql
CREATE DATABASE cinnamon;
```

Nama database tersebut hanya contoh. Sesuaikan dengan konfigurasi project.

### 7. Jalankan Migration

Pastikan koneksi database pada `.env` sudah benar, kemudian jalankan:

```bash
php artisan migrate
```

Perintah tersebut membuat tabel berdasarkan migration yang tersedia.

Jika project memiliki seeder dan memang memerlukannya untuk data awal, jalankan:

```bash
php artisan db:seed
```

Jangan menjalankan seeder secara otomatis pada database produksi sebelum memeriksa data yang akan dimasukkan.

### 8. Buat Symbolic Link untuk File Publik

Jika aplikasi menyimpan gambar produk melalui Laravel public storage, jalankan:

```bash
php artisan storage:link
```

Langkah ini diperlukan jika implementasi penyimpanan file menggunakan disk `public`.

---

## Konfigurasi Environment

Contoh konfigurasi dasar `.env`:

```dotenv
APP_NAME=CINNAMON
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://127.0.0.1:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=cinnamon
DB_USERNAME=root
DB_PASSWORD=
```

Sesuaikan nama database, username, password, host, dan port dengan konfigurasi MySQL lokal.

**Catatan keamanan:**

- Jangan mengunggah file `.env` ke repository publik.
- Jangan membagikan `APP_KEY`, kredensial database, atau informasi rahasia lainnya.
- Gunakan `APP_DEBUG=false` pada lingkungan produksi.
- Gunakan kredensial database dengan hak akses minimum yang diperlukan.

---

## Menjalankan Aplikasi

### 1. Jalankan Server Laravel

Buka terminal pertama:

```bash
php artisan serve
```

Secara default, aplikasi dapat diakses melalui:

`http://127.0.0.1:8000`

### 2. Jalankan Vite

Buka terminal kedua:

```bash
npm run dev
```

Vite akan menjalankan server pengembangan untuk aset frontend.

Biarkan proses tersebut berjalan selama pengembangan apabila aplikasi menggunakan Vite untuk memuat CSS dan JavaScript.

### 3. Build Aset untuk Produksi

Untuk membuat aset frontend siap produksi:

```bash
npm run build
```

### 4. Perintah Laravel yang Berguna

```bash
php artisan route:list
php artisan migrate:status
php artisan optimize:clear
php artisan test
```

Gunakan `route:list` untuk memeriksa route yang tersedia, `migrate:status` untuk memeriksa status migration, dan `optimize:clear` untuk membersihkan cache konfigurasi serta optimasi Laravel.

---

## Alur Bisnis Sistem

### 1. Penerimaan Barang

1. Admin mencatat transaksi barang masuk.
2. Detail produk dan berat penerimaan disimpan.
3. Barang diperiksa dan disetujui sesuai prosedur sistem.
4. Setelah disetujui, stok siap produk diperbarui.
5. Sistem memperbarui atau membuat batch stok sesuai periode tanggal penerimaan.
6. Riwayat stok dicatat untuk membantu penelusuran perubahan persediaan.

### 2. Pengelolaan Batch

Batch digunakan untuk mengelompokkan penerimaan stok ke dalam periode tertentu. Pada rancangan sistem ini, periode batch dibagi menjadi dua bagian dalam satu bulan:

- Periode pertama: tanggal 1–15.
- Periode kedua: tanggal 16 sampai akhir bulan.

Sebagai contoh, penerimaan produk pada 3 Januari dan 12 Januari dapat masuk ke batch periode pertama Januari untuk produk yang sama. Penerimaan pada 20 Januari masuk ke batch periode kedua.

Pengelompokan ini merupakan pendekatan batch periodik yang digunakan oleh aplikasi, bukan berarti setiap batch hanya berisi satu transaksi penerimaan.

### 3. Pengeluaran Barang

1. Admin mencatat transaksi barang keluar.
2. Sistem memeriksa ketersediaan stok.
3. Sistem mencari batch yang masih mempunyai sisa stok.
4. Stok dialokasikan mulai dari batch dengan tanggal awal paling lama.
5. Jika stok batch pertama tidak mencukupi, kebutuhan dilanjutkan ke batch berikutnya.
6. Sistem memperbarui jumlah stok keluar dan sisa stok.
7. Stok produk dan riwayat stok diperbarui.
8. Nilai penjualan, HPP, dan laba disimpan sesuai implementasi transaksi.

> Penting: proses pengeluaran harus dibatalkan jika total stok yang tersedia tidak mencukupi. Seluruh perubahan stok, batch, dan transaksi sebaiknya dilakukan dalam satu database transaction agar tidak terjadi pencatatan yang hanya tersimpan sebagian.

---

## Metode FIFO dan Pengelolaan Batch

FIFO (*First In First Out*) merupakan metode pengeluaran persediaan yang memprioritaskan stok masuk paling awal untuk dikeluarkan terlebih dahulu.

Dalam CINNAMON, FIFO diterapkan pada batch stok. Sistem memproses batch secara berurutan berdasarkan tanggal awal batch, kemudian mengurangi stok batch hingga kebutuhan transaksi terpenuhi.

Contoh:

| Batch | Total Stok | Stok Keluar Sebelumnya | Sisa |
|---|---:|---:|---:|
| JAN-1-2026 | 50 Kg | 0 Kg | 50 Kg |
| JAN-2-2026 | 70 Kg | 0 Kg | 70 Kg |

Jika terjadi transaksi barang keluar sebanyak 60 Kg, alokasi FIFO adalah:

| Batch | Alokasi Keluar | Sisa Setelah Transaksi |
|---|---:|---:|
| JAN-1-2026 | 50 Kg | 0 Kg |
| JAN-2-2026 | 10 Kg | 60 Kg |
| **Total** | **60 Kg** | **60 Kg** |

Perhitungan tersebut merupakan ilustrasi alokasi FIFO. Nilai aktual bergantung pada stok yang tersedia dan transaksi sebelumnya.

---

## Perhitungan HPP dan Laba

### 1. Subtotal Penjualan

Subtotal merupakan nilai penjualan untuk satu detail transaksi.

**Rumus:**

`Subtotal = Jumlah Barang × Harga Jual per Satuan`

### 2. Harga Pokok Penjualan

HPP merupakan biaya modal persediaan yang dikeluarkan untuk memenuhi transaksi penjualan.

Apabila barang yang dijual berasal dari beberapa batch dengan harga modal berbeda, HPP dihitung dari total biaya setiap alokasi batch.

**Rumus:**

`HPP = Σ (Jumlah Keluar dari Batch × Harga Modal Batch)`

### 3. Laba Kotor Penjualan

Laba kotor penjualan merupakan selisih antara nilai penjualan dan HPP.

**Rumus:**

`Laba Kotor = Total Penjualan − Total HPP`

Contoh, transaksi mengeluarkan 60 Kg dengan alokasi:

- 50 Kg dari batch dengan harga modal Rp10.000/Kg.
- 10 Kg dari batch dengan harga modal Rp12.000/Kg.
- Harga jual seluruh barang Rp15.000/Kg.

Maka:

```text
Total Penjualan
= 60 × Rp15.000
= Rp900.000

HPP
= (50 × Rp10.000) + (10 × Rp12.000)
= Rp620.000

Laba Kotor
= Rp900.000 − Rp620.000
= Rp280.000
```

Laba kotor tersebut belum dikurangi biaya operasional, biaya penyimpanan, transportasi, atau biaya lainnya. Oleh karena itu, nilai tersebut tidak otomatis sama dengan laba bersih akuntansi.

### 4. Laporan Berdasarkan Rentang Tanggal

Laporan penjualan dan laba dapat direkap berdasarkan tanggal transaksi barang keluar.

Untuk setiap periode laporan:

- Total penjualan merupakan penjumlahan subtotal transaksi pada periode tersebut.
- Total HPP merupakan penjumlahan HPP detail transaksi pada periode tersebut.
- Laba kotor merupakan total penjualan dikurangi total HPP.

HPP sebaiknya menggunakan nilai yang dicatat pada saat transaksi dilakukan, bukan dihitung ulang dari harga modal produk terbaru. Dengan demikian, perubahan harga modal pada penerimaan berikutnya tidak mengubah laporan transaksi sebelumnya.

---

## Struktur Direktori

Struktur berikut merupakan gambaran umum struktur project Laravel. Nama controller, model, dan direktori spesifik perlu disesuaikan dengan repository aktual.

```text
CINNAMON/
├── app/
│   ├── Http/
│   │   └── Controllers/
│   └── Models/
├── bootstrap/
├── config/
├── database/
│   ├── factories/
│   ├── migrations/
│   └── seeders/
├── public/
│   └── storage/
├── resources/
│   ├── css/
│   ├── js/
│   └── views/
├── routes/
│   ├── web.php
│   └── console.php
├── storage/
├── tests/
├── .env.example
├── artisan
├── composer.json
├── package.json
├── vite.config.js
└── README.md
```

Keterangan:

- `app/Models/` berisi model untuk produk, transaksi, batch stok, dan entitas lainnya.
- `app/Http/Controllers/` berisi controller untuk menangani permintaan aplikasi.
- `database/migrations/` mendefinisikan struktur tabel.
- `database/seeders/` berisi data awal jika digunakan.
- `resources/views/` berisi template Blade.
- `routes/web.php` berisi definisi route web.
- `public/` berisi aset publik aplikasi.
- `tests/` berisi pengujian otomatis.

---

## Pengujian

Sebelum aplikasi digunakan, lakukan pengujian terhadap alur utama berikut.

| Skenario | Hasil yang Diharapkan |
|---|---|
| Menambahkan produk | Produk tersimpan dengan data yang valid |
| Mencatat barang masuk | Detail penerimaan tersimpan |
| Menyetujui barang masuk | Stok siap dan batch diperbarui satu kali |
| Menambahkan penerimaan pada periode batch yang sama | Stok masuk ditambahkan ke batch yang sesuai |
| Mengeluarkan barang | Stok produk dan batch berkurang sesuai jumlah transaksi |
| Mengeluarkan barang dari beberapa batch | Batch lama digunakan terlebih dahulu |
| Stok tidak mencukupi | Transaksi ditolak tanpa perubahan parsial |
| Menghitung HPP | HPP sesuai alokasi dan harga modal setiap batch |
| Menghitung laba | Laba kotor sesuai total penjualan dikurangi HPP |
| Menampilkan laporan bulanan | Rekap hanya mencakup transaksi pada rentang tanggal terpilih |
| Mengoreksi stok | Perubahan tercatat dan saldo persediaan konsisten |

Jalankan pengujian Laravel menggunakan:

```bash
php artisan test
```

Pastikan pengujian database dilakukan menggunakan konfigurasi pengujian tersendiri agar data pengembangan atau produksi tidak berubah.

---

## Keamanan Aplikasi

Beberapa hal yang perlu diperhatikan:

1. Gunakan validasi server-side untuk seluruh input transaksi.
2. Gunakan autentikasi dan otorisasi sesuai peran pengguna.
3. Lindungi formulir perubahan data dengan CSRF protection Laravel.
4. Gunakan database transaction untuk operasi yang mengubah beberapa tabel.
5. Gunakan `lockForUpdate()` pada proses kritis yang memerlukan penguncian baris, dalam transaksi database yang sesuai.
6. Terapkan batasan stok agar transaksi tidak menyebabkan persediaan negatif.
7. Hindari menampilkan detail exception atau kredensial internal kepada pengguna.
8. Catat alasan dan nilai selisih pada proses koreksi stok.
9. Simpan rahasia aplikasi hanya pada konfigurasi environment.
10. Lakukan backup database secara berkala.

---

## Troubleshooting

### 1. Koneksi Database Gagal

Periksa konfigurasi berikut pada `.env`:

```dotenv
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=cinnamon
DB_USERNAME=root
DB_PASSWORD=
```

Pastikan layanan MySQL berjalan dan database telah dibuat.

### 2. Perubahan `.env` Tidak Terbaca

Jalankan:

```bash
php artisan optimize:clear
```

Kemudian periksa kembali konfigurasi environment.

### 3. Tabel Belum Tersedia

Jalankan:

```bash
php artisan migrate:status
php artisan migrate
```

Periksa juga apakah migration terkait tersedia pada project.

### 4. Gambar Produk Tidak Muncul

Jika gambar disimpan pada disk `public`, jalankan:

```bash
php artisan storage:link
```

Pastikan path penyimpanan dan URL gambar sesuai dengan konfigurasi aplikasi.

### 5. Aset CSS atau JavaScript Tidak Muncul

Untuk pengembangan:

```bash
npm install
npm run dev
```

Untuk build:

```bash
npm run build
```

Periksa konfigurasi Vite dan pemanggilan aset pada template Blade jika masalah masih terjadi.

### 6. Stok Batch Tidak Sesuai

Periksa konsistensi antara:

- Stok siap pada tabel produk.
- Total stok dan stok keluar pada tabel batch.
- Detail penerimaan dan pengeluaran.
- Riwayat stok.
- Catatan koreksi stok.

Hindari mengubah saldo langsung di database tanpa menelusuri transaksi terkait dan mencatat alasan penyesuaiannya.

---

## Pengembangan Selanjutnya

Pengembangan lanjutan yang dapat dipertimbangkan:

- Pengujian otomatis untuk FIFO dan perhitungan HPP.
- Audit trail yang lebih lengkap untuk setiap perubahan persediaan.
- Pengelolaan hak akses berdasarkan peran pengguna.
- Ekspor laporan penjualan ke PDF atau Excel.
- Dashboard statistik persediaan dan penjualan.
- Peringatan stok minimum.
- Backup dan pemulihan database.
- Deployment ke server produksi dengan konfigurasi keamanan yang sesuai.

Fitur-fitur tersebut merupakan rencana pengembangan dan tidak berarti seluruhnya sudah tersedia pada versi aplikasi saat ini.

---

## Lisensi

Project ini dikembangkan untuk mendukung pengelolaan inventaris dan transaksi produk rempah-rempah pada Toko Wahana Eka Sentral sebagai bagian dari proyek akademik.

Informasi lisensi penggunaan, distribusi, dan modifikasi mengikuti ketentuan yang ditetapkan oleh pemilik project. Tambahkan file lisensi apabila project akan didistribusikan secara publik.
