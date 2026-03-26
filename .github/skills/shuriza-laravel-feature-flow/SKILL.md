---
name: shuriza-laravel-feature-flow
description: 'Implementasi fitur Laravel e-commerce di Shuriza Store dengan perubahan aman pada routing, scope model, konsistensi Blade layout, aturan cart/checkout/order, serta verifikasi berbasis dampak perubahan.'
argument-hint: 'Jelaskan fitur atau bug yang ingin dikerjakan, area terdampak (frontend/admin/cart/order), dan batasannya.'
user-invocable: true
---

# Alur Fitur Laravel Shuriza

## Hasil Skill Ini
Alur implementasi lengkap untuk perubahan di Shuriza Store agar meminimalkan regresi pada routing, role, perilaku cart, alur order, dan konsistensi UI Blade.

## Kapan Digunakan
- Menambah atau mengubah fitur frontend pada halaman produk, cart, checkout, profil, atau konten.
- Menambah atau mengubah fitur admin yang dijaga middleware admin.
- Mengubah model, scope, accessor, atau transisi status untuk katalog/order.
- Refactor tanpa mengubah perilaku bisnis.

## Input Yang Dikumpulkan Dulu
1. Tujuan perubahan: fitur baru, perbaikan bug, refactor, atau penyesuaian UX.
2. Permukaan terdampak: route, controller, model, view, dan test.
3. Batasan utama:
- Pertahankan logika role (`admin` vs `user`) dan proteksi middleware.
- Pertahankan dualitas cart (`user_id` atau `session_id`).
- Pertahankan alur order (guest checkout tetap boleh) kecuali diminta berubah.

## Prosedur
1. Petakan ruang lingkup perubahan.
- Tentukan file yang akan disentuh di `routes/web.php`, `app/Http/Controllers/`, `app/Models/`, dan `resources/views/`.
- Pastikan layout yang dipakai tepat: publik (`layouts.app`) atau admin (`layouts.admin`).

2. Validasi aturan bisnis sebelum edit.
- Jika terkait produk, pastikan perilaku `slug` dan route key tetap valid.
- Jika terkait harga, pertahankan penyimpanan integer IDR dan gunakan accessor format harga.
- Jika terkait daftar kategori/produk, gunakan scope yang sudah ada (`active`, `inStock`, `sorted`, `ordered`).
- Jika terkait order, pertahankan format nomor order dan lifecycle status kecuali requirement mengubahnya.

3. Implementasikan perubahan sekecil mungkin namun aman.
- Pertahankan API publik dan penamaan saat ini kecuali requirement mengharuskan perubahan.
- Utamakan update controller/service yang memanfaatkan helper query dan scope model yang sudah ada.
- Untuk view, pertahankan pola komponen Blade dan gaya visual yang sudah berlaku.

4. Terapkan keputusan bercabang.
- Jika fitur khusus admin: wajib `auth` + `admin` middleware dan gunakan admin layout.
- Jika fitur mendukung guest: pastikan alur cart/order berbasis session tetap berjalan tanpa login.
- Jika aksi admin memakai request JSON: pastikan perilaku `expectsJson()` tetap kompatibel.
- Jika perlu setting baru: ambil dari pola config yang ada, jangan hardcode.

5. Tambah atau sesuaikan test sesuai dampak.
- Tambahkan test feature/unit yang relevan untuk perilaku yang berubah.
- Cakup minimal satu happy path dan satu boundary/failure path jika dampak perubahan signifikan.
- Gunakan fixture minimal dan asumsi factory/seed yang sudah ada.

6. Verifikasi dan cek regresi.
- Jalankan test yang relevan terhadap area yang berubah; jalankan suite penuh (`composer run test`) bila dampaknya lintas modul atau berisiko tinggi.
- Cek halaman Blade yang disentuh agar tidak ada mismatch layout, link/form rusak, atau regresi UX.
- Validasi alur kritikal yang terdampak (cart, checkout, WhatsApp link pada order success, atau aksi admin).

## Quality Gates (Definisi Selesai)
- Kompatibilitas aturan bisnis:
- Perilaku role dan middleware tidak berubah kecuali diminta.
- Perilaku cart dan checkout untuk guest/auth tetap konsisten kecuali diminta berubah.
- Konvensi model tetap terjaga (slug, scope, cast, label/badge status order).
- Konsistensi UI:
- Layout yang dipilih benar (`layouts.app` vs `layouts.admin`).
- Pola komponen/style yang sudah ada tetap dipertahankan.
- Validasi:
- Test yang relevan lulus.
- Tidak ada error lint/build/runtime baru pada file yang diubah.

## Format Output Respons Agen
1. Ringkasan solusi (apa yang berubah).
2. Daftar perubahan per file.
3. Catatan perilaku dan edge case.
4. Hasil verifikasi serta risiko tersisa.

## Contoh Slash Prompt
- `/shuriza-laravel-feature-flow Tambahkan filter produk berdasarkan kategori dan harga di halaman katalog.`
- `/shuriza-laravel-feature-flow Perbaiki bug checkout tamu agar order tetap bisa dibuat tanpa login.`
- `/shuriza-laravel-feature-flow Tambah aksi admin untuk bulk update status order dengan response JSON.`
