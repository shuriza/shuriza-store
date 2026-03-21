<x-guest-layout>
    <div class="text-center mb-6">
        <div class="w-16 h-16 rounded-full bg-peri/10 flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-envelope-open-text text-2xl text-peri"></i>
        </div>
        <h2 class="text-xl font-poppins font-bold text-gray-900 dark:text-white">Verifikasi Email</h2>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">
            Terima kasih sudah mendaftar! Sebelum melanjutkan, silakan verifikasi alamat email kamu dengan mengklik link yang baru saja kami kirim. Jika tidak menerima email, kami akan dengan senang hati mengirim ulang.
        </p>
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="mb-4 p-3 rounded-xl bg-green-50 dark:bg-green-500/10 border border-green-200 dark:border-green-500/20 text-sm text-green-700 dark:text-green-400">
            <i class="fas fa-check-circle mr-1"></i> Link verifikasi baru telah dikirim ke alamat email yang kamu daftarkan.
        </div>
    @endif

    <div class="mt-4 flex items-center justify-between">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <x-primary-button>
                <i class="fas fa-paper-plane mr-2"></i> Kirim Ulang Email
            </x-primary-button>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="text-sm text-gray-500 dark:text-gray-400 hover:text-peri transition-colors">
                Keluar
            </button>
        </form>
    </div>
</x-guest-layout>
