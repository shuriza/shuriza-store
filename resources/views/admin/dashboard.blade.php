@extends('layouts.admin')

@section('title', 'Dashboard Admin')
@section('page-title', 'Dashboard')
@section('breadcrumb')
<span>Dashboard</span>
@endsection

@section('content')
@php $maxOrders = max(1, $last7Days->max('orders')); @endphp

{{-- Stats Cards --}}
<div class="mb-6 grid grid-cols-2 gap-4 lg:grid-cols-4">
    {{-- Total Produk --}}
    <div class="rounded-2xl border border-gray-800 bg-gray-900 p-6">
        <div class="mb-4 flex items-center justify-between">
            <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-peri/10 text-peri">
                <i class="fas fa-box text-lg"></i>
            </div>
        </div>
        <p class="text-2xl font-extrabold text-white">{{ number_format($totalProducts) }}</p>
        <p class="mt-1 text-sm text-gray-400">Total Produk</p>
        <p class="mt-2 text-xs text-gray-500">{{ $activeProducts }} aktif</p>
    </div>

    {{-- Total Pesanan --}}
    <div class="rounded-2xl border border-gray-800 bg-gray-900 p-6">
        <div class="mb-4 flex items-center justify-between">
            <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-green-500/10 text-green-400">
                <i class="fas fa-shopping-cart text-lg"></i>
            </div>
        </div>
        <p class="text-2xl font-extrabold text-white">{{ number_format($totalOrders) }}</p>
        <p class="mt-1 text-sm text-gray-400">Total Pesanan</p>
        <p class="mt-2 text-xs text-gray-500">{{ $pendingOrders }} menunggu</p>
    </div>

    {{-- Pelanggan --}}
    <div class="rounded-2xl border border-gray-800 bg-gray-900 p-6">
        <div class="mb-4 flex items-center justify-between">
            <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-blue-500/10 text-blue-400">
                <i class="fas fa-users text-lg"></i>
            </div>
        </div>
        <p class="text-2xl font-extrabold text-white">{{ number_format($totalUsers) }}</p>
        <p class="mt-1 text-sm text-gray-400">Pelanggan</p>
        <p class="mt-2 text-xs text-gray-500">{{ $newUsersToday }} baru hari ini</p>
    </div>

    {{-- Pendapatan --}}
    <div class="rounded-2xl border border-gray-800 bg-gray-900 p-6">
        <div class="mb-4 flex items-center justify-between">
            <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-pink-500/10 text-pink-400">
                <i class="fas fa-wallet text-lg"></i>
            </div>
        </div>
        <p class="text-2xl font-extrabold text-white">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</p>
        <p class="mt-1 text-sm text-gray-400">Pendapatan</p>
        <p class="mt-2 text-xs text-gray-500">Rp {{ number_format($monthRevenue, 0, ',', '.') }} bulan ini</p>
    </div>
</div>

{{-- Quick Actions --}}
<div class="mb-6 flex flex-wrap gap-3">
    <a href="{{ route('admin.products.create') }}"
       class="inline-flex items-center gap-2 rounded-xl bg-peri px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-peri-dark">
        <i class="fas fa-plus"></i> Tambah Produk
    </a>
    <a href="{{ route('admin.orders.index', ['status' => 'pending']) }}"
       class="inline-flex items-center gap-2 rounded-xl border border-yellow-500/30 bg-yellow-500/10 px-4 py-2.5 text-sm font-semibold text-yellow-400 transition hover:bg-yellow-500/20">
        <i class="fas fa-clock"></i> {{ $pendingOrders ?? 0 }} Pesanan Pending
    </a>
    <a href="{{ route('admin.orders.index') }}"
       class="inline-flex items-center gap-2 rounded-xl border border-gray-700 px-4 py-2.5 text-sm font-semibold text-gray-300 transition hover:text-white hover:border-gray-600">
        <i class="fas fa-list"></i> Semua Pesanan
    </a>
    <a href="{{ route('admin.users.index') }}"
       class="inline-flex items-center gap-2 rounded-xl border border-gray-700 px-4 py-2.5 text-sm font-semibold text-gray-300 transition hover:text-white hover:border-gray-600">
        <i class="fas fa-users"></i> Kelola Pengguna
    </a>
</div>

