<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Jobs\SendTelegramNotification;
use App\Mail\OrderPlaced;
use App\Models\Delivery;
use App\Models\Order;
use App\Models\Product;
use App\Models\Sku;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

/**
 * Placing an order must queue both the customer confirmation email and a
 * Telegram notification for the team — neither should block the checkout
 * redirect on an external service being slow/down.
 *
 * CSRF is disabled per-test rather than relying on the framework's automatic
 * test-environment bypass: APP_ENV resolves to "local" (not "testing") even
 * under `php artisan test` in this project (no .env.testing, and .env's own
 * APP_ENV=local wins), so Illuminate\Foundation\Http\Middleware\ValidateCsrfToken's
 * runningUnitTests() check never kicks in and every real POST 419s. This is a
 * pre-existing environment issue (affects PasswordUpdateTest, RegistrationTest,
 * ProfileTest too), not something introduced here — worth a real fix later.
 */
class OrderTelegramNotificationTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(ValidateCsrfToken::class);
    }

    public function test_placing_an_order_queues_a_telegram_notification_with_order_details(): void
    {
        Queue::fake();
        Mail::fake();

        $product = Product::create(['name' => 'Test Product', 'slug' => 'test-product-'.uniqid()]);
        $sku = Sku::create(['product_id' => $product->id, 'code' => 'SKU1', 'price' => 15]);

        session(['cart' => [
            ['product_id' => $product->id, 'sku_id' => $sku->id, 'quantity' => 2],
        ]]);

        $response = $this->post('/order', [
            'name' => 'Denis',
            'phone' => '+380501234567',
            'email' => 'denis@example.com',
            'delivery_method' => Delivery::SAMOVUVOZ,
            'payment_method' => Order::PAYMENT_CASH,
        ]);

        $response->assertRedirect();

        $order = Order::firstWhere('customer_phone', '+380501234567');
        $this->assertNotNull($order, 'expected an order to have been created');

        Queue::assertPushed(SendTelegramNotification::class, function ($job) use ($order) {
            return str_contains($job->message, "Нове замовлення №{$order->id}")
                && str_contains($job->message, 'Test Product')
                && str_contains($job->message, 'Denis')
                && str_contains($job->message, '+380501234567');
        });

        Mail::assertQueued(OrderPlaced::class);
    }

    public function test_placing_an_order_without_email_still_queues_the_telegram_notification(): void
    {
        Queue::fake();
        Mail::fake();

        $product = Product::create(['name' => 'No Email Product', 'slug' => 'no-email-product-'.uniqid()]);
        $sku = Sku::create(['product_id' => $product->id, 'code' => 'SKU2', 'price' => 10]);

        session(['cart' => [
            ['product_id' => $product->id, 'sku_id' => $sku->id, 'quantity' => 1],
        ]]);

        $this->post('/order', [
            'name' => 'Olena',
            'phone' => '+380507654321',
            'delivery_method' => Delivery::SAMOVUVOZ,
            'payment_method' => Order::PAYMENT_CASH,
        ])->assertRedirect();

        Queue::assertPushed(SendTelegramNotification::class);
        Mail::assertNothingQueued();
    }
}
