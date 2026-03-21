@extends('layouts.app')

@section('title', 'Semua Produk')
@section('description', 'Temukan berbagai produk dan jasa digital premium di ' . setting('store_name', 'Shuriza Store Kediri') . '.')

@push('styles')
<style>
    .filter-sidebar::-webkit-scrollbar { width: 4px; }
    .filter-sidebar::-webkit-scrollbar-thumb { background: rgba(99,102,241,0.3); border-radius: 999px; }
</style>
@endpush

@section('content')

{{-- Page Header --}}
<div class="bg-white dark:bg-gray-800/50 border-b border-gray-200 dark:border-gray-700/50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {{-- Breadcrumb --}}
        <nav class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400 mb-4">
            <a href="{{ route('home') }}" class="hover:text-peri transition-colors">Beranda</a>
            <i class="fas fa-chevron-right text-[10px]"></i>
            <span class="text-gray-900 dark:text-white font-medium">Produk</span>
            @if(request('category'))
                <i class="fas fa-chevron-right text-[10px]"></i>
                <span class="text-gray-900 dark:text-white font-medium">{{ $categories->firstWhere('slug', request('category'))?->name ?? ucfirst(request('category')) }}</span>
            @endif
        </nav>

        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold font-poppins text-gray-900 dark:text-white">
                    @if(request('search'))
                        Hasil Pencarian: "{{ request('search') }}"
                    @elseif(request('category'))
                        {{ $categories->firstWhere('slug', request('category'))?->name ?? 'Produk' }}
                    @else
                        Semua Produk
                    @endif
                </h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $products->total() }} produk ditemukan</p>
            </div>

            {{-- Search --}}
            <form method="GET" action="{{ route('products.index') }}" class="w-full sm:w-80">
                @foreach(request()->except(['search', 'page']) as $key => $val)
                    @if($val) <input type="hidden" name="{{ $key }}" value="{{ $val }}"> @endif
                @endforeach
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari produk..."
                           class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-peri/50 focus:border-peri outline-none transition-all" />
                    <i class="fas fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Main Layout --}}
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8" x-data="{ showFilter: false }">
    <div class="flex gap-8 items-start">

        {{-- Mobile filter overlay --}}
        <div x-show="showFilter" x-transition.opacity @click="showFilter = false"
             class="fixed inset-0 bg-black/50 backdrop-blur-sm z-40 lg:hidden"></div>

        {{-- Sidebar --}}
        <aside class="filter-sidebar w-[280px] flex-shrink-0 lg:sticky lg:top-24
                      fixed inset-y-0 left-0 z-50 lg:z-auto lg:relative
                      bg-white dark:bg-gray-800 rounded-none lg:rounded-2xl shadow-lg lg:shadow-sm
                      border-r lg:border border-gray-200 dark:border-gray-700/50
                      overflow-y-auto lg:max-h-[calc(100vh-7rem)]
                      transform transition-transform duration-300
                      lg:transform-none p-5"
              :class="showFilter ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'">

            {{-- Sidebar header --}}
            <div class="flex items-center justify-between mb-5 pb-4 border-b border-gray-100 dark:border-gray-700/50">
                <span class="text-sm font-bold text-gray-900 dark:text-white flex items-center gap-2">
                    <i class="fas fa-filter text-peri text-xs"></i> Filter Produk
                </span>
                <button @click="showFilter = false" class="lg:hidden w-8 h-8 rounded-lg bg-gray-100 dark:bg-gray-700 flex items-center justify-center text-gray-500 hover:text-gray-900 dark:hover:text-white transition-colors">
                    <i class="fas fa-times text-sm"></i>
                </button>
            </div>

            <form method="GET" action="{{ route('products.index') }}" id="filterForm">
                @if(request('search'))
                    <input type="hidden" name="search" value="{{ request('search') }}" />
                @endif

                {{-- Categories --}}
                <div class="mb-6">
                    <h4 class="text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-3">
                        <i class="fas fa-layer-group text-peri mr-1.5"></i> Kategori
                    </h4>
                    <div class="flex flex-col gap-1">
                        <a href="{{ route('products.index', request()->except('category', 'page')) }}"
                           class="flex items-center justify-between px-3 py-2 rounded-lg text-sm transition-all
                                  {{ !request('category') ? 'bg-peri/10 text-peri font-semibold' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700/50' }}">
                            <span class="flex items-center gap-2.5">
                                <span class="w-7 h-7 rounded-lg flex items-center justify-center text-xs bg-peri/15 text-peri">
                                    <i class="fas fa-th-large"></i>
                                </span>
                                Semua Kategori
                            </span>
                            <span class="text-xs font-semibold px-2 py-0.5 rounded-full {{ !request('category') ? 'bg-peri/20 text-peri' : 'bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400' }}">
                                {{ $products->total() }}
                            </span>
                        </a>

                        @foreach($categories as $cat)
                            <a href="{{ route('products.index', array_merge(request()->except('category', 'page'), ['category' => $cat->slug])) }}"
                               class="flex items-center justify-between px-3 py-2 rounded-lg text-sm transition-all
                                      {{ request('category') === $cat->slug ? 'bg-peri/10 text-peri font-semibold' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700/50' }}">
                                <span class="flex items-center gap-2.5">
                                    <span class="w-7 h-7 rounded-lg flex items-center justify-center text-xs"
                                          style="background: {{ $cat->color ?? '#6c63ff' }}20; color: {{ $cat->color ?? '#6c63ff' }};">
                                        <i class="{{ $cat->icon ?? 'fas fa-box' }}"></i>
                                    </span>
                                    {{ $cat->name }}
                                </span>
                                <span class="text-xs font-semibold px-2 py-0.5 rounded-full {{ request('category') === $cat->slug ? 'bg-peri/20 text-peri' : 'bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400' }}">
                                    {{ $cat->active_products_count }}
                                </span>
                            </a>
                        @endforeach
                    </div>
                </div>

                {{-- Price range --}}
                <div class="mb-6">
                    <h4 class="text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-3">
                        <i class="fas fa-tag text-peri mr-1.5"></i> Rentang Harga
                    </h4>
                    <div class="grid grid-cols-2 gap-2">
                        <input type="number" name="price_min" value="{{ request('price_min') }}" placeholder="Min (Rp)" min="0"
                               class="w-full px-3 py-2 rounded-lg border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-sm text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-peri/50 focus:border-peri outline-none" />
                        <input type="number" name="price_max" value="{{ request('price_max') }}" placeholder="Max (Rp)" min="0"
                               class="w-full px-3 py-2 rounded-lg border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-sm text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-peri/50 focus:border-peri outline-none" />
                    </div>
                </div>

                {{-- Stock filter --}}
                <div class="mb-6">
                    <h4 class="text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-3">
                        <i class="fas fa-box text-peri mr-1.5"></i> Ketersediaan
                    </h4>
                    <label class="flex items-center gap-3 cursor-pointer group">
                        <input type="checkbox" name="in_stock" value="1" {{ request('in_stock') ? 'checked' : '' }}
                               class="w-4 h-4 rounded border-gray-300 dark:border-gray-600 text-peri focus:ring-peri/50" />
                        <span class="text-sm text-gray-600 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white transition-colors">Stok Tersedia</span>
                    </label>
                </div>

                {{-- Sort --}}
                <div class="mb-6">
                    <h4 class="text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-3">
                        <i class="fas fa-sort text-peri mr-1.5"></i> Urutkan
                    </h4>
                    <select name="sort"
                            class="w-full px-3 py-2 rounded-lg border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-peri/50 focus:border-peri outline-none">
                        <option value="default" {{ request('sort', 'default') === 'default' ? 'selected' : '' }}>Default</option>
                        <option value="price-asc" {{ request('sort') === 'price-asc' ? 'selected' : '' }}>Harga Terendah</option>
                        <option value="price-desc" {{ request('sort') === 'price-desc' ? 'selected' : '' }}>Harga Tertinggi</option>
                        <option value="newest" {{ request('sort') === 'newest' ? 'selected' : '' }}>Terbaru</option>
                        <option value="popular" {{ request('sort') === 'popular' ? 'selected' : '' }}>Terpopuler</option>
                    </select>
                </div>

                {{-- Actions --}}
                <div class="flex flex-col gap-2 pt-4 border-t border-gray-100 dark:border-gray-700/50">
                    <button type="submit"
                            class="w-full py-2.5 rounded-xl bg-peri text-white text-sm font-semibold hover:bg-peri/90 transition-colors flex items-center justify-center gap-2">
                        <i class="fas fa-check text-xs"></i> Terapkan Filter
                    </button>
                    <a href="{{ route('products.index') }}"
                       class="w-full py-2.5 rounded-xl border border-red-200 dark:border-red-500/30 text-red-500 text-sm font-semibold hover:bg-red-50 dark:hover:bg-red-500/10 transition-colors flex items-center justify-center gap-2">
                        <i class="fas fa-redo-alt text-xs"></i> Reset Filter
                    </a>
                </div>
            </form>
        </aside>

        {{-- Content Area --}}
        <div class="flex-1 min-w-0">

            {{-- Top bar --}}
            <div class="flex items-center justify-between mb-6 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700/50 px-4 py-3">
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Menampilkan <span class="font-semibold text-gray-900 dark:text-white">{{ $products->count() }}</span>
                    dari <span class="font-semibold text-gray-900 dark:text-white">{{ $products->total() }}</span> produk
                </p>
                <button @click="showFilter = true"
                        class="lg:hidden flex items-center gap-2 px-3 py-2 rounded-lg bg-peri/10 text-peri text-sm font-semibold hover:bg-peri/20 transition-colors">
                    <i class="fas fa-filter text-xs"></i> Filter
                    @if(request()->hasAny(['category','price_min','price_max','in_stock','badge']))
                        <span class="w-5 h-5 rounded-full bg-peri text-white text-[10px] font-bold flex items-center justify-center">
                            {{ count(array_filter(request()->only(['category','price_min','price_max','in_stock','badge']))) }}
                        </span>
                    @endif
                </button>
            </div>

            {{-- Product Grid --}}
            @if($products->count())
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
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

                {{-- Pagination --}}
                <div class="mt-8">
                    {{ $products->withQueryString()->links() }}
                </div>
            @else
                {{-- Empty state --}}
                <div class="text-center py-20">
                    <div class="w-20 h-20 rounded-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center mx-auto mb-5">
                        <i class="fas fa-box-open text-3xl text-gray-300 dark:text-gray-600"></i>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Produk Tidak Ditemukan</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">Coba ubah filter atau kata kunci pencarian kamu</p>
                    <a href="{{ route('products.index') }}"
                       class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-peri text-white text-sm font-semibold hover:bg-peri/90 transition-colors">
                        <i class="fas fa-redo-alt text-xs"></i> Reset Filter
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

@endsection
