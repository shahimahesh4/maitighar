<?php

namespace App\Http\Controllers;

use App\Models\GalleryAlbum;
use App\Models\Page;
use App\Models\Setting;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class GalleryController extends Controller
{
    public function index()
    {
        if (! Schema::hasTable('gallery_albums')) {
            Log::warning('Gallery is unavailable because the gallery_albums migration has not been applied.');
            $albums = new LengthAwarePaginator([], 0, 12, 1, [
                'path' => request()->url(),
                'query' => request()->query(),
            ]);
        } else {
            $albums = GalleryAlbum::published()->paginate(12);
        }

        $galleryPage = Page::published()->where('slug', 'gallery')->first();
        $photoGalleryTitle = $galleryPage?->title ?: 'Our Photo Gallery';
        $photoGalleryDescription = $galleryPage?->meta_description ?: 'Explore handcrafted artistry, cultural showcases, and memorable moments from Maitighar.';
        $photoGalleryBannerUrl = filled($galleryPage?->banner_image)
            ? $galleryPage->getBannerUrl()
            : $this->bannerUrl('photo_gallery_banner_image', $albums->first()?->cover_url);

        return view('gallery.index', compact('albums', 'photoGalleryBannerUrl', 'photoGalleryTitle', 'photoGalleryDescription'));
    }

    public function show(GalleryAlbum $album)
    {
        abort_unless($album->is_active && (! $album->published_at || $album->published_at->isPast()), 404);

        $allImages = Collection::make($album->imageItems());
        $page = LengthAwarePaginator::resolveCurrentPage();
        $images = new LengthAwarePaginator(
            $allImages->forPage($page, 30)->values(),
            $allImages->count(),
            30,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        return view('gallery.show', compact('album', 'images'));
    }

    private function bannerUrl(string $settingKey, ?string $fallback = null): string
    {
        $path = (string) Setting::get($settingKey, '');

        if (filled($path)) {
            return Str::startsWith($path, ['http://', 'https://'])
                ? $path
                : Storage::disk('public')->url($path);
        }

        return $fallback ?: asset('images/kba/about-us-academy.png');
    }
}
