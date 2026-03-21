@extends('layouts.admin')

@section('title', 'Kelola Artikel')
@section('page-title', 'Artikel')
@section('breadcrumb')
<span>Artikel</span>
@endsection

@section('content')
<div x-data="{ confirmDelete: null }">

{{-- Page Header --}}
<div class="mb-6 flex flex-wrap items-center justify-between gap-4">
    <div>
        <h1 class="text-2xl font-extrabold text-white">Kelola Artikel</h1>
        <p class="mt-1 text-sm text-gray-400">{{ $articles->total() }} artikel ditemukan</p>
    </div>
    <a href="{{ route('admin.articles.create') }}"
       class="inline-flex items-center gap-2 rounded-xl bg-peri px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-peri-dark">
        <i class="fas fa-plus"></i> Tulis Artikel
    </a>
</div>

{{-- Success Alert --}}
@if (session('success'))
<div class="mb-4 flex items-center gap-3 rounded-xl border border-green-500/20 bg-green-500/10 px-4 py-3">
    <i class="fas fa-check-circle text-green-400"></i>
    <span class="text-sm text-green-300">{{ session('success') }}</span>
</div>
@endif

{{-- Filter Bar --}}
<form action="{{ route('admin.articles.index') }}" method="GET"
      class="mb-6 rounded-2xl border border-gray-800 bg-gray-900 p-4">
    <div class="flex flex-wrap items-end gap-3">
        <div class="min-w-[220px] flex-1">
            <label class="mb-1 block text-xs font-medium text-gray-400">Cari</label>
            <div class="flex items-center gap-2 rounded-lg border border-gray-700 bg-gray-800 px-3 py-2">
                <i class="fas fa-search text-sm text-gray-500"></i>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Judul artikel..." class="w-full bg-transparent text-sm text-white placeholder-gray-500 outline-none">
            </div>
        </div>
        <div>
            <label class="mb-1 block text-xs font-medium text-gray-400">Status</label>
            <select name="status" class="rounded-lg border border-gray-700 bg-gray-800 px-3 py-2 text-sm text-white">
                <option value="">Semua</option>
                <option value="published" {{ request('status') === 'published' ? 'selected' : '' }}>Dipublikasikan</option>
                <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
            </select>
        </div>
        <div class="flex gap-2">
            <button type="submit" class="rounded-lg bg-peri px-4 py-2 text-sm font-semibold text-white hover:bg-peri-dark">
                <i class="fas fa-search mr-1"></i> Filter
            </button>
            @if(request()->hasAny(['search', 'status']))
            <a href="{{ route('admin.articles.index') }}" class="rounded-lg bg-white/5 px-4 py-2 text-sm text-gray-300 hover:bg-white/10">Reset</a>
            @endif
        </div>
    </div>
</form>

{{-- Articles Table --}}
@if($articles->isEmpty())
<div class="rounded-2xl border border-gray-800 bg-gray-900 p-12 text-center">
    <i class="fas fa-newspaper text-5xl text-gray-600 mb-4"></i>
    <h3 class="text-lg font-semibold text-white mb-2">Belum ada artikel</h3>
    <p class="text-sm text-gray-400 mb-4">Tulis artikel pertama untuk ditampilkan di website.</p>
    <a href="{{ route('admin.articles.create') }}"
       class="inline-flex items-center gap-2 rounded-xl bg-peri px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-peri-dark">
        <i class="fas fa-plus"></i> Tulis Artikel Pertama
    </a>
