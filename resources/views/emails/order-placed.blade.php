<x-mail::message>
# Дякуємо за замовлення, {{ $order->customer_name }}!

Номер замовлення: **{{ substr($order->uuid, 0, 8) }}**

<x-mail::table>
| Товар | Шт. | Сума |
| :--- | :---: | ---: |
@foreach ($order->orderItems as $item)
@php
    $label = $item->product_name.($item->attributes_summary ? ' — '.$item->attributes_summary : '');
    $productUrl = $item->product?->slug ? route('product', $item->product->slug) : null;
@endphp
| {{ $productUrl ? "[$label]($productUrl)" : $label }} | {{ $item->quantity }} | {{ number_format($item->price * $item->quantity / 100, 2, ',', "\u{00A0}") }}&nbsp;грн |
@endforeach
</x-mail::table>

**Всього: {{ number_format($order->total_amount / 100, 2, ',', "\u{00A0}") }}&nbsp;грн**

<x-mail::button :url="route('order.success', $order)">
Переглянути замовлення
</x-mail::button>

Ми зв'яжемося з вами за номером {{ $order->customer_phone }} для підтвердження деталей доставки.

З повагою,<br>
Casanel
</x-mail::message>
