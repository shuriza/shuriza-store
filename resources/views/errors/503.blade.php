@extends('layouts.app')

@section('title', 'Sedang Maintenance')

@section('content')
<div class="min-h-[60vh] flex items-center justify-center px-4">
    <div class="text-center">
        {{-- Icon --}}
        <div class="mb-6">
            <div class="w-32 h-32 mx-auto rounded-full bg-gradient-to-br from-amber-500/20 to-yellow-500/20 flex items-center justify-center animate-pulse">
                <i class="fas fa-tools text-5xl text-amber-500"></i>
            </div>
        </div>

        {{-- Message --}}
        <h1 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white mb-3">
            Sedang Dalam Perbaikan
        </h1>
        <p class="text-gray-500 dark:text-gray-400 mb-8 max-w-md mx-auto">
            Kami sedang melakukan pemeliharaan sistem untuk memberikan pengalaman yang lebih baik. Mohon tunggu sebentar ya!
        </p>

        {{-- Countdown/Status --}}
        <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-amber-100 dark:bg-amber-500/20 text-amber-700 dark:text-amber-400 text-sm font-medium mb-8">
            <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
            Maintenance sedang berlangsung
        </div>

        {{-- Social links --}}
        <div class="pt-8 border-t border-gray-200 dark:border-gray-700/50">
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Ikuti update terbaru:</p>
            <div class="flex justify-center gap-4">
                <a href="#" class="w-10 h-10 rounded-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center text-gray-500 hover:text-peri hover:bg-peri/10 transition-colors">
                    <i class="fab fa-instagram"></i>
                </a>
                <a href="#" class="w-10 h-10 rounded-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center text-gray-500 hover:text-green-500 hover:bg-green-500/10 transition-colors">
                    <i class="fab fa-whatsapp"></i>
                </a>
                <a href="#" class="w-10 h-10 rounded-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center text-gray-500 hover:text-blue-500 hover:bg-blue-500/10 transition-colors">
                    <i class="fab fa-telegram"></i>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
