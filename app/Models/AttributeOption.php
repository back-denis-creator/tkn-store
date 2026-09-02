<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class AttributeOption extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = [
        'attribute_id',
        'value',
        'meta',
    ];

    const COLOR_GROUPS = [
        ['id' => 0, 'name' => 'Однотон'],
        ['id' => 1, 'name' => 'Мармур'],
        ['id' => 2, 'name' => 'Новий рік'],
        ['id' => 3, 'name' => 'Пасха'],
        ['id' => 4, 'name' => 'Квіти'],
        ['id' => 5, 'name' => 'Геометрія'],
        ['id' => 6, 'name' => 'Прованс'],
    ];

    public function attribute(): BelongsTo
    {
        return $this->belongsTo(Attribute::class);
    }

    public function skus(): BelongsToMany
    {
        return $this->belongsToMany(Sku::class);
    }

    public function storage(): HasOne
    {
        return $this->hasOne(Storage::class);
    }
}
