@extends('layouts.app')

@section('title', 'Akses Ditolak')

@section('content')
<div class="min-h-[60vh] flex items-center justify-center px-4">
    <div class="text-center">
        {{-- Icon --}}
        <div class="mb-6">
            <div class="w-32 h-32 mx-auto rounded-full bg-gradient-to-br from-red-500/20 to-pink-500/20 flex items-center justify-center">
                <span class="text-6xl font-bold bg-gradient-to-r from-red-500 to-pink-500 bg-clip-text text-transparent">403</span>
            </div>
        </div>

        {{-- Message --}}
        <h1 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white mb-3">
            Akses Ditolak
        </h1>
        <p class="text-gray-500 dark:text-gray-400 mb-8 max-w-md mx-auto">
            Maaf, kamu tidak memiliki izin untuk mengakses halaman ini. Silakan login dengan akun yang sesuai atau kembali ke halaman utama.
        </p>

        {{-- Actions --}}
        <div class="flex flex-col sm:flex-row gap-3 justify-center">
            <a href="{{ route('home') }}" 
               class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl bg-peri text-white font-semibold hover:bg-peri-dark transition-colors shadow-lg shadow-peri/25">
                <i class="fas fa-home"></i>
                Kembali ke Beranda
            </a>
            @guest
            <a href="{{ route('login') }}" 
               class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-400 font-semibold hover:border-peri hover:text-peri dark:hover:border-peri dark:hover:text-peri transition-colors">
                <i class="fas fa-sign-in-alt"></i>
                Login
            </a>
            @endguest
        </div>
    </div>
</div>
@endsection
