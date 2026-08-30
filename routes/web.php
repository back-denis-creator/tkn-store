<?php

use App\Http\Controllers\AttributeController;
use App\Http\Controllers\AdminOrderController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LiqPayController;
use App\Http\Controllers\NPController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\Store\PageController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', [PageController::class, 'index'])->name('home');
Route::get('/catalog', [PageController::class, 'catalog'])->name('catalog');
Route::get('/catalog/{productSlug}', [PageController::class, 'product'])->name('product');
Route::get('/contacts', [PageController::class, 'contacts'])->name('contacts');
Route::get('/about', [PageController::class, 'about'])->name('about');
Route::get('/horeca', [PageController::class, 'horeca'])->name('horeca');
Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');
Route::get('/cart', [PageController::class, 'cart'])->name('cart');
Route::post('/cart/add', [CartController::class, 'addToCart'])->name('cart.add');
Route::delete('/cart/delete', [CartController::class, 'deleteFromCart'])->name('cart.delete');
Route::post('/cart/update', [CartController::class, 'updateCart'])->name('cart.update');
Route::get('/checkout', [PageController::class, 'checkout'])->name('checkout');
Route::post('/np-cities', [NPController::class, 'cities'])->name('np.cities');
Route::post('/np-warehouses', [NPController::class, 'warehouses'])->name('np.warehouses');
Route::post('/order', [OrderController::class, 'store'])->name('order.store');
Route::get('/order/success/{order:uuid}', [OrderController::class, 'success'])->name('order.success');
Route::get('/order/liqpay/{order:uuid}', [LiqPayController::class, 'checkout'])->name('liqpay.checkout');
Route::post('/payments/liqpay/callback', [LiqPayController::class, 'callback'])->name('liqpay.callback');

Route::get('/set-locale/{locale}', function ($locale) {
    if (!in_array($locale, config('app.locales'))) {
        abort(404);
    }
    app()->setLocale($locale);
    session()->put('locale', $locale);
    return redirect()->back();
})->name('locale.set');

Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'admin', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/profile/orders', [OrderController::class, 'myOrders'])->name('orders.mine');

    Route::resource('/blogs', BlogController::class)->middleware('admin');
    Route::resource('/products', ProductController::class, ['except' => ['update']])->middleware('admin');
    Route::post('products/{product}', [ProductController::class, 'update'])->name('products.update')->middleware('admin');
    Route::resource('/categories', CategoryController::class)->middleware('admin');
    // update() is POST, not PUT — same reason as products.update: PHP never parses a
    // multipart/form-data body into $_POST/$_FILES for anything but POST, so a real PUT
    // silently drops every field (including the file) the moment a photo is attached.
    Route::resource('/attributes', AttributeController::class, ['except' => ['update']])->middleware('admin');
    Route::post('attributes/{attribute}', [AttributeController::class, 'update'])->name('attributes.update')->middleware('admin');
    Route::resource('/orders', AdminOrderController::class)
        ->parameters(['orders' => 'order:uuid'])
        ->only(['index', 'show', 'update'])
        ->middleware('admin');
});

require __DIR__.'/auth.php';
