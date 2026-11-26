<?php

namespace Tests\Feature;

use App\Models\Category;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class CategorySlugRouteTest extends TestCase
{
    use DatabaseTransactions;

    public function test_active_category_is_available_at_its_slug_url(): void
    {
        $category = Category::factory()->create([
            'name' => 'Handmade Sarees',
            'slug' => 'handmade-sarees',
            'is_active' => true,
        ]);

        $response = $this->get(route('product-category.products', $category));

        $response->assertOk();
        $response->assertSee('Handmade Sarees');
    }
}
