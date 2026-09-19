<?php

namespace Tests\Feature;

use App\Models\Attribute;
use App\Models\AttributeOption;
use App\Models\Product;
use App\Models\Sku;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * A product with has_fabric_selection=true makes the catalog replace the
 * color attribute in the filter list via reject()->push(). reject() keeps
 * the original collection keys, so removing an item from the middle leaves a
 * gap (e.g. keys 0, 2 instead of 0, 1) — a gapped integer key set serializes
 * to a JSON object, not an array. The frontend calls
 * filters.attributes.find()/forEach(), which only exist on arrays, so this
 * broke the fabric filter and crashed SSR with
 * "props.filters.attributes.find is not a function".
 */
class CatalogFiltersJsonShapeTest extends TestCase
{
    use RefreshDatabase;

    public function test_filters_attributes_is_a_json_array_when_a_fabric_selection_product_is_present(): void
    {
        $colorAttribute = Attribute::create(['name' => 'Колір', 'is_color_attribute' => true]);
        $colorOption = AttributeOption::create(['attribute_id' => $colorAttribute->id, 'value' => 'Червоний']);

        $fabricAttribute = Attribute::create(['name' => 'Тканина', 'is_color_attribute' => false]);
        $fabricOption = AttributeOption::create(['attribute_id' => $fabricAttribute->id, 'value' => 'Бавовна']);

        $regularProduct = Product::create(['name' => 'Regular product', 'slug' => 'regular-product', 'has_fabric_selection' => false]);
        $regularSku = Sku::create(['product_id' => $regularProduct->id, 'code' => 'REG-1', 'price' => 10000]);
        $regularSku->attributeOptions()->attach([$colorOption->id, $fabricOption->id]);

        $fabricSelectionProduct = Product::create(['name' => 'Fabric selection product', 'slug' => 'fabric-selection-product', 'has_fabric_selection' => true]);
        Sku::create(['product_id' => $fabricSelectionProduct->id, 'code' => 'FAB-1', 'price' => 20000]);

        $response = $this->get('/catalog');

        $response->assertOk();

        // The initial page load embeds Inertia's JSON payload HTML-encoded
        // inside a data-page="..." attribute — decode it back to plain JSON
        // so the array-vs-object shape of "attributes" is inspectable.
        $decoded = html_entity_decode($response->getContent());
        $this->assertStringContainsString('"attributes":[', $decoded);
        $this->assertStringNotContainsString('"attributes":{', $decoded);
    }
}
