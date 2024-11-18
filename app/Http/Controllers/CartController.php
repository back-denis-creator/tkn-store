<?php


namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CartController extends Controller
{

    public function addToCart(Request $request)
    {
        $productData = [
            'product_id' => $request->input('product_id'),
            'sku_id' => $request->input('sku_id'),
            'quantity' => $request->input('quantity')
        ];

        // Получаем текущую корзину из сессии
        $cart = session()->get('cart', []);
        
        // Проверяем наличие товара в корзине
        $existProduct = false;
        foreach ($cart as $item) {
            if($productData['sku_id'] === $item['sku_id']) {
                $existProduct = true;
                break;
            }
        }

        // Добавляем товар в корзину, если его нету
        if(!$existProduct) $cart[] = $productData;

        session(['cart' => $cart]);

        return back()->with('status', __('Successfully'));
    }

    public function deleteFromCart(Request $request)
    {
        // Получаем текущую корзину из сессии
        $cart = session()->get('cart', []);

        $updated = array_map(function ($item) use($request) { 
            if($request->has('skuId') && $request->skuId !== $item['sku_id']) {
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
            if($request->has('skuId') && $request->skuId === $item['sku_id']) {
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