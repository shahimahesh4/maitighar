<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'test@example.com'],
            ['name' => 'Maitighar Admin', 'password' => bcrypt('password'), 'email_verified_at' => now(), 'role' => 'super_admin']
        );

        User::firstOrCreate(
            ['email' => 'customer@example.com'],
            ['name' => 'Demo Customer', 'password' => bcrypt('password'), 'email_verified_at' => now(), 'role' => 'customer']
        );

        $categoryData = [
            ['name' => 'Makeup', 'slug' => 'makeup', 'image' => 'images/kba/makeup-brush.jpeg'],
            ['name' => 'Skin Care', 'slug' => 'skin-care', 'image' => 'images/kba/crack-removal-cream.jpeg'],
            ['name' => 'Hair Removal', 'slug' => 'hair-removal', 'image' => 'images/kba/hair-remover.jpeg'],
        ];

        $categories = [];
        foreach ($categoryData as $data) {
            $categories[$data['slug']] = Category::updateOrCreate(
                ['slug' => $data['slug']],
                ['name' => $data['name'], 'image' => $data['image'], 'is_active' => true]
            );
        }

        $brand = Brand::updateOrCreate(
            ['slug' => 'maitighar'],
            ['name' => 'Maitighar', 'image' => 'images/logo.png', 'is_active' => true]
        );

        $products = [
            [
                'category' => 'makeup',
                'name' => 'Makeup Brush',
                'price' => 650,
                'image' => 'images/kba/makeup-brush.jpeg',
                'sku' => 'KBA-MB-001',
                'description' => 'A versatile premium makeup brush for smooth, even application and a professional finish.',
                'featured' => true,
                'sale' => false,
            ],
            [
                'category' => 'makeup',
                'name' => 'Eye Brushes',
                'price' => 600,
                'image' => 'images/kba/eye-brushes.jpeg',
                'sku' => 'KBA-EB-002',
                'description' => 'A complete eye-brush selection for blending, lining and detailed eye makeup.',
                'featured' => true,
                'sale' => false,
            ],
            [
                'category' => 'makeup',
                'name' => 'Brush for Skin',
                'price' => 400,
                'image' => 'images/kba/skin-brush.jpeg',
                'sku' => 'KBA-SB-003',
                'description' => 'Soft, dense bristles designed for effortless powder and complexion application.',
                'featured' => true,
                'sale' => true,
            ],
            [
                'category' => 'skin-care',
                'name' => 'Crack Removal Cream',
                'price' => 800,
                'image' => 'images/kba/crack-removal-cream.jpeg',
                'sku' => 'KBA-CRC-004',
                'description' => 'Deeply moisturizing care formulated to soften and restore dry, cracked skin.',
                'featured' => true,
                'sale' => true,
            ],
            [
                'category' => 'hair-removal',
                'name' => 'Hair Remover',
                'price' => 850,
                'image' => 'images/kba/hair-remover.jpeg',
                'sku' => 'KBA-HR-005',
                'description' => 'Gentle, convenient hair-removal care for smooth-looking skin at home.',
                'featured' => true,
                'sale' => false,
            ],
            [
                'category' => 'skin-care',
                'name' => 'Facial Serum',
                'price' => 850,
                'image' => 'images/kba/facial-serum.jpeg',
                'sku' => 'KBA-FS-006',
                'description' => 'Lightweight facial serum that hydrates and supports a brighter, refreshed complexion.',
                'featured' => true,
                'sale' => false,
            ],
        ];

        foreach ($products as $data) {
            Product::updateOrCreate(
                ['slug' => Str::slug($data['name'])],
                [
                    'category_id' => $categories[$data['category']]->id,
                    'brand_id' => $brand->id,
                    'name' => $data['name'],
                    'images' => [$data['image']],
                    'description' => $data['description'],
                    'short_description' => $data['description'],
                    'price' => $data['price'],
                    'sku' => $data['sku'],
                    'is_active' => true,
                    'is_featured' => $data['featured'],
                    'in_stock' => true,
                    'on_sale' => $data['sale'],
                    'meta_title' => $data['name'].' | Maitighar',
                    'meta_description' => $data['description'],
                    'meta_keywords' => strtolower($data['name'].', Maitighar, heritage, Nepal'),
                ]
            );
        }

        $this->call([
            HomeSliderSeeder::class,
            KathmanduBeautyWebsiteSeeder::class,
            AboutUsPageSeeder::class,
            PolicyPageSeeder::class,
            GalleryDemoSeeder::class,
            VideoGallerySeeder::class,
            DemoCatalogSeeder::class,
            BambooProductsSeeder::class,
            OperationalDemoSeeder::class,
            PaymentGatewaySeeder::class,
        ]);
    }
}
