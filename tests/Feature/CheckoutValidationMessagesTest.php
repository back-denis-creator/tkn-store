<?php

namespace Tests\Feature;

use App\Models\Delivery;
use App\Models\Product;
use App\Models\Sku;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Checkout is the one form a buyer cannot walk away from, so its errors have
 * to say what to do next — not name the column that failed.
 */
class CheckoutValidationMessagesTest extends TestCase
{
    use RefreshDatabase;

    private function withCart(): void
    {
        $product = Product::create(['name' => 'Серветка', 'slug' => 'servetka']);
        $sku = Sku::create(['product_id' => $product->id, 'price' => 65000, 'code' => 'S-1']);

        $this->withSession(['cart' => [
            ['product_id' => $product->id, 'sku_id' => $sku->id, 'quantity' => 1],
        ]]);
    }

    private function order(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Оксана',
            'phone' => '+38 (050) 000-0000',
            'delivery_method' => Delivery::NOVA_POSHTA,
            'np_city_ref' => 'city-ref',
            'np_warehouse_ref' => 'warehouse-ref',
            'payment_method' => 1,
        ], $overrides);
    }

    public function test_a_missing_nova_poshta_city_is_explained_in_plain_words(): void
    {
        $this->withCart();

        $this->post(route('order.store'), $this->order(['np_city_ref' => null, 'np_warehouse_ref' => null]))
            ->assertSessionHasErrors([
                'np_city_ref' => 'Оберіть місто зі списку Нової Пошти.',
                'np_warehouse_ref' => 'Оберіть відділення Нової Пошти.',
            ]);
    }

    public function test_the_message_never_names_a_database_column(): void
    {
        $this->withCart();

        $this->post(route('order.store'), $this->order(['np_city_ref' => null]));

        $message = session('errors')->first('np_city_ref');

        $this->assertStringNotContainsString('np city ref', $message);
        $this->assertStringNotContainsString('delivery method', $message);
    }

    public function test_the_contact_step_asks_for_what_it_needs(): void
    {
        $this->withCart();

        $this->post(route('order.store'), $this->order(['name' => '', 'phone' => '', 'email' => 'not-an-email']))
            ->assertSessionHasErrors([
                'name' => "Вкажіть, будь ласка, ваше ім'я.",
                'phone' => 'Вкажіть номер телефону — ми зателефонуємо, щоб підтвердити замовлення.',
                'email' => 'Перевірте пошту: здається, в адресі помилка.',
            ]);
    }

    public function test_a_missing_payment_method_is_explained(): void
    {
        $this->withCart();

        $this->post(route('order.store'), $this->order(['payment_method' => null]))
            ->assertSessionHasErrors(['payment_method' => 'Оберіть спосіб оплати.']);
    }

    public function test_an_unknown_delivery_method_is_explained(): void
    {
        $this->withCart();

        $this->post(route('order.store'), $this->order(['delivery_method' => 99]))
            ->assertSessionHasErrors(['delivery_method' => 'Оберіть спосіб доставки зі списку.']);
    }

    public function test_every_field_that_can_fail_is_shown_on_the_page(): void
    {
        // The checkout page prints each error next to its own step; a field
        // left out of the template fails silently and the button looks broken.
        $checkout = file_get_contents(resource_path('js/Pages/Checkout.vue'));

        foreach (['name', 'surname', 'phone', 'email', 'delivery_method', 'np_city_ref', 'np_warehouse_ref', 'payment_method', 'cart'] as $field) {
            $this->assertStringContainsString("form.errors.{$field}", $checkout, "No message is shown for {$field}.");
        }
    }
}
