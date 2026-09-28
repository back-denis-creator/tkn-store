<?php

namespace Tests\Feature;

use App\Models\Attribute;
use App\Models\AttributeOption;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * The attribute editor posts one variable for every leaf of every option. The
 * "Тканина" attribute (83 options) sent 1006 of them, and the host accepts
 * 1000. PHP dropped the rest without a word, so the last options kept their old
 * values and a deletion was lost.
 *
 * The form now sends the group id instead of the whole group object, and it
 * appends payload_complete last so the server can see a cut-short request.
 */
class AttributeOptionsPayloadTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['role' => User::ADMIN_ID]);
    }

    private function colorAttribute(): Attribute
    {
        return Attribute::create(['name' => 'Тканина', 'is_color_attribute' => true]);
    }

    public function test_it_saves_the_group_when_the_form_sends_its_id(): void
    {
        $attribute = $this->colorAttribute();
        $option = AttributeOption::create(['attribute_id' => $attribute->id, 'value' => 'Жовтогарячий']);

        $this->actingAs($this->admin())
            ->post(route('attributes.update', $attribute), [
                'name' => 'Тканина',
                'is_color_attribute' => true,
                'payload_complete' => 1,
                'options' => [
                    ['id' => $option->id, 'value' => 'Жовтогарячий', 'meta' => 3, 'sub_meta' => 1],
                ],
            ])
            ->assertSessionHasNoErrors();

        $option->refresh();

        $this->assertSame(3, (int) $option->meta);
        $this->assertSame(1, (int) $option->sub_meta);
    }

    /**
     * A tab opened before the change still posts the whole group object. That
     * request must keep working.
     */
    public function test_it_still_accepts_the_old_group_object(): void
    {
        $attribute = $this->colorAttribute();
        $option = AttributeOption::create(['attribute_id' => $attribute->id, 'value' => 'Сірий']);

        $this->actingAs($this->admin())
            ->post(route('attributes.update', $attribute), [
                'name' => 'Тканина',
                'is_color_attribute' => true,
                'payload_complete' => 1,
                'options' => [
                    [
                        'id' => $option->id,
                        'value' => 'Сірий',
                        'meta' => ['id' => 2, 'name' => 'Геометричні'],
                        'sub_meta' => ['id' => 4, 'name' => 'Зигзаг'],
                    ],
                ],
            ])
            ->assertSessionHasNoErrors();

        $option->refresh();

        $this->assertSame(2, (int) $option->meta);
        $this->assertSame(4, (int) $option->sub_meta);
    }

    /**
     * The "Однотонні" group has id 0. A falsy test would read that as "not set"
     * and leave the previous group in place.
     */
    public function test_the_group_with_id_zero_is_saved(): void
    {
        $attribute = $this->colorAttribute();
        $option = AttributeOption::create([
            'attribute_id' => $attribute->id,
            'value' => 'Білий',
            'meta' => 3,
        ]);

        $this->actingAs($this->admin())
            ->post(route('attributes.update', $attribute), [
                'name' => 'Тканина',
                'is_color_attribute' => true,
                'payload_complete' => 1,
                'options' => [
                    ['id' => $option->id, 'value' => 'Білий', 'meta' => 0, 'sub_meta' => null],
                ],
            ])
            ->assertSessionHasNoErrors();

        $this->assertSame(0, (int) $option->refresh()->meta);
    }

    public function test_a_cleared_subcategory_is_removed(): void
    {
        $attribute = $this->colorAttribute();
        $option = AttributeOption::create([
            'attribute_id' => $attribute->id,
            'value' => 'Карамель',
            'meta' => 2,
            'sub_meta' => 3,
        ]);

        $this->actingAs($this->admin())
            ->post(route('attributes.update', $attribute), [
                'name' => 'Тканина',
                'is_color_attribute' => true,
                'payload_complete' => 1,
                'options' => [
                    ['id' => $option->id, 'value' => 'Карамель', 'meta' => 2, 'sub_meta' => null],
                ],
            ])
            ->assertSessionHasNoErrors();

        $this->assertNull($option->refresh()->sub_meta);
    }

    public function test_a_cut_short_request_is_refused_instead_of_saved_in_part(): void
    {
        $attribute = $this->colorAttribute();
        $option = AttributeOption::create(['attribute_id' => $attribute->id, 'value' => 'Старе значення']);

        // No payload_complete: this is what the server receives after PHP drops
        // everything past max_input_vars.
        $this->actingAs($this->admin())
            ->post(route('attributes.update', $attribute), [
                'name' => 'Тканина, перейменована',
                'is_color_attribute' => true,
                'options' => [
                    ['id' => $option->id, 'value' => 'Нове значення'],
                ],
            ])
            ->assertSessionHasErrors('options');

        // Nothing at all was written — not the attribute, not the option.
        $this->assertSame('Тканина', $attribute->refresh()->name);
        $this->assertSame('Старе значення', $option->refresh()->value);
    }
}
