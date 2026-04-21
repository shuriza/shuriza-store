<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\CustomerDashboardController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\SettingsController as AdminSettingsController;
use App\Http\Controllers\Admin\ReviewController as AdminReviewController;
use App\Http\Controllers\Admin\CouponController as AdminCouponController;
use App\Http\Controllers\Admin\StockAlertController as AdminStockAlertController;
use App\Http\Controllers\Admin\BannerController as AdminBannerController;
use App\Http\Controllers\Admin\ArticleController as AdminArticleController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\WishlistController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\SeoController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\Admin\FaqController as AdminFaqController;
use App\Http\Controllers\Admin\ReportController as AdminReportController;

/*
|--------------------------------------------------------------------------
| Frontend Routes
|--------------------------------------------------------------------------
*/

// Halaman Utama
Route::get("/", [HomeController::class, "index"])->name("home");

// Search (AJAX)
Route::get("/search", [HomeController::class, "search"])->name("search");

// Social proof - recent orders (AJAX)
Route::get("/api/recent-orders", [HomeController::class, "recentOrders"])->middleware("throttle:10,1")->name("api.recent-orders");

// SEO
Route::get("/sitemap.xml", [SeoController::class, "sitemap"])->name("sitemap");
Route::get("/robots.txt", [SeoController::class, "robots"])->name("robots");

// Produk
Route::get("/produk", [ProductController::class, "index"])->name(
    "products.index",
);
Route::get("/produk/{product:slug}", [ProductController::class, "show"])->name(
    "products.show",
);
// Redirect /product/{slug} ke /produk/{slug} untuk konsistensi
Route::get("/product/{slug}", function (string $slug) {
    return redirect()->route('products.show', $slug, 301);
})->name("product.show");

// Reviews
Route::post("/produk/{product:slug}/review", [ReviewController::class, "store"])->middleware("throttle:5,1")->name("reviews.store");
Route::delete("/review/{review}", [ReviewController::class, "destroy"])->middleware("throttle:10,1")->name("reviews.destroy");

/*
|--------------------------------------------------------------------------
| Cart Routes
|--------------------------------------------------------------------------
*/

Route::prefix("cart")
    ->name("cart.")
    ->group(function () {
        Route::get("/", [CartController::class, "index"])->name("index");
        Route::post("/add", [CartController::class, "add"])->middleware("throttle:30,1")->name("add");
        Route::patch("/{cartItem}", [CartController::class, "update"])->middleware("throttle:30,1")->name(
            "update",
        );
        Route::delete("/clear", [CartController::class, "clear"])->name(
            "clear",
        );
        Route::delete("/{cartItem}", [CartController::class, "remove"])->name(
            "remove",
        );
        Route::get("/count", [CartController::class, "count"])->name("count");
        Route::get("/items", [CartController::class, "items"])->name("items");
    });

/*
|--------------------------------------------------------------------------
| Order / Checkout Routes
|--------------------------------------------------------------------------
*/

Route::prefix("order")
    ->name("order.")
    ->group(function () {
        Route::get("/checkout", [OrderController::class, "checkout"])->name(
            "checkout",
        );
        Route::post("/checkout", [OrderController::class, "store"])->middleware("throttle:5,1")->name(
            "store",
        );
        Route::post("/apply-coupon", [OrderController::class, "applyCoupon"])->middleware("throttle:10,1")->name(
            "apply-coupon",
        );
        Route::post("/remove-coupon", [OrderController::class, "removeCoupon"])->name(
            "remove-coupon",
        );
        Route::get("/success/{orderNumber}", [
            OrderController::class,
            "success",
        ])->middleware("throttle:20,1")->name("success");
        Route::get("/whatsapp/{orderNumber}", [
            OrderController::class,
            "whatsapp",
        ])->middleware("throttle:10,1")->name("whatsapp");

        // Riwayat & detail order (harus login)
        Route::middleware("auth")->group(function () {
            Route::get("/history", [OrderController::class, "history"])->name(
                "history",
            );
            Route::get("/{orderNumber}", [
                OrderController::class,
                "show",
            ])->name("show");
        });
    });

