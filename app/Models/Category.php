<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'parent_id'
    ];

    /**
     * The relationships that should always be loaded.
     *
     * @var array
     */
    protected $with = ['children'];

    /**
     * Applies to every Category query, including nested ones made for the eager-loaded
     * `children` above — so `products_count` is available at every level of the tree,
     * not just the roots the controller queries directly.
     *
     * @var array
     */
    protected $withCount = ['products'];

    public function parent()
    {
       return $this->hasOne(Category::class, 'id', 'parent_id');
    }

    public function children()
    {
       return $this->hasMany(Category::class, 'parent_id', 'id');
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_categories');
    }
}
