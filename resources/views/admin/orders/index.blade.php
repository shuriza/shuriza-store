@extends('layouts.admin')

@section('title', 'Kelola Pesanan')
@section('page-title', 'Pesanan')
@section('breadcrumb')
<span>Pesanan</span>
@endsection

@section('content')
<div x-data="{ confirmDelete: null, selectedOrders: [], selectAll: false }"
     x-init="$watch('selectAll', val => { selectedOrders = val ? [...document.querySelectorAll('[data-order-id]')].map(el => el.dataset.orderId) : [] })">

{{-- Page Header --}}
<div class="mb-6 flex flex-wrap items-center justify-between gap-4">
    <div>
        <h1 class="text-2xl font-extrabold text-white">Kelola Pesanan</h1>
        <p class="mt-1 text-sm text-gray-400">{{ $orders->total() }} pesanan ditemukan</p>
    </div>
    <div class="flex items-center gap-3">
        @if($stats['pending_delivery'] > 0)
        <a href="{{ route('admin.orders.index', ['status' => 'pending_delivery']) }}"
           class="inline-flex items-center gap-2 rounded-xl bg-amber-500/10 border border-amber-500/30 px-4 py-2.5 text-sm font-semibold text-amber-400 transition hover:bg-amber-500/20">
            <i class="fas fa-paper-plane"></i> Perlu Dikirim
            <span class="rounded-full bg-amber-500 px-1.5 py-0.5 text-xs font-bold text-white">{{ $stats['pending_delivery'] }}</span>
        </a>
        @endif
        <a href="{{ route('admin.orders.export', request()->query()) }}"
           class="inline-flex items-center gap-2 rounded-xl bg-peri px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-peri-dark">
            <i class="fas fa-download"></i> Export CSV
        </a>
    </div>
</div>

{{-- Filter Bar --}}
<form method="GET" action="{{ route('admin.orders.index') }}">
    <div class="mb-6 rounded-2xl border border-gray-800 bg-gray-900 p-4">
        <div class="flex flex-wrap items-end gap-3">
            {{-- Search --}}
            <div class="min-w-[200px] flex-1">
                <label class="mb-1 block text-xs font-medium text-gray-400">Cari</label>
                <div class="relative">
                    <i class="fas fa-search pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-xs text-gray-500"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="No. order, nama, HP..."
                           class="w-full rounded-xl border border-gray-700 bg-gray-800 py-2.5 pl-9 pr-4 text-sm text-white placeholder-gray-500 focus:border-peri focus:outline-none focus:ring-1 focus:ring-peri" />
                </div>
            </div>

            {{-- Status --}}
            <div class="w-48">
                <label class="mb-1 block text-xs font-medium text-gray-400">Status</label>
                <select name="status"
                        class="w-full rounded-xl border border-gray-700 bg-gray-800 px-4 py-2.5 text-sm text-white focus:border-peri focus:outline-none focus:ring-1 focus:ring-peri">
                    <option value="">Semua Status</option>
                    <option value="pending"           {{ request('status') === 'pending'           ? 'selected' : '' }}>Menunggu</option>
                    <option value="processing"        {{ request('status') === 'processing'        ? 'selected' : '' }}>Diproses</option>
                    <option value="completed"         {{ request('status') === 'completed'         ? 'selected' : '' }}>Selesai</option>
                    <option value="cancelled"         {{ request('status') === 'cancelled'         ? 'selected' : '' }}>Dibatalkan</option>
                    <option value="pending_delivery"  {{ request('status') === 'pending_delivery'  ? 'selected' : '' }}>⚡ Perlu Dikirim</option>
                </select>
            </div>

            {{-- Date From --}}
            <div class="w-40">
                <label class="mb-1 block text-xs font-medium text-gray-400">Dari</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}"
                       class="w-full rounded-xl border border-gray-700 bg-gray-800 px-4 py-2.5 text-sm text-white focus:border-peri focus:outline-none focus:ring-1 focus:ring-peri" />
            </div>

            {{-- Date To --}}
            <div class="w-40">
                <label class="mb-1 block text-xs font-medium text-gray-400">Sampai</label>
                <input type="date" name="date_to" value="{{ request('date_to') }}"
                       class="w-full rounded-xl border border-gray-700 bg-gray-800 px-4 py-2.5 text-sm text-white focus:border-peri focus:outline-none focus:ring-1 focus:ring-peri" />
            </div>

            {{-- Buttons --}}
            <div class="flex gap-2">
                <button type="submit"
                        class="inline-flex items-center gap-2 rounded-xl bg-peri px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-peri-dark">
                    <i class="fas fa-filter"></i> Filter
                </button>
                <a href="{{ route('admin.orders.index') }}"
                   class="inline-flex items-center gap-2 rounded-xl border border-gray-700 px-4 py-2.5 text-sm font-medium text-gray-400 transition hover:text-white">
                    <i class="fas fa-redo-alt"></i> Reset
                </a>
            </div>
        </div>
    </div>
</form>

@if($orders->count())

