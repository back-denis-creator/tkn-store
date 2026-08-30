<?php

namespace App\Services;

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

        return $cartItems->map(function ($cartItem) use ($products) {
            $product = $products->firstWhere('id', $cartItem['product_id']);
            if (!$product) {
                return null;
            }
            $productCopy = clone $product;
            $productCopy->setRelation('skus', $product->skus()->where('id', $cartItem['sku_id'])->with([
                'attributeOptions.media',
                'attributeOptions.attribute'
            ])->get());
            $productCopy->quantity = $cartItem['quantity'] ?? 0;
            return $productCopy;
        })->filter()->values();
    }
}