</div>
@else
<div class="rounded-2xl border border-gray-800 bg-gray-900 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-gray-800 text-left">
                    <th class="px-5 py-3 font-semibold text-gray-400">Artikel</th>
                    <th class="px-5 py-3 font-semibold text-gray-400 hidden sm:table-cell">Penulis</th>
                    <th class="px-5 py-3 font-semibold text-gray-400 hidden md:table-cell">Views</th>
                    <th class="px-5 py-3 font-semibold text-gray-400">Status</th>
                    <th class="px-5 py-3 font-semibold text-gray-400 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-800/50">
                @foreach($articles as $article)
                <tr class="group hover:bg-white/[0.02] transition">
                    <td class="px-5 py-3">
                        <div class="flex items-center gap-3">
                            @if($article->image)
                                <img src="{{ $article->image_url }}" alt="" class="w-14 h-10 rounded-lg object-cover shrink-0">
                            @else
                                <div class="w-14 h-10 rounded-lg bg-gray-800 flex items-center justify-center shrink-0">
                                    <i class="fas fa-newspaper text-gray-600 text-xs"></i>
                                </div>
                            @endif
                            <div class="min-w-0">
                                <div class="font-semibold text-white truncate max-w-[250px]">{{ $article->title }}</div>
                                <div class="text-xs text-gray-500 mt-0.5">{{ $article->formatted_date }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-5 py-3 text-gray-400 hidden sm:table-cell">{{ $article->author?->name ?? '-' }}</td>
                    <td class="px-5 py-3 text-gray-400 hidden md:table-cell">
                        <span class="inline-flex items-center gap-1"><i class="fas fa-eye text-xs"></i> {{ number_format($article->views) }}</span>
                    </td>
                    <td class="px-5 py-3">
                        <span class="rounded-full px-2.5 py-1 text-xs font-bold
                            {{ $article->is_published ? 'bg-green-500/20 text-green-400' : 'bg-amber-500/20 text-amber-400' }}">
                            {{ $article->is_published ? 'Publik' : 'Draft' }}
                        </span>
                    </td>
                    <td class="px-5 py-3 text-right">
                        <div class="flex items-center justify-end gap-1.5">
                            @if($article->is_published)
                            <a href="{{ route('articles.show', $article) }}" target="_blank"
                               class="rounded-lg bg-white/5 px-2.5 py-1.5 text-xs text-gray-300 hover:bg-white/10 transition" title="Lihat">
                                <i class="fas fa-external-link-alt"></i>
                            </a>
                            @endif
                            <a href="{{ route('admin.articles.edit', $article) }}"
                               class="rounded-lg bg-white/5 px-2.5 py-1.5 text-xs text-gray-300 hover:bg-white/10 transition" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('admin.articles.toggle', $article) }}" method="POST" class="inline">
                                @csrf @method('PATCH')
                                <button type="submit"
                                    class="rounded-lg px-2.5 py-1.5 text-xs transition
                                           {{ $article->is_published ? 'bg-amber-500/10 text-amber-400 hover:bg-amber-500/20' : 'bg-green-500/10 text-green-400 hover:bg-green-500/20' }}"
                                    title="{{ $article->is_published ? 'Sembunyikan' : 'Publikasikan' }}">
                                    <i class="fas {{ $article->is_published ? 'fa-eye-slash' : 'fa-eye' }}"></i>
                                </button>
                            </form>
                            <button @click="confirmDelete = {{ $article->id }}"
                                    class="rounded-lg bg-red-500/10 px-2.5 py-1.5 text-xs text-red-400 hover:bg-red-500/20 transition" title="Hapus">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>

                {{-- Delete Modal --}}
                <template x-teleport="body">
                    <div x-show="confirmDelete === {{ $article->id }}" x-cloak
                         class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm"
                         @keydown.escape.window="confirmDelete = null">
                        <div class="w-full max-w-sm rounded-2xl border border-gray-700 bg-gray-900 p-6 shadow-2xl"
                             @click.away="confirmDelete = null">
                            <h3 class="text-lg font-bold text-white">Hapus Artikel?</h3>
                            <p class="mt-2 text-sm text-gray-400">"{{ $article->title }}" akan dihapus permanen.</p>
                            <div class="mt-5 flex justify-end gap-3">
                                <button @click="confirmDelete = null"
                                        class="rounded-lg bg-white/5 px-4 py-2 text-sm font-medium text-gray-300 hover:bg-white/10">Batal</button>
                                <form action="{{ route('admin.articles.destroy', $article) }}" method="POST">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="rounded-lg bg-red-600 px-4 py-2 text-sm font-bold text-white hover:bg-red-700">Hapus</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </template>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

{{-- Pagination --}}
<div class="mt-4">
    {{ $articles->links() }}
</div>
@endif

</div>
@endsection
