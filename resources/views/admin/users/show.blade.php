@extends('layouts.admin')

@section('title', 'Detail Pelanggan - ' . $user->name)
@section('page-title', 'Detail Pelanggan')
@section('breadcrumb')
<a href="{{ route('admin.users.index') }}" class="text-gray-400 hover:text-white">Pelanggan</a>
<i class="fas fa-chevron-right mx-1 text-[0.5rem] text-gray-600"></i>
<span>{{ $user->name }}</span>
@endsection

@section('content')
<div x-data="{ confirmDelete: false, confirmToggle: false }">

{{-- Page Header --}}
<div class="mb-6 flex flex-wrap items-center justify-between gap-4">
    <div>
        <h1 class="text-2xl font-extrabold text-white">Detail Pelanggan</h1>
        <p class="mt-1 text-sm text-gray-400">Bergabung {{ $user->created_at->format('d M Y') }}</p>
    </div>
    <a href="{{ route('admin.users.index') }}"
       class="inline-flex items-center gap-2 rounded-xl border border-gray-700 px-4 py-2.5 text-sm font-medium text-gray-400 transition hover:text-white">
        <i class="fas fa-arrow-left"></i> Kembali
    </a>
</div>

{{-- Two-Column Layout --}}
<div class="grid gap-6 lg:grid-cols-3">

    {{-- Left Column (2/3) --}}
    <div class="space-y-6 lg:col-span-2">
        <div class="rounded-2xl border border-gray-800 bg-gray-900 p-6">
            <h2 class="mb-4 text-lg font-bold text-white">
                <i class="fas fa-shopping-bag mr-2 text-peri"></i>Pesanan Pelanggan
            </h2>
            @if($orders->count())
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="border-b border-gray-800 text-xs uppercase tracking-wider text-gray-500">
                        <tr>
                            <th class="pb-3 pr-4">No. Order</th>
                            <th class="pb-3 px-4 text-right">Total</th>
                            <th class="pb-3 px-4">Status</th>
                            <th class="pb-3 px-4">Tanggal</th>
                            <th class="pb-3 pl-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-800">
                        @foreach($orders as $order)
                        <tr class="transition hover:bg-gray-800/50">
                            <td class="py-3 pr-4 font-mono text-sm font-semibold text-white">{{ $order->order_number }}</td>
                            <td class="px-4 py-3 text-right font-semibold text-white">{{ $order->formatted_total }}</td>
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
                            <td class="px-4 py-3 text-gray-400">{{ $order->created_at->format('d M Y') }}</td>
                            <td class="py-3 pl-4 text-right">
                                <a href="{{ route('admin.orders.show', $order) }}"
                                   class="rounded-lg bg-peri/10 px-3 py-1.5 text-xs font-semibold text-peri transition hover:bg-peri/20">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="py-10 text-center">
                <i class="fas fa-shopping-cart mb-3 text-3xl text-gray-700"></i>
                <p class="text-sm text-gray-400">Belum ada pesanan.</p>
            </div>
            @endif
        </div>
    </div>

    {{-- Right Column (1/3) --}}
    <div class="space-y-6">

        {{-- Info Pelanggan --}}
        <div class="rounded-2xl border border-gray-800 bg-gray-900 p-6 text-center">
            <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-peri/20 text-2xl font-bold text-peri">
                {{ strtoupper(substr($user->name, 0, 1)) }}
            </div>
            <h3 class="text-lg font-bold text-white">{{ $user->name }}</h3>
            <p class="mt-1 text-sm text-gray-400">{{ $user->email }}</p>
            @if($user->phone)
            <p class="mt-1 text-sm text-gray-400"><i class="fas fa-phone mr-1 text-xs"></i>{{ $user->phone }}</p>
            @endif
            <div class="mt-3">
                @if($user->role === 'admin')
                    <span class="rounded-full bg-peri/10 px-3 py-1 text-xs font-semibold text-peri">Admin</span>
                @else
                    <span class="rounded-full bg-gray-700 px-3 py-1 text-xs font-semibold text-gray-300">Customer</span>
                @endif
            </div>
            <p class="mt-3 text-xs text-gray-500">Bergabung {{ $user->created_at->format('d M Y') }}</p>
        </div>

        {{-- Statistik --}}
        <div class="rounded-2xl border border-gray-800 bg-gray-900 p-6">
            <h2 class="mb-4 text-lg font-bold text-white">Statistik</h2>
            <dl class="space-y-3 text-sm">
                <div class="flex justify-between">
                    <dt class="text-gray-400">Total Pesanan</dt>
                    <dd class="font-semibold text-white">{{ $user->orders_count }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-400">Total Belanja</dt>
                    <dd class="font-semibold text-white">Rp {{ number_format($totalSpent, 0, ',', '.') }}</dd>
                </div>
            </dl>
        </div>

        {{-- Actions --}}
        @if($user->id !== auth()->id())
        <div class="space-y-3">
            <button @click="confirmToggle = true"
                    class="flex w-full items-center justify-center gap-2 rounded-xl bg-peri px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-peri-dark">
                <i class="fas fa-user-shield"></i> Ubah ke {{ $user->role === 'admin' ? 'Customer' : 'Admin' }}
            </button>
            <button @click="confirmDelete = true"
                    class="flex w-full items-center justify-center gap-2 rounded-xl border border-red-500/30 px-4 py-2.5 text-sm font-semibold text-red-400 transition hover:bg-red-500/10">
                <i class="fas fa-trash"></i> Hapus Pelanggan
            </button>
        </div>

        {{-- Toggle Role Confirmation --}}
        <div x-show="confirmToggle" x-cloak x-transition
             class="rounded-2xl border border-yellow-500/20 bg-yellow-500/5 p-4">
            <p class="mb-3 text-sm text-yellow-400">Ubah role {{ $user->name }} menjadi {{ $user->role === 'admin' ? 'Customer' : 'Admin' }}?</p>
            <div class="flex gap-2">
                <form action="{{ route('admin.users.toggle-role', $user) }}" method="POST" class="flex-1">
                    @csrf @method('PATCH')
                    <button type="submit" class="w-full rounded-xl bg-yellow-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-yellow-700">Ya, Ubah</button>
                </form>
                <button @click="confirmToggle = false" class="flex-1 rounded-xl border border-gray-700 px-4 py-2 text-sm text-gray-400 transition hover:text-white">Batal</button>
            </div>
        </div>

        {{-- Delete Confirmation --}}
        <div x-show="confirmDelete" x-cloak x-transition
             class="rounded-2xl border border-red-500/20 bg-red-500/5 p-4">
            <p class="mb-3 text-sm text-red-400">Yakin ingin menghapus {{ $user->name }}? Semua data akan hilang.</p>
            <div class="flex gap-2">
                <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="flex-1">
                    @csrf @method('DELETE')
                    <button type="submit" class="w-full rounded-xl bg-red-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-red-700">Hapus</button>
                </form>
                <button @click="confirmDelete = false" class="flex-1 rounded-xl border border-gray-700 px-4 py-2 text-sm text-gray-400 transition hover:text-white">Batal</button>
            </div>
        </div>
        @endif
    </div>

</div>
</div>
@endsection
