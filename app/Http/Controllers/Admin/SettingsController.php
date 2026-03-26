<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingsController extends Controller
{
    /**
     * Tampilkan halaman pengaturan.
     */
    public function index()
    {
        $this->ensureDefaults();

        $groups = [
            'store' => [
                'title' => 'Informasi Toko',
                'icon'  => 'fa-store',
                'desc'  => 'Nama, deskripsi, dan identitas toko',
            ],
            'contact' => [
                'title' => 'Kontak & Sosial Media',
                'icon'  => 'fa-address-book',
                'desc'  => 'WhatsApp, Instagram, Telegram, Email',
            ],
            'shop' => [
                'title' => 'Pengaturan Toko',
                'icon'  => 'fa-sliders-h',
                'desc'  => 'Status toko, minimum order, maintenance',
            ],
            'appearance' => [
                'title' => 'Pengaturan Tampilan',
                'icon'  => 'fa-palette',
                'desc'  => 'Hero banner, produk per halaman',
            ],
            'order' => [
                'title' => 'Pengaturan Order',
                'icon'  => 'fa-shopping-bag',
                'desc'  => 'Template WhatsApp, auto-cancel',
            ],
            'payment' => [
                'title' => 'Payment Gateway',
                'icon'  => 'fa-credit-card',
                'desc'  => 'Midtrans, Xendit — aktifkan pembayaran otomatis',
            ],
        ];

        $settings = Setting::all()->groupBy('group');

        return view('admin.setting.index', compact('groups', 'settings'));
    }

    /**
     * Simpan pengaturan.
     */
    public function update(Request $request)
    {
        $settings = Setting::all()->keyBy('key');

        foreach ($settings as $key => $setting) {
            if ($setting->type === 'boolean') {
                $value = $request->has($key) ? '1' : '0';
            } elseif ($setting->type === 'image') {
                if ($request->hasFile($key)) {
                    $request->validate([$key => 'image|max:2048']);
                    // Delete old image if exists
                    if ($setting->value && Storage::disk('public')->exists($setting->value)) {
                        Storage::disk('public')->delete($setting->value);
                    }
                    $value = $request->file($key)->store('settings', 'public');
                } else {
                    continue; // Skip if no new file uploaded
                }
            } else {
                $value = $request->input($key, '');
            }

            $setting->update(['value' => $value]);
        }

        // Clear settings cache so changes take effect immediately
        \Illuminate\Support\Facades\Cache::forget('app_settings');

        return back()->with('success', 'Pengaturan berhasil disimpan!');
    }

    /**
     * Pastikan semua default settings ada di database.
     */
    private function ensureDefaults(): void
    {
        $defaults = [
            // Informasi Toko
            ['group' => 'store', 'key' => 'store_name',        'value' => 'Shuriza Store Kediri', 'type' => 'text',     'label' => 'Nama Toko'],
            ['group' => 'store', 'key' => 'store_tagline',     'value' => 'Penyedia layanan digital terpercaya', 'type' => 'text', 'label' => 'Tagline Toko (footer)'],
            ['group' => 'store', 'key' => 'store_description',  'value' => 'Toko digital produk terlengkap di Kediri', 'type' => 'textarea', 'label' => 'Deskripsi Toko'],
            ['group' => 'store', 'key' => 'store_address',      'value' => 'Kediri, Jawa Timur, Indonesia', 'type' => 'textarea', 'label' => 'Alamat Toko'],
            ['group' => 'store', 'key' => 'store_logo',         'value' => null, 'type' => 'image',    'label' => 'Logo Toko'],
            ['group' => 'store', 'key' => 'store_favicon',      'value' => null, 'type' => 'image',    'label' => 'Favicon (ikon tab browser)'],

            // Kontak & Sosial Media
            ['group' => 'contact', 'key' => 'whatsapp_number',  'value' => config('app.whatsapp_number', '6281234567890'), 'type' => 'text', 'label' => 'Nomor WhatsApp'],
            ['group' => 'contact', 'key' => 'instagram_handle', 'value' => config('app.instagram_handle', 'shurizastore'), 'type' => 'text', 'label' => 'Instagram Handle'],
            ['group' => 'contact', 'key' => 'telegram_handle',  'value' => config('app.telegram_handle', 'shurizastore'),  'type' => 'text', 'label' => 'Telegram Handle'],
            ['group' => 'contact', 'key' => 'store_email',      'value' => 'admin@shurizastore.com', 'type' => 'text',    'label' => 'Email Toko'],

            // Pengaturan Toko
            ['group' => 'shop', 'key' => 'shop_status',         'value' => 'open',   'type' => 'text',     'label' => 'Status Toko'],
            ['group' => 'shop', 'key' => 'maintenance_message', 'value' => 'Toko sedang dalam perbaikan. Silakan kembali lagi nanti.', 'type' => 'textarea', 'label' => 'Pesan Maintenance'],
            ['group' => 'shop', 'key' => 'min_order_amount',    'value' => '0',      'type' => 'number',   'label' => 'Minimum Order (Rp)'],

            // Pengaturan Tampilan
            ['group' => 'appearance', 'key' => 'hero_title',    'value' => 'Produk Digital Premium', 'type' => 'text', 'label' => 'Judul Hero Banner'],
            ['group' => 'appearance', 'key' => 'hero_subtitle', 'value' => 'Temukan berbagai produk digital berkualitas untuk kebutuhan Anda', 'type' => 'textarea', 'label' => 'Subtitle Hero Banner'],
            ['group' => 'appearance', 'key' => 'products_per_page', 'value' => '12', 'type' => 'number', 'label' => 'Produk per Halaman'],

            // Pengaturan Order
            ['group' => 'order', 'key' => 'order_wa_template',  'value' => "Halo {store_name}!\nSaya sudah melakukan pemesanan:\n\n*No. Order:* {order_number}\n*Nama:* {name}\n*Total:* {total}\n\nMohon diproses, terima kasih!", 'type' => 'textarea', 'label' => 'Template Pesan WhatsApp Order'],
            ['group' => 'order', 'key' => 'auto_cancel_days',   'value' => '0', 'type' => 'number',  'label' => 'Auto Cancel (hari, 0 = nonaktif)'],

            // Payment Gateway
            ['group' => 'payment', 'key' => 'payment_gateway_enabled',   'value' => '0',        'type' => 'boolean', 'label' => 'Aktifkan Payment Gateway'],
            ['group' => 'payment', 'key' => 'payment_gateway_provider',  'value' => 'midtrans',  'type' => 'text',    'label' => 'Provider'],
            ['group' => 'payment', 'key' => 'midtrans_merchant_id',      'value' => '',          'type' => 'text',    'label' => 'Midtrans Merchant ID'],
            ['group' => 'payment', 'key' => 'midtrans_client_key',       'value' => '',          'type' => 'text',    'label' => 'Midtrans Client Key'],
            ['group' => 'payment', 'key' => 'midtrans_server_key',       'value' => '',          'type' => 'text',    'label' => 'Midtrans Server Key'],
            ['group' => 'payment', 'key' => 'midtrans_is_production',    'value' => '0',         'type' => 'boolean', 'label' => 'Mode Production (centang untuk live, kosongkan untuk sandbox)'],
            ['group' => 'payment', 'key' => 'xendit_secret_key',         'value' => '',          'type' => 'text',    'label' => 'Xendit Secret Key'],
            ['group' => 'payment', 'key' => 'xendit_callback_token',     'value' => '',          'type' => 'text',    'label' => 'Xendit Callback Token'],
        ];

        foreach ($defaults as $default) {
            Setting::firstOrCreate(
                ['key' => $default['key']],
                $default
            );
        }
    }
}
