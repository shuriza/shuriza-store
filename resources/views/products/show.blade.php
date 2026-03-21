@extends('layouts.app')

@section('title', $product->name)
@section('description', $product->short_description ?? Str::limit(strip_tags($product->description), 160))
@if($product->image_url)
@section('og_image', $product->image_url)
@endif

@push('styles')
<style>
    .prose-description { line-height: 1.75; }
    .prose-description p { margin-bottom: 0.75rem; }
</style>
@endpush

@section('content')
<div class="py-8">
    <div class="max-w-6xl mx-auto px-4 sm:px-6">

        {{-- Breadcrumb --}}
        <nav class="flex items-center flex-wrap gap-2 text-sm text-gray-500 dark:text-gray-400 mb-8">
            <a href="{{ route('home') }}" class="hover:text-peri transition-colors"><i class="fas fa-home text-xs"></i> Beranda</a>
            <i class="fas fa-chevron-right text-[10px]"></i>
            <a href="{{ route('products.index') }}" class="hover:text-peri transition-colors">Produk</a>
            @if($product->category)
                <i class="fas fa-chevron-right text-[10px]"></i>
                <a href="{{ route('products.index', ['category' => $product->category->slug]) }}" class="hover:text-peri transition-colors">
                    {{ $product->category->name }}
                </a>
            @endif
            <i class="fas fa-chevron-right text-[10px]"></i>
            <span class="text-gray-900 dark:text-white">{{ Str::limit($product->name, 40) }}</span>
        </nav>

        {{-- Product Detail Grid --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-12 items-start">

            {{-- LEFT: Image --}}
            <div class="lg:sticky lg:top-24">
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm overflow-hidden">
                    <div class="relative aspect-square">
                        @if($product->image_url)
                            <img src="{{ $product->image_url }}" alt="{{ $product->name }}"
                                 class="w-full h-full object-cover" loading="lazy">
                        @else
                            <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-peri/10 to-peri/5">
                                <i class="fas fa-box-open text-7xl text-peri/40"></i>
                            </div>
                        @endif

                        @if($product->badge)
                            <span class="absolute top-3 left-3 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wide text-white
                                {{ $product->badge === 'hot' ? 'bg-red-500' : ($product->badge === 'sale' ? 'bg-amber-500' : 'bg-emerald-500') }}">
                                {{ $product->badge_label }}
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            {{-- RIGHT: Info --}}
            <div class="space-y-5">

                {{-- Category pill --}}
                @if($product->category)
                    <a href="{{ route('products.index', ['category' => $product->category->slug]) }}"
                       class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold uppercase tracking-wider
                              bg-peri/10 text-peri border border-peri/20 hover:bg-peri/20 transition-colors">
                        <i class="{{ $product->category->icon ?? 'fas fa-tag' }} text-[10px]"></i>
                        {{ $product->category->name }}
                    </a>
                @endif

                {{-- Title --}}
                <h1 class="text-2xl sm:text-3xl font-poppins font-bold text-gray-900 dark:text-white leading-tight">
                    {{ $product->name }}
                </h1>

                {{-- Price --}}
                <div class="flex items-baseline flex-wrap gap-3">
                    <span class="text-3xl font-bold text-peri">{{ $product->formatted_effective_price }}</span>
                    @if($product->is_flash_sale)
                        <span class="text-base text-gray-400 line-through">{{ $product->formatted_price }}</span>
                        <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-red-500/15 text-red-500">
                            <i class="fas fa-bolt mr-0.5"></i>-{{ $product->flash_sale_percent }}%
                        </span>
                    @elseif($product->original_price && $product->original_price > $product->price)
                        <span class="text-base text-gray-400 line-through">Rp {{ number_format($product->original_price, 0, ',', '.') }}</span>
                        @php $discount = round(($product->original_price - $product->price) / $product->original_price * 100); @endphp
                        <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-amber-500/15 text-amber-500">-{{ $discount }}%</span>
                    @endif
                </div>

                {{-- Short description --}}
                @if($product->short_description)
                    <p class="text-gray-600 dark:text-gray-300 leading-relaxed">{{ $product->short_description }}</p>
                @endif

                {{-- Stock status --}}
                <div class="flex items-center gap-2 text-sm">
                    @if($product->is_in_stock)
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 animate-pulse"></span>
                        <span class="text-emerald-500 dark:text-emerald-400 font-medium">Stok tersedia: {{ $product->stock }}</span>
                    @else
                        <span class="w-2.5 h-2.5 rounded-full bg-red-400"></span>
                        <span class="text-red-500 dark:text-red-400 font-medium">Stok habis</span>
                    @endif
                </div>

                {{-- Quantity + Add to Cart --}}
                @if($product->is_in_stock)
                    <div x-data="{ qty: 1, max: {{ $product->stock }} }" class="space-y-4">
                        {{-- Quantity selector --}}
                        <div class="flex items-center gap-3">
                            <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Jumlah:</span>
                            <div class="flex items-center gap-1 bg-gray-100 dark:bg-white/10 rounded-full px-1.5 py-1">
                                <button type="button" @click="qty = Math.max(1, qty - 1)"
                                        class="w-8 h-8 rounded-full flex items-center justify-center text-gray-600 dark:text-gray-300 hover:bg-white dark:hover:bg-white/10 transition-colors">
                                    <i class="fas fa-minus text-xs"></i>
                                </button>
                                <input type="number" x-model.number="qty" min="1" :max="max"
                                       @change="qty = Math.min(Math.max(1, qty), max)"
                                       class="w-12 text-center bg-transparent border-none text-gray-900 dark:text-white font-semibold text-sm focus:outline-none [&::-webkit-inner-spin-button]:appearance-none">
                                <button type="button" @click="qty = Math.min(max, qty + 1)"
                                        class="w-8 h-8 rounded-full flex items-center justify-center text-gray-600 dark:text-gray-300 hover:bg-white dark:hover:bg-white/10 transition-colors">
                                    <i class="fas fa-plus text-xs"></i>
                                </button>
                            </div>
                        </div>

                        {{-- Add to cart button --}}
                        <button @click="addToCart({{ $product->id }}, qty)"
                                class="w-full flex items-center justify-center gap-2 px-6 py-3.5 rounded-xl bg-peri text-white font-semibold
                                       hover:bg-peri/90 active:scale-[0.98] transition-all duration-200 shadow-lg shadow-peri/25">
                            <i class="fas fa-shopping-cart"></i>
                            Tambah ke Keranjang
                        </button>

                        {{-- Wishlist button --}}
                        @auth
                            <form action="{{ route('wishlist.toggle', $product) }}" method="POST">
                                @csrf
                                @php $isWishlisted = auth()->user()->wishlists()->where('product_id', $product->id)->exists(); @endphp
                                <button type="submit"
                                        class="w-full flex items-center justify-center gap-2 px-6 py-3 rounded-xl border transition-all duration-200
                                               {{ $isWishlisted ? 'border-pink-500/30 bg-pink-500/10 text-pink-500 hover:bg-pink-500/20' : 'border-gray-200 dark:border-white/10 text-gray-500 hover:text-pink-500 hover:border-pink-500/30' }}">
                                    <i class="fas fa-heart"></i>
                                    {{ $isWishlisted ? 'Hapus dari Wishlist' : 'Tambah ke Wishlist' }}
                                </button>
                            </form>
                        @endauth
                    </div>
                @else
                    <button disabled
                            class="w-full flex items-center justify-center gap-2 px-6 py-3.5 rounded-xl bg-gray-200 dark:bg-white/10
                                   text-gray-400 dark:text-gray-500 font-semibold cursor-not-allowed">
                        <i class="fas fa-ban"></i>
                        Stok Habis
                    </button>
                @endif

                {{-- Info pills --}}
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-2 pt-2">
                    <div class="flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400 bg-gray-50 dark:bg-white/5 rounded-xl px-3 py-2.5">
                        <i class="fas fa-compact-disc text-peri"></i> Produk Digital
                    </div>
                    <div class="flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400 bg-gray-50 dark:bg-white/5 rounded-xl px-3 py-2.5">
                        <i class="fas fa-bolt text-peri"></i> Pengiriman Instan
                    </div>
                    <div class="flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400 bg-gray-50 dark:bg-white/5 rounded-xl px-3 py-2.5">
                        <i class="fas fa-shield-alt text-peri"></i> Transaksi Aman
                    </div>
                    <div class="flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400 bg-gray-50 dark:bg-white/5 rounded-xl px-3 py-2.5">
                        <i class="fas fa-headset text-peri"></i> Support 24/7
                    </div>
                    <div class="flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400 bg-gray-50 dark:bg-white/5 rounded-xl px-3 py-2.5">
                        <i class="fas fa-check-circle text-peri"></i> Produk Asli
                    </div>
                    <div class="flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400 bg-gray-50 dark:bg-white/5 rounded-xl px-3 py-2.5">
                        <i class="fas fa-undo text-peri"></i> Garansi
                    </div>
                </div>

                {{-- Share Buttons --}}
                <div class="flex items-center gap-2 pt-3 border-t border-gray-100 dark:border-white/5" x-data="{ copied: false }">
                    <span class="text-xs text-gray-400 mr-1"><i class="fas fa-share-alt"></i> Bagikan:</span>
                    <a href="https://wa.me/?text={{ urlencode($product->name . ' - ' . $product->formatted_price . ' ' . route('products.show', $product->slug)) }}"
                       target="_blank" rel="noopener"
                       class="w-8 h-8 rounded-lg bg-green-500/10 text-green-500 flex items-center justify-center text-sm hover:bg-green-500/20 transition" title="WhatsApp">
                        <i class="fab fa-whatsapp"></i>
                    </a>
                    <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(route('products.show', $product->slug)) }}"
                       target="_blank" rel="noopener"
                       class="w-8 h-8 rounded-lg bg-blue-500/10 text-blue-500 flex items-center justify-center text-sm hover:bg-blue-500/20 transition" title="Facebook">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                    <a href="https://twitter.com/intent/tweet?text={{ urlencode($product->name . ' ' . route('products.show', $product->slug)) }}"
                       target="_blank" rel="noopener"
                       class="w-8 h-8 rounded-lg bg-sky-500/10 text-sky-500 flex items-center justify-center text-sm hover:bg-sky-500/20 transition" title="Twitter">
                        <i class="fab fa-twitter"></i>
                    </a>
                    <button @click="navigator.clipboard.writeText('{{ route('products.show', $product->slug) }}'); copied = true; setTimeout(() => copied = false, 2000)"
                            class="w-8 h-8 rounded-lg bg-gray-100 dark:bg-white/5 text-gray-500 flex items-center justify-center text-sm hover:bg-gray-200 dark:hover:bg-white/10 transition" title="Salin Link">
                        <i class="fas" :class="copied ? 'fa-check text-green-500' : 'fa-link'"></i>
                    </button>
                    <span x-show="copied" x-transition class="text-xs text-green-500 ml-1">Tersalin!</span>
                </div>
            </div>
        </div>

        {{-- Description Section --}}
        <div class="mt-12">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm p-6 sm:p-8">
                <h2 class="text-lg font-poppins font-bold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                    <i class="fas fa-align-left text-peri text-sm"></i> Deskripsi Produk
                </h2>
                @if($product->description)
                    <div class="prose-description text-gray-600 dark:text-gray-300 text-sm leading-relaxed">
                        {!! nl2br(e($product->description)) !!}
                    </div>
                @else
                    <p class="text-gray-400 dark:text-gray-500 text-sm italic">Belum ada deskripsi untuk produk ini.</p>
                @endif
            </div>
        </div>

        {{-- Reviews Section --}}
        <div class="mt-12" id="reviews">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm p-6 sm:p-8">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-lg font-poppins font-bold text-gray-900 dark:text-white flex items-center gap-2">
                        <i class="fas fa-star text-amber-400 text-sm"></i> Ulasan ({{ $product->review_count }})
                    </h2>
                    @if($product->review_count > 0)
                        <div class="flex items-center gap-2">
                            <div class="flex items-center gap-0.5">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="fas fa-star text-sm {{ $i <= round($product->average_rating) ? 'text-amber-400' : 'text-gray-300 dark:text-gray-600' }}"></i>
                                @endfor
                            </div>
                            <span class="text-sm font-bold text-gray-900 dark:text-white">{{ $product->average_rating }}</span>
                        </div>
                    @endif
                </div>

                {{-- Review Form --}}
                @auth
                    <div class="mb-8 rounded-xl border border-gray-100 dark:border-white/10 p-4" x-data="{ rating: {{ $userReview?->rating ?? 0 }}, hover: 0 }">
                        <h3 class="text-sm font-bold text-gray-900 dark:text-white mb-3">
                            {{ $userReview ? 'Edit ulasan Anda' : 'Tulis ulasan' }}
                        </h3>
                        <form action="{{ route('reviews.store', $product->slug) }}" method="POST">
                            @csrf
                            {{-- Star Rating --}}
                            <div class="flex items-center gap-1 mb-3">
                                @for($i = 1; $i <= 5; $i++)
                                    <button type="button" @click="rating = {{ $i }}" @mouseenter="hover = {{ $i }}" @mouseleave="hover = 0"
                                            class="text-xl transition-transform hover:scale-110">
                                        <i class="fas fa-star" :class="(hover || rating) >= {{ $i }} ? 'text-amber-400' : 'text-gray-300 dark:text-gray-600'"></i>
                                    </button>
                                @endfor
                                <span class="ml-2 text-xs text-gray-500" x-show="rating > 0" x-text="['','Buruk','Kurang','Cukup','Bagus','Sangat Bagus'][rating]"></span>
                            </div>
                            <input type="hidden" name="rating" :value="rating">
                            <textarea name="comment" rows="3" placeholder="Ceritakan pengalaman Anda dengan produk ini..."
                                      class="w-full rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 px-4 py-3 text-sm text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:border-peri focus:outline-none focus:ring-1 focus:ring-peri resize-none">{{ $userReview?->comment }}</textarea>
                            @error('rating')
                                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                            <div class="flex items-center gap-2 mt-3">
                                <button type="submit" :disabled="rating === 0"
                                        class="px-5 py-2 rounded-xl bg-peri text-white text-sm font-semibold transition hover:bg-peri/90 disabled:opacity-40 disabled:cursor-not-allowed">
                                    <i class="fas fa-paper-plane mr-1"></i> {{ $userReview ? 'Perbarui' : 'Kirim' }} Ulasan
                                </button>
                            </div>
                        </form>
                        @if($userReview)
                            <form action="{{ route('reviews.destroy', $userReview) }}" method="POST" class="mt-2">
                                @csrf @method('DELETE')
                                <button type="submit" class="px-4 py-2 rounded-xl text-sm font-medium text-red-500 hover:bg-red-500/10 transition">
                                    <i class="fas fa-trash-alt mr-1"></i> Hapus
                                </button>
                            </form>
                        @endif
                    </div>
                @else
                    <div class="mb-6 rounded-xl bg-gray-50 dark:bg-white/5 p-4 text-center">
                        <p class="text-sm text-gray-500"><a href="{{ route('login') }}" class="text-peri font-semibold hover:underline">Masuk</a> untuk menulis ulasan.</p>
                    </div>
                @endauth

                {{-- Reviews List --}}
                @if($reviews->count() > 0)
                    <div class="space-y-4">
                        @foreach($reviews as $review)
                            <div class="rounded-xl border border-gray-100 dark:border-white/5 p-4">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-full bg-peri/10 flex items-center justify-center text-peri text-sm font-bold">
                                            {{ strtoupper(substr($review->user->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ $review->user->name }}</span>
                                            <div class="flex items-center gap-1 mt-0.5">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <i class="fas fa-star text-[10px] {{ $i <= $review->rating ? 'text-amber-400' : 'text-gray-300 dark:text-gray-600' }}"></i>
                                                @endfor
                                                <span class="text-[10px] text-gray-400 ml-1">{{ $review->created_at->diffForHumans() }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @if($review->comment)
                                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-300 leading-relaxed">{{ $review->comment }}</p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                    <div class="mt-4">{{ $reviews->fragment('reviews')->links() }}</div>
                @else
                    <div class="text-center py-8">
                        <i class="fas fa-comment-dots text-3xl text-gray-300 dark:text-gray-600 mb-3"></i>
                        <p class="text-sm text-gray-400">Belum ada ulasan. Jadilah yang pertama!</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- Related Products --}}
        @if($related->count())
            <div class="mt-12">
                <h2 class="text-xl font-poppins font-bold text-gray-900 dark:text-white mb-6">Produk Terkait</h2>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    @foreach($related as $relatedProduct)
                        <a href="{{ route('product.show', $relatedProduct->slug) }}"
                           class="group bg-white dark:bg-gray-800 rounded-2xl shadow-sm hover:shadow-md transition-all duration-300 overflow-hidden flex flex-col">

                            {{-- Image --}}
                            <div class="relative h-[195px] overflow-hidden bg-gray-100 dark:bg-white/5">
                                @if($relatedProduct->image_url)
                                    <img src="{{ $relatedProduct->image_url }}" alt="{{ $relatedProduct->name }}"
                                         class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105" loading="lazy">
                                @else
                                    <div class="w-full h-full flex items-center justify-center"
                                         style="background: {{ $relatedProduct->category?->color ?? '#6c63ff' }}20;">
                                        <i class="{{ $relatedProduct->category?->icon ?? 'fas fa-box' }} text-4xl" style="color: {{ $relatedProduct->category?->color ?? '#6c63ff' }};"></i>
                                    </div>
                                @endif
                                @if($relatedProduct->badge)
                                    <span class="absolute top-2 left-2 px-2 py-0.5 rounded-lg text-[10px] font-bold uppercase tracking-wide text-white
                                        {{ $relatedProduct->badge === 'hot' ? 'bg-red-500' : ($relatedProduct->badge === 'sale' ? 'bg-amber-500' : 'bg-emerald-500') }}">
                                        {{ $relatedProduct->badge_label }}
                                    </span>
                                @endif
                            </div>

                            {{-- Info --}}
                            <div class="p-3 flex flex-col flex-1">
                                <h3 class="text-sm font-bold text-gray-900 dark:text-white line-clamp-2 leading-snug">{{ $relatedProduct->name }}</h3>
                                <div class="mt-auto pt-2 flex items-end justify-between gap-1">
                                    <div>
                                        <span class="text-sm font-bold text-peri">{{ $relatedProduct->formatted_effective_price }}</span>
                                        @if($relatedProduct->is_flash_sale)
                                            <span class="block text-[11px] text-gray-400 line-through">{{ $relatedProduct->formatted_price }}</span>
                                        @elseif($relatedProduct->original_price && $relatedProduct->original_price > $relatedProduct->price)
                                            <span class="block text-[11px] text-gray-400 line-through">Rp {{ number_format($relatedProduct->original_price, 0, ',', '.') }}</span>
                                        @endif
                                    </div>
                                    <div class="flex items-center gap-1.5">
                                        <span class="w-2 h-2 rounded-full {{ $relatedProduct->is_in_stock ? 'bg-emerald-400' : 'bg-red-400' }}"></span>
                                        @if($relatedProduct->is_in_stock)
                                            <button onclick="event.preventDefault(); event.stopPropagation(); addToCart({{ $relatedProduct->id }})"
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
            </div>
        @endif

    </div>
</div>
@endsection
