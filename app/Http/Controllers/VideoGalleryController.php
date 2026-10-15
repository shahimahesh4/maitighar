<?php

namespace App\Http\Controllers;

use App\Models\VideoAlbum;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

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

        return view('videos.index', compact('albums'));
    }

    public function show(VideoAlbum $album)
    {
        abort_unless($album->is_active && (! $album->published_at || $album->published_at->isPast()), 404);
        $allVideos = Collection::make($album->videoItems());
        $page = LengthAwarePaginator::resolveCurrentPage();
        $videos = new LengthAwarePaginator($allVideos->forPage($page, 18)->values(), $allVideos->count(), 18, $page, ['path' => request()->url(), 'query' => request()->query()]);
        return view('videos.show', compact('album', 'videos'));
    }
}
