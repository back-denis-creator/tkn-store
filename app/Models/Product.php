<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'slug'
    ];

    protected $appends = [
        'default_image',
        'default_price',
    ];

    public function getDefaultImageAttribute()
    {
        $sku = $this->defaultSku();
        $media = $sku?->media;
        if($media && count($media)) {
            return $media[0]->original_url;
        }
        return null;
    }

    public function getDefaultPriceAttribute()
    {
        $sku = $this->defaultSku();
        return $sku?->price;
    }

    public function defaultSku()
    {
        return $this->skus()->first();
    }

    public function skus(): HasMany
    {
        return $this->hasMany(Sku::class);
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'product_categories');
    }
}
