<?php

namespace Tests\Feature;

use App\Models\CartItem;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\CouponUsage;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DigitalDeliveryCouponAnalyticsTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_retry_failed_digital_delivery_notification(): void
    {
        /** @var User $admin */
        $admin = User::factory()->create(['role' => 'admin']);
        /** @var User $buyer */
        $buyer = User::factory()->create();

        $order = $this->createOrder($buyer->id, 'processing', 20000, null);
        $item = OrderItem::create([
            'order_id' => $order->id,
            'product_id' => null,
            'product_name' => 'Akun Premium',
            'price' => 20000,
            'quantity' => 1,
            'subtotal' => 20000,
            'delivery_type' => 'account',
            'delivery_data' => 'user:test|pass:123',
            'delivery_status' => 'failed',
            'delivery_attempts' => 1,
            'last_delivery_error' => 'Queue timeout',
        ]);

        /** @var Authenticatable $adminAuth */
        $adminAuth = $admin;

        $response = $this->actingAs($adminAuth)
            ->post(route('admin.orders.retry-delivery', [$order, $item]));

        $response->assertRedirect();

        $item->refresh();
        $this->assertSame('delivered', $item->delivery_status);
        $this->assertSame(2, $item->delivery_attempts);
        $this->assertNull($item->last_delivery_error);

        $this->assertDatabaseHas('notifications', [
            'user_id' => $buyer->id,
            'type' => 'delivery',
        ]);
    }

    public function test_coupon_applies_advanced_rules_min_items_category_and_per_user_limit(): void
    {
        /** @var User $user */
        $user = User::factory()->create();

        $allowedCategory = Category::create([
            'name' => 'Voucher Allowed',
            'slug' => 'voucher-allowed',
            'is_active' => true,
        ]);

        $otherCategory = Category::create([
            'name' => 'Voucher Other',
            'slug' => 'voucher-other',
            'is_active' => true,
        ]);

        $allowedProduct = $this->createProduct($allowedCategory, 20000, 'Allowed Product');
        $otherProduct = $this->createProduct($otherCategory, 15000, 'Other Product');

        $coupon = Coupon::create([
            'code' => 'RULE50',
            'name' => 'Rule Based Coupon',
            'type' => 'fixed',
            'value' => 5000,
            'min_order' => 0,
            'min_total_items' => 2,
            'allowed_category_ids' => [$allowedCategory->id],
            'usage_limit_per_user' => 1,
            'is_active' => true,
        ]);

        CartItem::create([
            'user_id' => $user->id,
            'product_id' => $otherProduct->id,
            'quantity' => 1,
        ]);

        /** @var Authenticatable $userAuth */
        $userAuth = $user;

        $this->actingAs($userAuth)
            ->post(route('order.apply-coupon'), ['coupon_code' => $coupon->code])
            ->assertSessionHas('error');

        CartItem::query()->update(['quantity' => 2]);

        $this->actingAs($userAuth)
            ->post(route('order.apply-coupon'), ['coupon_code' => $coupon->code])
            ->assertSessionHas('error', 'Kupon hanya berlaku untuk kategori produk tertentu.');

        CartItem::create([
            'user_id' => $user->id,
            'product_id' => $allowedProduct->id,
            'quantity' => 1,
        ]);

        $usedOrder = $this->createOrder($user->id, 'completed', 30000, $coupon->code);
        CouponUsage::create([
            'coupon_id' => $coupon->id,
            'order_id' => $usedOrder->id,
            'user_id' => $user->id,
            'discount_amount' => 5000,
            'used_at' => now(),
        ]);

        $this->actingAs($userAuth)
            ->post(route('order.apply-coupon'), ['coupon_code' => $coupon->code])
            ->assertSessionHas('error', 'Batas penggunaan kupon per akun sudah tercapai.');
    }

    public function test_admin_report_includes_funnel_repeat_buyers_and_coupon_conversion(): void
    {
        /** @var User $admin */
        $admin = User::factory()->create(['role' => 'admin']);
        /** @var User $buyer */
        $buyer = User::factory()->create();

        $coupon = Coupon::create([
            'code' => 'ANALYTIC10',
            'name' => 'Analytics Coupon',
            'type' => 'fixed',
            'value' => 10000,
            'min_order' => 0,
            'is_active' => true,
        ]);

        $completedOne = $this->createOrder($buyer->id, 'completed', 40000, $coupon->code);
        $this->createOrder($buyer->id, 'completed', 35000, null);
        $this->createOrder($buyer->id, 'processing', 30000, null);
        $this->createOrder($buyer->id, 'cancelled', 25000, null);

        CouponUsage::create([
            'coupon_id' => $coupon->id,
            'order_id' => $completedOne->id,
            'user_id' => $buyer->id,
            'discount_amount' => 10000,
            'used_at' => now(),
        ]);

        /** @var Authenticatable $adminAuth */
        $adminAuth = $admin;

        $response = $this->actingAs($adminAuth)->get(route('admin.reports.index', [
            'date_from' => now()->subDay()->toDateString(),
            'date_to' => now()->addDay()->toDateString(),
        ]));

        $response->assertOk();
        $response->assertViewHas('checkoutFunnel', function (array $funnel): bool {
            return $funnel['checkout_created'] === 3
                && $funnel['paid'] === 3
                && $funnel['completed'] === 2;
        });

        $response->assertViewHas('repeatBuyers', function (array $repeat): bool {
            return $repeat['customers'] === 1
                && $repeat['orders'] === 3
                && $repeat['revenue'] === 105000;
        });

        $response->assertViewHas('couponConversion', function (array $coupon): bool {
            return $coupon['orders_with_coupon'] === 1
                && $coupon['usage_events'] === 1
                && (float) $coupon['conversion_rate'] === 33.33
                && $coupon['coupon_revenue'] === 40000;
        });
    }

    private function createOrder(?int $userId, string $status, int $total, ?string $couponCode): Order
    {
        return Order::create([
            'user_id' => $userId,
            'order_number' => Order::generateOrderNumber(),
            'name' => 'Test Buyer',
            'phone' => '6281234567890',
            'email' => 'buyer@example.com',
            'total' => $total,
            'status' => $status,
            'coupon_code' => $couponCode,
            'discount_amount' => $couponCode ? 10000 : 0,
        ]);
    }

    private function createProduct(Category $category, int $price, string $name): Product
    {
        return Product::create([
            'category_id' => $category->id,
            'name' => $name,
            'slug' => str($name)->slug() . '-' . fake()->unique()->numberBetween(100, 999),
            'price' => $price,
            'stock' => 99,
            'is_active' => true,
        ]);
    }
}
