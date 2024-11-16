<?php

use App\Http\Controllers\AttributeController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\NPController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Store\PageController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', [PageController::class, 'index']);
Route::get('/catalog', [PageController::class, 'catalog'])->name('catalog');
Route::get('/catalog/{productSlug}', [PageController::class, 'product'])->name('product');
Route::get('/contacts', [PageController::class, 'contacts'])->name('contacts');
Route::get('/cart', [PageController::class, 'cart'])->name('cart');
Route::post('/cart/add', [CartController::class, 'addToCart'])->name('cart.add');
Route::delete('/cart/delete', [CartController::class, 'deleteFromCart'])->name('cart.delete');
Route::get('/checkout', [PageController::class, 'checkout'])->name('checkout');
Route::post('/np-cities', [NPController::class, 'cities'])->name('np.cities');
Route::post('/np-warehouses', [NPController::class, 'warehouses'])->name('np.warehouses');

Route::get('/set-locale/{locale}', function ($locale) {
    if (!in_array($locale, config('app.locales'))) {
        abort(404);
    }
    app()->setLocale($locale);
    session()->put('locale', $locale);
    return redirect()->back();
})->name('locale.set');

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'admin', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('/blogs', BlogController::class)->middleware('admin');
    Route::resource('/products', ProductController::class, ['except' => ['update']])->middleware('admin');
    Route::post('products/{product}', [ProductController::class, 'update'])->name('products.update')->middleware('admin');
    Route::resource('/categories', CategoryController::class)->middleware('admin');
    Route::resource('/attributes', AttributeController::class)->middleware('admin');
});

require __DIR__.'/auth.php';
