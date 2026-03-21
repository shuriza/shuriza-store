@extends('layouts.app')

@section('title', 'Wishlist Saya')

@section('content')
<div class="py-8">
    <div class="max-w-6xl mx-auto px-4 sm:px-6">

        {{-- Breadcrumb --}}
        <nav class="flex items-center gap-2 text-sm text-gray-400 mb-6">
            <a href="{{ route('home') }}" class="hover:text-peri transition-colors"><i class="fas fa-home text-xs"></i> Beranda</a>
            <i class="fas fa-chevron-right text-[10px]"></i>
            <span class="text-white">Wishlist</span>
        </nav>

        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-2xl font-poppins font-bold text-white">
                    <i class="fas fa-heart text-pink-500 mr-2"></i>Wishlist Saya
                </h1>
                <p class="mt-1 text-sm text-gray-400">{{ $wishlistItems->total() }} produk tersimpan</p>
            </div>
        </div>

        @if($wishlistItems->count() > 0)
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
                @foreach($wishlistItems as $product)
                    <div class="group relative bg-white dark:bg-gray-800 rounded-2xl shadow-sm hover:shadow-md transition-all duration-300 overflow-hidden flex flex-col">

                        {{-- Remove from wishlist --}}
                        <form action="{{ route('wishlist.toggle', $product) }}" method="POST" class="absolute top-2 right-2 z-10">
                            @csrf
                            <button type="submit" class="w-8 h-8 rounded-full bg-white/90 dark:bg-gray-900/90 text-pink-500 flex items-center justify-center shadow hover:bg-pink-500 hover:text-white transition" title="Hapus dari wishlist">
                                <i class="fas fa-heart text-sm"></i>
                            </button>
                        </form>

                        {{-- Image --}}
                        <a href="{{ route('products.show', $product->slug) }}" class="block">
                            <div class="relative h-[195px] overflow-hidden bg-gray-100 dark:bg-white/5">
                                @if($product->image_url)
                                    <img src="{{ $product->image_url }}" alt="{{ $product->name }}"
                                         class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105" loading="lazy">
                                @else
                                    <div class="w-full h-full flex items-center justify-center" style="background: {{ $product->category->color ?? '#6c63ff' }}20;">
                                        <i class="{{ $product->category->icon ?? 'fas fa-box' }} text-4xl" style="color: {{ $product->category->color ?? '#6c63ff' }};"></i>
                                    </div>
                                @endif
                                @if($product->badge)
                                    <span class="absolute top-2 left-2 px-2 py-0.5 rounded-lg text-[10px] font-bold uppercase tracking-wide text-white
                                        {{ $product->badge === 'hot' ? 'bg-red-500' : ($product->badge === 'sale' ? 'bg-amber-500' : 'bg-emerald-500') }}">
                                        {{ $product->badge_label }}
                                    </span>
                                @endif
                            </div>
                        </a>

                        {{-- Info --}}
                        <div class="p-3 flex flex-col flex-1">
                            @if($product->category)
                                <span class="text-[10px] font-semibold uppercase tracking-wider text-peri/70 mb-1">{{ $product->category->name }}</span>
                            @endif
                            <a href="{{ route('products.show', $product->slug) }}">
                                <h3 class="text-sm font-bold text-gray-900 dark:text-white line-clamp-2 leading-snug hover:text-peri transition-colors">{{ $product->name }}</h3>
                            </a>
                            <div class="mt-auto pt-3 flex items-end justify-between gap-1">
                                <div>
                                    <span class="text-sm font-bold text-peri">{{ $product->formatted_price }}</span>
                                    @if($product->original_price && $product->original_price > $product->price)
                                        <span class="block text-[11px] text-gray-400 line-through">Rp {{ number_format($product->original_price, 0, ',', '.') }}</span>
                                    @endif
                                </div>
                                @if($product->is_in_stock)
                                    <button onclick="addToCart({{ $product->id }})"
                                            class="w-8 h-8 rounded-full bg-peri/10 text-peri hover:bg-peri hover:text-white flex items-center justify-center transition-all duration-200"
                                            title="Tambah ke keranjang">
                                        <i class="fas fa-cart-plus text-xs"></i>
                                    </button>
                                @else
                                    <span class="text-[10px] text-red-400 font-medium">Stok habis</span>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-8">{{ $wishlistItems->links() }}</div>
        @else
            <div class="text-center py-20">
                <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-pink-500/10 mb-4">
                    <i class="fas fa-heart text-3xl text-pink-500/50"></i>
                </div>
                <h2 class="text-lg font-bold text-white mb-2">Wishlist Kosong</h2>
                <p class="text-gray-400 mb-6">Belum ada produk yang Anda simpan.</p>
                <a href="{{ route('products.index') }}"
                   class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-peri text-white font-semibold hover:bg-peri/90 transition">
                    <i class="fas fa-shopping-bag"></i> Jelajahi Produk
                </a>
            </div>
        @endif
    </div>
</div>
@endsection
