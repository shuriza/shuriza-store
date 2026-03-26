@extends('layouts.app')

@section('title', 'Detail Pesanan - ' . $order->order_number)

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-8">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Breadcrumb --}}
        <nav class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400 mb-6">
            <a href="{{ url('/') }}" class="hover:text-peri transition">Beranda</a>
            <i class="fas fa-chevron-right text-xs"></i>
            <a href="{{ route('order.history') }}" class="hover:text-peri transition">Riwayat Pesanan</a>
            <i class="fas fa-chevron-right text-xs"></i>
            <span class="text-gray-900 dark:text-white font-medium">Detail Pesanan</span>
        </nav>

        {{-- Page Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white">Detail Pesanan</h1>
                <p class="mt-1 text-gray-500 dark:text-gray-400">
                    No. Order: <span class="font-mono font-semibold text-gray-900 dark:text-white">{{ $order->order_number }}</span>
                </p>
            </div>
            <div class="flex-shrink-0">
                @switch($order->status)
                    @case('pending')
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400">{{ $order->status_label }}</span>
                        @break
                    @case('processing')
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400">{{ $order->status_label }}</span>
                        @break
                    @case('completed')
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">{{ $order->status_label }}</span>
                        @break
                    @case('cancelled')
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400">{{ $order->status_label }}</span>
                        @break
                @endswitch
            </div>
        </div>

        {{-- Two-column layout --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- Left Column — Order Items --}}
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm">
                    <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h2 class="text-sm font-bold uppercase tracking-wide text-gray-900 dark:text-white">Item Pesanan</h2>
                    </div>
                    <div class="p-5 space-y-4">
                        @foreach($order->items as $item)
                            <div class="flex items-center justify-between gap-4 {{ !$loop->last ? 'pb-4 border-b border-gray-100 dark:border-gray-700/50' : '' }}">
                                <div class="flex items-center gap-3 min-w-0">
                                    @if($item->product && $item->product->image_url)
                                        <img src="{{ $item->product->image_url }}" alt="{{ $item->product->name }}" class="w-[60px] h-[60px] rounded-lg object-cover flex-shrink-0">
                                    @else
                                        <div class="w-[60px] h-[60px] rounded-lg bg-gray-100 dark:bg-gray-700 flex items-center justify-center flex-shrink-0">
                                            <i class="fas fa-box text-gray-400 dark:text-gray-500"></i>
                                        </div>
                                    @endif
                                    <div class="min-w-0">
                                        @if($item->product)
                                            <a href="{{ route('product.show', $item->product->slug) }}" class="text-sm font-semibold text-gray-900 dark:text-white hover:text-peri transition truncate block">{{ $item->product->name }}</a>
                                        @else
                                            <span class="text-sm font-semibold text-gray-900 dark:text-white truncate block">{{ $item->product_name ?? 'Produk dihapus' }}</span>
                                        @endif
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ $item->quantity }} × Rp {{ number_format($item->price, 0, ',', '.') }}</p>
                                    </div>
                                </div>
                                <span class="text-sm font-semibold text-gray-900 dark:text-white flex-shrink-0">Rp {{ number_format($item->quantity * $item->price, 0, ',', '.') }}</span>
                            </div>
                            @if($item->delivery_data)
                                @php
                                    $isDelivered = $item->delivery_status === 'delivered';
                                @endphp
                                <div class="ml-[72px] mt-2 p-3 rounded-xl {{ $isDelivered ? 'bg-green-50 dark:bg-green-900/20 border-green-200 dark:border-green-800/40' : 'bg-yellow-50 dark:bg-yellow-900/20 border-yellow-200 dark:border-yellow-800/40' }} border">
                                    <div class="flex items-center gap-2 mb-1">
                                        <i class="fas {{ $isDelivered ? 'fa-check-circle text-green-500' : 'fa-clock text-yellow-500' }} text-xs"></i>
                                        <span class="text-xs font-semibold {{ $isDelivered ? 'text-green-700 dark:text-green-400' : 'text-yellow-700 dark:text-yellow-400' }}">
                                            {{ $isDelivered ? 'Dikirim' : 'Sedang diproses' }}
                                            @if($item->delivered_at)
                                                {{ $item->delivered_at->format('d M Y, H:i') }}
                                            @endif
                                        </span>
                                    </div>
                                    <div class="text-sm text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 rounded-lg p-2 font-mono text-xs break-all">
                                        {{ $item->delivery_data }}
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                    <div class="px-5 py-4 border-t border-gray-200 dark:border-gray-700 flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Pembayaran</span>
                        <span class="text-lg font-bold text-peri">{{ $order->formatted_total }}</span>
                    </div>
                </div>

                {{-- Status Timeline --}}
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm">
                    <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h2 class="text-sm font-bold uppercase tracking-wide text-gray-900 dark:text-white">Status Pesanan</h2>
                    </div>
                    <div class="p-5">
                        @php
                            $statusFlow = ['pending', 'processing', 'completed'];
                            $isCancelled = $order->status === 'cancelled';
                            $currentIndex = $isCancelled ? -1 : array_search($order->status, $statusFlow);

                            $steps = [
                                ['key' => 'pending',    'label' => 'Pesanan Dibuat',   'icon' => 'fas fa-receipt',       'desc' => 'Pesanan berhasil dibuat dan menunggu konfirmasi.'],
                                ['key' => 'processing', 'label' => 'Sedang Diproses',  'icon' => 'fas fa-cog',           'desc' => 'Pesanan sedang diproses oleh admin.'],
                                ['key' => 'completed',  'label' => 'Selesai',          'icon' => 'fas fa-check-circle',  'desc' => 'Pesanan telah selesai. Produk telah dikirim.'],
                            ];
                        @endphp

                        @if($isCancelled)
                            <div class="flex items-start gap-4 p-4 rounded-xl bg-red-50 dark:bg-red-500/10 border border-red-200 dark:border-red-500/20">
                                <div class="w-10 h-10 rounded-full bg-red-500 flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-times text-white"></i>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-red-700 dark:text-red-400">Pesanan Dibatalkan</h3>
                                    <p class="text-sm text-red-600 dark:text-red-400/80 mt-0.5">Pesanan ini telah dibatalkan. Stok produk telah dikembalikan.</p>
                                    @if($order->updated_at->ne($order->created_at))
                                        <p class="text-xs text-red-500/70 mt-1">{{ $order->updated_at->format('d M Y, H:i') }}</p>
                                    @endif
                                </div>
                            </div>
                        @else
                            <div class="relative">
                                @foreach($steps as $i => $step)
                                    @php
                                        $stepIndex = array_search($step['key'], $statusFlow);
                                        $isDone = $stepIndex <= $currentIndex;
                                        $isCurrent = $stepIndex === $currentIndex;
                                    @endphp
                                    <div class="flex items-start gap-4 {{ !$loop->last ? 'pb-6' : '' }} relative">
                                        {{-- Connector line --}}
                                        @if(!$loop->last)
                                            <div class="absolute left-5 top-10 bottom-0 w-0.5 {{ $isDone && $stepIndex < $currentIndex ? 'bg-peri' : 'bg-gray-200 dark:bg-gray-700' }}"></div>
                                        @endif
                                        {{-- Circle --}}
                                        <div class="w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0 relative z-10 transition-all
                                            {{ $isDone ? 'bg-peri text-white shadow-lg shadow-peri/30' : 'bg-gray-100 dark:bg-gray-700 text-gray-400 dark:text-gray-500' }}
                                            {{ $isCurrent ? 'ring-4 ring-peri/20' : '' }}">
                                            <i class="{{ $step['icon'] }} text-sm"></i>
                                        </div>
                                        {{-- Text --}}
                                        <div class="pt-1.5">
                                            <h3 class="text-sm font-semibold {{ $isDone ? 'text-gray-900 dark:text-white' : 'text-gray-400 dark:text-gray-500' }}">
                                                {{ $step['label'] }}
                                                @if($isCurrent)
                                                    <span class="ml-2 text-xs font-normal text-peri">(Saat ini)</span>
                                                @endif
                                            </h3>
                                            <p class="text-xs {{ $isDone ? 'text-gray-500 dark:text-gray-400' : 'text-gray-300 dark:text-gray-600' }} mt-0.5">{{ $step['desc'] }}</p>
                                            @if($isCurrent && $step['key'] === 'pending')
                                                <p class="text-xs text-gray-400 mt-1">{{ $order->created_at->format('d M Y, H:i') }}</p>
                                            @elseif($isDone && $step['key'] === 'completed' && $order->updated_at->ne($order->created_at))
                                                <p class="text-xs text-gray-400 mt-1">{{ $order->updated_at->format('d M Y, H:i') }}</p>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Right Column — Order Info --}}
            <div class="lg:col-span-1">
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm sticky top-24">
                    <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h2 class="text-sm font-bold uppercase tracking-wide text-gray-900 dark:text-white">Informasi Pesanan</h2>
                    </div>
                    <div class="p-5 space-y-4">
                        <div>
                            <dt class="text-xs uppercase tracking-wider text-gray-500 dark:text-gray-400">Tanggal</dt>
                            <dd class="mt-1 text-sm font-medium text-gray-900 dark:text-white">{{ $order->created_at->format('d M Y, H:i') }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs uppercase tracking-wider text-gray-500 dark:text-gray-400">Status</dt>
                            <dd class="mt-1">
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
                            </dd>
                        </div>
                        <div>
                            <dt class="text-xs uppercase tracking-wider text-gray-500 dark:text-gray-400">Nama</dt>
                            <dd class="mt-1 text-sm font-medium text-gray-900 dark:text-white">{{ $order->name }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs uppercase tracking-wider text-gray-500 dark:text-gray-400">Email</dt>
                            <dd class="mt-1 text-sm font-medium text-gray-900 dark:text-white">{{ $order->email ?? '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs uppercase tracking-wider text-gray-500 dark:text-gray-400">Telepon</dt>
                            <dd class="mt-1 text-sm font-medium text-gray-900 dark:text-white">{{ $order->phone }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs uppercase tracking-wider text-gray-500 dark:text-gray-400">Catatan</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $order->notes ?? '-' }}</dd>
                        </div>
                        <div class="pt-3 border-t border-gray-200 dark:border-gray-700">
                            <dt class="text-xs uppercase tracking-wider text-gray-500 dark:text-gray-400">Total</dt>
                            <dd class="mt-1 text-lg font-bold text-peri">{{ $order->formatted_total }}</dd>
                        </div>
                    </div>

                    {{-- Action Buttons --}}
                    <div class="px-5 pb-5 space-y-3">
                        @if($order->status === 'pending' && !$order->paid_at && \App\Services\PaymentService::isEnabled())
                        <a href="{{ route('payment.pay', $order->order_number) }}"
                           class="flex items-center justify-center gap-2 w-full px-4 py-2.5 text-sm font-semibold rounded-xl bg-peri text-white hover:bg-peri/80 transition shadow-lg shadow-peri/25">
                            <i class="fas fa-credit-card"></i> Bayar Sekarang
                        </a>
                        @endif
                        <a href="{{ route('invoice.show', $order->order_number) }}" target="_blank"
                           class="flex items-center justify-center gap-2 w-full px-4 py-2.5 text-sm font-semibold rounded-xl {{ $order->status === 'pending' && !$order->paid_at && \App\Services\PaymentService::isEnabled() ? 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600' : 'bg-peri text-white hover:bg-peri/80' }} transition">
                            <i class="fas fa-file-invoice"></i> Lihat Invoice
                        </a>
                        <a href="{{ $order->getWhatsAppUrl() }}" target="_blank"
                           class="flex items-center justify-center gap-2 w-full px-4 py-2.5 text-sm font-semibold rounded-xl bg-green-500 text-white hover:bg-green-600 transition">
                            <i class="fab fa-whatsapp"></i> Chat via WhatsApp
                        </a>
                        <a href="{{ route('order.history') }}"
                           class="flex items-center justify-center gap-2 w-full px-4 py-2.5 text-sm font-semibold rounded-xl bg-peri/10 text-peri hover:bg-peri hover:text-white transition">
                            <i class="fas fa-arrow-left"></i> Kembali ke Riwayat
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection

