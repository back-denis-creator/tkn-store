<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class DefaultColor extends Model
{
    protected $fillable = [
        'name',
        'hex',
        'sort_order',
    ];

    public function attributeOptions(): BelongsToMany
    {
        return $this->belongsToMany(AttributeOption::class, 'attribute_option_default_color');
    }
}
