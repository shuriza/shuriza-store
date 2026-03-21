@extends('layouts.app')

@section('title', 'Kebijakan Privasi')

@section('content')
<div class="py-8 md:py-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6">

        {{-- Header --}}
        <div class="text-center mb-10">
            <div class="w-16 h-16 rounded-2xl bg-peri/10 flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-shield-alt text-2xl text-peri"></i>
            </div>
            <h1 class="text-3xl md:text-4xl font-poppins font-bold text-gray-900 dark:text-white mb-3">
                Kebijakan Privasi
            </h1>
            <p class="text-gray-500 dark:text-gray-400 max-w-2xl mx-auto">
                Terakhir diperbarui: {{ date('d F Y') }}
            </p>
        </div>

        {{-- Content --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-white/10 p-6 md:p-8 space-y-8">

            <section>
                <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-3 flex items-center gap-2">
                    <i class="fas fa-info-circle text-peri text-sm"></i> Pendahuluan
                </h2>
                <p class="text-sm text-gray-600 dark:text-gray-400 leading-relaxed">
                    {{ setting('store_name', 'Shuriza Store Kediri') }} ("kami") menghargai privasi setiap pengguna layanan kami.
                    Kebijakan Privasi ini menjelaskan bagaimana kami mengumpulkan, menggunakan, dan melindungi
                    informasi pribadi kamu saat menggunakan website dan layanan kami.
                </p>
            </section>

            <section>
                <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-3 flex items-center gap-2">
                    <i class="fas fa-database text-peri text-sm"></i> Informasi yang Kami Kumpulkan
                </h2>
                <div class="text-sm text-gray-600 dark:text-gray-400 leading-relaxed space-y-3">
                    <p>Kami mengumpulkan informasi berikut saat kamu menggunakan layanan kami:</p>
                    <ul class="list-disc list-inside space-y-1.5 ml-2">
                        <li><strong>Informasi akun:</strong> Nama, alamat email, dan password saat mendaftar.</li>
                        <li><strong>Informasi pesanan:</strong> Nama lengkap, nomor WhatsApp, email, dan catatan pesanan saat checkout.</li>
                        <li><strong>Informasi teknis:</strong> Alamat IP, jenis browser, dan informasi perangkat untuk keamanan dan analitik.</li>
                        <li><strong>Data sesi:</strong> Informasi keranjang belanja untuk pengguna tamu menggunakan session ID.</li>
                    </ul>
                </div>
            </section>

            <section>
                <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-3 flex items-center gap-2">
                    <i class="fas fa-cog text-peri text-sm"></i> Penggunaan Informasi
                </h2>
                <div class="text-sm text-gray-600 dark:text-gray-400 leading-relaxed space-y-3">
                    <p>Informasi yang kami kumpulkan digunakan untuk:</p>
                    <ul class="list-disc list-inside space-y-1.5 ml-2">
                        <li>Memproses dan mengirimkan pesanan kamu.</li>
                        <li>Berkomunikasi mengenai pesanan melalui WhatsApp atau email.</li>
                        <li>Meningkatkan kualitas layanan dan pengalaman pengguna.</li>
                        <li>Mencegah penipuan dan menjaga keamanan platform.</li>
                        <li>Mengirimkan informasi produk baru atau promosi (hanya jika kamu menyetujui).</li>
                    </ul>
                </div>
            </section>

            <section>
                <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-3 flex items-center gap-2">
                    <i class="fas fa-lock text-peri text-sm"></i> Keamanan Data
                </h2>
                <p class="text-sm text-gray-600 dark:text-gray-400 leading-relaxed">
                    Kami menggunakan langkah-langkah keamanan yang wajar untuk melindungi informasi pribadi kamu,
                    termasuk enkripsi password dan penggunaan protokol HTTPS. Namun, tidak ada metode transmisi
                    melalui internet yang 100% aman, dan kami tidak dapat menjamin keamanan absolut.
                </p>
            </section>

            <section>
                <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-3 flex items-center gap-2">
                    <i class="fas fa-share-alt text-peri text-sm"></i> Pembagian Informasi
                </h2>
                <p class="text-sm text-gray-600 dark:text-gray-400 leading-relaxed">
                    Kami <strong>tidak menjual, memperdagangkan, atau memindahkan</strong> informasi pribadi kamu
                    kepada pihak ketiga. Informasi hanya akan dibagikan jika diwajibkan oleh hukum yang berlaku
                    atau untuk melindungi hak dan keamanan kami.
                </p>
            </section>

            <section>
                <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-3 flex items-center gap-2">
                    <i class="fas fa-user-cog text-peri text-sm"></i> Hak Pengguna
                </h2>
                <div class="text-sm text-gray-600 dark:text-gray-400 leading-relaxed space-y-3">
                    <p>Kamu memiliki hak untuk:</p>
                    <ul class="list-disc list-inside space-y-1.5 ml-2">
                        <li>Mengakses dan memperbarui informasi profil melalui halaman Edit Profil.</li>
                        <li>Menghapus akun dan seluruh data terkait kapan saja.</li>
                        <li>Meminta salinan data pribadi yang kami simpan.</li>
                    </ul>
                </div>
            </section>

            <section>
                <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-3 flex items-center gap-2">
                    <i class="fas fa-envelope text-peri text-sm"></i> Kontak
                </h2>
                <p class="text-sm text-gray-600 dark:text-gray-400 leading-relaxed">
                    Jika kamu memiliki pertanyaan mengenai Kebijakan Privasi ini, silakan hubungi kami melalui:
                </p>
                <div class="mt-3 flex flex-col gap-2 text-sm text-gray-600 dark:text-gray-400">
                    <span><i class="fab fa-whatsapp text-green-500 mr-2"></i> <a href="https://wa.me/{{ setting('whatsapp_number') }}" class="text-peri hover:underline">WhatsApp Admin</a></span>
                    <span><i class="fas fa-envelope text-peri mr-2"></i> <a href="mailto:{{ setting('store_email', 'admin@shurizastore.com') }}" class="text-peri hover:underline">{{ setting('store_email', 'admin@shurizastore.com') }}</a></span>
                </div>
            </section>

        </div>

    </div>
</div>
@endsection
