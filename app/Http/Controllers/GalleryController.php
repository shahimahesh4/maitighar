<?php

namespace App\Http\Controllers;

use App\Models\GalleryAlbum;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

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

        return view('gallery.index', compact('albums'));
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
}
