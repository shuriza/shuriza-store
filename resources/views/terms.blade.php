@extends('layouts.app')

@section('title', 'Syarat & Ketentuan')

@section('content')
<div class="py-8 md:py-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6">

        {{-- Header --}}
        <div class="text-center mb-10">
            <div class="w-16 h-16 rounded-2xl bg-peri/10 flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-file-contract text-2xl text-peri"></i>
            </div>
            <h1 class="text-3xl md:text-4xl font-poppins font-bold text-gray-900 dark:text-white mb-3">
                Syarat &amp; Ketentuan
            </h1>
            <p class="text-gray-500 dark:text-gray-400 max-w-2xl mx-auto">
                Terakhir diperbarui: {{ date('d F Y') }}
            </p>
        </div>

        {{-- Content --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-white/10 p-6 md:p-8 space-y-8">

            <section>
                <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-3 flex items-center gap-2">
                    <i class="fas fa-handshake text-peri text-sm"></i> Ketentuan Umum
                </h2>
                <div class="text-sm text-gray-600 dark:text-gray-400 leading-relaxed space-y-3">
                    <p>
                        Dengan menggunakan layanan {{ setting('store_name', 'Shuriza Store Kediri') }}, kamu menyetujui syarat dan ketentuan berikut.
                        Jika kamu tidak setuju dengan ketentuan ini, mohon untuk tidak menggunakan layanan kami.
                    </p>
                    <ul class="list-disc list-inside space-y-1.5 ml-2">
                        <li>Pengguna harus memberikan informasi yang akurat dan benar saat melakukan pemesanan.</li>
                        <li>Setiap akun hanya boleh digunakan oleh pemilik akun yang terdaftar.</li>
                        <li>Kami berhak menolak atau membatalkan pesanan jika terdeteksi adanya penyalahgunaan.</li>
                    </ul>
                </div>
            </section>

            <section>
                <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-3 flex items-center gap-2">
                    <i class="fas fa-shopping-cart text-peri text-sm"></i> Pemesanan &amp; Pembayaran
                </h2>
                <div class="text-sm text-gray-600 dark:text-gray-400 leading-relaxed space-y-3">
                    <ul class="list-disc list-inside space-y-1.5 ml-2">
                        <li>Harga yang tertera di website adalah harga final dalam Rupiah (IDR).</li>
                        <li>Pembayaran dilakukan setelah konfirmasi pesanan melalui WhatsApp.</li>
                        <li>Pesanan yang belum dibayar dalam waktu <strong>1x24 jam</strong> akan otomatis dibatalkan.</li>
                        <li>Bukti pembayaran harus dikirimkan melalui WhatsApp ke admin.</li>
                    </ul>
                </div>
            </section>

            <section>
                <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-3 flex items-center gap-2">
                    <i class="fas fa-box text-peri text-sm"></i> Pengiriman Produk Digital
                </h2>
                <div class="text-sm text-gray-600 dark:text-gray-400 leading-relaxed space-y-3">
                    <ul class="list-disc list-inside space-y-1.5 ml-2">
                        <li>Produk digital akan dikirim setelah pembayaran dikonfirmasi oleh admin.</li>
                        <li>Pengiriman dilakukan melalui WhatsApp atau email sesuai jenis produk.</li>
                        <li>Waktu pengiriman: <strong>1–24 jam</strong> pada jam operasional (08.00–22.00 WIB).</li>
                        <li>Pesanan di luar jam operasional akan diproses pada hari kerja berikutnya.</li>
                    </ul>
                </div>
            </section>

            <section id="kebijakan-refund">
                <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-3 flex items-center gap-2">
                    <i class="fas fa-undo text-peri text-sm"></i> Kebijakan Refund
                </h2>
                <div class="text-sm text-gray-600 dark:text-gray-400 leading-relaxed space-y-3">
                    <p>Mengingat sifat produk digital yang tidak bisa dikembalikan, kebijakan refund kami adalah sebagai berikut:</p>
                    <ul class="list-disc list-inside space-y-1.5 ml-2">
                        <li><strong>Refund penuh</strong> diberikan jika produk tidak dapat digunakan sesuai deskripsi dan kami tidak dapat menyediakan penggantian.</li>
                        <li><strong>Penggantian produk</strong> akan menjadi opsi utama jika produk bermasalah.</li>
                        <li><strong>Tidak ada refund</strong> untuk produk yang sudah digunakan atau jika masalah terjadi karena kesalahan pengguna.</li>
                        <li>Pengajuan refund harus dilakukan dalam waktu <strong>7 hari</strong> setelah pembelian.</li>
                        <li>Proses refund membutuhkan waktu <strong>1–3 hari kerja</strong> setelah disetujui.</li>
                    </ul>
                </div>
            </section>

            <section>
                <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-3 flex items-center gap-2">
                    <i class="fas fa-ban text-peri text-sm"></i> Larangan
                </h2>
                <div class="text-sm text-gray-600 dark:text-gray-400 leading-relaxed space-y-3">
                    <p>Pengguna dilarang untuk:</p>
                    <ul class="list-disc list-inside space-y-1.5 ml-2">
                        <li>Menjual kembali produk yang dibeli dari {{ setting('store_name', 'Shuriza Store') }} tanpa izin.</li>
                        <li>Menggunakan produk untuk aktivitas ilegal atau melanggar hukum.</li>
                        <li>Melakukan tindakan yang merugikan {{ setting('store_name', 'Shuriza Store') }} atau pengguna lain.</li>
                        <li>Menyalahgunakan sistem order, akun, atau fitur website.</li>
                    </ul>
                </div>
            </section>

            <section>
                <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-3 flex items-center gap-2">
                    <i class="fas fa-gavel text-peri text-sm"></i> Perubahan Ketentuan
                </h2>
                <p class="text-sm text-gray-600 dark:text-gray-400 leading-relaxed">
                    {{ setting('store_name', 'Shuriza Store') }} berhak mengubah Syarat &amp; Ketentuan ini kapan saja tanpa pemberitahuan sebelumnya.
                    Perubahan akan efektif segera setelah dipublikasikan di halaman ini.
                    Penggunaan layanan setelah perubahan berlaku dianggap sebagai persetujuan atas ketentuan baru.
                </p>
            </section>

            <section>
                <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-3 flex items-center gap-2">
                    <i class="fas fa-envelope text-peri text-sm"></i> Kontak
                </h2>
                <p class="text-sm text-gray-600 dark:text-gray-400 leading-relaxed">
                    Untuk pertanyaan tentang Syarat &amp; Ketentuan ini, hubungi kami melalui:
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
