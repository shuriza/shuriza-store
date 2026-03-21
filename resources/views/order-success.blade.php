@extends('layouts.app')

@section('title', 'Pesanan Berhasil - ' . $order->order_number)

@push('styles')
<style>
    @keyframes pulse-check { 0%,100% { transform: scale(1); } 50% { transform: scale(1.15); } }
    @keyframes fade-in-up { from { opacity: 0; transform: translateY(16px); } to { opacity: 1; transform: translateY(0); } }
    .animate-pulse-check { animation: pulse-check 2s ease-in-out infinite; }
    .animate-fade-in { animation: fade-in-up .5s ease both; }
    .animate-fade-in-d1 { animation: fade-in-up .5s ease .15s both; }
    .animate-fade-in-d2 { animation: fade-in-up .5s ease .3s both; }
    .animate-fade-in-d3 { animation: fade-in-up .5s ease .45s both; }

    /* CSS-only confetti */
    @keyframes confetti-fall { 0% { transform: translateY(0) rotate(0deg); opacity: 1; } 100% { transform: translateY(100vh) rotate(720deg); opacity: 0; } }
    .confetti { position: fixed; top: -8px; width: 8px; height: 8px; pointer-events: none; z-index: 50; opacity: 0; animation: confetti-fall var(--d, 3s) ease var(--t, 0s) forwards; }
</style>
@endpush

