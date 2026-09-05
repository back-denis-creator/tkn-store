<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;

class SitemapController extends Controller
{
    /**
     * Generate sitemap.xml listing every public, finished, indexable URL.
     *
     * Catalog and product pages are deliberately excluded for now — the
     * catalog's content-population workflow isn't finished, those routes are
     * not linked from anywhere in the UI, and the current data has
     * placeholder/test products. Add them back (see git history for the
     * Product-listing code this replaced) once the catalog is ready to launch,
     * and also remove the matching noindex tags in Catalog.vue/Product.vue.
     */
    public function index(): Response
    {
        $urls = [
            ['loc' => route('home'), 'changefreq' => 'daily', 'priority' => '1.0'],
            ['loc' => route('horeca'), 'changefreq' => 'monthly', 'priority' => '0.7'],
            ['loc' => route('about'), 'changefreq' => 'monthly', 'priority' => '0.5'],
            ['loc' => route('contacts'), 'changefreq' => 'monthly', 'priority' => '0.5'],
            ['loc' => route('delivery'), 'changefreq' => 'monthly', 'priority' => '0.5'],
        ];

        $xml = view('sitemap', ['urls' => $urls])->render();

        return response($xml, 200)->header('Content-Type', 'text/xml');
    }
}
