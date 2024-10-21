<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
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
        ]);
    }

    public function catalog()
    {
        $categories = Category::all();

        return Inertia::render('Catalog', [
            'categories' => $categories,
            'canLogin' => Route::has('login'),
            'canRegister' => Route::has('register'),
            'laravelVersion' => Application::VERSION,
            'phpVersion' => PHP_VERSION,
        ]);
    }

    public function contacts()
    {
        // $categories = Category::all();

        return Inertia::render('Contacts', [
            // 'categories' => $categories,
            'canLogin' => Route::has('login'),
            'canRegister' => Route::has('register'),
            'laravelVersion' => Application::VERSION,
            'phpVersion' => PHP_VERSION,
        ]);
    }

}
