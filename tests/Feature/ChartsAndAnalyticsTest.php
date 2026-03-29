<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ChartsAndAnalyticsTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that analytics metrics are calculated correctly in the reports page.
     */
    public function test_reports_page_displays_checkout_funnel(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        // Create test orders with various statuses
        for ($i = 0; $i < 5; $i++) {
            Order::create(['order_number' => Order::generateOrderNumber(), 'name' => 'Order ' . $i, 'phone' => '08123456789', 'email' => 'test@test.com', 'status' => 'pending', 'total' => 100000]);
        }
        for ($i = 0; $i < 3; $i++) {
            Order::create(['order_number' => Order::generateOrderNumber(), 'name' => 'Order ' . $i, 'phone' => '08123456789', 'email' => 'test@test.com', 'status' => 'processing', 'total' => 100000]);
        }
        for ($i = 0; $i < 2; $i++) {
            Order::create(['order_number' => Order::generateOrderNumber(), 'name' => 'Order ' . $i, 'phone' => '08123456789', 'email' => 'test@test.com', 'status' => 'completed', 'total' => 100000]);
        }
        Order::create(['order_number' => Order::generateOrderNumber(), 'name' => 'Order Cancelled', 'phone' => '08123456789', 'email' => 'test@test.com', 'status' => 'cancelled', 'total' => 100000]);

        $response = $this->actingAs($admin)->get(route('admin.reports.index'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.reports-index');
        $response->assertViewHas('checkoutFunnel');

        // Verify funnel data
        $checkoutFunnel = $response->viewData('checkoutFunnel');
        $this->assertEquals(11, $checkoutFunnel['checkout_created']); // All orders (5+3+2+1)
        $this->assertEquals(10, $checkoutFunnel['paid']); // Non-cancelled orders (5+3+2)
        $this->assertEquals(2, $checkoutFunnel['completed']); // Only completed
    }

    /**
     * Test repeat buyers metrics calculation.
     */
    public function test_reports_page_displays_repeat_buyers(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        // User1 has 3 orders (repeat buyer)
        for ($i = 0; $i < 3; $i++) {
            Order::create(['order_number' => Order::generateOrderNumber(), 'user_id' => $user1->id, 'name' => 'User1 Order', 'phone' => '08123456789', 'email' => 'test@test.com', 'status' => 'completed', 'total' => 100000]);
        }

        // User2 has 2 orders (repeat buyer)
        for ($i = 0; $i < 2; $i++) {
            Order::create(['order_number' => Order::generateOrderNumber(), 'user_id' => $user2->id, 'name' => 'User2 Order', 'phone' => '08123456789', 'email' => 'test@test.com', 'status' => 'completed', 'total' => 100000]);
        }

        // Another user with 1 order (new buyer)
        $user3 = User::factory()->create();
        Order::create(['order_number' => Order::generateOrderNumber(), 'user_id' => $user3->id, 'name' => 'User3 Order', 'phone' => '08123456789', 'email' => 'test@test.com', 'status' => 'completed', 'total' => 100000]);

        $response = $this->actingAs($admin)->get(route('admin.reports.index'));

        $response->assertStatus(200);
        $response->assertViewHas('repeatBuyersData');

        $repeatBuyers = $response->viewData('repeatBuyersData');
        $this->assertEquals(2, $repeatBuyers['customer_count']); // 2 customers with 2+ orders
        $this->assertEquals(5, $repeatBuyers['total_orders']); // 3 + 2 orders
    }

    /**
     * Test coupon conversion metrics.
     */
    public function test_reports_page_displays_coupon_conversion(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        // Orders with coupons
        for ($i = 0; $i < 3; $i++) {
            Order::create(['order_number' => Order::generateOrderNumber(), 'name' => 'Order', 'phone' => '08123456789', 'email' => 'test@test.com', 'status' => 'completed', 'coupon_code' => 'COUPON1', 'discount_amount' => 50000, 'total' => 100000]);
        }

        // Orders without coupons
        for ($i = 0; $i < 7; $i++) {
            Order::create(['order_number' => Order::generateOrderNumber(), 'name' => 'Order', 'phone' => '08123456789', 'email' => 'test@test.com', 'status' => 'completed', 'coupon_code' => null, 'discount_amount' => 0, 'total' => 100000]);
        }

        $response = $this->actingAs($admin)->get(route('admin.reports.index'));

        $response->assertStatus(200);
        $response->assertViewHas('couponConversion');

        $coupon = $response->viewData('couponConversion');
        $this->assertEquals(3, $coupon['orders_with_coupon']);
        $this->assertEquals(10, $coupon['total_orders']);
        $this->assertEquals(30.0, $coupon['conversion_rate']); // 3/10 = 30%
        $this->assertEquals(150000, $coupon['coupon_revenue']); // 50000 * 3
    }

    /**
     * Test that charts are included in the response HTML.
     */
    public function test_reports_page_includes_chart_js(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        for ($i = 0; $i < 5; $i++) {
            Order::create(['order_number' => Order::generateOrderNumber(), 'name' => 'Order', 'phone' => '08123456789', 'email' => 'test@test.com', 'status' => 'completed', 'total' => 100000]);
        }

        $response = $this->actingAs($admin)->get(route('admin.reports.index'));

        // Check that Chart.js CDN is loaded
        $response->assertSee('chart.min.js');

        // Check that chart canvases are present
        $response->assertSee('funnelChart');
        $response->assertSee('repeatBuyersChart');
        $response->assertSee('couponChart');
    }
}