{{-- Bulk Action Bar --}}
<div x-show="selectedOrders.length > 0" x-cloak x-transition
     class="mb-4 flex flex-wrap items-center gap-3 rounded-xl border border-peri/30 bg-peri/5 p-3">
    <span class="text-sm font-medium text-peri">
        <i class="fas fa-check-circle mr-1"></i>
        <span x-text="selectedOrders.length"></span> pesanan dipilih
    </span>
    <form method="POST" action="{{ route('admin.orders.bulk-status') }}" class="flex items-center gap-2">
        @csrf
        <template x-for="id in selectedOrders" :key="id">
            <input type="hidden" name="order_ids[]" :value="id">
        </template>
        <select name="status" class="rounded-lg border border-gray-700 bg-gray-800 px-3 py-1.5 text-xs text-white focus:border-peri focus:ring-1 focus:ring-peri">
            <option value="pending">Menunggu</option>
            <option value="processing">Diproses</option>
            <option value="completed">Selesai</option>
            <option value="cancelled">Dibatalkan</option>
        </select>
        <button type="submit" class="rounded-lg bg-peri px-3 py-1.5 text-xs font-semibold text-white transition hover:bg-peri-dark">
            Ubah Status
        </button>
    </form>
    <button @click="selectedOrders = []; selectAll = false"
            class="ml-auto text-xs text-gray-400 hover:text-white transition">
        Batal
    </button>
</div>

{{-- Orders Table --}}
<div class="overflow-hidden rounded-2xl border border-gray-800 bg-gray-900">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm">
            <thead class="border-b border-gray-800 text-xs uppercase tracking-wider text-gray-500">
                <tr>
                    <th class="px-4 py-3 w-10">
                        <input type="checkbox" x-model="selectAll"
                               class="rounded border-gray-600 bg-gray-800 text-peri focus:ring-peri">
                    </th>
                    <th class="px-4 py-3">No. Order</th>
                    <th class="px-4 py-3">Pelanggan</th>
                    <th class="px-4 py-3">Total</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3">Tanggal</th>
                    <th class="px-4 py-3 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-800">
                @foreach($orders as $order)
                <tr class="transition hover:bg-gray-800/50" data-order-id="{{ $order->id }}">
                    {{-- Checkbox --}}
                    <td class="px-4 py-3">
                        <input type="checkbox" value="{{ $order->id }}"
                               x-model="selectedOrders"
                               class="rounded border-gray-600 bg-gray-800 text-peri focus:ring-peri">
                    </td>

                    {{-- Order Number --}}
                    <td class="px-4 py-3">
                        <span class="font-mono text-sm font-semibold text-white">{{ $order->order_number }}</span>
                        <p class="mt-0.5 text-xs text-gray-500">{{ $order->items->count() }} produk</p>
                        @if($order->pending_delivery_count > 0)
                            <span class="inline-flex items-center gap-1 mt-1 rounded-full bg-amber-500/10 px-2 py-0.5 text-[10px] font-semibold text-amber-400">
                                <i class="fas fa-paper-plane text-[8px]"></i> {{ $order->pending_delivery_count }} item belum dikirim
                            </span>
                        @endif
                    </td>

                    {{-- Customer --}}
                    <td class="px-4 py-3">
                        <p class="font-medium text-white">{{ $order->name }}</p>
                        <p class="mt-0.5 text-xs text-gray-500">{{ $order->phone }}</p>
                    </td>

                    {{-- Total --}}
                    <td class="px-4 py-3 font-semibold text-white">{{ $order->formatted_total }}</td>

                    {{-- Status Badge --}}
                    <td class="px-4 py-3">
                        @php
                            $badgeClass = match($order->status) {
                                'pending'    => 'bg-yellow-500/10 text-yellow-400',
                                'processing' => 'bg-blue-500/10 text-blue-400',
                                'completed'  => 'bg-green-500/10 text-green-400',
                                'cancelled'  => 'bg-red-500/10 text-red-400',
                                default      => 'bg-gray-500/10 text-gray-400',
                            };
                        @endphp
                        <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-semibold {{ $badgeClass }}">
                            <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
                            {{ $order->status_label }}
                        </span>
                    </td>

                    {{-- Date --}}
                    <td class="px-4 py-3 text-gray-400">
                        <p class="text-sm">{{ $order->created_at->format('d M Y') }}</p>
                        <p class="text-xs text-gray-500">{{ $order->created_at->format('H:i') }}</p>
                    </td>

                    {{-- Actions --}}
                    <td class="px-4 py-3 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('admin.orders.show', $order) }}"
                               class="rounded-lg bg-peri/10 px-3 py-1.5 text-xs font-semibold text-peri transition hover:bg-peri/20">
                                <i class="fas fa-eye"></i>
                            </a>
                            <button @click="confirmDelete = {{ $order->id }}"
                                    class="rounded-lg bg-red-500/10 px-3 py-1.5 text-xs font-semibold text-red-400 transition hover:bg-red-500/20">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>

                        {{-- Delete Confirmation --}}
                        <div x-show="confirmDelete === {{ $order->id }}" x-cloak x-transition
                             class="mt-2 rounded-lg border border-red-500/20 bg-red-500/5 p-3 text-left">
                            <p class="mb-2 text-xs text-red-400">Hapus order #{{ $order->order_number }}?</p>
                            <div class="flex gap-2">
                                <form action="{{ route('admin.orders.destroy', $order) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="rounded-lg bg-red-600 px-3 py-1 text-xs font-semibold text-white transition hover:bg-red-700">
                                        Hapus
                                    </button>
                                </form>
                                <button @click="confirmDelete = null"
                                        class="rounded-lg border border-gray-700 px-3 py-1 text-xs text-gray-400 transition hover:text-white">
                                    Batal
                                </button>
                            </div>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

{{-- Pagination --}}
<div class="mt-6">
    {{ $orders->links() }}
</div>
@else
<div class="rounded-2xl border border-gray-800 bg-gray-900 py-16 text-center">
    <i class="fas fa-shopping-cart mb-4 text-4xl text-gray-700"></i>
    <p class="text-gray-400">Tidak ada pesanan ditemukan.</p>
</div>
@endif

</div>
@endsection

