<?php

namespace App\Providers;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Create required directories if they don't exist
        $dirs = [
            resource_path('views/pages'),
            resource_path('views/errors'),
            resource_path('views/admin/reviews'),
            resource_path('views/admin/coupons'),
            resource_path('views/emails'),
            app_path('Mail'),
        ];

        foreach ($dirs as $dir) {
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }
        }

        // ── Cache invalidation saat data berubah ──────────────────────────
        $clearHomeCache = function () {
            Cache::forget('home_categories');
            Cache::forget('home_popular');
            Cache::forget('home_stats');
            Cache::forget('home_banners');
            Cache::forget('social_proof_orders');
        };

        \App\Models\Product::saved($clearHomeCache);
        \App\Models\Product::deleted($clearHomeCache);
        \App\Models\Category::saved($clearHomeCache);
        \App\Models\Category::deleted($clearHomeCache);
        \App\Models\Banner::saved($clearHomeCache);
        \App\Models\Banner::deleted($clearHomeCache);
        \App\Models\Order::saved($clearHomeCache);
    }
}
