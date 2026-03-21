@extends('layouts.app')

@section('title', 'Edit Profil')

@section('content')
<div class="py-8 md:py-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6">

        {{-- Header --}}
        <div class="mb-8">
            <div class="flex items-center gap-3 mb-2">
                <a href="{{ route('dashboard') }}" class="text-gray-400 dark:text-gray-500 hover:text-peri transition-colors">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <h1 class="text-2xl md:text-3xl font-poppins font-bold text-gray-900 dark:text-white">
                    Edit Profil
                </h1>
            </div>
            <p class="text-sm text-gray-500 dark:text-gray-400 ml-8">Kelola informasi akun dan keamanan kamu.</p>
        </div>

        {{-- Profile Info Card --}}
        <div class="mb-6 bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-white/10 shadow-sm overflow-hidden">
            <div class="flex items-center gap-3 px-6 py-4 border-b border-gray-100 dark:border-white/5">
                <div class="w-8 h-8 rounded-lg bg-peri/10 flex items-center justify-center">
                    <i class="fas fa-user text-peri text-sm"></i>
                </div>
                <h2 class="text-base font-bold text-gray-900 dark:text-white">Informasi Profil</h2>
            </div>
            <div class="p-6">
                @include('profile.partials.update-profile-information-form')
            </div>
        </div>

        {{-- Password Card --}}
        <div class="mb-6 bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-white/10 shadow-sm overflow-hidden">
            <div class="flex items-center gap-3 px-6 py-4 border-b border-gray-100 dark:border-white/5">
                <div class="w-8 h-8 rounded-lg bg-amber-500/10 flex items-center justify-center">
                    <i class="fas fa-lock text-amber-500 text-sm"></i>
                </div>
                <h2 class="text-base font-bold text-gray-900 dark:text-white">Ubah Password</h2>
            </div>
            <div class="p-6">
                @include('profile.partials.update-password-form')
            </div>
        </div>

        {{-- Delete Account Card --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-red-200 dark:border-red-500/20 shadow-sm overflow-hidden">
            <div class="flex items-center gap-3 px-6 py-4 border-b border-red-100 dark:border-red-500/10">
                <div class="w-8 h-8 rounded-lg bg-red-500/10 flex items-center justify-center">
                    <i class="fas fa-exclamation-triangle text-red-500 text-sm"></i>
                </div>
                <h2 class="text-base font-bold text-red-600 dark:text-red-400">Zona Berbahaya</h2>
            </div>
            <div class="p-6">
                @include('profile.partials.delete-user-form')
            </div>
        </div>

    </div>
</div>
@endsection