// Payment Gateway
Route::get("/payment/{orderNumber}", [PaymentController::class, "pay"])->name("payment.pay");
Route::get("/payment/{orderNumber}/finish", [PaymentController::class, "finish"])->name("payment.finish");
Route::post("/payment/notification", [PaymentController::class, "notification"])->name("payment.notification");

/*
|--------------------------------------------------------------------------
| Customer Dashboard (harus login)
|--------------------------------------------------------------------------
*/

Route::middleware("auth")->group(function () {
    Route::get("/dashboard", [CustomerDashboardController::class, "index"])->name(
        "dashboard",
    );

    // Wishlist
    Route::get("/wishlist", [WishlistController::class, "index"])->name("wishlist.index");
    Route::post("/wishlist/{product}", [WishlistController::class, "toggle"])->middleware("throttle:30,1")->name("wishlist.toggle");

    // Notifications
    Route::get("/notifikasi", [NotificationController::class, "index"])->name("notifications.index");
    Route::post("/notifikasi/{notification}/baca", [NotificationController::class, "markAsRead"])->name("notifications.read");
    Route::post("/notifikasi/baca-semua", [NotificationController::class, "markAllAsRead"])->name("notifications.readAll");
    Route::get("/notifikasi/unread-count", [NotificationController::class, "unreadCount"])->name("notifications.unreadCount");

    // Invoice
    Route::get("/invoice/{orderNumber}", [InvoiceController::class, "show"])->name("invoice.show");
    Route::get("/invoice/{orderNumber}/download", [InvoiceController::class, "download"])->name("invoice.download");
});

/*
|--------------------------------------------------------------------------
| Static / Info Pages
|--------------------------------------------------------------------------
*/

// Cek Order (tanpa login)
Route::get("/cek-order", [OrderController::class, "trackForm"])->name("order.track");
Route::post("/cek-order", [OrderController::class, "track"])->middleware("throttle:5,1")->name("order.track.submit");

Route::get("/cara-pembelian", [PageController::class, "howToBuy"])->name("pages.how-to-buy");
Route::get("/faq", [PageController::class, "faq"])->name("pages.faq");
Route::get("/kebijakan-privasi", [PageController::class, "privacy"])->name("pages.privacy");
Route::get("/syarat-ketentuan", [PageController::class, "terms"])->name("pages.terms");
Route::get("/tentang-kami", [PageController::class, "about"])->name("pages.about");
Route::get("/hubungi-kami", [PageController::class, "contact"])->name("pages.contact");

// Promo / Diskon
Route::get("/promo", [ProductController::class, "promo"])->name("products.promo");

// Kategori dedicated page
Route::get("/kategori/{category:slug}", [ProductController::class, "category"])->name("products.category");

// Artikel
Route::get("/artikel", [ArticleController::class, "index"])->name("articles.index");
Route::get("/artikel/{article:slug}", [ArticleController::class, "show"])->name("articles.show");

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/

