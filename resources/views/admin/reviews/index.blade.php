@extends('layouts.admin')

@section('title', 'Kelola Ulasan')
@section('page-title', 'Ulasan Produk')

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-white">Ulasan Produk</h1>
            <p class="mt-1 text-sm text-gray-400">Kelola ulasan dari pelanggan</p>
        </div>
        {{-- Filter --}}
        <div class="flex gap-2">
            <a href="{{ route('admin.reviews.index') }}"
               class="rounded-xl px-4 py-2 text-sm font-semibold transition {{ !request('status') ? 'bg-peri text-white' : 'bg-gray-800 text-gray-400 hover:text-white' }}">
                Semua
            </a>
            <a href="{{ route('admin.reviews.index', ['status' => 'approved']) }}"
               class="rounded-xl px-4 py-2 text-sm font-semibold transition {{ request('status') === 'approved' ? 'bg-emerald-600 text-white' : 'bg-gray-800 text-gray-400 hover:text-white' }}">
                Disetujui
            </a>
            <a href="{{ route('admin.reviews.index', ['status' => 'hidden']) }}"
               class="rounded-xl px-4 py-2 text-sm font-semibold transition {{ request('status') === 'hidden' ? 'bg-red-600 text-white' : 'bg-gray-800 text-gray-400 hover:text-white' }}">
                Disembunyikan
            </a>
        </div>
    </div>

    {{-- Reviews Table --}}
    @if($reviews->count() > 0)
        <div class="overflow-hidden rounded-2xl border border-gray-800 bg-gray-900">
            <table class="w-full text-left text-sm">
                <thead class="border-b border-gray-800 bg-gray-900/50">
                    <tr>
                        <th class="px-4 py-3 font-semibold text-gray-400">Pelanggan</th>
                        <th class="px-4 py-3 font-semibold text-gray-400">Produk</th>
                        <th class="px-4 py-3 font-semibold text-gray-400">Rating</th>
                        <th class="px-4 py-3 font-semibold text-gray-400">Komentar</th>
                        <th class="px-4 py-3 font-semibold text-gray-400">Status</th>
                        <th class="px-4 py-3 font-semibold text-gray-400 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-800">
                    @foreach($reviews as $review)
                        <tr class="hover:bg-white/[0.02] transition">
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2">
                                    <div class="flex h-8 w-8 items-center justify-center rounded-full bg-peri/10 text-xs font-bold text-peri">
                                        {{ strtoupper(substr($review->user->name ?? '?', 0, 1)) }}
                                    </div>
                                    <div>
                                        <span class="font-medium text-white">{{ $review->user->name ?? 'Dihapus' }}</span>
                                        <span class="block text-[11px] text-gray-500">{{ $review->created_at->format('d M Y') }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <a href="{{ route('products.show', $review->product->slug ?? '#') }}" target="_blank"
                                   class="font-medium text-peri hover:underline">
                                    {{ Str::limit($review->product->name ?? '-', 30) }}
                                </a>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-0.5">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="fas fa-star text-[10px] {{ $i <= $review->rating ? 'text-amber-400' : 'text-gray-600' }}"></i>
                                    @endfor
                                </div>
                            </td>
                            <td class="max-w-xs px-4 py-3">
                                <p class="truncate text-gray-300">{{ $review->comment ?: '-' }}</p>
                            </td>
                            <td class="px-4 py-3">
                                @if($review->is_approved)
                                    <span class="inline-flex items-center gap-1 rounded-full bg-emerald-500/10 px-2.5 py-1 text-xs font-semibold text-emerald-400">
                                        <i class="fas fa-check-circle text-[10px]"></i> Disetujui
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 rounded-full bg-red-500/10 px-2.5 py-1 text-xs font-semibold text-red-400">
                                        <i class="fas fa-eye-slash text-[10px]"></i> Disembunyikan
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex items-center justify-end gap-1">
                                    <form action="{{ route('admin.reviews.toggle', $review) }}" method="POST">
                                        @csrf @method('PATCH')
                                        <button type="submit" title="{{ $review->is_approved ? 'Sembunyikan' : 'Setujui' }}"
                                                class="rounded-lg p-2 transition {{ $review->is_approved ? 'text-amber-400 hover:bg-amber-500/10' : 'text-emerald-400 hover:bg-emerald-500/10' }}">
                                            <i class="fas {{ $review->is_approved ? 'fa-eye-slash' : 'fa-check' }}"></i>
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.reviews.destroy', $review) }}" method="POST"
                                          onsubmit="return confirm('Hapus review ini?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="rounded-lg p-2 text-red-400 transition hover:bg-red-500/10" title="Hapus">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $reviews->withQueryString()->links() }}</div>
    @else
        <div class="rounded-2xl border border-gray-800 bg-gray-900 p-12 text-center">
            <i class="fas fa-comment-dots text-4xl text-gray-600 mb-3"></i>
            <p class="text-gray-400">Belum ada ulasan{{ request('status') ? ' dengan filter ini' : '' }}.</p>
        </div>
    @endif
</div>
@endsection
