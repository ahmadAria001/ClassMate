# KawanDesa

KawanDesa adalah sebuah sistem informasi RT/RW yang bertujuan untuk meningkatkan efisiensi dan integrasi dalam administrasi tingkat RT/RW. Sistem ini dirancang dengan fokus pada integrasi data masyarakat, memudahkan akses informasi bagi warga, dan mendukung proses administrasi yang lebih transparan dan efektif. Dengan fitur-fitur seperti manajemen data penduduk yang terpusat, pelaporan pengaduan warga yang sistematis, pengelolaan iuran yang terstruktur, dan penerbitan surat keterangan yang otomatis, produk ini akan memberikan kemudahan bagi warga dalam mengakses informasi terkini dan memperoleh pelayanan administrasi yang lebih cepat dan efisien.

## Persyaratan

- PHP >= 7.3
- Composer
- Node.js >= 12
- pnpm

Pastikan Anda telah menginstal semua persyaratan sebelum melanjutkan. Jika belum, maka Anda bisa melihat cara mendownload dan installnya disini:
- PHP
	- https://sko.dev/snippet/cara-install-php-di-windows
- Composer
	- https://www.hostinger.co.id/tutorial/cara-install-composer
- Node.js
	- https://radixweb.com/blog/installing-npm-and-nodejs-on-windows-and-mac
- pnpm
	- https://rifqiazam.medium.com/pnpm-package-manager-js-yang-bikin-hemat-penyimpanan-7ada499787c0

## Instalasi

1. Clone repo ini:
```bash
   https://github.com/ahmadAria001/KawanDesa.git
```
2. Masuk ke direktori
```bash
cd KawanDesa
```
3. Install dependencies PHP menggunakan Composer
```bash
composer install
```
4.  Install dependencies Node.js menggunakan pnpm:
```bash
pnpm install
```
5. Buat file `.env` dari `.env.example` dan sesuaikan konfigurasi database:
```
cp .env.example .env
```
6. Generate key aplikasi:
```bash
php artisan key:generate
```
7. Migrasi dan seeding database:
```bash
php artisan migrate --seed
```
8. Jalankan Svelte dengan pnpm
```bash
pnpm dev
```
9. Buka terminal baru lain dan jalankan laravel
```bash
php artisan serve
```
Aplikasi akan berjalan di `http://localhost:8000` dan anda bisa memulai dengan masuk ke halaman login dengan `http://localhost:8000/login`.
