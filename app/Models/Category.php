<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

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

    /**
     * Overwrites products_count (normally each category's own direct product
     * count, via $withCount) with a count that also includes every
     * descendant's products. The catalog's category checkboxes cascade —
     * checking a parent auto-checks every descendant and filters by all of
     * their ids — so the parent's badge should show what that selection
     * actually returns, not just its own directly-tagged products.
     *
     * @param  Collection<int, self>  $categories  Root categories, each with children eager-loaded.
     */
    public static function attachAggregateProductCounts(Collection $categories): void
    {
        $productIdsByCategory = DB::table('product_categories')
            ->select('category_id', 'product_id')
            ->get()
            ->groupBy('category_id')
            ->map(fn ($rows) => $rows->pluck('product_id'));

        $walk = function (self $category) use (&$walk, $productIdsByCategory) {
            $ids = $productIdsByCategory->get($category->id, collect());
            foreach ($category->children as $child) {
                $ids = $ids->merge($walk($child));
            }
            $ids = $ids->unique();
            $category->products_count = $ids->count();

            return $ids;
        };

        foreach ($categories as $category) {
            $walk($category);
        }
    }
}
