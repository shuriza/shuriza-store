@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 py-8">
    {{-- Header --}}
    <div class="mb-8">
        <h1 class="text-2xl md:text-3xl font-poppins font-bold text-gray-900 dark:text-white">
            Halo, {{ $user->name }}! 👋
        </h1>
        <p class="text-gray-500 dark:text-gray-400 mt-1">Selamat datang di dashboard akun kamu.</p>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 shadow-sm border border-gray-100 dark:border-gray-700">
            <div class="w-10 h-10 rounded-xl bg-peri/10 flex items-center justify-center mb-3">
                <i class="fas fa-shopping-bag text-peri"></i>
            </div>
            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $totalOrders }}</p>
            <p class="text-sm text-gray-500 dark:text-gray-400">Total Pesanan</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 shadow-sm border border-gray-100 dark:border-gray-700">
            <div class="w-10 h-10 rounded-xl bg-yellow-500/10 flex items-center justify-center mb-3">
                <i class="fas fa-clock text-yellow-500"></i>
            </div>
            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $pendingOrders }}</p>
            <p class="text-sm text-gray-500 dark:text-gray-400">Menunggu</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 shadow-sm border border-gray-100 dark:border-gray-700">
            <div class="w-10 h-10 rounded-xl bg-green-500/10 flex items-center justify-center mb-3">
                <i class="fas fa-check-circle text-green-500"></i>
            </div>
            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $completedOrders }}</p>
            <p class="text-sm text-gray-500 dark:text-gray-400">Selesai</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 shadow-sm border border-gray-100 dark:border-gray-700">
            <div class="w-10 h-10 rounded-xl bg-pink-500/10 flex items-center justify-center mb-3">
                <i class="fas fa-wallet text-pink-500"></i>
            </div>
            <p class="text-2xl font-bold text-gray-900 dark:text-white">Rp {{ number_format($totalSpent, 0, ',', '.') }}</p>
            <p class="text-sm text-gray-500 dark:text-gray-400">Total Belanja</p>
        </div>
    </div>

    <div class="grid lg:grid-cols-3 gap-6">
        {{-- Sidebar: Quick Actions + Profile --}}
        <div class="lg:col-span-1 space-y-4">
            <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-gray-700">
                <h2 class="font-poppins font-bold text-gray-900 dark:text-white mb-4">Menu Cepat</h2>
                <div class="space-y-2">
                    <a href="{{ route('products.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700 transition group">
                        <div class="w-9 h-9 rounded-lg bg-peri/10 flex items-center justify-center"><i class="fas fa-store text-peri text-sm"></i></div>
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300 group-hover:text-peri transition">Belanja Produk</span>
                        <i class="fas fa-chevron-right text-xs text-gray-400 ml-auto"></i>
                    </a>
                    <a href="{{ route('cart.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700 transition group">
                        <div class="w-9 h-9 rounded-lg bg-orange-500/10 flex items-center justify-center"><i class="fas fa-shopping-cart text-orange-500 text-sm"></i></div>
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300 group-hover:text-orange-500 transition">Keranjang</span>
                        <i class="fas fa-chevron-right text-xs text-gray-400 ml-auto"></i>
                    </a>
                    <a href="{{ route('order.history') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700 transition group">
                        <div class="w-9 h-9 rounded-lg bg-blue-500/10 flex items-center justify-center"><i class="fas fa-history text-blue-500 text-sm"></i></div>
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300 group-hover:text-blue-500 transition">Riwayat Pesanan</span>
                        <i class="fas fa-chevron-right text-xs text-gray-400 ml-auto"></i>
                    </a>
                    <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700 transition group">
                        <div class="w-9 h-9 rounded-lg bg-green-500/10 flex items-center justify-center"><i class="fas fa-user-edit text-green-500 text-sm"></i></div>
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300 group-hover:text-green-500 transition">Edit Profil</span>
                        <i class="fas fa-chevron-right text-xs text-gray-400 ml-auto"></i>
                    </a>
                    <a href="https://wa.me/{{ setting('whatsapp_number') }}?text={{ urlencode('Halo ' . setting('store_name', 'Shuriza Store') . ', saya butuh bantuan.') }}"
                       target="_blank" rel="noopener"
                       class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700 transition group">
                        <div class="w-9 h-9 rounded-lg bg-emerald-500/10 flex items-center justify-center"><i class="fab fa-whatsapp text-emerald-500 text-sm"></i></div>
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300 group-hover:text-emerald-500 transition">Hubungi Support</span>
                        <i class="fas fa-chevron-right text-xs text-gray-400 ml-auto"></i>
                    </a>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-gray-700">
                <h2 class="font-poppins font-bold text-gray-900 dark:text-white mb-4">Info Akun</h2>
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-12 h-12 rounded-full bg-gradient-to-br from-peri to-peri-dark flex items-center justify-center">
                        <span class="text-white font-bold text-lg">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                    </div>
                    <div>
                        <p class="font-semibold text-gray-900 dark:text-white">{{ $user->name }}</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ $user->email }}</p>
                    </div>
                </div>
                <div class="pt-3 border-t border-gray-100 dark:border-gray-700 space-y-1">
                    @if($user->phone)
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        <i class="fas fa-phone mr-1"></i> {{ $user->phone }}
                    </p>
                    @endif
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        <i class="fas fa-calendar-alt mr-1"></i> Bergabung sejak {{ $user->created_at->format('d M Y') }}
                    </p>
                </div>
            </div>
        </div>

        {{-- Recent Orders --}}
        <div class="lg:col-span-2">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
                <div class="flex items-center justify-between p-6 border-b border-gray-100 dark:border-gray-700">
                    <h2 class="font-poppins font-bold text-gray-900 dark:text-white">Pesanan Terakhir</h2>
                    <a href="{{ route('order.history') }}" class="text-sm text-peri hover:text-peri-dark font-medium transition">Lihat Semua <i class="fas fa-arrow-right ml-1"></i></a>
                </div>

                @if($recentOrders->isEmpty())
                    <div class="p-12 text-center">
                        <div class="w-16 h-16 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-shopping-bag text-2xl text-gray-400"></i>
                        </div>
                        <p class="text-gray-500 dark:text-gray-400 mb-4">Belum ada pesanan</p>
                        <a href="{{ route('products.index') }}" class="inline-flex items-center gap-2 rounded-xl bg-peri px-5 py-2.5 text-sm font-semibold text-white hover:bg-peri-dark transition">
                            <i class="fas fa-store"></i> Mulai Belanja
                        </a>
                    </div>
                @else
                    <div class="divide-y divide-gray-100 dark:divide-gray-700">
                        @foreach($recentOrders as $order)
                        <div class="p-4 sm:p-6 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="font-mono text-sm font-semibold text-gray-900 dark:text-white">{{ $order->order_number }}</span>
                                        @switch($order->status)
                                            @case('pending')
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400">Menunggu</span>
                                                @break
                                            @case('processing')
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400">Diproses</span>
                                                @break
                                            @case('completed')
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">Selesai</span>
                                                @break
                                            @case('cancelled')
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400">Dibatalkan</span>
                                                @break
                                        @endswitch
                                    </div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                        {{ $order->created_at->format('d M Y, H:i') }} · {{ $order->items->count() }} item
                                    </p>
                                </div>
                                <div class="flex items-center gap-4">
                                    <span class="text-sm font-bold text-peri">{{ $order->formatted_total }}</span>
                                    <a href="{{ route('order.show', $order->order_number) }}" class="text-sm text-gray-500 hover:text-peri transition">
                                        <i class="fas fa-chevron-right"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
