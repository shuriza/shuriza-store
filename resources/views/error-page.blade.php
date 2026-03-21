@extends('layouts.app')

@section('title', $title)

@push('styles')
<style>
    .error-container {
        min-height: 60vh;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .error-code {
        font-size: 8rem;
        font-weight: 800;
        background: linear-gradient(135deg, var(--primary), var(--secondary));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        line-height: 1;
    }
    .error-card {
        background: var(--card-bg);
        border: 1px solid rgba(108, 99, 255, 0.15);
        border-radius: 1rem;
        padding: 3rem;
        text-align: center;
        max-width: 500px;
        width: 100%;
    }
</style>
@endpush

@section('content')
<div class="error-container">
    <div class="error-card">
        <div class="error-code">{{ $code }}</div>
        <h2 class="text-2xl font-bold text-white mt-4 mb-2">{{ $title }}</h2>
        <p class="text-gray-400 mb-8">{{ $message }}</p>
        <div class="flex flex-col sm:flex-row gap-3 justify-center">
            <a href="{{ url('/') }}"
               class="inline-flex items-center justify-center px-6 py-3 rounded-lg text-white font-semibold transition"
               style="background: var(--primary);">
                <i class="fas fa-home mr-2"></i> Ke Beranda
            </a>
            <a href="{{ route('products.index') }}"
               class="inline-flex items-center justify-center px-6 py-3 rounded-lg font-semibold transition border"
               style="border-color: var(--primary); color: var(--primary);">
                <i class="fas fa-shopping-bag mr-2"></i> Lihat Produk
            </a>
        </div>
    </div>
</div>
@endsection
