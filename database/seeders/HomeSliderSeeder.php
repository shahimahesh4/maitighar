<?php

namespace Database\Seeders;

use App\Models\HomeSlider;
use Illuminate\Database\Seeder;

class HomeSliderSeeder extends Seeder
{
    public function run(): void
    {
        $slides = [
            ['name' => 'Brand introduction', 'eyebrow' => 'Tradition • Elegance • Craft', 'title' => 'Maitighar', 'subtitle' => 'Nepalese Heritage with Love', 'desktop_image' => 'images/kba/beauty-girl.png', 'button_label' => 'Explore Collection', 'button_url' => '/products', 'sort_order' => 1],
            ['name' => 'Professional training', 'eyebrow' => 'Learn • Practice • Grow', 'title' => 'Heritage & Skill Mastery', 'subtitle' => 'Hands-on training and mentorship with experienced professionals', 'desktop_image' => 'images/kba/about-us-academy.png', 'button_label' => 'Explore Training', 'button_url' => '/academy-training', 'sort_order' => 2],
            ['name' => 'Authentic crafts', 'eyebrow' => 'Authentic • Handcrafted • Timeless', 'title' => 'Handcrafted Treasures', 'subtitle' => 'Explore authentic Nepalese bamboo, handicrafts, and cultural creations', 'desktop_image' => 'images/kba/makeup-brush.jpeg', 'button_label' => 'Shop All Products', 'button_url' => '/products', 'sort_order' => 3],
        ];

        foreach ($slides as $slide) {
            HomeSlider::updateOrCreate(
                ['name' => $slide['name']],
                $slide + ['duration' => 6000, 'is_active' => true]
            );
        }
    }
}
