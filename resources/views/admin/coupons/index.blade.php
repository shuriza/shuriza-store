@extends('layouts.admin')

@section('title', 'Kelola Kupon')
@section('page-title', 'Kupon')

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-white">Kupon & Diskon</h1>
            <p class="mt-1 text-sm text-gray-400">Kelola kupon diskon untuk pelanggan</p>
        </div>
        <a href="{{ route('admin.coupons.create') }}"
           class="inline-flex items-center gap-2 rounded-xl bg-peri px-5 py-2.5 text-sm font-bold text-white shadow-lg shadow-peri/25 transition hover:bg-peri/90">
            <i class="fas fa-plus"></i> Tambah Kupon
        </a>
    </div>

    {{-- Coupons Table --}}
    @if($coupons->count() > 0)
        <div class="overflow-hidden rounded-2xl border border-gray-800 bg-gray-900">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="border-b border-gray-800 bg-gray-900/50">
                        <tr>
                            <th class="px-4 py-3 font-semibold text-gray-400">Kode</th>
                            <th class="px-4 py-3 font-semibold text-gray-400">Nama</th>
                            <th class="px-4 py-3 font-semibold text-gray-400">Diskon</th>
                            <th class="px-4 py-3 font-semibold text-gray-400">Min. Order</th>
                            <th class="px-4 py-3 font-semibold text-gray-400">Penggunaan</th>
                            <th class="px-4 py-3 font-semibold text-gray-400">Berlaku</th>
                            <th class="px-4 py-3 font-semibold text-gray-400">Status</th>
                            <th class="px-4 py-3 font-semibold text-gray-400 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-800">
                        @foreach($coupons as $coupon)
                            <tr class="hover:bg-white/[0.02] transition">
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center gap-1.5 rounded-lg bg-peri/10 px-2.5 py-1 text-xs font-bold text-peri tracking-wider">
                                        <i class="fas fa-ticket-alt text-[10px]"></i> {{ $coupon->code }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 font-medium text-white">{{ $coupon->name }}</td>
                                <td class="px-4 py-3">
                                    <span class="font-semibold text-emerald-400">{{ $coupon->formatted_value }}</span>
                                    @if($coupon->type === 'percent' && $coupon->max_discount)
                                        <span class="block text-[11px] text-gray-500">Maks: Rp {{ number_format($coupon->max_discount, 0, ',', '.') }}</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-gray-300">
                                    @if($coupon->min_order > 0)
                                        Rp {{ number_format($coupon->min_order, 0, ',', '.') }}
                                    @else
                                        <span class="text-gray-500">-</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-gray-300">
                                    {{ $coupon->used_count }}{{ $coupon->usage_limit ? '/' . $coupon->usage_limit : '' }}
                                </td>
                                <td class="px-4 py-3 text-xs text-gray-400">
                                    @if($coupon->starts_at || $coupon->expires_at)
                                        {{ $coupon->starts_at?->format('d M Y') ?? '...' }} - {{ $coupon->expires_at?->format('d M Y') ?? '...' }}
                                    @else
                                        <span class="text-gray-500">Tanpa batas</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    @php $label = $coupon->status_label; @endphp
                                    <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-1 text-xs font-semibold
                                        {{ $label === 'Aktif' ? 'bg-emerald-500/10 text-emerald-400' : ($label === 'Nonaktif' ? 'bg-gray-500/10 text-gray-400' : 'bg-red-500/10 text-red-400') }}">
                                        {{ $label }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <div class="flex items-center justify-end gap-1">
                                        <a href="{{ route('admin.coupons.edit', $coupon) }}" class="rounded-lg p-2 text-gray-400 transition hover:bg-white/5 hover:text-white" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.coupons.toggle', $coupon) }}" method="POST">
                                            @csrf @method('PATCH')
                                            <button type="submit" class="rounded-lg p-2 transition {{ $coupon->is_active ? 'text-amber-400 hover:bg-amber-500/10' : 'text-emerald-400 hover:bg-emerald-500/10' }}" title="{{ $coupon->is_active ? 'Nonaktifkan' : 'Aktifkan' }}">
                                                <i class="fas {{ $coupon->is_active ? 'fa-toggle-on' : 'fa-toggle-off' }}"></i>
                                            </button>
                                        </form>
                                        <form action="{{ route('admin.coupons.destroy', $coupon) }}" method="POST" onsubmit="return confirm('Hapus kupon ini?')">
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
        </div>
        <div class="mt-4">{{ $coupons->links() }}</div>
    @else
        <div class="rounded-2xl border border-gray-800 bg-gray-900 p-12 text-center">
            <i class="fas fa-ticket-alt text-4xl text-gray-600 mb-3"></i>
            <p class="text-gray-400 mb-4">Belum ada kupon.</p>
            <a href="{{ route('admin.coupons.create') }}" class="inline-flex items-center gap-2 rounded-xl bg-peri px-5 py-2.5 text-sm font-bold text-white">
                <i class="fas fa-plus"></i> Buat Kupon Pertama
            </a>
        </div>
    @endif
</div>
@endsection
