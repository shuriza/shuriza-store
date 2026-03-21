@extends('layouts.admin')

@section('title', 'Kelola Produk')
@section('page-title', 'Produk')
@section('breadcrumb')
<span>Produk</span>
@endsection

@section('content')
<div x-data="{
    selected: [],
    allChecked: false,
    toggleAll(ids) {
        this.allChecked = !this.allChecked;
        this.selected = this.allChecked ? ids : [];
    },
    toggleOne(id) {
        this.selected.includes(id) ? this.selected = this.selected.filter(i => i !== id) : this.selected.push(id);
        this.allChecked = this.selected.length === ids.length;
    },
    confirmDelete: null
}" x-init="ids = {{ $products->pluck('id') }}">

{{-- Page Header --}}
<div class="mb-6 flex flex-wrap items-center justify-between gap-4">
    <div>
        <h1 class="text-2xl font-extrabold text-white">Kelola Produk</h1>
        <p class="mt-1 text-sm text-gray-400">{{ $products->total() }} produk ditemukan</p>
    </div>
    <a href="{{ route('admin.products.create') }}"
       class="inline-flex items-center gap-2 rounded-xl bg-peri px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-peri-dark">
        <i class="fas fa-plus"></i> Tambah Produk
    </a>
</div>

{{-- Filter Bar --}}
<form action="{{ route('admin.products.index') }}" method="GET"
      class="mb-6 rounded-2xl border border-gray-800 bg-gray-900 p-4">
    <div class="flex flex-wrap items-end gap-3">
        <div class="min-w-[220px] flex-1">
            <label class="mb-1 block text-xs font-medium text-gray-400">Cari</label>
            <div class="flex items-center gap-2 rounded-lg border border-gray-700 bg-gray-800 px-3 py-2">
                <i class="fas fa-search text-sm text-gray-500"></i>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Nama produk..." class="w-full bg-transparent text-sm text-white placeholder-gray-500 outline-none">
            </div>
        </div>
        <div>
            <label class="mb-1 block text-xs font-medium text-gray-400">Kategori</label>
            <select name="category"
                    class="rounded-lg border border-gray-700 bg-gray-800 px-3 py-2 text-sm text-white outline-none">
                <option value="">Semua</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" @selected(request('category') == $cat->id)>{{ $cat->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="mb-1 block text-xs font-medium text-gray-400">Status</label>
            <select name="status"
                    class="rounded-lg border border-gray-700 bg-gray-800 px-3 py-2 text-sm text-white outline-none">
                <option value="">Semua</option>
                <option value="active" @selected(request('status') === 'active')>Aktif</option>
                <option value="inactive" @selected(request('status') === 'inactive')>Non-aktif</option>
            </select>
        </div>
        <div>
            <label class="mb-1 block text-xs font-medium text-gray-400">Stok</label>
            <select name="stock"
                    class="rounded-lg border border-gray-700 bg-gray-800 px-3 py-2 text-sm text-white outline-none">
                <option value="">Semua</option>
                <option value="available" @selected(request('stock') === 'available')>Tersedia</option>
                <option value="empty" @selected(request('stock') === 'empty')>Habis</option>
            </select>
        </div>
        <div class="flex gap-2">
            <button type="submit"
                    class="rounded-lg bg-peri px-4 py-2 text-sm font-semibold text-white transition hover:bg-peri-dark">
                <i class="fas fa-filter"></i> Filter
            </button>
            <a href="{{ route('admin.products.index') }}"
               class="rounded-lg border border-gray-700 px-4 py-2 text-sm text-gray-400 transition hover:text-white">
                Reset
            </a>
        </div>
    </div>
</form>

{{-- Bulk Actions --}}
<div x-show="selected.length > 0" x-cloak
     class="mb-4 flex flex-wrap items-center gap-3 rounded-2xl border border-peri/30 bg-peri/5 p-3">
    <span class="text-sm font-medium text-peri" x-text="selected.length + ' dipilih'"></span>
    <form method="POST" action="{{ route('admin.products.bulk') }}" class="flex gap-2"
          x-ref="bulkForm">
        @csrf
        <template x-for="id in selected" :key="id">
            <input type="hidden" name="product_ids[]" :value="id">
        </template>
        <button type="submit" name="action" value="activate"
                class="rounded-lg bg-green-600/20 px-3 py-1.5 text-xs font-semibold text-green-400 transition hover:bg-green-600/30">
            Aktifkan
        </button>
        <button type="submit" name="action" value="deactivate"
                class="rounded-lg bg-yellow-600/20 px-3 py-1.5 text-xs font-semibold text-yellow-400 transition hover:bg-yellow-600/30">
            Non-aktifkan
        </button>
        <button type="submit" name="action" value="delete"
                onclick="return confirm('Hapus produk terpilih?')"
                class="rounded-lg bg-red-600/20 px-3 py-1.5 text-xs font-semibold text-red-400 transition hover:bg-red-600/30">
            Hapus
        </button>
    </form>
</div>

