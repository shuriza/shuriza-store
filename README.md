# Shuriza Store

Selamat datang di repositori **Shuriza Store**! Website ini adalah platform e-commerce produk digital (dan fisik) yang dikembangkan khusus untuk melayani pelanggan Shuriza Store cabang Kediri.

🌐 **Website Live / Beta:** [https://shurizastore.my.id/](https://shurizastore.my.id/)

---

## 📸 Tampilan / Screenshots

> *Catatan: Tambahkan screenshot asli website ke folder `docs/` atau ubah path gambarnya sesuai kebutuhan Anda.*

|   Halaman Utama (Homepage)   |           Katalog Produk           |
| :--------------------------: | :--------------------------------: |
| ![Homepage](docs/homepage.png) | ![Katalog Produk](docs/products.png) |

| Halaman Checkout / Keranjang |         Dashboard Admin         |
| :--------------------------: | :------------------------------: |
|   ![Checkout](docs/cart.png)   | ![Dashboard Admin](docs/admin.png) |

*(Buat folder `docs` di root project dan letakkan gambar `.png` Anda di sana untuk menampilkan foto di atas, atau bisa upload langsung ke GitHub via web dan copy link-nya ke sini.)*

---

## ✨ Fitur Utama (Features)

Project ini dibuat dengan basis Laravel 12 dan mengusung berbagai fitur lengkap untuk e-commerce:

### 🛍️ Katalog & Belanja (User Features)

- **Tampilan Dark Mode / Modern UI:** Desain antarmuka eksklusif yang responsif dan cepat menggunakan TailwindCSS & Alpine.js.
- **Katalog & Filter Produk:** Etalase produk digital tersortir berdasarkan kategori, harga termurah, termahal, dan terpopuler.
- **Sistem Keranjang Belanja Ganda (Cart Duality):** Mendukung pengalaman belanja tanpa harus login *(Guest Checkout)* menyimpan di Session, serta otomatis bergabung jika user login.
- **Pemesanan via WhatsApp (WhatsApp Checkout):** Redirect otomatis dengan teks format pemesanan saat user menyelesaikan pesanan menuju nomor Admin.
- **Manajemen Akun User:** History pesanan, pengaturan profil.
- **Fitur Tambahan Frontend:** Review / Ulasan Produk, Artikel, FAQ, dan Halaman Halaman Informasi (Tentang Kami, dll.) serta auto-generate sitemap.

### 🛡️ Sistem Manajemen (Admin Dashboard)

- **Manajemen Produk:** Tambah, edit, update stok, atur diskon harga produk dengan auto-generated slug URL.
- **Manajemen Kategori:** Organisasi daftar produk per kategori.
- **Manajemen Pesanan:** Overview semua pesanan masuk, update status order (`pending` -> `processing` -> `completed` / `cancelled`).
- **Sistem Notifikasi & Alert:** Notifikasi low-stock email dan update status order.
- **Banners & Promo:** Mengatur Slider Banner untuk promosi di halaman utama.
- **Manajemen Kupon (Coupons):** Pembuatan kode diskon (fixed / persentase diskon) beserta limit kuota per kupon.

### ⚙️ Teknis & Arsitektur

- **Backend Framework:** Laravel 12 dengan arsitektur Database SQLite (dev) atau MySQL (prod).
- **Konfigurasi Role:** RBAC (Role-Based Access Control) sederhana. Pembagian otoritas *Admin* dan *User*.
- **SEO & Sitemap Automation:** Sitemap otomatis yang dirender melalui controller / views `sitemap.blade.php`.

---

## 🛠️ Persyaratan & Instalasi (Installation Setup)

### Spesifikasi Kebutuhan

- **PHP** >= 8.2
- **Composer** v2+
- **Node.js** & NPM

### Setup Environment Lokal (Development)

Untuk menjalankan environment Shuriza Store secara lokal, ikuti langkah-langkah mudah di bawah ini:

1. **Clone repository ini**

   ```bash
   git clone https://github.com/shuriza/shuriza-store.git
   cd shuriza-store
   ```
2. **Jalankan Setup Script (All-in-One)**
   Project ini memiliki perintah otomatis untuk menginstall seluruh *dependencies*, generate key, migrasi tabel, serta build asset frontend:

   ```bash
   composer run setup
   ```
3. **Seeding Database Cepat**
   Masukkan data dummy (Kategori, User, Admin, Produk)

   ```bash
   php artisan db:seed
   ```

   > **Kredensial Default:**
   >
   > - Admin: `admin@shurizastore.com` / `admin123`
   > - User: `user@example.com` / `password`
   >
4. **Storage Link**
   Ekspos link media (untuk gambar produk & banner):

   ```bash
   php artisan storage:link
   ```
5. **Start Development Server**
   Menjalankan Server Laravel + Vite Watch secara bersamaan:

   ```bash
   composer run dev
   ```

## 📬 Pembayaran & Webhook (In Development)

Shuriza Store telah dipersiapkan *(scaffolded)* untuk mendukung gateway pembayaran otomatis **Midtrans** & **Xendit**, termasuk dengan arsitektur pendeteksian Webhook Payment yang menampung duplikasi request (`ayment_webhook_events`), dan scheduler *Replay Webhooks*. Saat ini metode checkout utama diarahkan langsung ke WhatsApp Admin.

---

## 📑 Lisensi (License)

Source code project Shuriza Store ini terbuka untuk keperluan **belajar, edukasi, dan penggunaan pribadi**.

**PERINGATAN KERAS:** Dilarang keras untuk memperjualbelikan (resell) source code atau project ini dalam bentuk apapun tanpa izin tertulis dari kreator asli.

*Built with ❤️ in Kediri.*`<p align="center"><a href="https://laravel.com" target="_blank">``<img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a>``</p>`

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>
