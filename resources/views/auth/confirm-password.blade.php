<x-guest-layout>
    <div class="text-center mb-6">
        <div class="w-16 h-16 rounded-full bg-amber-500/10 flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-shield-alt text-2xl text-amber-500"></i>
        </div>
        <h2 class="text-xl font-poppins font-bold text-gray-900 dark:text-white">Konfirmasi Password</h2>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">
            Ini adalah area aman. Silakan konfirmasi password kamu sebelum melanjutkan.
        </p>
    </div>

    <form method="POST" action="{{ route('password.confirm') }}">
        @csrf

        <!-- Password -->
        <div>
            <x-input-label for="password" value="Password" />
            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="current-password" placeholder="Masukkan password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="mt-6">
            <x-primary-button class="w-full justify-center py-3">
                <i class="fas fa-check-circle mr-2"></i> Konfirmasi
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
