<?php

namespace App\Models;

use App\Models\Concerns\NormalizesEditorHtml;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory, NormalizesEditorHtml;

    protected $fillable = [
        'name',
        'description',
        'slug',
        'is_hidden',
        'has_fabric_selection',
        'share_variation_images',
    ];

    protected function casts(): array
    {
        return [
            'is_hidden' => 'boolean',
            'has_fabric_selection' => 'boolean',
            'share_variation_images' => 'boolean',
        ];
    }

    /**
     * Products the storefront may show. Kept as a scope the storefront asks
     * for, not a global scope: the admin's own lists have to keep showing a
     * hidden product, otherwise it could never be brought back.
     */
    public function scopeVisible(Builder $query): Builder
    {
        return $query->where('is_hidden', false);
    }

    protected $appends = [
        'default_image',
        'default_price',
        'min_price',
        'max_price',
    ];

    protected function description(): Attribute
    {
        return $this->editorHtml();
    }

    public function getDefaultImageAttribute()
    {
        $sku = $this->defaultSku();
        $media = $sku?->media;
        if ($media && count($media)) {
            return $media[0]->original_url;
        }

        return null;
    }

    public function getDefaultPriceAttribute()
    {
        $sku = $this->defaultSku();

        return $sku?->price;
    }

    public function getMinPriceAttribute()
    {
        return $this->skus()->min('price') / 100;
    }

    public function getMaxPriceAttribute()
    {
        return $this->skus()->max('price') / 100;
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

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }
}
