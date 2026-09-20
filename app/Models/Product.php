<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

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

    /**
     * The admin's Quill editor exports its HTML with every single space
     * written as a non-breaking one. A paragraph then holds no place a
     * browser may break at, so it runs off the side of a phone screen and
     * widens the whole page. Cleaned on the way in, so the editor cannot
     * store it again, and on the way out, so descriptions saved before this
     * wrap as well.
     */
    protected function description(): Attribute
    {
        return Attribute::make(
            get: fn (?string $value) => $this->withBreakableSpaces($value),
            set: fn (?string $value) => $this->withBreakableSpaces($value),
        );
    }

    private function withBreakableSpaces(?string $value): ?string
    {
        return $value === null ? null : str_replace(["\u{00A0}", '&nbsp;'], ' ', $value);
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