@if($products->count())
{{-- Products Table --}}
<div class="overflow-hidden rounded-2xl border border-gray-800 bg-gray-900">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm">
            <thead class="border-b border-gray-800 text-xs uppercase text-gray-500">
                <tr>
                    <th class="px-4 py-3">
                        <input type="checkbox" :checked="allChecked" @click="toggleAll(ids)"
                               class="rounded border-gray-600 bg-gray-800 text-peri focus:ring-peri">
                    </th>
                    <th class="px-4 py-3">Gambar</th>
                    <th class="px-4 py-3">Nama Produk</th>
                    <th class="px-4 py-3">Kategori</th>
                    <th class="px-4 py-3">Harga</th>
                    <th class="px-4 py-3">Stok</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-800">
                @foreach($products as $product)
                <tr class="transition hover:bg-gray-800/50">
                    <td class="px-4 py-3">
                        <input type="checkbox" :value="{{ $product->id }}"
                               :checked="selected.includes({{ $product->id }})"
                               @click="toggleOne({{ $product->id }})"
                               class="rounded border-gray-600 bg-gray-800 text-peri focus:ring-peri">
                    </td>
                    <td class="px-4 py-3">
                        <img src="{{ $product->image_url }}" alt="{{ $product->name }}"
                             class="h-10 w-10 rounded-lg object-cover">
                    </td>
                    <td class="px-4 py-3">
                        <div class="font-medium text-white">{{ $product->name }}</div>
                        <div class="flex items-center gap-2 mt-0.5">
                            @if($product->is_popular)
                                <span class="inline-flex items-center gap-1 rounded-full bg-yellow-500/10 px-2 py-0.5 text-[0.65rem] font-semibold text-yellow-400">
                                    <i class="fas fa-fire text-[0.55rem]"></i> Populer
                                </span>
                            @endif
                            @if($product->badge)
                                <span class="rounded-full bg-peri/10 px-2 py-0.5 text-[0.65rem] font-semibold text-peri">{{ $product->badge }}</span>
                            @endif
                        </div>
                    </td>
                    <td class="px-4 py-3 text-gray-400">{{ $product->category?->name ?? '-' }}</td>
                    <td class="px-4 py-3">
                        <span class="font-semibold text-white">{{ $product->formatted_price }}</span>
                        @if($product->original_price && $product->original_price > $product->price)
                            <span class="ml-1 text-xs text-gray-500 line-through">Rp {{ number_format($product->original_price, 0, ',', '.') }}</span>
                        @endif
                    </td>
                    <td class="px-4 py-3">
                        @if($product->stock > 0)
                            <span class="font-medium text-green-400">{{ $product->stock }}</span>
                        @else
                            <span class="font-medium text-red-400">Habis</span>
                        @endif
                    </td>
                    <td class="px-4 py-3">
                        <form method="POST" action="{{ route('admin.products.toggle-active', $product) }}">
                            @csrf @method('PATCH')
                            <button type="submit"
                                    class="relative h-6 w-11 rounded-full transition {{ $product->is_active ? 'bg-green-500' : 'bg-gray-600' }}">
                                <span class="absolute top-0.5 h-5 w-5 rounded-full bg-white shadow transition-all {{ $product->is_active ? 'left-[1.375rem]' : 'left-0.5' }}"></span>
                            </button>
                        </form>
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex items-center justify-end gap-1">
                            <a href="{{ route('admin.products.edit', $product) }}"
                               class="rounded-lg p-2 text-gray-400 transition hover:bg-gray-700 hover:text-white" title="Edit">
                                <i class="fas fa-pen text-xs"></i>
                            </a>
                            <form method="POST" action="{{ route('admin.products.toggle-popular', $product) }}">
                                @csrf @method('PATCH')
                                <button type="submit"
                                        class="rounded-lg p-2 transition hover:bg-gray-700 {{ $product->is_popular ? 'text-yellow-400' : 'text-gray-400 hover:text-yellow-400' }}"
                                        title="{{ $product->is_popular ? 'Hapus Populer' : 'Tandai Populer' }}">
                                    <i class="fas fa-fire text-xs"></i>
                                </button>
                            </form>
                            <form method="POST" action="{{ route('admin.products.destroy', $product) }}"
                                  onsubmit="return confirm('Yakin hapus produk {{ $product->name }}?')">
                                @csrf @method('DELETE')
                                <button type="submit"
                                        class="rounded-lg p-2 text-gray-400 transition hover:bg-red-500/10 hover:text-red-400" title="Hapus">
                                    <i class="fas fa-trash text-xs"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($products->hasPages())
    <div class="border-t border-gray-800 px-4 py-3">
        {{ $products->withQueryString()->links() }}
    </div>
    @endif
</div>

@else
{{-- Empty State --}}
<div class="flex flex-col items-center justify-center rounded-2xl border border-gray-800 bg-gray-900 py-20">
    <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-peri/10 text-peri">
        <i class="fas fa-box-open text-2xl"></i>
    </div>
    <h3 class="mt-4 text-lg font-bold text-white">Belum ada produk</h3>
    <p class="mt-1 text-sm text-gray-400">Mulai tambahkan produk pertama Anda</p>
    <a href="{{ route('admin.products.create') }}"
       class="mt-5 inline-flex items-center gap-2 rounded-xl bg-peri px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-peri-dark">
        <i class="fas fa-plus"></i> Tambah Produk
    </a>
</div>
@endif

</div>
@endsection
