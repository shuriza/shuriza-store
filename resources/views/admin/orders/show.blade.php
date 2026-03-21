@extends('layouts.admin')

@section('title', 'Detail Pesanan #' . $order->order_number)
@section('page-title', 'Detail Pesanan')
@section('breadcrumb')
<a href="{{ route('admin.orders.index') }}" class="text-gray-400 hover:text-white">Pesanan</a>
<i class="fas fa-chevron-right mx-1 text-[0.5rem] text-gray-600"></i>
<span>#{{ $order->order_number }}</span>
@endsection

@section('content')
<div x-data="{ confirmDelete: false }">

{{-- Page Header --}}
<div class="mb-6 flex flex-wrap items-center justify-between gap-4">
    <div>
        <h1 class="text-2xl font-extrabold text-white">Detail Pesanan #{{ $order->order_number }}</h1>
        <p class="mt-1 text-sm text-gray-400">Dibuat {{ $order->created_at->format('d M Y, H:i') }}</p>
    </div>
    <a href="{{ route('admin.orders.index') }}"
       class="inline-flex items-center gap-2 rounded-xl border border-gray-700 px-4 py-2.5 text-sm font-medium text-gray-400 transition hover:text-white">
        <i class="fas fa-arrow-left"></i> Kembali
    </a>
</div>

