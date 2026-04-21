<x-guest-layout>
    <div class="text-center mb-6">
        <h2 class="text-xl font-poppins font-bold text-gray-900 dark:text-white">Buat Akun Baru</h2>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Daftar untuk mulai belanja di {{ setting('store_name', 'Shuriza Store') }}</p>
    </div>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Nama -->
        <div>
            <x-input-label for="name" value="Nama Lengkap" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" placeholder="Masukkan nama lengkap" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email -->
        <div class="mt-4">
            <x-input-label for="email" value="Email" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" placeholder="nama@email.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Phone -->
        <div class="mt-4">
            <x-input-label for="phone" value="Nomor WhatsApp" />
            <span class="text-xs text-gray-400 dark:text-gray-500">(opsional)</span>
            <x-text-input id="phone" class="block mt-1 w-full" type="tel" name="phone" :value="old('phone')" autocomplete="tel" placeholder="08xxxxxxxxxx" />
            <x-input-error :messages="$errors->get('phone')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" value="Password" />
            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" placeholder="Minimal 8 karakter" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Konfirmasi Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" value="Konfirmasi Password" />
            <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="Ulangi password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="mt-6">
            <x-primary-button class="w-full justify-center py-3">
                <i class="fas fa-user-plus mr-2"></i> Daftar
            </x-primary-button>
        </div>

        <p class="text-center text-sm text-gray-500 dark:text-gray-400 mt-6">
            Sudah punya akun?
            <a href="{{ route('login') }}" class="text-peri hover:text-peri-dark dark:hover:text-peri-light font-semibold transition-colors">Masuk di sini</a>
        </p>
    </form>
</x-guest-layout>
