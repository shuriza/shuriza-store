@extends('layouts.app')

@section('title', 'Cara Pembelian')

@section('content')
<div class="py-8 md:py-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6">

        {{-- Header --}}
        <div class="text-center mb-10">
            <div class="w-16 h-16 rounded-2xl bg-peri/10 flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-shopping-bag text-2xl text-peri"></i>
            </div>
            <h1 class="text-3xl md:text-4xl font-poppins font-bold text-gray-900 dark:text-white mb-3">
                Cara Pembelian
            </h1>
            <p class="text-gray-500 dark:text-gray-400 max-w-2xl mx-auto">
                Panduan lengkap untuk membeli produk digital di {{ setting('store_name', 'Shuriza Store Kediri') }}.
            </p>
        </div>

        {{-- Steps --}}
        <div class="space-y-6">

            {{-- Step 1 --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-white/10 p-6 flex gap-5">
                <div class="shrink-0 w-12 h-12 rounded-xl bg-peri/10 flex items-center justify-center">
                    <span class="text-lg font-bold text-peri">1</span>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Pilih Produk</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 leading-relaxed">
                        Jelajahi berbagai produk digital kami melalui halaman <a href="{{ route('products.index') }}" class="text-peri hover:underline">Produk</a>.
                        Gunakan filter kategori atau fitur pencarian untuk menemukan produk yang kamu butuhkan.
                        Klik pada produk untuk melihat detail lengkap termasuk deskripsi, harga, dan ketersediaan stok.
                    </p>
                </div>
            </div>

            {{-- Step 2 --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-white/10 p-6 flex gap-5">
                <div class="shrink-0 w-12 h-12 rounded-xl bg-peri/10 flex items-center justify-center">
                    <span class="text-lg font-bold text-peri">2</span>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Tambah ke Keranjang</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 leading-relaxed">
                        Klik tombol <strong>"Tambah ke Keranjang"</strong> pada produk yang ingin kamu beli.
                        Kamu bisa mengatur jumlah item yang diinginkan. Keranjang belanja bisa diakses kapan saja melalui ikon keranjang di pojok kanan atas.
                        Kamu bisa berbelanja tanpa perlu login terlebih dahulu.
                    </p>
                </div>
            </div>

            {{-- Step 3 --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-white/10 p-6 flex gap-5">
                <div class="shrink-0 w-12 h-12 rounded-xl bg-peri/10 flex items-center justify-center">
                    <span class="text-lg font-bold text-peri">3</span>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Checkout &amp; Isi Data</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 leading-relaxed">
                        Setelah produk siap, klik <strong>"Lanjut Checkout"</strong>.
                        Isi data diri kamu: nama lengkap, nomor WhatsApp, dan email (opsional).
                        Kamu juga bisa menambahkan catatan khusus untuk pesanan.
                    </p>
                </div>
            </div>

            {{-- Step 4 --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-white/10 p-6 flex gap-5">
                <div class="shrink-0 w-12 h-12 rounded-xl bg-peri/10 flex items-center justify-center">
                    <span class="text-lg font-bold text-peri">4</span>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Konfirmasi via WhatsApp</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 leading-relaxed">
                        Setelah order dibuat, kamu akan diarahkan ke halaman sukses dengan tombol WhatsApp.
                        Klik tombol tersebut untuk mengirim detail pesanan langsung ke admin {{ setting('store_name', 'Shuriza Store') }}.
                        Admin akan memproses pesanan dan memberikan instruksi pembayaran melalui WhatsApp.
                    </p>
                </div>
            </div>

            {{-- Step 5 --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-white/10 p-6 flex gap-5">
                <div class="shrink-0 w-12 h-12 rounded-xl bg-green-500/10 flex items-center justify-center">
                    <span class="text-lg font-bold text-green-500">5</span>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Pembayaran &amp; Pengiriman</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 leading-relaxed">
                        Lakukan pembayaran sesuai instruksi admin. Setelah pembayaran dikonfirmasi,
                        produk digital akan dikirimkan melalui WhatsApp atau email kamu.
                        Proses pengiriman biasanya dilakukan dalam waktu <strong>1–24 jam</strong> setelah pembayaran diterima.
                    </p>
                </div>
            </div>

        </div>

        {{-- CTA --}}
        <div class="mt-10 text-center">
            <div class="bg-gradient-to-r from-peri/10 to-pink-500/10 rounded-2xl border border-peri/20 p-8">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Ada pertanyaan?</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-5">Jangan ragu untuk menghubungi kami melalui WhatsApp.</p>
                <a href="https://wa.me/{{ setting('whatsapp_number') }}" target="_blank"
                   class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-green-500 text-white font-semibold text-sm
                          shadow-lg shadow-green-500/25 hover:shadow-green-500/40 hover:-translate-y-0.5 transition-all duration-200">
                    <i class="fab fa-whatsapp"></i> Chat Admin
                </a>
            </div>
        </div>

    </div>
</div>
@endsection
