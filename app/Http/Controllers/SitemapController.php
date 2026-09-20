<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\Product;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    /**
     * Generate sitemap.xml listing every public, finished, indexable URL.
     */
    public function index(): Response
    {
        $urls = [
            ['loc' => route('home'), 'changefreq' => 'daily', 'priority' => '1.0'],
            ['loc' => route('catalog'), 'changefreq' => 'daily', 'priority' => '0.9'],
            ['loc' => route('horeca'), 'changefreq' => 'monthly', 'priority' => '0.7'],
            ['loc' => route('about'), 'changefreq' => 'monthly', 'priority' => '0.5'],
            ['loc' => route('fabrics'), 'changefreq' => 'monthly', 'priority' => '0.5'],
            ['loc' => route('blog'), 'changefreq' => 'weekly', 'priority' => '0.6'],
            ['loc' => route('contacts'), 'changefreq' => 'monthly', 'priority' => '0.5'],
            ['loc' => route('delivery'), 'changefreq' => 'monthly', 'priority' => '0.5'],
        ];

        foreach (Blog::select('slug', 'updated_at')->get() as $post) {
            $urls[] = [
                'loc' => route('blog.post', $post->slug),
                'lastmod' => $post->updated_at->toAtomString(),
                'changefreq' => 'monthly',
                'priority' => '0.6',
            ];
        }

        foreach (Product::visible()->select('slug', 'updated_at')->get() as $product) {
            $urls[] = [
                'loc' => route('product', $product->slug),
                'lastmod' => $product->updated_at->toAtomString(),
                'changefreq' => 'weekly',
                'priority' => '0.8',
            ];
        }

        $xml = view('sitemap', ['urls' => $urls])->render();

        return response($xml, 200)->header('Content-Type', 'text/xml');
    }
}
