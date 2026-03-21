@extends('layouts.app')

@section('title', 'FAQ – Pertanyaan Umum')

@section('content')
<div class="py-8 md:py-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6">

        {{-- Header --}}
        <div class="text-center mb-10">
            <div class="w-16 h-16 rounded-2xl bg-peri/10 flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-question-circle text-2xl text-peri"></i>
            </div>
            <h1 class="text-3xl md:text-4xl font-poppins font-bold text-gray-900 dark:text-white mb-3">
                Pertanyaan Umum (FAQ)
            </h1>
            <p class="text-gray-500 dark:text-gray-400 max-w-2xl mx-auto">
                Temukan jawaban atas pertanyaan yang sering diajukan seputar {{ setting('store_name', 'Shuriza Store') }}.
            </p>
        </div>

        {{-- FAQ Items --}}
        <div class="space-y-4" x-data="{ open: null }">
            @forelse($faqs as $index => $faq)
                <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-white/10 overflow-hidden">
                    <button @click="open = open === {{ $index }} ? null : {{ $index }}" class="w-full flex items-center justify-between px-6 py-4 text-left">
                        <span class="text-sm font-bold text-gray-900 dark:text-white">{{ $faq->question }}</span>
                        <i class="fas fa-chevron-down text-xs text-gray-400 transition-transform duration-200" :class="open === {{ $index }} && 'rotate-180'"></i>
                    </button>
                    <div x-show="open === {{ $index }}" x-cloak
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 -translate-y-1"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100"
                         x-transition:leave-end="opacity-0">
                        <div class="px-6 pb-4 text-sm text-gray-600 dark:text-gray-400 leading-relaxed">
                            {!! nl2br(e($faq->answer)) !!}
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-12">
                    <i class="fas fa-inbox text-4xl text-gray-300 dark:text-gray-600 mb-4"></i>
                    <p class="text-gray-500 dark:text-gray-400">Belum ada FAQ yang ditambahkan.</p>
                </div>
            @endforelse
        </div>

        {{-- CTA --}}
        <div class="mt-10 text-center">
            <div class="bg-gradient-to-r from-peri/10 to-pink-500/10 rounded-2xl border border-peri/20 p-8">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Pertanyaan belum terjawab?</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-5">Hubungi kami langsung via WhatsApp untuk bantuan lebih lanjut.</p>
                <a href="https://wa.me/{{ setting('whatsapp_number') }}" target="_blank"
                   class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-green-500 text-white font-semibold text-sm
                          shadow-lg shadow-green-500/25 hover:shadow-green-500/40 hover:-translate-y-0.5 transition-all duration-200">
                    <i class="fab fa-whatsapp"></i> Chat Admin
                </a>
            </div>
        </div>

    </div>
</div>
@endsection
