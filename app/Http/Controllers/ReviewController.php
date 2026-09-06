<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function store(Request $request, Product $product)
    {
        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|min:3|max:2000',
        ]);

        if (Review::where('product_id', $product->id)->where('user_id', auth()->id())->exists()) {
            return back()->withErrors(['comment' => 'Ви вже залишили відгук на цей товар.']);
        }

        Review::create([
            'product_id' => $product->id,
            'user_id' => auth()->id(),
            'author_name' => auth()->user()->name,
            'rating' => $validated['rating'],
            'comment' => $validated['comment'],
            'status' => Review::STATUS_PENDING,
        ]);

        return back()->with('message', 'Дякуємо! Ваш відгук буде опубліковано після модерації.');
    }
}
