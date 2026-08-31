<?php


namespace App\Http\Controllers;

use App\Services\CartService;
use Illuminate\Http\Request;

class CartController extends Controller
{
    /**
     * Lean, hydrated cart data for the NavBar's hover preview — fetched on
     * demand instead of shared globally, since most pages have no other
     * reason to touch the cart tables on every request.
     */
    public function preview()
    {
        return response()->json(
            CartService::hydrate()->map(fn ($product) => [
                'name' => $product->name,
                'slug' => $product->slug,
                'quantity' => $product->quantity,
                'price' => $product->skus[0]->price,
                'sku_id' => $product->skus[0]->id,
                'image' => $product->skus[0]->media[0]?->original_url,
                'attributes' => $product->skus[0]->attributeOptions->map(fn ($option) => [
                    'name' => $option->attribute->name,
                    'value' => $option->value,
                ]),
            ])->values()
        );
    }

    public function addToCart(Request $request)
    {
        $addedQuantity = (int) ($request->input('quantity') ?: 1);
        $productData = [
            'product_id' => $request->input('product_id'),
            'sku_id' => $request->input('sku_id'),
            'quantity' => $addedQuantity
        ];

        // Получаем текущую корзину из сессии
        $cart = session()->get('cart', []);

        // Если товар уже в корзині — просто збільшуємо кількість, а не ігноруємо.
        // Cast to int: sku_id round-trips through the session/request as either
        // an int or a numeric string depending on how it was serialized on the
        // way in, and strict comparison silently treats those as "different".
        $existProductIndex = null;
        foreach ($cart as $index => $item) {
            if((int) $productData['sku_id'] === (int) $item['sku_id']) {
                $existProductIndex = $index;
                break;
            }
        }

        if($existProductIndex !== null) {
            $cart[$existProductIndex]['quantity'] = (int) $cart[$existProductIndex]['quantity'] + $addedQuantity;
        } else {
            $cart[] = $productData;
        }

        session(['cart' => $cart]);

        return back()->with('status', __('Successfully'));
    }

    public function deleteFromCart(Request $request)
    {
        // Получаем текущую корзину из сессии
        $cart = session()->get('cart', []);

        $updated = array_map(function ($item) use($request) {
            if($request->has('skuId') && (int) $request->skuId !== (int) $item['sku_id']) {
                return $item;
            }
        }, $cart);
        
        session(['cart' => array_values(array_filter($updated))]);

        return back()->with('status', __('Successfully'));
    }

    public function updateCart(Request $request)
    {
        // Получаем текущую корзину из сессии
        $cart = session()->get('cart', []);

        $updated = array_map(function ($item) use($request) {
            if($request->has('skuId') && (int) $request->skuId === (int) $item['sku_id']) {
                $item['quantity'] = $request->quantity;
            }
            return $item;
        }, $cart);

        session(['cart' => array_values(array_filter($updated))]);

        return back();
    }

    // public function checkout(Request $request)
    // {
    //     // Получаем корзину из сессии
    //     $cart = session()->get('cart', []);

    //     if (empty($cart)) {
    //         return response()->json(['error' => 'Корзина пуста'], 400);
    //     }

    //     // Создаем заказ
    //     $order = \App\Models\Order::create([
    //         'user_id' => auth()->id(),
    //         'total_amount' => array_sum(array_column($cart, 'price')), // Вычисляем общую сумму
    //         'status' => 'pending'
    //     ]);

    //     // Добавляем товары в order_items
    //     foreach ($cart as $item) {
    //         $order->orderItems()->create([
    //             'product_id' => $item['product_id'],
    //             'product_variation_id' => $item['product_variation_id'],
    //             'quantity' => $item['quantity'],
    //             'price' => $item['price'],
    //             'total_price' => $item['price'] * $item['quantity']
    //         ]);
    //     }

    //     // Очищаем корзину в сессии
    //     session()->forget('cart');

    //     return response()->json(['message' => 'Заказ успешно создан']);
    // }


}