<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BambooProductsSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create or retrieve Brand
        $brand = Brand::firstOrCreate(
            ['slug' => 'the-bamboo-bazar'],
            [
                'name' => 'The Bamboo Bazar',
                'image' => 'images/logo.png',
                'is_active' => true,
            ]
        );

        // Also ensure Maitighar default brand exists
        Brand::firstOrCreate(
            ['slug' => 'maitighar'],
            [
                'name' => 'Maitighar',
                'image' => 'images/logo.png',
                'is_active' => true,
            ]
        );

        // 2. Categories
        $categoriesData = [
            [
                'name' => 'Bamboo Furniture',
                'slug' => 'bamboo-furniture',
                'image' => 'images/products/bamboo-rocking-chair.jpg',
            ],
            [
                'name' => 'Drinkware & Bottles',
                'slug' => 'drinkware-bottles',
                'image' => 'images/products/bamboo-stanley-tumbler.jpeg',
            ],
            [
                'name' => 'Eco Living & Personal Care',
                'slug' => 'eco-living-personal-care',
                'image' => 'images/products/bamboo-toothbrush-pack.jpeg',
            ],
            [
                'name' => 'Handicrafts & Baskets',
                'slug' => 'handicrafts-baskets',
                'image' => 'images/products/bamboo-basket-handle.jpg',
            ],
            [
                'name' => 'Custom Engravings',
                'slug' => 'custom-engravings',
                'image' => 'images/products/bamboo-engraved-board.jpeg',
            ],
        ];

        $categories = [];
        foreach ($categoriesData as $cat) {
            $categories[$cat['slug']] = Category::updateOrCreate(
                ['slug' => $cat['slug']],
                [
                    'name' => $cat['name'],
                    'image' => $cat['image'],
                    'is_active' => true,
                ]
            );
        }

        // 3. The 6 Demo Products
        $demoProducts = [
            [
                'name' => 'Premium Foldable Bamboo Rocking Chair with Recliner',
                'slug' => 'premium-foldable-bamboo-rocking-chair-with-recliner',
                'category_slug' => 'bamboo-furniture',
                'sku' => 'TBB-RC-001',
                'price' => 25000,
                'regular_price' => 28000,
                'sale_price' => 25000,
                'image' => 'images/products/bamboo-rocking-chair.jpg',
                'short_description' => 'Handcrafted foldable bamboo rocking chair featuring an ergonomic reclining mechanism for indoor and outdoor relaxation.',
                'description' => '<p>Experience ultimate comfort and authentic craftsmanship with the <strong>Premium Foldable Bamboo Rocking Chair with Recliner</strong> from The Bamboo Bazar. Handcrafted from 100% seasoned natural bamboo, this chair offers an ergonomic multi-position reclining mechanism that supports your back and neck perfectly.</p><p>Features a foldable space-saving design, ultra-smooth lacquer finish, and reinforced load-bearing joints capable of supporting up to 150 kg. Perfect for your living room, balcony, garden, or reading nook.</p>',
                'specifications' => [
                    'Material' => '100% Seasoned Natural Bamboo',
                    'Feature' => 'Multi-angle Reclining & Foldable',
                    'Weight Capacity' => 'Up to 150 kg',
                    'Usage' => 'Living Room, Balcony, Garden, Veranda',
                    'Origin' => 'Handcrafted in Nepal',
                ],
                'is_featured' => true,
                'on_sale' => true,
            ],
            [
                'name' => 'Bamboo Stanley High Quality Tumbler',
                'slug' => 'bamboo-stanley-high-quality-tumbler',
                'category_slug' => 'drinkware-bottles',
                'sku' => 'TBB-ST-002',
                'price' => 2000,
                'regular_price' => 2400,
                'sale_price' => 2000,
                'image' => 'images/products/bamboo-stanley-tumbler.jpeg',
                'short_description' => 'Eco-friendly insulated bamboo Stanley-style tumbler with ergonomic handle and stainless steel interior.',
                'description' => '<p>The <strong>Bamboo Stanley High Quality Tumbler</strong> combines iconic modern design with sustainable natural materials. Featuring a genuine bamboo outer wrap with food-grade 304 double-wall vacuum stainless steel interior, it keeps your coffee piping hot for up to 8 hours or your cold drinks chilled for up to 18 hours.</p><p>Comes with a 2-in-1 spill-resistant lid with straw access, comfortable ergonomic grip handle, and car cup holder compatible tapered base.</p>',
                'specifications' => [
                    'Material' => 'Natural Bamboo Exterior & Food-Grade 304 Stainless Steel',
                    'Capacity' => '900 ml / 30 oz',
                    'Insulation' => 'Double-Wall Vacuum Insulation (Hot: 8h, Cold: 18h)',
                    'Lid' => 'BPA-Free 3-Way Rotating Lid with Straw',
                    'Eco Feature' => 'Plastic-free alternative for daily hydration',
                ],
                'is_featured' => true,
                'on_sale' => true,
            ],
            [
                'name' => 'Bamboo LED Smart Sipper Bottle 450ml',
                'slug' => 'bamboo-led-smart-sipper-bottle-450ml',
                'category_slug' => 'drinkware-bottles',
                'sku' => 'TBB-LED-003',
                'price' => 950,
                'regular_price' => 1200,
                'sale_price' => 950,
                'image' => 'images/products/bamboo-led-sipper.jpeg',
                'short_description' => 'Smart 450ml bamboo thermos flask with real-time LED touch temperature display and removable tea strainer.',
                'description' => '<p>Keep track of your beverage temperature at a single touch with the <strong>Bamboo LED Smart Sipper Bottle 450ml</strong>. Features a waterproof intelligent LED display built into the cap that indicates temperature in Celsius instantly.</p><p>Crafted with a sleek natural bamboo casing and rust-proof 304 stainless steel interior. Includes a detachable stainless steel tea infuser filter, making it ideal for brewing loose leaf green tea, herbal infusions, or fresh coffee on the go.</p>',
                'specifications' => [
                    'Material' => 'Natural Bamboo Casing, 304 Stainless Steel Interior',
                    'Capacity' => '450 ml',
                    'Display' => 'Touch-Activated LED Temperature Screen',
                    'Accessories' => 'Detachable 304 Stainless Steel Tea Infuser Basket',
                    'Battery Life' => 'Over 50,000 touches without battery replacement',
                ],
                'is_featured' => true,
                'on_sale' => true,
            ],
            [
                'name' => 'Bamboo Toothbrush Family Pack (Set of 5)',
                'slug' => 'bamboo-toothbrush-family-pack-set-of-5',
                'category_slug' => 'eco-living-personal-care',
                'sku' => 'TBB-TB-004',
                'price' => 400,
                'regular_price' => 500,
                'sale_price' => 400,
                'image' => 'images/products/bamboo-toothbrush-pack.jpeg',
                'short_description' => '100% biodegradable organic bamboo toothbrushes with charcoal-infused soft bristles (Pack of 5).',
                'description' => '<p>Make a zero-waste switch for your entire household with the <strong>Bamboo Toothbrush Family Pack</strong>. Each toothbrush is carved from sustainably harvested organic bamboo, which is naturally antibacterial, water-resistant, and 100% biodegradable.</p><p>Fitted with soft, charcoal-infused wave bristles that effectively absorb plaque, remove surface stains, and freshen breath while remaining gentle on tooth enamel and gums.</p>',
                'specifications' => [
                    'Quantity' => 'Set of 5 Individual Toothbrushes',
                    'Handle' => '100% Biodegradable & Compostable Moso Bamboo',
                    'Bristles' => 'Charcoal-Infused Soft BPA-Free Bristles',
                    'Packaging' => 'Plastic-Free Recyclable Kraft Paper Box',
                    'Eco Benefit' => '100% Zero Plastic Waste',
                ],
                'is_featured' => true,
                'on_sale' => true,
            ],
            [
                'name' => '12-Inch Handwoven Bamboo Basket with Handle',
                'slug' => '12-inch-handwoven-bamboo-basket-with-handle',
                'category_slug' => 'handicrafts-baskets',
                'sku' => 'TBB-BB-005',
                'price' => 400,
                'regular_price' => 550,
                'sale_price' => 400,
                'image' => 'images/products/bamboo-basket-handle.jpg',
                'short_description' => 'Authentic traditional handwoven Nepali bamboo basket with a sturdy arched handle.',
                'description' => '<p>Add rustic elegance and organic charm to your home with the <strong>12-Inch Handwoven Bamboo Basket with Handle</strong>. Artfully woven by skilled traditional Nepali artisans using durable bamboo strips, this basket is as functional as it is beautiful.</p><p>Features a strong arched carrying handle and a reinforced base. Perfect as a fresh fruit display on your dining table, a pantry organizer, a picnic hamper, or an eco-friendly gift basket for festive occasions.</p>',
                'specifications' => [
                    'Dimensions' => '12 Inches Diameter (approx. 30 cm)',
                    'Material' => '100% Natural Bamboo Cane Strips',
                    'Craft' => 'Traditional Handwoven Artisanal Craftsmanship',
                    'Features' => 'Reinforced Sturdy Carrying Handle',
                    'Care' => 'Wipe clean with a damp cloth and air dry',
                ],
                'is_featured' => true,
                'on_sale' => true,
            ],
            [
                'name' => 'Customized Bamboo Engraved Wooden Board (A4 Size)',
                'slug' => 'customized-bamboo-engraved-wooden-board-a4',
                'category_slug' => 'custom-engravings',
                'sku' => 'TBB-EB-006',
                'price' => 2500,
                'regular_price' => 3000,
                'sale_price' => 2500,
                'image' => 'images/products/bamboo-engraved-board.jpeg',
                'short_description' => 'Precision laser-engraved solid natural bamboo wooden board in standard A4 size.',
                'description' => '<p>Commemorate special moments or elevate your culinary presentation with the <strong>Customized Bamboo Engraved Wooden Board (A4 Size)</strong>. Constructed from cross-laminated organic bamboo that provides superior warp resistance and knife-friendly durability.</p><p>Customizable with high-definition laser engraving for personal names, family crests, anniversary dates, corporate logos, or bespoke artistic illustrations. Finished with organic food-safe mineral oil.</p>',
                'specifications' => [
                    'Dimensions' => 'A4 Size (approx. 21 cm x 29.7 cm)',
                    'Thickness' => '1.5 cm Solid Multi-ply Bamboo',
                    'Engraving' => 'Precision Deep Laser Etched',
                    'Finish' => 'Pre-seasoned with 100% Food-Grade Natural Oil',
                    'Usage' => 'Memorial Plaque, Decorative Wall Sign, Charcuterie Board',
                ],
                'is_featured' => true,
                'on_sale' => true,
            ],
        ];

        foreach ($demoProducts as $prod) {
            $cat = $categories[$prod['category_slug']];
            Product::updateOrCreate(
                ['slug' => $prod['slug']],
                [
                    'category_id' => $cat->id,
                    'brand_id' => $brand->id,
                    'name' => $prod['name'],
                    'sku' => $prod['sku'],
                    'price' => $prod['price'],
                    'regular_price' => $prod['regular_price'],
                    'sale_price' => $prod['sale_price'],
                    'old_price' => $prod['regular_price'],
                    'main_image' => $prod['image'],
                    'images' => [$prod['image']],
                    'gallery_images' => [],
                    'short_description' => $prod['short_description'],
                    'description' => $prod['description'],
                    'specifications' => $prod['specifications'],
                    'delivery_policy' => 'Fast delivery across Nepal within 2-4 business days. Safe and secure packaging guaranteed.',
                    'shipping_returns' => '7-day easy return policy for unused products in original condition.',
                    'is_active' => true,
                    'is_featured' => $prod['is_featured'],
                    'in_stock' => true,
                    'on_sale' => $prod['on_sale'],
                    'meta_title' => $prod['name'] . ' | The Bamboo Bazar Nepal',
                    'meta_description' => $prod['short_description'],
                    'meta_keywords' => 'bamboo products nepal, eco friendly, ' . strtolower($prod['name']),
                ]
            );
        }
    }
}
