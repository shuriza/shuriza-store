@extends('layouts.app')

@section('title', 'Beranda')
@section('description', setting('store_name', 'Shuriza Store Kediri') . ' – ' . setting('store_tagline', 'Toko digital terpercaya di Kediri') . '. Dapatkan produk dan jasa digital premium dengan harga terjangkau.')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
<style>
    .hide-scrollbar::-webkit-scrollbar { display: none; }
    .hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    .line-clamp-2 { display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }

    /* ── Hero Banner Swiper ── */
    .hero-swiper {
        overflow: visible !important;
        padding-bottom: 40px !important;
    }
    .hero-swiper .swiper-wrapper {
        align-items: center;
    }
    .hero-swiper .swiper-slide {
        position: relative;
        border-radius: 1rem;
        overflow: hidden;
        height: 220px;
        transition: transform 0.5s ease, box-shadow 0.5s ease, opacity 0.5s ease;
        opacity: 0.5;
        transform: scale(0.92);
    }
    .hero-swiper .swiper-slide-active {
        opacity: 1;
        transform: scale(1);
        box-shadow: 0 20px 50px -12px rgba(0,0,0,0.4);
        z-index: 2;
    }
    @media (min-width: 640px) {
        .hero-swiper .swiper-slide { height: 320px; }
    }
    @media (min-width: 1024px) {
        .hero-swiper .swiper-slide { height: 400px; }
    }
    .hero-swiper .swiper-slide img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }
    /* Pagination */
    .hero-swiper .swiper-pagination {
        bottom: 0 !important;
    }
    .hero-swiper .swiper-pagination-bullet {
        background: rgba(255,255,255,0.6);
        opacity: 1;
        width: 8px;
        height: 8px;
        transition: all .3s;
    }
    .hero-swiper .swiper-pagination-bullet-active {
        background: #6c63ff;
        width: 28px;
        border-radius: 4px;
    }
    /* Navigation arrows */
    .hero-swiper .swiper-button-next,
    .hero-swiper .swiper-button-prev {
        color: #fff;
        background: rgba(0,0,0,0.3);
        width: 42px;
        height: 42px;
        border-radius: 50%;
        backdrop-filter: blur(8px);
        transition: background .2s;
    }
    .hero-swiper .swiper-button-next:after,
    .hero-swiper .swiper-button-prev:after {
        font-size: 15px;
        font-weight: 700;
    }
    .hero-swiper .swiper-button-next:hover,
    .hero-swiper .swiper-button-prev:hover {
        background: rgba(108,99,255,0.85);
    }
    @media (max-width: 640px) {
        .hero-swiper .swiper-button-next,
        .hero-swiper .swiper-button-prev { display: none; }
    }
</style>
@endpush

