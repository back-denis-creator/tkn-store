<?php

namespace App\Http\Controllers;

use App\Models\Attribute;
use App\Models\Category;
use App\Models\Product;
use App\Models\Sku;
use Cocur\Slugify\Slugify;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Product::with([
            'categories',
            'skus' => fn ($skus) => $skus->with('attributeOptions'),
        ])->paginate(10);

        return Inertia::render(
            'Products/Index',
            [
                'products' => $products,
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
                'attributes' => Attribute::with('attributeOptions')->get(),
            ]
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:20000',
            'category_ids' => 'array',
            'is_hidden' => 'boolean',
            'has_fabric_selection' => 'boolean',
            'share_variation_images' => 'boolean',
            'variations' => 'array',
            'variations.*.code' => 'required|string|max:255',
            'variations.*.price' => 'required|numeric|min:0',
            'variations.*.images.*' => 'file|mimes:jpg,jpeg,png,webp,mp4,mov,webm|max:20000',
        ]);

        $this->assertVariationsArrivedIntact($request);

        $slugify = new Slugify;

        // Everything below is one unit of work: a failure part-way used to leave
        // the product saved with only some of its variations written. Media files
        // already on disk are not covered — only the rows that point at them.
        DB::transaction(function () use ($request, $slugify) {
            $product = Product::create([
                'name' => $request->name,
                'slug' => $this->uniqueSlug($request->slug ?: $slugify->slugify($request->name)),
                'description' => $request->description,
                'is_hidden' => $request->boolean('is_hidden'),
                'has_fabric_selection' => $request->boolean('has_fabric_selection'),
                'share_variation_images' => $request->boolean('share_variation_images'),
            ]);

            // CREATE VARIATIONS
            foreach ($request->variations as $variation) {
                $sku = $product->skus()->create([
                    'price' => $variation['price'],
                    'code' => $variation['code'],
                ]);

                // SET VARIATION IMAGES
                // A variation with no files never gets an 'images' key at all —
                // multipart/FormData drops empty arrays entirely, it doesn't
                // send them as [].
                foreach ($variation['images'] ?? [] as $image) {
                    $sku->addMedia($image)->toMediaCollection('variation_images');
                }

                $sku->attributeOptions()->sync($this->resolveOptionIds($variation['attributes']));
            }

            if (! empty($request->category_ids)) {
                $product->categories()->sync($request->category_ids);
            }

            $this->syncSharedVariationImages($product);
        });

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
                    'skus' => fn ($skus) => $skus->with('attributeOptions'),
                ])->where('id', $product->id)->first(),
                'categories' => Category::all(),
                'attributes' => Attribute::with('attributeOptions')->get(),
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
            'slug' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:20000',
            'category_ids' => 'array',
            'is_hidden' => 'boolean',
            'has_fabric_selection' => 'boolean',
            'share_variation_images' => 'boolean',
            'delete_variations_ids' => 'array',
            'variations' => 'array',
            'variations.*.code' => 'required|string|max:255',
            'variations.*.price' => 'required|numeric|min:0',
            'variations.*.new_images.*' => 'file|mimes:jpg,jpeg,png,webp,mp4,mov,webm|max:20000',
        ]);

        $this->assertVariationsArrivedIntact($request);

        $slugify = new Slugify;

        // One unit of work — see store(). A crash part-way through the variation
        // loop used to leave the product saved with only some variations updated.
        DB::transaction(function () use ($request, $product, $slugify) {
            $product->name = $request->name;
            $product->slug = $this->uniqueSlug($request->slug ?: $slugify->slugify($request->name), $product->id);
            $product->description = $request->description;
            $product->is_hidden = $request->boolean('is_hidden');
            $product->has_fabric_selection = $request->boolean('has_fabric_selection');
            $product->share_variation_images = $request->boolean('share_variation_images');
            $product->save();

            if (! empty($request->category_ids)) {
                $product->categories()->sync($request->category_ids);
            }

            // UPDATE OR DELETE EXIST VARIATIONS
            $skusIds = array_column($request->variations, 'id');
            $product->skus()->each(function (object $sku) use ($request, $skusIds) {
                // A Sku the form did not send back — the admin removed that variation
                // in the UI — gives array_search() false, and $request->variations[false]
                // is PHP for $request->variations[0]: the Sku on its way out first got
                // overwritten with the first variation's price, code and photos. Skip it;
                // the delete_variations_ids check below still runs.
                $index = array_search($sku->id, $skusIds);
                if ($index !== false && isset($request->variations[$index])) {
                    $sku->update([
                        'price' => $request->variations[$index]['price'],
                        'code' => $request->variations[$index]['code'],
                    ]);
                    // DELETE VARIATION IMAGES
                    // No 'images' key at all means the admin removed every photo:
                    // multipart/FormData drops an empty array instead of sending [].
                    // (It can no longer mean a truncated payload — assertVariationsArrivedIntact()
                    // has already checked that, since 'images' is serialized before the
                    // 'attributes' key it looks for.) Guarding with isset() here left
                    // those photos in place, so "delete all" silently did nothing.
                    //
                    // clearMediaCollectionExcept() matches each existing media against
                    // this list by reading ITS OWN 'id' key (Media::getKeyName()) — so
                    // it needs actual Media models here, not raw ids. Raw ids (e.g. a
                    // plain [2]) never match, since data_get(2, 'id') is always null,
                    // and every image in the collection gets deleted regardless of
                    // what the client asked to keep.
                    //
                    // Two shapes are accepted: the bare id list the form sends now,
                    // and the full media objects it used to send — so an admin whose
                    // tab was open across a deploy does not lose a gallery on save.
                    $keptMediaIds = collect($request->variations[$index]['images'] ?? [])
                        ->map(fn ($image) => is_array($image) ? ($image['id'] ?? null) : $image)
                        ->filter()
                        ->map(fn ($id) => (int) $id)
                        ->all();
                    $keptMedia = $sku->getMedia('variation_images')->whereIn('id', $keptMediaIds);
                    $sku->clearMediaCollectionExcept('variation_images', $keptMedia);
                    // CREATE VARIATION IMAGES
                    if (isset($request->variations[$index]['new_images'])) {
                        foreach ($request->variations[$index]['new_images'] as $image) {
                            $sku->addMedia($image)->toMediaCollection('variation_images');
                        }
                    }
                    $sku->attributeOptions()->sync($this->resolveOptionIds($request->variations[$index]['attributes']));
                }
                if ($request->has('delete_variations_ids')) {
                    $indexForDelete = array_search($sku->id, $request->delete_variations_ids);
                    if ($indexForDelete > -1) {
                        $sku->delete();
                    }
                }
            });

            // CREATE VARIATIONS
            foreach ($request->variations as $variation) {
                if ($variation['id'] === 'new') {
                    $sku = $product->skus()->create([
                        'price' => $variation['price'],
                        'code' => $variation['code'],
                    ]);
                    // SET VARIATION IMAGES
                    if (isset($variation['new_images'])) {
                        foreach ($variation['new_images'] as $image) {
                            $sku->addMedia($image)->toMediaCollection('variation_images');
                        }
                    }
                    $sku->attributeOptions()->sync($this->resolveOptionIds($variation['attributes']));
                }
            }

            $this->syncSharedVariationImages($product);
        });

        return redirect()->route('products.index')->with('message', 'Product Updated Successfully');
    }

    /**
     * PHP truncates a POST that goes over max_input_vars from the end, without
     * telling the application — and 'attributes' is the last key serialized
     * inside every variation, so a variation that arrives without it means the
     * browser sent more than PHP accepted. Both store() and update() would then
     * die on a null key deep inside the variation loop (500), which reads like
     * a broken product rather than a payload that never fully arrived. Tell the
     * admin what actually happened instead.
     */
    /**
     * Two products named the same produced the same slug, and the storefront
     * resolves a slug with first() — so the newer product became unreachable
     * behind the older one and the sitemap listed that URL twice. Nothing in
     * the form or the schema prevented it.
     */
    private function uniqueSlug(string $slug, ?int $ignoreProductId = null): string
    {
        $base = $slug;
        $suffix = 2;

        while (Product::where('slug', $slug)
            ->when($ignoreProductId, fn ($query) => $query->where('id', '!=', $ignoreProductId))
            ->exists()
        ) {
            $slug = $base.'-'.$suffix++;
        }

        return $slug;
    }

    /**
     * Turn a variation's posted attributes into the option ids to sync, with
     * the unit each one carries.
     *
     * The value is either a string the admin typed or the {id, value} option
     * they picked from the dropdown. Both mean the same thing here: use the
     * attribute's existing option with that value, and only create one when it
     * genuinely does not exist yet. store() used to create unconditionally for
     * a typed string, which is how the catalogue ended up with two "150*200см"
     * rows under Розмір.
     */
    private function resolveOptionIds(array $attributes): array
    {
        $optionIds = [];

        foreach ($attributes as $attribute) {
            $value = is_array($attribute['value'] ?? null)
                ? ($attribute['value']['value'] ?? null)
                : ($attribute['value'] ?? null);

            if (blank($value)) {
                continue;
            }

            $model = Attribute::find($attribute['id']);

            if (! $model) {
                continue;
            }

            $option = $model->attributeOptions()->where('value', $value)->first()
                ?: $model->attributeOptions()->create(['value' => $value]);

            $optionIds[$option->id] = ['unit' => $attribute['unit'] ?? null];
        }

        return $optionIds;
    }

    private function assertVariationsArrivedIntact(Request $request): void
    {
        foreach ($request->input('variations', []) as $variation) {
            if (! isset($variation['attributes'])) {
                throw ValidationException::withMessages([
                    'variations' => 'Форма не дійшла на сервер повністю — забагато даних за один раз. '
                        .'Збережіть товар з меншою кількістю варіацій або зображень, а решту додайте окремо.',
                ]);
            }
        }
    }

    /**
     * When share_variation_images is on, every variation should show the same
     * photos as the first one, so the admin only has to upload them once.
     * Re-copying on every save (rather than only when the flag flips) also
     * picks up new photos added to the first variation later.
     */
    private function syncSharedVariationImages(Product $product): void
    {
        if (! $product->share_variation_images) {
            return;
        }

        $skus = $product->skus()->orderBy('id')->get();
        $firstSku = $skus->first();
        if (! $firstSku) {
            return;
        }

        $sourceMedia = $firstSku->getMedia('variation_images');
        foreach ($skus->skip(1) as $sku) {
            $sku->clearMediaCollection('variation_images');
            foreach ($sourceMedia as $media) {
                $media->copy($sku, 'variation_images');
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        // skus.product_id has no ON DELETE rule (unlike order_items.product_id/sku_id,
        // which SET NULL), so deleting a product while its skus still reference it
        // fails with a 1451 FK violation. Deleting each sku through Eloquent first
        // (rather than relying on a DB-level cascade) also runs Spatie's media
        // cleanup and cascades the attribute_option_sku pivot as normal.
        $product->skus->each(fn (Sku $sku) => $sku->delete());
        $product->delete();

        return redirect()->route('products.index')->with('message', 'Product Delete Successfully');
    }
}
