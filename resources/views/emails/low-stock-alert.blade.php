<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Peringatan Stok Menipis</title>
    <style>
        body { margin: 0; padding: 0; background: #0f0e17; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; color: #e0e0e0; }
        .container { max-width: 600px; margin: 0 auto; background: #1e1d2e; }
        .header { background: linear-gradient(135deg, #f59e0b, #d97706); padding: 32px 24px; text-align: center; }
        .header h1 { color: #ffffff; margin: 0; font-size: 24px; font-weight: 700; }
        .header p { color: rgba(255,255,255,0.8); margin: 8px 0 0; font-size: 14px; }
        .body { padding: 32px 24px; }
        .summary { background: #252438; border-radius: 12px; padding: 20px; margin-bottom: 24px; border: 1px solid #333252; text-align: center; }
        .summary-number { font-size: 36px; font-weight: 800; color: #fbbf24; }
        .summary-label { color: #9e9eb8; font-size: 13px; margin-top: 4px; }
        .product-table { width: 100%; border-collapse: collapse; margin-bottom: 24px; }
        .product-table th { background: #252438; color: #9e9eb8; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px; padding: 12px 16px; text-align: left; }
        .product-table td { padding: 12px 16px; border-bottom: 1px solid #333252; font-size: 14px; color: #e0e0e0; }
        .stock-badge { display: inline-block; padding: 3px 10px; border-radius: 20px; font-size: 12px; font-weight: 700; }
        .stock-zero { background: #ef444420; color: #f87171; }
        .stock-low { background: #fbbf2420; color: #fbbf24; }
        .cta-btn { display: inline-block; background: #6c63ff; color: #ffffff; text-decoration: none; padding: 14px 32px; border-radius: 12px; font-weight: 700; font-size: 15px; }
        .cta-section { text-align: center; margin: 32px 0; }
        .footer { background: #151424; padding: 24px; text-align: center; color: #666; font-size: 12px; }
    </style>
</head>
<body>
    <div class="container">
        {{-- Header --}}
        <div class="header">
            <h1>⚠️ Peringatan Stok</h1>
            <p>Ada produk yang stoknya menipis!</p>
        </div>

        {{-- Body --}}
        <div class="body">
            <div class="summary">
                <p class="summary-number">{{ $products->count() }}</p>
                <p class="summary-label">Produk dengan stok menipis</p>
            </div>

            <table class="product-table">
                <thead>
                    <tr>
                        <th>Produk</th>
                        <th>Kategori</th>
                        <th style="text-align: center;">Stok</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($products as $product)
                    <tr>
                        <td style="font-weight: 600; color: #fff;">{{ $product->name }}</td>
                        <td>{{ $product->category->name ?? '-' }}</td>
                        <td style="text-align: center;">
                            <span class="stock-badge {{ $product->stock === 0 ? 'stock-zero' : 'stock-low' }}">
                                {{ $product->stock }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="cta-section">
                <p style="color: #b0b0c8; font-size: 14px; margin-bottom: 16px;">
                    Segera restock produk-produk di atas agar tidak kehabisan.
                </p>
                <a href="{{ url('/admin/stock-alerts') }}" class="cta-btn">
                    📦 Kelola Stok
                </a>
            </div>
        </div>

        {{-- Footer --}}
        <div class="footer">
            <p>&copy; {{ date('Y') }} {{ setting('store_name', 'Shuriza Store Kediri') }} — Admin Notification</p>
        </div>
    </div>
</body>
</html>
