<?php

namespace App\Http\Controllers;

use App\Models\GalleryAlbum;
use App\Models\Page;
use App\Models\Product;
use App\Models\VideoAlbum;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Schema;

class SitemapController extends Controller
{
    public function __invoke(): Response
    {
        $urls = collect([
            ['loc' => route('index'), 'lastmod' => null, 'changefreq' => 'weekly', 'priority' => '1.0'],
            ['loc' => route('all-products'), 'lastmod' => null, 'changefreq' => 'daily', 'priority' => '0.9'],
            ['loc' => route('gallery.index'), 'lastmod' => null, 'changefreq' => 'weekly', 'priority' => '0.7'],
            ['loc' => route('videos.index'), 'lastmod' => null, 'changefreq' => 'weekly', 'priority' => '0.7'],
            ['loc' => route('contact'), 'lastmod' => null, 'changefreq' => 'monthly', 'priority' => '0.7'],
        ]);

        Page::published()->get(['slug', 'updated_at'])->each(fn (Page $page) => $urls->push([
            'loc' => route('pages.show', $page), 'lastmod' => $page->updated_at, 'changefreq' => 'monthly', 'priority' => '0.7',
        ]));

        Product::query()->where('is_active', true)->latest('updated_at')->get(['slug', 'updated_at'])->each(fn (Product $product) => $urls->push([
            'loc' => route('product-details', $product->slug), 'lastmod' => $product->updated_at, 'changefreq' => 'weekly', 'priority' => '0.8',
        ]));

        if (Schema::hasTable('gallery_albums')) {
            GalleryAlbum::published()->get(['slug', 'updated_at'])->each(fn (GalleryAlbum $album) => $urls->push([
                'loc' => route('gallery.show', $album), 'lastmod' => $album->updated_at, 'changefreq' => 'monthly', 'priority' => '0.6',
            ]));
        }

        if (Schema::hasTable('video_albums')) {
            VideoAlbum::published()->get(['slug', 'updated_at'])->each(fn (VideoAlbum $album) => $urls->push([
                'loc' => route('videos.show', $album), 'lastmod' => $album->updated_at, 'changefreq' => 'monthly', 'priority' => '0.6',
            ]));
        }

        return response()
            ->view('seo.sitemap', ['urls' => $urls])
            ->header('Content-Type', 'application/xml; charset=UTF-8')
            ->header('Cache-Control', 'public, max-age=3600');
    }
}
