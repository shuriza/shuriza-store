<?php

namespace Tests\Feature;

use App\Models\CartItem;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Setting;
use App\Models\User;
use App\Services\XenditService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class PaymentAndCheckoutFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_checkout_store_creates_order_and_reduces_stock_for_authenticated_user(): void
    {
        $this->setSetting('payment_gateway_enabled', '0');

        /** @var User $user */
        $user = User::factory()->create();
        $product = $this->createProduct(5, 10000);

        CartItem::create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'quantity' => 2,
        ]);

        Auth::login($user);

        $response = $this->post(route('order.store'), [
            'name' => 'Budi',
            'phone' => '6281234567890',
            'email' => 'budi@example.com',
            'notes' => 'Tolong cepat',
        ]);

        $order = Order::query()->first();

        $response->assertRedirect(route('order.success', $order->order_number));

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'user_id' => $user->id,
            'status' => 'pending',
            'total' => 20000,
        ]);

        $this->assertDatabaseHas('order_items', [
            'order_id' => $order->id,
            'product_id' => $product->id,
            'quantity' => 2,
            'subtotal' => 20000,
        ]);

        $this->assertDatabaseCount('cart_items', 0);
        $this->assertSame(3, $product->fresh()->stock);
    }

    public function test_xendit_create_transaction_updates_order_with_invoice_url(): void
    {
        $this->setSetting('payment_gateway_provider', 'xendit');
        $this->setSetting('xendit_secret_key', 'xnd_development_secret');

        $order = $this->createPendingOrder(25000);

        Http::fake([
            'https://api.xendit.co/v2/invoices' => Http::response([
                'id' => 'inv-test-1',
                'invoice_url' => 'https://checkout.xendit.co/inv-test-1',
            ], 200),
        ]);

        $result = XenditService::createTransaction($order->fresh()->load('items.product'));

        $this->assertTrue($result['success']);
        $this->assertSame('inv-test-1', $result['token']);

        $order->refresh();
        $this->assertSame('xendit', $order->payment_method);
        $this->assertSame('inv-test-1', $order->payment_token);
        $this->assertSame('https://checkout.xendit.co/inv-test-1', $order->payment_url);
    }

    public function test_midtrans_notification_is_idempotent_for_settlement(): void
    {
        $this->setSetting('payment_gateway_provider', 'midtrans');
        $this->setSetting('midtrans_server_key', 'mid-server-test');

        /** @var User $user */
        $user = User::factory()->create();
        $order = $this->createPendingOrder(15000, $user->id);

        $payload = [
            'order_id' => $order->order_number,
            'status_code' => '200',
            'gross_amount' => '15000.00',
            'transaction_status' => 'settlement',
            'fraud_status' => 'accept',
            'transaction_id' => 'trx-mid-1',
        ];
        $payload['signature_key'] = $this->midtransSignature($payload, 'mid-server-test');

        $first = $this->postJson(route('payment.notification'), $payload);
        $second = $this->postJson(route('payment.notification'), $payload);

        $first->assertOk()->assertJson(['status' => 'ok']);
        $second->assertOk()->assertJson(['status' => 'ok']);

        $order->refresh();
        $this->assertSame('processing', $order->status);
        $this->assertNotNull($order->paid_at);
        $this->assertSame('trx-mid-1', $order->payment_gateway_id);
        $this->assertDatabaseCount('notifications', 1);
        $this->assertDatabaseHas('payment_webhook_events', [
            'provider' => 'midtrans',
            'event_id' => 'trx-mid-1',
            'status' => 'processed',
            'attempts' => 2,
            'response_code' => 200,
        ]);
    }

    public function test_midtrans_notification_rejects_amount_mismatch(): void
    {
        $this->setSetting('payment_gateway_provider', 'midtrans');
        $this->setSetting('midtrans_server_key', 'mid-server-test');

        $order = $this->createPendingOrder(15000);

        $payload = [
            'order_id' => $order->order_number,
            'status_code' => '200',
            'gross_amount' => '10000.00',
            'transaction_status' => 'settlement',
            'fraud_status' => 'accept',
            'transaction_id' => 'trx-mid-2',
        ];
        $payload['signature_key'] = $this->midtransSignature($payload, 'mid-server-test');

        $response = $this->postJson(route('payment.notification'), $payload);

        $response->assertStatus(400)
            ->assertJson(['status' => 'error']);

        $order->refresh();
        $this->assertSame('pending', $order->status);
        $this->assertNull($order->paid_at);
        $this->assertDatabaseHas('payment_webhook_events', [
            'provider' => 'midtrans',
            'event_id' => 'trx-mid-2',
            'status' => 'failed',
            'response_code' => 400,
        ]);
    }

    public function test_xendit_notification_requires_callback_token_and_updates_order(): void
    {
        $this->setSetting('payment_gateway_provider', 'xendit');
        $this->setSetting('xendit_callback_token', 'secure-token');

        /** @var User $user */
        $user = User::factory()->create();
        $order = $this->createPendingOrder(20000, $user->id);

        $payload = [
            'id' => 'inv-xnd-1',
            'external_id' => $order->order_number,
            'status' => 'PAID',
            'amount' => 20000,
        ];

        $this->postJson(route('payment.notification'), $payload)
            ->assertStatus(400)
            ->assertJson(['status' => 'error']);

        $this->postJson(route('payment.notification'), $payload, ['x-callback-token' => 'secure-token'])
            ->assertOk()
            ->assertJson(['status' => 'ok']);

        $order->refresh();
        $this->assertSame('processing', $order->status);
        $this->assertNotNull($order->paid_at);
        $this->assertSame('inv-xnd-1', $order->payment_gateway_id);
        $this->assertDatabaseCount('notifications', 1);
        $this->assertDatabaseHas('payment_webhook_events', [
            'provider' => 'xendit',
            'event_id' => 'inv-xnd-1',
            'status' => 'processed',
            'attempts' => 2,
            'response_code' => 200,
        ]);
    }

    private function createPendingOrder(int $total, ?int $userId = null): Order
    {
        $product = $this->createProduct(10, $total);

        $order = Order::create([
            'user_id' => $userId,
            'order_number' => Order::generateOrderNumber(),
            'name' => 'Test Buyer',
            'phone' => '6281234567890',
            'email' => 'buyer@example.com',
            'total' => $total,
            'status' => 'pending',
            'payment_method' => 'manual',
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'product_name' => $product->name,
            'price' => $product->price,
            'quantity' => 1,
            'subtotal' => $total,
        ]);

        return $order;
    }

    private function createProduct(int $stock, int $price): Product
    {
        $category = Category::create([
            'name' => 'Voucher Game',
            'slug' => 'voucher-game-' . fake()->unique()->numberBetween(1000, 9999),
            'is_active' => true,
        ]);

        return Product::create([
            'category_id' => $category->id,
            'name' => 'Produk Digital ' . fake()->unique()->numberBetween(1000, 9999),
            'slug' => 'produk-digital-' . fake()->unique()->numberBetween(1000, 9999),
            'price' => $price,
            'stock' => $stock,
            'is_active' => true,
        ]);
    }

    private function midtransSignature(array $payload, string $serverKey): string
    {
        return hash('sha512', $payload['order_id'] . $payload['status_code'] . $payload['gross_amount'] . $serverKey);
    }

    private function setSetting(string $key, string $value, string $group = 'payment', string $type = 'text'): void
    {
        Setting::updateOrCreate(
            ['key' => $key],
            [
                'group' => $group,
                'type' => $type,
                'label' => $key,
                'value' => $value,
            ]
        );
    }
}
