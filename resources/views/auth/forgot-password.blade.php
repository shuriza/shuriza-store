<x-guest-layout>
    <div class="text-center mb-6">
        <h2 class="text-xl font-poppins font-bold text-gray-900 dark:text-white">Lupa Password</h2>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
            Masukkan email kamu dan kami akan mengirimkan link untuk mengatur ulang password.
        </p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <!-- Email -->
        <div>
            <x-input-label for="email" value="Email" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus placeholder="nama@email.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="mt-6">
            <x-primary-button class="w-full justify-center py-3">
                <i class="fas fa-envelope mr-2"></i> Kirim Link Reset Password
            </x-primary-button>
        </div>

        <p class="text-center text-sm text-gray-500 dark:text-gray-400 mt-6">
            Ingat password kamu?
            <a href="{{ route('login') }}" class="text-peri hover:text-peri-dark dark:hover:text-peri-light font-semibold transition-colors">Kembali ke login</a>
        </p>
    </form>
</x-guest-layout>
