<?php

namespace App\Services;

use App\Models\Order;
use LiqPay;

class LiqPayService
{
    protected ?LiqPay $liqPay = null;

    /**
     * Lazy — this service is injected into OrderController::store() for
     * every payment method, not just LiqPay, so the SDK must not be
     * constructed (and its public/private key presence checked) until a
     * LiqPay-specific method is actually called.
     */
    protected function liqPay(): LiqPay
    {
        return $this->liqPay ??= new LiqPay(
            config('services.liqpay.public_key'),
            config('services.liqpay.private_key'),
        );
    }

    /**
     * Build the {url, data, signature} triple for an auto-submitting
     * checkout form — the standard LiqPay integration pattern (redirect the
     * browser to LiqPay's hosted payment page via a signed POST).
     *
     * @return array{url: string, data: string, signature: string}
     */
    public function buildCheckoutForm(Order $order): array
    {
        return $this->liqPay()->cnb_form_raw([
            'version' => 3,
            'action' => 'pay',
            'amount' => $order->total_amount / 100,
            'currency' => 'UAH',
            'description' => "Замовлення №{$order->id} — Casanel",
            'order_id' => $order->uuid,
            'result_url' => route('order.success', $order),
            'server_url' => route('liqpay.callback'),
        ]);
    }

    /**
     * Verify a LiqPay server callback's signature. Never trust the payload
     * without this check — anyone could POST fake "success" data otherwise.
     */
    public function isValidSignature(string $data, string $signature): bool
    {
        $privateKey = config('services.liqpay.private_key');
        $expected = base64_encode(sha1($privateKey . $data . $privateKey, true));

        return hash_equals($expected, $signature);
    }

    public function decodeData(string $data): array
    {
        return json_decode(base64_decode($data), true) ?? [];
    }
}
