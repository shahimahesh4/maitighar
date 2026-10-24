<?php

namespace Tests\Feature;

use App\Models\GalleryAlbum;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class GalleryFrontendTest extends TestCase
{
    use DatabaseTransactions;

    public function test_published_albums_are_visible_in_gallery(): void
    {
        GalleryAlbum::create([
            'title' => 'Student Work', 'slug' => 'student-work',
            'description' => 'Our latest student work.',
            'images' => ['https://example.com/one.jpg'], 'is_active' => true,
        ]);
        GalleryAlbum::create([
            'title' => 'Hidden Album', 'slug' => 'hidden-album',
            'images' => [], 'is_active' => false,
        ]);

        $this->get('/gallery')
            ->assertOk()
            ->assertSee('Student Work')
            ->assertDontSee('Hidden Album');
    }

    public function test_album_displays_thirty_images_per_page(): void
    {
        $album = GalleryAlbum::create([
            'title' => 'Academy Moments', 'slug' => 'academy-moments',
            'images' => collect(range(1, 35))->map(fn (int $number) => "https://example.com/photo-{$number}.jpg")->all(),
            'is_active' => true,
        ]);

        $response = $this->get(route('gallery.show', $album));

        $response->assertOk()->assertViewHas('images', fn ($images) => $images->count() === 30)
            ->assertSee('photo-30.jpg')->assertDontSee('photo-31.jpg');
        $this->get(route('gallery.show', [$album, 'page' => 2]))
            ->assertOk()->assertSee('photo-31.jpg')->assertSee('photo-35.jpg');
    }
}
