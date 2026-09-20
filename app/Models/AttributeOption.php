<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class AttributeOption extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = [
        'attribute_id',
        'value',
        'meta',
        'sub_meta',
        'description',
        'article',
    ];

    public function registerMediaConversions(?Media $media = null): void
    {
        $this
            ->addMediaConversion('preview')
            ->fit(Fit::Contain, 300, 300);
    }

    // Fabric print taxonomy for a color/fabric attribute's options — meta
    // holds a category's id, sub_meta (nullable) holds a subcategory's id
    // from that category's own "subcategories" list, when it has one. Keep
    // the name (COLOR_GROUPS): every existing prop/column already uses it,
    // and it still means "how this color/fabric attribute option is
    // grouped" even though the groups are prints/categories, not colors.
    const COLOR_GROUPS = [
        ['id' => 0, 'name' => 'Однотонні'],
        ['id' => 1, 'name' => 'Мармур і Текстура'],
        ['id' => 2, 'name' => 'Геометричні', 'subcategories' => [
            ['id' => 0, 'name' => 'Смужки'],
            ['id' => 1, 'name' => 'Клітинка'],
            ['id' => 2, 'name' => 'Горошок'],
            ['id' => 3, 'name' => 'Ромби та трикутники'],
            ['id' => 4, 'name' => 'Зигзаг'],
            ['id' => 5, 'name' => 'Абстрактна геометрія'],
        ]],
        ['id' => 3, 'name' => 'Ботаніка', 'subcategories' => [
            ['id' => 0, 'name' => 'Великі квіти та букети'],
            ['id' => 1, 'name' => 'Дрібні квіти'],
            ['id' => 2, 'name' => 'Листя'],
            ['id' => 3, 'name' => 'Трави та гілки'],
            ['id' => 4, 'name' => 'Фрукти та ягоди'],
            ['id' => 5, 'name' => 'Туаль де Жуі та класика'],
            ['id' => 6, 'name' => 'Овочі та зелень'],
        ]],
        ['id' => 4, 'name' => 'Тваринні мотиви', 'subcategories' => [
            ['id' => 0, 'name' => 'Птахи'],
            ['id' => 1, 'name' => 'Метелики'],
            ['id' => 2, 'name' => 'Інші тварини'],
        ]],
        ['id' => 5, 'name' => 'Орнаментальні та традиційні', 'subcategories' => [
            ['id' => 0, 'name' => 'Вензелі / монограми'],
            ['id' => 1, 'name' => 'Класичні орнаменти'],
            ['id' => 2, 'name' => 'Традиційні'],
            ['id' => 3, 'name' => 'Абстракція (не геометрична)'],
        ]],
        ['id' => 6, 'name' => 'Святкові', 'subcategories' => [
            ['id' => 0, 'name' => 'Новорічні'],
            ['id' => 1, 'name' => 'Великодній'],
            ['id' => 2, 'name' => 'Інші сезонні'],
        ]],
        ['id' => 7, 'name' => 'Дитячі'],
        ['id' => 8, 'name' => 'Інші', 'subcategories' => [
            ['id' => 0, 'name' => 'Морський'],
            ['id' => 1, 'name' => 'Сердечка'],
            ['id' => 2, 'name' => 'Солодкі мотиви'],
        ]],
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

    public function defaultColors(): BelongsToMany
    {
        return $this->belongsToMany(DefaultColor::class, 'attribute_option_default_color');
    }
}
