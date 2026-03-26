@extends('layouts.admin')
@section('title', 'Laporan Penjualan')
@section('page-title', 'Laporan')
@section('breadcrumb') <span>Laporan</span> @endsection

@section('content')
<div>
    {{-- Header --}}
    <div class="mb-6">
        <h1 class="text-2xl font-extrabold text-white">Laporan Penjualan</h1>
        <p class="mt-1 text-sm text-gray-400">Analisis performa toko</p>
    </div>

    {{-- Date Filter --}}
    <form action="{{ route('admin.reports.index') }}" method="GET"
          class="mb-6 rounded-2xl border border-gray-800 bg-gray-900 p-4">
        <div class="flex flex-wrap items-end gap-3">
            <div>
                <label class="mb-1 block text-xs font-medium text-gray-400">Dari Tanggal</label>
                <input type="date" name="date_from" value="{{ $dateFrom }}"
                       class="rounded-lg border border-gray-700 bg-gray-800 px-3 py-2 text-sm text-white">
            </div>
            <div>
                <label class="mb-1 block text-xs font-medium text-gray-400">Sampai Tanggal</label>
                <input type="date" name="date_to" value="{{ $dateTo }}"
                       class="rounded-lg border border-gray-700 bg-gray-800 px-3 py-2 text-sm text-white">
            </div>
            <button type="submit" class="rounded-lg bg-peri px-4 py-2 text-sm font-semibold text-white hover:bg-peri-dark">
                <i class="fas fa-filter mr-1"></i> Tampilkan
            </button>
            <a href="{{ route('admin.reports.export', ['date_from' => $dateFrom, 'date_to' => $dateTo]) }}"
               class="rounded-lg bg-green-600 px-4 py-2 text-sm font-semibold text-white hover:bg-green-700">
                <i class="fas fa-file-excel mr-1"></i> Export Excel
            </a>
        </div>
    </form>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
        <div class="rounded-2xl border border-gray-800 bg-gray-900 p-5">
            <div class="text-xs text-gray-500 uppercase tracking-wide mb-1">Total Pesanan</div>
            <div class="text-2xl font-extrabold text-white">{{ number_format($revenue->total_orders ?? 0) }}</div>
        </div>
        <div class="rounded-2xl border border-gray-800 bg-gray-900 p-5">
            <div class="text-xs text-gray-500 uppercase tracking-wide mb-1">Total Pendapatan</div>
            <div class="text-2xl font-extrabold text-green-400">Rp {{ number_format($revenue->total_revenue ?? 0, 0, ',', '.') }}</div>
        </div>
        <div class="rounded-2xl border border-gray-800 bg-gray-900 p-5">
            <div class="text-xs text-gray-500 uppercase tracking-wide mb-1">Rata-rata Order</div>
            <div class="text-2xl font-extrabold text-peri">Rp {{ number_format($revenue->avg_order ?? 0, 0, ',', '.') }}</div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        {{-- Status Breakdown --}}
        <div class="rounded-2xl border border-gray-800 bg-gray-900 p-5">
            <h3 class="text-sm font-bold text-white mb-4"><i class="fas fa-chart-pie text-peri mr-2"></i>Status Pesanan</h3>
            <div class="space-y-3">
                @php
                    $statuses = ['pending' => ['Menunggu', 'yellow'], 'processing' => ['Diproses', 'blue'], 'completed' => ['Selesai', 'green'], 'cancelled' => ['Dibatalkan', 'red']];
                @endphp
                @foreach($statuses as $key => [$label, $color])
                @php $data = $statusBreakdown->get($key); @endphp
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full bg-{{ $color }}-500"></span>
                        <span class="text-sm text-gray-300">{{ $label }}</span>
                    </div>
                    <div class="text-right">
                        <span class="text-sm font-bold text-white">{{ $data->count ?? 0 }}</span>
                        <span class="text-xs text-gray-500 ml-2">Rp {{ number_format($data->total ?? 0, 0, ',', '.') }}</span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Daily Chart --}}
        <div class="rounded-2xl border border-gray-800 bg-gray-900 p-5">
            <h3 class="text-sm font-bold text-white mb-4"><i class="fas fa-chart-bar text-peri mr-2"></i>Pendapatan Harian</h3>
            @if($dailyRevenue->isEmpty())
                <p class="text-sm text-gray-500 text-center py-8">Tidak ada data pada periode ini.</p>
            @else
            <div class="space-y-2 max-h-64 overflow-y-auto">
                @php $maxRev = $dailyRevenue->max('revenue') ?: 1; @endphp
                @foreach($dailyRevenue as $day)
                <div class="flex items-center gap-3">
                    <span class="text-xs text-gray-500 w-20 shrink-0">{{ \Carbon\Carbon::parse($day->date)->format('d M') }}</span>
                    <div class="flex-1 h-6 bg-gray-800 rounded-full overflow-hidden">
                        <div class="h-full bg-peri rounded-full" style="width: {{ ($day->revenue / $maxRev) * 100 }}%"></div>
                    </div>
                    <span class="text-xs text-gray-400 w-28 text-right shrink-0">Rp {{ number_format($day->revenue, 0, ',', '.') }}</span>
                </div>
                @endforeach
            </div>
            @endif
        </div>
    </div>

    {{-- Top Products --}}
    <div class="rounded-2xl border border-gray-800 bg-gray-900 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-800">
            <h3 class="text-sm font-bold text-white"><i class="fas fa-trophy text-amber-400 mr-2"></i>Produk Terlaris</h3>
        </div>
        @if($topProducts->isEmpty())
            <p class="text-sm text-gray-500 text-center py-8">Tidak ada data penjualan pada periode ini.</p>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-800">
                        <th class="px-5 py-3 text-left font-semibold text-gray-400">#</th>
                        <th class="px-5 py-3 text-left font-semibold text-gray-400">Produk</th>
                        <th class="px-5 py-3 text-right font-semibold text-gray-400">Terjual</th>
                        <th class="px-5 py-3 text-right font-semibold text-gray-400">Pendapatan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-800/50">
                    @foreach($topProducts as $i => $product)
                    <tr>
                        <td class="px-5 py-3 text-gray-500">{{ $i + 1 }}</td>
                        <td class="px-5 py-3 font-semibold text-white">{{ $product->name }}</td>
                        <td class="px-5 py-3 text-right text-gray-300">{{ number_format($product->sold) }}</td>
                        <td class="px-5 py-3 text-right text-green-400 font-semibold">Rp {{ number_format($product->revenue ?? 0, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>

    {{-- Advanced Analytics --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">

        {{-- Category Revenue --}}
        <div class="rounded-2xl border border-gray-800 bg-gray-900 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-800">
                <h3 class="text-sm font-bold text-white"><i class="fas fa-layer-group text-blue-400 mr-2"></i>Pendapatan per Kategori</h3>
            </div>
            @if($categoryRevenue->isEmpty())
                <p class="text-sm text-gray-500 text-center py-8">Tidak ada data pada periode ini.</p>
            @else
            @php $maxCatRev = $categoryRevenue->max('cat_revenue') ?: 1; @endphp
            <div class="p-5 space-y-3">
                @foreach($categoryRevenue as $cat)
                <div>
                    <div class="flex justify-between items-center mb-1">
                        <span class="text-xs font-medium text-gray-300">{{ $cat->name }}</span>
                        <span class="text-xs text-gray-400">{{ number_format($cat->cat_sold) }} terjual · Rp {{ number_format($cat->cat_revenue, 0, ',', '.') }}</span>
                    </div>
                    <div class="h-2 bg-gray-800 rounded-full overflow-hidden">
                        <div class="h-full bg-blue-500 rounded-full" style="width: {{ ($cat->cat_revenue / $maxCatRev) * 100 }}%"></div>
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        </div>

        {{-- Coupon & Customer Stats --}}
        <div class="space-y-6">

            {{-- Customer Stats --}}
            <div class="rounded-2xl border border-gray-800 bg-gray-900 p-5">
                <h3 class="text-sm font-bold text-white mb-4"><i class="fas fa-users text-emerald-400 mr-2"></i>Statistik Pelanggan</h3>
                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-emerald-500/10 rounded-xl p-3 text-center">
                        <div class="text-2xl font-extrabold text-emerald-400">{{ number_format($newCustomers) }}</div>
                        <div class="text-xs text-gray-400 mt-1">Pelanggan Baru</div>
                    </div>
                    <div class="bg-blue-500/10 rounded-xl p-3 text-center">
                        <div class="text-2xl font-extrabold text-blue-400">{{ number_format($returningCustomers) }}</div>
                        <div class="text-xs text-gray-400 mt-1">Pelanggan Lama</div>
                    </div>
                </div>
            </div>

            {{-- Coupon Stats --}}
            <div class="rounded-2xl border border-gray-800 bg-gray-900 p-5">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-bold text-white"><i class="fas fa-ticket-alt text-amber-400 mr-2"></i>Statistik Kupon</h3>
                    <span class="text-xs text-gray-400">Total diskon: <span class="text-red-400 font-semibold">Rp {{ number_format($totalDiscount, 0, ',', '.') }}</span></span>
                </div>
                @if($couponStats->isEmpty())
                    <p class="text-xs text-gray-500 text-center py-4">Tidak ada kupon yang digunakan.</p>
                @else
                <div class="space-y-2">
                    @foreach($couponStats as $cs)
                    <div class="flex items-center justify-between py-1.5 border-b border-gray-800/50 last:border-0">
                        <div class="flex items-center gap-2">
                            <span class="inline-flex items-center gap-1 rounded-lg bg-peri/10 px-2 py-0.5 text-xs font-bold text-peri tracking-wider">{{ $cs->coupon_code }}</span>
                            <span class="text-xs text-gray-400">{{ $cs->times_used }}× digunakan</span>
                        </div>
                        <span class="text-xs text-red-400 font-semibold">-Rp {{ number_format($cs->total_discount, 0, ',', '.') }}</span>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>

        </div>

    </div>
</div>
@endsection
