@extends('layouts.app')

@section('title', $article->title)
@section('description', $article->excerpt ?: Str::limit(strip_tags($article->body), 160))

@if($article->image)
@section('og_image', $article->image_url)
@endif

@section('content')
<div class="mt-24 max-w-4xl mx-auto px-4 sm:px-6 pb-16">

    {{-- Breadcrumb --}}
    <nav class="mb-6 text-sm text-gray-400 dark:text-gray-500">
        <a href="{{ route('home') }}" class="hover:text-peri transition">Beranda</a>
        <i class="fas fa-chevron-right text-[0.5rem] mx-2"></i>
        <a href="{{ route('articles.index') }}" class="hover:text-peri transition">Artikel</a>
        <i class="fas fa-chevron-right text-[0.5rem] mx-2"></i>
        <span class="text-gray-600 dark:text-gray-300">{{ Str::limit($article->title, 40) }}</span>
    </nav>

    {{-- Article Header --}}
    <article>
        <header class="mb-8">
            <h1 class="text-3xl sm:text-4xl font-poppins font-bold text-gray-900 dark:text-white leading-tight">
                {{ $article->title }}
            </h1>
            <div class="mt-4 flex flex-wrap items-center gap-4 text-sm text-gray-400 dark:text-gray-500">
                @if($article->author)
                <span class="flex items-center gap-2">
                    <div class="w-7 h-7 rounded-full bg-peri/10 flex items-center justify-center">
                        <i class="fas fa-user text-peri text-xs"></i>
                    </div>
                    {{ $article->author->name }}
                </span>
                @endif
                <span><i class="far fa-calendar mr-1"></i> {{ $article->formatted_date }}</span>
                <span><i class="far fa-eye mr-1"></i> {{ number_format($article->views) }} views</span>
                <span><i class="far fa-clock mr-1"></i> {{ $article->reading_time }} min baca</span>
            </div>
        </header>

        {{-- Featured Image --}}
        @if($article->image)
        <div class="mb-8 rounded-2xl overflow-hidden shadow-lg">
            <img src="{{ $article->image_url }}" alt="{{ $article->title }}"
                 class="w-full max-h-[500px] object-cover">
        </div>
        @endif

        {{-- Article Body --}}
        <div class="prose prose-lg dark:prose-invert max-w-none
                    prose-headings:font-poppins prose-headings:text-gray-900 dark:prose-headings:text-white
                    prose-p:text-gray-600 dark:prose-p:text-gray-300 prose-p:leading-relaxed
                    prose-a:text-peri prose-a:no-underline hover:prose-a:underline
                    prose-img:rounded-xl prose-img:shadow-md
                    prose-strong:text-gray-900 dark:prose-strong:text-white
                    prose-ul:text-gray-600 dark:prose-ul:text-gray-300
                    prose-ol:text-gray-600 dark:prose-ol:text-gray-300
                    prose-blockquote:border-peri prose-blockquote:text-gray-500 dark:prose-blockquote:text-gray-400">
            {!! $article->body !!}
        </div>

        {{-- Share --}}
        <div class="mt-10 pt-6 border-t border-gray-200 dark:border-white/10">
            <p class="text-sm font-semibold text-gray-600 dark:text-gray-300 mb-3">Bagikan Artikel:</p>
            <div class="flex items-center gap-3">
                <a href="https://wa.me/?text={{ urlencode($article->title . ' — ' . route('articles.show', $article)) }}"
                   target="_blank" rel="noopener"
                   class="w-10 h-10 rounded-full bg-green-500/10 text-green-500 flex items-center justify-center hover:bg-green-500 hover:text-white transition">
                    <i class="fab fa-whatsapp text-lg"></i>
                </a>
                <a href="https://twitter.com/intent/tweet?text={{ urlencode($article->title) }}&url={{ urlencode(route('articles.show', $article)) }}"
                   target="_blank" rel="noopener"
                   class="w-10 h-10 rounded-full bg-sky-500/10 text-sky-500 flex items-center justify-center hover:bg-sky-500 hover:text-white transition">
                    <i class="fab fa-twitter text-lg"></i>
                </a>
                <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(route('articles.show', $article)) }}"
                   target="_blank" rel="noopener"
                   class="w-10 h-10 rounded-full bg-blue-500/10 text-blue-500 flex items-center justify-center hover:bg-blue-500 hover:text-white transition">
                    <i class="fab fa-facebook-f text-lg"></i>
                </a>
                <button onclick="navigator.clipboard.writeText('{{ route('articles.show', $article) }}').then(() => this.innerHTML = '<i class=\'fas fa-check text-lg\'></i>')"
                        class="w-10 h-10 rounded-full bg-gray-500/10 text-gray-500 flex items-center justify-center hover:bg-gray-500 hover:text-white transition" title="Salin link">
                    <i class="fas fa-link text-lg"></i>
                </button>
            </div>
        </div>
    </article>

    {{-- Related Articles --}}
    @if($relatedArticles->isNotEmpty())
    <section class="mt-12 pt-8 border-t border-gray-200 dark:border-white/10">
        <h2 class="text-2xl font-poppins font-bold text-gray-900 dark:text-white mb-6">
            Artikel Lainnya
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
            @foreach($relatedArticles as $related)
            <a href="{{ route('articles.show', $related) }}"
               class="group rounded-2xl border border-gray-200 dark:border-white/5 bg-white dark:bg-gray-800 overflow-hidden shadow-sm hover:shadow-md hover:border-peri/30 transition-all duration-300">
                <div class="h-36 overflow-hidden bg-gray-100 dark:bg-gray-700">
                    @if($related->image)
                        <img src="{{ $related->image_url }}" alt="{{ $related->title }}"
                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                    @else
                        <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-peri/20 to-peri/5">
                            <i class="fas fa-newspaper text-3xl text-peri/30"></i>
                        </div>
                    @endif
                </div>
                <div class="p-4">
                    <div class="text-xs text-gray-400 dark:text-gray-500 mb-2">{{ $related->formatted_date }}</div>
                    <h3 class="text-sm font-bold text-gray-900 dark:text-white group-hover:text-peri transition-colors line-clamp-2">
                        {{ $related->title }}
                    </h3>
                </div>
            </a>
            @endforeach
        </div>
    </section>
    @endif

</div>
@endsection

@push('styles')
<style>
    .line-clamp-2 { display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
</style>
@endpush
