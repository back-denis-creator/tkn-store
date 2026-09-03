<x-mail::message>
# Статус вашого замовлення змінено

Доброго дня, {{ $order->customer_name }}!

Замовлення **№{{ substr($order->uuid, 0, 8) }}** тепер має статус:

## {{ \App\Models\Order::STATUS_NAMES[$order->status] ?? 'Невідомо' }}

<x-mail::button :url="route('order.success', $order)">
Переглянути замовлення
</x-mail::button>

Якщо у вас виникли питання — зв'яжіться з нами, ми завжди раді допомогти.

З повагою,<br>
Casanel
</x-mail::message>
