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

        $response->assertSessionHasNoErrors();

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

    public function test_the_admin_can_set_the_text_and_the_button_of_a_slide(): void
    {
        $slide = HeroSlide::create(['sort_order' => 1]);

        $this->actingAs($this->admin())
            ->patch(route('hero-slides.update', $slide), [
                'title' => 'Осіння колекція',
                'description' => "Перший рядок\nДругий рядок",
                'show_button' => false,
            ])
            ->assertSessionHasNoErrors();

        $slide->refresh();

        $this->assertSame('Осіння колекція', $slide->title);
        $this->assertSame("Перший рядок\nДругий рядок", $slide->description);
        $this->assertFalse($slide->show_button);
    }

    public function test_clearing_the_text_of_a_slide_brings_back_the_default(): void
    {
        $slide = HeroSlide::create(['sort_order' => 1, 'title' => 'Щось', 'description' => 'Щось іще']);

        $this->actingAs($this->admin())->patch(route('hero-slides.update', $slide), [
            'title' => '',
            'description' => '',
            'show_button' => true,
        ]);

        $slide->refresh();

        $this->assertNull($slide->title);
        $this->assertNull($slide->description);
    }

    public function test_the_settings_page_lists_the_slides(): void
    {
        $this->actingAs($this->admin())->post('/hero-slides', [
            'image' => UploadedFile::fake()->image('banner.png'),
        ]);

        HeroSlide::sole()->update(['title' => 'Осіння колекція', 'show_button' => false]);

        $slides = $this->actingAs($this->admin())
            ->get(route('settings.index'))
            ->viewData('page')['props']['heroSlides'];

        $this->assertCount(1, $slides);
        $this->assertSame('Осіння колекція', $slides[0]['title']);
        $this->assertFalse($slides[0]['show_button']);
        $this->assertStringContainsString('.webp', $slides[0]['url']);
    }

    public function test_a_guest_cannot_manage_the_slides(): void
    {
        $this->post('/hero-slides', [
            'image' => UploadedFile::fake()->image('banner.png'),
        ])->assertRedirect(route('login'));

        $slide = HeroSlide::create(['sort_order' => 1, 'title' => 'Оригінал']);

        $this->patch(route('hero-slides.update', $slide), [
            'title' => 'Зламано',
            'show_button' => false,
        ])->assertRedirect(route('login'));

        $this->assertSame(1, HeroSlide::count());
        $this->assertSame('Оригінал', $slide->fresh()->title);
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
