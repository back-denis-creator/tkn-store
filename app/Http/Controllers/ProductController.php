<?php

namespace App\Http\Controllers;

use App\Models\Attribute;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Cocur\Slugify\Slugify;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Product::with([
            'categories',
            'skus' => fn($skus) => $skus->with('attributeOptions')
        ])->paginate(10);

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
                'attributes' => Attribute::with('attributeOptions')->get()
            ]
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'         => 'required|string|max:255',
            'slug'         => 'nullable|string|max:255',
            'description'  => 'nullable|string|max:255',
            'category_ids' => 'array',
            'variations'   => 'array'
        ]);

        $slugify = new Slugify();

        $product = Product::create([
            'name'        => $request->name,
            'slug'        => $request->slug ?: $slugify->slugify($request->name),
            'description' => $request->description
        ]);

        //CREATE VARIATIONS
        foreach ($request->variations as $variation) {
            $sku = $product->skus()->create([
                'price' =>  $variation['price'],
                'code'  =>  $variation['code'],
            ]);

            //SET VARIATION IMAGES
            foreach ($variation['images'] as $image) {
                $sku->addMedia($image)->toMediaCollection('variation_images');
            }

            //CREATE OR GET OPTIONS AND SYNC
            $optionIds = [];
            foreach ($variation['attributes'] as $attribute) {
                $option = null;
                $model = Attribute::find($attribute['id']);
                if(is_array($attribute['value'])) {
                    $option = $model->attributeOptions()->where(['value' => $attribute['value']['value']])->first();
                } else if(!empty($attribute['value'])) {
                    $option = $model->attributeOptions()->create([
                        'attribute_id' => $attribute['id'],
                        'value'        => $attribute['value'],
                    ]);
                }
                if($option) array_push($optionIds, $option->id);
            }

            $sku->attributeOptions()->sync(array_values($optionIds));
        }

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
                'product' => $product->with([
                        'categories',
                        'skus' => fn($skus) => $skus->with('attributeOptions')
                    ])->where('id', $product->id)->first(),
                'categories' => Category::all(),
                'attributes' => Attribute::with('attributeOptions')->get()
            ]
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        // dd($request);
        $request->validate([
            'name'         => 'required|string|max:255',
            'slug'         => 'nullable|string|max:255',
            'description'  => 'nullable|string|max:255',
            'category_ids' => 'array',
            'delete_variations_ids'   => 'array',
            'variations'   => 'array'
        ]);

        $slugify = new Slugify();

        $product->name = $request->name;
        $product->slug = $request->slug ?: $slugify->slugify($request->name);
        $product->description = $request->description;
        $product->save();

        if(!empty($request->category_ids)) {
            $product->categories()->sync($request->category_ids);
        }

        //UPDATE OR DELETE EXIST VARIATIONS
        $skusIds = array_column($request->variations, 'id');
        $product->skus()->each(function (object $sku) use($request, $skusIds) {
            $index = array_search($sku->id, $skusIds);
            if(isset($request->variations[$index])) {
                $sku->update([
                    'price' =>  $request->variations[$index]['price'],
                    'code'  =>  $request->variations[$index]['code'],
                ]);
                //DELETE VARIATION IMAGES
                if(isset($request->variations[$index]['images'])) {
                    $sku->clearMediaCollectionExcept('variation_images', $request->variations[$index]['images']);
                }
                //CREATE VARIATION IMAGES
                if(isset($request->variations[$index]['new_images'])) {
                    foreach ($request->variations[$index]['new_images'] as $image) {
                        $sku->addMedia($image)->toMediaCollection('variation_images');
                    }
                }
                //CREATE OR GET OPTIONS AND SYNC
                $optionIds = [];
                foreach ($request->variations[$index]['attributes'] as $attribute) {
                    $option = null;
                    $atr = Attribute::find($attribute['id']);
                    if(is_array($attribute['value']) && $attribute['value']['value']) {
                        $option = $atr->attributeOptions()->where(['value' => $attribute['value']['value']])->first();
                    } else if($attribute['value']) {
                        $option = $atr->attributeOptions()->where(['value' => $attribute['value']])->first();
                    } 
                    if(!$option && $attribute['value']) {
                        $option = $atr->attributeOptions()->create([
                            'value' => $attribute['value'],
                        ]);
                    }
                    if($option) array_push($optionIds, $option->id);
                }
                $sku->attributeOptions()->sync(array_values($optionIds));
            }
            $indexForDelete = array_search($sku->id, $request->delete_variations_ids);
            if($indexForDelete > -1) {
                $sku->delete();
            }
        });

        //CREATE VARIATIONS
        foreach ($request->variations as $variation) {
            if($variation['id'] === 'new') {
                $sku = $product->skus()->create([
                    'price' =>  $variation['price'],
                    'code'  =>  $variation['code'],
                ]);
                //SET VARIATION IMAGES
                if(isset($variation['new_images'])) {
                    foreach ($variation['new_images'] as $image) {
                        $sku->addMedia($image)->toMediaCollection('variation_images');
                    }
                }
                //CREATE OR GET OPTIONS AND SYNC
                $optionIds = [];
                foreach ($variation['attributes'] as $attribute) {
                    $option = null;
                    $atr = Attribute::find($attribute['id']);
                    if(is_array($attribute['value']) && $attribute['value']['value']) {
                        $option = $atr->attributeOptions()->where(['value' => $attribute['value']['value']])->first();
                    } else if($attribute['value']) {
                        $option = $atr->attributeOptions()->where(['value' => $attribute['value']])->first();
                    } 
                    if(!$option && $attribute['value']) {
                        $option = $atr->attributeOptions()->create([
                            'value' => $attribute['value'],
                        ]);
                    }
                    if($option) array_push($optionIds, $option->id);
                }
                $sku->attributeOptions()->sync(array_values($optionIds));
            }
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
