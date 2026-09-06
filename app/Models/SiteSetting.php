<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteSetting extends Model
{
    const MODE_FREE_SHIPPING = 'free_shipping';

    const MODE_CUSTOM = 'custom';

    protected $fillable = [
        'announcement_enabled',
        'announcement_mode',
        'announcement_custom_text',
        'free_shipping_threshold',
    ];

    protected function casts(): array
    {
        return [
            'announcement_enabled' => 'boolean',
            'free_shipping_threshold' => 'integer',
        ];
    }

    public static function current(): self
    {
        return static::query()->firstOrCreate([]);
    }
}
