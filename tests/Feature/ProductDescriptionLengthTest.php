<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * The admin product form uses a rich text (Quill) editor for description,
 * which stores HTML, not plain text — a couple thousand characters of
 * visible text can easily produce several thousand characters of markup.
 * The validation rule capped it at 255, far too small for that.
 */
class ProductDescriptionLengthTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['role' => User::ADMIN_ID]);
    }

    public function test_a_description_over_255_characters_is_accepted(): void
    {
        $description = '<p>'.Str::random(2000).'</p>';

        $response = $this->actingAs($this->admin())->post('/products', [
            'name' => 'Test product',
            'description' => $description,
            'category_ids' => [],
            'variations' => [],
        ]);

        $response->assertSessionDoesntHaveErrors('description');
        $this->assertDatabaseHas('products', ['name' => 'Test product', 'description' => $description]);
    }

    public function test_a_description_over_the_new_limit_is_still_rejected(): void
    {
        $description = Str::random(20001);

        $response = $this->actingAs($this->admin())->post('/products', [
            'name' => 'Test product too long',
            'description' => $description,
            'category_ids' => [],
            'variations' => [],
        ]);

        $response->assertSessionHasErrors('description');
    }
}
