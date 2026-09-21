<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Sku;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * The cart preview behind the bag icon in the header. It is fetched with
 * plain axios, so a failure there shows as an empty white panel with no
 * error anywhere the buyer can see.
 */
class CartPreviewTest extends TestCase
{
    use RefreshDatabase;

    private function cartWith(Product $product, Sku $sku): void
    {
        $this->withSession(['cart' => [
            ['product_id' => $product->id, 'sku_id' => $sku->id, 'quantity' => 1],
        ]]);
    }

    public function test_the_preview_survives_a_product_with_no_photo(): void
    {
        $product = Product::create(['name' => 'Серветка', 'slug' => 'servetka']);
        $sku = Sku::create(['product_id' => $product->id, 'price' => 65000, 'code' => 'S-1']);

        $this->cartWith($product, $sku);

        $response = $this->getJson(route('cart.preview'))->assertOk();

        $this->assertCount(1, $response->json());
        $this->assertNull($response->json('0.image'));
        $this->assertSame('Серветка', $response->json('0.name'));
    }

    public function test_an_empty_cart_previews_as_an_empty_list(): void
    {
        $this->getJson(route('cart.preview'))->assertOk()->assertExactJson([]);
    }
}