Route::prefix("admin")
    ->name("admin.")
    ->middleware(["auth", "admin"])
    ->group(function () {
        // Dashboard
        Route::get("/", [DashboardController::class, "index"])->name(
            "dashboard",
        );

        // Products
        Route::prefix("products")
            ->name("products.")
            ->group(function () {
                Route::get("/", [AdminProductController::class, "index"])->name(
                    "index",
                );
                Route::get("/create", [
                    AdminProductController::class,
                    "create",
                ])->name("create");
                Route::post("/", [
                    AdminProductController::class,
                    "store",
                ])->name("store");
                Route::get("/{product}/edit", [
                    AdminProductController::class,
                    "edit",
                ])->name("edit");
                Route::put("/{product}", [
                    AdminProductController::class,
                    "update",
                ])->name("update");
                Route::delete("/{product}", [
                    AdminProductController::class,
                    "destroy",
                ])->name("destroy");
                Route::patch("/{product}/toggle-active", [
                    AdminProductController::class,
                    "toggleActive",
                ])->name("toggle-active");
                Route::patch("/{product}/toggle-popular", [
                    AdminProductController::class,
                    "togglePopular",
                ])->name("toggle-popular");
                Route::post("/bulk", [
                    AdminProductController::class,
                    "bulk",
                ])->name("bulk");
            });

        // Categories
        Route::prefix("categories")
            ->name("categories.")
            ->group(function () {
                Route::get("/", [
                    AdminCategoryController::class,
                    "index",
                ])->name("index");
                Route::get("/create", [
                    AdminCategoryController::class,
                    "create",
                ])->name("create");
                Route::post("/", [
                    AdminCategoryController::class,
                    "store",
                ])->name("store");
                Route::get("/{category}/edit", [
                    AdminCategoryController::class,
                    "edit",
                ])->name("edit");
                Route::put("/{category}", [
                    AdminCategoryController::class,
                    "update",
                ])->name("update");
                Route::delete("/{category}", [
                    AdminCategoryController::class,
                    "destroy",
                ])->name("destroy");
                Route::patch("/{category}/toggle-active", [
                    AdminCategoryController::class,
                    "toggleActive",
                ])->name("toggle-active");
                Route::post("/update-order", [
                    AdminCategoryController::class,
                    "updateOrder",
                ])->name("update-order");
            });

        // Orders
        Route::prefix("orders")
            ->name("orders.")
            ->group(function () {
                Route::get("/", [AdminOrderController::class, "index"])->name(
                    "index",
                );
                Route::get("/export", [
                    AdminOrderController::class,
                    "export",
                ])->name("export");
                Route::get("/{order}", [
                    AdminOrderController::class,
                    "show",
                ])->name("show");
                Route::patch("/{order}/status", [
                    AdminOrderController::class,
                    "updateStatus",
                ])->name("update-status");
                Route::patch("/{order}/notes", [
                    AdminOrderController::class,
                    "updateNotes",
                ])->name("update-notes");
                Route::delete("/{order}", [
                    AdminOrderController::class,
                    "destroy",
                ])->name("destroy");
                Route::post("/bulk-status", [
                    AdminOrderController::class,
                    "bulkUpdateStatus",
                ])->name("bulk-status");
                Route::get("/{order}/whatsapp", [
                    AdminOrderController::class,
                    "contactWhatsApp",
                ])->name("whatsapp");
            });

        // Users
        Route::prefix("users")
            ->name("users.")
            ->group(function () {
                Route::get("/", [AdminUserController::class, "index"])->name(
                    "index",
                );
                Route::get("/{user}", [
                    AdminUserController::class,
                    "show",
                ])->name("show");
                Route::patch("/{user}/toggle-role", [
                    AdminUserController::class,
                    "toggleRole",
                ])->name("toggle-role");
                Route::delete("/{user}", [
                    AdminUserController::class,
                    "destroy",
                ])->name("destroy");
            });

        // Settings
        Route::prefix("settings")
            ->name("settings.")
            ->group(function () {
                Route::get("/", [AdminSettingsController::class, "index"])->name("index");
                Route::put("/", [AdminSettingsController::class, "update"])->name("update");
            });

        // Reviews
        Route::prefix("reviews")
            ->name("reviews.")
            ->group(function () {
                Route::get("/", [AdminReviewController::class, "index"])->name("index");
                Route::patch("/{review}/toggle", [AdminReviewController::class, "toggleApproval"])->name("toggle");
                Route::delete("/{review}", [AdminReviewController::class, "destroy"])->name("destroy");
            });

        // Coupons
        Route::prefix("coupons")
            ->name("coupons.")
            ->group(function () {
                Route::get("/", [AdminCouponController::class, "index"])->name("index");
                Route::get("/create", [AdminCouponController::class, "create"])->name("create");
                Route::post("/", [AdminCouponController::class, "store"])->name("store");
                Route::get("/{coupon}/edit", [AdminCouponController::class, "edit"])->name("edit");
                Route::put("/{coupon}", [AdminCouponController::class, "update"])->name("update");
                Route::patch("/{coupon}/toggle", [AdminCouponController::class, "toggleActive"])->name("toggle");
                Route::delete("/{coupon}", [AdminCouponController::class, "destroy"])->name("destroy");
            });

        // Stock Alerts
        Route::prefix("stock-alerts")
            ->name("stock-alerts.")
            ->group(function () {
                Route::get("/", [AdminStockAlertController::class, "index"])->name("index");
                Route::post("/send", [AdminStockAlertController::class, "sendAlert"])->name("send");
            });

        // Banners
        Route::prefix("banners")
            ->name("banners.")
            ->group(function () {
                Route::get("/", [AdminBannerController::class, "index"])->name("index");
                Route::get("/create", [AdminBannerController::class, "create"])->name("create");
                Route::post("/", [AdminBannerController::class, "store"])->name("store");
                Route::get("/{banner}/edit", [AdminBannerController::class, "edit"])->name("edit");
                Route::put("/{banner}", [AdminBannerController::class, "update"])->name("update");
                Route::patch("/{banner}/toggle", [AdminBannerController::class, "toggleActive"])->name("toggle");
                Route::delete("/{banner}", [AdminBannerController::class, "destroy"])->name("destroy");
            });

        // Articles
        Route::prefix("articles")
            ->name("articles.")
            ->group(function () {
                Route::get("/", [AdminArticleController::class, "index"])->name("index");
                Route::get("/create", [AdminArticleController::class, "create"])->name("create");
                Route::post("/", [AdminArticleController::class, "store"])->name("store");
                Route::get("/{article}/edit", [AdminArticleController::class, "edit"])->name("edit");
                Route::put("/{article}", [AdminArticleController::class, "update"])->name("update");
                Route::patch("/{article}/toggle", [AdminArticleController::class, "togglePublish"])->name("toggle");
                Route::delete("/{article}", [AdminArticleController::class, "destroy"])->name("destroy");
            });

        // FAQ
        Route::prefix("faqs")
            ->name("faqs.")
            ->group(function () {
                Route::get("/", [AdminFaqController::class, "index"])->name("index");
                Route::get("/create", [AdminFaqController::class, "create"])->name("create");
                Route::post("/", [AdminFaqController::class, "store"])->name("store");
                Route::get("/{faq}/edit", [AdminFaqController::class, "edit"])->name("edit");
                Route::put("/{faq}", [AdminFaqController::class, "update"])->name("update");
                Route::patch("/{faq}/toggle", [AdminFaqController::class, "toggleActive"])->name("toggle");
                Route::delete("/{faq}", [AdminFaqController::class, "destroy"])->name("destroy");
            });

        // Reports
        Route::prefix("reports")
            ->name("reports.")
            ->group(function () {
                Route::get("/", [AdminReportController::class, "index"])->name("index");
                Route::get("/export", [AdminReportController::class, "export"])->name("export");
            });

        // Digital Delivery
        Route::post("/orders/{order}/items/{item}/deliver", [AdminOrderController::class, "deliverItem"])->name("orders.deliver-item");

        // Admin quick stats API (untuk notification bell & auto-refresh)
        Route::get("/api/quick-stats", [DashboardController::class, "quickStats"])->name("api.quick-stats");
    });

/*
|--------------------------------------------------------------------------
| Auth Routes (Breeze)
|--------------------------------------------------------------------------
*/

require __DIR__ . "/auth.php";
