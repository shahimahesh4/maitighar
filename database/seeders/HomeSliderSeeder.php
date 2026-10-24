<?php

namespace Database\Seeders;

use App\Models\HomeSlider;
use Illuminate\Database\Seeder;

class HomeSliderSeeder extends Seeder
{
    public function run(): void
    {
        $slides = [
            [
                'name' => 'Palpali Dhaka Collection',
                'eyebrow' => 'NEPALI HERITAGE, TIMELESS STYLE',
                'title' => 'Palpali Dhaka Collection',
                'subtitle' => 'Wear tradition. Celebrate elegance.',
                'desktop_image' => 'sliders/palpali_dhaka_collection_banner.png',
                'button_label' => 'Shop Now',
                'button_url' => '/products',
                'sort_order' => 1,
                'duration' => 6000,
                'is_active' => true,
            ],
            [
                'name' => 'Palpali Dhaka Kids Collection',
                'eyebrow' => 'LITTLE ONES, PROUD TRADITIONS',
                'title' => 'Palpali Dhaka Kids Collection',
                'subtitle' => 'Little smiles. Timeless Nepali style.',
                'desktop_image' => 'sliders/palpali_dhaka_kids_collection_banner.png',
                'button_label' => 'Shop Kids',
                'button_url' => '/products/palpali-dhaka-baby-boy-pasni-set',
                'sort_order' => 2,
                'duration' => 6000,
                'is_active' => true,
            ],
        ];

        // Clean up outdated placeholder slides
        HomeSlider::whereIn('name', ['Brand introduction', 'Professional training', 'Authentic crafts'])->delete();

        foreach ($slides as $slide) {
            HomeSlider::updateOrCreate(
                ['name' => $slide['name']],
                $slide
            );
        }
    }
}
