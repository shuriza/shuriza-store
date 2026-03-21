@extends('layouts.app')

@section('title', 'Riwayat Pesanan')

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Breadcrumb --}}
        <nav class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400 mb-6">
            <a href="{{ url('/') }}" class="hover:text-peri transition">Beranda</a>
            <i class="fas fa-chevron-right text-xs"></i>
            <span class="text-gray-900 dark:text-white font-medium">Riwayat Pesanan</span>
        </nav>

        {{-- Page Header --}}
        <div class="mb-6">
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white">Riwayat Pesanan</h1>
            <p class="mt-1 text-gray-500 dark:text-gray-400">{{ $orders->total() }} pesanan ditemukan</p>
        </div>

        {{-- Filters --}}
        <div class="mb-6 space-y-4">
            {{-- Search --}}
            <form action="{{ route('order.history') }}" method="GET" class="flex gap-2">
                @if(!empty($status))<input type="hidden" name="status" value="{{ $status }}">@endif
                <div class="relative flex-1">
                    <i class="fas fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                    <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Cari nomor pesanan..."
                           class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-peri/30 focus:border-peri transition">
                </div>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-peri text-white text-sm font-semibold hover:bg-peri-dark transition">
                    Cari
                </button>
            </form>

            {{-- Status Tabs --}}
            <div class="flex flex-wrap gap-2">
                @php
                    $statuses = [
                        ''           => ['label' => 'Semua',       'icon' => 'fas fa-list'],
                        'pending'    => ['label' => 'Pending',     'icon' => 'fas fa-clock'],
                        'processing' => ['label' => 'Diproses',    'icon' => 'fas fa-spinner'],
                        'completed'  => ['label' => 'Selesai',     'icon' => 'fas fa-check-circle'],
                        'cancelled'  => ['label' => 'Dibatalkan',  'icon' => 'fas fa-times-circle'],
                    ];
                @endphp
                @foreach($statuses as $key => $info)
                    <a href="{{ route('order.history', array_filter(['status' => $key, 'search' => $search ?? ''])) }}"
                       class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-sm font-medium transition
                              {{ ($status ?? '') === $key ? 'bg-peri text-white shadow-lg shadow-peri/25' : 'bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 border border-gray-200 dark:border-gray-700 hover:border-peri/30 hover:text-peri' }}">
                        <i class="{{ $info['icon'] }} text-xs"></i>
                        {{ $info['label'] }}
                    </a>
                @endforeach
            </div>
        </div>

        @if($orders->count())
            {{-- Order Cards --}}
            <div class="space-y-4">
                @foreach($orders as $order)
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm p-5 sm:p-6">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                            {{-- Left: Order info --}}
                            <div class="space-y-2">
                                <div class="flex flex-wrap items-center gap-3">
                                    <span class="font-mono text-sm font-semibold text-gray-900 dark:text-white">{{ $order->order_number }}</span>
                                    @switch($order->status)
                                        @case('pending')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400">{{ $order->status_label }}</span>
                                            @break
                                        @case('processing')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400">{{ $order->status_label }}</span>
                                            @break
                                        @case('completed')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">{{ $order->status_label }}</span>
                                            @break
                                        @case('cancelled')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400">{{ $order->status_label }}</span>
                                            @break
                                    @endswitch
                                </div>
                                <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-sm text-gray-500 dark:text-gray-400">
                                    <span><i class="far fa-calendar-alt mr-1"></i>{{ $order->created_at->format('d M Y, H:i') }}</span>
                                    <span><i class="fas fa-box mr-1"></i>{{ $order->items->count() }} item</span>
                                </div>
                            </div>

                            {{-- Right: Price + action --}}
                            <div class="flex items-center justify-between sm:justify-end gap-3">
                                <span class="text-peri font-bold text-lg">{{ $order->formatted_total }}</span>
                                @if($order->status === 'pending' && !$order->paid_at && \App\Services\PaymentService::isEnabled())
                                <a href="{{ route('payment.pay', $order->order_number) }}"
                                   class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-medium rounded-xl bg-peri text-white hover:bg-peri/90 shadow-lg shadow-peri/25 transition">
                                    <i class="fas fa-credit-card text-xs"></i>
                                    Bayar
                                </a>
                                @endif
                                <a href="{{ route('order.show', $order->order_number) }}"
                                   class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-medium rounded-xl bg-peri/10 text-peri hover:bg-peri hover:text-white transition">
                                    Detail
                                    <i class="fas fa-arrow-right text-xs"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Pagination --}}
            <div class="mt-8">
                {{ $orders->links() }}
            </div>
        @else
            {{-- Empty State --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm py-16 px-6 text-center">
                <div class="flex justify-center mb-4">
                    <div class="w-20 h-20 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center">
                        <i class="fas fa-shopping-bag text-3xl text-gray-400 dark:text-gray-500"></i>
                    </div>
                </div>
                @if(!empty($search) || !empty($status))
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Tidak ada pesanan ditemukan</h2>
                    <p class="text-gray-500 dark:text-gray-400 mb-6">Coba ubah filter atau kata kunci pencarian.</p>
                    <a href="{{ route('order.history') }}"
                       class="inline-flex items-center gap-2 px-6 py-2.5 bg-peri text-white font-medium rounded-xl hover:bg-peri/90 transition">
                        <i class="fas fa-redo"></i>
                        Reset Filter
                    </a>
                @else
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Belum ada pesanan</h2>
                    <p class="text-gray-500 dark:text-gray-400 mb-6">Ayo mulai belanja dan temukan produk favoritmu!</p>
                    <a href="{{ route('products.index') }}"
                       class="inline-flex items-center gap-2 px-6 py-2.5 bg-peri text-white font-medium rounded-xl hover:bg-peri/90 transition">
                        <i class="fas fa-store"></i>
                        Lihat Produk
                    </a>
                @endif
            </div>
        @endif

    </div>
</div>
@endsection
