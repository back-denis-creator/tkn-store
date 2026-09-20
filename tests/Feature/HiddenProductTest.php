<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Sku;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * An admin can take a product off the storefront without deleting it —
 * deleting one would take the history of every order that holds it.
 */
class HiddenProductTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['role' => User::ADMIN_ID]);
    }

    private function product(string $name, string $slug, bool $hidden): Product
    {
        $product = Product::create(['name' => $name, 'slug' => $slug, 'is_hidden' => $hidden]);
        Sku::create(['product_id' => $product->id, 'price' => 100000, 'code' => $slug.'-1']);

        return $product;
    }

    public function test_a_hidden_product_link_leads_to_the_catalog(): void
    {
        $this->product('Прихована серветка', 'prykhovana', true);

        $this->get(route('product', 'prykhovana'))->assertRedirect(route('catalog'));
    }

    public function test_a_visible_product_still_opens(): void
    {
        $this->product('Серветка', 'servetka', false);

        $this->get(route('product', 'servetka'))->assertOk();
    }

    public function test_the_catalog_leaves_out_a_hidden_product(): void
    {
        $this->product('Серветка', 'servetka', false);
        $this->product('Прихована серветка', 'prykhovana', true);

        $slugs = collect($this->get(route('catalog'))->viewData('page')['props']['products']['data'])
            ->pluck('slug');

        $this->assertContains('servetka', $slugs);
        $this->assertNotContains('prykhovana', $slugs);
    }

    public function test_the_homepage_leaves_out_a_hidden_product(): void
    {
        $this->product('Серветка', 'servetka', false);
        $this->product('Прихована серветка', 'prykhovana', true);

        $slugs = collect($this->get('/')->viewData('page')['props']['productSlider'])->pluck('slug');

        $this->assertContains('servetka', $slugs);
        $this->assertNotContains('prykhovana', $slugs);
    }

    public function test_the_sitemap_leaves_out_a_hidden_product(): void
    {
        $this->product('Серветка', 'servetka', false);
        $this->product('Прихована серветка', 'prykhovana', true);

        $sitemap = $this->get('/sitemap.xml')->assertOk()->getContent();

        $this->assertStringContainsString(route('product', 'servetka'), $sitemap);
        $this->assertStringNotContainsString(route('product', 'prykhovana'), $sitemap);
    }

    public function test_the_admin_can_hide_and_show_a_product_again(): void
    {
        $product = $this->product('Серветка', 'servetka', false);
        $admin = $this->admin();

        $this->actingAs($admin)->post(route('products.update', $product), [
            'name' => 'Серветка',
            'slug' => 'servetka',
            'is_hidden' => true,
            'category_ids' => [],
            'variations' => [],
        ])->assertSessionHasNoErrors();

        $this->assertTrue($product->fresh()->is_hidden);

        $this->actingAs($admin)->post(route('products.update', $product), [
            'name' => 'Серветка',
            'slug' => 'servetka',
            'is_hidden' => false,
            'category_ids' => [],
            'variations' => [],
        ]);

        $this->assertFalse($product->fresh()->is_hidden);
    }

    public function test_the_admin_list_still_holds_a_hidden_product(): void
    {
        $this->product('Прихована серветка', 'prykhovana', true);

        $products = $this->actingAs($this->admin())
            ->get(route('products.index'))
            ->viewData('page')['props']['products']['data'];

        $this->assertCount(1, $products);
        $this->assertTrue($products[0]['is_hidden']);
    }

    public function test_a_product_is_visible_unless_it_is_hidden(): void
    {
        $this->actingAs($this->admin())->post('/products', [
            'name' => 'Нова серветка',
            'category_ids' => [],
            'variations' => [],
        ])->assertSessionHasNoErrors();

        $this->assertFalse(Product::sole()->is_hidden);
    }
}