{{-- Revenue Summary --}}
<div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-3">
    <div class="rounded-2xl border border-gray-800 bg-gray-900 p-5">
        <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Pendapatan Hari Ini</p>
        <p class="mt-2 text-xl font-extrabold text-white">Rp {{ number_format($todayRevenue, 0, ',', '.') }}</p>
    </div>
    <div class="rounded-2xl border border-gray-800 bg-gray-900 p-5">
        <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Bulan Ini</p>
        <p class="mt-2 text-xl font-extrabold text-white">Rp {{ number_format($monthRevenue, 0, ',', '.') }}</p>
    </div>
    <div class="rounded-2xl border border-gray-800 bg-gray-900 p-5">
        <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Total Keseluruhan</p>
        <p class="mt-2 text-xl font-extrabold text-white">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</p>
    </div>
</div>

{{-- Two Column Layout --}}
<div class="mb-6 grid grid-cols-1 gap-6 lg:grid-cols-3">

    {{-- Left Column (2/3) --}}
    <div class="space-y-6 lg:col-span-2">

        {{-- Order Chart --}}
        <div class="rounded-2xl border border-gray-800 bg-gray-900 p-6">
            <h3 class="mb-6 flex items-center gap-2 text-base font-bold text-white">
                <i class="fas fa-chart-bar text-peri"></i> Pesanan 7 Hari Terakhir
            </h3>
            <div class="flex items-end gap-2" style="height: 160px">
                @foreach($last7Days as $day)
                <div class="flex flex-1 flex-col items-center gap-1 h-full">
                    <span class="text-xs font-bold text-peri">{{ $day['orders'] }}</span>
                    <div class="flex w-full flex-1 items-end">
                        <div class="w-full rounded-t bg-peri" style="height: {{ ($day['orders'] / $maxOrders) * 100 }}%"></div>
                    </div>
                    <span class="text-[0.65rem] text-gray-500">{{ $day['date'] }}</span>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Pesanan Terbaru --}}
        <div class="rounded-2xl border border-gray-800 bg-gray-900">
            <div class="flex items-center justify-between border-b border-gray-800 px-6 py-4">
                <h3 class="flex items-center gap-2 text-sm font-bold text-white">
                    <i class="fas fa-receipt text-peri"></i> Pesanan Terbaru
                </h3>
                <a href="{{ route('admin.orders.index') }}" class="text-xs font-medium text-peri hover:text-peri-light">
                    Lihat Semua <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="border-b border-gray-800 bg-white/[.02]">
                            <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-gray-500">No. Order</th>
                            <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-gray-500">Pelanggan</th>
                            <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-gray-500">Item</th>
                            <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-gray-500">Total</th>
                            <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-gray-500">Status</th>
                            <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-gray-500">Tanggal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentOrders as $order)
                        <tr class="border-b border-gray-800/50 transition hover:bg-peri/5">
                            <td class="px-6 py-3">
                                <a href="{{ route('admin.orders.show', $order) }}" class="text-sm font-semibold text-peri hover:underline">{{ $order->order_number }}</a>
                            </td>
                            <td class="px-6 py-3 text-sm text-gray-300">{{ $order->name }}</td>
                            <td class="px-6 py-3 text-sm text-gray-400">{{ $order->items->count() }} item</td>
                            <td class="px-6 py-3 text-sm font-semibold text-white">{{ $order->formatted_total }}</td>
                            <td class="px-6 py-3">
                                @php
                                    $colors = [
                                        'pending' => 'bg-yellow-400/10 text-yellow-400',
                                        'processing' => 'bg-blue-400/10 text-blue-400',
                                        'completed' => 'bg-green-400/10 text-green-400',
                                        'cancelled' => 'bg-red-400/10 text-red-400',
                                    ];
                                @endphp
                                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-bold {{ $colors[$order->status] ?? 'bg-gray-700 text-gray-300' }}">
                                    {{ $order->status_label }}
                                </span>
                            </td>
                            <td class="px-6 py-3 text-xs text-gray-500">{{ $order->created_at->format('d M Y') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-sm text-gray-500">Belum ada pesanan.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Right Column (1/3) --}}
    <div class="space-y-6">

        {{-- Status Pesanan --}}
        <div class="rounded-2xl border border-gray-800 bg-gray-900 p-6">
            <h3 class="mb-4 text-sm font-bold text-white">Status Pesanan</h3>
            <div class="space-y-3">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="h-2.5 w-2.5 rounded-full bg-yellow-400"></span>
                        <span class="text-sm text-gray-400">Pending</span>
                    </div>
                    <span class="text-sm font-bold text-white">{{ $pendingOrders }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="h-2.5 w-2.5 rounded-full bg-blue-400"></span>
                        <span class="text-sm text-gray-400">Diproses</span>
                    </div>
                    <span class="text-sm font-bold text-white">{{ $processingOrders }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="h-2.5 w-2.5 rounded-full bg-green-400"></span>
                        <span class="text-sm text-gray-400">Selesai</span>
                    </div>
                    <span class="text-sm font-bold text-white">{{ $completedOrders }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="h-2.5 w-2.5 rounded-full bg-red-400"></span>
                        <span class="text-sm text-gray-400">Dibatalkan</span>
                    </div>
                    <span class="text-sm font-bold text-white">{{ $cancelledOrders }}</span>
                </div>
            </div>
        </div>

        {{-- Produk Terlaris --}}
        <div class="rounded-2xl border border-gray-800 bg-gray-900">
            <div class="border-b border-gray-800 px-6 py-4">
                <h3 class="flex items-center gap-2 text-sm font-bold text-white">
                    <i class="fas fa-trophy text-yellow-400"></i> Produk Terlaris
                </h3>
            </div>
            <div class="divide-y divide-gray-800/50">
                @foreach($topProducts->take(5) as $i => $product)
                <div class="flex items-center gap-3 px-6 py-3 transition hover:bg-peri/5">
                    @php
                        $rankClasses = match($i) {
                            0 => 'bg-yellow-400/20 text-yellow-400',
                            1 => 'bg-gray-400/10 text-gray-400',
                            2 => 'bg-amber-600/15 text-amber-500',
                            default => 'bg-white/5 text-gray-500',
                        };
                    @endphp
                    <span class="flex h-7 w-7 flex-shrink-0 items-center justify-center rounded-lg text-xs font-extrabold {{ $rankClasses }}">
                        #{{ $i + 1 }}
                    </span>
                    <div class="min-w-0 flex-1">
                        <p class="truncate text-sm font-semibold text-white">{{ $product->name }}</p>
                        <p class="text-xs text-gray-500">{{ $product->category?->name ?? '-' }}</p>
                    </div>
                    <div class="text-right flex-shrink-0">
                        <p class="text-sm font-bold text-white">{{ $product->total_sold }}</p>
                        <p class="text-[0.65rem] text-gray-500">terjual</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Stok Menipis --}}
        <div class="rounded-2xl border border-amber-500/20 bg-gray-900">
            <div class="flex items-center justify-between border-b border-gray-800 px-6 py-4">
                <h3 class="flex items-center gap-2 text-sm font-bold text-amber-400">
                    <i class="fas fa-exclamation-triangle"></i> Stok Menipis
                </h3>
                <a href="{{ route('admin.stock-alerts.index') }}" class="text-xs font-medium text-peri hover:text-peri-light">
                    Lihat Semua <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
            @if($outOfStockProducts > 0)
            <div class="mx-6 mt-4 flex items-center gap-2 rounded-lg bg-red-500/10 px-3 py-2 text-xs font-semibold text-red-400">
                <i class="fas fa-times-circle"></i> {{ $outOfStockProducts }} produk habis stok
            </div>
            @endif
            <div class="divide-y divide-gray-800/50">
                @forelse($lowStockProducts as $product)
                <div class="flex items-center gap-3 px-6 py-3">
                    <div class="min-w-0 flex-1">
                        <p class="truncate text-sm font-semibold text-white">{{ $product->name }}</p>
                        <p class="text-xs text-gray-500">{{ $product->category?->name ?? '-' }}</p>
                    </div>
                    <span class="flex-shrink-0 rounded-full px-2.5 py-0.5 text-xs font-bold {{ $product->stock === 0 ? 'bg-red-500/15 text-red-400' : 'bg-amber-400/15 text-amber-400' }}">
                        {{ $product->stock }} stok
                    </span>
                </div>
                @empty
                <div class="px-6 py-6 text-center text-sm text-gray-500">Semua stok aman 👍</div>
                @endforelse
            </div>
        </div>

    </div>
</div>
@endsection
