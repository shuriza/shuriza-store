@extends('layouts.app')

@section('title', 'Cek Status Pesanan')
@section('description', 'Lacak status pesanan kamu di ' . setting('store_name', 'Shuriza Store Kediri') . ' tanpa perlu login.')

@section('content')
<div class="py-8">
    <div class="max-w-2xl mx-auto px-4 sm:px-6">

        {{-- Breadcrumb --}}
        <nav class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400 mb-8">
            <a href="{{ route('home') }}" class="hover:text-peri transition-colors"><i class="fas fa-home text-xs"></i> Beranda</a>
            <i class="fas fa-chevron-right text-[10px]"></i>
            <span class="text-gray-900 dark:text-white font-medium">Cek Pesanan</span>
        </nav>

        {{-- Header --}}
        <div class="text-center mb-8">
            <div class="w-16 h-16 rounded-2xl bg-peri/10 flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-search text-2xl text-peri"></i>
            </div>
            <h1 class="text-2xl sm:text-3xl font-poppins font-bold text-gray-900 dark:text-white">Cek Status Pesanan</h1>
            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Masukkan nomor order dan nomor HP untuk melacak pesanan kamu</p>
        </div>

        {{-- Form --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700/50 p-6 mb-8">
            <form method="POST" action="{{ route('order.track.submit') }}">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">
                            <i class="fas fa-receipt text-peri mr-1"></i> Nomor Order
                        </label>
                        <input type="text" name="order_number" value="{{ old('order_number') }}"
                               placeholder="Contoh: SHR-260321-A1B2C"
                               class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-peri/50 focus:border-peri outline-none transition-all uppercase"
                               required>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">
                            <i class="fab fa-whatsapp text-green-500 mr-1"></i> Nomor HP / WhatsApp
                        </label>
                        <input type="text" name="phone" value="{{ old('phone') }}"
                               placeholder="Contoh: 081234567890"
                               class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-peri/50 focus:border-peri outline-none transition-all"
                               required>
                    </div>
                    <button type="submit"
                            class="w-full py-3 rounded-xl bg-peri text-white font-semibold text-sm hover:bg-peri-dark transition-colors flex items-center justify-center gap-2">
                        <i class="fas fa-search text-xs"></i> Lacak Pesanan
                    </button>
                </div>
            </form>
        </div>

        {{-- Result --}}
        @isset($order)
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700/50 overflow-hidden">
            {{-- Order Header --}}
            <div class="p-6 border-b border-gray-100 dark:border-gray-700/50">
                <div class="flex items-center justify-between flex-wrap gap-3">
                    <div>
                        <h2 class="text-lg font-bold text-gray-900 dark:text-white">{{ $order->order_number }}</h2>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">
                            <i class="far fa-calendar mr-1"></i> {{ $order->created_at->translatedFormat('d F Y, H:i') }} WIB
                        </p>
                    </div>
                    <div>
                        @php
                            $statusColors = [
                                'pending'    => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400',
                                'processing' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400',
                                'completed'  => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400',
                                'cancelled'  => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400',
                            ];
                            $statusIcons = [
                                'pending'    => 'fas fa-clock',
                                'processing' => 'fas fa-spinner',
                                'completed'  => 'fas fa-check-circle',
                                'cancelled'  => 'fas fa-times-circle',
                            ];
                        @endphp
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold {{ $statusColors[$order->status] ?? 'bg-gray-100 text-gray-800' }}">
                            <i class="{{ $statusIcons[$order->status] ?? 'fas fa-circle' }} text-[10px]"></i>
                            {{ $order->status_label }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- Status Timeline --}}
            <div class="p-6 border-b border-gray-100 dark:border-gray-700/50">
                <h3 class="text-sm font-bold text-gray-900 dark:text-white mb-4">Status Pesanan</h3>
                @php
                    $steps = [
                        ['key' => 'pending', 'label' => 'Menunggu', 'icon' => 'fas fa-clock', 'desc' => 'Pesanan dibuat'],
                        ['key' => 'processing', 'label' => 'Diproses', 'icon' => 'fas fa-cog', 'desc' => 'Sedang diproses admin'],
                        ['key' => 'completed', 'label' => 'Selesai', 'icon' => 'fas fa-check', 'desc' => 'Produk telah dikirim'],
                    ];
                    $statusOrder = ['pending' => 0, 'processing' => 1, 'completed' => 2, 'cancelled' => -1];
                    $currentStep = $statusOrder[$order->status] ?? 0;
                    $isCancelled = $order->status === 'cancelled';
                @endphp
                @if($isCancelled)
                    <div class="flex items-center gap-3 p-4 rounded-xl bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800/30">
                        <div class="w-10 h-10 rounded-full bg-red-500 flex items-center justify-center shrink-0">
                            <i class="fas fa-times text-white"></i>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-red-700 dark:text-red-400">Pesanan Dibatalkan</p>
                            <p class="text-xs text-red-600/70 dark:text-red-400/70 mt-0.5">Pesanan ini telah dibatalkan.</p>
                        </div>
                    </div>
                @else
                    <div class="flex items-center gap-0">
                        @foreach($steps as $i => $step)
                            @php $done = $currentStep >= $i; @endphp
                            <div class="flex-1 flex flex-col items-center text-center">
                                <div class="w-10 h-10 rounded-full flex items-center justify-center text-sm font-bold transition-all
                                    {{ $done ? 'bg-peri text-white shadow-md shadow-peri/30' : 'bg-gray-100 dark:bg-gray-700 text-gray-400' }}">
                                    <i class="{{ $step['icon'] }}"></i>
                                </div>
                                <p class="mt-2 text-xs font-semibold {{ $done ? 'text-peri' : 'text-gray-400' }}">{{ $step['label'] }}</p>
                                <p class="text-[10px] text-gray-400 mt-0.5">{{ $step['desc'] }}</p>
                            </div>
                            @if($i < count($steps) - 1)
                                <div class="flex-1 h-0.5 rounded-full mt-[-30px] {{ $currentStep > $i ? 'bg-peri' : 'bg-gray-200 dark:bg-gray-700' }}"></div>
                            @endif
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Order Items --}}
            <div class="p-6 border-b border-gray-100 dark:border-gray-700/50">
                <h3 class="text-sm font-bold text-gray-900 dark:text-white mb-3">Detail Pesanan</h3>
                <div class="space-y-3">
                    @foreach($order->items as $item)
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-xl bg-gray-100 dark:bg-gray-700 flex items-center justify-center shrink-0 overflow-hidden">
                                @if($item->product && $item->product->image_url)
                                    <img src="{{ $item->product->image_url }}" alt="{{ $item->product_name }}" class="w-full h-full object-cover">
                                @else
                                    <i class="fas fa-box text-gray-400 text-sm"></i>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold text-gray-900 dark:text-white truncate">{{ $item->product_name }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $item->quantity }}x {{ $item->formatted_price }}</p>
                            </div>
                            <span class="text-sm font-bold text-gray-900 dark:text-white shrink-0">{{ $item->formatted_subtotal }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Total --}}
            <div class="p-6">
                @if($order->discount_amount > 0)
                    <div class="flex justify-between text-sm mb-2">
                        <span class="text-gray-500 dark:text-gray-400">Subtotal</span>
                        <span class="text-gray-900 dark:text-white">Rp {{ number_format($order->total + $order->discount_amount, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-sm mb-2">
                        <span class="text-green-600 dark:text-green-400">Diskon ({{ $order->coupon_code }})</span>
                        <span class="text-green-600 dark:text-green-400">-Rp {{ number_format($order->discount_amount, 0, ',', '.') }}</span>
                    </div>
                @endif
                <div class="flex justify-between items-center pt-3 border-t border-gray-100 dark:border-gray-700/50">
                    <span class="text-sm font-bold text-gray-900 dark:text-white">Total</span>
                    <span class="text-lg font-bold text-peri">{{ $order->formatted_total }}</span>
                </div>

                {{-- Actions --}}
                <div class="flex flex-col sm:flex-row gap-3 mt-6">
                    <a href="{{ route('order.whatsapp', $order->order_number) }}"
                       class="flex-1 py-3 rounded-xl bg-green-500 text-white font-semibold text-sm hover:bg-green-600 transition-colors flex items-center justify-center gap-2"
                       target="_blank">
                        <i class="fab fa-whatsapp"></i> Hubungi Admin
                    </a>
                    <a href="{{ route('order.track') }}"
                       class="flex-1 py-3 rounded-xl border border-gray-200 dark:border-gray-600 text-gray-700 dark:text-gray-300 font-semibold text-sm hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors flex items-center justify-center gap-2">
                        <i class="fas fa-search text-xs"></i> Cek Order Lain
                    </a>
                </div>
            </div>
        </div>
        @endisset

        {{-- Info --}}
        @empty($order)
        <div class="text-center text-sm text-gray-500 dark:text-gray-400">
            <p>Nomor order bisa ditemukan di halaman sukses setelah checkout atau di pesan WhatsApp konfirmasi.</p>
            <p class="mt-2">Butuh bantuan? <a href="https://wa.me/{{ setting('whatsapp_number', '6281234567890') }}" class="text-peri hover:underline" target="_blank">Chat Admin</a></p>
        </div>
        @endempty

    </div>
</div>
@endsection
