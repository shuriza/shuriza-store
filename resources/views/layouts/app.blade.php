<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>@yield('title', setting('store_name', 'Shuriza Store Kediri')) – Produk & Jasa Digital Premium</title>
    <meta name="description" content="@yield('description', setting('store_name', 'Shuriza Store Kediri') . ' – ' . setting('store_tagline', 'Penyedia layanan digital terpercaya') . '. Berbagai produk dan jasa digital premium dengan harga terjangkau.')" />

    {{-- Open Graph --}}
    <meta property="og:type" content="website" />
    <meta property="og:title" content="@yield('title', setting('store_name', 'Shuriza Store Kediri')) – Produk & Jasa Digital Premium" />
    <meta property="og:description" content="@yield('description', setting('store_name', 'Shuriza Store Kediri') . ' – ' . setting('store_tagline', 'Penyedia layanan digital terpercaya') . '. Berbagai produk dan jasa digital premium dengan harga terjangkau.')" />
    <meta property="og:url" content="{{ url()->current() }}" />
    <meta property="og:site_name" content="{{ setting('store_name', 'Shuriza Store Kediri') }}" />
    @if(setting('store_favicon'))
        <link rel="icon" href="{{ asset('storage/' . setting('store_favicon')) }}" type="image/png">
    @endif
    @hasSection('og_image')
        <meta property="og:image" content="@yield('og_image')" />
    @endif

    {{-- Dark mode: prevent FOUC --}}
    <script>
        (function() {
            const t = localStorage.getItem('theme');
            if (t === 'dark' || (!t && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        })();
    </script>

    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Poppins:wght@600;700;800&display=swap" rel="stylesheet" />

    {{-- Font Awesome --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />

    {{-- Iconify --}}
    <script src="https://code.iconify.design/3/3.1.0/iconify.min.js"></script>

    {{-- Vite --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        /* ── Legacy CSS Variables (backward compat for sub-pages) ── */
        :root {
            --primary: #6c63ff;
            --primary-dark: #574fd6;
            --primary-light: #8b85ff;
            --secondary: #ff6584;
            --accent: #43e97b;
            --dark: #0f0e17;
            --dark2: #1a1926;
            --dark3: #252436;
            --card-bg: #1e1d2e;
            --card-border: rgba(108,99,255,0.15);
            --text: #e8e8f0;
            --text-muted: #9898b0;
            --gradient: linear-gradient(135deg, #6c63ff, #ff6584);
        }

        /* Scrollbar */
        ::-webkit-scrollbar { width: 6px; }
        .dark ::-webkit-scrollbar-track { background: #1a1926; }
        ::-webkit-scrollbar-track { background: #f1f5f9; }
        .dark ::-webkit-scrollbar-thumb { background: #6c63ff; border-radius: 3px; }
        ::-webkit-scrollbar-thumb { background: #6c63ff; border-radius: 3px; }

        /* Toast slide-in */
        @keyframes toastIn {
            from { opacity: 0; transform: translateY(-12px) scale(.96); }
            to   { opacity: 1; transform: translateY(0) scale(1); }
        }
        .toast-animate { animation: toastIn .3s ease forwards; }

        /* Pulse for WhatsApp button */
        @keyframes waPulse {
            0%, 100% { box-shadow: 0 0 0 0 rgba(37,211,102,0.5); }
            50%      { box-shadow: 0 0 0 12px rgba(37,211,102,0); }
        }

        /* ── Legacy fadeInUp (used by sub-pages) ── */
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to   { opacity: 1; transform: translateY(0); }
        }
    </style>

    @stack('styles')
</head>
<body class="bg-gray-50 dark:bg-peri-darkest text-gray-900 dark:text-white font-sans antialiased overflow-x-hidden leading-relaxed"
      x-data="layoutApp()" @keydown.window="handleGlobalKey($event)">

{{-- Admin warning banner for shop status --}}
@if(session('shop_status_warning'))
<div class="fixed top-0 inset-x-0 z-[100] bg-amber-500 text-black text-center py-2 px-4 text-sm font-semibold">
    <i class="fas fa-exclamation-triangle mr-1"></i> {{ session('shop_status_warning') }}
    <a href="{{ route('admin.settings.index') }}" class="underline ml-2 font-bold">Buka Pengaturan</a>
</div>
<div class="h-10"></div>
@endif

{{-- ─── NAVBAR ─────────────────────────────────────────────────────────── --}}
<header class="fixed top-0 inset-x-0 z-50 transition-all duration-300"
        :class="scrolled
            ? 'bg-white/90 dark:bg-peri-darkest/95 shadow-lg shadow-black/5 dark:shadow-black/30 backdrop-blur-xl border-b border-gray-200/60 dark:border-peri/10'
            : 'bg-white/70 dark:bg-peri-darkest/80 backdrop-blur-lg border-b border-transparent'"
        id="navbar">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 flex items-center justify-between h-16 lg:h-[70px]">

        {{-- Logo --}}
        <a href="{{ route('home') }}" class="flex items-center gap-2.5 shrink-0">
            @if(setting('store_logo'))
                <img src="{{ asset('storage/' . setting('store_logo')) }}" alt="{{ setting('store_name', 'Shuriza Store') }}" class="w-9 h-9 rounded-xl object-contain">
            @else
                <span class="w-9 h-9 rounded-xl bg-gradient-to-br from-peri-light to-peri-dark flex items-center justify-center text-white text-sm">
                    <i class="fas fa-store"></i>
                </span>
            @endif
            <span class="bg-gradient-to-br from-peri-light to-peri-dark bg-clip-text text-transparent font-bold text-xl font-poppins leading-tight">
                {{ setting('store_name', 'Shuriza Store') }}
            </span>
        </a>

        {{-- Center: Search trigger (desktop) --}}
        <button @click="searchOpen = true"
                class="hidden md:flex items-center gap-3 px-4 py-2 rounded-xl
                       bg-gray-100 dark:bg-white/5 border border-gray-200 dark:border-white/10
                       text-gray-400 dark:text-gray-500 text-sm hover:border-peri/40
                       transition-all duration-200 cursor-pointer min-w-[260px] lg:min-w-[320px]">
            <i class="fas fa-search text-xs"></i>
            <span class="flex-1 text-left">Cari produk...</span>
            <kbd class="hidden lg:inline-flex items-center gap-1 px-2 py-0.5 rounded-md
                        bg-gray-200/70 dark:bg-white/10 text-[11px] font-semibold
                        text-gray-500 dark:text-gray-400">Ctrl+K</kbd>
        </button>

        {{-- Right actions --}}
        <div class="flex items-center gap-1.5 sm:gap-2">

            {{-- Mobile search icon --}}
            <button @click="searchOpen = true"
                    class="md:hidden w-10 h-10 rounded-full flex items-center justify-center
                           bg-gray-100 dark:bg-white/5 text-gray-600 dark:text-gray-300
                           hover:bg-peri hover:text-white transition-all duration-200">
                <i class="fas fa-search text-sm"></i>
            </button>

            {{-- Dark/Light toggle --}}
            <button @click="toggleTheme()"
                    class="w-10 h-10 rounded-full flex items-center justify-center
                           bg-gray-100 dark:bg-white/5 text-gray-600 dark:text-gray-300
                           hover:bg-peri hover:text-white transition-all duration-200"
                    title="Ganti tema">
                {{-- Sun icon (shown in dark mode) --}}
                <svg x-show="darkMode" x-cloak class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m8.66-13.66l-.71.71M4.05 19.95l-.71.71M21 12h-1M4 12H3m16.66 7.66l-.71-.71M4.05 4.05l-.71-.71M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
                {{-- Moon icon (shown in light mode) --}}
                <svg x-show="!darkMode" x-cloak class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                </svg>
            </button>

            {{-- Cart --}}
            {{-- Wishlist --}}
            @auth
                {{-- Notifications --}}
                <a href="{{ route('notifications.index') }}"
                   class="relative w-10 h-10 rounded-full flex items-center justify-center
                          bg-gray-100 dark:bg-white/5 text-gray-600 dark:text-gray-300
                          hover:bg-peri hover:text-white transition-all duration-200"
                   title="Notifikasi">
                    <i class="fas fa-bell text-sm"></i>
                    @php $unreadNotifs = \App\Models\Notification::where('user_id', auth()->id())->whereNull('read_at')->count(); @endphp
                    @if($unreadNotifs > 0)
                        <span class="absolute -top-1 -right-1 min-w-[20px] h-5 rounded-full
                                     bg-red-500 text-white text-[11px] font-bold
                                     flex items-center justify-center px-1
                                     border-2 border-white dark:border-peri-darkest">{{ $unreadNotifs > 9 ? '9+' : $unreadNotifs }}</span>
                    @endif
                </a>

                <a href="{{ route('wishlist.index') }}"
                   class="relative w-10 h-10 rounded-full flex items-center justify-center
                          bg-gray-100 dark:bg-white/5 text-gray-600 dark:text-gray-300
                          hover:bg-pink-500 hover:text-white transition-all duration-200"
                   title="Wishlist">
                    <i class="fas fa-heart text-sm"></i>
                    @php $wishCount = auth()->user()->wishlists()->count(); @endphp
                    @if($wishCount > 0)
                        <span class="absolute -top-1 -right-1 min-w-[20px] h-5 rounded-full
                                     bg-pink-500 text-white text-[11px] font-bold
                                     flex items-center justify-center px-1
                                     border-2 border-white dark:border-peri-darkest">{{ $wishCount }}</span>
                    @endif
                </a>
            @endauth

            <button @click="cartOpen = true"
                    class="relative w-10 h-10 rounded-full flex items-center justify-center
                           bg-gray-100 dark:bg-white/5 text-gray-600 dark:text-gray-300
                           hover:bg-peri hover:text-white transition-all duration-200"
                    id="cartToggle" title="Keranjang">
                <i class="fas fa-shopping-cart text-sm"></i>
                <span class="absolute -top-1 -right-1 min-w-[20px] h-5 rounded-full
                             bg-pink-500 text-white text-[11px] font-bold
                             flex items-center justify-center px-1
                             border-2 border-white dark:border-peri-darkest
                             transition-all duration-200"
                      :class="cartCount === 0 ? 'scale-0 opacity-0' : 'scale-100 opacity-100'"
                      id="cartBadge"
                      x-text="cartCount">{{ session('cart_count', 0) }}</span>
            </button>

            {{-- Auth --}}
            @guest
                <a href="{{ route('login') }}"
                   class="hidden sm:inline-flex items-center gap-2 px-4 py-2 rounded-xl
                          bg-gradient-to-r from-peri to-peri-dark text-white text-sm font-semibold
                          shadow-lg shadow-peri/25 hover:shadow-peri/40 hover:-translate-y-0.5
                          transition-all duration-200">
                    <i class="fas fa-sign-in-alt text-xs"></i> Masuk
                </a>
                <a href="{{ route('login') }}"
                   class="sm:hidden w-10 h-10 rounded-full flex items-center justify-center
                          bg-peri text-white hover:bg-peri-dark transition-all duration-200">
                    <i class="fas fa-sign-in-alt text-sm"></i>
                </a>
            @else
                <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                    <button @click="open = !open"
                            class="w-10 h-10 rounded-full flex items-center justify-center
                                   bg-gradient-to-br from-peri to-peri-dark text-white text-sm font-bold
                                   hover:shadow-lg hover:shadow-peri/30 transition-all duration-200"
                            title="{{ auth()->user()->name }}">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </button>

                    {{-- Dropdown --}}
                    <div x-show="open" x-cloak
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
                         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 scale-100"
                         x-transition:leave-end="opacity-0 scale-95"
                         class="absolute right-0 top-full mt-2 w-56 rounded-2xl overflow-hidden
                                bg-white dark:bg-gray-800 border border-gray-200 dark:border-white/10
                                shadow-xl shadow-black/10 dark:shadow-black/40 z-50">
                        {{-- User info --}}
                        <div class="px-4 py-3 border-b border-gray-100 dark:border-white/5">
                            <p class="text-sm font-bold text-gray-900 dark:text-white truncate">{{ auth()->user()->name }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 truncate">{{ auth()->user()->email }}</p>
                        </div>
                        <div class="p-1.5">
                            <a href="{{ route('dashboard') }}"
                               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm text-gray-600 dark:text-gray-300
                                      hover:bg-gray-100 dark:hover:bg-white/5 transition-colors duration-150">
                                <i class="fas fa-th-large w-4 text-peri text-xs"></i> Dashboard Saya
                            </a>
                            <a href="{{ route('profile.edit') }}"
                               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm text-gray-600 dark:text-gray-300
                                      hover:bg-gray-100 dark:hover:bg-white/5 transition-colors duration-150">
                                <i class="fas fa-user-edit w-4 text-peri text-xs"></i> Edit Profil
                            </a>
                            <a href="{{ route('order.history') }}"
                               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm text-gray-600 dark:text-gray-300
                                      hover:bg-gray-100 dark:hover:bg-white/5 transition-colors duration-150">
                                <i class="fas fa-box w-4 text-peri text-xs"></i> Pesanan Saya
                            </a>
                            @if(auth()->user()->isAdmin())
                            <a href="{{ route('admin.dashboard') }}"
                               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm text-gray-600 dark:text-gray-300
                                      hover:bg-gray-100 dark:hover:bg-white/5 transition-colors duration-150">
                                <i class="fas fa-shield-alt w-4 text-peri text-xs"></i> Panel Admin
                            </a>
                            @endif
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                        class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm
                                               text-red-500 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-500/10
                                               transition-colors duration-150">
                                    <i class="fas fa-sign-out-alt w-4 text-xs"></i> Keluar
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endguest

            {{-- Mobile hamburger --}}
            <button @click="mobileMenuOpen = true"
                    class="lg:hidden w-10 h-10 rounded-full flex items-center justify-center
                           bg-gray-100 dark:bg-white/5 text-gray-600 dark:text-gray-300
                           hover:bg-peri hover:text-white transition-all duration-200">
                <i class="fas fa-bars text-sm"></i>
            </button>
        </div>
    </div>
</header>

{{-- ─── SEARCH MODAL (Ctrl+K) ─────────────────────────────────────────── --}}
<div x-show="searchOpen" x-cloak
     class="fixed inset-0 z-[100] flex items-start justify-center pt-[10vh] sm:pt-[15vh] px-4"
     @keydown.escape.window="searchOpen && (searchOpen = false)">
    {{-- Backdrop --}}
    <div class="absolute inset-0 bg-black/40 dark:bg-black/60 backdrop-blur-sm" @click="searchOpen = false"
         x-show="searchOpen"
         x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"></div>

    {{-- Modal --}}
    <div class="relative w-full max-w-xl bg-white dark:bg-gray-800 rounded-2xl shadow-2xl shadow-black/20
                border border-gray-200 dark:border-white/10 overflow-hidden"
         x-show="searchOpen"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95 -translate-y-4"
         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         @click.outside="searchOpen = false">

        {{-- Search input --}}
        <div class="flex items-center gap-3 px-5 border-b border-gray-200 dark:border-white/10">
            <i class="fas fa-search text-gray-400 dark:text-gray-500"></i>
            <input type="text" id="searchInput"
                   x-ref="searchInput"
                   @input.debounce.400ms="doSearch($event.target.value)"
                   x-init="$watch('searchOpen', v => { if(v) $nextTick(() => $refs.searchInput.focus()); else { $refs.searchInput.value = ''; searchResultsHtml = ''; } })"
                   placeholder="Cari produk, kategori, atau jasa digital..."
                   autocomplete="off"
                   class="flex-1 py-4 bg-transparent border-none outline-none text-gray-900 dark:text-white
                          placeholder-gray-400 dark:placeholder-gray-500 text-sm focus:ring-0" />
            <kbd class="px-2 py-1 rounded-md bg-gray-100 dark:bg-white/10 text-[11px] font-semibold
                        text-gray-400 dark:text-gray-500 cursor-pointer"
                 @click="searchOpen = false">ESC</kbd>
        </div>

        {{-- Results --}}
        <div class="max-h-[50vh] overflow-y-auto" id="searchResults" x-html="searchResultsHtml"></div>
    </div>
</div>

{{-- ─── MOBILE MENU (slide from left) ─────────────────────────────────── --}}
<template x-teleport="body">
    <div x-show="mobileMenuOpen" x-cloak class="fixed inset-0 z-[90]">
        {{-- Backdrop --}}
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" @click="mobileMenuOpen = false"
             x-show="mobileMenuOpen"
             x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"></div>
        {{-- Panel --}}
        <aside class="absolute top-0 left-0 bottom-0 w-72 max-w-[80vw]
                      bg-white dark:bg-gray-800 shadow-2xl shadow-black/30
                      border-r border-gray-200 dark:border-white/10
                      flex flex-col overflow-y-auto"
               x-show="mobileMenuOpen"
               x-transition:enter="transition ease-out duration-300"
               x-transition:enter-start="-translate-x-full"
               x-transition:enter-end="translate-x-0"
               x-transition:leave="transition ease-in duration-200"
               x-transition:leave-start="translate-x-0"
               x-transition:leave-end="-translate-x-full">

            {{-- Header --}}
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100 dark:border-white/5">
                <a href="{{ route('home') }}" class="flex items-center gap-2">
                    @if(setting('store_logo'))
                        <img src="{{ asset('storage/' . setting('store_logo')) }}" alt="{{ setting('store_name', 'Shuriza Store') }}" class="w-8 h-8 rounded-lg object-contain">
                    @else
                        <span class="w-8 h-8 rounded-lg bg-gradient-to-br from-peri-light to-peri-dark flex items-center justify-center text-white text-xs">
                            <i class="fas fa-store"></i>
                        </span>
                    @endif
                    <span class="font-bold text-lg bg-gradient-to-br from-peri-light to-peri-dark bg-clip-text text-transparent font-poppins">
                        {{ setting('store_name', 'Shuriza Store') }}
                    </span>
                </a>
                <button @click="mobileMenuOpen = false"
                        class="w-9 h-9 rounded-full flex items-center justify-center
                               bg-gray-100 dark:bg-white/5 text-gray-500 dark:text-gray-400
                               hover:bg-gray-200 dark:hover:bg-white/10 transition-colors">
                    <i class="fas fa-times text-sm"></i>
                </button>
            </div>

            {{-- Links --}}
            <nav class="flex-1 p-3 space-y-0.5">
                <a href="{{ route('home') }}"
                   class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-colors
                          {{ request()->routeIs('home') ? 'bg-peri/10 text-peri dark:text-peri-light' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-white/5' }}">
                    <i class="fas fa-home w-5 text-center text-peri"></i> Beranda
                </a>
                <a href="{{ route('products.index') }}"
                   class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-colors
                          {{ request()->routeIs('products.*') ? 'bg-peri/10 text-peri dark:text-peri-light' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-white/5' }}">
                    <i class="fas fa-box w-5 text-center text-peri"></i> Produk
                </a>
                <a href="{{ route('articles.index') }}"
                   class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-colors
                          {{ request()->routeIs('articles.*') ? 'bg-peri/10 text-peri dark:text-peri-light' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-white/5' }}">
                    <i class="fas fa-newspaper w-5 text-center text-peri"></i> Artikel
                </a>
                <a href="{{ route('cart.index') }}"
                   class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-white/5 transition-colors">
                    <i class="fas fa-shopping-cart w-5 text-center text-peri"></i> Keranjang
                </a>
                @auth
                    <a href="{{ route('dashboard') }}"
                       class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-white/5 transition-colors">
                        <i class="fas fa-th-large w-5 text-center text-peri"></i> Dashboard
                    </a>
                    <a href="{{ route('profile.edit') }}"
                       class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-white/5 transition-colors">
                        <i class="fas fa-user-edit w-5 text-center text-peri"></i> Edit Profil
                    </a>
                    <a href="{{ route('order.history') }}"
                       class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-white/5 transition-colors">
                        <i class="fas fa-receipt w-5 text-center text-peri"></i> Pesanan Saya
                    </a>
                    @if(auth()->user()->isAdmin())
                    <a href="{{ route('admin.dashboard') }}"
                       class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-white/5 transition-colors">
                        <i class="fas fa-shield-alt w-5 text-center text-peri"></i> Panel Admin
                    </a>
                    @endif
                @else
                    <a href="{{ route('login') }}"
                       class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-white/5 transition-colors">
                        <i class="fas fa-sign-in-alt w-5 text-center text-peri"></i> Masuk
                    </a>
                    <a href="{{ route('register') }}"
                       class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-white/5 transition-colors">
                        <i class="fas fa-user-plus w-5 text-center text-peri"></i> Daftar
                    </a>
                @endauth
            </nav>

            {{-- Footer --}}
            @auth
            <div class="p-3 border-t border-gray-100 dark:border-white/5">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                            class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium
                                   text-red-500 hover:bg-red-50 dark:hover:bg-red-500/10 transition-colors">
                        <i class="fas fa-sign-out-alt w-5 text-center"></i> Keluar
                    </button>
                </form>
            </div>
            @endauth
        </aside>
    </div>
</template>

{{-- ─── CART SIDEBAR (slide from right) ───────────────────────────────── --}}
<template x-teleport="body">
    <div x-show="cartOpen" x-cloak class="fixed inset-0 z-[80]">
        {{-- Backdrop --}}
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" @click="cartOpen = false"
             x-show="cartOpen"
             x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
             id="cartOverlay"></div>
        {{-- Panel --}}
        <aside class="absolute top-0 right-0 bottom-0 w-[400px] max-w-full
                      bg-white dark:bg-gray-800 shadow-2xl shadow-black/30
                      border-l border-gray-200 dark:border-white/10
                      flex flex-col"
               x-show="cartOpen"
               x-transition:enter="transition ease-out duration-300"
               x-transition:enter-start="translate-x-full"
               x-transition:enter-end="translate-x-0"
               x-transition:leave="transition ease-in duration-200"
               x-transition:leave-start="translate-x-0"
               x-transition:leave-end="translate-x-full"
               id="cartSidebar"
               x-init="$watch('cartOpen', v => { if(v) loadCart(); })">

            {{-- Header --}}
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100 dark:border-white/5">
                <h3 class="flex items-center gap-2 text-base font-bold text-gray-900 dark:text-white">
                    <i class="fas fa-shopping-cart text-peri"></i> Keranjang Belanja
                </h3>
                <button @click="cartOpen = false"
                        class="w-9 h-9 rounded-full flex items-center justify-center
                               bg-gray-100 dark:bg-white/5 text-gray-500 dark:text-gray-400
                               hover:bg-gray-200 dark:hover:bg-white/10 transition-colors"
                        id="cartClose">
                    <i class="fas fa-times text-sm"></i>
                </button>
            </div>

            {{-- Body --}}
            <div class="flex-1 overflow-y-auto" id="cartBody">
                <div class="hidden flex-col items-center justify-center h-full p-8 text-center" id="cartEmpty">
                    <div class="w-20 h-20 rounded-full bg-gray-100 dark:bg-white/5 flex items-center justify-center mb-4">
                        <i class="fas fa-shopping-cart text-3xl text-gray-300 dark:text-gray-600"></i>
                    </div>
                    <p class="text-gray-500 dark:text-gray-400 mb-4 text-sm">Keranjang kamu masih kosong</p>
                    <a href="{{ route('products.index') }}"
                       class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl
                              bg-gradient-to-r from-peri to-peri-dark text-white text-sm font-semibold
                              shadow-lg shadow-peri/25 hover:shadow-peri/40 transition-all duration-200">
                        Mulai Belanja
                    </a>
                </div>
                <div class="px-5 py-3 divide-y divide-gray-100 dark:divide-white/5" id="cartItemsList"></div>
            </div>

            {{-- Footer --}}
            <div class="hidden border-t border-gray-100 dark:border-white/5 p-5 space-y-3" id="cartFooter">
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-500 dark:text-gray-400">Total Belanja:</span>
                    <strong class="text-lg font-extrabold text-gray-900 dark:text-white" id="cartTotalDisplay">Rp 0</strong>
                </div>
                <a href="{{ route('order.checkout') }}"
                   class="flex items-center justify-center gap-2 w-full px-5 py-3 rounded-xl
                          bg-gradient-to-r from-peri to-peri-dark text-white text-sm font-semibold
                          shadow-lg shadow-peri/25 hover:shadow-peri/40 hover:-translate-y-0.5
                          transition-all duration-200">
                    <i class="fas fa-credit-card text-xs"></i> Lanjut Checkout
                </a>
                <a href="{{ route('cart.index') }}"
                   class="flex items-center justify-center gap-2 w-full px-5 py-2.5 rounded-xl
                          bg-gray-100 dark:bg-white/5 text-gray-700 dark:text-gray-300 text-sm font-medium
                          border border-gray-200 dark:border-white/10
                          hover:bg-gray-200 dark:hover:bg-white/10 transition-all duration-200">
                    <i class="fas fa-shopping-cart text-xs"></i> Lihat Keranjang
                </a>
            </div>
        </aside>
    </div>
</template>

{{-- ─── MAIN CONTENT ──────────────────────────────────────────────────── --}}
<main class="pt-16 lg:pt-[70px] min-h-screen">
    @isset($header)
    <div class="bg-white dark:bg-gray-800 shadow-sm border-b border-gray-200 dark:border-gray-700">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            {{ $header }}
        </div>
    </div>
    @endisset
    @yield('content')
    {{ $slot ?? '' }}
</main>

{{-- ─── FOOTER ────────────────────────────────────────────────────────── --}}
<footer class="bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-white/5 mt-auto" id="kontak">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 pt-12 lg:pt-16 pb-0">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-8 lg:gap-6">

            {{-- Brand --}}
            <div class="lg:col-span-2">
                <a href="{{ route('home') }}" class="inline-flex items-center gap-2.5 mb-4">
                    @if(setting('store_logo'))
                        <img src="{{ asset('storage/' . setting('store_logo')) }}" alt="{{ setting('store_name', 'Shuriza Store') }}" class="w-9 h-9 rounded-xl object-contain">
                    @else
                        <span class="w-9 h-9 rounded-xl bg-gradient-to-br from-peri-light to-peri-dark flex items-center justify-center text-white text-sm">
                            <i class="fas fa-store"></i>
                        </span>
                    @endif
                    <span class="bg-gradient-to-br from-peri-light to-peri-dark bg-clip-text text-transparent font-bold text-xl font-poppins">
                        {{ setting('store_name', 'Shuriza Store') }}
                    </span>
                </a>
                <p class="text-sm text-gray-500 dark:text-gray-400 leading-relaxed max-w-sm mb-5">
                    {{ setting('store_description', 'Toko digital terpercaya. Menyediakan berbagai produk dan jasa digital berkualitas dengan harga terjangkau dan proses cepat.') }}
                </p>
                <div class="flex gap-2">
                    <a href="https://wa.me/{{ setting('whatsapp_number') }}" target="_blank"
                       class="w-10 h-10 rounded-xl bg-gray-100 dark:bg-white/5 flex items-center justify-center
                              text-gray-500 dark:text-gray-400 hover:bg-green-500 hover:text-white transition-all duration-200"
                       title="WhatsApp">
                        <i class="fab fa-whatsapp"></i>
                    </a>
                    <a href="https://instagram.com/{{ setting('instagram_handle') }}" target="_blank"
                       class="w-10 h-10 rounded-xl bg-gray-100 dark:bg-white/5 flex items-center justify-center
                              text-gray-500 dark:text-gray-400 hover:bg-gradient-to-br hover:from-purple-500 hover:to-pink-500 hover:text-white transition-all duration-200"
                       title="Instagram">
                        <i class="fab fa-instagram"></i>
                    </a>
                    <a href="https://t.me/{{ setting('telegram_handle') }}" target="_blank"
                       class="w-10 h-10 rounded-xl bg-gray-100 dark:bg-white/5 flex items-center justify-center
                              text-gray-500 dark:text-gray-400 hover:bg-blue-500 hover:text-white transition-all duration-200"
                       title="Telegram">
                        <i class="fab fa-telegram"></i>
                    </a>
                </div>
            </div>

            {{-- Kategori --}}
            <div>
                <h4 class="text-sm font-bold text-gray-900 dark:text-white mb-4">Kategori Produk</h4>
                <ul class="space-y-2.5">
                    <li><a href="{{ route('products.index', ['category'=>'streaming']) }}" class="text-sm text-gray-500 dark:text-gray-400 hover:text-peri dark:hover:text-peri-light transition-colors flex items-center gap-2"><i class="fas fa-play-circle w-4 text-xs text-peri"></i>Streaming</a></li>
                    <li><a href="{{ route('products.index', ['category'=>'desain']) }}" class="text-sm text-gray-500 dark:text-gray-400 hover:text-peri dark:hover:text-peri-light transition-colors flex items-center gap-2"><i class="fas fa-palette w-4 text-xs text-peri"></i>Desain</a></li>
                    <li><a href="{{ route('products.index', ['category'=>'produktivitas']) }}" class="text-sm text-gray-500 dark:text-gray-400 hover:text-peri dark:hover:text-peri-light transition-colors flex items-center gap-2"><i class="fas fa-briefcase w-4 text-xs text-peri"></i>Produktivitas</a></li>
                    <li><a href="{{ route('products.index', ['category'=>'gaming']) }}" class="text-sm text-gray-500 dark:text-gray-400 hover:text-peri dark:hover:text-peri-light transition-colors flex items-center gap-2"><i class="fas fa-gamepad w-4 text-xs text-peri"></i>Gaming</a></li>
                    <li><a href="{{ route('products.index', ['category'=>'jasa']) }}" class="text-sm text-gray-500 dark:text-gray-400 hover:text-peri dark:hover:text-peri-light transition-colors flex items-center gap-2"><i class="fas fa-hands-helping w-4 text-xs text-peri"></i>Jasa Digital</a></li>
                    <li><a href="{{ route('products.index', ['category'=>'edukasi']) }}" class="text-sm text-gray-500 dark:text-gray-400 hover:text-peri dark:hover:text-peri-light transition-colors flex items-center gap-2"><i class="fas fa-graduation-cap w-4 text-xs text-peri"></i>Edukasi</a></li>
                </ul>
            </div>

            {{-- Informasi --}}
            <div>
                <h4 class="text-sm font-bold text-gray-900 dark:text-white mb-4">Informasi</h4>
                <ul class="space-y-2.5">
                    <li><a href="{{ route('pages.how-to-buy') }}" class="text-sm text-gray-500 dark:text-gray-400 hover:text-peri dark:hover:text-peri-light transition-colors flex items-center gap-2"><i class="fas fa-info-circle w-4 text-xs text-peri"></i>Cara Pembelian</a></li>
                    <li><a href="{{ route('pages.faq') }}" class="text-sm text-gray-500 dark:text-gray-400 hover:text-peri dark:hover:text-peri-light transition-colors flex items-center gap-2"><i class="fas fa-question-circle w-4 text-xs text-peri"></i>FAQ</a></li>
                    <li><a href="{{ route('pages.about') }}" class="text-sm text-gray-500 dark:text-gray-400 hover:text-peri dark:hover:text-peri-light transition-colors flex items-center gap-2"><i class="fas fa-store w-4 text-xs text-peri"></i>Tentang Kami</a></li>
                    <li><a href="{{ route('articles.index') }}" class="text-sm text-gray-500 dark:text-gray-400 hover:text-peri dark:hover:text-peri-light transition-colors flex items-center gap-2"><i class="fas fa-newspaper w-4 text-xs text-peri"></i>Artikel</a></li>
                    <li><a href="{{ route('pages.privacy') }}" class="text-sm text-gray-500 dark:text-gray-400 hover:text-peri dark:hover:text-peri-light transition-colors flex items-center gap-2"><i class="fas fa-shield-alt w-4 text-xs text-peri"></i>Kebijakan Privasi</a></li>
                    <li><a href="{{ route('pages.terms') }}" class="text-sm text-gray-500 dark:text-gray-400 hover:text-peri dark:hover:text-peri-light transition-colors flex items-center gap-2"><i class="fas fa-file-contract w-4 text-xs text-peri"></i>Syarat & Ketentuan</a></li>
                    <li><a href="{{ route('pages.terms') }}#kebijakan-refund" class="text-sm text-gray-500 dark:text-gray-400 hover:text-peri dark:hover:text-peri-light transition-colors flex items-center gap-2"><i class="fas fa-undo w-4 text-xs text-peri"></i>Kebijakan Refund</a></li>
                    @guest
                    <li><a href="{{ route('register') }}" class="text-sm text-gray-500 dark:text-gray-400 hover:text-peri dark:hover:text-peri-light transition-colors flex items-center gap-2"><i class="fas fa-user-plus w-4 text-xs text-peri"></i>Daftar Member</a></li>
                    @endguest
                </ul>
            </div>

            {{-- Lainnya (blank / contact) --}}
            <div>
                <h4 class="text-sm font-bold text-gray-900 dark:text-white mb-4">Hubungi Kami</h4>
                <div class="space-y-3 text-sm text-gray-500 dark:text-gray-400">
                    <div class="flex items-start gap-2">
                        <i class="fas fa-map-marker-alt w-4 text-peri mt-0.5 text-xs"></i>
                        <span>{{ setting('store_address', 'Kediri, Jawa Timur') }}</span>
                    </div>
                    <div class="flex items-start gap-2">
                        <i class="fab fa-whatsapp w-4 text-peri mt-0.5 text-xs"></i>
                        <a href="https://wa.me/{{ setting('whatsapp_number') }}" class="hover:text-peri transition-colors">{{ format_phone_display(setting('whatsapp_number')) }}</a>
                    </div>
                    <div class="flex items-start gap-2">
                        <i class="fas fa-envelope w-4 text-peri mt-0.5 text-xs"></i>
                        <a href="mailto:{{ setting('store_email', 'admin@shurizastore.com') }}" class="hover:text-peri transition-colors">{{ setting('store_email', 'admin@shurizastore.com') }}</a>
                    </div>
                    <div class="flex items-start gap-2">
                        <i class="fas fa-clock w-4 text-peri mt-0.5 text-xs"></i>
                        <span>Senin – Minggu, 08.00 – 22.00 WIB</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Bottom bar --}}
    <div class="border-t border-gray-200 dark:border-white/5 mt-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 py-5 flex flex-col sm:flex-row items-center justify-between gap-3">
            <p class="text-sm text-gray-500 dark:text-gray-400">
                &copy; {{ date('Y') }} {{ setting('store_name', 'Shuriza Store Kediri') }}. Made with <span class="text-red-500">&hearts;</span> {{ setting('store_name', 'Shuriza Store Kediri') }}
            </p>
            <p class="text-xs text-gray-400 dark:text-gray-500">{{ setting('store_tagline', 'Penyedia layanan digital terpercaya') }}</p>
        </div>
    </div>
</footer>

{{-- ─── FLOATING WhatsApp ─────────────────────────────────────────────── --}}
<a href="https://wa.me/{{ setting('whatsapp_number') }}" target="_blank"
   class="fixed bottom-24 right-5 z-50 w-14 h-14 rounded-full bg-green-500 text-white
          flex items-center justify-center text-2xl shadow-lg shadow-green-500/30
          hover:scale-110 hover:shadow-green-500/50 transition-all duration-300 group"
   style="animation: waPulse 2.5s ease infinite;"
   title="Chat via WhatsApp">
    <i class="fab fa-whatsapp"></i>
    <span class="absolute right-16 px-3 py-1.5 rounded-lg bg-gray-900 dark:bg-gray-700 text-white text-xs font-medium
                 whitespace-nowrap opacity-0 group-hover:opacity-100 pointer-events-none transition-opacity duration-200
                 shadow-lg">
        Chat Admin
    </span>
</a>

{{-- ─── SCROLL TO TOP ─────────────────────────────────────────────────── --}}
<button @click="window.scrollTo({ top: 0, behavior: 'smooth' })"
        class="fixed bottom-6 left-5 z-50 w-11 h-11 rounded-full
               bg-peri/20 dark:bg-peri/20 text-peri border border-peri/30
               flex items-center justify-center cursor-pointer
               hover:bg-peri hover:text-white hover:-translate-y-1
               transition-all duration-300"
        :class="scrolled ? 'opacity-100 pointer-events-auto' : 'opacity-0 pointer-events-none'"
        title="Kembali ke atas"
        id="scrollTop">
    <i class="fas fa-chevron-up text-sm"></i>
</button>

{{-- ─── TOAST CONTAINER ──────────────────────────────────────────────── --}}
<div class="fixed top-20 left-1/2 -translate-x-1/2 z-[200] flex flex-col items-center gap-2 pointer-events-none w-full max-w-sm px-4"
     id="toastContainer"></div>

{{-- ─── SCRIPTS ──────────────────────────────────────────────────────── --}}
<script>
const CSRF_TOKEN = '{{ csrf_token() }}';
const ROUTES = {
    cartAdd:   '{{ route("cart.add") }}',
    cartCount: '{{ route("cart.count") }}',
    search:    '{{ route("search") }}',
};

// ── Alpine.js layout controller ───────────────────────────────────────────────
function layoutApp() {
    return {
        scrolled: false,
        darkMode: document.documentElement.classList.contains('dark'),
        searchOpen: false,
        searchResultsHtml: '',
        mobileMenuOpen: false,
        cartOpen: false,
        cartCount: {{ session('cart_count', 0) }},

        init() {
            // Scroll listener
            window.addEventListener('scroll', () => {
                this.scrolled = window.scrollY > 50;
            });
            this.scrolled = window.scrollY > 50;

            // Init cart badge from server
            this.refreshCartBadge();

            // Flash toasts
            @if(session('success'))
                this.$nextTick(() => showToast(@json(session('success')), 'success'));
            @endif
            @if(session('error'))
                this.$nextTick(() => showToast(@json(session('error')), 'error'));
            @endif
        },

        toggleTheme() {
            this.darkMode = !this.darkMode;
            document.documentElement.classList.toggle('dark', this.darkMode);
            localStorage.setItem('theme', this.darkMode ? 'dark' : 'light');
        },

        handleGlobalKey(e) {
            if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
                e.preventDefault();
                this.searchOpen = !this.searchOpen;
            }
        },

        async doSearch(q) {
            q = q.trim();
            if (q.length < 2) { this.searchResultsHtml = ''; return; }

            this.searchResultsHtml = '<div class="px-5 py-6 text-center text-sm text-gray-400"><i class="fas fa-spinner fa-spin mr-1"></i> Mencari...</div>';

            try {
                const res  = await fetch(`${ROUTES.search}?q=${encodeURIComponent(q)}`);
                const data = await res.json();

                if (!data.results || data.results.length === 0) {
                    this.searchResultsHtml = `<div class="px-5 py-8 text-center"><i class="fas fa-search text-2xl text-gray-300 dark:text-gray-600 mb-2 block"></i><p class="text-sm text-gray-500 dark:text-gray-400">Tidak ada hasil untuk "<b>${q}</b>"</p></div>`;
                    return;
                }

                this.searchResultsHtml = data.results.map(p => `
                    <a href="${p.url}" class="flex items-center gap-3 px-5 py-3 hover:bg-gray-50 dark:hover:bg-white/5 transition-colors border-b border-gray-100 dark:border-white/5 last:border-0">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center text-sm shrink-0" style="background:${p.color}18;color:${p.color};">
                            <i class="${p.icon}"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="text-sm font-semibold text-gray-900 dark:text-white truncate">${p.name}</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">${p.category ?? ''} ${p.badge ? '· <b>'+p.badge+'</b>' : ''} ${!p.is_in_stock ? '· <span class="text-red-500">Stok Habis</span>' : ''}</div>
                        </div>
                        <span class="text-sm font-bold text-peri dark:text-peri-light whitespace-nowrap">${p.price}</span>
                    </a>
                `).join('');
            } catch {
                this.searchResultsHtml = '<div class="px-5 py-6 text-center text-sm text-gray-500">Terjadi kesalahan. Coba lagi.</div>';
            }
        },

        async refreshCartBadge() {
            try {
                const res  = await fetch(ROUTES.cartCount);
                const data = await res.json();
                this.cartCount = data.count;
            } catch(e) {}
        },
    };
}

// ── Cart Sidebar Logic ────────────────────────────────────────────────────────
// Elements are inside x-teleport template — look them up lazily at call time
function cartEls() {
    return {
        empty:  document.getElementById('cartEmpty'),
        list:   document.getElementById('cartItemsList'),
        footer: document.getElementById('cartFooter'),
        total:  document.getElementById('cartTotalDisplay'),
        badge:  document.getElementById('cartBadge'),
    };
}

async function loadCart() {
    try {
        const res  = await fetch('{{ route("cart.items") }}', {
            credentials: 'same-origin',
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
        });
        const data = await res.json();
        updateCartBadge(data.count);
        const { total } = cartEls();
        if (total) total.textContent = data.total;
        renderCartItems(data.items);
    } catch(e) {
        console.error('Cart load error', e);
    }
}

async function renderCartFromPage() {
    return loadCart();
}

function renderCartItems(items) {
    const { empty, list: cartItemsList, footer: cartFooter } = cartEls();
    if (!items || items.length === 0) {
        if (empty) { empty.classList.remove('hidden'); empty.classList.add('flex'); }
        if (cartItemsList) cartItemsList.innerHTML = '';
        if (cartFooter) cartFooter.classList.add('hidden');
    } else {
        if (empty) { empty.classList.add('hidden'); empty.classList.remove('flex'); }
        if (cartFooter) { cartFooter.classList.remove('hidden'); cartFooter.classList.add('block'); }
        if (cartItemsList) {
            cartItemsList.innerHTML = items.map(item => {
                const p = item.product;
                if (!p) return '';
                const img = p.image_url
                    ? `<img src="${p.image_url}" alt="${p.name}" class="w-14 h-14 rounded-xl object-cover">`
                    : `<div class="w-14 h-14 rounded-xl bg-gray-100 dark:bg-gray-700 flex items-center justify-center text-gray-400"><i class="fas fa-box"></i></div>`;
                const subtotalFmt = 'Rp ' + item.subtotal.toLocaleString('id-ID');
                const stockBadge  = p.stock === 0
                    ? `<span class="text-xs text-red-500"><i class="fas fa-times-circle mr-1"></i>Stok habis</span>`
                    : (p.stock <= 5 ? `<span class="text-xs text-amber-500"><i class="fas fa-exclamation-triangle mr-1"></i>Sisa ${p.stock}</span>` : '');
                return `
                <div data-cart-item="${item.id}" class="py-3 flex gap-3 items-start">
                    <a href="/produk/${p.slug}">${img}</a>
                    <div class="flex-1 min-w-0">
                        <a href="/produk/${p.slug}" class="text-sm font-semibold text-gray-900 dark:text-white truncate block hover:text-peri transition">${p.name}</a>
                        <p class="text-xs text-gray-500 dark:text-gray-400">${p.formatted_price} / item</p>
                        ${stockBadge}
                        <div class="mt-2 flex items-center justify-between gap-3">
                            <div class="flex items-center gap-1">
                                <button data-qty-btn="minus" data-item-id="${item.id}"
                                    class="w-7 h-7 rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 flex items-center justify-center text-xs hover:bg-peri hover:text-white transition">
                                    <i class="fas fa-minus"></i>
                                </button>
                                <span data-qty-val="${item.id}" class="w-8 text-center text-sm font-semibold text-gray-900 dark:text-white">${item.quantity}</span>
                                <button data-qty-btn="plus" data-item-id="${item.id}"
                                    class="w-7 h-7 rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 flex items-center justify-center text-xs hover:bg-peri hover:text-white transition">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                            <span class="text-sm font-bold text-gray-900 dark:text-white">${subtotalFmt}</span>
                            <button data-remove-item="${item.id}" class="w-7 h-7 rounded-lg text-gray-400 hover:bg-red-50 hover:text-red-500 dark:hover:bg-red-500/10 flex items-center justify-center transition">
                                <i class="fas fa-trash-alt text-xs"></i>
                            </button>
                        </div>
                    </div>
                </div>`;
            }).join('');
            bindCartItemEvents();
        }
    }
}

function bindCartItemEvents() {
    const { list: cartItemsList } = cartEls();
    if (!cartItemsList) return;
    cartItemsList.querySelectorAll('[data-qty-btn]').forEach(btn => {
        btn.addEventListener('click', async () => {
            const itemId = btn.dataset.itemId;
            const action = btn.dataset.qtyBtn;
            const qtyEl  = document.querySelector(`[data-qty-val="${itemId}"]`);
            let qty = parseInt(qtyEl?.textContent ?? 1);
            qty = action === 'plus' ? qty + 1 : Math.max(1, qty - 1);
            await updateCartItem(itemId, qty);
        });
    });
    cartItemsList.querySelectorAll('[data-remove-item]').forEach(btn => {
        btn.addEventListener('click', async () => {
            await removeCartItem(btn.dataset.removeItem);
        });
    });
}

async function updateCartItem(id, qty) {
    try {
        const res  = await fetch(`/cart/${id}`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': CSRF_TOKEN,
                'Accept': 'application/json',
            },
            body: JSON.stringify({ quantity: qty }),
        });
        const data = await res.json();
        if (data.success) {
            updateCartBadge(data.cart_count);
            const { total } = cartEls();
            if (total) total.textContent = data.cart_total;
            await loadCart();
            showToast('Keranjang diperbarui.', 'success');
        }
    } catch(e) {}
}

async function removeCartItem(id) {
    try {
        const res  = await fetch(`/cart/${id}`, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' },
        });
        const data = await res.json();
        if (data.success) {
            updateCartBadge(data.cart_count);
            const { total, list: cartItemsList, empty: cartEmpty, footer: cartFooter } = cartEls();
            if (total) total.textContent = data.cart_total;
            const item = cartItemsList?.querySelector(`[data-cart-item="${id}"]`);
            if (item) item.remove();
            if (data.cart_count === 0) {
                if (cartEmpty) { cartEmpty.classList.remove('hidden'); cartEmpty.classList.add('flex'); }
                if (cartFooter) cartFooter.classList.add('hidden');
            }
            showToast(data.message, 'success');
        }
    } catch(e) {}
}

window.addToCart = async function(productId, qty = 1) {
    try {
        const res  = await fetch(ROUTES.cartAdd, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': CSRF_TOKEN,
                'Accept': 'application/json',
            },
            body: JSON.stringify({ product_id: productId, quantity: qty }),
        });
        const data = await res.json();
        if (data.success) {
            updateCartBadge(data.cart_count);
            showToast(data.message, 'success');
        } else {
            showToast(data.message ?? 'Terjadi kesalahan.', 'error');
        }
    } catch(e) {
        showToast('Gagal menambahkan ke keranjang.', 'error');
    }
};

function updateCartBadge(count) {
    // Find the root Alpine component and update cartCount
    const root = document.querySelector('[x-data*="layoutApp"]') || document.querySelector('[x-data]');
    if (root && root._x_dataStack) {
        root._x_dataStack[0].cartCount = count;
    }
}

// ── Toast System ──────────────────────────────────────────────────────────────
window.showToast = function(message, type = 'success') {
    const container = document.getElementById('toastContainer');
    if (!container) return;
    const colors = {
        success: 'bg-green-500',
        error:   'bg-red-500',
        info:    'bg-peri',
    };
    const icons = {
        success: 'fa-check-circle',
        error:   'fa-exclamation-circle',
        info:    'fa-info-circle',
    };
    const toast = document.createElement('div');
    toast.className = `pointer-events-auto flex items-center gap-3 px-5 py-3 rounded-2xl
                       ${colors[type] ?? colors.info} text-white text-sm font-medium
                       shadow-xl shadow-black/15 toast-animate w-full`;
    toast.innerHTML = `<i class="fas ${icons[type] ?? icons.info}"></i> <span class="flex-1">${message}</span>`;
    container.appendChild(toast);
    setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transition = 'opacity .3s ease';
        setTimeout(() => toast.remove(), 300);
    }, 3500);
};
</script>

@stack('scripts')
</body>
</html>
