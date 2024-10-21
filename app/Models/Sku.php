<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Sku extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = [
        'product_id',
        'code',
        'price',
    ];
 
    /**
     * The relationships that should always be loaded.
     *
     * @var array
     */
    protected $with = ['media'];

    protected function price(): Attribute
    {
        return Attribute::make(
            get: static fn($value) => $value / 100,
            set: static fn($value) => $value * 100,
        );
    }
 
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
 
    public function attributeOptions(): BelongsToMany
    {
        return $this->belongsToMany(AttributeOption::class);
    }
}