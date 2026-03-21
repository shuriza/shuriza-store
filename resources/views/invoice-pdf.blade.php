<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Invoice {{ $order->order_number }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, sans-serif; color: #333; background: #fff; font-size: 14px; line-height: 1.6; }
        .container { max-width: 800px; margin: 0 auto; padding: 40px; }
        .header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 40px; border-bottom: 3px solid #6c63ff; padding-bottom: 20px; }
        .logo { font-size: 24px; font-weight: 800; color: #6c63ff; }
        .logo small { display: block; font-size: 12px; color: #666; font-weight: 400; }
        .invoice-title { text-align: right; }
        .invoice-title h1 { font-size: 28px; color: #6c63ff; text-transform: uppercase; letter-spacing: 2px; }
        .invoice-title .number { font-size: 14px; color: #666; margin-top: 4px; }
        .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 30px; margin-bottom: 30px; }
        .info-box h3 { font-size: 11px; text-transform: uppercase; color: #999; letter-spacing: 1px; margin-bottom: 8px; }
        .info-box p { font-size: 14px; color: #333; margin-bottom: 2px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        th { background: #6c63ff; color: #fff; padding: 12px 16px; text-align: left; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px; }
        th:last-child, td:last-child { text-align: right; }
        td { padding: 12px 16px; border-bottom: 1px solid #eee; }
        tr:nth-child(even) td { background: #f9f9ff; }
        .totals { margin-left: auto; width: 300px; }
        .totals .row { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #eee; }
        .totals .row.grand { border-top: 2px solid #6c63ff; border-bottom: none; font-weight: 700; font-size: 18px; color: #6c63ff; padding-top: 12px; }
        .status { display: inline-block; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; }
        .status-pending { background: #fef3c7; color: #92400e; }
        .status-processing { background: #dbeafe; color: #1e40af; }
        .status-completed { background: #d1fae5; color: #065f46; }
        .status-cancelled { background: #fee2e2; color: #991b1b; }
        .footer { margin-top: 40px; padding-top: 20px; border-top: 1px solid #eee; text-align: center; font-size: 12px; color: #999; }
        @media print { body { padding: 0; } .container { padding: 20px; } .no-print { display: none; } }
    </style>
</head>
<body>
    <div class="container">
        {{-- Print Button --}}
        <div class="no-print" style="text-align: right; margin-bottom: 20px;">
            <button onclick="window.print()" style="background: #6c63ff; color: #fff; border: none; padding: 10px 24px; border-radius: 8px; font-size: 14px; cursor: pointer; font-weight: 600;">
                🖨️ Cetak Invoice
            </button>
        </div>

        {{-- Header --}}
        <div class="header">
            <div class="logo">
                {{ $storeName }}
                <small>{{ $storeAddress }}</small>
                @if($storePhone)<small>📱 {{ $storePhone }}</small>@endif
                @if($storeEmail)<small>📧 {{ $storeEmail }}</small>@endif
            </div>
            <div class="invoice-title">
                <h1>Invoice</h1>
                <div class="number">{{ $order->order_number }}</div>
                <div class="number">{{ $order->created_at->translatedFormat('d F Y, H:i') }}</div>
            </div>
        </div>

        {{-- Customer & Order Info --}}
        <div class="info-grid">
            <div class="info-box">
                <h3>Ditagihkan Kepada</h3>
                <p><strong>{{ $order->name }}</strong></p>
                <p>📱 {{ $order->phone }}</p>
                @if($order->email)<p>📧 {{ $order->email }}</p>@endif
            </div>
            <div class="info-box" style="text-align: right;">
                <h3>Info Pesanan</h3>
                <p>No. Order: <strong>{{ $order->order_number }}</strong></p>
                <p>Tanggal: {{ $order->created_at->translatedFormat('d M Y') }}</p>
                <p>Status: <span class="status status-{{ $order->status }}">{{ $order->status_label }}</span></p>
            </div>
        </div>

        {{-- Items Table --}}
        <table>
            <thead>
                <tr>
                    <th style="width:40px">#</th>
                    <th>Produk</th>
                    <th style="width:80px;text-align:center">Qty</th>
                    <th style="width:150px">Harga</th>
                    <th style="width:150px">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $i => $item)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $item->product_name }}</td>
                    <td style="text-align:center">{{ $item->quantity }}</td>
                    <td>Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                    <td>Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        {{-- Totals --}}
        <div class="totals">
            @php $subtotal = $order->items->sum('subtotal'); @endphp
            <div class="row">
                <span>Subtotal</span>
                <span>Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
            </div>
            @if($order->discount_amount > 0)
            <div class="row">
                <span>Diskon {{ $order->coupon_code ? "({$order->coupon_code})" : '' }}</span>
                <span style="color: #ef4444;">- Rp {{ number_format($order->discount_amount, 0, ',', '.') }}</span>
            </div>
            @endif
            <div class="row grand">
                <span>Total</span>
                <span>Rp {{ number_format($order->total, 0, ',', '.') }}</span>
            </div>
        </div>

        @if($order->notes)
        <div style="margin-top: 30px; padding: 16px; background: #f9f9ff; border-radius: 8px; border-left: 3px solid #6c63ff;">
            <strong style="font-size: 12px; text-transform: uppercase; color: #999;">Catatan:</strong>
            <p style="margin-top: 4px;">{{ $order->notes }}</p>
        </div>
        @endif

        {{-- Footer --}}
        <div class="footer">
            <p>Terima kasih telah berbelanja di <strong>{{ $storeName }}</strong></p>
            <p>Invoice ini dibuat secara otomatis dan sah tanpa tanda tangan.</p>
        </div>
    </div>
</body>
</html>
