@extends('layouts.app')

@section('title', 'Keranjang Belanja')

@push('styles')
<style>
    [x-cloak] { display: none !important; }
</style>
@endpush

@section('content')
<div class="mx-auto max-w-6xl px-4 py-8 sm:px-6 lg:px-8">

    {{-- Breadcrumb --}}
    <nav class="mb-2 flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400">
        <a href="{{ route('home') }}" class="hover:text-peri transition">Beranda</a>
        <i class="fas fa-chevron-right text-[.6rem]"></i>
        <span class="text-gray-900 dark:text-white">Keranjang</span>
    </nav>

    {{-- Page Title --}}
    <h1 class="mb-6 flex items-center gap-3 text-2xl font-extrabold text-gray-900 dark:text-white">
        <i class="fas fa-shopping-cart text-peri"></i>
        Keranjang Belanja
        @if($items->count())
            <span class="text-base font-normal text-gray-500 dark:text-gray-400">({{ $items->count() }} item)</span>
        @endif
    </h1>

    @if($items->isEmpty())
        {{-- Empty State --}}
        <div data-cart-empty class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm flex flex-col items-center justify-center px-6 py-20 text-center">
            <div class="mb-4 text-6xl text-gray-300 dark:text-gray-600">
                <i class="fas fa-shopping-cart"></i>
            </div>
            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-1">Keranjang Kosong</h3>
            <p class="text-gray-500 dark:text-gray-400 mb-6">Belum ada produk di keranjang. Yuk mulai belanja!</p>
            <a href="{{ route('products.index') }}" class="inline-flex items-center gap-2 rounded-xl bg-peri px-6 py-3 text-sm font-semibold text-white shadow hover:bg-peri-dark transition">
                <i class="fas fa-shopping-bag"></i> Mulai Belanja
            </a>
        </div>
    @else
        {{-- Two-column layout --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">

            {{-- Left Column — Cart Items --}}
            <div class="lg:col-span-2 space-y-4">
                @foreach($items as $item)
                    @php
                        $product  = $item->product;
                        $subtotal = $item->quantity * $product->effective_price;
                    @endphp
                    <div
                        data-cart-item="{{ $item->id }}"
                        data-product-id="{{ $product->id }}"
                        data-item-name="{{ $product->name }}"
                        data-item-price="{{ $product->effective_price }}"
                        data-item-quantity="{{ $item->quantity }}"
                        data-item-image="{{ $product->image_url }}"
                        class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm p-4 sm:p-5 flex flex-col sm:flex-row gap-4 items-start sm:items-center transition hover:shadow-md"
                    >
                        {{-- Product Image --}}
                        <a href="{{ route('products.show', $product->slug) }}" class="shrink-0">
                            @if($product->image_url)
                                <img src="{{ $product->image_url }}" alt="{{ $product->name }}"
                                     class="h-20 w-20 rounded-xl object-cover bg-gray-100 dark:bg-gray-700">
                            @else
                                <div class="h-20 w-20 rounded-xl bg-gray-100 dark:bg-gray-700 flex items-center justify-center text-2xl text-gray-400 dark:text-gray-500">
                                    <i class="fas fa-box"></i>
                                </div>
                            @endif
                        </a>

                        {{-- Details --}}
                        <div class="flex-1 min-w-0">
                            <a href="{{ route('products.show', $product->slug) }}" class="block text-sm font-bold text-gray-900 dark:text-white truncate hover:text-peri transition">
                                {{ $product->name }}
                            </a>
                            <p class="mt-0.5 text-sm text-gray-500 dark:text-gray-400">{{ $product->formatted_effective_price }} / item</p>
                            @if(!$product->is_in_stock)
                                <p class="mt-1 text-xs text-red-500 flex items-center gap-1">
                                    <i class="fas fa-times-circle"></i> Stok habis
                                </p>
                            @elseif($product->stock <= 5)
                                <p class="mt-1 text-xs text-amber-500 flex items-center gap-1">
                                    <i class="fas fa-exclamation-triangle"></i> Stok tersisa {{ $product->stock }}
                                </p>
                            @endif
                        </div>

                        {{-- Qty + Subtotal + Remove --}}
                        <div class="flex items-center gap-4 sm:gap-6 flex-wrap sm:flex-nowrap">
                            {{-- Quantity Control --}}
                            <form method="POST" action="{{ route('cart.update', $item->id) }}" class="flex items-center gap-1.5" x-data="{ qty: {{ $item->quantity }} }">
                                @csrf @method('PATCH')
                                <input type="hidden" name="quantity" :value="qty">
                                <button type="button" @click="qty = Math.max(1, qty - 1); $el.closest('form').submit()"
                                        class="h-8 w-8 rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 flex items-center justify-center text-xs hover:bg-peri hover:text-white transition disabled:opacity-40"
                                        :disabled="qty <= 1">
                                    <i class="fas fa-minus"></i>
                                </button>
                                <span class="w-9 text-center text-sm font-semibold text-gray-900 dark:text-white select-none" x-text="qty">{{ $item->quantity }}</span>
                                <button type="button" @click="qty++; $el.closest('form').submit()"
                                        class="h-8 w-8 rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 flex items-center justify-center text-xs hover:bg-peri hover:text-white transition">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </form>

                            {{-- Line Total --}}
                            <span class="min-w-[100px] text-right text-sm font-bold text-gray-900 dark:text-white">
                                Rp {{ number_format($subtotal, 0, ',', '.') }}
                            </span>

                            {{-- Remove --}}
                            <form method="POST" action="{{ route('cart.remove', $item->id) }}">
                                @csrf @method('DELETE')
                                <button type="submit" data-remove-item="{{ $item->id }}" title="Hapus"
                                        class="h-8 w-8 rounded-lg text-gray-400 hover:bg-red-50 hover:text-red-500 dark:hover:bg-red-500/10 flex items-center justify-center transition">
                                    <i class="fas fa-trash-alt text-sm"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Right Column — Order Summary --}}
            <div class="lg:col-span-1">
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm p-5 sticky top-24 space-y-4">
                    <h2 class="flex items-center gap-2 text-base font-bold text-gray-900 dark:text-white">
                        <i class="fas fa-receipt text-peri"></i> Ringkasan Pesanan
                    </h2>

                    {{-- Subtotal --}}
                    <div class="flex justify-between text-sm text-gray-500 dark:text-gray-400">
                        <span>Subtotal ({{ $items->count() }} item)</span>
                        <span data-cart-total class="font-semibold text-gray-900 dark:text-white">Rp {{ number_format($total, 0, ',', '.') }}</span>
                    </div>

                    <hr class="border-gray-200 dark:border-gray-700">

                    {{-- Total --}}
                    <div class="flex justify-between items-center">
                        <span class="font-bold text-gray-900 dark:text-white">Total</span>
                        <span class="text-lg font-extrabold text-peri">Rp {{ number_format($total, 0, ',', '.') }}</span>
                    </div>

                    {{-- Checkout --}}
                    <a href="{{ route('order.checkout') }}"
                       class="mt-2 flex w-full items-center justify-center gap-2 rounded-xl bg-peri px-5 py-3 text-sm font-bold text-white shadow-lg shadow-peri/25 hover:bg-peri-dark transition">
                        <i class="fas fa-credit-card"></i> Lanjut Checkout
                    </a>

                    {{-- Continue Shopping --}}
                    <a href="{{ route('products.index') }}"
                       class="flex w-full items-center justify-center gap-2 rounded-xl border border-gray-200 dark:border-gray-700 px-5 py-2.5 text-sm font-medium text-gray-500 dark:text-gray-400 hover:text-peri dark:hover:text-peri hover:border-peri/30 transition">
                        <i class="fas fa-arrow-left"></i> Lanjut Belanja
                    </a>
                </div>
            </div>

        </div>
    @endif

</div>
@endsection
