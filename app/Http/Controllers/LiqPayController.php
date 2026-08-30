<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\LiqPayService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LiqPayController extends Controller
{
    /**
     * Render an auto-submitting form that redirects the browser to LiqPay's
     * hosted checkout page — the standard LiqPay integration pattern.
     */
    public function checkout(Order $order, LiqPayService $liqPay)
    {
        $form = $liqPay->buildCheckoutForm($order);

        return view('liqpay-redirect', $form);
    }

    /**
     * LiqPay's server-to-server payment status callback. Never trust the
     * browser redirect (result_url) for confirming payment — only this.
     */
    public function callback(Request $request, LiqPayService $liqPay)
    {
        $data = $request->input('data');
        $signature = $request->input('signature');

        if (!$data || !$signature || !$liqPay->isValidSignature($data, $signature)) {
            Log::warning('LiqPay callback: invalid signature', ['data' => $data]);
            return response('invalid signature', 400);
        }

        $payload = $liqPay->decodeData($data);
        $order = Order::where('uuid', $payload['order_id'] ?? null)->first();

        if (!$order) {
            Log::warning('LiqPay callback: order not found', ['payload' => $payload]);
            return response('order not found', 404);
        }

        if (in_array($payload['status'] ?? null, ['success', 'sandbox'], true)) {
            $order->update([
                'status' => Order::STATUS_PROCESSING,
                'paid_at' => now(),
            ]);
        } else {
            Log::warning('LiqPay callback: payment not successful', ['payload' => $payload]);
        }

        return response('ok');
    }
}
