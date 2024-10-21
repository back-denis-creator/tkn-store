<?php

use App\Http\Controllers\AttributeController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Store\PageController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', [PageController::class, 'index']);
Route::get('/catalog', [PageController::class, 'catalog'])->name('catalog');
Route::get('/contacts', [PageController::class, 'contacts'])->name('contacts');

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('/blogs', BlogController::class)->middleware('admin');
    Route::resource('/products', ProductController::class, ['except' => ['update']])->middleware('admin');
    Route::post('products/{product}', [ProductController::class, 'update'])->name('products.update')->middleware('admin');
    Route::resource('/categories', CategoryController::class)->middleware('admin');
    Route::resource('/attributes', AttributeController::class)->middleware('admin');
});

require __DIR__.'/auth.php';
