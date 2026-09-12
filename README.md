# SM Sport Center — Sistem Reservasi Lapangan Futsal & Badminton

Aplikasi web untuk mengelola reservasi lapangan futsal dan badminton — menggantikan proses manual via telepon/WhatsApp yang sebelumnya menyebabkan jadwal bentrok, kesalahan pencatatan transaksi, dan sulitnya membuat laporan penggunaan lapangan.

**Status:** Proyek akademik / studi kasus uji kompetensi.

---

## Tech Stack

`Laravel 12` · `MySQL 8` · `Tailwind CSS (Vite)` · `Vanilla JavaScript` · `PHPUnit`

## Daftar Isi

- [Fitur](#fitur)  
- [Struktur Basis Data](#struktur-basis-data)  
- [Instalasi](#instalasi)  
- [Akun Testing](#akun-testing)  
- [Menjalankan Test](#menjalankan-test)  
- [Struktur Project](#struktur-project)  
- [Rencana Lanjutan](#rencana-lanjutan)

## Fitur

### 🛡️ Sisi Admin

- **Dashboard** — KPI harian (pendapatan, reservasi, okupansi, menunggu verifikasi), papan status lapangan real-time, transaksi terbaru  
- **Kelola Reservasi** — filter & pencarian, booking manual (walk-in), detail reservasi, edit jadwal, konfirmasi/tolak, kirim ulang notifikasi  
- **Data Lapangan** — CRUD lapangan, upload foto, kelola fasilitas, toggle status Aktif/Maintenance  
- **Transaksi & Keuangan** — ringkasan finansial per periode, verifikasi/tolak pembayaran dengan preview bukti transfer, export CSV

### 🙋 Sisi Pelanggan

- **Registrasi & Login** — dengan rate limiting (5x gagal → kunci 15 menit) dan fitur lupa password  
- **Dashboard** — quick-book per lapangan, jadwal main terdekat  
- **Booking Online** — alur bertahap dengan pengecekan slot jam real-time (anti double-booking) \+ pencatatan pembayaran  
- **Pesanan Saya** — riwayat & status reservasi, detail transaksi, pembatalan mandiri

## Struktur Basis Data

5 tabel inti dengan relasi sebagai berikut:

![ERD SM Sport Center]()

- **users** — pelanggan & admin dibedakan lewat kolom `role`  
- **lapangan** — data lapangan, harga, status, fasilitas  
- **reservasi** — jantung sistem, dengan `UNIQUE(lapangan_id, tanggal_main, jam_mulai)` untuk mencegah double booking di level database  
- **pembayaran** — mendukung pembayaran bertahap (DP → Pelunasan)  
- **notifikasi** — log pengiriman notifikasi ke pelanggan

>   
> 📁 Taruh file `ERD_SM_Sport_Center.png` di folder `docs/erd.png` supaya gambar di atas tampil di GitHub.

## Instalasi

\# 1\. Clone repository

git clone https://github.com/Dze17/website\_booking\_lapangan.git

cd website\_booking\_lapangan

\# 2\. Install dependency

composer install

npm install

\# 3\. Setup environment

cp .env.example .env

php artisan key:generate

\# lalu edit .env: sesuaikan DB\_DATABASE, DB\_USERNAME, DB\_PASSWORD

\# 4\. Buat database kosong (nama sesuai .env), lalu migrate

php artisan migrate

\# 5\. Hubungkan storage (supaya upload gambar bisa diakses)

php artisan storage:link

\# 6\. Daftarkan middleware admin di bootstrap/app.php:

\#    \-\>withMiddleware(function (Middleware $middleware) {

\#        $middleware-\>alias(\['admin' \=\> \\App\\Http\\Middleware\\EnsureUserIsAdmin::class\]);

\#    })

\# 7\. Isi data testing

php artisan db:seed

\# 8\. Build aset frontend

npm run build

\# 9\. Jalankan

php artisan serve

Panduan lebih detail (termasuk troubleshooting error umum) ada di [`README_INSTALASI.md`](http://README_INSTALASI.md).

## Akun Testing

| Role | Email | Password |
| :---- | :---- | :---- |
| Admin | `admin@smsportcenter.com` | `password123` |
| Pelanggan | `andika.pratama@gmail.com` | `password123` |

Daftar lengkap ada di [`AKUN_TESTING.md`](http://AKUN_TESTING.md).

## Menjalankan Test

php artisan test

Mencakup unit test modul Reservasi (pencegahan double-booking) dan Pembayaran (verifikasi, penolakan, perhitungan tagihan). Gunakan database testing terpisah (`.env.testing` atau SQLite in-memory) — lihat catatan di `README_INSTALASI.md`.

## Struktur Project

app/

├─ Http/Controllers/

│  ├─ Auth/              \# Login, Register, Forgot/Reset Password

│  ├─ Admin/             \# Dashboard, Reservasi, Lapangan, Pembayaran

│  ├─ BookingController.php

│  └─ PelangganDashboardController.php

├─ Http/Middleware/

│  └─ EnsureUserIsAdmin.php

└─ Models/

   ├─ User.php, Lapangan.php, Reservasi.php

   └─ Pembayaran.php, Notifikasi.php

resources/views/

├─ layouts/          \# auth, admin, pelanggan

├─ auth/

├─ admin/

└─ pelanggan/

routes/

├─ web.php           \# auth \+ dashboard & booking pelanggan

└─ admin.php         \# seluruh route /admin/\*

tests/Feature/

├─ ReservasiOverlapTest.php

└─ PembayaranTest.php

## Rencana Lanjutan

- [ ] Halaman "Cari Lapangan" (pencarian & filter lapangan untuk pelanggan)  
- [ ] Halaman Profil pelanggan  
- [ ] Integrasi payment gateway online (struktur data sudah mendukung)  
- [ ] Notifikasi WhatsApp via queue worker  
- [ ] Halaman Laporan dengan grafik (tren pendapatan, breakdown per lapangan)

---

*Dibangun sebagai studi kasus uji kompetensi — analisis kebutuhan, perancangan basis data, dan implementasi sistem reservasi lapangan olahraga.*  
