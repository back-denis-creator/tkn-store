<?php

namespace App\Http\Controllers;

use App\Models\Attribute;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Dashboard', [
            'stats' => [
                'products' => Product::count(),
                'categories' => Category::count(),
                'attributes' => Attribute::count(),
                'newOrders' => Order::where('status', Order::STATUS_NEW)->count(),
            ],
            'recentOrders' => Order::latest()
                ->take(5)
                ->get(['uuid', 'customer_name', 'customer_surname', 'total_amount', 'status', 'created_at'])
                ->map(fn (Order $order) => [
                    'uuid' => $order->uuid,
                    'customer_name' => trim("{$order->customer_name} {$order->customer_surname}"),
                    'total_amount' => $order->total_amount / 100,
                    'status' => $order->status,
                    'status_name' => Order::STATUS_NAMES[$order->status] ?? null,
                    'created_at' => $order->created_at,
                ]),
        ]);
    }
}
