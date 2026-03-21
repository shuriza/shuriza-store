@extends('layouts.app')
@section('title', 'Notifikasi')

@section('content')
<div class="mt-24 max-w-3xl mx-auto px-4 sm:px-6 pb-16">

    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-poppins font-bold text-gray-900 dark:text-white">
                <i class="fas fa-bell text-peri mr-2"></i>Notifikasi
            </h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                {{ $notifications->total() }} notifikasi
            </p>
        </div>
        @if($notifications->where('read_at', null)->count() > 0)
        <form action="{{ route('notifications.mark-all-read') }}" method="POST">
            @csrf
            <button class="text-sm font-semibold text-peri hover:text-peri-light transition">
                <i class="fas fa-check-double mr-1"></i> Tandai Semua Dibaca
            </button>
        </form>
        @endif
    </div>

    @if(session('success'))
    <div class="mb-4 flex items-center gap-3 rounded-xl border border-green-500/20 bg-green-500/10 px-4 py-3">
        <i class="fas fa-check-circle text-green-400"></i>
        <span class="text-sm text-green-300">{{ session('success') }}</span>
    </div>
    @endif

    @if($notifications->isEmpty())
    <div class="rounded-2xl border border-gray-200 dark:border-white/5 bg-white dark:bg-gray-800 p-12 text-center">
        <i class="fas fa-bell-slash text-5xl text-gray-300 dark:text-gray-600 mb-4"></i>
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Belum ada notifikasi</h3>
        <p class="text-sm text-gray-500 dark:text-gray-400">Notifikasi pesanan dan update akan muncul di sini.</p>
    </div>
    @else
    <div class="space-y-2">
        @foreach($notifications as $notif)
        <a href="{{ route('notifications.read', $notif) }}"
           class="flex items-start gap-4 rounded-2xl border p-4 transition-all duration-200
                  {{ $notif->is_read
                      ? 'border-gray-200 dark:border-white/5 bg-white dark:bg-gray-800 opacity-60 hover:opacity-100'
                      : 'border-peri/20 bg-peri/5 dark:bg-peri/10 hover:bg-peri/10 dark:hover:bg-peri/15' }}">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0
                        {{ $notif->is_read ? 'bg-gray-100 dark:bg-gray-700' : 'bg-peri/10' }}">
                <i class="{{ $notif->icon ?? 'fas fa-bell text-peri' }}"></i>
            </div>
            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2">
                    <h3 class="text-sm font-bold text-gray-900 dark:text-white truncate">{{ $notif->title }}</h3>
                    @unless($notif->is_read)
                    <span class="w-2 h-2 rounded-full bg-peri shrink-0"></span>
                    @endunless
                </div>
                @if($notif->message)
                <p class="mt-0.5 text-xs text-gray-500 dark:text-gray-400 line-clamp-2">{{ $notif->message }}</p>
                @endif
                <p class="mt-1 text-xs text-gray-400 dark:text-gray-500">
                    <i class="far fa-clock mr-1"></i>{{ $notif->created_at->diffForHumans() }}
                </p>
            </div>
            <i class="fas fa-chevron-right text-xs text-gray-400 mt-3 shrink-0"></i>
        </a>
        @endforeach
    </div>

    <div class="mt-6">{{ $notifications->links() }}</div>
    @endif
</div>
@endsection
