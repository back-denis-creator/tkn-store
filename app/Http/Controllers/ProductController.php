<?php

namespace App\Http\Controllers;

use App\Models\Attribute;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Product::with('categories')->paginate(10);

        return Inertia::render(
            'Products/Index',
            [
                'products' => $products
            ]
        );
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render(
            'Products/Create',
            [
                'categories' => Category::all(),
                'attributes' => Attribute::all()
            ]
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //TODO
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required',
            'description' => 'nullable|string|max:255',
            'category_ids' => 'array'
        ]);

        $product = Product::create([
            'name' => $request->name,
            'price' => $request->price,
            'description' => $request->description
        ]);

        if(!empty($request->category_ids)) {
            $product->categories()->sync($request->category_ids);
        }

        return redirect()->route('products.index')->with('message', 'Product Created Successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        return Inertia::render(
            'Products/Edit',
            [
                'product' => $product->with('categories')->where('id', $product->id)->first(),
                'categories' => Category::all()
            ]
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required',
            'description' => 'nullable|string|max:255',
            'category_ids' => 'array'
        ]);

        $product->name = $request->name;
        $product->price = $request->price;
        $product->description = $request->description;
        $product->save();

        if(!empty($request->category_ids)) {
            $product->categories()->sync($request->category_ids);
        }

        return redirect()->route('products.index')->with('message', 'Product Updated Successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        $product->delete();

        return redirect()->route('products.index')->with('message', 'Product Delete Successfully');
    }
}
