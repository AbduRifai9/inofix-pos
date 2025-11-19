# Aplikasi (Point Of Sales) sederhana

Aplikasi *Point of Sale (POS)* sederhana yang dibangun menggunakan framework *Laravel 12* dan *Database MySQL*. Aplikasi ini digunakan untuk mencatat transaksi penjualan, mengelola data produk dan pelanggan, serta menghitung diskon secara otomatis berdasarkan total nilai transaksi.

## Tech Stack

- Laravel 12.x
- Database MySQL
- Bootstrap

## Author

- [Abdu Rifai](https://github.com/AbduRifai9)

## Fitur Utama

| No | Nama Fitur                 | Deskripsi                                                                                     | Status       |
|----|-----------------------------|-----------------------------------------------------------------------------------------------|--------------|
| 1  | *Autentikasi*             | Login sederhana untuk pengguna. Akses fitur lain dibatasi hanya untuk pengguna login.           | ✅ Done      |
| 2  | *Manajemen Produk*        | CRUD produk (kode, nama, harga, stok) + pencarian & pagination.                                 | ✅ Done      |
| 6  | *Manajemen Transaksi*     | Simpan transaksi + detail item, update stok otomatis setiap pembelian.                          | ✅ Done      |
| 3  | *Manajemen Pelanggan*     | CRUD pelanggan (nama, no telepon, email (opsional)). Bisa ditambahkan langsung dari halaman transaksi. | ✅ Done      |
| 4  | *Transaksi Penjualan (POS)* | Pilih customer, tambahkan produk, input qty, otomatis hitung subtotal, diskon, dan total.     | ✅ Done      |
| 5  | *Skema Diskon Otomatis*   | Diskon otomatis berdasarkan total belanja (≥500K → 10%, ≥1JT → 15%).                            | ✅ Done      |
| 7  | *Riwayat Transaksi*       | Lihat daftar transaksi yang sudah berhasil melakukan transaksi.                                                    | ✅ Done      |
| 6 | Dashboard Admin            | Menampilkan ringkasan data seperti jumlah produk & penjualan.                                   | ✅ Done |

## Skema Database

Berikut adalah skema database utama yang digunakan pada aplikasi POS sederhana ini:

| No | Nama Tabel | Deskripsi |
|---|------------|-----------|
| 1 | users | Menyimpan data akun pengguna yang dapat login ke sistem. |
| 3 | products | Menyimpan data produk/barang yang dijual. |
| 4 | customers | Menyimpan data pelanggan. |
| 5 | transactions | Menyimpan data transaksi penjualan (header). |
| 6 | transaction_items | Menyimpan detail barang yang dibeli dalam satu transaksi. |

## Diagram Database
![Diagram Database](https://github.com/AbduRifai9/inofix-pos/blob/master/inofix-pos.png)

## Skema Diskon

Jika total belanja ≥ Rp 500.000 → diskon 10%
Jika total belanja ≥ Rp 1.000.000 → diskon 15%
Diskon hanya berlaku jika memenuhi syarat & dihitung otomatis oleh sistem.

## Panduan Instalasi Project

1. *Clone Repository*
```bash
git clone https://github.com/AbduRifai9/inofix-pos.git
```

2. *Buka terminal, lalu ketik*
```bash
cd inofix-pos
composer install
php artisan key:generate
```

3. *Buka .env lalu ubah baris berikut sesuaikan dengan database yang anda miliki*
```bash
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=root
DB_PASSWORD=
```

4. *Jalankan bash*
```bash
php artisan config:cache
php artisan storage:link
php artisan route:clear
atau
php artisan optimize
```

5. *Jalankan migrations dan seeders*
```bash
php artisan migrate --seed
```

6. *Jalankan website menggunakan terminal*
```bash
php artisan serve
```

## Jika ada pertanyaan silahkan hubungi email dibawah ini:
```bash
abdurifai70@gmail.com
```
