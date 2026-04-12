# Shuriza Store

Selamat datang di repositori **Shuriza Store**! Website ini adalah platform e-commerce produk digital (dan fisik) yang dikembangkan khusus untuk melayani pelanggan Shuriza Store cabang Kediri.

🌐 **Website Live / Beta:** [https://shurizastore.my.id/](https://shurizastore.my.id/)

---

## 📸 Tampilan / Screenshots

> *Catatan: Tambahkan screenshot asli website ke folder `docs/` atau ubah path gambarnya sesuai kebutuhan Anda.*

| Halaman Utama (Homepage) | Katalog Produk |
| :---: | :---: |
| ![Homepage](docs/homepage.png) | ![Katalog Produk](docs/products.png) |

| Halaman Checkout / Keranjang | Dashboard Admin |
| :---: | :---: |
| ![Checkout](docs/cart.png) | ![Dashboard Admin](docs/admin.png) |

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
   > - Admin: `admin@shurizastore.com` / `admin123`
   > - User: `user@example.com` / `password`

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
Proyek Shuriza Store dibuat secara internal eksklusif. Dilarang menggandakan repositori atau menjual source code ini tanpa izin dari kreator asli.

*Built with ❤️ in Kediri.*<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework. You can also check out [Laravel Learn](https://laravel.com/learn), where you will be guided through building a modern Laravel application.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development)**
- **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
