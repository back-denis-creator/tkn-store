<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\Attribute;
use App\Models\AttributeOption;
use App\Models\Category;
use App\Models\Delivery;
use App\Models\Order;
use App\Models\Product;
use App\Models\Sku;
use App\Services\CartService;
use Cocur\Slugify\Slugify;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Application;

class PageController extends Controller
{
    /**
     * Display the home page.
     */
    public function index()
    {
        $products = Product::with([
            'categories',
            'skus'
        ])->inRandomOrder()->limit(12)->get();

        return Inertia::render('Welcome', [
            'productSlider' => $products,
            'canLogin' => Route::has('login'),
            'canRegister' => Route::has('register'),
            'laravelVersion' => Application::VERSION,
            'phpVersion' => PHP_VERSION,
            'cart' => fn() => session()->get('cart', [])
        ]);
    }

    public function product(Request $request, string $productSlug)
    {
        $query = Product::where('slug', $productSlug)->with([
            'skus',
            'skus.attributeOptions.attribute',
            'skus.attributeOptions.media',
            'categories'
        ]);

        $product = $query->first();

        $categoryIds = $product?->categories->pluck('id') ?? collect();

        $relatedProducts = $categoryIds->isEmpty() ? collect() : Product::with('skus')
            ->whereHas('categories', fn ($q) => $q->whereIn('categories.id', $categoryIds))
            ->where('id', '!=', $product->id)
            ->inRandomOrder()
            ->limit(4)
            ->get();

        return Inertia::render('Product', [
            'product' => fn() => $product,
            'relatedProducts' => fn() => $relatedProducts,
            'canLogin' => Route::has('login'),
            'canRegister' => Route::has('register'),
            'laravelVersion' => Application::VERSION,
            'phpVersion' => PHP_VERSION,
            'status' => session('status'),
            'cart' => fn() => session()->get('cart', []),
        ]);
    }

    public function catalog(Request $request)
    {
        $query = Product::with([
            'skus',
            'skus.attributeOptions.attribute',
            'categories'
        ]);

        // Фильтрация по категории, array или string category
        if($request->has('category')) {
            if(is_array($request->category)) {
                $query = $query->whereHas('categories', function ($q) use($request) {
                    $q->whereIn('id', $request->category);
                });
            } else if(is_string($request->category)) {
                $query = $query->whereHas('categories', function ($q) use($request) {
                    $q->where('id', $request->category);
                });
            }
        }

        // Получение минимальной и максимальной цены
        $minPrice = $query->clone()
            ->join('skus', 'products.id', '=', 'skus.product_id')
            ->min(\DB::raw('skus.price'));
        $maxPrice = $query->clone()
            ->join('skus', 'products.id', '=', 'skus.product_id')
            ->max(\DB::raw('skus.price'));

        // Фильтрация по прайсу
        $filteredProductIds = $query->whereHas('skus', function ($query) use ($request, $minPrice, $maxPrice) {
            $query->whereBetween('price', [
                $request->min_price ? $request->min_price * 100 : $minPrice,
                $request->max_price ? $request->max_price * 100 : $maxPrice
            ]);
        })->pluck('id');

        // Атрибуты под категорию и прайс рендж
        $attributes = Attribute::whereHas('attributeOptions.skus', function ($query) use ($filteredProductIds) {
            $query->whereIn('product_id', $filteredProductIds);
        })->with('attributeOptions.media')->get();

        $slugify = new Slugify();
        $attributesWithFirstImage = $attributes->map(function ($attribute) use($slugify, $request, $query) {
            $slug = $slugify->slugify($attribute->name);
            $checked = [];
            if($request->has($slug)) {
                $checked = $request[$slug];
                // Фильтрация по всем атрибутам кроме цвета
                if($slug !== $slugify->slugify(Attribute::COLOR)) {
                    $query->whereHas('skus', function ($query) use ($checked, $attribute) {
                        $query->whereHas('attributeOptions', function ($query) use ($checked, $attribute) {
                            $query->whereHas('attribute', function ($query) use($attribute) {
                                $query->where('name', $attribute->name);
                            })->whereIn('value', $checked);
                        });
                    });
                }
            }
            $data = [
                'id' => $attribute->id,
                'name' => $attribute->name,
                'unit_type' => $attribute->unit_type,
                'description' => $attribute->description,
                'checked' => $checked,
                'slug' => $slug,
                'attribute_options' => $attribute->attributeOptions->map(function ($option) {
                    return [
                        'id' => $option->id,
                        'value' => $option->value,
                        'meta' => $option->meta,
                        'image_url' => $option->media->first()?->getUrl(),
                    ];
                }),
            ];
            if($attribute->name === Attribute::COLOR) $data['color_groups'] = AttributeOption::COLOR_GROUPS;
            return $data;
        });

        // Фильтрация по цвету
        if($request->colors) {
            $query->whereHas('skus', function ($query) use ($request) {
                $query->whereHas('attributeOptions', function ($query) use ($request) {
                    $query->whereHas('attribute', function ($query) {
                        $query->where('name', Attribute::COLOR);
                    })->whereIn('value', $request->colors);
                });
            });
        }

        $filters = [
            'attributes' => $attributesWithFirstImage,
            'categories' => Category::where('parent_id', null)->get(),
            'prices' => [ 'min' => $minPrice / 100, 'max' => $maxPrice / 100 ]
        ];

        // The sort dropdown used to be decorative — nothing here ever called
        // orderBy(), so every option (including the previous default,
        // reverse-alphabetical) returned products in whatever order the DB
        // happened to store them. Newest-first is the default now; price
        // sorts order by each product's cheapest SKU via a correlated
        // subquery so products with multiple SKUs still sort predictably.
        if (in_array($request->sort, ['price-min-max', 'price-max-min'])) {
            $direction = $request->sort === 'price-min-max' ? 'asc' : 'desc';
            $query->orderBy(
                Sku::select('price')->whereColumn('skus.product_id', 'products.id')->orderBy('price', $direction)->limit(1),
                $direction
            );
        } elseif ($request->sort === 'asc') {
            $query->orderBy('name', 'asc');
        } elseif ($request->sort === 'desc') {
            $query->orderBy('name', 'desc');
        } else {
            $query->orderBy('created_at', 'desc');
        }

        return Inertia::render('Catalog', [
            'filters' => $filters,
            'products' => $query->paginate($request?->rows ?: 12),
            'canLogin' => Route::has('login'),
            'canRegister' => Route::has('register'),
            'laravelVersion' => Application::VERSION,
            'phpVersion' => PHP_VERSION,
            'cart' => fn() => session()->get('cart', []),
        ]);
    }

