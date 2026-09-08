<?php

namespace App\Http\Controllers;

use App\Jobs\SendTelegramNotification;
use App\Mail\OrderPlaced;
use App\Models\Delivery;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\SiteSetting;
use App\Services\CartService;
use App\Services\LiqPayService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class OrderController extends Controller
{
    public function store(Request $request, LiqPayService $liqPay)
    {
        $deliveryIds = implode(',', array_column(Delivery::ALL, 'id'));
        $paymentIds = implode(',', array_keys(Order::PAYMENT_NAMES));

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'surname' => 'nullable|string|max:255',
            'phone' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'comment' => 'nullable|string',
            'delivery_method' => "required|integer|in:{$deliveryIds}",
            'np_city_ref' => 'required_if:delivery_method,'.Delivery::NOVA_POSHTA.'|nullable|string',
            'np_city_name' => 'nullable|string',
            'np_warehouse_ref' => 'required_if:delivery_method,'.Delivery::NOVA_POSHTA.'|nullable|string',
            'np_warehouse_name' => 'nullable|string',
            'payment_method' => "required|integer|in:{$paymentIds}",
        ]);

        $cart = CartService::hydrate();
        if ($cart->isEmpty()) {
            throw ValidationException::withMessages(['cart' => 'Кошик порожній.']);
        }

        $order = DB::transaction(function () use ($validated, $cart) {
            $totalAmount = (int) round($cart->sum(function ($product) {
                $sku = $product->skus[0];

                return $sku->price * $product->quantity;
            }) * 100);
            $settings = SiteSetting::current();
            $freeShipping = $settings->announcement_enabled
                && $settings->announcement_mode === SiteSetting::MODE_FREE_SHIPPING
                && $totalAmount >= $settings->free_shipping_threshold;

            $order = Order::create([
                'user_id' => auth()->id(),
                'customer_name' => $validated['name'],
                'customer_surname' => $validated['surname'] ?? null,
                'customer_phone' => $validated['phone'],
                'customer_email' => $validated['email'] ?? null,
                'comment' => $validated['comment'] ?? null,
                'delivery_method' => $validated['delivery_method'],
                'np_city_ref' => $validated['np_city_ref'] ?? null,
                'np_city_name' => $validated['np_city_name'] ?? null,
                'np_warehouse_ref' => $validated['np_warehouse_ref'] ?? null,
                'np_warehouse_name' => $validated['np_warehouse_name'] ?? null,
                'payment_method' => $validated['payment_method'],
                'status' => Order::STATUS_NEW,
                'total_amount' => $totalAmount,
                'free_shipping' => $freeShipping,
            ]);

            foreach ($cart as $product) {
                $sku = $product->skus[0];
                $summaryParts = $sku->attributeOptions->map(function ($option) {
                    return "{$option->attribute->name}: {$option->value}";
                })->all();
                // Fabric is decoupled from the Sku (has_fabric_selection) —
                // fold it into the same snapshot string so the workshop sees
                // it alongside the real Sku attributes, with no separate
                // order_items column or admin/customer view changes needed.
                if ($product->selected_fabric) {
                    $summaryParts[] = "{$product->selected_fabric->attribute->name}: {$product->selected_fabric->value}";
                }
                $attributesSummary = implode(', ', $summaryParts);

                $order->orderItems()->create([
                    'product_id' => $product->id,
                    'sku_id' => $sku->id,
                    'product_name' => $product->name,
                    'sku_code' => $sku->code,
                    'attributes_summary' => $attributesSummary ?: null,
                    'price' => (int) round($sku->price * 100),
                    'quantity' => $product->quantity,
                ]);
            }

            return $order;
        });

        session()->forget('cart');

        if ($order->customer_email) {
            // ->queue(), not ->send() — OrderPlaced already declares ShouldQueue,
            // but send() ignores that and sends synchronously regardless. A slow
            // or unreachable SMTP server would otherwise hang the whole checkout
            // request (and the LiqPay redirect after it) until the mail attempt
            // times out.
            Mail::to($order->customer_email)->queue(new OrderPlaced($order));
        }

        SendTelegramNotification::dispatch($this->formatOrderTelegramMessage($order));

        if ((int) $validated['payment_method'] === Order::PAYMENT_LIQPAY) {
            // Inertia::location(), not Redirect::route() — this request came in
            // as an Inertia visit (the checkout form's own submit), so a plain
            // redirect gets followed by Inertia's client-side XHR instead of a
            // real browser navigation. liqpay.checkout returns a bare Blade view
            // (an auto-submitting <form> to LiqPay's hosted page), not an
            // Inertia response, so the client can't do anything useful with it
            // over XHR and just sits there. Inertia::location() sends a 409
            // with X-Inertia-Location instead, which the client recognizes and
            // turns into a full window.location navigation — the only way that
            // auto-submit script actually gets to run.
            return Inertia::location(route('liqpay.checkout', $order));
        }

        return Redirect::route('order.success', $order);
    }

    public function myOrders(Request $request)
    {
        $orders = $request->user()->orders()
            ->with('orderItems')
            ->latest()
            ->paginate(10);

        return \Inertia\Inertia::render('Order/Mine', [
            'orders' => $orders->through(fn (Order $order) => [
                'uuid' => $order->uuid,
                'status' => $order->status,
                'status_name' => Order::STATUS_NAMES[$order->status] ?? null,
                'payment_name' => Order::PAYMENT_NAMES[$order->payment_method] ?? null,
                'total_amount' => $order->total_amount / 100,
                'free_shipping' => $order->free_shipping,
                'created_at' => $order->created_at,
                'items' => $order->orderItems->map(fn (OrderItem $item) => [
                    'product_name' => $item->product_name,
                    'attributes_summary' => $item->attributes_summary,
                    'quantity' => $item->quantity,
                    'price' => $item->price / 100,
                ]),
            ]),
        ]);
    }

    public function success(Order $order)
    {
        return \Inertia\Inertia::render('Order/Success', [
            'order' => [
                'uuid' => $order->uuid,
                'status' => $order->status,
                'status_name' => Order::STATUS_NAMES[$order->status] ?? null,
                'payment_method' => $order->payment_method,
                'payment_name' => Order::PAYMENT_NAMES[$order->payment_method] ?? null,
                'total_amount' => $order->total_amount / 100,
                'free_shipping' => $order->free_shipping,
                'paid_at' => $order->paid_at,
            ],
        ]);
    }

    private function formatOrderTelegramMessage(Order $order): string
    {
        $customerName = trim("{$order->customer_name} {$order->customer_surname}");
        $deliveryName = Delivery::NAMES[$order->delivery_method] ?? 'Невідомо';
        $paymentName = Order::PAYMENT_NAMES[$order->payment_method] ?? 'Невідомо';

        $lines = [
            "🛒 <b>Нове замовлення №{$order->id}</b>",
            "Клієнт: {$customerName}",
            "Телефон: {$order->customer_phone}",
        ];

        if ($order->customer_email) {
            $lines[] = "Email: {$order->customer_email}";
        }

        $deliveryLine = "Доставка: {$deliveryName}";
        if ($order->np_city_name) {
            $deliveryLine .= " ({$order->np_city_name}, {$order->np_warehouse_name})";
        }
        $lines[] = $deliveryLine;
        $lines[] = "Оплата: {$paymentName}";
        $lines[] = sprintf('Сума: %s грн', number_format($order->total_amount / 100, 2, '.', ' '));

        if ($order->free_shipping) {
            $lines[] = '🎁 Безкоштовна доставка — оплачує магазин';
        }

        if ($order->comment) {
            $lines[] = "Коментар: {$order->comment}";
        }

        $lines[] = '';
        $lines[] = 'Товари:';
        foreach ($order->orderItems as $item) {
            $price = number_format($item->price / 100, 2, '.', ' ');
            $lines[] = "• {$item->product_name} × {$item->quantity} — {$price} грн";
        }

        return implode("\n", $lines);
    }
}
