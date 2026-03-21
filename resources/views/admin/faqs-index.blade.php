@extends('layouts.admin')
@section('title', 'Kelola FAQ')
@section('page-title', 'FAQ')
@section('breadcrumb') <span>FAQ</span> @endsection

@section('content')
<div x-data="{ confirmDelete: null }">

<div class="mb-6 flex flex-wrap items-center justify-between gap-4">
    <div>
        <h1 class="text-2xl font-extrabold text-white">Kelola FAQ</h1>
        <p class="mt-1 text-sm text-gray-400">{{ $faqs->count() }} pertanyaan</p>
    </div>
    <a href="{{ route('admin.faqs.create') }}" class="inline-flex items-center gap-2 rounded-xl bg-peri px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-peri-dark">
        <i class="fas fa-plus"></i> Tambah FAQ
    </a>
</div>

@if(session('success'))
<div class="mb-4 flex items-center gap-3 rounded-xl border border-green-500/20 bg-green-500/10 px-4 py-3">
    <i class="fas fa-check-circle text-green-400"></i>
    <span class="text-sm text-green-300">{{ session('success') }}</span>
</div>
@endif

@if($faqs->isEmpty())
<div class="rounded-2xl border border-gray-800 bg-gray-900 p-12 text-center">
    <i class="fas fa-question-circle text-5xl text-gray-600 mb-4"></i>
    <h3 class="text-lg font-semibold text-white mb-2">Belum ada FAQ</h3>
    <p class="text-sm text-gray-400 mb-4">Tambahkan pertanyaan yang sering ditanyakan pelanggan.</p>
</div>
@else
<div class="space-y-3">
    @foreach($faqs as $faq)
    <div class="rounded-2xl border border-gray-800 bg-gray-900 p-5 flex flex-col sm:flex-row gap-4 {{ !$faq->is_active ? 'opacity-50' : '' }}">
        <div class="flex-1 min-w-0">
            <div class="flex items-center gap-2 mb-1">
                <span class="text-xs text-gray-500">#{{ $faq->sort_order }}</span>
                <span class="rounded-full px-2 py-0.5 text-xs font-bold {{ $faq->is_active ? 'bg-green-500/20 text-green-400' : 'bg-red-500/20 text-red-400' }}">
                    {{ $faq->is_active ? 'Aktif' : 'Nonaktif' }}
                </span>
            </div>
            <h3 class="font-bold text-white">{{ $faq->question }}</h3>
            <p class="mt-1 text-sm text-gray-400 line-clamp-2">{{ Str::limit(strip_tags($faq->answer), 150) }}</p>
        </div>
        <div class="flex items-center gap-2 shrink-0">
            <a href="{{ route('admin.faqs.edit', $faq) }}" class="rounded-lg bg-white/5 px-3 py-2 text-xs font-medium text-gray-300 hover:bg-white/10"><i class="fas fa-edit"></i></a>
            <form action="{{ route('admin.faqs.toggle', $faq) }}" method="POST">
                @csrf @method('PATCH')
                <button class="rounded-lg px-3 py-2 text-xs font-medium {{ $faq->is_active ? 'bg-amber-500/10 text-amber-400' : 'bg-green-500/10 text-green-400' }}">
                    <i class="fas {{ $faq->is_active ? 'fa-eye-slash' : 'fa-eye' }}"></i>
                </button>
            </form>
            <button @click="confirmDelete = {{ $faq->id }}" class="rounded-lg bg-red-500/10 px-3 py-2 text-xs font-medium text-red-400"><i class="fas fa-trash"></i></button>
        </div>
    </div>

    <div x-show="confirmDelete === {{ $faq->id }}" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm" @keydown.escape.window="confirmDelete = null">
        <div class="w-full max-w-sm rounded-2xl border border-gray-700 bg-gray-900 p-6 shadow-2xl" @click.away="confirmDelete = null">
            <h3 class="text-lg font-bold text-white">Hapus FAQ?</h3>
            <p class="mt-2 text-sm text-gray-400">"{{ Str::limit($faq->question, 50) }}" akan dihapus.</p>
            <div class="mt-5 flex justify-end gap-3">
                <button @click="confirmDelete = null" class="rounded-lg bg-white/5 px-4 py-2 text-sm text-gray-300">Batal</button>
                <form action="{{ route('admin.faqs.destroy', $faq) }}" method="POST">
                    @csrf @method('DELETE')
                    <button class="rounded-lg bg-red-600 px-4 py-2 text-sm font-bold text-white">Hapus</button>
                </form>
            </div>
        </div>
    </div>
    @endforeach
</div>
@endif
</div>
@endsection
