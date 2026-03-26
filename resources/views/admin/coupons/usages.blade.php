@extends('layouts.admin')

@section('title', 'Riwayat Penggunaan Kupon – ' . $coupon->code)
@section('page-title', 'Kupon')
@section('breadcrumb')
<a href="{{ route('admin.coupons.index') }}" class="text-gray-400 hover:text-white">Kupon</a>
<i class="fas fa-chevron-right mx-1 text-[0.5rem] text-gray-600"></i>
<span>{{ $coupon->code }}</span>
<i class="fas fa-chevron-right mx-1 text-[0.5rem] text-gray-600"></i>
<span>Riwayat Penggunaan</span>
@endsection

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-white">
                Riwayat Penggunaan
                <span class="ml-2 rounded-lg bg-peri/10 px-3 py-1 text-lg text-peri tracking-wider">{{ $coupon->code }}</span>
            </h1>
            <p class="mt-1 text-sm text-gray-400">{{ $coupon->name }}{{ $coupon->campaign_name ? ' · Campaign: ' . $coupon->campaign_name : '' }}</p>
        </div>
        <a href="{{ route('admin.coupons.index') }}"
           class="inline-flex items-center gap-2 rounded-xl border border-gray-700 px-4 py-2.5 text-sm font-medium text-gray-400 transition hover:text-white">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="rounded-2xl border border-gray-800 bg-gray-900 p-5">
            <div class="text-xs text-gray-500 uppercase tracking-wide mb-1">Total Digunakan</div>
            <div class="text-2xl font-extrabold text-white">{{ number_format($stats['total_uses']) }}</div>
            @if($coupon->usage_limit)
            <div class="text-xs text-gray-500 mt-0.5">dari {{ number_format($coupon->usage_limit) }} batas</div>
            @endif
        </div>
        <div class="rounded-2xl border border-gray-800 bg-gray-900 p-5">
            <div class="text-xs text-gray-500 uppercase tracking-wide mb-1">Total Diskon</div>
            <div class="text-xl font-extrabold text-green-400">Rp {{ number_format($stats['total_discount'], 0, ',', '.') }}</div>
        </div>
        <div class="rounded-2xl border border-gray-800 bg-gray-900 p-5">
            <div class="text-xs text-gray-500 uppercase tracking-wide mb-1">Pengguna Unik</div>
            <div class="text-2xl font-extrabold text-blue-400">{{ number_format($stats['unique_users']) }}</div>
        </div>
        <div class="rounded-2xl border border-gray-800 bg-gray-900 p-5">
            <div class="text-xs text-gray-500 uppercase tracking-wide mb-1">Terakhir Dipakai</div>
            <div class="text-sm font-bold text-white">
                {{ $stats['last_used_at'] ? $stats['last_used_at']->format('d M Y') : '-' }}
            </div>
        </div>
    </div>

    {{-- Coupon Details --}}
    <div class="rounded-2xl border border-gray-800 bg-gray-900 p-5">
        <h3 class="text-sm font-bold text-white mb-4"><i class="fas fa-info-circle text-peri mr-2"></i>Detail Kupon</h3>
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-sm">
            <div>
                <dt class="text-gray-500 text-xs mb-0.5">Tipe Diskon</dt>
                <dd class="font-semibold text-white">{{ $coupon->type === 'percent' ? 'Persentase' : 'Nominal' }}</dd>
            </div>
            <div>
                <dt class="text-gray-500 text-xs mb-0.5">Nilai</dt>
                <dd class="font-semibold text-emerald-400">{{ $coupon->formatted_value }}</dd>
            </div>
            <div>
                <dt class="text-gray-500 text-xs mb-0.5">Min. Order</dt>
                <dd class="font-semibold text-white">{{ $coupon->min_order > 0 ? 'Rp ' . number_format($coupon->min_order, 0, ',', '.') : 'Tidak ada' }}</dd>
            </div>
            <div>
                <dt class="text-gray-500 text-xs mb-0.5">Status</dt>
                <dd>
                    @php $label = $coupon->status_label; @endphp
                    <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-semibold
                        {{ $label === 'Aktif' ? 'bg-emerald-500/10 text-emerald-400' : ($label === 'Nonaktif' ? 'bg-gray-500/10 text-gray-400' : 'bg-red-500/10 text-red-400') }}">
                        {{ $label }}
                    </span>
                </dd>
            </div>
        </div>
    </div>

    {{-- Usage History Table --}}
    <div class="overflow-hidden rounded-2xl border border-gray-800 bg-gray-900">
        <div class="px-5 py-4 border-b border-gray-800">
            <h3 class="text-sm font-bold text-white"><i class="fas fa-history text-peri mr-2"></i>Riwayat Penggunaan ({{ $usages->total() }})</h3>
        </div>
        @if($usages->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="border-b border-gray-800 text-xs uppercase tracking-wider text-gray-500">
                    <tr>
                        <th class="px-5 py-3">No. Order</th>
                        <th class="px-5 py-3">Pelanggan</th>
                        <th class="px-5 py-3">Akun</th>
                        <th class="px-5 py-3 text-right">Diskon</th>
                        <th class="px-5 py-3">Digunakan</th>
                        <th class="px-5 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-800">
                    @foreach($usages as $usage)
                    <tr class="hover:bg-white/[0.02] transition">
                        <td class="px-5 py-3">
                            <span class="font-mono text-xs font-semibold text-white">{{ $usage->order->order_number ?? '-' }}</span>
                            @if($usage->order)
                            <p class="text-xs text-gray-500 mt-0.5">{{ $usage->order->status_label }}</p>
                            @endif
                        </td>
                        <td class="px-5 py-3">
                            <p class="font-medium text-white">{{ $usage->order->name ?? '-' }}</p>
                            <p class="text-xs text-gray-500">{{ $usage->order->email ?? ($usage->order->phone ?? '-') }}</p>
                        </td>
                        <td class="px-5 py-3">
                            @if($usage->user)
                                <p class="text-sm text-gray-300">{{ $usage->user->name }}</p>
                                <p class="text-xs text-gray-500">{{ $usage->user->email }}</p>
                            @else
                                <span class="text-xs text-gray-500 italic">Tamu</span>
                            @endif
                        </td>
                        <td class="px-5 py-3 text-right font-semibold text-green-400">
                            Rp {{ number_format($usage->discount_amount, 0, ',', '.') }}
                        </td>
                        <td class="px-5 py-3 text-gray-400 text-xs">
                            {{ $usage->used_at ? $usage->used_at->format('d M Y, H:i') : '-' }}
                        </td>
                        <td class="px-5 py-3 text-right">
                            @if($usage->order)
                            <a href="{{ route('admin.orders.show', $usage->order) }}"
                               class="rounded-lg bg-peri/10 px-3 py-1.5 text-xs font-semibold text-peri transition hover:bg-peri/20">
                                <i class="fas fa-eye"></i>
                            </a>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-5 py-4 border-t border-gray-800">
            {{ $usages->links() }}
        </div>
        @else
        <div class="py-12 text-center">
            <i class="fas fa-ticket-alt text-3xl text-gray-700 mb-3"></i>
            <p class="text-sm text-gray-500">Belum ada penggunaan untuk kupon ini.</p>
        </div>
        @endif
    </div>

</div>
@endsection
