<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Order extends Model
{
    const STATUS_NEW = 1;
    const STATUS_PROCESSING = 2;
    const STATUS_SHIPPED = 3;
    const STATUS_COMPLETED = 4;
    const STATUS_CANCELLED = 5;

    const STATUS_NAMES = [
        self::STATUS_NEW => 'Нове',
        self::STATUS_PROCESSING => 'В обробці',
        self::STATUS_SHIPPED => 'Відправлено',
        self::STATUS_COMPLETED => 'Завершено',
        self::STATUS_CANCELLED => 'Скасовано',
    ];

    const PAYMENT_CASH = 1;
    const PAYMENT_TRANSFER = 2;
    const PAYMENT_COD = 3;
    const PAYMENT_LIQPAY = 4;

    const PAYMENT_NAMES = [
        self::PAYMENT_CASH => 'Готівка',
        self::PAYMENT_TRANSFER => 'Грошовий переказ',
        self::PAYMENT_COD => 'Післяплата (Нова Пошта)',
        self::PAYMENT_LIQPAY => 'Оплата карткою онлайн (LiqPay)',
    ];

    protected $fillable = [
        'uuid',
        'user_id',
        'customer_name',
        'customer_surname',
        'customer_phone',
        'customer_email',
        'comment',
        'delivery_method',
        'np_city_ref',
        'np_city_name',
        'np_warehouse_ref',
        'np_warehouse_name',
        'payment_method',
        'status',
        'total_amount',
        'paid_at',
    ];

    protected function casts(): array
    {
        return [
            'delivery_method' => 'integer',
            'payment_method' => 'integer',
            'status' => 'integer',
            'total_amount' => 'integer',
            'paid_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Order $order) {
            $order->uuid = $order->uuid ?: (string) Str::uuid();
        });
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }
}