    public function contacts()
    {
        return Inertia::render('Contacts', [
            'canLogin' => Route::has('login'),
            'canRegister' => Route::has('register'),
            'laravelVersion' => Application::VERSION,
            'phpVersion' => PHP_VERSION,
            'cart' => fn() => session()->get('cart', []),
        ]);
    }

    public function about()
    {
        return Inertia::render('About', [
            'canLogin' => Route::has('login'),
            'canRegister' => Route::has('register'),
            'laravelVersion' => Application::VERSION,
            'phpVersion' => PHP_VERSION,
            'cart' => fn() => session()->get('cart', []),
        ]);
    }

    public function horeca()
    {
        return Inertia::render('Horeca', [
            'canLogin' => Route::has('login'),
            'canRegister' => Route::has('register'),
            'laravelVersion' => Application::VERSION,
            'phpVersion' => PHP_VERSION,
            'cart' => fn() => session()->get('cart', []),
        ]);
    }

    public function cart()
    {
        return Inertia::render('Cart', [
            'canLogin' => Route::has('login'),
            'canRegister' => Route::has('register'),
            'laravelVersion' => Application::VERSION,
            'phpVersion' => PHP_VERSION,
            'status' => session('status'),
            'cart' => fn() => CartService::hydrate(),
        ]);
    }

    public function checkout(Request $request)
    {
        return Inertia::render('Checkout', [
            'canLogin' => Route::has('login'),
            'canRegister' => Route::has('register'),
            'laravelVersion' => Application::VERSION,
            'phpVersion' => PHP_VERSION,
            'status' => fn() => session('status'),
            'cities' => fn() => session('cities'),
            'warehouses' => fn() => session('warehouses'),
            'cart' => fn() => CartService::hydrate(),
            'deliveries' => fn() => Delivery::ALL,
            'payments' => fn() => Order::PAYMENT_NAMES,
        ]);
    }
}
