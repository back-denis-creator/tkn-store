<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AdminOrderController extends Controller
{
    public function index(Request $request)
    {
        $orders = Order::query()
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->latest()
            ->paginate(20);

        return Inertia::render('Orders/Index', [
            'orders' => $orders,
            'statusFilter' => $request->status,
            'statuses' => Order::STATUS_NAMES,
        ]);
    }

    public function show(Order $order)
    {
        return Inertia::render('Orders/Show', [
            'order' => $order->load('orderItems'),
            'statuses' => Order::STATUS_NAMES,
        ]);
    }

    public function update(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|integer|in:' . implode(',', array_keys(Order::STATUS_NAMES)),
        ]);

        $order->update(['status' => $request->status]);

        return back()->with('message', 'Статус замовлення оновлено');
    }
}
