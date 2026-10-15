<?php

namespace Database\Factories;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        $name = fake()->unique()->words(3, true);

        return [
            'category_id' => Category::factory(),
            'brand_id' => Brand::factory(),
            'name' => ucfirst($name),
            'slug' => Str::slug($name),
            'images' => [],
            'main_image' => null,
            'gallery_images' => [],
            'description' => fake()->paragraphs(3, true),
            'short_description' => fake()->sentence(),
            'price' => fake()->randomFloat(2, 10, 1000),
            'old_price' => fake()->optional(0.35)->randomFloat(2, 1001, 1400),
            'specifications' => [
                ['label' => 'Suitable for', 'value' => fake()->randomElement(['All skin types', 'Professional use', 'Daily use'])],
                ['label' => 'Country of origin', 'value' => 'Nepal'],
            ],
            'delivery_policy' => 'Orders are normally delivered within 3–5 business days inside Nepal.',
            'shipping_returns' => 'Unopened products may be returned within 7 days of delivery. Delivery charges are non-refundable.',
            'custom_tab_title' => 'Beauty Tips',
            'custom_tab_content' => fake()->paragraph(),
            'product_tabs' => [
                ['title' => 'How to Use', 'content' => '<p>Follow the package directions and use clean beauty tools.</p>'],
            ],
            'sku' => strtoupper(fake()->bothify('???-#####')),
            'is_active' => true,
            'is_featured' => fake()->boolean(20),
            'in_stock' => true,
            'on_sale' => fake()->boolean(30),
            'meta_title' => ucfirst($name),
            'meta_description' => fake()->sentence(),
            'meta_keywords' => implode(',', fake()->words(5)),
        ];
    }
}
