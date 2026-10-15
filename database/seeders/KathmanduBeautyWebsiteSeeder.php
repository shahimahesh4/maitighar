<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Page;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class KathmanduBeautyWebsiteSeeder extends Seeder
{
    public function run(): void
    {
        $brand = Brand::updateOrCreate(
            ['slug' => 'maitighar'],
            ['name' => 'Maitighar', 'image' => 'images/logo.png', 'is_active' => true]
        );

        $categories = collect([
            ['name' => 'Makeup', 'slug' => 'makeup', 'image' => 'images/kba/makeup-brush.jpeg'],
            ['name' => 'Skin Care', 'slug' => 'skin-care', 'image' => 'images/kba/facial-serum.jpeg'],
            ['name' => 'Hair Removal', 'slug' => 'hair-removal', 'image' => 'images/kba/hair-remover.jpeg'],
        ])->mapWithKeys(function (array $data) {
            $category = Category::updateOrCreate(
                ['slug' => $data['slug']],
                ['name' => $data['name'], 'image' => $data['image'], 'is_active' => true]
            );

            return [$data['slug'] => $category];
        });

        $products = [
            ['Makeup Brush', 'makeup', 650, 'makeup-brush.jpeg', 'KBA-MB-001', 'A versatile premium makeup brush for smooth, even application and a professional finish.'],
            ['Eye Brushes', 'makeup', 600, 'eye-brushes.jpeg', 'KBA-EB-002', 'A complete eye-brush selection for blending, lining, and detailed eye makeup.'],
            ['Brush for Skin', 'makeup', 400, 'skin-brush.jpeg', 'KBA-SB-003', 'Soft, dense bristles designed for effortless powder and complexion application.'],
            ['Crack Removal Cream', 'skin-care', 800, 'crack-removal-cream.jpeg', 'KBA-CRC-004', 'Deeply moisturizing care formulated to soften and restore dry, cracked skin.'],
            ['Hair Remover', 'hair-removal', 850, 'hair-remover.jpeg', 'KBA-HR-005', 'Gentle, convenient hair-removal care for smooth-looking skin at home.'],
            ['Facial Serum', 'skin-care', 850, 'facial-serum.jpeg', 'KBA-FS-006', 'A lightweight facial serum that hydrates and supports a brighter, refreshed complexion.'],
        ];

        foreach ($products as [$name, $category, $price, $file, $sku, $description]) {
            $image = 'images/kba/'.$file;
            Product::updateOrCreate(
                ['slug' => Str::slug($name)],
                [
                    'category_id' => $categories[$category]->id,
                    'brand_id' => $brand->id,
                    'name' => $name,
                    'images' => [$image],
                    'main_image' => $image,
                    'gallery_images' => [],
                    'description' => '<p>'.e($description).'</p>',
                    'short_description' => $description,
                    'price' => $price,
                    'sku' => $sku,
                    'is_active' => true,
                    'is_featured' => true,
                    'in_stock' => true,
                    'on_sale' => false,
                    'meta_title' => $name.' | Maitighar',
                    'meta_description' => $description,
                    'meta_keywords' => strtolower($name.', Maitighar, beauty products, Nepal'),
                ]
            );
        }

        
        $pages = [
            [
                'About Us',
                'about-us',
                '<p>Maitighar celebrates Nepalese Heritage with Love, bringing you timeless traditions, refined craftsmanship, and dedicated care.</p><p>Our collections and services combine authentic techniques with heartfelt dedication, creating memorable experiences for our valued clients and students.</p>',
                'images/kba/beauty-girl.png',
            ],
            [
                'Academy Training',
                'academy-training',
                '<h2>Maitighar Training & Services</h2><ul><li>Professional beauty and heritage skill training</li><li>Safe and well-equipped arrangements for learners</li><li>Direct guidance from experienced mentors</li><li>Certificate of completion</li><li>Portfolio development</li><li>Career preparation and counselling</li></ul>',
                'images/logo.png',
            ],
            [
                'Videos',
                'videos',
                '<p>Training, heritage, beauty-service, and special moments from Maitighar will be published here.</p>',
                'images/kba/beauty-girl.png',
            ],
            [
                'Contact Us',
                'contact-us',
                '<p><strong>Address:</strong> 2/75 Dartbrook Road, Auburn NSW, Sydney, NSW 2144</p><p><strong>Email:</strong> maitigharaustralia@gmail.com</p><p><strong>Phone:</strong> +61 432 685 546</p>',
                'images/logo.png',
            ],
        ];

        foreach ($pages as $sortOrder => [$title, $slug, $content, $image]) {
            Page::updateOrCreate(
                ['slug' => $slug],
                [
                    'title' => $title,
                    'content' => $content,
                    'thumbnail_image' => $image,
                    'banner_image' => $image,
                    'meta_title' => $title.' | Maitighar',
                    'meta_description' => match ($slug) {
                        'about-us' => 'Meet Maitighar—Nepalese Heritage with Love, professional services, authentic craftsmanship, and dedicated mentorship.',
                        'academy-training' => 'Explore hands-on beauty and heritage training with experienced trainers, professional certification, and career guidance at Maitighar.',
                        'videos' => 'Watch training, heritage, beauty-service, and special event videos from Maitighar.',
                        'contact-us' => 'Contact Maitighar for courses, salon services, appointments, and product enquiries.',
                        default => Str::limit(trim(preg_replace('/\s+/', ' ', strip_tags($content))), 155),
                    },
                    'meta_keywords' => strtolower($title.', Maitighar, Nepalese Heritage with Love, Bhairahawa, Nepal'),
                    'status' => 'published',
                    'sort_order' => $sortOrder + 1,
                ]
            );
        }
    }
}
