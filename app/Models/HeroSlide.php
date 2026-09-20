<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * One image in the homepage hero's auto-rotating slider. sort_order controls
 * playback order; the image itself lives in the "image" media collection,
 * already converted to webp before it ever reaches addMedia() (see
 * HeroSlideController::store()).
 *
 * title and description override the translated default hero text for this
 * one slide, and stay empty on a slide that keeps the default. They hold a
 * single language, like the announcement bar's own custom text.
 */
class HeroSlide extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $fillable = [
        'title',
        'description',
        'show_button',
        'sort_order',
    ];

    protected $casts = [
        'show_button' => 'boolean',
    ];

    public function registerMediaConversions(?Media $media = null): void
    {
        $this
            ->addMediaConversion('preview')
            ->fit(Fit::Crop, 400, 225);
    }
}
