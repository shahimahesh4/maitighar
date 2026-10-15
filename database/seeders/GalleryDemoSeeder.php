<?php

namespace Database\Seeders;

use App\Models\GalleryAlbum;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class GalleryDemoSeeder extends Seeder
{
    public function run(): void
    {
        $sources = [
            'academy' => 'images/kba/about-us-academy.png',
            'brushes' => 'images/kba/makeup-brush.jpeg',
            'eyes' => 'images/kba/eye-brushes.jpeg',
            'skin' => 'images/kba/skin-brush.jpeg',
            'serum' => 'images/kba/facial-serum.jpeg',
            'cream' => 'images/kba/crack-removal-cream.jpeg',
            'hair' => 'images/kba/hair-remover.jpeg',
            'beauty' => 'images/kba/beauty-girl.png',
        ];

        $images = [];
        foreach ($sources as $name => $source) {
            $extension = pathinfo($source, PATHINFO_EXTENSION);
            $destination = "gallery/demo/{$name}.{$extension}";
            if (is_file(public_path($source)) && ! Storage::disk('public')->exists($destination)) {
                Storage::disk('public')->put($destination, file_get_contents(public_path($source)));
            }
            if (Storage::disk('public')->exists($destination)) {
                $images[$name] = $destination;
            }
        }

        $albums = [
            ['Maitighar Heritage Collection', 'academy-life-training', 'A glimpse into artisan craftsmanship, authentic traditions, and heritage collections at Maitighar.', ['academy', 'eyes', 'brushes', 'skin', 'beauty'], 1],
            ['Traditional Handcrafts & Art', 'professional-beauty-work', 'Explore traditional artistry, exquisite handcrafted garments, jewelry, and ethnic lifestyle collections.', ['beauty', 'brushes', 'eyes', 'skin', 'serum'], 2],
            ['Maitighar Living & Essentials', 'products-studio-essentials', 'A curated look at ethnic lifestyle, home decor, traditional accessories, and cultural collections.', ['serum', 'cream', 'hair', 'brushes', 'eyes', 'skin'], 3],
        ];

        foreach ($albums as [$title, $slug, $description, $keys, $sortOrder]) {
            $albumImages = collect($keys)->map(fn (string $key) => $images[$key] ?? null)->filter()->values()->all();
            GalleryAlbum::updateOrCreate(['slug' => $slug], [
                'title' => $title,
                'description' => $description,
                'cover_image' => $albumImages[0] ?? null,
                'images' => $albumImages,
                'is_active' => true,
                'sort_order' => $sortOrder,
                'published_at' => now(),
            ]);
        }
    }
}
