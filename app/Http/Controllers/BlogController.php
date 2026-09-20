<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use Cocur\Slugify\Slugify;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class BlogController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('Blogs/Index', [
            'blogs' => Blog::latest()->get()->map(fn (Blog $blog) => $this->toAdminArray($blog)),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Blogs/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $this->validated($request);

        $blog = Blog::create([
            'title' => $validated['title'],
            'slug' => $this->slug($validated, null),
            'excerpt' => $validated['excerpt'] ?? null,
            'content' => $validated['content'],
        ]);

        if ($request->hasFile('cover')) {
            $blog->addMedia($request->file('cover'))->toMediaCollection('cover');
        }

        return redirect()->route('blogs.index')->with('message', 'Допис створено');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Blog $blog)
    {
        return Inertia::render('Blogs/Edit', [
            'blog' => $this->toAdminArray($blog),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Blog $blog)
    {
        $validated = $this->validated($request, $blog);

        $blog->update([
            'title' => $validated['title'],
            'slug' => $this->slug($validated, $blog),
            'excerpt' => $validated['excerpt'] ?? null,
            'content' => $validated['content'],
        ]);

        // Only replace the cover when a new file actually arrives: the form
        // submits without one every time the admin edits just the text.
        if ($request->hasFile('cover')) {
            $blog->clearMediaCollection('cover');
            $blog->addMedia($request->file('cover'))->toMediaCollection('cover');
        } elseif ($request->boolean('remove_cover')) {
            $blog->clearMediaCollection('cover');
        }

        return redirect()->route('blogs.index')->with('message', 'Допис збережено');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Blog $blog)
    {
        $blog->clearMediaCollection('cover');
        $blog->delete();

        return redirect()->route('blogs.index')->with('message', 'Допис видалено');
    }

    private function validated(Request $request, ?Blog $blog = null): array
    {
        return $request->validate([
            'title' => 'required|string|max:255',
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('blogs', 'slug')->ignore($blog?->id)],
            'excerpt' => 'nullable|string|max:500',
            'content' => 'required|string|max:60000',
            'cover' => 'nullable|image|max:10240',
            'remove_cover' => 'boolean',
        ]);
    }

    private function slug(array $validated, ?Blog $blog): string
    {
        $slugify = new Slugify;
        // A nullable field the form never sent is absent from the validated
        // set, not null — reading it directly would be an undefined key.
        $slug = ($validated['slug'] ?? '') ?: $slugify->slugify($validated['title']);

        // A Cyrillic-only title can slugify to an empty string; a post still
        // needs an address, so fall back to something that always exists.
        if ($slug === '') {
            $slug = 'post-'.($blog?->id ?? (Blog::max('id') + 1));
        }

        return $slug;
    }

    private function toAdminArray(Blog $blog): array
    {
        return [
            'id' => $blog->id,
            'title' => $blog->title,
            'slug' => $blog->slug,
            'excerpt' => $blog->excerpt,
            'content' => $blog->content,
            'cover_url' => $blog->coverUrl('card'),
            'created_at' => $blog->created_at?->toDateString(),
        ];
    }
}
