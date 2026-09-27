<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Sku;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * The admin product form posts every variation back as one multipart body, and
 * two properties of that encoding kept biting the admin on a phone:
 *
 *  - an empty array is dropped entirely, it is never sent as [];
 *  - PHP truncates the whole body from the end once it goes over
 *    max_input_vars, silently.
 *
 * 'attributes' is the last key serialized inside a variation, so truncation
 * took it out first and ProductController::update() died on a null key — six
 * 500s in the production log on 2026-09-23 alone. These cover both, plus the
 * Sku-mismatch bug that sat next to them.
 */
class ProductVariationUpdateTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['role' => User::ADMIN_ID]);
    }

    private function product(string $slug = 'servetka'): Product
    {
        return Product::create(['name' => 'Серветка', 'slug' => $slug, 'is_hidden' => false]);
    }

    private function sku(Product $product, string $code, int $price = 100000): Sku
    {
        return Sku::create(['product_id' => $product->id, 'price' => $price, 'code' => $code]);
    }

    public function test_a_variation_that_arrives_without_its_attributes_is_a_readable_error_not_a_500(): void
    {
        $product = $this->product();
        $sku = $this->sku($product, 'A-1');

        $this->actingAs($this->admin())
            ->post(route('products.update', $product), [
                'name' => 'Серветка',
                'category_ids' => [],
                // No 'attributes' key — what PHP leaves behind after truncating
                // the POST at max_input_vars.
                'variations' => [
                    ['id' => $sku->id, 'code' => 'A-2', 'price' => '200'],
                ],
            ])
            ->assertSessionHasErrors('variations');

        // Nothing was half-written before the request was rejected.
        $this->assertSame('A-1', $sku->fresh()->code);
    }

    public function test_creating_a_product_with_a_truncated_variation_is_also_rejected(): void
    {
        $this->actingAs($this->admin())
            ->post(route('products.store'), [
                'name' => 'Нова серветка',
                'category_ids' => [],
                'variations' => [
                    ['id' => 'new', 'code' => 'B-1', 'price' => '300'],
                ],
            ])
            ->assertSessionHasErrors('variations');

        $this->assertSame(0, Product::count());
    }

    public function test_a_sku_missing_from_the_payload_keeps_its_own_data(): void
    {
        $product = $this->product();
        $untouched = $this->sku($product, 'KEEP-1', 100000);
        $posted = $this->sku($product, 'POST-1', 200000);

        // Only the second Sku is sent back. array_search() returns false for the
        // first one, and $variations[false] is $variations[0] in PHP — which used
        // to copy the posted variation's code and price onto the absent Sku.
        $this->actingAs($this->admin())
            ->post(route('products.update', $product), [
                'name' => 'Серветка',
                'category_ids' => [],
                'variations' => [
                    ['id' => $posted->id, 'code' => 'POST-2', 'price' => '250', 'attributes' => []],
                ],
            ])
            ->assertSessionHasNoErrors();

        $this->assertSame('KEEP-1', $untouched->fresh()->code);
        $this->assertSame(100000, (int) $untouched->fresh()->price);
        $this->assertSame('POST-2', $posted->fresh()->code);
    }

    public function test_removing_every_photo_from_a_variation_actually_deletes_them(): void
    {
        Storage::fake('public');

        $product = $this->product();
        $sku = $this->sku($product, 'A-1');
        $sku->addMedia(UploadedFile::fake()->image('one.jpg'))->toMediaCollection('variation_images');
        $sku->addMedia(UploadedFile::fake()->image('two.jpg'))->toMediaCollection('variation_images');

        $this->assertCount(2, $sku->getMedia('variation_images'));

        // The admin cleared the gallery: 'images' is now an empty array on the
        // client, which multipart drops, so no key reaches the server at all.
        $this->actingAs($this->admin())
            ->post(route('products.update', $product), [
                'name' => 'Серветка',
                'category_ids' => [],
                'variations' => [
                    ['id' => $sku->id, 'code' => 'A-1', 'price' => '100', 'attributes' => []],
                ],
            ])
            ->assertSessionHasNoErrors();

        $this->assertCount(0, $sku->fresh()->getMedia('variation_images'));
    }

    public function test_a_photo_the_admin_kept_survives_the_save(): void
    {
        Storage::fake('public');

        $product = $this->product();
        $sku = $this->sku($product, 'A-1');
        $kept = $sku->addMedia(UploadedFile::fake()->image('keep.jpg'))->toMediaCollection('variation_images');
        $sku->addMedia(UploadedFile::fake()->image('drop.jpg'))->toMediaCollection('variation_images');

        $this->actingAs($this->admin())
            ->post(route('products.update', $product), [
                'name' => 'Серветка',
                'category_ids' => [],
                'variations' => [
                    [
                        'id' => $sku->id,
                        'code' => 'A-1',
                        'price' => '100',
                        // The form sends the ids of the photos to keep, nothing
                        // more — see the transform() in Products/Edit.vue.
                        'images' => [$kept->id],
                        'attributes' => [],
                    ],
                ],
            ])
            ->assertSessionHasNoErrors();

        $remaining = $sku->fresh()->getMedia('variation_images');

        $this->assertCount(1, $remaining);
        $this->assertSame($kept->id, $remaining->first()->id);
    }

    /**
     * The edit form used to post every photo back as its full media record, and
     * a tab left open across the deploy that changed it still will. Reading
     * that shape as "keep nothing" would wipe the gallery on save.
     */
    public function test_the_old_full_media_payload_still_keeps_the_right_photo(): void
    {
        Storage::fake('public');

        $product = $this->product();
        $sku = $this->sku($product, 'A-1');
        $kept = $sku->addMedia(UploadedFile::fake()->image('keep.jpg'))->toMediaCollection('variation_images');
        $sku->addMedia(UploadedFile::fake()->image('drop.jpg'))->toMediaCollection('variation_images');

        $this->actingAs($this->admin())
            ->post(route('products.update', $product), [
                'name' => 'Серветка',
                'category_ids' => [],
                'variations' => [
                    [
                        'id' => $sku->id,
                        'code' => 'A-1',
                        'price' => '100',
                        'images' => [['id' => $kept->id, 'file_name' => 'keep.jpg', 'uuid' => 'x']],
                        'attributes' => [],
                    ],
                ],
            ])
            ->assertSessionHasNoErrors();

        $remaining = $sku->fresh()->getMedia('variation_images');

        $this->assertCount(1, $remaining);
        $this->assertSame($kept->id, $remaining->first()->id);
    }
}
