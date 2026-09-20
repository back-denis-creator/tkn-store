<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * The admin's Quill editor exports its HTML with every space written as a
 * non-breaking one, which leaves a paragraph with nowhere for the browser to
 * break it — on a phone the text then runs off the screen and widens the page.
 */
class ProductDescriptionWrappingTest extends TestCase
{
    use RefreshDatabase;

    private const QUILL_EXPORT = '<p>Має&nbsp;водовідштовхувальне&nbsp;просочення&nbsp;—&nbsp;практичне&nbsp;рішення</p>';

    public function test_saving_a_description_keeps_its_spaces_breakable(): void
    {
        $admin = User::factory()->create(['role' => User::ADMIN_ID]);

        $this->actingAs($admin)->post('/products', [
            'name' => 'Серветка',
            'description' => self::QUILL_EXPORT,
            'category_ids' => [],
            'variations' => [],
        ])->assertSessionDoesntHaveErrors('description');

        $this->assertSame(
            '<p>Має водовідштовхувальне просочення — практичне рішення</p>',
            Product::sole()->description
        );
    }

    public function test_a_description_saved_before_the_fix_still_wraps(): void
    {
        // Written straight to the column, the way rows already in the database
        // hold it — the model's own accessor has to clean those up too.
        $product = Product::create(['name' => 'Серветка', 'slug' => 'servetka']);
        Product::whereKey($product->id)->update(['description' => self::QUILL_EXPORT]);

        $this->assertStringNotContainsString('&nbsp;', $product->fresh()->description);
    }

    public function test_the_storefront_gets_a_breakable_description(): void
    {
        $product = Product::create(['name' => 'Серветка', 'slug' => 'servetka']);
        Product::whereKey($product->id)->update(['description' => self::QUILL_EXPORT]);

        $description = $this->get(route('product', 'servetka'))
            ->viewData('page')['props']['product']['description'];

        $this->assertStringNotContainsString('&nbsp;', $description);
        $this->assertStringNotContainsString("\u{00A0}", $description);
    }
}
