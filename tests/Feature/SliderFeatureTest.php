<?php

namespace Tests\Feature;

use App\Livewire\Slider as LivewireSlider;
use App\Models\Slider;
use App\Models\SliderItem;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Livewire\Livewire;
use Tests\TestCase;

class SliderFeatureTest extends TestCase
{
    use DatabaseTransactions;

    public function test_slider_and_slider_items_creation_and_relations(): void
    {
        $slider = Slider::create([
            'name' => 'Hero Banner Slider',
            'code' => 'test-hero-' . uniqid(),
            'description' => 'Test Slider for Revolution Slider functionality',
            'is_active' => true,
            'autoplay' => true,
            'autoplay_speed' => 5000,
            'transition_speed' => 800,
            'transition_effect' => 'fade',
            'show_navigation' => true,
            'show_pagination' => true,
            'pause_on_hover' => true,
            'desktop_height' => '650px',
            'tablet_height' => '500px',
            'mobile_height' => '420px',
        ]);

        // 1. Single Image Banner Slide
        $singleItem = $slider->items()->create([
            'title' => 'Festive Promotion Graphic',
            'type' => 'single_image',
            'sort_order' => 1,
            'is_active' => true,
            'desktop_image' => 'sliders/banner-desktop.png',
            'mobile_image' => 'sliders/banner-mobile.png',
            'link_url' => 'https://example.com/festive-sale',
            'open_in_new_tab' => true,
        ]);

        // 2. Animated Content Banner Slide
        $animatedItem = $slider->items()->create([
            'title' => 'Bamboo Coffee Mug Special',
            'type' => 'animated_content',
            'sort_order' => 2,
            'is_active' => true,
            'text_content' => [
                'main_title' => 'Bamboo Thermal Mug',
                'second_title' => 'Stainless Steel Edition',
            ],
            'background_settings' => [
                'type' => 'gradient',
                'gradient_from' => '#09101d',
                'gradient_to' => '#1e293b',
                'gradient_direction' => '135deg',
            ],
            'layer_animations' => [
                'badge' => ['effect' => 'fade_down', 'delay' => 0.1, 'duration' => 0.6],
                'title' => ['effect' => 'fade_up', 'delay' => 0.25, 'duration' => 0.8],
            ],
        ]);

        $this->assertTrue($singleItem->isSingleImage());
        $this->assertFalse($singleItem->isAnimated());

        $this->assertTrue($animatedItem->isAnimated());
        $this->assertFalse($animatedItem->isSingleImage());

        $this->assertCount(2, $slider->items);
        $this->assertCount(2, $slider->activeItems);
        $this->assertStringContainsString('linear-gradient', $animatedItem->background_style);
    }

    public function test_livewire_slider_component_renders_correctly(): void
    {
        $slider = Slider::where('code', 'homepage-hero')->first();
        $this->assertNotNull($slider, 'Seeded homepage-hero slider should exist');

        Livewire::test(LivewireSlider::class, ['location' => 'homepage-hero'])
            ->assertStatus(200)
            ->assertSee('Bamboo Coffee Mug')
            ->assertSee('rev-slider-container')
            ->assertDontSee('data-layer="title"', false);
    }

    public function test_admin_can_access_slider_management_and_preview(): void
    {
        $this->assertSame('Slider Management', \App\Filament\Resources\SliderResource::getNavigationLabel());
        $this->assertSame('Website', \App\Filament\Resources\SliderResource::getNavigationGroup());

        $admin = User::firstOrCreate(
            ['email' => 'admin-test@maitighar.com'],
            [
                'name' => 'Admin Tester',
                'password' => bcrypt('password123'),
                'is_admin' => true,
                'role' => 'admin',
            ]
        );

        $slider = Slider::where('code', 'homepage-hero')->first();
        $this->assertNotNull($slider);

        $this->actingAs($admin)
            ->get('/stnapanel/sliders')
            ->assertOk();

        $this->actingAs($admin)
            ->get("/stnapanel/sliders/{$slider->id}/edit")
            ->assertOk();

        $this->actingAs($admin)
            ->get("/stnapanel/sliders/{$slider->id}/preview")
            ->assertOk()
            ->assertSee('Bamboo Coffee Mug')
            ->assertSee('rev-slider-container');
    }
}
