<?php
namespace Database\Seeders;
use App\Models\VideoAlbum;
use Illuminate\Database\Seeder;
class VideoGallerySeeder extends Seeder
{
    public function run(): void
    {
        VideoAlbum::updateOrCreate(['slug' => 'academy-training-videos'], [
            'title' => 'Maitighar Showcase Videos', 'description' => 'Watch practical demonstrations, artisan stories, and authentic cultural showcases from Maitighar.', 'cover_image' => 'images/maitighar/logo.png', 'sort_order' => 1, 'is_active' => true, 'published_at' => now(),
            'videos' => [
                ['title' => 'Maitighar Artisan Crafting', 'type' => 'youtube', 'youtube_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ'],
                ['title' => 'Maitighar Heritage Highlights', 'type' => 'youtube', 'youtube_url' => 'https://www.youtube.com/watch?v=ysz5S6PUM-U'],
                ['title' => 'Authentic Nepalese Tradition', 'type' => 'youtube', 'youtube_url' => 'https://www.youtube.com/watch?v=jNQXAC9IVRw'],
            ],
        ]);
    }
}
