@extends('layouts.app')

@section('title', 'Hubungi Kami')

@section('content')
<div class="py-8 md:py-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6">

        {{-- Header --}}
        <div class="text-center mb-10">
            <div class="w-16 h-16 rounded-2xl bg-peri/10 flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-headset text-2xl text-peri"></i>
            </div>
            <h1 class="text-3xl md:text-4xl font-poppins font-bold text-gray-900 dark:text-white mb-3">
                Hubungi Kami
            </h1>
            <p class="text-gray-500 dark:text-gray-400 max-w-2xl mx-auto">
                Punya pertanyaan atau butuh bantuan? Tim kami siap membantu Anda!
            </p>
        </div>

        {{-- Contact Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            
            {{-- WhatsApp --}}
            <a href="https://wa.me/{{ setting('whatsapp_number') }}" target="_blank"
               class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-white/10 p-6 hover:border-green-500 dark:hover:border-green-500 transition-all hover:shadow-lg hover:shadow-green-500/10 group">
                <div class="flex items-start gap-4">
                    <div class="shrink-0 w-12 h-12 rounded-xl bg-green-500/10 flex items-center justify-center group-hover:bg-green-500 transition-colors">
                        <i class="fab fa-whatsapp text-2xl text-green-500 group-hover:text-white transition-colors"></i>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-1">WhatsApp</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-2">Respon cepat via chat</p>
                        <p class="text-sm font-semibold text-green-600 dark:text-green-400">{{ format_phone_display(setting('whatsapp_number')) }}</p>
                    </div>
                    <i class="fas fa-external-link-alt text-gray-300 dark:text-gray-600 text-sm group-hover:text-green-500 transition-colors"></i>
                </div>
            </a>

            {{-- Email --}}
            <a href="mailto:{{ setting('store_email', 'admin@shurizastore.com') }}"
               class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-white/10 p-6 hover:border-peri dark:hover:border-peri transition-all hover:shadow-lg hover:shadow-peri/10 group">
                <div class="flex items-start gap-4">
                    <div class="shrink-0 w-12 h-12 rounded-xl bg-peri/10 flex items-center justify-center group-hover:bg-peri transition-colors">
                        <i class="fas fa-envelope text-2xl text-peri group-hover:text-white transition-colors"></i>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-1">Email</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-2">Untuk pertanyaan detail</p>
                        <p class="text-sm font-semibold text-peri">{{ setting('store_email', 'admin@shurizastore.com') }}</p>
                    </div>
                    <i class="fas fa-external-link-alt text-gray-300 dark:text-gray-600 text-sm group-hover:text-peri transition-colors"></i>
                </div>
            </a>

            {{-- Instagram --}}
            @if(setting('instagram_handle'))
            <a href="https://instagram.com/{{ setting('instagram_handle') }}" target="_blank"
               class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-white/10 p-6 hover:border-pink-500 dark:hover:border-pink-500 transition-all hover:shadow-lg hover:shadow-pink-500/10 group">
                <div class="flex items-start gap-4">
                    <div class="shrink-0 w-12 h-12 rounded-xl bg-pink-500/10 flex items-center justify-center group-hover:bg-gradient-to-br group-hover:from-purple-500 group-hover:to-pink-500 transition-all">
                        <i class="fab fa-instagram text-2xl text-pink-500 group-hover:text-white transition-colors"></i>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-1">Instagram</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-2">Follow untuk update produk</p>
                        <p class="text-sm font-semibold text-pink-600 dark:text-pink-400">@{{ setting('instagram_handle') }}</p>
                    </div>
                    <i class="fas fa-external-link-alt text-gray-300 dark:text-gray-600 text-sm group-hover:text-pink-500 transition-colors"></i>
                </div>
            </a>
            @endif

            {{-- Telegram --}}
            @if(setting('telegram_handle'))
            <a href="https://t.me/{{ setting('telegram_handle') }}" target="_blank"
               class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-white/10 p-6 hover:border-blue-500 dark:hover:border-blue-500 transition-all hover:shadow-lg hover:shadow-blue-500/10 group">
                <div class="flex items-start gap-4">
                    <div class="shrink-0 w-12 h-12 rounded-xl bg-blue-500/10 flex items-center justify-center group-hover:bg-blue-500 transition-colors">
                        <i class="fab fa-telegram text-2xl text-blue-500 group-hover:text-white transition-colors"></i>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-1">Telegram</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-2">Channel & grup komunitas</p>
                        <p class="text-sm font-semibold text-blue-600 dark:text-blue-400">@{{ setting('telegram_handle') }}</p>
                    </div>
                    <i class="fas fa-external-link-alt text-gray-300 dark:text-gray-600 text-sm group-hover:text-blue-500 transition-colors"></i>
                </div>
            </a>
            @endif
        </div>

        {{-- Lokasi & Jam Operasional --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-white/10 p-6 md:p-8 space-y-6">
            
            {{-- Alamat --}}
            <div>
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-10 h-10 rounded-xl bg-peri/10 flex items-center justify-center">
                        <i class="fas fa-map-marker-alt text-peri"></i>
                    </div>
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white">Alamat Toko</h2>
                </div>
                <p class="text-sm text-gray-600 dark:text-gray-400 leading-relaxed ml-[52px]">
                    {{ setting('store_address', 'Jl. Raya Kertosono-Kediri No. 2, Desa Muneng, Kec. Purwoasri, Kab. Kediri, Jawa Timur 64154') }}
                </p>
            </div>

            {{-- Jam Operasional --}}
            <div>
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-10 h-10 rounded-xl bg-accent/10 flex items-center justify-center">
                        <i class="fas fa-clock text-accent"></i>
                    </div>
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white">Jam Operasional</h2>
                </div>
                <div class="ml-[52px] space-y-2 text-sm">
                    <div class="flex items-center justify-between max-w-sm">
                        <span class="text-gray-600 dark:text-gray-400">Senin – Minggu</span>
                        <span class="font-semibold text-gray-900 dark:text-white">08.00 – 22.00 WIB</span>
                    </div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 italic">
                        * Pesanan di luar jam operasional akan diproses pada hari kerja berikutnya
                    </p>
                </div>
            </div>

            {{-- Respon Time --}}
            <div>
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-10 h-10 rounded-xl bg-secondary/10 flex items-center justify-center">
                        <i class="fas fa-bolt text-secondary"></i>
                    </div>
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white">Waktu Respon</h2>
                </div>
                <div class="ml-[52px] space-y-2 text-sm text-gray-600 dark:text-gray-400">
                    <p><i class="fas fa-check-circle text-green-500 mr-2"></i> WhatsApp: <strong>1-5 menit</strong> (jam operasional)</p>
                    <p><i class="fas fa-check-circle text-green-500 mr-2"></i> Email: <strong>1-24 jam</strong></p>
                    <p><i class="fas fa-check-circle text-green-500 mr-2"></i> Pengiriman produk: <strong>1-24 jam</strong> setelah pembayaran dikonfirmasi</p>
                </div>
            </div>
        </div>

        {{-- FAQ Shortcut --}}
        <div class="mt-8 bg-gradient-to-br from-peri/10 to-secondary/10 dark:from-peri/5 dark:to-secondary/5 rounded-2xl border border-peri/20 dark:border-peri/10 p-6 text-center">
            <i class="fas fa-question-circle text-3xl text-peri mb-3"></i>
            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Pertanyaan Umum?</h3>
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                Cek halaman FAQ kami untuk jawaban cepat atas pertanyaan yang sering ditanyakan.
            </p>
            <a href="{{ route('pages.faq') }}"
               class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-peri text-white font-semibold shadow-lg shadow-peri/25 hover:shadow-peri/40 hover:-translate-y-0.5 transition-all duration-200">
                <i class="fas fa-book text-sm"></i> Lihat FAQ
            </a>
        </div>

    </div>
</div>
@endsection
