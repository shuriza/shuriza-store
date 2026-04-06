@extends('layouts.app')

@section('title', 'Terjadi Kesalahan')

@section('content')
<div class="min-h-[60vh] flex items-center justify-center px-4">
    <div class="text-center">
        {{-- Icon --}}
        <div class="mb-6">
            <div class="w-32 h-32 mx-auto rounded-full bg-gradient-to-br from-red-500/20 to-orange-500/20 flex items-center justify-center">
                <span class="text-6xl font-bold bg-gradient-to-r from-red-500 to-orange-500 bg-clip-text text-transparent">500</span>
            </div>
        </div>

        {{-- Message --}}
        <h1 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white mb-3">
            Terjadi Kesalahan Server
        </h1>
        <p class="text-gray-500 dark:text-gray-400 mb-8 max-w-md mx-auto">
            Maaf, terjadi kesalahan pada server kami. Tim teknis sudah diberitahu dan sedang memperbaikinya. Silakan coba lagi dalam beberapa saat.
        </p>

        {{-- Actions --}}
        <div class="flex flex-col sm:flex-row gap-3 justify-center">
            <a href="{{ route('home') }}" 
               class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl bg-peri text-white font-semibold hover:bg-peri-dark transition-colors shadow-lg shadow-peri/25">
                <i class="fas fa-home"></i>
                Kembali ke Beranda
            </a>
            <button onclick="window.location.reload()" 
                    class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-400 font-semibold hover:border-peri hover:text-peri dark:hover:border-peri dark:hover:text-peri transition-colors">
                <i class="fas fa-redo"></i>
                Coba Lagi
            </button>
        </div>

        {{-- Support info --}}
        <div class="mt-10 pt-8 border-t border-gray-200 dark:border-gray-700/50">
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-2">Butuh bantuan?</p>
            <a href="{{ route('pages.contact') }}" class="text-peri hover:text-peri-dark font-medium">
                <i class="fas fa-headset mr-1"></i> Hubungi Tim Support
            </a>
        </div>
    </div>
</div>
@endsection
