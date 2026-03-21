<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Konfirmasi Order</title>
    <style>
        body { margin: 0; padding: 0; background: #0f0e17; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; color: #e0e0e0; }
        .container { max-width: 600px; margin: 0 auto; background: #1e1d2e; }
        .header { background: linear-gradient(135deg, #6c63ff, #5a52d5); padding: 32px 24px; text-align: center; }
        .header h1 { color: #ffffff; margin: 0; font-size: 24px; font-weight: 700; }
        .header p { color: rgba(255,255,255,0.8); margin: 8px 0 0; font-size: 14px; }
        .body { padding: 32px 24px; }
        .greeting { font-size: 18px; font-weight: 600; color: #ffffff; margin-bottom: 16px; }
        .info-box { background: #252438; border-radius: 12px; padding: 20px; margin-bottom: 24px; border: 1px solid #333252; }
        .info-row { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #333252; }
        .info-row:last-child { border-bottom: none; }
        .info-label { color: #9e9eb8; font-size: 13px; }
        .info-value { color: #ffffff; font-size: 13px; font-weight: 600; }
        .items-table { width: 100%; border-collapse: collapse; margin-bottom: 24px; }
        .items-table th { background: #252438; color: #9e9eb8; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px; padding: 12px 16px; text-align: left; }
        .items-table td { padding: 12px 16px; border-bottom: 1px solid #333252; font-size: 14px; color: #e0e0e0; }
        .items-table .price { font-weight: 600; color: #ffffff; }
        .total-box { background: linear-gradient(135deg, #6c63ff15, #ff658415); border: 1px solid #6c63ff40; border-radius: 12px; padding: 20px; text-align: center; margin-bottom: 24px; }
        .total-label { color: #9e9eb8; font-size: 13px; margin-bottom: 4px; }
        .total-amount { color: #6c63ff; font-size: 28px; font-weight: 800; }
        .discount-info { color: #43e97b; font-size: 13px; margin-top: 8px; }
        .cta-btn { display: inline-block; background: #25d366; color: #ffffff; text-decoration: none; padding: 14px 32px; border-radius: 12px; font-weight: 700; font-size: 15px; }
        .cta-section { text-align: center; margin: 32px 0; }
        .footer { background: #151424; padding: 24px; text-align: center; color: #666; font-size: 12px; }
        .footer a { color: #6c63ff; text-decoration: none; }
        .status-badge { display: inline-block; background: #fbbf2420; color: #fbbf24; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 700; }
    </style>
</head>
<body>
    <div class="container">
        {{-- Header --}}
        <div class="header">
            <h1>🛍️ {{ setting('store_name', 'Shuriza Store') }}</h1>
            <p>Konfirmasi Pesanan Kamu</p>
        </div>

        {{-- Body --}}
        <div class="body">
            <p class="greeting">Halo {{ $order->name }}! 👋</p>
            <p style="color: #b0b0c8; font-size: 14px; line-height: 1.6; margin-bottom: 24px;">
                Terima kasih sudah berbelanja di <strong style="color: #6c63ff;">{{ setting('store_name', 'Shuriza Store') }}</strong>!
                Pesananmu telah kami terima dan sedang menunggu konfirmasi.
            </p>

            {{-- Order Info --}}
            <div class="info-box">
                <div class="info-row">
                    <span class="info-label">No. Order</span>
                    <span class="info-value">{{ $order->order_number }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Tanggal</span>
                    <span class="info-value">{{ $order->created_at->format('d M Y, H:i') }} WIB</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Status</span>
                    <span class="info-value"><span class="status-badge">Menunggu</span></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Nama</span>
                    <span class="info-value">{{ $order->name }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">No. WhatsApp</span>
                    <span class="info-value">{{ $order->phone }}</span>
                </div>
            </div>

            {{-- Order Items --}}
            <table class="items-table">
                <thead>
                    <tr>
                        <th>Produk</th>
                        <th style="text-align: center;">Qty</th>
                        <th style="text-align: right;">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($order->items as $item)
                    <tr>
                        <td>{{ $item->product_name }}</td>
                        <td style="text-align: center;">{{ $item->quantity }}</td>
                        <td style="text-align: right;" class="price">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            {{-- Total --}}
            <div class="total-box">
                @if ($order->discount_amount > 0)
                    <p class="discount-info">
                        🎫 Kupon {{ $order->coupon_code }} — Diskon Rp {{ number_format($order->discount_amount, 0, ',', '.') }}
                    </p>
                @endif
                <p class="total-label">Total Pembayaran</p>
                <p class="total-amount">{{ $order->formatted_total }}</p>
            </div>

            {{-- WhatsApp CTA --}}
            <div class="cta-section">
                <p style="color: #b0b0c8; font-size: 14px; margin-bottom: 16px;">
                    Konfirmasi pesananmu via WhatsApp:
                </p>
                <a href="{{ $order->getWhatsAppUrl() }}" class="cta-btn">
                    💬 Konfirmasi via WhatsApp
                </a>
            </div>

            @if ($order->notes)
            <div class="info-box">
                <div class="info-row">
                    <span class="info-label">Catatan</span>
                    <span class="info-value">{{ $order->notes }}</span>
                </div>
            </div>
            @endif
        </div>

        {{-- Footer --}}
        <div class="footer">
            <p>&copy; {{ date('Y') }} {{ setting('store_name', 'Shuriza Store Kediri') }}. Semua hak dilindungi.</p>
            <p style="margin-top: 8px;">
                Ada pertanyaan? Hubungi kami via
                <a href="https://wa.me/{{ setting('whatsapp_number') }}">WhatsApp</a>
            </p>
        </div>
    </div>
</body>
</html>
