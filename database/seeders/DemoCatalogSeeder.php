<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductFaq;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DemoCatalogSeeder extends Seeder
{
    public function run(): void
    {
        $customer = User::firstOrCreate(
            ['email' => 'customer@example.com'],
            [
                'name' => 'Demo Customer',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
                'role' => 'customer',
            ]
        );

        $categories = [];
        foreach ([
            ['name' => 'Makeup', 'slug' => 'makeup', 'image' => 'images/kba/makeup-brush.jpeg'],
            ['name' => 'Skin Care', 'slug' => 'skin-care', 'image' => 'images/kba/crack-removal-cream.jpeg'],
            ['name' => 'Hair Removal', 'slug' => 'hair-removal', 'image' => 'images/kba/hair-remover.jpeg'],
            ['name' => 'Hair Care', 'slug' => 'hair-care', 'image' => 'images/kba/facial-serum.jpeg'],
            ['name' => 'Nail Care', 'slug' => 'nail-care', 'image' => 'images/kba/crack-removal-cream.jpeg'],
            ['name' => 'Beauty Tools', 'slug' => 'beauty-tools', 'image' => 'images/kba/eye-brushes.jpeg'],
        ] as $data) {
            $categories[$data['slug']] = Category::updateOrCreate(
                ['slug' => $data['slug']],
                ['name' => $data['name'], 'image' => $data['image'], 'is_active' => true]
            );
        }

        $brands = [];
        foreach ([
            ['name' => 'Maitighar', 'slug' => 'maitighar'],
            ['name' => 'Himalayan Glow', 'slug' => 'himalayan-glow'],
            ['name' => 'Nepal Heritage Pro', 'slug' => 'nepal-heritage-pro'],
        ] as $data) {
            $brands[$data['slug']] = Brand::updateOrCreate(
                ['slug' => $data['slug']],
                ['name' => $data['name'], 'image' => 'images/logo.png', 'is_active' => true]
            );
        }

        $products = [
            ['category' => 'skin-care', 'brand' => 'himalayan-glow', 'name' => 'Vitamin C Glow Serum', 'price' => 1200, 'image' => 'images/kba/facial-serum.jpeg', 'sku' => 'HG-VCS-007', 'description' => 'A lightweight vitamin C serum that helps brighten dull skin and supports an even-looking complexion.', 'featured' => true, 'sale' => true],
            ['category' => 'skin-care', 'brand' => 'himalayan-glow', 'name' => 'Hydrating Face Cream', 'price' => 950, 'image' => 'images/kba/crack-removal-cream.jpeg', 'sku' => 'HG-HFC-008', 'description' => 'A rich daily moisturizer made for lasting hydration and soft, comfortable skin in Nepal’s changing climate.', 'featured' => true, 'sale' => false],
            ['category' => 'makeup', 'brand' => 'nepal-heritage-pro', 'name' => 'Professional Foundation Brush', 'price' => 725, 'image' => 'images/kba/skin-brush.jpeg', 'sku' => 'NBP-PFB-009', 'description' => 'A dense professional brush for seamless liquid and cream foundation application without streaks.', 'featured' => false, 'sale' => true],
            ['category' => 'beauty-tools', 'brand' => 'nepal-heritage-pro', 'name' => 'Complete Makeup Brush Set', 'price' => 1850, 'image' => 'images/kba/makeup-brush.jpeg', 'sku' => 'NBP-CBS-010', 'description' => 'A complete face and eye brush collection for students, working artists, and everyday beauty routines.', 'featured' => true, 'sale' => true],
            ['category' => 'beauty-tools', 'brand' => 'nepal-heritage-pro', 'name' => 'Precision Eye Brush Kit', 'price' => 1100, 'image' => 'images/kba/eye-brushes.jpeg', 'sku' => 'NBP-PEB-011', 'description' => 'Precision eye brushes for blending, shading, liner work, brows, and detailed professional looks.', 'featured' => false, 'sale' => false],
            ['category' => 'hair-care', 'brand' => 'himalayan-glow', 'name' => 'Nourishing Hair Serum', 'price' => 875, 'image' => 'images/kba/facial-serum.jpeg', 'sku' => 'HG-NHS-012', 'description' => 'A non-greasy finishing serum that smooths flyaways, adds shine, and helps protect dry hair ends.', 'featured' => true, 'sale' => false],
            ['category' => 'hair-removal', 'brand' => 'maitighar', 'name' => 'Salon Hair Removal Cream', 'price' => 775, 'image' => 'images/kba/hair-remover.jpeg', 'sku' => 'MG-AUS-013', 'description' => 'An easy-to-use hair removal cream designed for a smooth finish with a gentle, salon-inspired formula.', 'featured' => false, 'sale' => true],
            ['category' => 'nail-care', 'brand' => 'maitighar', 'name' => 'Nail and Cuticle Repair Cream', 'price' => 625, 'image' => 'images/kba/crack-removal-cream.jpeg', 'sku' => 'MG-AUS-014', 'description' => 'A conditioning cream for dry cuticles and brittle-looking nails, ideal for home and salon aftercare.', 'featured' => false, 'sale' => false],
            ['category' => 'makeup', 'brand' => 'himalayan-glow', 'name' => 'Radiance Makeup Primer', 'price' => 1050, 'image' => 'images/kba/facial-serum.jpeg', 'sku' => 'HG-RMP-015', 'description' => 'A silky makeup primer that smooths the look of skin and creates a radiant base for longer-lasting makeup.', 'featured' => true, 'sale' => true],
            ['category' => 'beauty-tools', 'brand' => 'maitighar', 'name' => 'Soft Kabuki Face Brush', 'price' => 550, 'image' => 'images/kba/skin-brush.jpeg', 'sku' => 'MG-AUS-016', 'description' => 'A compact, ultra-soft kabuki brush for powder, bronzer, blush, and quick touch-ups on the go.', 'featured' => false, 'sale' => false],
        ];

        foreach ($products as $index => $data) {
            $product = Product::updateOrCreate(
                ['slug' => Str::slug($data['name'])],
                [
                    'category_id' => $categories[$data['category']]->id,
                    'brand_id' => $brands[$data['brand']]->id,
                    'name' => $data['name'],
                    'images' => [$data['image']],
                    'main_image' => $data['image'],
                    'gallery_images' => [$data['image'], 'images/kba/eye-brushes.jpeg'],
                    'description' => $data['description'],
                    'short_description' => $data['description'],
                    'price' => $data['price'],
                    'old_price' => $data['sale'] ? round($data['price'] * 1.2, 2) : null,
                    'specifications' => [
                        ['label' => 'Brand', 'value' => $brands[$data['brand']]->name],
                        ['label' => 'Category', 'value' => $categories[$data['category']]->name],
                        ['label' => 'Suitable for', 'value' => 'Everyday and professional use'],
                        ['label' => 'Country of origin', 'value' => 'Nepal'],
                    ],
                    'delivery_policy' => 'Delivery is available throughout Nepal. Orders are usually delivered within 3–5 business days, depending on the destination.',
                    'shipping_returns' => 'Unopened and unused products may be returned within 7 days of delivery. Please contact us before returning an item. Delivery fees are non-refundable.',
                    'custom_tab_title' => 'Beauty Tips',
                    'custom_tab_content' => 'For the best result, follow the directions on the package, use clean tools, and store the product in a cool, dry place away from direct sunlight.',
                    'product_tabs' => [
                        ['title' => 'Delivery Policy', 'content' => '<p>Delivery is available throughout Nepal. Orders are usually delivered within 3-5 business days, depending on the destination.</p>'],
                        ['title' => 'Shipping & Returns', 'content' => '<p>Unopened and unused products may be returned within 7 days of delivery. Please contact us before returning an item. Delivery fees are non-refundable.</p>'],
                        ['title' => 'Beauty Tips', 'content' => '<p>For the best result, follow the directions on the package, use clean tools, and store the product in a cool, dry place away from direct sunlight.</p>'],
                    ],
                    'sku' => $data['sku'],
                    'is_active' => true,
                    'is_featured' => $data['featured'],
                    'in_stock' => true,
                    'on_sale' => $data['sale'],
                    'meta_title' => $data['name'].' | Maitighar',
                    'meta_description' => $data['description'],
                    'meta_keywords' => strtolower($data['name'].', Maitighar, Nepal products'),
                ]
            );

            ProductFaq::updateOrCreate(
                ['product_id' => $product->id, 'question' => 'Is this product suitable for everyday use?'],
                ['answer' => 'Yes. Follow the product directions and perform a patch test first if you have sensitive skin.', 'is_active' => true]
            );

            Review::updateOrCreate(
                ['product_id' => $product->id, 'user_id' => $customer->id],
                [
                    'rating' => 4 + ($index % 2),
                    'comment' => $index % 2 === 0
                        ? 'Good quality and nicely packaged. It worked well in my daily beauty routine.'
                        : 'Excellent value and easy to use. I would purchase this product again.',
                    'is_approved' => true,
                ]
            );
        }
    }
}