@section('content')
<div class="mt-24 max-w-7xl mx-auto px-4 sm:px-6 pb-16 space-y-10">

    {{-- ─── HERO SWIPER ──────────────────────────────────────────────────────── --}}
    <section class="swiper hero-swiper">
        <div class="swiper-wrapper">
            @forelse($banners as $slide)
            <div class="swiper-slide relative {{ $slide->image ? '' : $slide->gradient_class }}">
                @if($slide->image)
                    <img src="{{ $slide->image_url }}" alt="{{ $slide->title ?? 'Banner' }}">
                    @if($slide->title || $slide->subtitle)
                    <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-black/10 to-transparent"></div>
                    @endif
                @endif
                @if($slide->link)
                <a href="{{ $slide->link }}" class="absolute inset-0 z-10" target="_blank" rel="noopener"></a>
                @endif
                @if($slide->title || $slide->subtitle)
                <div class="absolute bottom-0 left-0 right-0 p-5 sm:p-8 z-[5]">
                    @if($slide->title)
                    <h2 class="text-xl sm:text-2xl lg:text-3xl font-poppins font-bold text-white drop-shadow-lg leading-tight">{{ $slide->title }}</h2>
                    @endif
                    @if($slide->subtitle)
                    <p class="mt-1.5 text-sm sm:text-base text-white/80 drop-shadow max-w-lg">{{ $slide->subtitle }}</p>
                    @endif
                </div>
                @endif
            </div>
            @empty
            {{-- Default slides jika belum ada banner --}}
            <div class="swiper-slide relative bg-gradient-to-br from-peri to-peri-dark">
                <div class="absolute inset-0 flex items-center justify-center">
                    <div class="text-center px-6 max-w-xl">
                        <div class="mb-4"><i class="fas fa-store text-4xl sm:text-5xl text-white/30"></i></div>
                        <h2 class="text-xl sm:text-2xl lg:text-3xl font-poppins font-bold text-white leading-tight">{{ setting('hero_title', 'Selamat Datang di ' . setting('store_name', 'Shuriza Store')) }}</h2>
                        <p class="mt-2 text-sm sm:text-base text-white/75">{{ setting('hero_subtitle', 'Toko digital terpercaya di Kediri') }}</p>
                    </div>
                </div>
            </div>
            <div class="swiper-slide relative bg-gradient-to-br from-pink-500 to-purple-600">
                <div class="absolute inset-0 flex items-center justify-center">
                    <div class="text-center px-6 max-w-xl">
                        <div class="mb-4"><i class="fas fa-gem text-4xl sm:text-5xl text-white/30"></i></div>
                        <h2 class="text-xl sm:text-2xl lg:text-3xl font-poppins font-bold text-white leading-tight">Produk Digital Premium</h2>
                        <p class="mt-2 text-sm sm:text-base text-white/75">Kualitas terbaik untuk kebutuhan digitalmu</p>
                    </div>
                </div>
            </div>
            <div class="swiper-slide relative bg-gradient-to-br from-blue-500 to-peri">
                <div class="absolute inset-0 flex items-center justify-center">
                    <div class="text-center px-6 max-w-xl">
                        <div class="mb-4"><i class="fas fa-shield-alt text-4xl sm:text-5xl text-white/30"></i></div>
                        <h2 class="text-xl sm:text-2xl lg:text-3xl font-poppins font-bold text-white leading-tight">Harga Terjangkau &amp; Terpercaya</h2>
                        <p class="mt-2 text-sm sm:text-base text-white/75">Transaksi aman dengan harga bersahabat</p>
                    </div>
                </div>
            </div>
            @endforelse
        </div>
        <div class="swiper-pagination"></div>
        <div class="swiper-button-prev"></div>
        <div class="swiper-button-next"></div>
    </section>

    {{-- ─── FLASH SALE ────────────────────────────────────────────────────── --}}
    @if($flashSaleProducts->count() > 0)
    <section x-data="flashSaleTimer()" x-init="init()" class="relative">
        <div class="bg-gradient-to-r from-red-500/10 to-orange-500/10 dark:from-red-900/20 dark:to-orange-900/20 rounded-2xl border border-red-200 dark:border-red-800/30 p-5">
            <div class="flex items-center justify-between flex-wrap gap-3 mb-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-red-500 flex items-center justify-center animate-pulse">
                        <i class="fas fa-bolt text-white text-lg"></i>
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-gray-900 dark:text-white">⚡ Flash Sale</h2>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Diskon terbatas waktu!</p>
                    </div>
                </div>
                <div class="flex items-center gap-1.5">
                    <div class="bg-gray-900 dark:bg-white/10 text-white rounded-lg px-2 py-1 min-w-[36px] text-center">
                        <span class="text-sm font-bold font-mono" x-text="hours">00</span>
                        <div class="text-[9px] text-gray-400">Jam</div>
                    </div>
                    <span class="text-gray-400 font-bold">:</span>
                    <div class="bg-gray-900 dark:bg-white/10 text-white rounded-lg px-2 py-1 min-w-[36px] text-center">
                        <span class="text-sm font-bold font-mono" x-text="minutes">00</span>
                        <div class="text-[9px] text-gray-400">Min</div>
                    </div>
                    <span class="text-gray-400 font-bold">:</span>
                    <div class="bg-gray-900 dark:bg-white/10 text-white rounded-lg px-2 py-1 min-w-[36px] text-center">
                        <span class="text-sm font-bold font-mono" x-text="seconds">00</span>
                        <div class="text-[9px] text-gray-400">Det</div>
                    </div>
                </div>
            </div>
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
                @foreach($flashSaleProducts as $fp)
                <a href="{{ route('product.show', $fp->slug) }}"
                   class="group bg-white dark:bg-gray-800 rounded-xl overflow-hidden border border-gray-100 dark:border-gray-700 hover:shadow-lg hover:-translate-y-0.5 transition-all">
                    <div class="relative aspect-square">
                        @if($fp->image_url)
                            <img src="{{ $fp->image_url }}" alt="{{ $fp->name }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center">
                                <i class="fas fa-box text-2xl text-gray-300"></i>
                            </div>
                        @endif
                        <div class="absolute top-2 left-2 bg-red-500 text-white text-[10px] font-bold px-2 py-0.5 rounded-full">
                            -{{ $fp->flash_sale_percent }}%
                        </div>
                    </div>
                    <div class="p-3">
                        <h3 class="text-xs font-semibold text-gray-900 dark:text-white truncate group-hover:text-peri transition">{{ $fp->name }}</h3>
                        <div class="mt-1 flex items-center gap-2">
                            <span class="text-sm font-bold text-red-500">{{ $fp->formatted_effective_price }}</span>
                            <span class="text-[10px] text-gray-400 line-through">{{ $fp->formatted_price }}</span>
                        </div>
                    </div>
                </a>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    {{-- ─── CATEGORY TABS ────────────────────────────────────────────────────── --}}
    <section x-data="{ filterOpen: false }" class="relative">
        <div class="flex items-center gap-3">
            <div class="flex-1 flex gap-2 overflow-x-auto hide-scrollbar py-1">
                <a href="{{ route('home', array_merge(request()->except('kategori', 'page'), [])) }}"
                   class="shrink-0 px-4 py-2 rounded-full text-sm font-semibold transition-all duration-200
                          {{ !request('kategori') ? 'bg-peri text-white shadow-md shadow-peri/30' : 'bg-white dark:bg-white/5 border border-gray-200 dark:border-white/10 text-gray-600 dark:text-gray-400 hover:border-peri/40' }}">
                    Semua
                </a>
                @foreach($categories as $cat)
                    <a href="{{ route('home', array_merge(request()->except('page'), ['kategori' => $cat->slug])) }}"
                       class="shrink-0 px-4 py-2 rounded-full text-sm font-semibold transition-all duration-200
                              {{ request('kategori') === $cat->slug ? 'bg-peri text-white shadow-md shadow-peri/30' : 'bg-white dark:bg-white/5 border border-gray-200 dark:border-white/10 text-gray-600 dark:text-gray-400 hover:border-peri/40' }}">
                        {{ $cat->name }}
                    </a>
                @endforeach
            </div>
            <div class="relative shrink-0">
                <button @click="filterOpen = !filterOpen"
                        class="flex items-center gap-2 px-4 py-2 rounded-full text-sm font-semibold bg-white dark:bg-white/5 border border-gray-200 dark:border-white/10 text-gray-600 dark:text-gray-400 hover:border-peri/40 transition-all duration-200">
                    <i class="fas fa-sliders-h text-xs"></i> Filter
                </button>
                <div x-show="filterOpen" @click.outside="filterOpen = false" x-transition x-cloak
                     class="absolute right-0 mt-2 w-56 bg-white dark:bg-gray-800 rounded-xl shadow-xl border border-gray-200 dark:border-white/10 p-3 z-30">
                    <a href="{{ route('home', array_merge(request()->query(), ['in_stock' => request()->boolean('in_stock') ? '0' : '1'])) }}"
                       class="flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-gray-100 dark:hover:bg-white/5 text-sm text-gray-700 dark:text-gray-300 transition">
                        <span class="w-5 h-5 rounded border-2 flex items-center justify-center {{ request()->boolean('in_stock') ? 'bg-peri border-peri text-white' : 'border-gray-300 dark:border-gray-600' }}">
                            @if(request()->boolean('in_stock'))
                                <i class="fas fa-check text-[10px]"></i>
                            @endif
                        </span>
                        Sembunyikan Stok Kosong
                    </a>
                </div>
            </div>
        </div>
    </section>

    {{-- ─── PRODUCT GRID ─────────────────────────────────────────────────────── --}}
    <section>
        @if($products->count())
            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-4">
                @foreach($products as $product)
                    <a href="{{ route('product.show', $product->slug) }}"
                       class="group bg-white dark:bg-gray-800 rounded-2xl shadow-sm hover:shadow-md transition-all duration-300 overflow-hidden flex flex-col">
                        {{-- Image --}}
                        <div class="relative h-[195px] overflow-hidden bg-gray-100 dark:bg-white/5">
                            @if($product->image_url)
                                <img src="{{ $product->image_url }}" alt="{{ $product->name }}"
                                     class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105" loading="lazy">
                            @else
                                <div class="w-full h-full flex items-center justify-center"
                                     style="background: {{ $product->category?->color ?? '#6c63ff' }}20;">
                                    <i class="{{ $product->category?->icon ?? 'fas fa-box' }} text-4xl" style="color: {{ $product->category?->color ?? '#6c63ff' }};"></i>
                                </div>
                            @endif
                            @if($product->badge)
                                <span class="absolute top-2 left-2 px-2 py-0.5 rounded-lg text-[10px] font-bold uppercase tracking-wide text-white
                                    {{ $product->badge === 'hot' ? 'bg-red-500' : ($product->badge === 'sale' ? 'bg-amber-500' : 'bg-emerald-500') }}">
                                    {{ $product->badge_label }}
                                </span>
                            @endif
                        </div>
                        {{-- Info --}}
                        <div class="p-3 flex flex-col flex-1">
                            <h3 class="text-sm font-bold text-gray-900 dark:text-white line-clamp-2 leading-snug">{{ $product->name }}</h3>
                            <div class="mt-auto pt-2 flex items-end justify-between gap-1">
                                <div>
                                    <span class="text-sm font-bold text-peri">{{ $product->formatted_effective_price }}</span>
                                    @if($product->is_flash_sale)
                                        <span class="block text-[11px] text-gray-400 line-through">{{ $product->formatted_price }}</span>
                                    @elseif($product->original_price && $product->original_price > $product->price)
                                        <span class="block text-[11px] text-gray-400 line-through">Rp {{ number_format($product->original_price, 0, ',', '.') }}</span>
                                    @endif
                                </div>
                                <div class="flex items-center gap-1.5">
                                    <span class="w-2 h-2 rounded-full {{ $product->is_in_stock ? 'bg-emerald-400' : 'bg-red-400' }}"></span>
                                    @if($product->is_in_stock)
                                        <button onclick="event.preventDefault(); event.stopPropagation(); addToCart({{ $product->id }})"
                                                class="w-8 h-8 rounded-full bg-peri/10 text-peri hover:bg-peri hover:text-white flex items-center justify-center transition-all duration-200"
                                                title="Tambah ke keranjang">
                                            <i class="fas fa-cart-plus text-xs"></i>
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
            <div class="mt-8">
                {{ $products->links() }}
            </div>
        @else
            <div class="text-center py-20">
                <div class="w-20 h-20 mx-auto rounded-full bg-gray-100 dark:bg-white/5 flex items-center justify-center mb-4">
                    <i class="fas fa-box-open text-3xl text-gray-300 dark:text-gray-600"></i>
                </div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">Produk tidak ditemukan</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Coba ubah filter atau kata kunci pencarian.</p>
                <a href="{{ route('home') }}" class="inline-flex items-center gap-2 mt-4 px-5 py-2.5 rounded-full bg-peri text-white text-sm font-semibold hover:bg-peri-dark transition">
                    <i class="fas fa-arrow-left text-xs"></i> Lihat Semua Produk
                </a>
            </div>
        @endif
    </section>

    {{-- ─── POPULAR PRODUCTS ─────────────────────────────────────────────────── --}}
    @if($popularProducts->count())
    <section>
        <div class="flex items-center justify-between mb-5">
            <h2 class="text-xl font-poppins font-bold text-gray-900 dark:text-white">
                <i class="fas fa-fire text-amber-500 mr-1"></i> Produk Populer
            </h2>
            <a href="{{ route('products.index') }}" class="text-sm text-peri hover:text-peri-dark font-semibold transition">
                Lihat Semua <i class="fas fa-arrow-right text-xs ml-1"></i>
            </a>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            @foreach($popularProducts as $p)
                <a href="{{ route('product.show', $p->slug) }}"
                   class="group bg-white dark:bg-gray-800 rounded-2xl shadow-sm hover:shadow-md transition-all duration-300 overflow-hidden flex flex-col">
                    <div class="relative h-[195px] overflow-hidden bg-gray-100 dark:bg-white/5">
                        @if($p->image_url)
                            <img src="{{ $p->image_url }}" alt="{{ $p->name }}"
                                 class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105" loading="lazy">
                        @else
                            <div class="w-full h-full flex items-center justify-center"
                                 style="background: {{ $p->category?->color ?? '#6c63ff' }}20;">
                                <i class="{{ $p->category?->icon ?? 'fas fa-box' }} text-4xl" style="color: {{ $p->category?->color ?? '#6c63ff' }};"></i>
                            </div>
                        @endif
                        @if($p->badge)
                            <span class="absolute top-2 left-2 px-2 py-0.5 rounded-lg text-[10px] font-bold uppercase tracking-wide text-white
                                {{ $p->badge === 'hot' ? 'bg-red-500' : ($p->badge === 'sale' ? 'bg-amber-500' : 'bg-emerald-500') }}">
                                {{ $p->badge_label }}
                            </span>
                        @endif
                    </div>
                    <div class="p-3 flex flex-col flex-1">
                        <h3 class="text-sm font-bold text-gray-900 dark:text-white line-clamp-2 leading-snug">{{ $p->name }}</h3>
                        <div class="mt-auto pt-2 flex items-end justify-between gap-1">
                            <div>
                                <span class="text-sm font-bold text-peri">{{ $p->formatted_effective_price }}</span>
                                @if($p->is_flash_sale)
                                    <span class="block text-[11px] text-gray-400 line-through">{{ $p->formatted_price }}</span>
                                @elseif($p->original_price && $p->original_price > $p->price)
                                    <span class="block text-[11px] text-gray-400 line-through">Rp {{ number_format($p->original_price, 0, ',', '.') }}</span>
                                @endif
                            </div>
                            <div class="flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full {{ $p->is_in_stock ? 'bg-emerald-400' : 'bg-red-400' }}"></span>
                                @if($p->is_in_stock)
                                    <button onclick="event.preventDefault(); event.stopPropagation(); addToCart({{ $p->id }})"
                                            class="w-8 h-8 rounded-full bg-peri/10 text-peri hover:bg-peri hover:text-white flex items-center justify-center transition-all duration-200"
                                            title="Tambah ke keranjang">
                                        <i class="fas fa-cart-plus text-xs"></i>
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    </section>
    @endif

    {{-- ─── WHY CHOOSE US ────────────────────────────────────────────────────── --}}
    <section>
        <div class="text-center mb-8">
            <h2 class="text-2xl sm:text-3xl font-poppins font-bold text-gray-900 dark:text-white">
                Kenapa Belanja di {{ setting('store_name', 'Shuriza Store') }}?
            </h2>
            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Keunggulan yang membuat kami berbeda</p>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 text-center shadow-sm border border-gray-100 dark:border-white/5">
                <div class="w-12 h-12 rounded-xl bg-peri/10 flex items-center justify-center mx-auto mb-3">
                    <i class="fas fa-bolt text-xl text-peri"></i>
                </div>
                <h3 class="text-sm font-bold text-gray-900 dark:text-white">Instan</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Pengiriman langsung setelah bayar</p>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 text-center shadow-sm border border-gray-100 dark:border-white/5">
                <div class="w-12 h-12 rounded-xl bg-accent/10 flex items-center justify-center mx-auto mb-3">
                    <i class="fas fa-shield-alt text-xl text-accent"></i>
                </div>
                <h3 class="text-sm font-bold text-gray-900 dark:text-white">Garansi</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Garansi penuh setiap produk</p>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 text-center shadow-sm border border-gray-100 dark:border-white/5">
                <div class="w-12 h-12 rounded-xl bg-secondary/10 flex items-center justify-center mx-auto mb-3">
                    <i class="fas fa-headset text-xl text-secondary"></i>
                </div>
                <h3 class="text-sm font-bold text-gray-900 dark:text-white">Support 24/7</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Admin siap bantu kapan saja</p>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 text-center shadow-sm border border-gray-100 dark:border-white/5">
                <div class="w-12 h-12 rounded-xl bg-blue-500/10 flex items-center justify-center mx-auto mb-3">
                    <i class="fas fa-tags text-xl text-blue-500"></i>
                </div>
                <h3 class="text-sm font-bold text-gray-900 dark:text-white">Murah</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Harga terjangkau & kompetitif</p>
            </div>
        </div>
    </section>

    {{-- ─── STATS COUNTER ────────────────────────────────────────────────────── --}}
    <section x-data="statsCounter()" x-init="observeAndCount($el)" class="grid grid-cols-3 gap-4">
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 text-center shadow-sm">
            <div class="text-2xl sm:text-3xl font-poppins font-bold text-peri" x-text="formatted(orders)">0</div>
            <div class="mt-1 text-xs sm:text-sm text-gray-500 dark:text-gray-400">Transaksi</div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 text-center shadow-sm">
            <div class="text-2xl sm:text-3xl font-poppins font-bold text-peri" x-text="formatted(produk)">0</div>
            <div class="mt-1 text-xs sm:text-sm text-gray-500 dark:text-gray-400">Produk</div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 text-center shadow-sm">
            <div class="text-2xl sm:text-3xl font-poppins font-bold text-peri" x-text="formatted(kategori)">0</div>
            <div class="mt-1 text-xs sm:text-sm text-gray-500 dark:text-gray-400">Kategori</div>
        </div>
    </section>

    {{-- ─── ARTIKEL TERBARU ─────────────────────────────────────────────────── --}}
    @if($latestArticles->isNotEmpty())
    <section>
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-2xl sm:text-3xl font-poppins font-bold text-gray-900 dark:text-white">
                    Artikel Terbaru
                </h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Tips & info seputar produk digital</p>
            </div>
            <a href="{{ route('articles.index') }}"
               class="hidden sm:inline-flex items-center gap-1 text-sm font-semibold text-peri hover:text-peri-light transition">
                Lihat Semua <i class="fas fa-arrow-right text-xs"></i>
            </a>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
            @foreach($latestArticles as $article)
            <a href="{{ route('articles.show', $article) }}"
               class="group rounded-2xl border border-gray-200 dark:border-white/5 bg-white dark:bg-gray-800 overflow-hidden shadow-sm hover:shadow-lg hover:border-peri/30 dark:hover:border-peri/30 transition-all duration-300">
                <div class="relative h-40 overflow-hidden bg-gray-100 dark:bg-gray-700">
                    @if($article->image)
                        <img src="{{ $article->image_url }}" alt="{{ $article->title }}"
                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                    @else
                        <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-peri/20 to-peri/5">
                            <i class="fas fa-newspaper text-3xl text-peri/30"></i>
                        </div>
                    @endif
                </div>
                <div class="p-4">
                    <div class="flex items-center gap-3 text-xs text-gray-400 dark:text-gray-500 mb-2">
                        <span><i class="far fa-calendar mr-1"></i> {{ $article->formatted_date }}</span>
                        <span><i class="far fa-clock mr-1"></i> {{ $article->reading_time }} min</span>
                    </div>
                    <h3 class="text-base font-bold text-gray-900 dark:text-white group-hover:text-peri transition-colors line-clamp-2">
                        {{ $article->title }}
                    </h3>
                    @if($article->excerpt)
                    <p class="mt-1.5 text-sm text-gray-500 dark:text-gray-400 line-clamp-2">{{ $article->excerpt }}</p>
                    @endif
                </div>
            </a>
            @endforeach
        </div>
        <div class="mt-4 text-center sm:hidden">
            <a href="{{ route('articles.index') }}" class="text-sm font-semibold text-peri hover:text-peri-light transition">
                Lihat Semua Artikel <i class="fas fa-arrow-right text-xs"></i>
            </a>
        </div>
    </section>
    @endif

    {{-- ─── TESTIMONIALS ─────────────────────────────────────────────────────── --}}
    <section>
        <div class="text-center mb-8">
            <h2 class="text-2xl sm:text-3xl font-poppins font-bold text-gray-900 dark:text-white">
                Apa Kata Mereka?
            </h2>
            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Testimoni dari pelanggan setia {{ setting('store_name', 'Shuriza Store') }}</p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            @php
                $testimonials = [
                    ['name' => 'Andi S.', 'text' => 'Pengiriman cepat banget! Baru bayar langsung dapat akun Netflix. Recommended seller 👍', 'rating' => 5, 'product' => 'Netflix Premium'],
                    ['name' => 'Putri R.', 'text' => 'Harganya murah dibanding tempat lain, kualitas produk juga bagus. Sudah langganan di sini.', 'rating' => 5, 'product' => 'Spotify Premium'],
                    ['name' => 'Budi K.', 'text' => 'Admin ramah dan fast response. Pernah ada masalah langsung diganti. Mantap!', 'rating' => 5, 'product' => 'Microsoft Office'],
                ];
            @endphp
            @foreach($testimonials as $t)
                <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 shadow-sm border border-gray-100 dark:border-white/5">
                    <div class="flex items-center gap-1 mb-3">
                        @for($i = 0; $i < $t['rating']; $i++)
                            <i class="fas fa-star text-amber-400 text-xs"></i>
                        @endfor
                    </div>
                    <p class="text-sm text-gray-600 dark:text-gray-300 leading-relaxed mb-4">"{{ $t['text'] }}"</p>
                    <div class="flex items-center gap-3 pt-3 border-t border-gray-100 dark:border-white/5">
                        <div class="w-9 h-9 rounded-full bg-peri/10 flex items-center justify-center">
                            <i class="fas fa-user text-peri text-xs"></i>
                        </div>
                        <div>
                            <div class="text-sm font-semibold text-gray-900 dark:text-white">{{ $t['name'] }}</div>
                            <div class="text-xs text-gray-400">Pembeli {{ $t['product'] }}</div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    {{-- ─── REGISTER CTA (GUEST ONLY) ───────────────────────────────────────── --}}
    @guest
    <section class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-peri to-peri-dark p-8 sm:p-12">
        {{-- Decorative blobs --}}
        <div class="absolute -top-16 -left-16 w-48 h-48 rounded-full bg-white/10 blur-3xl"></div>
        <div class="absolute -bottom-20 -right-20 w-64 h-64 rounded-full bg-pink-500/10 blur-3xl"></div>

        <div class="relative flex flex-col lg:flex-row items-center gap-8">
            <div class="flex-1 text-center lg:text-left">
                <h2 class="text-2xl sm:text-3xl font-poppins font-bold text-white leading-tight">
                    Dapatkan berbagai keuntungan dengan mendaftar
                </h2>
                <p class="mt-3 text-white/75 text-sm sm:text-base leading-relaxed max-w-xl">
                    Kamu akan bebas biaya admin, lebih mudah mengelola transaksi dan pesanan, serta bebas mengatur jumlah dan menyimpannya ke dalam keranjang.
                </p>
                <a href="{{ route('register') }}"
                   class="inline-flex items-center gap-2 mt-6 px-6 py-3 rounded-full bg-white text-peri font-bold text-sm hover:bg-gray-100 transition-all duration-200 shadow-lg shadow-black/10">
                    <i class="fas fa-user-plus text-xs"></i> Daftar Sekarang
                </a>
            </div>
            <div class="hidden lg:flex shrink-0 w-48 h-48 rounded-2xl bg-white/10 backdrop-blur-sm items-center justify-center">
                <i class="fas fa-gift text-6xl text-white/60"></i>
            </div>
        </div>
    </section>
    @endguest

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        new Swiper('.hero-swiper', {
            rewind: true,
            centeredSlides: true,
            slidesPerView: 1.15,
            spaceBetween: 16,
            grabCursor: true,
            autoplay: { delay: 4500, disableOnInteraction: false },
            pagination: { el: '.swiper-pagination', clickable: true },
            navigation: {
                nextEl: '.swiper-button-next',
                prevEl: '.swiper-button-prev',
            },
            breakpoints: {
                640: { slidesPerView: 1.25, spaceBetween: 20 },
                1024: { slidesPerView: 1.35, spaceBetween: 28 },
            },
            speed: 600,
        });
    });

    function statsCounter() {
        return {
            orders: 0,
            produk: 0,
            kategori: 0,
            targetOrders: {{ $stats['orders'] ?? 0 }},
            targetProduk: {{ $stats['products'] ?? 0 }},
            targetKategori: {{ $stats['categories'] ?? 0 }},
            _counted: false,
            formatted(n) { return n.toLocaleString('id-ID'); },
            observeAndCount(el) {
                const observer = new IntersectionObserver((entries) => {
                    if (entries[0].isIntersecting && !this._counted) {
                        this._counted = true;
                        this.startCount();
                        observer.disconnect();
                    }
                }, { threshold: 0.3 });
                observer.observe(el);
            },
            startCount() {
                this.animateTo('orders', this.targetOrders);
                this.animateTo('produk', this.targetProduk);
                this.animateTo('kategori', this.targetKategori);
            },
            animateTo(prop, target) {
                if (target === 0) return;
                const duration = 1200;
                const steps = 40;
                const increment = target / steps;
                let current = 0;
                const interval = setInterval(() => {
                    current += increment;
                    if (current >= target) {
                        this[prop] = target;
                        clearInterval(interval);
                    } else {
                        this[prop] = Math.floor(current);
                    }
                }, duration / steps);
            }
        };
    }

    function flashSaleTimer() {
        return {
            hours: '00', minutes: '00', seconds: '00',
            endTime: null,
            init() {
                @if($flashSaleProducts->count() > 0)
                    this.endTime = new Date('{{ $flashSaleProducts->first()->flash_sale_end->toIso8601String() }}').getTime();
                    this.tick();
                    setInterval(() => this.tick(), 1000);
                @endif
            },
            tick() {
                const now = Date.now();
                const diff = Math.max(0, this.endTime - now);
                const h = Math.floor(diff / 3600000);
                const m = Math.floor((diff % 3600000) / 60000);
                const s = Math.floor((diff % 60000) / 1000);
                this.hours = String(h).padStart(2, '0');
                this.minutes = String(m).padStart(2, '0');
                this.seconds = String(s).padStart(2, '0');
            }
        };
    }
</script>
@endpush
