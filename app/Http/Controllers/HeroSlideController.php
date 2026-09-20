<?php

namespace App\Http\Controllers;

use App\Models\HeroSlide;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Spatie\Image\Image;

class HeroSlideController extends Controller
{
    /**
     * The homepage hero's slides, in playback order, for the settings page
     * that edits them.
     */
    public static function slidesForAdmin(): Collection
    {
        return HeroSlide::orderBy('sort_order')->get()->map(fn (HeroSlide $slide) => [
            'id' => $slide->id,
            'url' => $slide->getFirstMediaUrl('image'),
            'preview_url' => $slide->getFirstMediaUrl('image', 'preview'),
            'title' => $slide->title,
            'description' => $slide->description,
            'show_button' => $slide->show_button,
        ]);
    }

    /**
     * Store a newly uploaded slide, converted to webp first.
     */
    public function store(Request $request)
    {
        $request->validate([
            'image' => 'required|image|max:10240',
        ]);

        // Convert first, and keep the row and its image in one transaction: a
        // slide row that outlives a failed image is a blank frame in the
        // rotation that the admin then has to find and delete by hand.
        $webpPath = $this->convertToWebp($request->file('image'));

        DB::transaction(function () use ($webpPath) {
            $slide = HeroSlide::create([
                'sort_order' => (int) HeroSlide::max('sort_order') + 1,
            ]);

            $slide->addMedia($webpPath)
                ->usingFileName("hero-slide-{$slide->id}.webp")
                ->toMediaCollection('image');
        });

        return back()->with('message', 'Слайд додано');
    }

    /**
     * Update the text this slide shows over its image.
     */
    public function update(Request $request, HeroSlide $heroSlide)
    {
        $validated = $request->validate([
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:1000',
            'show_button' => 'required|boolean',
        ]);

        $heroSlide->update([
            'title' => $validated['title'] ?: null,
            'description' => $validated['description'] ?: null,
            'show_button' => $validated['show_button'],
        ]);

        return back()->with('message', 'Слайд збережено');
    }

    /**
     * Remove the specified slide.
     */
    public function destroy(HeroSlide $heroSlide)
    {
        $heroSlide->clearMediaCollection();
        $heroSlide->delete();

        return back()->with('message', 'Слайд видалено');
    }

    /**
     * Persist the slider's new playback order (drag-and-drop in the admin).
     */
    public function reorder(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:hero_slides,id',
        ]);

        foreach ($request->input('ids') as $index => $id) {
            HeroSlide::whereKey($id)->update(['sort_order' => $index]);
        }

        return back();
    }

    /**
     * Converts the uploaded photo to webp before it ever reaches Spatie
     * MediaLibrary, so the hero slider serves the smaller format as the
     * actual stored file — not just as a generated "preview" conversion.
     * addMedia() deletes this temp file itself once it's done with it
     * (FileAdder's default preserveOriginal(false) behavior for a string path).
     *
     * Not tempnam() — it creates the file at a path with no .webp extension,
     * and Spatie's own cleanup only unlinks the path it was actually given
     * (the one below, with the extension), leaking the tempnam one instead.
     *
     * useImageDriver(), not Image::load() — that defaults to Imagick
     * regardless of environment, but this app (like MediaLibrary's own
     * conversions, see config/media-library.php) only has the gd extension.
     */
    private function convertToWebp(UploadedFile $file): string
    {
        $destination = sys_get_temp_dir().'/'.Str::uuid().'.webp';
        Image::useImageDriver(config('media-library.image_driver'))
            ->loadFile($file->getRealPath())
            ->format('webp')
            ->save($destination);

        return $destination;
    }
}
