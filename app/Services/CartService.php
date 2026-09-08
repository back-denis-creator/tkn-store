<?php

namespace App\Services;

use App\Models\AttributeOption;
use App\Models\Product;
use Illuminate\Support\Collection;

class CartService
{
    /**
     * Hydrate the raw session cart (product_id/sku_id/quantity) into full
     * Product+Sku data (name, price, images, attributes) — shared by the
     * Cart page, Checkout page, and order creation, so price/name are
     * always read fresh from the database, never trusted from the session.
     */
    public static function hydrate(): Collection
    {
        $cartItems = collect(session('cart', []));

        $productIds = $cartItems->pluck('product_id')->unique();

        $products = Product::with('categories')
            ->whereIn('id', $productIds)
            ->get();

        $fabricOptionIds = $cartItems->pluck('fabric_attribute_option_id')->filter()->unique();
        $fabricOptions = $fabricOptionIds->isEmpty()
            ? collect()
            : AttributeOption::with('attribute')->whereIn('id', $fabricOptionIds)->get()->keyBy('id');

        return $cartItems->map(function ($cartItem) use ($products, $fabricOptions) {
            $product = $products->firstWhere('id', $cartItem['product_id']);
            if (!$product) {
                return null;
            }
            $productCopy = clone $product;
            $skus = $product->skus()->where('id', $cartItem['sku_id'])->with([
                'attributeOptions.media',
                'attributeOptions.attribute'
            ])->get();
            if ($product->has_fabric_selection) {
                // Legacy Sku-linked color values (from before this product
                // switched to the global fabric catalog) are irrelevant now —
                // rendering them here would duplicate/contradict
                // selected_fabric below. Mirrors the same skip in
                // Product.vue's `attributes` computed.
                $skus->each(fn ($sku) => $sku->setRelation(
                    'attributeOptions',
                    $sku->attributeOptions->reject(fn ($option) => $option->attribute->is_color_attribute)->values()
                ));
            }
            $productCopy->setRelation('skus', $skus);
            $productCopy->quantity = $cartItem['quantity'] ?? 0;
            // Fabric is decoupled from the Sku (has_fabric_selection) — carry
            // the buyer's chosen fabric alongside the resolved Sku so the
            // Cart page can render it even though it's not one of the Sku's
            // own attributeOptions.
            $productCopy->selected_fabric = ! empty($cartItem['fabric_attribute_option_id'])
                ? $fabricOptions->get($cartItem['fabric_attribute_option_id'])
                : null;
            return $productCopy;
        })->filter()->values();
    }
}
