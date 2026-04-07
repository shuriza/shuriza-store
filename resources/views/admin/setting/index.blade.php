@extends('layouts.admin')

@section('title', 'Pengaturan')
@section('page-title', 'Pengaturan')

@section('content')
<div x-data="{ activeTab: 'store' }">

    {{-- Tab Navigation --}}
    <div class="mb-6 flex flex-wrap gap-2">
        @foreach($groups as $groupKey => $group)
        <button @click="activeTab = '{{ $groupKey }}'" type="button"
                :class="activeTab === '{{ $groupKey }}'
                    ? 'bg-peri text-white shadow-lg shadow-peri/25'
                    : 'bg-gray-800 text-gray-400 hover:bg-gray-700 hover:text-white'"
                class="flex items-center gap-2 rounded-xl px-4 py-2.5 text-sm font-semibold transition-all">
            <i class="fas {{ $group['icon'] }}"></i>
            <span class="hidden sm:inline">{{ $group['title'] }}</span>
        </button>
        @endforeach
    </div>

    {{-- Settings Form --}}
    <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        @foreach($groups as $groupKey => $group)
        <div x-show="activeTab === '{{ $groupKey }}'" x-cloak
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0">

            <div class="rounded-2xl border border-gray-800 bg-gray-900 p-6">
                {{-- Group Header --}}
                <div class="mb-6 flex items-center gap-3 border-b border-gray-800 pb-4">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-peri/10 text-peri">
                        <i class="fas {{ $group['icon'] }}"></i>
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-white">{{ $group['title'] }}</h2>
                        <p class="text-sm text-gray-500">{{ $group['desc'] }}</p>
                    </div>
                </div>

                {{-- Settings Fields --}}
                <div class="space-y-5">
                    @if(isset($settings[$groupKey]))
                        @foreach($settings[$groupKey] as $setting)
                            <div>
                                <label for="{{ $setting['key'] }}" class="mb-1.5 block text-sm font-medium text-gray-300">
                                    {{ $setting['label'] }}
                                </label>

                                @if($setting['type'] === 'textarea')
                                    <textarea name="{{ $setting['key'] }}" id="{{ $setting['key'] }}" rows="3"
                                              class="w-full rounded-xl border border-gray-700 bg-gray-800 px-4 py-3 text-sm text-white placeholder-gray-500 focus:border-peri focus:outline-none focus:ring-1 focus:ring-peri resize-none">{{ old($setting['key'], $setting['value']) }}</textarea>

                                @elseif($setting['type'] === 'number')
                                    <input type="number" name="{{ $setting['key'] }}" id="{{ $setting['key'] }}"
                                           value="{{ old($setting['key'], $setting['value']) }}"
                                           class="w-full rounded-xl border border-gray-700 bg-gray-800 px-4 py-3 text-sm text-white placeholder-gray-500 focus:border-peri focus:outline-none focus:ring-1 focus:ring-peri">

                                @elseif($setting['type'] === 'boolean')
                                    <label class="relative inline-flex cursor-pointer items-center">
                                        <input type="checkbox" name="{{ $setting['key'] }}" value="1"
                                               {{ $setting['value'] == '1' ? 'checked' : '' }}
                                               class="peer sr-only">
                                        <div class="peer h-6 w-11 rounded-full bg-gray-700 after:absolute after:left-[2px] after:top-[2px] after:h-5 after:w-5 after:rounded-full after:bg-white after:transition-all after:content-[''] peer-checked:bg-peri peer-checked:after:translate-x-full"></div>
                                    </label>

                                @elseif($setting['type'] === 'image')
                                    <div x-data="{ preview: '{{ $setting['value'] ? asset('storage/' . $setting['value']) : '' }}' }">
                                        <template x-if="preview">
                                            <div class="mb-3">
                                                <img :src="preview" alt="{{ $setting['label'] }}"
                                                     class="h-16 w-auto rounded-lg border border-gray-700 object-contain">
                                            </div>
                                        </template>
                                        <input type="file" name="{{ $setting['key'] }}" id="{{ $setting['key'] }}" accept="image/*"
                                               @change="preview = URL.createObjectURL($event.target.files[0])"
                                               class="w-full rounded-xl border border-gray-700 bg-gray-800 px-4 py-2.5 text-sm text-gray-400 file:mr-3 file:rounded-lg file:border-0 file:bg-peri/10 file:px-3 file:py-1 file:text-sm file:font-medium file:text-peri">
                                        <p class="mt-1 text-xs text-gray-500">Format: JPG, PNG, SVG. Maks: 2MB</p>
                                    </div>

                                @else
                                    @if($setting['key'] === 'shop_status')
                                        <select name="{{ $setting['key'] }}" id="{{ $setting['key'] }}"
                                                class="w-full rounded-xl border border-gray-700 bg-gray-800 px-4 py-3 text-sm text-white focus:border-peri focus:outline-none focus:ring-1 focus:ring-peri">
                                            <option value="open" {{ $setting['value'] === 'open' ? 'selected' : '' }}>🟢 Buka</option>
                                            <option value="closed" {{ $setting['value'] === 'closed' ? 'selected' : '' }}>🔴 Tutup</option>
                                            <option value="maintenance" {{ $setting['value'] === 'maintenance' ? 'selected' : '' }}>🟡 Maintenance</option>
                                        </select>
                                    @elseif($setting['key'] === 'payment_gateway_provider')
                                        <select name="{{ $setting['key'] }}" id="{{ $setting['key'] }}"
                                                class="w-full rounded-xl border border-gray-700 bg-gray-800 px-4 py-3 text-sm text-white focus:border-peri focus:outline-none focus:ring-1 focus:ring-peri">
                                            <option value="midtrans" {{ $setting['value'] === 'midtrans' ? 'selected' : '' }}>🏦 Midtrans (QRIS, E-Wallet, Bank Transfer)</option>
                                            <option value="xendit" {{ $setting['value'] === 'xendit' ? 'selected' : '' }}>💳 Xendit (Invoice, VA, E-Wallet)</option>
                                        </select>
                                        <p class="mt-1 text-xs text-gray-500">Pilih provider pembayaran.</p>
                                    @elseif($setting['key'] === 'midtrans_server_key' || $setting['key'] === 'xendit_secret_key')
                                        <input type="password" name="{{ $setting['key'] }}" id="{{ $setting['key'] }}"
                                               value="{{ old($setting['key'], $setting['value']) }}"
                                               placeholder="{{ $setting['key'] === 'midtrans_server_key' ? 'Mid-server-xxxx' : 'xnd_development_xxxx' }}"
                                               class="w-full rounded-xl border border-gray-700 bg-gray-800 px-4 py-3 text-sm text-white placeholder-gray-500 focus:border-peri focus:outline-none focus:ring-1 focus:ring-peri">
                                        <p class="mt-1 text-xs text-red-400"><i class="fas fa-lock mr-1"></i>Key ini bersifat rahasia. Jangan bagikan ke siapapun.</p>
                                    @else
                                        <input type="{{ $setting['key'] === 'store_email' ? 'email' : 'text' }}"
                                               name="{{ $setting['key'] }}" id="{{ $setting['key'] }}"
                                               value="{{ old($setting['key'], $setting['value']) }}"
                                               class="w-full rounded-xl border border-gray-700 bg-gray-800 px-4 py-3 text-sm text-white placeholder-gray-500 focus:border-peri focus:outline-none focus:ring-1 focus:ring-peri"
                                               @if($setting['key'] === 'whatsapp_number') placeholder="6281234567890" @endif
                                               @if($setting['key'] === 'instagram_handle') placeholder="shurizastore" @endif
                                               @if($setting['key'] === 'telegram_handle') placeholder="shurizastore" @endif
                                               @if($setting['key'] === 'store_email') placeholder="admin@shurizastore.com" @endif
                                               @if($setting['key'] === 'midtrans_merchant_id') placeholder="G580120824" @endif
                                               @if($setting['key'] === 'midtrans_client_key') placeholder="Mid-client-xxxx" @endif>
                                    @endif

                                    @if($setting['key'] === 'whatsapp_number')
                                        <p class="mt-1 text-xs text-gray-500">Format: kode negara tanpa + (contoh: 6281234567890)</p>
                                    @elseif($setting['key'] === 'instagram_handle')
                                        <p class="mt-1 text-xs text-gray-500">Username Instagram tanpa @ (contoh: shurizastore)</p>
                                    @elseif($setting['key'] === 'auto_cancel_days')
                                        <p class="mt-1 text-xs text-gray-500">Isi 0 untuk menonaktifkan auto-cancel</p>
                                    @elseif($setting['key'] === 'min_order_amount')
                                        <p class="mt-1 text-xs text-gray-500">Isi 0 jika tidak ada minimum order</p>
                                    @elseif($setting['key'] === 'midtrans_merchant_id')
                                        <p class="mt-1 text-xs text-gray-500">Dapatkan di dashboard.midtrans.com → Settings → Access Keys</p>
                                    @elseif($setting['key'] === 'midtrans_client_key')
                                        <p class="mt-1 text-xs text-gray-500">Client key untuk integrasi frontend (Snap.js)</p>
                                    @elseif($setting['key'] === 'xendit_callback_token')
                                        <p class="mt-1 text-xs text-gray-500">Dapatkan di dashboard.xendit.co → Settings → Callbacks</p>
                                    @endif
                                @endif
                            </div>
                        @endforeach
                    @else
                        <p class="text-sm text-gray-500">Belum ada pengaturan untuk grup ini.</p>
                    @endif
                </div>
            </div>
        </div>
        @endforeach

        {{-- Save Button --}}
        <div class="mt-6 flex justify-end">
            <button type="submit"
                    class="inline-flex items-center gap-2 rounded-xl bg-peri px-6 py-3 text-sm font-bold text-white shadow-lg shadow-peri/25 transition-all hover:bg-peri/90 hover:shadow-peri/40">
                <i class="fas fa-save"></i> Simpan Pengaturan
            </button>
        </div>
    </form>

    {{-- WhatsApp Template Variables Info --}}
    <div x-show="activeTab === 'order'" x-cloak class="mt-4">
        <div class="rounded-2xl border border-gray-800 bg-gray-900/50 p-5">
            <h3 class="mb-3 text-sm font-bold text-gray-300"><i class="fas fa-info-circle mr-1 text-peri"></i> Variabel Template WhatsApp</h3>
            <div class="grid grid-cols-2 gap-2 text-xs sm:grid-cols-3">
                <div class="rounded-lg bg-gray-800 px-3 py-2">
                    <code class="text-peri">{order_number}</code>
                    <span class="ml-1 text-gray-500">No. Order</span>
                </div>
                <div class="rounded-lg bg-gray-800 px-3 py-2">
                    <code class="text-peri">{name}</code>
                    <span class="ml-1 text-gray-500">Nama</span>
                </div>
                <div class="rounded-lg bg-gray-800 px-3 py-2">
                    <code class="text-peri">{total}</code>
                    <span class="ml-1 text-gray-500">Total</span>
                </div>
                <div class="rounded-lg bg-gray-800 px-3 py-2">
                    <code class="text-peri">{phone}</code>
                    <span class="ml-1 text-gray-500">No. HP</span>
                </div>
                <div class="rounded-lg bg-gray-800 px-3 py-2">
                    <code class="text-peri">{email}</code>
                    <span class="ml-1 text-gray-500">Email</span>
                </div>
                <div class="rounded-lg bg-gray-800 px-3 py-2">
                    <code class="text-peri">{items}</code>
                    <span class="ml-1 text-gray-500">Daftar Item</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Payment Gateway Guide --}}
    <div x-show="activeTab === 'payment'" x-cloak class="mt-4">
        <div class="rounded-2xl border border-amber-500/30 bg-amber-500/5 p-5">
            <h3 class="mb-3 text-sm font-bold text-amber-400"><i class="fas fa-exclamation-triangle mr-1"></i> Panduan Konfigurasi Midtrans</h3>
            <ol class="space-y-2 text-xs text-gray-400 list-decimal list-inside">
                <li>Buka <a href="https://dashboard.sandbox.midtrans.com" target="_blank" class="text-peri underline">dashboard.sandbox.midtrans.com</a> (untuk testing)</li>
                <li>Login atau daftar akun Midtrans</li>
                <li>Pergi ke <strong class="text-white">Settings → Access Keys</strong></li>
                <li>Copy <strong class="text-white">Merchant ID</strong>, <strong class="text-white">Client Key</strong>, dan <strong class="text-white">Server Key</strong></li>
                <li>Paste ke form di atas</li>
                <li>Centang <strong class="text-white">"Aktifkan Payment Gateway"</strong></li>
                <li><strong class="text-red-400">JANGAN</strong> centang "Mode Production" untuk testing sandbox</li>
            </ol>
            <div class="mt-4 rounded-lg bg-gray-800 p-3">
                <p class="text-xs text-gray-400 mb-2"><i class="fas fa-link mr-1"></i> Notification URL (wajib diset di dashboard Midtrans):</p>
                <code class="text-xs text-green-400 break-all">{{ url('/payment/notification') }}</code>
            </div>
        </div>
    </div>
</div>
@endsection
