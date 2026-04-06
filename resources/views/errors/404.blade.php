@extends('layouts.app')

@section('title', 'Halaman Tidak Ditemukan')

@section('content')
<div class="min-h-[60vh] flex items-center justify-center px-4">
    <div class="text-center">
        {{-- Icon --}}
        <div class="mb-6">
            <div class="w-32 h-32 mx-auto rounded-full bg-gradient-to-br from-peri/20 to-secondary/20 flex items-center justify-center">
                <span class="text-6xl font-bold bg-gradient-to-r from-peri to-secondary bg-clip-text text-transparent">404</span>
            </div>
        </div>

        {{-- Message --}}
        <h1 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white mb-3">
            Halaman Tidak Ditemukan
        </h1>
        <p class="text-gray-500 dark:text-gray-400 mb-8 max-w-md mx-auto">
            Maaf, halaman yang kamu cari tidak ada atau sudah dipindahkan. Yuk kembali ke halaman utama!
        </p>

        {{-- Actions --}}
        <div class="flex flex-col sm:flex-row gap-3 justify-center">
            <a href="{{ route('home') }}" 
               class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl bg-peri text-white font-semibold hover:bg-peri-dark transition-colors shadow-lg shadow-peri/25">
                <i class="fas fa-home"></i>
                Kembali ke Beranda
            </a>
            <a href="{{ route('products.index') }}" 
               class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-400 font-semibold hover:border-peri hover:text-peri dark:hover:border-peri dark:hover:text-peri transition-colors">
                <i class="fas fa-shopping-bag"></i>
                Lihat Produk
            </a>
        </div>

        {{-- Search suggestion --}}
        <div class="mt-10 pt-8 border-t border-gray-200 dark:border-gray-700/50">
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Atau coba cari produk yang kamu inginkan:</p>
            <form action="{{ route('products.index') }}" method="GET" class="max-w-md mx-auto">
                <div class="relative">
                    <input type="text" name="search" placeholder="Cari produk..."
                           class="w-full pl-12 pr-4 py-3 rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-peri/50 focus:border-peri outline-none transition-all" />
                    <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
