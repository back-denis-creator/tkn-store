<?php

namespace App\Models;

use App\Models\Concerns\NormalizesEditorHtml;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * A blog post. The slug is its public address, the excerpt is the short text
 * on the card that links to it (and the page's meta description), and the
 * content is the article itself, written in the same editor as a product
 * description and printed with the same .rich-text styles.
 */
class Blog extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia, NormalizesEditorHtml;

    protected $fillable = [
        'title',
        'slug',
        'excerpt',
        'content',
    ];

    protected function content(): Attribute
    {
        return $this->editorHtml();
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this
            ->addMediaConversion('card')
            ->fit(Fit::Crop, 800, 500);
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function coverUrl(string $conversion = ''): ?string
    {
        $url = $this->getFirstMediaUrl('cover', $conversion);

        return $url === '' ? null : $url;
    }
}
