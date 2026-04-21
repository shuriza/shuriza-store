@extends('layouts.app')

@section('title', 'Checkout')

@section('content')
<div class="mx-auto max-w-6xl px-4 py-8 sm:px-6 lg:px-8">

    {{-- Breadcrumb --}}
    <nav class="mb-2 flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400">
        <a href="{{ route('home') }}" class="hover:text-peri transition">Beranda</a>
        <i class="fas fa-chevron-right text-[.6rem]"></i>
        <a href="{{ route('cart.index') }}" class="hover:text-peri transition">Keranjang</a>
        <i class="fas fa-chevron-right text-[.6rem]"></i>
        <span class="text-gray-900 dark:text-white">Checkout</span>
    </nav>

    {{-- Page Title --}}
    <h1 class="mb-6 flex items-center gap-3 text-2xl font-extrabold text-gray-900 dark:text-white">
        <i class="fas fa-credit-card text-peri"></i>
        Checkout
    </h1>

    @if($cartItems->isEmpty())
        {{-- Empty State --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm flex flex-col items-center justify-center px-6 py-20 text-center">
            <div class="mb-4 text-6xl text-gray-300 dark:text-gray-600">
                <i class="fas fa-shopping-cart"></i>
            </div>
            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-1">Keranjang Kosong</h3>
            <p class="text-gray-500 dark:text-gray-400 mb-6">Tidak ada item untuk di-checkout.</p>
            <a href="{{ route('products.index') }}" class="inline-flex items-center gap-2 rounded-xl bg-peri px-6 py-3 text-sm font-semibold text-white shadow hover:bg-peri-dark transition">
                <i class="fas fa-shopping-bag"></i> Mulai Belanja
            </a>
        </div>
    @else
        {{-- Validation Errors --}}
        @if ($errors->any())
            <div class="mb-6 rounded-xl border border-red-300 dark:border-red-600 bg-red-50 dark:bg-red-900/20 p-4">
                <div class="flex items-center gap-2 text-sm font-semibold text-red-600 dark:text-red-400 mb-1">
                    <i class="fas fa-exclamation-circle"></i> Terdapat kesalahan:
                </div>
                <ul class="list-disc list-inside text-sm text-red-600 dark:text-red-400 space-y-0.5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('order.store') }}">
            @csrf

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">

                {{-- Left Column — Customer Form --}}
                <div class="lg:col-span-2">
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm">
                        <div class="border-b border-gray-100 dark:border-gray-700 px-6 py-4">
                            <h2 class="text-base font-bold text-gray-900 dark:text-white flex items-center gap-2">
                                <i class="fas fa-user-circle text-peri"></i>
                                Informasi Pelanggan
                            </h2>
                        </div>

                        <div class="p-6 space-y-5">
                            {{-- Name --}}
                            <div>
                                <label for="name" class="mb-1.5 block text-sm font-semibold text-gray-900 dark:text-white">
                                    Nama Lengkap <span class="text-red-500">*</span>
                                </label>
                                <input type="text" id="name" name="name"
                                       value="{{ old('name', auth()->user()?->name) }}"
                                       placeholder="Masukkan nama lengkap"
                                       required
                                       class="w-full bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl px-4 py-3 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:ring-2 focus:ring-peri focus:border-transparent transition @error('name') border-red-500 dark:border-red-500 @enderror">
                                @error('name')
                                    <p class="mt-1 text-red-500 text-sm"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Email --}}
                            <div>
                                <label for="email" class="mb-1.5 block text-sm font-semibold text-gray-900 dark:text-white">
                                    Email <span class="text-gray-400 dark:text-gray-500 font-normal text-xs">(opsional)</span>
                                </label>
                                <input type="email" id="email" name="email"
                                       value="{{ old('email', auth()->user()?->email) }}"
                                       placeholder="contoh@email.com"
                                       class="w-full bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl px-4 py-3 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:ring-2 focus:ring-peri focus:border-transparent transition @error('email') border-red-500 dark:border-red-500 @enderror">
                                @error('email')
                                    <p class="mt-1 text-red-500 text-sm"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Phone --}}
                            <div>
                                <label for="phone" class="mb-1.5 block text-sm font-semibold text-gray-900 dark:text-white">
                                    Nomor WhatsApp <span class="text-red-500">*</span>
                                </label>
                                <input type="tel" id="phone" name="phone"
                                       value="{{ old('phone', auth()->user()?->phone) }}"
                                       placeholder="08xxxxxxxxxx"
                                       pattern="[0-9]{10,15}"
                                       required
                                       class="w-full bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl px-4 py-3 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:ring-2 focus:ring-peri focus:border-transparent transition @error('phone') border-red-500 dark:border-red-500 @enderror">
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400"><i class="fab fa-whatsapp mr-1"></i>Konfirmasi order akan dikirim via WhatsApp ke nomor ini.</p>
                                @error('phone')
                                    <p class="mt-1 text-red-500 text-sm"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Notes --}}
                            <div>
                                <label for="notes" class="mb-1.5 block text-sm font-semibold text-gray-900 dark:text-white">
                                    Catatan <span class="text-gray-400 dark:text-gray-500 font-normal text-xs">(opsional)</span>
                                </label>
                                <textarea id="notes" name="notes" rows="3"
                                          placeholder="Catatan tambahan untuk pesanan..."
                                          class="w-full bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl px-4 py-3 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:ring-2 focus:ring-peri focus:border-transparent transition resize-none @error('notes') border-red-500 dark:border-red-500 @enderror">{{ old('notes') }}</textarea>
                                @error('notes')
                                    <p class="mt-1 text-red-500 text-sm"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Right Column — Order Summary --}}
                <div class="lg:sticky lg:top-24">
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm">
                        <div class="border-b border-gray-100 dark:border-gray-700 px-6 py-4">
                            <h2 class="text-base font-bold text-gray-900 dark:text-white flex items-center gap-2">
                                <i class="fas fa-receipt text-peri"></i>
                                Ringkasan Pesanan
                            </h2>
                        </div>

                        <div class="p-6 space-y-4">
                            {{-- Cart Items --}}
                            <div class="space-y-3">
                                @foreach($cartItems as $item)
                                    @php $product = $item->product; @endphp
                                    <div class="flex items-center gap-3">
                                        @if($product->image_url)
                                            <img src="{{ $product->image_url }}" alt="{{ $product->name }}"
                                                 class="h-12 w-12 rounded-lg object-cover bg-gray-100 dark:bg-gray-700 shrink-0">
                                        @else
                                            <div class="h-12 w-12 rounded-lg bg-gray-100 dark:bg-gray-700 flex items-center justify-center text-gray-400 dark:text-gray-500 shrink-0">
                                                <i class="fas fa-box"></i>
                                            </div>
                                        @endif
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-semibold text-gray-900 dark:text-white truncate">{{ $product->name }}</p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $item->quantity }} &times; {{ $product->formatted_effective_price }}</p>
                                        </div>
                                        <p class="text-sm font-semibold text-gray-900 dark:text-white shrink-0">
                                            Rp {{ number_format($item->quantity * $product->effective_price, 0, ',', '.') }}
                                        </p>
                                    </div>
                                @endforeach
                            </div>

                            {{-- Divider --}}
                            <hr class="border-gray-100 dark:border-gray-700">

                            {{-- Coupon Code --}}
                            <div>
                                @if($coupon)
                                    <div class="flex items-center justify-between rounded-xl bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-700 px-3 py-2.5">
                                        <div class="flex items-center gap-2">
                                            <i class="fas fa-ticket-alt text-emerald-500"></i>
                                            <span class="text-sm font-semibold text-emerald-700 dark:text-emerald-400">{{ $coupon->code }}</span>
                                            <span class="text-xs text-emerald-600 dark:text-emerald-500">({{ $coupon->formatted_value }})</span>
                                        </div>
                                        <button type="button" onclick="document.getElementById('removeCouponForm').submit()"
                                                class="w-6 h-6 rounded-full hover:bg-red-100 dark:hover:bg-red-500/20 text-gray-400 hover:text-red-500 flex items-center justify-center transition" title="Hapus kupon">
                                            <i class="fas fa-times text-xs"></i>
                                        </button>
                                    </div>
                                @else
                                    <div class="coupon-input" x-data="{ code: '' }">
                                        <div class="flex gap-2">
                                            <input type="text" x-model="code" placeholder="Kode kupon"
                                                   class="flex-1 rounded-xl border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 px-3 py-2 text-sm text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-peri focus:border-transparent transition uppercase">
                                            <button type="button" @click="if(code) { document.getElementById('coupon_code_input').value = code; document.getElementById('couponForm').submit(); }"
                                                    class="shrink-0 rounded-xl bg-peri/10 px-4 py-2 text-sm font-semibold text-peri hover:bg-peri/20 transition">
                                                Pakai
                                            </button>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            {{-- Subtotal --}}
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-gray-500 dark:text-gray-400">Subtotal</span>
                                <span class="font-semibold text-gray-900 dark:text-white">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                            </div>

                            {{-- Discount --}}
                            @if($discount > 0)
                                <div class="flex items-center justify-between text-sm">
                                    <span class="text-emerald-500"><i class="fas fa-tag mr-1"></i>Diskon</span>
                                    <span class="font-semibold text-emerald-500">-Rp {{ number_format($discount, 0, ',', '.') }}</span>
                                </div>
                            @endif

                            {{-- Total --}}
                            <div class="flex items-center justify-between">
                                <span class="text-base font-bold text-gray-900 dark:text-white">Total</span>
                                <span class="text-lg font-extrabold text-peri">Rp {{ number_format($total, 0, ',', '.') }}</span>
                            </div>

                            {{-- Place Order --}}
                            <button type="submit"
                                    class="mt-2 w-full flex items-center justify-center gap-2 rounded-xl bg-peri px-6 py-3.5 text-sm font-bold text-white shadow hover:bg-peri-dark transition">
                                <i class="fas fa-paper-plane"></i>
                                Buat Pesanan
                            </button>

                            {{-- Back to Cart --}}
                            <a href="{{ route('cart.index') }}"
                               class="block w-full text-center text-sm text-gray-500 dark:text-gray-400 hover:text-peri transition mt-1">
                                <i class="fas fa-arrow-left mr-1"></i> Kembali ke Keranjang
                            </a>

                            {{-- Info --}}
                            <div class="mt-4 rounded-xl bg-peri/5 border border-peri/10 p-3">
                                <p class="text-xs text-gray-500 dark:text-gray-400 leading-relaxed">
                                    @if(\App\Services\PaymentService::isEnabled())
                                    <i class="fas fa-credit-card text-peri mr-1"></i>
                                    Setelah pesanan dibuat, kamu akan diarahkan ke halaman <strong class="text-gray-700 dark:text-gray-300">pembayaran online</strong> ({{ ucfirst(\App\Services\PaymentService::getProvider()) }}).
                                    Tersedia QRIS, e-wallet, dan transfer bank.
                                    @else
                                    <i class="fab fa-whatsapp text-green-500 mr-1"></i>
                                    Setelah pesanan dibuat, kamu akan diarahkan untuk konfirmasi via <strong class="text-gray-700 dark:text-gray-300">WhatsApp</strong>.
                                    Admin akan memproses pesanan kamu segera.
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </form>
    @endif

</div>

{{-- Hidden coupon form --}}
<form id="couponForm" action="{{ route('order.apply-coupon') }}" method="POST" class="hidden">
    @csrf
    <input type="hidden" name="coupon_code" id="coupon_code_input" value="">
</form>
{{-- Hidden remove coupon form --}}
<form id="removeCouponForm" action="{{ route('order.remove-coupon') }}" method="POST" class="hidden">
    @csrf
</form>
@endsection
