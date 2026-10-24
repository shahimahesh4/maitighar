<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductAttribute;
use App\Models\ProductVariation;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class MaitigharProductsSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function (): void {
            // 1. Remove existing products and variations
            DB::table('product_variation_attribute_values')->delete();
            DB::table('product_variations')->delete();
            DB::table('product_attribute_values')->delete();
            DB::table('product_attributes')->delete();
            Product::query()->delete();

            // 2. Ensure Maitighar Brand exists
            $brand = Brand::firstOrCreate(
                ['slug' => 'maitighar'],
                [
                    'name' => 'Maitighar',
                    'image' => 'images/logo.png',
                    'is_active' => true,
                ]
            );

            // 3. Ensure Categories exist
            $categories = [
                'drinkware-bottles' => Category::firstOrCreate(
                    ['slug' => 'drinkware-bottles'],
                    ['name' => 'Drinkware & Bottles', 'image' => 'products/main/bamboo-stainless-steel-bottle.png', 'is_active' => true]
                ),
                'eco-living-personal-care' => Category::firstOrCreate(
                    ['slug' => 'eco-living-personal-care'],
                    ['name' => 'Eco Living & Stationery', 'image' => 'products/main/bamboo-clicker-pen.png', 'is_active' => true]
                ),
                'handicrafts-baskets' => Category::firstOrCreate(
                    ['slug' => 'handicrafts-baskets'],
                    ['name' => 'Handicrafts & Baskets', 'image' => 'products/main/handle-bamboo-baskets-12-inch.png', 'is_active' => true]
                ),
                'custom-engravings' => Category::firstOrCreate(
                    ['slug' => 'custom-engravings'],
                    ['name' => 'Custom Engravings & Keychains', 'image' => 'products/main/bamboo-bookmarks-with-design-15-cm.png', 'is_active' => true]
                ),
            ];

            // 4. Products from D:\New Download\Maitighar
            $productsData = [
                [
                    'name' => 'Bamboo Stainless Steel Bottle (500ml)',
                    'slug' => 'bamboo-stainless-steel-bottle-500ml',
                    'sku' => 'MG-AUS-001',
                    'category' => 'drinkware-bottles',
                    'price' => 35.00,
                    'regular_price' => 42.00,
                    'sale_price' => 35.00,
                    'stock_quantity' => 60,
                    'image' => 'products/main/bamboo-stainless-steel-bottle.png',
                    'short_description' => 'Sustainable 500ml double-wall vacuum insulated water bottle crafted with natural seasoned bamboo exterior and 304 food-grade stainless steel interior.',
                    'description' => '<p>Elevate your hydration routine with the <strong>Bamboo Stainless Steel Bottle (500ml)</strong> from Maitighar. Handcrafted from sustainably harvested natural bamboo, this eco-friendly flask features a high-grade 304 stainless steel double-wall vacuum insulated interior that keeps your beverages hot for up to 12 hours or cold for up to 24 hours.</p><p>Features a leak-proof bamboo cap with steel carrying loop, wide-mouth opening for ice cubes, and a removable stainless steel tea infuser/strainer for loose leaf teas and fruit infusions.</p>',
                    'specifications' => [
                        ['label' => 'Capacity', 'value' => '500 ml'],
                        ['label' => 'Material', 'value' => 'Natural Bamboo Exterior & 304 Food-Grade Stainless Steel'],
                        ['label' => 'Thermal Retention', 'value' => 'Hot for 12h, Cold for 24h'],
                        ['label' => 'Accessories', 'value' => 'Removable Stainless Steel Tea Infuser Included'],
                        ['label' => 'Eco Friendly', 'value' => '100% BPA-Free & Reusable'],
                    ],
                ],
                [
                    'name' => 'Bamboo Clicker Pen',
                    'slug' => 'bamboo-clicker-pen',
                    'sku' => 'MG-AUS-002',
                    'category' => 'eco-living-personal-care',
                    'price' => 9.50,
                    'regular_price' => 12.00,
                    'sale_price' => 9.50,
                    'stock_quantity' => 120,
                    'image' => 'products/main/bamboo-clicker-pen.png',
                    'short_description' => 'Smooth-writing retractable clicker ballpoint pen made with natural seasoned bamboo barrel, lightweight grip, and polished chrome accents.',
                    'description' => '<p>Make your daily writing sustainable with the <strong>Bamboo Clicker Pen</strong> from Maitighar. Skillfully turned from seasoned organic bamboo, each pen features a distinct natural grain pattern, lightweight ergonomic grip, and smooth-gliding 0.7mm black archival ink.</p><p>Equipped with a sturdy push-button retractable mechanism, polished chrome pocket clip, and easily replaceable standard ink refills for years of eco-conscious note-taking and journaling.</p>',
                    'specifications' => [
                        ['label' => 'Material', 'value' => 'Natural Seasoned Bamboo & Stainless Steel Clip'],
                        ['label' => 'Ink Type', 'value' => '0.7mm Black Document Ink'],
                        ['label' => 'Mechanism', 'value' => 'Push-Button Click Retractable'],
                        ['label' => 'Refillable', 'value' => 'Yes (Standard Style)'],
                        ['label' => 'Origin', 'value' => 'Handcrafted for Maitighar'],
                    ],
                ],
                [
                    'name' => 'Handle Bamboo Baskets (12-inch)',
                    'slug' => 'handle-bamboo-baskets-12-inch',
                    'sku' => 'MG-AUS-003',
                    'category' => 'handicrafts-baskets',
                    'price' => 38.00,
                    'regular_price' => 45.00,
                    'sale_price' => 38.00,
                    'stock_quantity' => 40,
                    'image' => 'products/main/handle-bamboo-baskets-12-inch.png',
                    'short_description' => 'Handwoven 12-inch rustic bamboo basket with an arched reinforced handle, ideal for fruit displays, gift hampers, ceremony offerings, and kitchen storage.',
                    'description' => '<p>Bring the warmth of authentic traditional craftsmanship to your home with the <strong>12-Inch Handwoven Bamboo Basket with Handle</strong>. Meticulously hand-braided by hereditary artisans using split natural bamboo reeds treated for longevity and pest resistance.</p><p>Features a spacious wide-mouth open bowl with a reinforced double-ribbed arch handle. Perfect for farmhouse kitchen counters, fresh fruit displays, ceremony puja offerings, or bespoke gift packaging.</p>',
                    'specifications' => [
                        ['label' => 'Diameter', 'value' => '12 Inches (Approx. 30 cm)'],
                        ['label' => 'Height', 'value' => '14 Inches including Handle'],
                        ['label' => 'Material', 'value' => '100% Hand-split Natural Bamboo'],
                        ['label' => 'Usage', 'value' => 'Fruit Basket, Storage, Gift Hampers, Decor'],
                        ['label' => 'Craft', 'value' => 'Authentic Handloom Weave'],
                    ],
                ],
                [
                    'name' => 'Bamboo Bookmarks with Design (15 cm)',
                    'slug' => 'bamboo-bookmarks-with-design-15-cm',
                    'sku' => 'MG-AUS-004',
                    'category' => 'custom-engravings',
                    'price' => 10.00,
                    'regular_price' => 14.00,
                    'sale_price' => 10.00,
                    'stock_quantity' => 100,
                    'image' => 'products/main/bamboo-bookmarks-with-design-15-cm.png',
                    'short_description' => '15 cm laser-carved natural bamboo bookmark featuring traditional intricate art motifs, ultra-thin flexible finish, and a delicate hanging silk tassel.',
                    'description' => '<p>A timeless keepsake for book lovers and stationery enthusiasts. The <strong>Bamboo Bookmark (15 cm)</strong> is precision laser-engraved onto ultra-thin, flexible natural bamboo wood, showcasing exquisite traditional Nepali cultural and floral motifs.</p><p>Smoothly sanded to protect book pages from creasing or damage, and finished with a silk tassel cord for easy page-marking in novels, textbooks, or personal journals.</p>',
                    'specifications' => [
                        ['label' => 'Length', 'value' => '15 cm (6 inches)'],
                        ['label' => 'Width', 'value' => '3 cm'],
                        ['label' => 'Material', 'value' => 'Micro-sanded Flexible Bamboo Wood'],
                        ['label' => 'Finish', 'value' => 'Natural Satin Polish with Silk Tassel'],
                        ['label' => 'Design', 'value' => 'Traditional Laser-Carved Cultural Patterns'],
                    ],
                ],
                [
                    'name' => 'Bamboo Coffee Mug with Dual Opening',
                    'slug' => 'bamboo-coffee-mug-with-dual-opening',
                    'sku' => 'MG-AUS-005',
                    'category' => 'drinkware-bottles',
                    'price' => 24.00,
                    'regular_price' => 29.00,
                    'sale_price' => 24.00,
                    'stock_quantity' => 50,
                    'image' => 'products/main/bamboo-coffee-mug-with-dual-opening.png',
                    'short_description' => 'Insulated bamboo travel coffee mug with 304 stainless steel interior liner, ergonomic comfort handle, and innovative dual-opening flip lid.',
                    'description' => '<p>Start your mornings right with the <strong>Bamboo Coffee Mug with Dual Opening</strong>. Combining rustic natural bamboo warmth with contemporary drinkware functionality, this mug features a food-grade 304 stainless steel interior that locks in temperature while preventing lingering flavors or odors.</p><p>The smart dual-opening lid allows you to drink directly from the ergonomic sip spout or insert a reusable straw. Comes with an anti-slip base and heat-insulated bamboo handle.</p>',
                    'specifications' => [
                        ['label' => 'Capacity', 'value' => '400 ml / 13.5 oz'],
                        ['label' => 'Liner', 'value' => 'Food-Grade 304 Stainless Steel'],
                        ['label' => 'Shell', 'value' => '100% Genuine Natural Bamboo'],
                        ['label' => 'Lid', 'value' => 'Dual-Opening BPA-Free Splash Proof Lid'],
                        ['label' => 'Usage', 'value' => 'Hot Coffee, Tea, Cold Brews'],
                    ],
                ],
                [
                    'name' => 'Bamboo Coffee Tumbler with Steel Cover',
                    'slug' => 'bamboo-coffee-tumbler-with-steel-cover',
                    'sku' => 'MG-AUS-006',
                    'category' => 'drinkware-bottles',
                    'price' => 26.00,
                    'regular_price' => 32.00,
                    'sale_price' => 26.00,
                    'stock_quantity' => 55,
                    'image' => 'products/main/bamboo-coffee-tumbler-with-steel-cover.png',
                    'short_description' => 'Sleek double-wall vacuum insulated bamboo coffee tumbler featuring a leak-proof brushed stainless steel twist-on cover.',
                    'description' => '<p>Sip in style on your commute with the <strong>Bamboo Coffee Tumbler with Steel Cover</strong>. Boasting a tapered silhouette that fits standard vehicle cup holders, this tumbler is wrapped in polished organic bamboo with a thermal double-wall vacuum core.</p><p>The brushed stainless steel screw-top lid creates an airtight, leak-resistant seal to prevent spills in your bag or car. Keeps espresso and tea steaming hot for up to 6 hours.</p>',
                    'specifications' => [
                        ['label' => 'Capacity', 'value' => '450 ml / 15 oz'],
                        ['label' => 'Exterior', 'value' => 'Hand-selected Natural Bamboo'],
                        ['label' => 'Interior', 'value' => 'Double-Wall Stainless Steel Vacuum Core'],
                        ['label' => 'Cover', 'value' => 'Heavy-Duty Stainless Steel Screw-On Lid'],
                        ['label' => 'Fit', 'value' => 'Compatible with Standard Car Cup Holders'],
                    ],
                ],
                [
                    'name' => 'Bamboo Collapsible Mobile Stand',
                    'slug' => 'bamboo-collapsible-mobile-stand',
                    'sku' => 'MG-AUS-007',
                    'category' => 'eco-living-personal-care',
                    'price' => 14.50,
                    'regular_price' => 19.00,
                    'sale_price' => 14.50,
                    'stock_quantity' => 80,
                    'image' => 'products/main/bamboo-collapsible-mobile-stand.png',
                    'short_description' => 'Two-piece interlocking collapsible bamboo desk phone stand with charging cable cutout for hands-free video calls, reading, and charging.',
                    'description' => '<p>Declutter your workspace sustainably with the <strong>Bamboo Collapsible Mobile Stand</strong>. Precision-cut from seasoned high-density bamboo, this two-piece interlocking stand sets up in seconds and collapses completely flat to slip into your pocket, backpack, or laptop sleeve.</p><p>Offers an optimal ergonomic viewing angle for Zoom meetings, FaceTime calls, video watching, and recipe reading, with a built-in cable pass-through channel for charging while mounted.</p>',
                    'specifications' => [
                        ['label' => 'Dimensions', 'value' => '14 cm x 8 cm (Flat)'],
                        ['label' => 'Material', 'value' => 'High-Density Seasoned Bamboo'],
                        ['label' => 'Compatibility', 'value' => 'Universal for all iPhone, Samsung, and Android devices'],
                        ['label' => 'Portability', 'value' => 'Ultra-flat Two-Piece Interlocking Design'],
                        ['label' => 'Cable Slot', 'value' => 'Bottom Passthrough for Charging Cords'],
                    ],
                ],
                [
                    'name' => 'Bamboo Diary Hardcover Eco-friendly Notebook',
                    'slug' => 'bamboo-diary-hardcover-eco-friendly-notebook',
                    'sku' => 'MG-AUS-008',
                    'category' => 'eco-living-personal-care',
                    'price' => 22.00,
                    'regular_price' => 28.00,
                    'sale_price' => 22.00,
                    'stock_quantity' => 70,
                    'image' => 'products/main/bamboo-diary-hardcover-eco-friendly-notebook.png',
                    'short_description' => 'Premium A5 journal with laser-cut natural bamboo wood hardcover, twin-wire bronze spiral ring binding, and 160 pages of recycled lined paper.',
                    'description' => '<p>Capture your thoughts, plans, and artistic sketches in the <strong>Bamboo Diary Hardcover Eco-Friendly Notebook</strong>. Features solid real bamboo wooden front and back covers with a smooth laser-etched border design that gives every journal a distinctive executive presence.</p><p>Contains 160 pages (80 sheets) of thick, bleed-resistant recycled wood-free paper held together with durable twin-wire bronze spiral binding that lays flat 360 degrees on your desk.</p>',
                    'specifications' => [
                        ['label' => 'Size', 'value' => 'A5 (21 cm x 14.8 cm)'],
                        ['label' => 'Covers', 'value' => 'Solid Natural Bamboo Wood Hardcover'],
                        ['label' => 'Pages', 'value' => '160 Pages (80 Sheets) 80gsm Lined Recycled Paper'],
                        ['label' => 'Binding', 'value' => 'Heavy-Duty Dual Wire Bronze Spiral'],
                        ['label' => 'Pen Loop / Closure', 'value' => 'Elastic Band Support'],
                    ],
                ],
                [
                    'name' => 'Bamboo Keychain Single-sided (Round Shape)',
                    'slug' => 'bamboo-keychain-single-sided-round-shape',
                    'sku' => 'MG-AUS-009',
                    'category' => 'custom-engravings',
                    'price' => 6.50,
                    'regular_price' => 9.00,
                    'sale_price' => 6.50,
                    'stock_quantity' => 150,
                    'image' => 'products/main/bamboo-keychain-single-sided-round-shape.png',
                    'short_description' => 'Circular natural bamboo keychain with beveled edge polish, lightweight feel, and reinforced stainless steel key ring.',
                    'description' => '<p>Add natural organic charm to your keys with the <strong>Bamboo Keychain (Round Shape)</strong>. Handcrafted from solid bamboo wood discs with rounded smooth beveled edges, this tactile keychain is lightweight, scratch-resistant, and comfortable in pockets.</p><p>Connected via a heavy-duty stainless steel jump ring and split key loop, ideal for house keys, car remotes, or bag zipper tags.</p>',
                    'specifications' => [
                        ['label' => 'Diameter', 'value' => '4 cm (1.6 inches)'],
                        ['label' => 'Thickness', 'value' => '5 mm'],
                        ['label' => 'Material', 'value' => 'Solid Polished Natural Bamboo'],
                        ['label' => 'Hardware', 'value' => 'Rust-Resistant Stainless Steel Key Ring'],
                        ['label' => 'Weight', 'value' => 'Ultra-lightweight (Approx. 12g)'],
                    ],
                ],
                [
                    'name' => 'Handcrafted Bamboo Keyrings',
                    'slug' => 'handcrafted-bamboo-keyrings',
                    'sku' => 'MG-AUS-010',
                    'category' => 'custom-engravings',
                    'price' => 7.00,
                    'regular_price' => 10.00,
                    'sale_price' => 7.00,
                    'stock_quantity' => 140,
                    'image' => 'products/main/keyrings.png',
                    'short_description' => 'Artisanal multi-design Nepali bamboo keyrings with smooth protective lacquer, high-tensile hardware, and traditional woodcraft finishing.',
                    'description' => '<p>Showcasing the delicate artistry of Nepali bamboo turners, these <strong>Handcrafted Bamboo Keyrings</strong> blend functional everyday utility with authentic cultural appeal. Hand-polished to a warm golden sheen that highlights natural bamboo fiber rings.</p><p>Durable enough for daily wear and tear while remaining gentle on car dashboards and pocket linings. Makes a thoughtful souvenir or eco-conscious party gift.</p>',
                    'specifications' => [
                        ['label' => 'Material', 'value' => 'Seasoned Golden Bamboo Wood'],
                        ['label' => 'Hardware', 'value' => 'High-Tensile Swivel Snap & Split Ring'],
                        ['label' => 'Finish', 'value' => 'Protective Natural Herbal Lacquer'],
                        ['label' => 'Craftsmanship', 'value' => '100% Handcrafted'],
                        ['label' => 'Usage', 'value' => 'Keys, Bag Charms, Luggage Tags'],
                    ],
                ],
                [
                    'name' => 'Rectangle Bamboo Keychain One-sided',
                    'slug' => 'rectangle-bamboo-keychain-one-sided',
                    'sku' => 'MG-AUS-011',
                    'category' => 'custom-engravings',
                    'price' => 6.50,
                    'regular_price' => 9.00,
                    'sale_price' => 6.50,
                    'stock_quantity' => 150,
                    'image' => 'products/main/rectangle-bamboo-keychain-one-sided.png',
                    'short_description' => 'Sleek rectangular bamboo keychain with crisp beveled corners, smooth satin touch, and brushed metal split ring hardware.',
                    'description' => '<p>Minimalist, contemporary, and grounded in nature. The <strong>Rectangle Bamboo Keychain One-Sided</strong> features a clean linear silhouette crafted from dense, cross-laminated organic bamboo wood.</p><p>Lightweight yet remarkably rigid and drop-proof, fitted with a high-strength swivel chain and flat split ring that holds multiple keys securely without bending.</p>',
                    'specifications' => [
                        ['label' => 'Dimensions', 'value' => '5.5 cm x 3 cm'],
                        ['label' => 'Thickness', 'value' => '5 mm'],
                        ['label' => 'Material', 'value' => 'Cross-Laminated Natural Bamboo'],
                        ['label' => 'Keyring', 'value' => 'Flat Stainless Steel Split Ring with Swivel Link'],
                        ['label' => 'Finish', 'value' => 'Silk Matte Organic Protective Wax'],
                    ],
                ],
            ];

            foreach ($productsData as $data) {
                $category = $categories[$data['category']];
                $image = $data['image'];

                Product::create([
                    'category_id' => $category->id,
                    'brand_id' => $brand->id,
                    'name' => $data['name'],
                    'product_type' => 'simple',
                    'slug' => $data['slug'],
                    'sku' => $data['sku'],
                    'price' => $data['sale_price'],
                    'regular_price' => $data['regular_price'],
                    'sale_price' => $data['sale_price'],
                    'old_price' => $data['regular_price'],
                    'discount_percentage' => (int) round((($data['regular_price'] - $data['sale_price']) / $data['regular_price']) * 100),
                    'stock_quantity' => $data['stock_quantity'],
                    'main_image' => $image,
                    'images' => [$image],
                    'gallery_images' => [$image],
                    'short_description' => $data['short_description'],
                    'description' => $data['description'],
                    'specifications' => $data['specifications'],
                    'delivery_policy' => 'Fast tracked dispatch from our Sydney warehouse. Standard delivery 2–5 business days across Australia.',
                    'shipping_returns' => '14-day replacement guarantee for damaged or defective items during transit.',
                    'in_stock' => true,
                    'is_active' => true,
                    'is_featured' => true,
                    'on_sale' => $data['regular_price'] > $data['sale_price'],
                    'meta_title' => $data['name'] . ' | Maitighar Australia',
                    'meta_description' => $data['short_description'],
                    'meta_keywords' => 'bamboo products, eco-friendly, maitighar australia, handcrafted bamboo, ' . strtolower($data['name']),
                ]);
            }
        });
    }
}
