<?php

namespace Database\Seeders;

use App\Models\Slider;
use Illuminate\Database\Seeder;

class SliderSeeder extends Seeder
{
    public function run(): void
    {
        $slider = Slider::updateOrCreate(
            ['code' => 'homepage-hero'],
            [
                'name' => 'Homepage Hero Revolution Slider',
                'description' => 'Revolution Slider banner for the homepage.',
                'is_active' => true,
                'autoplay' => true,
                'autoplay_speed' => 6000,
                'transition_speed' => 800,
                'transition_effect' => 'fade',
                'show_navigation' => true,
                'show_pagination' => true,
                'pause_on_hover' => true,
                'desktop_height' => '650px',
                'tablet_height' => '520px',
                'mobile_height' => '460px',
            ]
        );

        $slider->items()->delete();

        // Slide 1: Animated Content Banner (Revolution Slider style)
        $slider->items()->create([
            'title' => 'Bamboo Coffee Mug with Dual Opening',
            'type' => 'animated_content',
            'sort_order' => 1,
            'is_active' => true,
            'text_content' => [
                'main_title' => 'Bamboo Coffee Mug',
                'second_title' => 'With Dual Opening Lid',
                'font_size' => 'large',
            ],
            'background_settings' => [
                'type' => 'solid',
                'bg_color' => '#ebf6ff',
            ],
            'layer_animations' => [
                'title' => ['effect' => 'fade_up', 'delay' => 0.25, 'duration' => 0.8],
            ],
            'responsive_settings' => [
                'desktop_height' => '650px',
                'tablet_height' => '520px',
                'mobile_height' => '460px',
                'mobile_text_alignment' => 'center',
                'mobile_title_size' => 'text-3xl',
            ],
        ]);

        // Slide 2: Single Image Banner
        $slider->items()->create([
            'title' => 'Palpali Dhaka Heritage Collection',
            'type' => 'single_image',
            'sort_order' => 2,
            'is_active' => true,
            'desktop_image' => '/images/banners/palpali_dhaka_collection_banner.png',
            'link_url' => '/categories',
            'open_in_new_tab' => false,
        ]);
    }
}
