@extends('layouts.app')

@section('title', 'Tentang Kami')

@section('content')
<div class="py-8 md:py-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6">

        {{-- Header --}}
        <div class="text-center mb-10">
            <div class="w-16 h-16 rounded-2xl bg-peri/10 flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-store text-2xl text-peri"></i>
            </div>
            <h1 class="text-3xl md:text-4xl font-poppins font-bold text-gray-900 dark:text-white mb-3">
                Tentang {{ setting('store_name', 'Shuriza Store') }}
            </h1>
            <p class="text-gray-500 dark:text-gray-400 max-w-2xl mx-auto">
                Toko produk digital terpercaya di Kediri, Jawa Timur.
            </p>
        </div>

        {{-- Main Content --}}
        <div class="space-y-6">

            {{-- Siapa Kami --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-white/10 p-6 md:p-8">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 rounded-xl bg-peri/10 flex items-center justify-center">
                        <i class="fas fa-users text-peri"></i>
                    </div>
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white">Siapa Kami?</h2>
                </div>
                <div class="text-gray-600 dark:text-gray-300 leading-relaxed space-y-3">
                    <p>
                        <strong class="text-gray-900 dark:text-white">{{ setting('store_name', 'Shuriza Store Kediri') }}</strong> adalah toko online yang menyediakan
                        berbagai produk digital berkualitas dengan harga terjangkau. Kami berdedikasi untuk memberikan layanan terbaik
                        kepada pelanggan di seluruh Indonesia.
                    </p>
                    <p>
                        Berdiri sejak awal, kami terus berkembang dan berkomitmen untuk menjadi pilihan utama dalam penyediaan
                        produk digital — mulai dari akun streaming, software, game, hingga voucher dan layanan digital lainnya.
                    </p>
                </div>
            </div>

            {{-- Kenapa Memilih Kami --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-white/10 p-6 md:p-8">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-10 h-10 rounded-xl bg-accent/10 flex items-center justify-center">
                        <i class="fas fa-star text-accent"></i>
                    </div>
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white">Kenapa Memilih Kami?</h2>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="flex gap-3">
                        <div class="shrink-0 w-8 h-8 rounded-lg bg-peri/10 flex items-center justify-center">
                            <i class="fas fa-bolt text-sm text-peri"></i>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900 dark:text-white text-sm">Pengiriman Instan</h3>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Produk dikirim langsung setelah pembayaran dikonfirmasi.</p>
                        </div>
                    </div>
                    <div class="flex gap-3">
                        <div class="shrink-0 w-8 h-8 rounded-lg bg-accent/10 flex items-center justify-center">
                            <i class="fas fa-shield-alt text-sm text-accent"></i>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900 dark:text-white text-sm">Garansi Produk</h3>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Setiap produk dilengkapi garansi sesuai ketentuan.</p>
                        </div>
                    </div>
                    <div class="flex gap-3">
                        <div class="shrink-0 w-8 h-8 rounded-lg bg-secondary/10 flex items-center justify-center">
                            <i class="fas fa-headset text-sm text-secondary"></i>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900 dark:text-white text-sm">Layanan Responsif</h3>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Tim kami siap membantu via WhatsApp kapan saja.</p>
                        </div>
                    </div>
                    <div class="flex gap-3">
                        <div class="shrink-0 w-8 h-8 rounded-lg bg-blue-500/10 flex items-center justify-center">
                            <i class="fas fa-tags text-sm text-blue-500"></i>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900 dark:text-white text-sm">Harga Terjangkau</h3>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Harga kompetitif dengan kualitas terjamin.</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Lokasi --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-white/10 p-6 md:p-8">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 rounded-xl bg-secondary/10 flex items-center justify-center">
                        <i class="fas fa-map-marker-alt text-secondary"></i>
                    </div>
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white">Lokasi</h2>
                </div>
                <div class="text-gray-600 dark:text-gray-300 leading-relaxed">
                    <p>
                        Kami berbasis di <strong class="text-gray-900 dark:text-white">Kediri, Jawa Timur</strong>, Indonesia.
                        Meskipun beroperasi secara online, kami melayani pelanggan dari seluruh Indonesia tanpa batasan wilayah.
                    </p>
                </div>
            </div>

            {{-- Hubungi Kami --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-white/10 p-6 md:p-8">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 rounded-xl bg-green-500/10 flex items-center justify-center">
                        <i class="fab fa-whatsapp text-green-500"></i>
                    </div>
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white">Hubungi Kami</h2>
                </div>
                <div class="text-gray-600 dark:text-gray-300 leading-relaxed space-y-3">
                    <p>Punya pertanyaan atau butuh bantuan? Jangan ragu untuk menghubungi kami:</p>
                    <div class="flex flex-wrap gap-3">
                        <a href="https://wa.me/{{ setting('whatsapp_number') }}"
                           target="_blank" rel="noopener"
                           class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-green-500/10 text-green-600 dark:text-green-400 text-sm font-semibold hover:bg-green-500/20 transition">
                            <i class="fab fa-whatsapp"></i> WhatsApp
                        </a>
                        @if(setting('instagram_handle'))
                        <a href="https://instagram.com/{{ setting('instagram_handle') }}"
                           target="_blank" rel="noopener"
                           class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-pink-500/10 text-pink-600 dark:text-pink-400 text-sm font-semibold hover:bg-pink-500/20 transition">
                            <i class="fab fa-instagram"></i> Instagram
                        </a>
                        @endif
                        @if(setting('telegram_handle'))
                        <a href="https://t.me/{{ setting('telegram_handle') }}"
                           target="_blank" rel="noopener"
                           class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-blue-500/10 text-blue-600 dark:text-blue-400 text-sm font-semibold hover:bg-blue-500/20 transition">
                            <i class="fab fa-telegram"></i> Telegram
                        </a>
                        @endif
                    </div>
                </div>
            </div>

        </div>

        {{-- CTA --}}
        <div class="mt-10 text-center">
            <a href="{{ route('products.index') }}"
               class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-peri text-white font-semibold shadow-lg shadow-peri/25 hover:bg-peri-dark transition">
                <i class="fas fa-shopping-bag"></i> Mulai Belanja Sekarang
            </a>
        </div>

    </div>
</div>
@endsection
