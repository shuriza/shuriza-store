<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Status Order</title>
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
        .status-box { border-radius: 12px; padding: 24px; text-align: center; margin-bottom: 24px; }
        .status-arrow { color: #9e9eb8; font-size: 20px; margin: 0 12px; }
        .old-status { display: inline-block; padding: 6px 16px; border-radius: 20px; font-size: 13px; font-weight: 700; background: #333252; color: #9e9eb8; text-decoration: line-through; }
        .new-status { display: inline-block; padding: 6px 16px; border-radius: 20px; font-size: 13px; font-weight: 700; }
        .status-pending { background: #fbbf2420; color: #fbbf24; }
        .status-processing { background: #3b82f620; color: #60a5fa; }
        .status-completed { background: #22c55e20; color: #4ade80; }
        .status-cancelled { background: #ef444420; color: #f87171; }
        .cta-btn { display: inline-block; background: #6c63ff; color: #ffffff; text-decoration: none; padding: 14px 32px; border-radius: 12px; font-weight: 700; font-size: 15px; }
        .cta-section { text-align: center; margin: 32px 0; }
        .footer { background: #151424; padding: 24px; text-align: center; color: #666; font-size: 12px; }
        .footer a { color: #6c63ff; text-decoration: none; }
    </style>
</head>
<body>
    <div class="container">
        {{-- Header --}}
        <div class="header">
            <h1>🛍️ {{ setting('store_name', 'Shuriza Store') }}</h1>
            <p>Update Status Pesanan</p>
        </div>

        {{-- Body --}}
        <div class="body">
            <p class="greeting">Halo {{ $order->name }}! 👋</p>
            <p style="color: #b0b0c8; font-size: 14px; line-height: 1.6; margin-bottom: 24px;">
                Status pesananmu dengan nomor <strong style="color: #6c63ff;">{{ $order->order_number }}</strong> telah diperbarui.
            </p>

            {{-- Status Change --}}
            @php
                $statusLabels = [
                    'pending' => 'Menunggu',
                    'processing' => 'Diproses',
                    'completed' => 'Selesai',
                    'cancelled' => 'Dibatalkan',
                ];
            @endphp
            <div class="status-box info-box" style="text-align: center;">
                <p style="color: #9e9eb8; font-size: 12px; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 16px;">Perubahan Status</p>
                <span class="old-status">{{ $statusLabels[$oldStatus] ?? ucfirst($oldStatus) }}</span>
                <span class="status-arrow">→</span>
                <span class="new-status status-{{ $order->status }}">{{ $order->status_label }}</span>
            </div>

            {{-- Message per status --}}
            <div class="info-box">
                @if ($order->status === 'processing')
                    <p style="color: #60a5fa; font-size: 14px; margin: 0;">
                        🔄 Pesananmu sedang kami proses. Kami akan menghubungi kamu jika ada update selanjutnya.
                    </p>
                @elseif ($order->status === 'completed')
                    <p style="color: #4ade80; font-size: 14px; margin: 0;">
                        ✅ Pesananmu telah selesai! Terima kasih sudah berbelanja di {{ setting('store_name', 'Shuriza Store') }}. Semoga puas dengan produknya! 🎉
                    </p>
                @elseif ($order->status === 'cancelled')
                    <p style="color: #f87171; font-size: 14px; margin: 0;">
                        ❌ Pesananmu telah dibatalkan. Jika kamu merasa ini kesalahan, silakan hubungi kami via WhatsApp.
                    </p>
                @else
                    <p style="color: #fbbf24; font-size: 14px; margin: 0;">
                        ⏳ Pesananmu menunggu konfirmasi. Silakan hubungi kami jika ada pertanyaan.
                    </p>
                @endif
            </div>

            {{-- Order Summary --}}
            <div class="info-box">
                <div class="info-row">
                    <span class="info-label">No. Order</span>
                    <span class="info-value">{{ $order->order_number }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Total</span>
                    <span class="info-value">{{ $order->formatted_total }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Jumlah Item</span>
                    <span class="info-value">{{ $order->items->count() }} item</span>
                </div>
            </div>

            {{-- CTA --}}
            <div class="cta-section">
                <a href="https://wa.me/{{ setting('whatsapp_number') }}" class="cta-btn">
                    💬 Hubungi Kami
                </a>
            </div>
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
