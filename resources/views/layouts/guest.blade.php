<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', setting('store_name', 'Shuriza Store')) }}</title>
        @if(setting('store_favicon'))
            <link rel="icon" href="{{ asset('storage/' . setting('store_favicon')) }}" type="image/png">
        @endif

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700|poppins:600,700&display=swap" rel="stylesheet" />

        <!-- Font Awesome -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- Dark mode FOUC prevention -->
        <script>
            if (localStorage.getItem('darkMode') === 'true' || (!localStorage.getItem('darkMode') && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        </script>
    </head>
    <body class="font-sans text-gray-900 dark:text-gray-100 antialiased bg-gray-50 dark:bg-peri-darkest">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0">
            <div>
                <a href="/" class="flex items-center gap-2">
                    @if(setting('store_logo'))
                        <img src="{{ asset('storage/' . setting('store_logo')) }}" alt="{{ setting('store_name', 'Shuriza Store') }}" class="w-10 h-10 rounded-xl object-contain">
                    @else
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-peri to-peri-dark flex items-center justify-center">
                            <span class="text-white font-poppins font-bold text-lg">S</span>
                        </div>
                    @endif
                    <span class="font-poppins font-bold text-xl text-gray-900 dark:text-white">{{ setting('store_name', 'Shuriza Store') }}</span>
                </a>
            </div>

            <div class="w-full sm:max-w-md mt-6 px-6 py-6 bg-white dark:bg-gray-800 shadow-sm dark:shadow-none border border-gray-200 dark:border-gray-700 overflow-hidden rounded-2xl">
                {{ $slot }}
            </div>

            <a href="{{ url('/') }}" class="mt-4 text-sm text-gray-500 dark:text-gray-400 hover:text-peri transition-colors flex items-center gap-1.5">
                <i class="fas fa-arrow-left text-xs"></i> Kembali ke Toko
            </a>
        </div>
    </body>
</html>
