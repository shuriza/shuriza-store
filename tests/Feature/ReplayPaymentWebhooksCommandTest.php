<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\PaymentWebhookEvent;
use App\Models\Product;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class ReplayPaymentWebhooksCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_replay_command_processes_failed_xendit_event(): void
    {
        $this->setSetting('xendit_callback_token', 'secure-token');

        $user = User::factory()->create();
        $order = $this->createPendingOrder($user->id, 35000);

        $event = PaymentWebhookEvent::create([
            'provider' => 'xendit',
            'event_id' => 'inv-replay-1',
            'order_number' => $order->order_number,
            'endpoint' => 'payment.notification',
            'payload' => [
                'id' => 'inv-replay-1',
                'external_id' => $order->order_number,
                'status' => 'PAID',
                'amount' => 35000,
            ],
            'headers' => [
                'x-callback-token' => ['secure-token'],
            ],
            'payload_hash' => hash('sha256', json_encode(['id' => 'inv-replay-1'])),
            'status' => 'failed',
            'attempts' => 1,
            'response_code' => 400,
            'error_message' => 'initial failed',
        ]);

        $exitCode = Artisan::call('payment:webhooks:replay', [
            '--provider' => 'xendit',
            '--limit' => 10,
        ]);

        $this->assertSame(0, $exitCode);

        $event->refresh();
        $order->refresh();

        $this->assertSame('processed', $event->status);
        $this->assertSame(2, $event->attempts);
        $this->assertSame(200, $event->response_code);
        $this->assertNull($event->error_message);

        $this->assertSame('processing', $order->status);
        $this->assertNotNull($order->paid_at);
        $this->assertSame('inv-replay-1', $order->payment_gateway_id);
    }

    public function test_replay_command_dry_run_does_not_mutate_event(): void
    {
        $event = PaymentWebhookEvent::create([
            'provider' => 'midtrans',
            'event_id' => 'trx-dry-run-1',
            'order_number' => 'SHR-TEST-001',
            'endpoint' => 'payment.notification',
            'payload' => ['order_id' => 'SHR-TEST-001'],
            'headers' => [],
            'payload_hash' => hash('sha256', json_encode(['order_id' => 'SHR-TEST-001'])),
            'status' => 'failed',
            'attempts' => 1,
            'response_code' => 400,
            'error_message' => 'invalid signature',
        ]);

        $exitCode = Artisan::call('payment:webhooks:replay', [
            '--dry-run' => true,
            '--limit' => 10,
        ]);

        $this->assertSame(0, $exitCode);

        $event->refresh();
        $this->assertSame('failed', $event->status);
        $this->assertSame(1, $event->attempts);
        $this->assertSame(400, $event->response_code);
    }

    private function createPendingOrder(int $userId, int $total): Order
    {
        $category = Category::create([
            'name' => 'Topup',
            'slug' => 'topup-' . fake()->unique()->numberBetween(1000, 9999),
            'is_active' => true,
        ]);

        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Produk Replay ' . fake()->unique()->numberBetween(1000, 9999),
            'slug' => 'produk-replay-' . fake()->unique()->numberBetween(1000, 9999),
            'price' => $total,
            'stock' => 5,
            'is_active' => true,
        ]);

        $order = Order::create([
            'user_id' => $userId,
            'order_number' => Order::generateOrderNumber(),
            'name' => 'Replay User',
            'phone' => '6281234567890',
            'email' => 'replay@example.com',
            'total' => $total,
            'status' => 'pending',
            'payment_method' => 'xendit',
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

    private function setSetting(string $key, string $value): void
    {
        Setting::updateOrCreate(
            ['key' => $key],
            [
                'group' => 'payment',
                'type' => 'text',
                'label' => $key,
                'value' => $value,
            ]
        );
    }
}
