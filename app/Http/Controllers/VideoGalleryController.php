<?php

namespace App\Http\Controllers;

use App\Models\VideoAlbum;
use App\Models\Page;
use App\Models\Setting;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class VideoGalleryController extends Controller
{
    public function index()
    {
        if (! Schema::hasTable('video_albums')) {
            Log::warning('Video gallery is unavailable because the video_albums migration has not been applied.');
            $albums = new LengthAwarePaginator([], 0, 12, 1, [
                'path' => request()->url(),
                'query' => request()->query(),
            ]);
        } else {
            $albums = VideoAlbum::published()->paginate(12);
        }

        $galleryPage = Page::published()->where('slug', 'videos')->first();
        $videoGalleryTitle = $galleryPage?->title ?: 'Our Video Gallery';
        $videoGalleryDescription = $galleryPage?->meta_description ?: 'Watch artisan craft, cultural stories, product showcases, and inspiring moments from Maitighar.';
        $videoGalleryBannerUrl = filled($galleryPage?->banner_image)
            ? $galleryPage->getBannerUrl()
            : $this->bannerUrl('video_gallery_banner_image', $albums->first()?->cover_url);

        return view('videos.index', compact('albums', 'videoGalleryBannerUrl', 'videoGalleryTitle', 'videoGalleryDescription'));
    }

    public function show(VideoAlbum $album)
    {
        abort_unless($album->is_active && (! $album->published_at || $album->published_at->isPast()), 404);
        $allVideos = Collection::make($album->videoItems());
        $page = LengthAwarePaginator::resolveCurrentPage();
        $videos = new LengthAwarePaginator($allVideos->forPage($page, 18)->values(), $allVideos->count(), 18, $page, ['path' => request()->url(), 'query' => request()->query()]);
        return view('videos.show', compact('album', 'videos'));
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
