<?php

namespace Tests\Feature;

use App\Models\Blog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

/**
 * The blog: an admin writes posts with a cover photo, a title and a short
 * description, and the storefront shows them at /blog.
 */
class BlogTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['role' => User::ADMIN_ID]);
    }

    private function createPost(array $attributes = []): Blog
    {
        return Blog::create(array_merge([
            'title' => 'Як доглядати за скатертиною',
            'slug' => 'yak-doglyadaty',
            'excerpt' => 'Коротко про прання і прасування.',
            'content' => '<p>Текст допису</p>',
        ], $attributes));
    }

    public function test_the_admin_creates_a_post_with_a_cover(): void
    {
        $this->actingAs($this->admin())->post(route('blogs.store'), [
            'title' => 'Як доглядати за скатертиною',
            'excerpt' => 'Коротко про прання і прасування.',
            'content' => '<p>Текст допису</p>',
            'cover' => UploadedFile::fake()->image('cover.jpg', 1600, 1000),
        ])->assertRedirect(route('blogs.index'));

        $blog = Blog::sole();

        $this->assertSame('yak-doglyadati-za-skatertinoyu', $blog->slug);
        $this->assertSame('Коротко про прання і прасування.', $blog->excerpt);
        $this->assertNotNull($blog->coverUrl());
    }

    public function test_a_post_keeps_its_cover_when_only_the_text_changes(): void
    {
        $admin = $this->admin();
        $this->actingAs($admin)->post(route('blogs.store'), [
            'title' => 'Допис',
            'content' => '<p>Текст</p>',
            'cover' => UploadedFile::fake()->image('cover.jpg'),
        ]);

        $blog = Blog::sole();

        $this->actingAs($admin)->patch(route('blogs.update', $blog), [
            'title' => 'Допис, оновлений',
            'slug' => $blog->slug,
            'content' => '<p>Новий текст</p>',
        ])->assertSessionHasNoErrors();

        $this->assertSame('Допис, оновлений', $blog->fresh()->title);
        $this->assertNotNull($blog->fresh()->coverUrl());
    }

    public function test_the_admin_can_remove_a_cover(): void
    {
        $admin = $this->admin();
        $this->actingAs($admin)->post(route('blogs.store'), [
            'title' => 'Допис',
            'content' => '<p>Текст</p>',
            'cover' => UploadedFile::fake()->image('cover.jpg'),
        ]);

        $blog = Blog::sole();

        $this->actingAs($admin)->patch(route('blogs.update', $blog), [
            'title' => 'Допис',
            'slug' => $blog->slug,
            'content' => '<p>Текст</p>',
            'remove_cover' => true,
        ]);

        $this->assertNull($blog->fresh()->coverUrl());
    }

    public function test_two_posts_cannot_share_a_slug(): void
    {
        $this->createPost();

        $this->actingAs($this->admin())->post(route('blogs.store'), [
            'title' => 'Інший допис',
            'slug' => 'yak-doglyadaty',
            'content' => '<p>Текст</p>',
        ])->assertSessionHasErrors('slug');
    }

    public function test_the_blog_page_lists_the_posts(): void
    {
        $this->createPost(['title' => 'Перший', 'slug' => 'pershyi']);
        $this->createPost(['title' => 'Другий', 'slug' => 'drugyi']);

        $posts = $this->get(route('blog'))->viewData('page')['props']['posts'];

        $this->assertCount(2, $posts);
        $this->assertSame(['Перший', 'Другий'], array_column($posts, 'title'));
    }

    public function test_a_post_opens_by_its_slug(): void
    {
        $this->createPost();

        $props = $this->get(route('blog.post', 'yak-doglyadaty'))->assertOk()->viewData('page')['props'];

        $this->assertSame('Як доглядати за скатертиною', $props['post']['title']);
        $this->assertSame('<p>Текст допису</p>', $props['post']['content']);
    }

    public function test_an_unknown_post_is_not_found(): void
    {
        $this->get(route('blog.post', 'nichogo-nemaye'))->assertNotFound();
    }

    public function test_a_post_suggests_other_posts_to_read(): void
    {
        $this->createPost(['title' => 'Перший', 'slug' => 'pershyi']);
        $this->createPost(['title' => 'Другий', 'slug' => 'drugyi']);

        $more = $this->get(route('blog.post', 'pershyi'))->viewData('page')['props']['morePosts'];

        $this->assertCount(1, $more);
        $this->assertSame('Другий', $more[0]['title']);
    }

    public function test_the_sitemap_lists_the_blog(): void
    {
        $this->createPost();

        $sitemap = $this->get('/sitemap.xml')->assertOk()->getContent();

        $this->assertStringContainsString(route('blog'), $sitemap);
        $this->assertStringContainsString(route('blog.post', 'yak-doglyadaty'), $sitemap);
    }

    public function test_a_guest_cannot_write_a_post(): void
    {
        $this->post(route('blogs.store'), [
            'title' => 'Чужий допис',
            'content' => '<p>Текст</p>',
        ])->assertRedirect(route('login'));

        $this->assertSame(0, Blog::count());
    }
}
