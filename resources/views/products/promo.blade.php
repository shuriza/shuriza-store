@extends('layouts.app')

@section('title', 'Promo & Diskon')
@section('description', 'Dapatkan produk digital dengan harga diskon spesial di ' . setting('store_name', 'Shuriza Store Kediri') . '.')

@push('styles')
<style>
    .hide-scrollbar::-webkit-scrollbar { display: none; }
    .hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
</style>
@endpush

@section('content')

{{-- Page Header --}}
<div class="bg-gradient-to-br from-red-500/10 via-orange-500/5 to-yellow-500/10 dark:from-red-900/20 dark:via-orange-900/10 dark:to-yellow-900/20 border-b border-red-200/50 dark:border-red-800/20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <nav class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400 mb-4">
            <a href="{{ route('home') }}" class="hover:text-peri transition-colors">Beranda</a>
            <i class="fas fa-chevron-right text-[10px]"></i>
            <span class="text-gray-900 dark:text-white font-medium">Promo & Diskon</span>
        </nav>
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 rounded-2xl bg-red-500 flex items-center justify-center shadow-lg shadow-red-500/30">
                <i class="fas fa-percent text-2xl text-white"></i>
            </div>
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold font-poppins text-gray-900 dark:text-white">Promo & Diskon</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $products->total() }} produk sedang diskon</p>
            </div>
        </div>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-10">

    {{-- Flash Sale Section --}}
    @if($flashSaleProducts->count() > 0)
    <section x-data="promoFlashTimer()" x-init="init()">
        <div class="bg-gradient-to-r from-red-500/10 to-orange-500/10 dark:from-red-900/20 dark:to-orange-900/20 rounded-2xl border border-red-200 dark:border-red-800/30 p-5">
            <div class="flex items-center justify-between flex-wrap gap-3 mb-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-red-500 flex items-center justify-center animate-pulse">
                        <i class="fas fa-bolt text-white text-lg"></i>
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-gray-900 dark:text-white">Flash Sale</h2>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Berakhir dalam:</p>
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
                        <x-product-image :product="$fp" />
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

    {{-- All Discounted Products --}}
    <section>
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-poppins font-bold text-gray-900 dark:text-white">
                <i class="fas fa-tags text-amber-500 mr-1"></i> Semua Produk Diskon
            </h2>
            <div>
                <select onchange="window.location.href=this.value"
                        class="px-3 py-2 rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-peri/50 outline-none">
                    <option value="{{ route('products.promo') }}" {{ !request('sort') ? 'selected' : '' }}>Default</option>
                    <option value="{{ route('products.promo', ['sort' => 'price-asc']) }}" {{ request('sort') === 'price-asc' ? 'selected' : '' }}>Harga Terendah</option>
                    <option value="{{ route('products.promo', ['sort' => 'price-desc']) }}" {{ request('sort') === 'price-desc' ? 'selected' : '' }}>Harga Tertinggi</option>
                    <option value="{{ route('products.promo', ['sort' => 'newest']) }}" {{ request('sort') === 'newest' ? 'selected' : '' }}>Terbaru</option>
                    <option value="{{ route('products.promo', ['sort' => 'popular']) }}" {{ request('sort') === 'popular' ? 'selected' : '' }}>Terpopuler</option>
                </select>
            </div>
        </div>

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
                            @if($product->discount_percent)
                                <span class="absolute top-2 right-2 px-2 py-0.5 rounded-lg text-[10px] font-bold text-white bg-red-500">
                                    -{{ $product->discount_percent }}%
                                </span>
                            @elseif($product->is_flash_sale && $product->flash_sale_percent)
                                <span class="absolute top-2 right-2 px-2 py-0.5 rounded-lg text-[10px] font-bold text-white bg-red-500">
                                    -{{ $product->flash_sale_percent }}%
                                </span>
                            @endif
                        </div>
                        <div class="p-3 flex flex-col flex-1">
                            <h3 class="text-sm font-bold text-gray-900 dark:text-white line-clamp-2 leading-snug">{{ $product->name }}</h3>
                            <div class="mt-auto pt-2 flex items-end justify-between gap-1">
                                <div>
                                    <span class="text-sm font-bold text-red-500">{{ $product->formatted_effective_price }}</span>
                                    @if($product->is_flash_sale)
                                        <span class="block text-[11px] text-gray-400 line-through">{{ $product->formatted_price }}</span>
                                    @elseif($product->original_price && $product->original_price > $product->price)
                                        <span class="block text-[11px] text-gray-400 line-through">Rp {{ number_format($product->original_price, 0, ',', '.') }}</span>
                                    @endif
                                </div>
                                @if($product->is_in_stock)
                                    <button onclick="event.preventDefault(); event.stopPropagation(); addToCart({{ $product->id }})"
                                            class="w-8 h-8 rounded-full bg-peri/10 text-peri hover:bg-peri hover:text-white flex items-center justify-center transition-all duration-200"
                                            title="Tambah ke keranjang">
                                        <i class="fas fa-cart-plus text-xs"></i>
                                    </button>
                                @endif
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
            <div class="mt-8">{{ $products->links() }}</div>
        @else
            <div class="text-center py-20">
                <div class="w-20 h-20 rounded-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center mx-auto mb-5">
                    <i class="fas fa-tag text-3xl text-gray-300 dark:text-gray-600"></i>
                </div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Belum Ada Promo</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">Nantikan promo menarik dari kami!</p>
                <a href="{{ route('products.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-peri text-white text-sm font-semibold hover:bg-peri/90 transition-colors">
                    <i class="fas fa-arrow-left text-xs"></i> Lihat Semua Produk
                </a>
            </div>
        @endif
    </section>
</div>
@endsection

@push('scripts')
<script>
function promoFlashTimer() {
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
            this.hours = String(Math.floor(diff / 3600000)).padStart(2, '0');
            this.minutes = String(Math.floor((diff % 3600000) / 60000)).padStart(2, '0');
            this.seconds = String(Math.floor((diff % 60000) / 1000)).padStart(2, '0');
        }
    };
}
</script>
@endpush
