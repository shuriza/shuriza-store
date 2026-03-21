@extends('layouts.app')

@section('title', 'Artikel Terbaru')
@section('description', 'Baca artikel terbaru seputar produk digital, tips & trik, dan informasi menarik dari ' . setting('store_name', 'Shuriza Store Kediri') . '.')

@section('content')
<div class="mt-24 max-w-7xl mx-auto px-4 sm:px-6 pb-16">

    {{-- Header --}}
    <div class="mb-8">
        <h1 class="text-3xl sm:text-4xl font-poppins font-bold text-gray-900 dark:text-white">
            <i class="fas fa-newspaper text-peri mr-2"></i>Artikel Terbaru
        </h1>
        <p class="mt-2 text-gray-500 dark:text-gray-400">Tips, info, dan berita seputar produk digital</p>
    </div>

    @if($articles->isEmpty())
    <div class="rounded-2xl border border-gray-200 dark:border-white/5 bg-white dark:bg-gray-800 p-12 text-center">
        <i class="fas fa-newspaper text-5xl text-gray-300 dark:text-gray-600 mb-4"></i>
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Belum ada artikel</h3>
        <p class="text-sm text-gray-500 dark:text-gray-400">Artikel akan segera hadir, nantikan ya!</p>
    </div>
    @else
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($articles as $article)
        <a href="{{ route('articles.show', $article) }}"
           class="group rounded-2xl border border-gray-200 dark:border-white/5 bg-white dark:bg-gray-800 overflow-hidden shadow-sm hover:shadow-lg hover:border-peri/30 dark:hover:border-peri/30 transition-all duration-300">
            {{-- Thumbnail --}}
            <div class="relative h-48 overflow-hidden bg-gray-100 dark:bg-gray-700">
                @if($article->image)
                    <img src="{{ $article->image_url }}" alt="{{ $article->title }}"
                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                @else
                    <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-peri/20 to-peri/5">
                        <i class="fas fa-newspaper text-4xl text-peri/30"></i>
                    </div>
                @endif
            </div>

            {{-- Content --}}
            <div class="p-5">
                <div class="flex items-center gap-3 text-xs text-gray-400 dark:text-gray-500 mb-3">
                    <span><i class="far fa-calendar mr-1"></i> {{ $article->formatted_date }}</span>
                    <span><i class="far fa-eye mr-1"></i> {{ number_format($article->views) }}</span>
                    <span><i class="far fa-clock mr-1"></i> {{ $article->reading_time }} min</span>
                </div>
                <h2 class="text-lg font-bold text-gray-900 dark:text-white group-hover:text-peri transition-colors duration-200 line-clamp-2">
                    {{ $article->title }}
                </h2>
                @if($article->excerpt)
                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400 line-clamp-2">
                    {{ $article->excerpt }}
                </p>
                @endif
                <div class="mt-4 inline-flex items-center gap-1 text-sm font-semibold text-peri group-hover:gap-2 transition-all duration-200">
                    Baca Selengkapnya <i class="fas fa-arrow-right text-xs"></i>
                </div>
            </div>
        </a>
        @endforeach
    </div>

    {{-- Pagination --}}
    <div class="mt-8">
        {{ $articles->links() }}
    </div>
    @endif

</div>
@endsection

@push('styles')
<style>
    .line-clamp-2 { display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
</style>
@endpush
