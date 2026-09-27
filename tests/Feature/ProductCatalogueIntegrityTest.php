<?php

namespace Tests\Feature;

use App\Models\Attribute;
use App\Models\AttributeOption;
use App\Models\Product;
use App\Models\Sku;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Three defects found in the production data on 2026-09-27, each with its own
 * cause in this controller and model:
 *
 *  - two products named the same got the same slug, and the second became
 *    unreachable behind the first;
 *  - a typed-in attribute value always created a new option on create, so the
 *    catalogue grew a second "150*200см" under Розмір;
 *  - a variation's video was treated as its cover image.
 */
class ProductCatalogueIntegrityTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['role' => User::ADMIN_ID]);
    }

    public function test_two_products_with_the_same_name_do_not_share_a_slug(): void
    {
        $admin = $this->admin();

        foreach (['Серветка з рюшем', 'Серветка з рюшем', 'Серветка з рюшем'] as $name) {
            $this->actingAs($admin)
                ->post(route('products.store'), ['name' => $name, 'category_ids' => [], 'variations' => []])
                ->assertSessionHasNoErrors();
        }

        $slugs = Product::pluck('slug');

        $this->assertCount(3, $slugs);
        $this->assertCount(3, $slugs->unique(), 'Each product needs a slug of its own: '.$slugs->join(', '));
    }

    public function test_a_product_keeps_its_own_slug_when_it_is_saved_again(): void
    {
        $product = Product::create(['name' => 'Серветка', 'slug' => 'servetka', 'is_hidden' => false]);

        $this->actingAs($this->admin())
            ->post(route('products.update', $product), [
                'name' => 'Серветка',
                'category_ids' => [],
                'variations' => [],
            ])
            ->assertSessionHasNoErrors();

        // Saving with no change must not push the product onto 'servetka-2'.
        $this->assertSame('servetka', $product->fresh()->slug);
    }

    public function test_creating_a_product_reuses_an_attribute_option_that_already_exists(): void
    {
        $attribute = Attribute::create(['name' => 'Розмір']);
        $existing = AttributeOption::create(['attribute_id' => $attribute->id, 'value' => '150*200см']);

        $this->actingAs($this->admin())
            ->post(route('products.store'), [
                'name' => 'Скатертина',
                'category_ids' => [],
                'variations' => [
                    [
                        'id' => 'new',
                        'code' => 'ST-1',
                        'price' => '100',
                        // A value the admin typed rather than picked — the shape
                        // that used to create a duplicate every time.
                        'attributes' => [['id' => $attribute->id, 'value' => '150*200см', 'unit' => '']],
                    ],
                ],
            ])
            ->assertSessionHasNoErrors();

        $options = AttributeOption::where('attribute_id', $attribute->id)->get();

        $this->assertCount(1, $options);
        $this->assertSame($existing->id, $options->first()->id);
        $this->assertSame($existing->id, Sku::sole()->attributeOptions->first()->id);
    }

    public function test_a_genuinely_new_value_still_becomes_an_option(): void
    {
        $attribute = Attribute::create(['name' => 'Розмір']);

        $this->actingAs($this->admin())
            ->post(route('products.store'), [
                'name' => 'Скатертина',
                'category_ids' => [],
                'variations' => [
                    [
                        'id' => 'new',
                        'code' => 'ST-1',
                        'price' => '100',
                        'attributes' => [['id' => $attribute->id, 'value' => '200*300см', 'unit' => '']],
                    ],
                ],
            ])
            ->assertSessionHasNoErrors();

        $this->assertSame('200*300см', AttributeOption::sole()->value);
    }

    /**
     * Two options of one attribute may legitimately share a value: "Жовтогарячий"
     * N-3 and N-9 are different cloths sitting in different sub_meta subgroups.
     * The option the admin picks in the dropdown arrives as {id, value}, and only
     * that id tells them apart.
     */
    public function test_the_option_picked_in_the_dropdown_wins_over_one_with_the_same_value(): void
    {
        $attribute = Attribute::create(['name' => 'Тканина']);
        $first = AttributeOption::create(['attribute_id' => $attribute->id, 'value' => 'Жовтогарячий', 'article' => 'N-3']);
        $second = AttributeOption::create(['attribute_id' => $attribute->id, 'value' => 'Жовтогарячий', 'article' => 'N-9']);

        $this->actingAs($this->admin())
            ->post(route('products.store'), [
                'name' => 'Скатертина',
                'category_ids' => [],
                'variations' => [
                    [
                        'id' => 'new',
                        'code' => 'ST-1',
                        'price' => '100',
                        'attributes' => [[
                            'id' => $attribute->id,
                            'value' => ['id' => $second->id, 'value' => 'Жовтогарячий'],
                            'unit' => '',
                        ]],
                    ],
                ],
            ])
            ->assertSessionHasNoErrors();

        $attached = Sku::sole()->attributeOptions->first();

        $this->assertSame($second->id, $attached->id, 'The cloth the admin picked must be the one that is saved');
        $this->assertSame('N-9', $attached->article);
        $this->assertSame(2, AttributeOption::count(), 'Neither option may be duplicated by the save');
        $this->assertNotSame($first->id, $attached->id);
    }

    public function test_a_video_among_the_photos_is_not_used_as_the_cover_image(): void
    {
        Storage::fake('public');

        $product = Product::create(['name' => 'Скатертина', 'slug' => 'skatertina', 'is_hidden' => false]);
        $sku = Sku::create(['product_id' => $product->id, 'price' => 160000, 'code' => 'TC-BD-0001']);

        $sku->addMedia(UploadedFile::fake()->create('clip.mov', 100, 'video/quicktime'))
            ->toMediaCollection('variation_images');
        $sku->addMedia(UploadedFile::fake()->image('photo.jpg'))
            ->toMediaCollection('variation_images');

        $image = $product->fresh()->default_image;

        $this->assertNotNull($image, 'A variation with photos must have a cover image');
        $this->assertStringContainsString('photo', $image);
        $this->assertStringNotContainsString('clip.mov', $image);
    }

    public function test_a_variation_with_only_a_video_has_no_cover_image(): void
    {
        Storage::fake('public');

        $product = Product::create(['name' => 'Скатертина', 'slug' => 'skatertina', 'is_hidden' => false]);
        $sku = Sku::create(['product_id' => $product->id, 'price' => 160000, 'code' => 'TC-BD-0001']);

        $sku->addMedia(UploadedFile::fake()->create('clip.mov', 100, 'video/quicktime'))
            ->toMediaCollection('variation_images');

        // Better no image than a .mov in og:image and the JSON-LD.
        $this->assertNull($product->fresh()->default_image);
    }
}
