@extends('layouts.admin')

@section('title', 'Kelola Banner')
@section('page-title', 'Banner')
@section('breadcrumb')
<span>Banner</span>
@endsection

@section('content')
<div x-data="{ confirmDelete: null }">

{{-- Page Header --}}
<div class="mb-6 flex flex-wrap items-center justify-between gap-4">
    <div>
        <h1 class="text-2xl font-extrabold text-white">Kelola Banner</h1>
        <p class="mt-1 text-sm text-gray-400">{{ $banners->count() }} banner slide</p>
    </div>
    <a href="{{ route('admin.banners.create') }}"
       class="inline-flex items-center gap-2 rounded-xl bg-peri px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-peri-dark">
        <i class="fas fa-plus"></i> Tambah Banner
    </a>
</div>

{{-- Success Alert --}}
@if (session('success'))
<div class="mb-4 flex items-center gap-3 rounded-xl border border-green-500/20 bg-green-500/10 px-4 py-3">
    <i class="fas fa-check-circle text-green-400"></i>
    <span class="text-sm text-green-300">{{ session('success') }}</span>
</div>
@endif

{{-- Banner List --}}
@if($banners->isEmpty())
<div class="rounded-2xl border border-gray-800 bg-gray-900 p-12 text-center">
    <i class="fas fa-images text-5xl text-gray-600 mb-4"></i>
    <h3 class="text-lg font-semibold text-white mb-2">Belum ada banner</h3>
    <p class="text-sm text-gray-400 mb-4">Tambahkan banner slide untuk tampil di halaman utama. Jika tidak ada banner, akan ditampilkan slide default.</p>
    <a href="{{ route('admin.banners.create') }}"
       class="inline-flex items-center gap-2 rounded-xl bg-peri px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-peri-dark">
        <i class="fas fa-plus"></i> Tambah Banner Pertama
    </a>
</div>
@else
<div class="space-y-4">
    @foreach($banners as $banner)
    <div class="group rounded-2xl border border-gray-800 bg-gray-900 overflow-hidden transition hover:border-gray-700">
        <div class="flex flex-col sm:flex-row">
            {{-- Preview --}}
            <div class="sm:w-72 h-40 sm:h-auto shrink-0 relative overflow-hidden {{ $banner->image ? '' : $banner->gradient_class }}">
                @if($banner->image)
                    <img src="{{ $banner->image_url }}" alt="{{ $banner->title }}" class="w-full h-full object-cover">
                @else
                    <div class="w-full h-full flex items-center justify-center">
                        <i class="fas fa-image text-4xl text-white/30"></i>
                    </div>
                @endif
                @unless($banner->is_active)
                    <div class="absolute inset-0 bg-black/60 flex items-center justify-center">
                        <span class="rounded-full bg-red-500/20 px-3 py-1 text-xs font-bold text-red-400">Nonaktif</span>
                    </div>
                @endunless
            </div>

            {{-- Info --}}
            <div class="flex-1 p-5 flex flex-col justify-between">
                <div>
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <h3 class="text-lg font-bold text-white">
                                {{ $banner->title ?: '(Tanpa judul)' }}
                            </h3>
                            @if($banner->subtitle)
                                <p class="mt-1 text-sm text-gray-400">{{ $banner->subtitle }}</p>
                            @endif
                        </div>
                        <span class="shrink-0 rounded-full px-2.5 py-1 text-xs font-bold
                            {{ $banner->is_active ? 'bg-green-500/20 text-green-400' : 'bg-red-500/20 text-red-400' }}">
                            {{ $banner->is_active ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </div>

                    <div class="mt-3 flex flex-wrap items-center gap-3 text-xs text-gray-500">
                        <span><i class="fas fa-sort-numeric-up mr-1"></i> Urutan: {{ $banner->sort_order }}</span>
                        @if($banner->link)
                            <span><i class="fas fa-link mr-1"></i> {{ Str::limit($banner->link, 40) }}</span>
                        @endif
                        @if($banner->image)
                            <span class="text-green-400"><i class="fas fa-image mr-1"></i> Ada gambar</span>
                        @else
                            <span class="text-amber-400"><i class="fas fa-palette mr-1"></i> Gradient</span>
                        @endif
                    </div>
                </div>

                {{-- Actions --}}
                <div class="mt-4 flex items-center gap-2">
                    <a href="{{ route('admin.banners.edit', $banner) }}"
                       class="inline-flex items-center gap-1.5 rounded-lg bg-white/5 px-3 py-2 text-xs font-medium text-gray-300 transition hover:bg-white/10">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                    <form action="{{ route('admin.banners.toggle', $banner) }}" method="POST">
                        @csrf @method('PATCH')
                        <button type="submit"
                            class="inline-flex items-center gap-1.5 rounded-lg px-3 py-2 text-xs font-medium transition
                                   {{ $banner->is_active ? 'bg-amber-500/10 text-amber-400 hover:bg-amber-500/20' : 'bg-green-500/10 text-green-400 hover:bg-green-500/20' }}">
                            <i class="fas {{ $banner->is_active ? 'fa-eye-slash' : 'fa-eye' }}"></i>
                            {{ $banner->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                        </button>
                    </form>
                    <button @click="confirmDelete = {{ $banner->id }}"
                            class="inline-flex items-center gap-1.5 rounded-lg bg-red-500/10 px-3 py-2 text-xs font-medium text-red-400 transition hover:bg-red-500/20">
                        <i class="fas fa-trash"></i> Hapus
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Delete Modal --}}
    <div x-show="confirmDelete === {{ $banner->id }}" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm"
         @keydown.escape.window="confirmDelete = null">
        <div class="w-full max-w-sm rounded-2xl border border-gray-700 bg-gray-900 p-6 shadow-2xl"
             @click.away="confirmDelete = null">
            <h3 class="text-lg font-bold text-white">Hapus Banner?</h3>
            <p class="mt-2 text-sm text-gray-400">Banner "{{ $banner->title ?: 'Tanpa judul' }}" akan dihapus permanen.</p>
            <div class="mt-5 flex justify-end gap-3">
                <button @click="confirmDelete = null"
                        class="rounded-lg bg-white/5 px-4 py-2 text-sm font-medium text-gray-300 hover:bg-white/10">Batal</button>
                <form action="{{ route('admin.banners.destroy', $banner) }}" method="POST">
                    @csrf @method('DELETE')
                    <button type="submit"
                            class="rounded-lg bg-red-600 px-4 py-2 text-sm font-bold text-white hover:bg-red-700">Hapus</button>
                </form>
            </div>
        </div>
    </div>
    @endforeach
</div>
@endif

</div>
@endsection
