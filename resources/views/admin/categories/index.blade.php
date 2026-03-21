@extends('layouts.admin')

@section('title', 'Kelola Kategori')
@section('page-title', 'Kategori')
@section('breadcrumb')
<span>Kategori</span>
@endsection

@section('content')
<div x-data="{ confirmDelete: null }">

{{-- Page Header --}}
<div class="mb-6 flex flex-wrap items-center justify-between gap-4">
    <div>
        <h1 class="text-2xl font-extrabold text-white">Kelola Kategori</h1>
        <p class="mt-1 text-sm text-gray-400">{{ $categories->total() }} kategori ditemukan</p>
    </div>
    <a href="{{ route('admin.categories.create') }}"
       class="inline-flex items-center gap-2 rounded-xl bg-peri px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-peri-dark">
        <i class="fas fa-plus"></i> Tambah Kategori
    </a>
</div>

@if($categories->count())
{{-- Categories Table --}}
<div class="overflow-hidden rounded-2xl border border-gray-800 bg-gray-900">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm">
            <thead class="border-b border-gray-800 text-xs uppercase tracking-wider text-gray-500">
                <tr>
                    <th class="px-4 py-3">Icon</th>
                    <th class="px-4 py-3">Nama</th>
                    <th class="px-4 py-3">Slug</th>
                    <th class="px-4 py-3">Produk Aktif</th>
                    <th class="px-4 py-3">Urutan</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-800">
                @foreach($categories as $category)
                <tr class="transition hover:bg-gray-800/50">
                    <td class="px-4 py-3">
                        <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-gray-800">
                            <i class="{{ $category->icon }}" style="color: {{ $category->color }}"></i>
                        </div>
                    </td>
                    <td class="px-4 py-3 font-medium text-white">{{ $category->name }}</td>
                    <td class="px-4 py-3 text-gray-400">{{ $category->slug }}</td>
                    <td class="px-4 py-3">
                        <span class="inline-flex items-center gap-1 rounded-full bg-peri/10 px-2.5 py-0.5 text-xs font-semibold text-peri">
                            {{ $category->active_products_count }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-gray-400">
                        <div class="flex items-center gap-1">
                            <span>{{ $category->sort_order }}</span>
                            <div class="flex flex-col ml-1">
                                <button onclick="moveCategoryOrder({{ $category->id }}, 'up')"
                                        class="text-gray-500 hover:text-peri transition text-[10px] leading-none p-0.5"
                                        title="Naikkan">
                                    <i class="fas fa-chevron-up"></i>
                                </button>
                                <button onclick="moveCategoryOrder({{ $category->id }}, 'down')"
                                        class="text-gray-500 hover:text-peri transition text-[10px] leading-none p-0.5"
                                        title="Turunkan">
                                    <i class="fas fa-chevron-down"></i>
                                </button>
                            </div>
                        </div>
                    </td>
                    <td class="px-4 py-3">
                        <form action="{{ route('admin.categories.toggle-active', $category) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-semibold transition
                                {{ $category->is_active
                                    ? 'bg-green-500/10 text-green-400 hover:bg-green-500/20'
                                    : 'bg-red-500/10 text-red-400 hover:bg-red-500/20' }}">
                                <span class="h-1.5 w-1.5 rounded-full {{ $category->is_active ? 'bg-green-400' : 'bg-red-400' }}"></span>
                                {{ $category->is_active ? 'Aktif' : 'Non-aktif' }}
                            </button>
                        </form>
                    </td>
                    <td class="px-4 py-3 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('admin.categories.edit', $category) }}"
                               class="rounded-lg bg-peri/10 px-3 py-1.5 text-xs font-semibold text-peri transition hover:bg-peri/20">
                                <i class="fas fa-edit"></i>
                            </a>
                            <button @click="confirmDelete = {{ $category->id }}"
                                    class="rounded-lg bg-red-500/10 px-3 py-1.5 text-xs font-semibold text-red-400 transition hover:bg-red-500/20">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>

                        {{-- Delete Confirmation --}}
                        <div x-show="confirmDelete === {{ $category->id }}" x-cloak x-transition
                             class="mt-2 rounded-lg border border-red-500/20 bg-red-500/5 p-3 text-left">
                            <p class="mb-2 text-xs text-red-400">Hapus "{{ $category->name }}"?</p>
                            <div class="flex gap-2">
                                <form action="{{ route('admin.categories.destroy', $category) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="rounded-lg bg-red-600 px-3 py-1 text-xs font-semibold text-white transition hover:bg-red-700">
                                        Hapus
                                    </button>
                                </form>
                                <button @click="confirmDelete = null"
                                        class="rounded-lg border border-gray-700 px-3 py-1 text-xs text-gray-400 transition hover:text-white">
                                    Batal
                                </button>
                            </div>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

{{-- Pagination --}}
<div class="mt-6">
    {{ $categories->links() }}
</div>
@else
<div class="rounded-2xl border border-gray-800 bg-gray-900 py-16 text-center">
    <i class="fas fa-tags mb-4 text-4xl text-gray-700"></i>
    <p class="text-gray-400">Belum ada kategori.</p>
    <a href="{{ route('admin.categories.create') }}"
       class="mt-4 inline-flex items-center gap-2 rounded-xl bg-peri px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-peri-dark">
        <i class="fas fa-plus"></i> Tambah Kategori
    </a>
</div>
@endif

</div>

@push('scripts')
<script>
function moveCategoryOrder(categoryId, direction) {
    const rows = [...document.querySelectorAll('tbody tr')];
    const ids = rows.map(row => {
        const idMatch = row.innerHTML.match(/moveCategoryOrder\((\d+)/);
        return idMatch ? parseInt(idMatch[1]) : null;
    }).filter(Boolean);

    const idx = ids.indexOf(categoryId);
    if (idx === -1) return;
    if (direction === 'up' && idx === 0) return;
    if (direction === 'down' && idx === ids.length - 1) return;

    const swapIdx = direction === 'up' ? idx - 1 : idx + 1;
    [ids[idx], ids[swapIdx]] = [ids[swapIdx], ids[idx]];

    fetch('{{ route("admin.categories.update-order") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
        },
        body: JSON.stringify({ orders: ids }),
    }).then(res => res.json()).then(data => {
        if (data.success) location.reload();
    });
}
</script>
@endpush
@endsection
