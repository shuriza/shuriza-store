<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ isset($status) && $status === 'closed' ? 'Toko Tutup' : 'Maintenance' }} - {{ setting('store_name', 'Shuriza Store') }}</title>
    @if(setting('store_favicon'))
        <link rel="icon" href="{{ asset('storage/' . setting('store_favicon')) }}" type="image/png">
    @endif
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #0f0e17, #1a1926);
            font-family: 'Poppins', sans-serif;
            color: #e0e0e0;
            padding: 2rem;
        }
        .container {
            text-align: center;
            max-width: 500px;
        }
        .icon {
            font-size: 4rem;
            margin-bottom: 1.5rem;
        }
        h1 {
            font-size: 1.75rem;
            font-weight: 700;
            color: #fff;
            margin-bottom: 0.75rem;
        }
        p {
            font-size: 1rem;
            color: #a0a0b8;
            line-height: 1.6;
            margin-bottom: 2rem;
        }
        .brand {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1.25rem;
            border-radius: 999px;
            background: rgba(108, 99, 255, 0.1);
            border: 1px solid rgba(108, 99, 255, 0.2);
            color: #6c63ff;
            font-weight: 600;
            font-size: 0.875rem;
        }
    </style>
</head>
<body>
    <div class="container">
        @if(isset($status) && $status === 'closed')
            <div class="icon">🔒</div>
            <h1>Toko Sedang Tutup</h1>
        @else
            <div class="icon">🔧</div>
            <h1>Sedang Dalam Perbaikan</h1>
        @endif
        <p>{{ $message }}</p>
        <div class="brand">
            {{ setting('store_name', 'Shuriza Store') }}
        </div>
    </div>
</body>
</html>
