<?php

namespace Tests\Feature;

use App\Models\HeroSlide;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

/**
 * The homepage hero is an auto-rotating slider whose images the admin uploads.
 * Every upload is converted to webp before storage, so the homepage — the
 * heaviest page for a first-time visitor — serves the smaller format.
 */
class HeroSlideTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['role' => User::ADMIN_ID]);
    }

    public function test_an_uploaded_slide_is_stored_as_webp(): void
    {
        $response = $this->actingAs($this->admin())->post('/hero-slides', [
            'image' => UploadedFile::fake()->image('banner.png', 1920, 800),
        ]);

        $response->assertRedirect(route('hero-slides.index'));

        $slide = HeroSlide::sole();
        $media = $slide->getFirstMedia('image');

        $this->assertNotNull($media);
        $this->assertSame('image/webp', $media->mime_type);
        $this->assertSame("hero-slide-{$slide->id}.webp", $media->file_name);
    }

    public function test_slides_get_an_increasing_sort_order(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)->post('/hero-slides', [
            'image' => UploadedFile::fake()->image('first.png'),
        ]);
        $this->actingAs($admin)->post('/hero-slides', [
            'image' => UploadedFile::fake()->image('second.png'),
        ]);

        $this->assertSame([1, 2], HeroSlide::orderBy('id')->pluck('sort_order')->all());
    }

    public function test_the_admin_can_reorder_the_slides(): void
    {
        $first = HeroSlide::create(['sort_order' => 1]);
        $second = HeroSlide::create(['sort_order' => 2]);

        $this->actingAs($this->admin())
            ->patch(route('hero-slides.reorder'), ['ids' => [$second->id, $first->id]])
            ->assertSessionHasNoErrors();

        $this->assertSame(0, $second->fresh()->sort_order);
        $this->assertSame(1, $first->fresh()->sort_order);
    }

    public function test_the_admin_can_delete_a_slide(): void
    {
        $this->actingAs($this->admin())->post('/hero-slides', [
            'image' => UploadedFile::fake()->image('banner.png'),
        ]);

        $slide = HeroSlide::sole();

        $this->actingAs($this->admin())->delete(route('hero-slides.destroy', $slide));

        $this->assertDatabaseMissing('hero_slides', ['id' => $slide->id]);
        $this->assertDatabaseMissing('media', ['model_id' => $slide->id, 'model_type' => HeroSlide::class]);
    }

    public function test_a_slide_without_an_image_is_kept_out_of_the_hero(): void
    {
        HeroSlide::create(['sort_order' => 1]);

        $this->assertSame([], $this->get('/')->viewData('page')['props']['heroSlides']);
    }

    public function test_a_guest_cannot_manage_the_slides(): void
    {
        $this->post('/hero-slides', [
            'image' => UploadedFile::fake()->image('banner.png'),
        ])->assertRedirect(route('login'));

        $this->assertSame(0, HeroSlide::count());
    }

    public function test_the_homepage_gets_the_slides_in_playback_order(): void
    {
        $admin = $this->admin();
        $this->actingAs($admin)->post('/hero-slides', [
            'image' => UploadedFile::fake()->image('first.png'),
        ]);
        $this->actingAs($admin)->post('/hero-slides', [
            'image' => UploadedFile::fake()->image('second.png'),
        ]);

        HeroSlide::orderBy('id')->get()->each(
            fn (HeroSlide $slide, int $index) => $slide->update(['sort_order' => 1 - $index])
        );

        $slides = $this->get('/')->viewData('page')['props']['heroSlides'];

        $this->assertCount(2, $slides);
        $this->assertSame(
            HeroSlide::orderBy('sort_order')->pluck('id')->all(),
            array_column($slides, 'id')
        );
        $this->assertStringContainsString('.webp', $slides[0]['url']);
    }
}
