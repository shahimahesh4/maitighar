<?php

namespace App\Http\Controllers;

use App\Models\GalleryAlbum;
use App\Models\VideoAlbum;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;

class GalleryApiController extends Controller
{
    public function photoAlbums(): JsonResponse
    {
        $albums = GalleryAlbum::published()->paginate(12)->through(fn (GalleryAlbum $album): array => $this->photoAlbumData($album));

        return response()->json($albums);
    }

    public function photoAlbum(string $slug): JsonResponse
    {
        $album = GalleryAlbum::published()->where('slug', $slug)->firstOrFail();

        return response()->json([
            'data' => $this->photoAlbumData($album, true),
        ]);
    }

    public function videoAlbums(): JsonResponse
    {
        $albums = VideoAlbum::published()->paginate(12)->through(fn (VideoAlbum $album): array => $this->videoAlbumData($album));

        return response()->json($albums);
    }

    public function videoAlbum(string $slug): JsonResponse
    {
        $album = VideoAlbum::published()->where('slug', $slug)->firstOrFail();

        return response()->json([
            'data' => $this->videoAlbumData($album, true),
        ]);
    }

    private function photoAlbumData(GalleryAlbum $album, bool $includeImages = false): array
    {
        return [
            'id' => $album->id,
            'title' => $album->title,
            'slug' => $album->slug,
            'description' => $album->description,
            'cover_url' => $album->cover_url,
            'photo_count' => $album->image_count,
            'published_at' => $album->published_at?->toAtomString(),
            ...($includeImages ? ['photos' => $album->imageItems()] : []),
        ];
    }

    private function videoAlbumData(VideoAlbum $album, bool $includeVideos = false): array
    {
        $videos = Collection::make($album->videoItems());

        return [
            'id' => $album->id,
            'title' => $album->title,
            'slug' => $album->slug,
            'description' => $album->description,
            'cover_url' => $album->cover_url,
            'video_count' => $videos->count(),
            'published_at' => $album->published_at?->toAtomString(),
            ...($includeVideos ? ['videos' => $videos->all()] : []),
        ];
    }
}
