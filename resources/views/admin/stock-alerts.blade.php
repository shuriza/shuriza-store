@extends('layouts.admin')

@section('title', 'Stok Menipis')
@section('page-title', 'Peringatan Stok')
@section('breadcrumb')
<a href="{{ route('admin.dashboard') }}" class="text-peri hover:text-peri-light">Dashboard</a>
<span class="mx-1">/</span>
<span>Stok Menipis</span>
@endsection

@section('content')

{{-- Stats Cards --}}
<div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-3">
    <div class="rounded-2xl border border-amber-500/20 bg-gray-900 p-5">
        <div class="flex items-center gap-3">
            <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-amber-400/10 text-amber-400">
                <i class="fas fa-exclamation-triangle text-lg"></i>
            </div>
            <div>
                <p class="text-2xl font-extrabold text-white">{{ $stats['low_stock'] }}</p>
                <p class="text-xs text-gray-400">Stok Menipis (≤ {{ $stats['threshold'] }})</p>
            </div>
        </div>
    </div>
    <div class="rounded-2xl border border-red-500/20 bg-gray-900 p-5">
        <div class="flex items-center gap-3">
            <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-red-400/10 text-red-400">
                <i class="fas fa-times-circle text-lg"></i>
            </div>
            <div>
                <p class="text-2xl font-extrabold text-white">{{ $stats['out_of_stock'] }}</p>
                <p class="text-xs text-gray-400">Stok Habis</p>
            </div>
        </div>
    </div>
    <div class="rounded-2xl border border-gray-800 bg-gray-900 p-5">
        <div class="flex items-center gap-3">
            <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-peri/10 text-peri">
                <i class="fas fa-sliders-h text-lg"></i>
            </div>
            <div>
                <p class="text-2xl font-extrabold text-white">{{ $stats['threshold'] }}</p>
                <p class="text-xs text-gray-400">Batas Stok Rendah</p>
            </div>
        </div>
    </div>
</div>

{{-- Actions --}}
<div class="mb-6 flex flex-wrap items-center justify-between gap-4">
    <div class="flex flex-wrap items-center gap-3">
        {{-- Filter --}}
        <a href="{{ route('admin.stock-alerts.index') }}"
           class="rounded-xl px-4 py-2 text-sm font-semibold transition {{ !request('filter') || request('filter') === 'all' ? 'bg-peri text-white' : 'border border-gray-700 text-gray-300 hover:text-white hover:border-gray-600' }}">
            Semua
        </a>
        <a href="{{ route('admin.stock-alerts.index', ['filter' => 'low']) }}"
           class="rounded-xl px-4 py-2 text-sm font-semibold transition {{ request('filter') === 'low' ? 'bg-amber-500 text-white' : 'border border-gray-700 text-gray-300 hover:text-white hover:border-gray-600' }}">
            <i class="fas fa-exclamation-triangle mr-1"></i> Menipis
        </a>
        <a href="{{ route('admin.stock-alerts.index', ['filter' => 'out']) }}"
           class="rounded-xl px-4 py-2 text-sm font-semibold transition {{ request('filter') === 'out' ? 'bg-red-500 text-white' : 'border border-gray-700 text-gray-300 hover:text-white hover:border-gray-600' }}">
            <i class="fas fa-times-circle mr-1"></i> Habis
        </a>

        {{-- Search --}}
        <form action="{{ route('admin.stock-alerts.index') }}" method="GET" class="flex items-center gap-2">
            @if (request('filter'))
                <input type="hidden" name="filter" value="{{ request('filter') }}">
            @endif
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari produk..."
                   class="rounded-xl border border-gray-700 bg-gray-800 px-4 py-2 text-sm text-white placeholder-gray-500 focus:border-peri focus:outline-none focus:ring-1 focus:ring-peri">
            <button type="submit" class="rounded-xl bg-gray-800 px-3 py-2 text-sm text-gray-300 transition hover:bg-gray-700 hover:text-white">
                <i class="fas fa-search"></i>
            </button>
        </form>
    </div>

    {{-- Send Alert Email --}}
    <form action="{{ route('admin.stock-alerts.send') }}" method="POST">
        @csrf
        <button type="submit"
                class="inline-flex items-center gap-2 rounded-xl bg-amber-500 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-amber-600"
                onclick="return confirm('Kirim email alert stok menipis ke semua admin?')">
            <i class="fas fa-envelope"></i> Kirim Email Alert
        </button>
    </form>
</div>

{{-- Product Table --}}
<div class="rounded-2xl border border-gray-800 bg-gray-900">
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="border-b border-gray-800 bg-white/[.02]">
                    <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-gray-500">Produk</th>
                    <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-gray-500">Kategori</th>
                    <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-gray-500 text-center">Stok</th>
                    <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-gray-500">Harga</th>
                    <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-gray-500">Status</th>
                    <th class="px-6 py-3 text-xs font-bold uppercase tracking-wider text-gray-500 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($products as $product)
                <tr class="border-b border-gray-800/50 transition hover:bg-peri/5">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            @if ($product->image)
                                <img src="{{ $product->image_url }}" alt="{{ $product->name }}"
                                     class="h-10 w-10 rounded-lg object-cover">
                            @else
                                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-gray-800 text-gray-500">
                                    <i class="fas fa-image"></i>
                                </div>
                            @endif
                            <div>
                                <p class="text-sm font-semibold text-white">{{ $product->name }}</p>
                                <p class="text-xs text-gray-500">{{ $product->slug }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-400">{{ $product->category->name ?? '-' }}</td>
                    <td class="px-6 py-4 text-center">
                        <span class="inline-flex items-center rounded-full px-3 py-1 text-sm font-bold
                            {{ $product->stock === 0 ? 'bg-red-500/15 text-red-400' : 'bg-amber-400/15 text-amber-400' }}">
                            {{ $product->stock }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-sm font-semibold text-white">{{ $product->formatted_price }}</td>
                    <td class="px-6 py-4">
                        @if ($product->stock === 0)
                            <span class="inline-flex items-center gap-1 rounded-full bg-red-500/10 px-2.5 py-0.5 text-xs font-bold text-red-400">
                                <i class="fas fa-times-circle"></i> Habis
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 rounded-full bg-amber-400/10 px-2.5 py-0.5 text-xs font-bold text-amber-400">
                                <i class="fas fa-exclamation-triangle"></i> Menipis
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-center">
                        <a href="{{ route('admin.products.edit', $product) }}"
                           class="inline-flex items-center gap-1 rounded-lg bg-peri/10 px-3 py-1.5 text-xs font-semibold text-peri transition hover:bg-peri/20">
                            <i class="fas fa-edit"></i> Edit Stok
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center">
                        <div class="flex flex-col items-center gap-3">
                            <div class="flex h-16 w-16 items-center justify-center rounded-full bg-green-500/10 text-green-400">
                                <i class="fas fa-check-circle text-2xl"></i>
                            </div>
                            <p class="text-sm font-semibold text-white">Semua Stok Aman! 🎉</p>
                            <p class="text-xs text-gray-500">Tidak ada produk dengan stok rendah saat ini.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if ($products->hasPages())
    <div class="border-t border-gray-800 px-6 py-4">
        {{ $products->links() }}
    </div>
    @endif
</div>

@endsection
