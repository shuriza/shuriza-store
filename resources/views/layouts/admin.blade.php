<!DOCTYPE html>
<html lang="id" class="dark scroll-smooth">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>@yield('title', 'Dashboard') – Admin {{ setting('store_name', 'Shuriza Store') }}</title>
    @if(setting('store_favicon'))
        <link rel="icon" href="{{ asset('storage/' . setting('store_favicon')) }}" type="image/png">
    @endif

    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet" />

    {{-- Font Awesome --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />

    {{-- Vite --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        /* Minimal scrollbar styling */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: rgba(255,255,255,.1); border-radius: 3px; }
        ::-webkit-scrollbar-thumb:hover { background: rgba(255,255,255,.2); }
    </style>

    @stack('styles')
</head>

<body class="min-h-screen bg-gray-950 font-sans text-gray-300 antialiased"
      x-data="{
          sidebarOpen: true,
          mobileMenu: false,
          userDropdown: false,
      }">

    {{-- Mobile sidebar backdrop --}}
    <div x-show="mobileMenu" x-transition.opacity @click="mobileMenu = false"
         class="fixed inset-0 z-40 bg-black/60 backdrop-blur-sm lg:hidden"></div>

    {{-- ═══════════ Sidebar ═══════════ --}}
    <aside :class="[
               mobileMenu ? 'translate-x-0' : '-translate-x-full',
               sidebarOpen ? 'lg:w-[260px]' : 'lg:w-[72px]'
           ]"
           class="fixed inset-y-0 left-0 z-50 flex w-[260px] flex-col border-r border-gray-800 bg-gray-900
                  transition-all duration-300 ease-in-out lg:translate-x-0">

        {{-- Logo --}}
        <div class="flex h-16 items-center gap-3 border-b border-gray-800 px-5">
            @if(setting('store_logo'))
                <img src="{{ asset('storage/' . setting('store_logo')) }}" alt="{{ setting('store_name', 'Shuriza Store') }}" class="h-9 w-9 flex-shrink-0 rounded-xl object-contain">
            @else
                <div class="flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-xl bg-peri text-white">
                    <i class="fas fa-store text-sm"></i>
                </div>
            @endif
            <div class="overflow-hidden whitespace-nowrap transition-all duration-300"
                 :class="sidebarOpen ? 'w-auto opacity-100' : 'lg:w-0 lg:opacity-0'">
                <span class="block text-sm font-extrabold text-white">{{ setting('store_name', 'Shuriza Store') }}</span>
                <span class="block text-[0.65rem] font-medium tracking-wide text-peri-light">ADMIN PANEL</span>
            </div>
        </div>

        {{-- Navigation --}}
        <nav class="flex-1 space-y-1 overflow-y-auto px-3 py-4">
            {{-- Menu Utama --}}
            <p class="mb-2 px-3 text-[0.65rem] font-bold uppercase tracking-wider text-gray-500 transition-opacity duration-300"
               :class="sidebarOpen ? '' : 'lg:opacity-0'">Menu Utama</p>

            <a href="{{ route('admin.dashboard') }}"
               class="group flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition-all duration-200
                      {{ request()->routeIs('admin.dashboard') ? 'border-l-2 border-peri bg-peri/10 text-white' : 'text-gray-400 hover:bg-white/5 hover:text-white' }}">
                <i class="fas fa-th-large w-5 text-center {{ request()->routeIs('admin.dashboard') ? 'text-peri' : 'text-gray-500 group-hover:text-gray-300' }}"></i>
                <span class="transition-all duration-300" :class="sidebarOpen ? '' : 'lg:hidden'">Dashboard</span>
            </a>

            {{-- Kelola Toko --}}
            <p class="mb-2 mt-6 px-3 text-[0.65rem] font-bold uppercase tracking-wider text-gray-500 transition-opacity duration-300"
               :class="sidebarOpen ? '' : 'lg:opacity-0'">Kelola Toko</p>

            @php
                $navItems = [
                    ['route' => 'admin.products.index', 'pattern' => 'admin.products*', 'icon' => 'fa-box', 'label' => 'Produk'],
                    ['route' => 'admin.categories.index', 'pattern' => 'admin.categories*', 'icon' => 'fa-tags', 'label' => 'Kategori'],
                    ['route' => 'admin.orders.index', 'pattern' => 'admin.orders*', 'icon' => 'fa-shopping-cart', 'label' => 'Pesanan'],
                    ['route' => 'admin.users.index', 'pattern' => 'admin.users*', 'icon' => 'fa-users', 'label' => 'Pelanggan'],
                    ['route' => 'admin.reviews.index', 'pattern' => 'admin.reviews*', 'icon' => 'fa-star', 'label' => 'Ulasan'],
                    ['route' => 'admin.coupons.index', 'pattern' => 'admin.coupons*', 'icon' => 'fa-ticket-alt', 'label' => 'Kupon'],
                    ['route' => 'admin.stock-alerts.index', 'pattern' => 'admin.stock-alerts*', 'icon' => 'fa-exclamation-triangle', 'label' => 'Stok Menipis'],
                    ['route' => 'admin.banners.index', 'pattern' => 'admin.banners*', 'icon' => 'fa-images', 'label' => 'Banner'],
                    ['route' => 'admin.articles.index', 'pattern' => 'admin.articles*', 'icon' => 'fa-newspaper', 'label' => 'Artikel'],
                    ['route' => 'admin.faqs.index', 'pattern' => 'admin.faqs*', 'icon' => 'fa-question-circle', 'label' => 'FAQ'],
                    ['route' => 'admin.reports.index', 'pattern' => 'admin.reports*', 'icon' => 'fa-chart-bar', 'label' => 'Laporan'],
                ];
                $lowStockCount = \App\Models\Product::active()->where('stock', '<=', (int) config('app.low_stock_threshold', 5))->count();
            @endphp

            @foreach ($navItems as $item)
                <a href="{{ route($item['route']) }}"
                   class="group flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition-all duration-200
                          {{ request()->routeIs($item['pattern']) ? 'border-l-2 border-peri bg-peri/10 text-white' : 'text-gray-400 hover:bg-white/5 hover:text-white' }}">
                    <i class="fas {{ $item['icon'] }} w-5 text-center {{ request()->routeIs($item['pattern']) ? 'text-peri' : 'text-gray-500 group-hover:text-gray-300' }}"></i>
                    <span class="flex-1 transition-all duration-300" :class="sidebarOpen ? '' : 'lg:hidden'">{{ $item['label'] }}</span>
                    @if ($item['route'] === 'admin.stock-alerts.index' && $lowStockCount > 0)
                        <span class="rounded-full bg-amber-500/20 px-2 py-0.5 text-[0.65rem] font-bold text-amber-400 transition-all duration-300"
                              :class="sidebarOpen ? '' : 'lg:hidden'">{{ $lowStockCount }}</span>
                    @endif
                </a>
            @endforeach

            {{-- Lainnya --}}
            <p class="mb-2 mt-6 px-3 text-[0.65rem] font-bold uppercase tracking-wider text-gray-500 transition-opacity duration-300"
               :class="sidebarOpen ? '' : 'lg:opacity-0'">Lainnya</p>

            <a href="{{ route('home') }}" target="_blank"
               class="group flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium text-gray-400 transition-all duration-200 hover:bg-white/5 hover:text-white">
                <i class="fas fa-store w-5 text-center text-gray-500 group-hover:text-gray-300"></i>
                <span class="transition-all duration-300" :class="sidebarOpen ? '' : 'lg:hidden'">Ke Toko</span>
            </a>
            <a href="{{ route('admin.settings.index') }}"
               class="group flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition-all duration-200
                      {{ request()->routeIs('admin.settings.*') ? 'bg-peri/10 text-peri' : 'text-gray-400 hover:bg-white/5 hover:text-white' }}">
                <i class="fas fa-cog w-5 text-center {{ request()->routeIs('admin.settings.*') ? 'text-peri' : 'text-gray-500 group-hover:text-gray-300' }}"></i>
                <span class="transition-all duration-300" :class="sidebarOpen ? '' : 'lg:hidden'">Pengaturan</span>
            </a>
        </nav>

        {{-- Sidebar footer: collapse toggle + user --}}
        <div class="border-t border-gray-800">
            {{-- Collapse toggle (desktop only) --}}
            <button @click="sidebarOpen = !sidebarOpen"
                    class="hidden w-full items-center justify-center py-3 text-gray-500 transition-colors hover:bg-white/5 hover:text-white lg:flex">
                <i class="fas fa-angles-left transition-transform duration-300" :class="sidebarOpen ? '' : 'rotate-180'"></i>
            </button>

            {{-- User info --}}
            <div class="flex items-center gap-3 px-4 py-3">
                <div class="flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-full bg-peri/20 text-sm font-bold text-peri">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
                <div class="min-w-0 flex-1 overflow-hidden transition-all duration-300"
                     :class="sidebarOpen ? '' : 'lg:w-0 lg:opacity-0'">
                    <p class="truncate text-sm font-semibold text-white">{{ auth()->user()->name }}</p>
                    <p class="text-[0.7rem] text-peri-light">{{ ucfirst(auth()->user()->role ?? 'Admin') }}</p>
                </div>
            </div>
        </div>
    </aside>

    {{-- ═══════════ Main Area ═══════════ --}}
    <div class="transition-all duration-300 lg:ml-[260px]"
         :class="sidebarOpen ? 'lg:ml-[260px]' : 'lg:ml-[72px]'">

        {{-- Top Header --}}
        <header class="sticky top-0 z-30 flex h-16 items-center justify-between border-b border-gray-800 bg-gray-900/90 px-4 backdrop-blur-xl sm:px-6">
            {{-- Left --}}
            <div class="flex items-center gap-4">
                <button @click="mobileMenu = !mobileMenu" class="text-gray-400 hover:text-white lg:hidden">
                    <i class="fas fa-bars text-lg"></i>
                </button>
                <div>
                    <h1 class="text-sm font-bold text-white sm:text-base">@yield('page-title', 'Dashboard')</h1>
                    <div class="text-xs text-gray-500">@yield('breadcrumb')</div>
                </div>
            </div>

            {{-- Right --}}
            <div class="flex items-center gap-2">
                <a href="{{ route('home') }}" target="_blank"
                   class="hidden rounded-lg px-3 py-2 text-xs font-medium text-gray-400 transition hover:bg-white/5 hover:text-white sm:inline-flex sm:items-center sm:gap-2">
                    <i class="fas fa-external-link-alt"></i>
                    <span>Ke Toko</span>
                </a>

                {{-- Notification bell placeholder --}}
                <button class="relative flex h-9 w-9 items-center justify-center rounded-lg text-gray-400 transition hover:bg-white/5 hover:text-white">
                    <i class="fas fa-bell"></i>
                    <span class="absolute right-1.5 top-1.5 h-2 w-2 rounded-full bg-peri"></span>
                </button>

                {{-- User dropdown --}}
                <div class="relative" x-data @click.away="userDropdown = false">
                    <button @click="userDropdown = !userDropdown"
                            class="flex items-center gap-2 rounded-lg px-2 py-1.5 transition hover:bg-white/5">
                        <div class="flex h-8 w-8 items-center justify-center rounded-full bg-peri/20 text-xs font-bold text-peri">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>
                        <span class="hidden text-sm font-medium text-gray-300 sm:inline">{{ auth()->user()->name }}</span>
                        <i class="fas fa-chevron-down text-[0.6rem] text-gray-500"></i>
                    </button>

                    <div x-show="userDropdown" x-transition
                         class="absolute right-0 mt-2 w-56 origin-top-right rounded-xl border border-gray-800 bg-gray-900 py-1 shadow-xl">
                        <div class="border-b border-gray-800 px-4 py-3">
                            <p class="text-sm font-bold text-white">{{ auth()->user()->name }}</p>
                            <p class="mt-0.5 text-xs text-gray-500">{{ auth()->user()->email }}</p>
                        </div>
                        <a href="{{ route('profile.edit') }}"
                           class="flex items-center gap-2 px-4 py-2.5 text-sm text-gray-400 transition hover:bg-white/5 hover:text-white">
                            <i class="fas fa-user-circle w-4 text-center"></i> Profile
                        </a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"
                                    class="flex w-full items-center gap-2 px-4 py-2.5 text-sm text-gray-400 transition hover:bg-white/5 hover:text-red-400">
                                <i class="fas fa-sign-out-alt w-4 text-center"></i> Logout
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        {{-- Flash messages --}}
        @if (session('success'))
            <div class="mx-4 mt-4 flex items-center gap-3 rounded-xl border border-green-500/20 bg-green-500/10 px-4 py-3 text-sm text-green-400 sm:mx-6"
                 x-data="{ show: true }" x-show="show" x-transition>
                <i class="fas fa-check-circle"></i>
                <span class="flex-1">{{ session('success') }}</span>
                <button @click="show = false" class="text-green-400/60 hover:text-green-400"><i class="fas fa-times"></i></button>
            </div>
        @endif

        @if (session('error'))
            <div class="mx-4 mt-4 flex items-center gap-3 rounded-xl border border-red-500/20 bg-red-500/10 px-4 py-3 text-sm text-red-400 sm:mx-6"
                 x-data="{ show: true }" x-show="show" x-transition>
                <i class="fas fa-exclamation-circle"></i>
                <span class="flex-1">{{ session('error') }}</span>
                <button @click="show = false" class="text-red-400/60 hover:text-red-400"><i class="fas fa-times"></i></button>
            </div>
        @endif

        {{-- Page content --}}
        <main class="p-4 sm:p-6">
            @yield('content')
        </main>

        {{-- Footer --}}
        <footer class="border-t border-gray-800 px-6 py-4 text-center text-xs text-gray-600">
            &copy; {{ date('Y') }} {{ setting('store_name', 'Shuriza Store') }}. Admin Panel.
        </footer>
    </div>

    @stack('scripts')
</body>
</html>
