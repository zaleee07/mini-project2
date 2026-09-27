# Product Manager

Mini project Pemrograman Web Pertemuan 3.

## Teknologi
- PHP
- MySQL
- PDO
- HTML
- CSS Flexbox

## Fitur
- Create produk
- Read/list produk
- Update produk
- Delete produk dengan POST + CSRF
- Validasi server-side
- Prepared statement PDO
- Escaping output dengan htmlspecialchars
- Post-Redirect-Get (PRG)
- Search/filter dengan GET
- UI responsif

## Cara menjalankan di XAMPP

### 1. Salin project
Ekstrak folder `product-manager` ke:

`C:\xampp\htdocs\`

Hasil akhirnya:

`C:\xampp\htdocs\product-manager\`

### 2. Jalankan XAMPP
Aktifkan:
- Apache
- MySQL

### 3. Import database
Buka:

`http://localhost/phpmyadmin`

Pilih menu **Import**, kemudian pilih:

`product-manager/database/store_db.sql`

Klik **Import/Go**.

Database `store_db` dan tabel `products` akan dibuat otomatis.

### 4. Periksa koneksi database
File:

`config/db.php`

Konfigurasi default XAMPP:

- Host: localhost
- Database: store_db
- Username: root
- Password: kosong

Jika konfigurasi MySQL kamu berbeda, ubah file tersebut.

### 5. Jalankan aplikasi

Buka:

`http://localhost/product-manager/public/`

## Pengujian yang disarankan

1. Tambah produk valid.
2. Coba nama kurang dari 3 karakter.
3. Coba harga negatif.
4. Coba stok negatif.
5. Coba nama produk duplikat.
6. Edit produk.
7. Hapus produk.
8. Refresh setelah menambah produk dan pastikan tidak terjadi duplikasi.
9. Masukkan `<b>Promo</b>` sebagai nama produk. Tag harus tampil sebagai teks, bukan menjadi HTML.
10. Gunakan fitur pencarian berdasarkan nama atau kategori.
11. Uji tampilan pada layar sempit.

## Struktur

product-manager/
├── config/db.php
├── public/index.php
├── public/create.php
├── public/edit.php
├── public/delete.php
├── public/assets/style.css
├── database/store_db.sql
└── README.md
