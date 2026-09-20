<?php

namespace Tests\Feature;

use App\Models\Attribute;
use App\Models\AttributeOption;
use App\Models\DefaultColor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * The fabrics page lists every fabric of the color attribute. It shows them
 * in the shop's own default color sequence rather than in insert order, so
 * the same colors follow each other in every group on the page.
 */
class FabricsPageOrderTest extends TestCase
{
    use RefreshDatabase;

    private function fabric(string $value, ?DefaultColor $color = null): AttributeOption
    {
        $attribute = Attribute::firstOrCreate(
            ['name' => 'Тканина'],
            ['is_color_attribute' => true]
        );

        $option = AttributeOption::create([
            'attribute_id' => $attribute->id,
            'value' => $value,
        ]);

        if ($color) {
            $option->defaultColors()->attach($color);
        }

        return $option;
    }

    private function fabricValuesOnPage(): array
    {
        return array_column(
            $this->get(route('fabrics'))->viewData('page')['props']['fabricOptions'],
            'value'
        );
    }

    public function test_fabrics_follow_the_default_color_order(): void
    {
        $white = DefaultColor::create(['name' => 'Білий', 'hex' => '#ffffff', 'sort_order' => 1]);
        $red = DefaultColor::create(['name' => 'Червоний', 'hex' => '#ff0000', 'sort_order' => 2]);
        $blue = DefaultColor::create(['name' => 'Синій', 'hex' => '#0000ff', 'sort_order' => 3]);

        // Created out of order on purpose — insert order must not decide this.
        $this->fabric('Синя', $blue);
        $this->fabric('Біла', $white);
        $this->fabric('Червона', $red);

        $this->assertSame(['Біла', 'Червона', 'Синя'], $this->fabricValuesOnPage());
    }

    public function test_a_fabric_on_several_colors_follows_its_first_color(): void
    {
        $first = DefaultColor::create(['name' => 'Перший', 'hex' => '#111111', 'sort_order' => 1]);
        $last = DefaultColor::create(['name' => 'Останній', 'hex' => '#222222', 'sort_order' => 9]);

        $this->fabric('Однотонна', $last);
        $this->fabric('Багатокольорова', $first)->defaultColors()->attach($last);

        $this->assertSame(['Багатокольорова', 'Однотонна'], $this->fabricValuesOnPage());
    }

    public function test_a_fabric_with_no_color_goes_last(): void
    {
        $color = DefaultColor::create(['name' => 'Синій', 'hex' => '#0000ff', 'sort_order' => 5]);

        $this->fabric('Без кольору');
        $this->fabric('Синя', $color);

        $this->assertSame(['Синя', 'Без кольору'], $this->fabricValuesOnPage());
    }
}
