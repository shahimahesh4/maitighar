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
            ['name' => 'Heritage Tools', 'slug' => 'heritage-tools', 'image' => 'images/kba/skin-brush.jpeg'],
        ])->mapWithKeys(function (array $data) {
            $category = Category::updateOrCreate(
                ['slug' => $data['slug']],
                ['name' => $data['name'], 'image' => $data['image'], 'is_active' => true]
            );

            return [$data['slug'] => $category];
        });

        $products = [
            ['Bamboo Stainless Steel Bottle -500ML', 'skin-care', 22.00, 'makeup-brush.jpeg', 'MG-AUS-001', 'A versatile premium bamboo stainless steel bottle with double-wall insulation.'],
            ['Bamboo Clicker Pen', 'makeup', 18.50, 'eye-brushes.jpeg', 'MG-AUS-002', 'Eco-friendly smooth writing bamboo clicker pen handcrafted from seasoned bamboo.'],
            ['Handle Bamboo Baskets', 'heritage-tools', 15.00, 'skin-brush.jpeg', 'MG-AUS-003', 'Handwoven natural bamboo basket with sturdy handle for home storage and gifting.'],
            ['Bamboo Bookmarks with Design 15 cm', 'skin-care', 26.00, 'crack-removal-cream.jpeg', 'MG-AUS-004', 'Laser-engraved natural bamboo bookmarks featuring traditional Nepali art motifs.'],
            ['Hair Remover', 'hair-removal', 28.50, 'hair-remover.jpeg', 'MG-AUS-005', 'Gentle, convenient hair-removal care for smooth-looking skin at home.'],
            ['Facial Serum', 'skin-care', 34.00, 'facial-serum.jpeg', 'MG-AUS-006', 'A lightweight facial serum that hydrates and supports a brighter, refreshed complexion.'],
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
                    'sale_price' => $price,
                    'regular_price' => round($price * 1.25, 2),
                    'sku' => $sku,
                    'is_active' => true,
                    'is_featured' => true,
                    'in_stock' => true,
                    'on_sale' => false,
                    'meta_title' => $name.' | Maitighar',
                    'meta_description' => $description,
                    'meta_keywords' => strtolower($name.', Maitighar, heritage products, Australia'),
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
                'Heritage Experiences',
                'academy-training',
                '<h2>Maitighar Experiences & Services</h2><ul><li>Professional heritage skill development</li><li>Safe and well-equipped arrangements for learners</li><li>Direct guidance from experienced mentors</li><li>Recognition of participation</li><li>Portfolio development</li><li>Career preparation and counselling</li></ul>',
                'images/logo.png',
            ],
            [
                'Videos',
                'videos',
                '<p>Heritage, personal care, and special moments from Maitighar will be published here.</p>',
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
                        'academy-training' => 'Explore hands-on heritage learning with experienced mentors, recognition of participation, and career guidance at Maitighar.',
                        'videos' => 'Watch heritage, personal-care, and special event videos from Maitighar.',
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
