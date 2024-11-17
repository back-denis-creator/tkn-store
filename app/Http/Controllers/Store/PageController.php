<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\Attribute;
use App\Models\AttributeOption;
use App\Models\Category;
use App\Models\Delivery;
use App\Models\Product;
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

        return Inertia::render('Product', [
            'product' => fn() => $product,
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

    public function cart()
    {
        // Получение данных из корзины
        $cartProducts = session('cart', []);
        $cartItems = collect($cartProducts);

        // Получение всех уникальных product_id
        $productIds = $cartItems->pluck('product_id')->unique();

        // Получение товаров из базы с нужными связями
        $products = Product::with('categories')
            ->whereIn('id', $productIds)
            ->get();

        // Преобразование товаров в коллекцию с учетом каждого элемента корзины
        $productsWithCartQuantity = $cartItems->map(function ($cartItem) use ($products) {
            // Найти товар по product_id
            $product = $products->firstWhere('id', $cartItem['product_id']);
            if ($product) {
                // Клонирование товара для работы с каждым SKU отдельно
                $productCopy = clone $product;
                // Загрузка одного SKU по sku_id из корзины и связанных данных
                $productCopy->setRelation('skus', $product->skus()->where('id', $cartItem['sku_id'])->with([
                    'attributeOptions.media',
                    'attributeOptions.attribute' // Добавление связи attribute
                ])->get());
                // Добавление данных из корзины
                $productCopy->quantity = $cartItem['quantity'] ?? 0;
                return $productCopy;
            }
            return null;
        })->filter();

        return Inertia::render('Cart', [
            'canLogin' => Route::has('login'),
            'canRegister' => Route::has('register'),
            'laravelVersion' => Application::VERSION,
            'phpVersion' => PHP_VERSION,
            'status' => session('status'),
            'cart' => fn() => $productsWithCartQuantity,
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
            'cart' => fn() => session()->get('cart', []),
            'deliveries' => fn() => Delivery::ALL
        ]);
    }
}
