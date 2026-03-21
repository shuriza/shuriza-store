@extends('layouts.app')

@section('title', 'Pembayaran - ' . $order->order_number)

@push('styles')
<style>
    @keyframes pulse-ring { 0% { transform: scale(0.9); opacity: 1; } 100% { transform: scale(1.5); opacity: 0; } }
    .pulse-ring::before { content: ''; position: absolute; inset: -8px; border-radius: 9999px; border: 3px solid rgba(108, 99, 255, 0.4); animation: pulse-ring 1.5s ease-out infinite; }
</style>
@endpush

@section('content')
<div class="min-h-screen py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-lg mx-auto space-y-6">

        {{-- Header --}}
        <div class="text-center">
            <div class="relative inline-flex items-center justify-center w-20 h-20 rounded-full bg-peri/10 mb-4 pulse-ring">
                <i class="fas fa-credit-card text-3xl text-peri"></i>
            </div>
            <h1 class="text-2xl font-bold font-poppins text-gray-900 dark:text-white">Pembayaran</h1>
            <p class="mt-2 text-gray-500 dark:text-gray-400">Order <span class="font-mono font-semibold text-peri">{{ $order->order_number }}</span></p>
        </div>

        {{-- Order Summary --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                <h2 class="text-sm font-bold text-gray-900 dark:text-white flex items-center gap-2">
                    <i class="fas fa-receipt text-peri"></i> Ringkasan Pesanan
                </h2>
            </div>
            <div class="px-6 py-4 space-y-3">
                @foreach($order->items as $item)
                <div class="flex justify-between items-center text-sm">
                    <div class="min-w-0">
                        <p class="font-medium text-gray-900 dark:text-white truncate">{{ $item->product_name }}</p>
                        <p class="text-xs text-gray-500">{{ $item->quantity }} × Rp {{ number_format($item->price, 0, ',', '.') }}</p>
                    </div>
                    <span class="font-semibold text-gray-900 dark:text-white flex-shrink-0 ml-4">
                        Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                    </span>
                </div>
                @endforeach

                @if($order->discount_amount > 0)
                <div class="flex justify-between items-center text-sm pt-2 border-t border-gray-100 dark:border-gray-700">
                    <span class="text-green-500 flex items-center gap-1">
                        <i class="fas fa-tag"></i> Diskon {{ $order->coupon_code }}
                    </span>
                    <span class="font-semibold text-green-500">-Rp {{ number_format($order->discount_amount, 0, ',', '.') }}</span>
                </div>
                @endif
            </div>
            <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-700 flex justify-between items-center">
                <span class="font-bold text-gray-900 dark:text-white">Total Bayar</span>
                <span class="text-xl font-bold text-peri">Rp {{ number_format($order->total, 0, ',', '.') }}</span>
            </div>
        </div>

        {{-- Payment Button --}}
        <div class="space-y-3">
            <button id="pay-button"
                    class="flex items-center justify-center gap-3 w-full px-6 py-4 bg-peri hover:bg-peri/90 text-white font-bold text-base rounded-2xl shadow-lg shadow-peri/25 transition-all hover:-translate-y-0.5">
                <i class="fas fa-lock"></i> Bayar Sekarang
            </button>

            <a href="{{ route('order.success', $order->order_number) }}"
               class="flex items-center justify-center gap-2 w-full px-4 py-3 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-700 rounded-2xl text-sm font-semibold hover:border-peri hover:text-peri transition">
                <i class="fas fa-arrow-left"></i> Bayar Nanti / Manual via WhatsApp
            </a>
        </div>

        {{-- Security Badge --}}
        <div class="text-center">
            <div class="inline-flex items-center gap-2 px-4 py-2 bg-green-50 dark:bg-green-900/20 rounded-full">
                <i class="fas fa-shield-alt text-green-500 text-xs"></i>
                <span class="text-xs text-green-700 dark:text-green-400 font-medium">Pembayaran dijamin aman oleh {{ ucfirst($provider) }}</span>
            </div>
        </div>

    </div>
</div>
@endsection

@push('scripts')
@if($provider === 'midtrans' && $order->payment_token)
<script src="{{ $snapJsUrl }}" data-client-key="{{ $clientKey }}"></script>
<script>
    document.getElementById('pay-button').addEventListener('click', function () {
        this.disabled = true;
        this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Memproses...';

        window.snap.pay('{{ $order->payment_token }}', {
            onSuccess: function(result) {
                window.location.href = '{{ route('payment.finish', $order->order_number) }}?status=success';
            },
            onPending: function(result) {
                window.location.href = '{{ route('payment.finish', $order->order_number) }}?status=pending';
            },
            onError: function(result) {
                alert('Pembayaran gagal. Silakan coba lagi.');
                window.location.reload();
            },
            onClose: function() {
                document.getElementById('pay-button').disabled = false;
                document.getElementById('pay-button').innerHTML = '<i class="fas fa-lock"></i> Bayar Sekarang';
            }
        });
    });
</script>
@endif
@endpush
