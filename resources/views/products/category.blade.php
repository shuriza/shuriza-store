@extends('layouts.app')

@section('title', $category->name)
@section('description', $category->description ?? 'Produk ' . $category->name . ' di ' . setting('store_name', 'Shuriza Store Kediri'))

@push('jsonld')
@php
    $bcItems = [
        ['@type' => 'ListItem', 'position' => 1, 'name' => 'Beranda', 'item' => route('home')],
        ['@type' => 'ListItem', 'position' => 2, 'name' => 'Produk', 'item' => route('products.index')],
        ['@type' => 'ListItem', 'position' => 3, 'name' => $category->name],
    ];
@endphp
<script type="application/ld+json">{!! json_encode(['@context' => 'https://schema.org', '@type' => 'BreadcrumbList', 'itemListElement' => $bcItems], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
@endpush

@section('content')

{{-- Category Hero --}}
<div class="border-b border-gray-200 dark:border-gray-700/50 relative overflow-hidden">
    <div class="absolute inset-0 opacity-5" style="background: {{ $category->color ?? '#6c63ff' }};"></div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 relative">
        {{-- Breadcrumb --}}
        <nav class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400 mb-6">
            <a href="{{ route('home') }}" class="hover:text-peri transition-colors"><i class="fas fa-home text-xs"></i> Beranda</a>
            <i class="fas fa-chevron-right text-[10px]"></i>
            <a href="{{ route('products.index') }}" class="hover:text-peri transition-colors">Produk</a>
            <i class="fas fa-chevron-right text-[10px]"></i>
            <span class="text-gray-900 dark:text-white font-medium">{{ $category->name }}</span>
        </nav>

        <div class="flex items-start gap-5">
            <div class="w-16 h-16 rounded-2xl flex items-center justify-center shadow-lg shrink-0"
                 style="background: {{ $category->color ?? '#6c63ff' }}20;">
                <i class="{{ $category->icon ?? 'fas fa-tag' }} text-3xl" style="color: {{ $category->color ?? '#6c63ff' }};"></i>
            </div>
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold font-poppins text-gray-900 dark:text-white">{{ $category->name }}</h1>
                @if($category->description)
                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400 max-w-2xl leading-relaxed">{{ $category->description }}</p>
                @endif
                <p class="mt-2 text-sm text-gray-400 dark:text-gray-500">{{ $products->total() }} produk tersedia</p>
            </div>
        </div>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    {{-- Popular in Category --}}
    @if($popularInCategory->count() > 0)
    <section class="mb-10">
        <h2 class="text-lg font-poppins font-bold text-gray-900 dark:text-white mb-4">
            <i class="fas fa-fire text-amber-500 mr-1"></i> Populer di {{ $category->name }}
        </h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            @foreach($popularInCategory as $p)
                <a href="{{ route('product.show', $p->slug) }}"
                   class="group bg-white dark:bg-gray-800 rounded-2xl shadow-sm hover:shadow-md transition-all duration-300 overflow-hidden flex flex-col">
                    <div class="relative h-[160px] overflow-hidden bg-gray-100 dark:bg-white/5">
                        <x-product-image :product="$p" />
                        @if($p->badge)
                            <span class="absolute top-2 left-2 px-2 py-0.5 rounded-lg text-[10px] font-bold uppercase tracking-wide text-white
                                {{ $p->badge === 'hot' ? 'bg-red-500' : ($p->badge === 'sale' ? 'bg-amber-500' : 'bg-emerald-500') }}">
                                {{ $p->badge_label }}
                            </span>
                        @endif
                    </div>
                    <div class="p-3 flex flex-col flex-1">
                        <h3 class="text-sm font-bold text-gray-900 dark:text-white line-clamp-2 leading-snug">{{ $p->name }}</h3>
                        <div class="mt-auto pt-2">
                            <span class="text-sm font-bold text-peri">{{ $p->formatted_effective_price }}</span>
                            @if($p->original_price && $p->original_price > $p->price)
                                <span class="text-[11px] text-gray-400 line-through ml-1">Rp {{ number_format($p->original_price, 0, ',', '.') }}</span>
                            @endif
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    </section>
    @endif

    {{-- Toolbar --}}
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
        <h2 class="text-lg font-poppins font-bold text-gray-900 dark:text-white">
            Semua Produk {{ $category->name }}
        </h2>
        <div class="flex items-center gap-3">
            {{-- Search --}}
            <form method="GET" action="{{ route('products.category', $category) }}" class="relative">
                @foreach(request()->except(['search', 'page']) as $key => $val)
                    @if($val) <input type="hidden" name="{{ $key }}" value="{{ $val }}"> @endif
                @endforeach
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari di {{ $category->name }}..."
                       class="w-48 sm:w-64 pl-9 pr-3 py-2 rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-peri/50 focus:border-peri outline-none transition-all" />
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
            </form>
            {{-- Sort --}}
            <select onchange="window.location.href=this.value"
                    class="px-3 py-2 rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-peri/50 outline-none">
                <option value="{{ route('products.category', array_merge([$category->slug], request()->except('sort', 'page'))) }}" {{ !request('sort') ? 'selected' : '' }}>Default</option>
                <option value="{{ route('products.category', array_merge([$category->slug], request()->except('page'), ['sort' => 'price-asc'])) }}" {{ request('sort') === 'price-asc' ? 'selected' : '' }}>Harga Terendah</option>
                <option value="{{ route('products.category', array_merge([$category->slug], request()->except('page'), ['sort' => 'price-desc'])) }}" {{ request('sort') === 'price-desc' ? 'selected' : '' }}>Harga Tertinggi</option>
                <option value="{{ route('products.category', array_merge([$category->slug], request()->except('page'), ['sort' => 'newest'])) }}" {{ request('sort') === 'newest' ? 'selected' : '' }}>Terbaru</option>
                <option value="{{ route('products.category', array_merge([$category->slug], request()->except('page'), ['sort' => 'popular'])) }}" {{ request('sort') === 'popular' ? 'selected' : '' }}>Terpopuler</option>
            </select>
        </div>
    </div>

    {{-- Product Grid --}}
    @if($products->count())
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
            @foreach($products as $product)
                <a href="{{ route('product.show', $product->slug) }}"
                   class="group bg-white dark:bg-gray-800 rounded-2xl shadow-sm hover:shadow-md transition-all duration-300 overflow-hidden flex flex-col">
                    <div class="relative h-[195px] overflow-hidden bg-gray-100 dark:bg-white/5">
                        <x-product-image :product="$product" />
                        @if($product->badge)
                            <span class="absolute top-2 left-2 px-2 py-0.5 rounded-lg text-[10px] font-bold uppercase tracking-wide text-white
                                {{ $product->badge === 'hot' ? 'bg-red-500' : ($product->badge === 'sale' ? 'bg-amber-500' : 'bg-emerald-500') }}">
                                {{ $product->badge_label }}
                            </span>
                        @endif
                    </div>
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
        <div class="mt-8">{{ $products->links() }}</div>
    @else
        <div class="text-center py-20">
            <div class="w-20 h-20 rounded-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center mx-auto mb-5">
                <i class="{{ $category->icon ?? 'fas fa-box' }} text-3xl text-gray-300 dark:text-gray-600"></i>
            </div>
            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Belum Ada Produk</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">Produk untuk kategori {{ $category->name }} akan segera hadir.</p>
            <a href="{{ route('products.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-peri text-white text-sm font-semibold hover:bg-peri/90 transition-colors">
                <i class="fas fa-arrow-left text-xs"></i> Lihat Semua Produk
            </a>
        </div>
    @endif

    {{-- Other Categories --}}
    @if($otherCategories->count() > 0)
    <section class="mt-12 pt-8 border-t border-gray-200 dark:border-gray-700/50">
        <h2 class="text-lg font-poppins font-bold text-gray-900 dark:text-white mb-4">
            <i class="fas fa-th-large text-peri mr-1"></i> Kategori Lainnya
        </h2>
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3">
            @foreach($otherCategories as $cat)
                <a href="{{ route('products.category', $cat) }}"
                   class="group bg-white dark:bg-gray-800 rounded-xl p-4 text-center shadow-sm border border-gray-100 dark:border-gray-700/50 hover:border-peri/30 hover:shadow-md transition-all">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center mx-auto mb-2"
                         style="background: {{ $cat->color ?? '#6c63ff' }}15;">
                        <i class="{{ $cat->icon ?? 'fas fa-tag' }} text-lg" style="color: {{ $cat->color ?? '#6c63ff' }};"></i>
                    </div>
                    <h3 class="text-xs font-bold text-gray-900 dark:text-white group-hover:text-peri transition">{{ $cat->name }}</h3>
                    <p class="text-[10px] text-gray-400 mt-0.5">{{ $cat->active_products_count }} produk</p>
                </a>
            @endforeach
        </div>
    </section>
    @endif

</div>
@endsection