{{-- Two-Column Layout --}}
<div class="grid gap-6 lg:grid-cols-3">

    {{-- Left Column (2/3) --}}
    <div class="space-y-6 lg:col-span-2">

        {{-- Items Card --}}
        <div class="rounded-2xl border border-gray-800 bg-gray-900 p-6">
            <h2 class="mb-4 text-lg font-bold text-white">
                <i class="fas fa-box mr-2 text-peri"></i>Item Pesanan
            </h2>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="border-b border-gray-800 text-xs uppercase tracking-wider text-gray-500">
                        <tr>
                            <th class="pb-3 pr-4">Produk</th>
                            <th class="pb-3 px-4 text-center">Qty</th>
                            <th class="pb-3 px-4 text-right">Harga</th>
                            <th class="pb-3 pl-4 text-right">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-800">
                        @foreach($order->items as $item)
                        <tr>
                            <td class="py-3 pr-4">
                                <p class="font-medium text-white">{{ $item->product_name }}</p>
                                @if($item->product)
                                    <a href="{{ route('admin.products.edit', $item->product->id) }}"
                                       class="text-xs text-peri hover:underline">Edit produk</a>
                                @else
                                    <span class="text-xs italic text-gray-500">Produk dihapus</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center text-white">{{ $item->quantity }}</td>
                            <td class="px-4 py-3 text-right text-gray-400">{{ $item->formatted_price }}</td>
                            <td class="py-3 pl-4 text-right font-semibold text-white">{{ $item->formatted_subtotal }}</td>
                        </tr>
                        {{-- Digital Delivery --}}
                        <tr>
                            <td colspan="4" class="pb-3">
                                @if($item->delivered_at)
                                    <div class="flex items-start gap-2 px-3 py-2 bg-green-900/20 border border-green-800/30 rounded-xl">
                                        <i class="fas fa-check-circle text-green-400 mt-0.5"></i>
                                        <div class="flex-1 min-w-0">
                                            <span class="text-xs text-green-400 font-medium">Terkirim {{ $item->delivered_at->format('d M Y H:i') }}</span>
                                            <p class="text-xs text-gray-300 font-mono mt-1 break-all">{{ $item->delivery_data }}</p>
                                        </div>
                                    </div>
                                @else
                                    <div x-data="{ open: false }">
                                        <button @click="open = !open" type="button"
                                                class="text-xs text-peri hover:text-peri/80 font-medium flex items-center gap-1">
                                            <i class="fas fa-paper-plane"></i> Kirim Data Produk Digital
                                        </button>
                                        <form x-show="open" x-cloak method="POST"
                                              action="{{ route('admin.orders.deliver-item', [$order, $item]) }}"
                                              class="mt-2 p-3 bg-gray-800 rounded-xl border border-gray-700 space-y-2">
                                            @csrf
                                            <select name="delivery_type" class="w-full bg-gray-900 border border-gray-700 rounded-lg px-3 py-2 text-sm text-white">
                                                <option value="account">Akun (username & password)</option>
                                                <option value="link">Link Download</option>
                                                <option value="code">Kode / Voucher</option>
                                                <option value="other">Lainnya</option>
                                            </select>
                                            <textarea name="delivery_data" rows="2" required placeholder="Masukkan data produk digital..."
                                                      class="w-full bg-gray-900 border border-gray-700 rounded-lg px-3 py-2 text-sm text-white placeholder-gray-500"></textarea>
                                            <button type="submit" class="px-4 py-1.5 bg-peri text-white text-xs font-semibold rounded-lg hover:bg-peri/80 transition">
                                                <i class="fas fa-paper-plane mr-1"></i> Kirim
                                            </button>
                                        </form>
                                    </div>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="border-t border-gray-700">
                        <tr>
                            <td colspan="3" class="py-3 pr-4 text-right text-sm font-bold text-gray-400">Total</td>
                            <td class="py-3 pl-4 text-right text-lg font-extrabold text-white">{{ $order->formatted_total }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        {{-- Notes Card --}}
        <div class="rounded-2xl border border-gray-800 bg-gray-900 p-6">
            <h2 class="mb-3 text-lg font-bold text-white">
                <i class="fas fa-sticky-note mr-2 text-yellow-400"></i>Catatan Pelanggan
            </h2>
            @if($order->notes)
                <p class="text-sm leading-relaxed text-gray-300">{{ $order->notes }}</p>
            @else
                <p class="text-sm italic text-gray-500">Tidak ada catatan dari pelanggan.</p>
            @endif
        </div>

        {{-- Admin Notes --}}
        <div class="rounded-2xl border border-gray-800 bg-gray-900 p-6">
            <h2 class="mb-3 text-lg font-bold text-white">
                <i class="fas fa-pen-square mr-2 text-peri"></i>Catatan Admin
            </h2>
            <form action="{{ route('admin.orders.update-notes', $order) }}" method="POST">
                @csrf
                @method('PATCH')
                <textarea name="admin_notes" rows="3" placeholder="Tambahkan catatan internal..."
                          class="w-full rounded-xl border border-gray-700 bg-gray-800 px-4 py-3 text-sm text-white placeholder-gray-500 focus:border-peri focus:outline-none focus:ring-1 focus:ring-peri resize-none">{{ $order->admin_notes }}</textarea>
                <button type="submit" class="mt-2 w-full rounded-xl bg-peri/10 px-4 py-2 text-sm font-semibold text-peri transition hover:bg-peri/20">
                    <i class="fas fa-save mr-1"></i> Simpan Catatan
                </button>
            </form>
        </div>
    </div>

    {{-- Right Column (1/3) --}}
    <div class="space-y-6">

        {{-- Order Info Card --}}
        <div class="rounded-2xl border border-gray-800 bg-gray-900 p-6">
            <h2 class="mb-4 text-lg font-bold text-white">Info Pesanan</h2>
            <dl class="space-y-3 text-sm">
                <div class="flex justify-between">
                    <dt class="text-gray-400">No. Order</dt>
                    <dd class="font-mono font-semibold text-white">{{ $order->order_number }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-400">Tanggal</dt>
                    <dd class="text-white">{{ $order->created_at->format('d M Y, H:i') }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-400">Status</dt>
                    <dd>
                        @php
                            $badgeClass = match($order->status) {
                                'pending'    => 'bg-yellow-500/10 text-yellow-400',
                                'processing' => 'bg-blue-500/10 text-blue-400',
                                'completed'  => 'bg-green-500/10 text-green-400',
                                'cancelled'  => 'bg-red-500/10 text-red-400',
                                default      => 'bg-gray-500/10 text-gray-400',
                            };
                        @endphp
                        <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-semibold {{ $badgeClass }}">
                            <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
                            {{ $order->status_label }}
                        </span>
                    </dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-400">Total</dt>
                    <dd class="font-bold text-white">{{ $order->formatted_total }}</dd>
                </div>
            </dl>
        </div>

        {{-- Update Status --}}
        <div class="rounded-2xl border border-gray-800 bg-gray-900 p-6">
            <h2 class="mb-4 text-lg font-bold text-white">Update Status</h2>
            <form action="{{ route('admin.orders.update-status', $order) }}" method="POST">
                @csrf
                @method('PATCH')
                <select name="status"
                        class="mb-3 w-full rounded-xl border border-gray-700 bg-gray-800 px-4 py-3 text-sm text-white focus:border-peri focus:outline-none focus:ring-1 focus:ring-peri">
                    <option value="pending"    {{ $order->status === 'pending'    ? 'selected' : '' }}>Menunggu</option>
                    <option value="processing" {{ $order->status === 'processing' ? 'selected' : '' }}>Diproses</option>
                    <option value="completed"  {{ $order->status === 'completed'  ? 'selected' : '' }}>Selesai</option>
                    <option value="cancelled"  {{ $order->status === 'cancelled'  ? 'selected' : '' }}>Dibatalkan</option>
                </select>
                <button type="submit"
                        class="w-full rounded-xl bg-peri px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-peri-dark">
                    <i class="fas fa-save mr-1"></i> Simpan Status
                </button>
            </form>
        </div>

        {{-- Customer Info --}}
        <div class="rounded-2xl border border-gray-800 bg-gray-900 p-6">
            <h2 class="mb-4 text-lg font-bold text-white">Info Pelanggan</h2>
            <dl class="space-y-3 text-sm">
                <div>
                    <dt class="text-gray-500">Nama</dt>
                    <dd class="mt-0.5 font-medium text-white">{{ $order->name }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500">Email</dt>
                    <dd class="mt-0.5 text-white">{{ $order->email ?? '-' }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500">Telepon</dt>
                    <dd class="mt-0.5 text-white">{{ $order->phone }}</dd>
                </div>
            </dl>
        </div>

        {{-- Action Buttons --}}
        <div class="space-y-3">
            <a href="{{ route('admin.orders.whatsapp', $order) }}" target="_blank"
               class="flex w-full items-center justify-center gap-2 rounded-xl bg-green-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-green-700">
                <i class="fab fa-whatsapp"></i> Hubungi via WhatsApp
            </a>
            <button onclick="printInvoice()"
                    class="flex w-full items-center justify-center gap-2 rounded-xl bg-peri/10 px-4 py-2.5 text-sm font-semibold text-peri transition hover:bg-peri/20">
                <i class="fas fa-print"></i> Cetak Invoice
            </button>
            <button @click="confirmDelete = true"
                    class="flex w-full items-center justify-center gap-2 rounded-xl border border-red-500/30 px-4 py-2.5 text-sm font-semibold text-red-400 transition hover:bg-red-500/10">
                <i class="fas fa-trash"></i> Hapus Pesanan
            </button>
        </div>

        {{-- Delete Confirmation --}}
        <div x-show="confirmDelete" x-cloak x-transition
             class="rounded-2xl border border-red-500/20 bg-red-500/5 p-4">
            <p class="mb-3 text-sm text-red-400">Yakin ingin menghapus pesanan #{{ $order->order_number }}?</p>
            <div class="flex gap-2">
                <form action="{{ route('admin.orders.destroy', $order) }}" method="POST" class="flex-1">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="w-full rounded-xl bg-red-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-red-700">
                        Hapus
                    </button>
                </form>
                <button @click="confirmDelete = false"
                        class="flex-1 rounded-xl border border-gray-700 px-4 py-2 text-sm text-gray-400 transition hover:text-white">
                    Batal
                </button>
            </div>
        </div>
    </div>

</div>
</div>

@push('scripts')
<script>
function printInvoice() {
    const w = window.open('', '_blank', 'width=800,height=600');
    w.document.write(`<!DOCTYPE html><html><head><title>Invoice #{{ $order->order_number }}</title>
    <style>
        body{font-family:Arial,sans-serif;color:#333;max-width:700px;margin:0 auto;padding:30px;}
        h1{font-size:22px;margin:0 0 5px;} .header{display:flex;justify-content:space-between;align-items:start;margin-bottom:30px;border-bottom:2px solid #6c63ff;padding-bottom:15px;}
        .meta{text-align:right;font-size:13px;color:#666;} .meta strong{color:#333;}
        table{width:100%;border-collapse:collapse;margin:15px 0;} th{background:#f8f8f8;text-align:left;padding:10px;font-size:13px;border-bottom:2px solid #ddd;}
        td{padding:10px;border-bottom:1px solid #eee;font-size:13px;} .text-right{text-align:right;} .text-center{text-align:center;}
        .total-row td{border-top:2px solid #333;font-weight:bold;font-size:15px;} .section{margin:20px 0;} .section h3{font-size:14px;margin:0 0 8px;color:#6c63ff;}
        .section p{font-size:13px;margin:3px 0;} .footer{margin-top:30px;text-align:center;font-size:12px;color:#999;border-top:1px solid #eee;padding-top:15px;}
        @media print{body{padding:15px;}}
    </style></head><body>
    <div class="header">
        <div><h1>INVOICE</h1><p style="color:#6c63ff;font-weight:bold;">{{ setting('store_name', 'Shuriza Store Kediri') }}</p></div>
        <div class="meta"><strong>#{{ $order->order_number }}</strong><br>{{ $order->created_at->format('d M Y, H:i') }}<br>Status: {{ $order->status_label }}</div>
    </div>
    <div class="section"><h3>Pelanggan</h3><p><strong>{{ $order->name }}</strong></p><p>{{ $order->phone }}</p>@if($order->email)<p>{{ $order->email }}</p>@endif</div>
    <table><thead><tr><th>Produk</th><th class="text-center">Qty</th><th class="text-right">Harga</th><th class="text-right">Subtotal</th></tr></thead>
    <tbody>@foreach($order->items as $item)<tr><td>{{ $item->product_name }}</td><td class="text-center">{{ $item->quantity }}</td><td class="text-right">Rp {{ number_format($item->price, 0, ',', '.') }}</td><td class="text-right">Rp {{ number_format($item->quantity * $item->price, 0, ',', '.') }}</td></tr>@endforeach</tbody>
    <tfoot><tr class="total-row"><td colspan="3" class="text-right">Total</td><td class="text-right">{{ $order->formatted_total }}</td></tr></tfoot></table>
    @if($order->notes)<div class="section"><h3>Catatan</h3><p>{{ $order->notes }}</p></div>@endif
    <div class="footer">Terima kasih telah berbelanja di {{ setting('store_name', 'Shuriza Store Kediri') }}<br>Invoice ini dibuat secara otomatis.</div>
    </body></html>`);
    w.document.close();
    w.focus();
    setTimeout(() => w.print(), 300);
}
</script>
@endpush
@endsection