@section('content')
<div class="min-h-screen py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-2xl mx-auto space-y-6">

        {{-- Confetti --}}
        @php $confettiColors = ['bg-peri','bg-pink-500','bg-green-400','bg-yellow-400','bg-blue-400']; @endphp
        @for ($i = 0; $i < 30; $i++)
            <div class="confetti {{ $confettiColors[$i % 5] }} {{ $i % 3 === 0 ? 'rounded-full' : 'rounded-sm' }}"
                 style="left:{{ $i * 3.3 + rand(0,2) }}%;--d:{{ 2 + ($i % 5) * 0.5 }}s;--t:{{ ($i % 7) * 0.3 }}s;"></div>
        @endfor

        {{-- 1. Success Header --}}
        <div class="text-center animate-fade-in">
            <div class="animate-pulse-check inline-flex items-center justify-center w-20 h-20 rounded-full bg-green-100 dark:bg-green-900/30 mb-4">
                <i class="fas fa-check text-3xl text-green-500"></i>
            </div>
            <h1 class="text-2xl font-bold font-poppins text-gray-900 dark:text-white">Pesanan Berhasil!</h1>
            <p class="mt-2 text-gray-500 dark:text-gray-400">Terima kasih, {{ $order->name }}</p>
        </div>

        {{-- 2. Order Info Card --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm overflow-hidden animate-fade-in-d1">
            <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between flex-wrap gap-3">
                <h2 class="text-sm font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                    <i class="fas fa-receipt text-peri"></i> Detail Pesanan
                </h2>
                <div x-data="{ copied: false }" class="flex items-center gap-2">
                    <code class="text-sm font-mono text-peri bg-peri/10 px-3 py-1 rounded-full">{{ $order->order_number }}</code>
                    <button @click="navigator.clipboard.writeText('{{ $order->order_number }}'); copied = true; setTimeout(() => copied = false, 2000)"
                            class="text-gray-400 hover:text-peri transition" title="Salin nomor order">
                        <i x-show="!copied" class="fas fa-copy text-sm"></i>
                        <i x-show="copied" x-cloak class="fas fa-check text-sm text-green-500"></i>
                    </button>
                </div>
            </div>
            <div class="px-6 py-5 space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-1">Tanggal</p>
                        <p class="text-sm text-gray-900 dark:text-white">{{ $order->created_at->format('d M Y, H:i') }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-1">Status</p>
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold
                            @if($order->status === 'pending') bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400
                            @elseif($order->status === 'processing') bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400
                            @elseif($order->status === 'completed') bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400
                            @else bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400 @endif">
                            <i class="fas fa-circle text-[0.35rem]"></i> {{ $order->status_label }}
                        </span>
                    </div>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-1">Nama</p>
                        <p class="text-sm text-gray-900 dark:text-white">{{ $order->name }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-1">Email</p>
                        <p class="text-sm text-gray-900 dark:text-white">{{ $order->email ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-1">Telepon</p>
                        <p class="text-sm text-gray-900 dark:text-white">{{ $order->phone }}</p>
                    </div>
                </div>

                @if($order->notes)
                <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800/30 rounded-xl p-3">
                    <p class="text-xs font-semibold text-yellow-700 dark:text-yellow-400 mb-1">Catatan</p>
                    <p class="text-sm text-gray-700 dark:text-gray-300">{{ $order->notes }}</p>
                </div>
                @endif
            </div>
        </div>

        {{-- 3. Order Items Card --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm overflow-hidden animate-fade-in-d2">
            <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                <h2 class="text-sm font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                    <i class="fas fa-box text-peri"></i> Daftar Produk
                </h2>
            </div>
            <div class="divide-y divide-gray-100 dark:divide-gray-700">
                @foreach($order->items as $item)
                <div class="px-6 py-4 flex items-center justify-between gap-4">
                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ $item->product->name ?? $item->product_name }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $item->quantity }} &times; Rp {{ number_format($item->price, 0, ',', '.') }}</p>
                    </div>
                    <p class="text-sm font-semibold text-gray-900 dark:text-white whitespace-nowrap">
                        Rp {{ number_format($item->quantity * $item->price, 0, ',', '.') }}
                    </p>
                </div>
                @endforeach
            </div>
            <div class="px-6 py-4 border-t-2 border-gray-200 dark:border-gray-600 flex items-center justify-between">
                <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Pembayaran</span>
                <span class="text-lg font-bold text-peri">{{ $order->formatted_total }}</span>
            </div>
        </div>

        {{-- 4. Action Buttons --}}
        <div class="space-y-3 animate-fade-in-d3">
            @if($order->status === 'pending' && !$order->paid_at && \App\Services\PaymentService::isEnabled())
            <a href="{{ route('payment.pay', $order->order_number) }}"
               class="flex items-center justify-center gap-3 w-full px-6 py-4 bg-peri hover:bg-peri/90 text-white font-bold text-base rounded-2xl shadow-lg shadow-peri/25 transition-all hover:-translate-y-0.5">
                <i class="fas fa-credit-card text-xl"></i>
                Bayar Sekarang
            </a>
            @endif
            <a href="{{ $order->getWhatsAppUrl() }}" target="_blank"
               class="flex items-center justify-center gap-3 w-full px-6 py-4 bg-green-500 hover:bg-green-600 text-white font-bold text-base rounded-2xl shadow-lg shadow-green-500/25 transition-all hover:-translate-y-0.5">
                <i class="fab fa-whatsapp text-xl"></i>
                {{ \App\Services\PaymentService::isEnabled() ? 'Konfirmasi via WhatsApp' : 'Konfirmasi via WhatsApp' }}
            </a>
            @auth
            <a href="{{ route('invoice.show', $order->order_number) }}" target="_blank"
               class="flex items-center justify-center gap-3 w-full px-6 py-3 bg-peri/10 text-peri border border-peri/20 font-semibold text-sm rounded-2xl hover:bg-peri/20 transition">
                <i class="fas fa-file-invoice"></i> Lihat Invoice
            </a>
            @endauth
            <div class="grid {{ auth()->check() ? 'grid-cols-2' : '' }} gap-3">
                <a href="{{ route('products.index') }}"
                   class="flex items-center justify-center gap-2 px-4 py-3 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-700 rounded-2xl text-sm font-semibold hover:border-peri hover:text-peri transition">
                    <i class="fas fa-store"></i> Lanjut Belanja
                </a>
                @auth
                <a href="{{ route('order.history') }}"
                   class="flex items-center justify-center gap-2 px-4 py-3 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-700 rounded-2xl text-sm font-semibold hover:border-peri hover:text-peri transition">
                    <i class="fas fa-receipt"></i> Riwayat Order
                </a>
                @endauth
            </div>
        </div>

    </div>
</div>
@endsection
