<!doctype html>
<html lang="uk">
<head>
    <meta charset="utf-8">
    <title>Замовлення №{{ $order->id }}</title>
</head>
<body style="font-family: sans-serif; color: #1f2937;">
    <h1 style="font-size: 20px;">Дякуємо за замовлення, {{ $order->customer_name }}!</h1>
    <p>Номер замовлення: <strong>{{ $order->uuid }}</strong></p>

    <table style="width: 100%; border-collapse: collapse; margin-top: 16px;">
        <thead>
            <tr>
                <td style="border-bottom: 1px solid #e5e7eb; padding: 8px 0;"><strong>Товар</strong></td>
                <td style="border-bottom: 1px solid #e5e7eb; padding: 8px 0;"><strong>К-сть</strong></td>
                <td style="border-bottom: 1px solid #e5e7eb; padding: 8px 0;"><strong>Сума</strong></td>
            </tr>
        </thead>
        <tbody>
            @foreach ($order->orderItems as $item)
                <tr>
                    <td style="padding: 8px 0;">
                        {{ $item->product_name }}
                        @if ($item->attributes_summary)
                            <br><small style="color: #6b7280;">{{ $item->attributes_summary }}</small>
                        @endif
                    </td>
                    <td style="padding: 8px 0;">{{ $item->quantity }}</td>
                    <td style="padding: 8px 0;">{{ number_format($item->price * $item->quantity / 100, 2) }} грн</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <p style="margin-top: 16px; font-size: 18px;">
        <strong>Всього: {{ number_format($order->total_amount / 100, 2) }} грн</strong>
    </p>

    <p style="margin-top: 24px; color: #6b7280;">
        Ми зв'яжемося з вами за номером {{ $order->customer_phone }} для підтвердження деталей доставки.
    </p>
</body>
</html>
