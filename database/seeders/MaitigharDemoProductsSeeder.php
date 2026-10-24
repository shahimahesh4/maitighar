<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class MaitigharDemoProductsSeeder extends Seeder
{
    public function run(): void
    {
        $maitigharBrand = Brand::firstOrCreate(
            ['slug' => 'maitighar'],
            ['name' => 'Maitighar', 'image' => 'images/logo.png', 'is_active' => true]
        );

        $bambooBrand = Brand::firstOrCreate(
            ['slug' => 'the-bamboo-bazar'],
            ['name' => 'The Bamboo Bazar', 'image' => 'images/logo.png', 'is_active' => true]
        );

        // Ensure key categories exist
        $furnitureCat = Category::firstOrCreate(['slug' => 'bamboo-furniture'], ['name' => 'Bamboo Furniture', 'is_active' => true]);
        $drinkwareCat = Category::firstOrCreate(['slug' => 'drinkware-bottles'], ['name' => 'Drinkware & Bottles', 'is_active' => true]);
        $ecoLivingCat = Category::firstOrCreate(['slug' => 'eco-living-personal-care'], ['name' => 'Eco Living & Personal Care', 'is_active' => true]);
        $handicraftCat = Category::firstOrCreate(['slug' => 'handicrafts-baskets'], ['name' => 'Handicrafts & Baskets', 'is_active' => true]);
        $engravingCat = Category::firstOrCreate(['slug' => 'custom-engravings'], ['name' => 'Custom Engravings', 'is_active' => true]);

        $products = [
            [
                'name' => 'Hand-Hammered Himalayan Meditation Singing Bowl (7 Metal)',
                'slug' => 'hand-hammered-himalayan-singing-bowl',
                'category_id' => $handicraftCat->id,
                'brand_id' => $maitigharBrand->id,
                'sku' => 'MG-SB-008',
                'price' => 48.00,
                'regular_price' => 60.00,
                'sale_price' => 48.00,
                'main_image' => 'https://images.unsplash.com/photo-1545205597-3d9d02c29597?w=600&auto=format&fit=crop&q=80',
                'images' => ['https://images.unsplash.com/photo-1545205597-3d9d02c29597?w=600&auto=format&fit=crop&q=80'],
                'short_description' => 'Authentic 7-metal handcrafted Tibetan singing bowl with wooden striker and silk ring cushion for meditation and mindfulness.',
                'description' => '<p>Harmonize your mind, body, and surroundings with the <strong>Hand-Hammered Himalayan Meditation Singing Bowl</strong>. Hand-crafted by hereditary Newari metal artisans in Patan, Nepal using an ancient formulation of seven planetary metals including copper, brass, and tin.</p><p>Produces rich, sustained overtone vibrations calibrated to the Heart Chakra (Anahata), ideal for yoga studios, sound therapy practitioners, or daily mindfulness rituals.</p>',
                'specifications' => [
                    ['label' => 'Diameter', 'value' => '14 cm / 5.5 inches'],
                    ['label' => 'Composition', 'value' => 'Traditional 7-Metal Alloy'],
                    ['label' => 'Included Accessories', 'value' => 'Suede-Wrapped Wooden Striker & Silk Cushion'],
                    ['label' => 'Origin', 'value' => 'Patan, Kathmandu Valley, Nepal'],
                ],
                'is_featured' => true,
                'is_active' => true,
                'in_stock' => true,
                'on_sale' => true,
            ],
            [
                'name' => 'Handmade Nepali Lokta Paper Journal & Quill Set',
                'slug' => 'handmade-nepali-lokta-paper-journal',
                'category_id' => $ecoLivingCat->id,
                'brand_id' => $maitigharBrand->id,
                'sku' => 'MG-LPJ-009',
                'price' => 16.50,
                'regular_price' => 22.00,
                'sale_price' => 16.50,
                'main_image' => 'https://images.unsplash.com/photo-1544716278-ca5e3f4abd8c?w=600&auto=format&fit=crop&q=80',
                'images' => ['https://images.unsplash.com/photo-1544716278-ca5e3f4abd8c?w=600&auto=format&fit=crop&q=80'],
                'short_description' => 'Eco-friendly handmade journal bound in wild Himalayan Daphne bush bark paper with deckle edges and wrap tie closure.',
                'description' => '<p>Capture your thoughts and creative reflections in an authentic <strong>Nepali Lokta Paper Journal</strong>. Hand-harvested from sustainable Daphne shrub bark at high altitudes in Nepal, Lokta paper has been used for centuries for sacred manuscripts due to its natural pest resistance and archival longevity.</p><p>Features 96 deckle-edged unlined sheets perfect for fountain pens, calligraphy, sketching, and watercolor journaling.</p>',
                'specifications' => [
                    ['label' => 'Page Count', 'value' => '192 writing pages (96 sheets)'],
                    ['label' => 'Material', 'value' => '100% Wild Himalayan Lokta Fiber'],
                    ['label' => 'Binding', 'value' => 'Hand-Stitched Leather Tie Wrap'],
                    ['label' => 'Eco Property', 'value' => 'Tree-Free & Chemical-Free'],
                ],
                'is_featured' => true,
                'is_active' => true,
                'in_stock' => true,
                'on_sale' => true,
            ],
            [
                'name' => 'Pure Himalayan Cashmere Pashmina Shawl (Charcoal Grey)',
                'slug' => 'pure-himalayan-cashmere-pashmina-shawl',
                'category_id' => $ecoLivingCat->id,
                'brand_id' => $maitigharBrand->id,
                'sku' => 'MG-PAS-010',
                'price' => 120.00,
                'regular_price' => 150.00,
                'sale_price' => 120.00,
                'main_image' => 'https://images.unsplash.com/photo-1608231387042-66d1773070a5?w=600&auto=format&fit=crop&q=80',
                'images' => ['https://images.unsplash.com/photo-1608231387042-66d1773070a5?w=600&auto=format&fit=crop&q=80'],
                'short_description' => 'Handwoven pure Chyangra cashmere pashmina shawl from the high Himalayas, ultra-soft, warm and feather-light.',
                'description' => '<p>Wrap yourself in unmatched elegance with the <strong>Pure Himalayan Cashmere Pashmina Shawl</strong>. Sourced ethically from the fine undercoat of mountain Chyangra goats residing at 4,000+ meters elevation in the Himalayas.</p><p>Hand-spun and handwoven on traditional wooden looms in Nepal. Lightweight yet deeply insulating, it drapes gracefully over evening wear or casual attire.</p>',
                'specifications' => [
                    ['label' => 'Dimensions', 'value' => '200 cm x 70 cm (approx. 78 x 28 inches)'],
                    ['label' => 'Composition', 'value' => '100% Pure Mountain Cashmere'],
                    ['label' => 'Weave', 'value' => 'Diamond Weave with Eyelash Fringes'],
                    ['label' => 'Care', 'value' => 'Dry Clean or Gentle Cold Handwash'],
                ],
                'is_featured' => true,
                'is_active' => true,
                'in_stock' => true,
                'on_sale' => true,
            ],
            [
                'name' => 'Handwoven Bamboo Hanging Pendant Lamp Shade',
                'slug' => 'handwoven-bamboo-hanging-pendant-lamp',
                'category_id' => $furnitureCat->id,
                'brand_id' => $bambooBrand->id,
                'sku' => 'TBB-HLS-011',
                'price' => 38.00,
                'regular_price' => 48.00,
                'sale_price' => 38.00,
                'main_image' => 'https://images.unsplash.com/photo-1513506003901-1e6a229e2d15?w=600&auto=format&fit=crop&q=80',
                'images' => ['https://images.unsplash.com/photo-1513506003901-1e6a229e2d15?w=600&auto=format&fit=crop&q=80'],
                'short_description' => 'Natural woven bamboo ceiling lampshade with warm ambient diffusion, hand-crafted by indigenous artisans.',
                'description' => '<p>Bring an organic, boho-minimalist vibe to your dining room, living space, or cafe with the <strong>Handwoven Bamboo Hanging Pendant Lamp Shade</strong>. Hand-plaited from flexible mountain bamboo canes with an open-weave lattice that casts breathtaking shadow patterns across your walls.</p>',
                'specifications' => [
                    ['label' => 'Diameter', 'value' => '35 cm (approx. 14 inches)'],
                    ['label' => 'Height', 'value' => '30 cm'],
                    ['label' => 'Fitting', 'value' => 'Standard E27 Bulb Holder Compatible'],
                    ['label' => 'Material', 'value' => '100% Eco Bamboo Cane with Clear Eco-Varnish'],
                ],
                'is_featured' => true,
                'is_active' => true,
                'in_stock' => true,
                'on_sale' => true,
            ],
            [
                'name' => 'Traditional Hand-Carved Newari Wooden Peacock Window Frame',
                'slug' => 'traditional-hand-carved-peacock-window',
                'category_id' => $handicraftCat->id,
                'brand_id' => $maitigharBrand->id,
                'sku' => 'MG-NW-012',
                'price' => 145.00,
                'regular_price' => 180.00,
                'sale_price' => 145.00,
                'main_image' => 'https://images.unsplash.com/photo-1579783900882-c0d3dad7b119?w=600&auto=format&fit=crop&q=80',
                'images' => ['https://images.unsplash.com/photo-1579783900882-c0d3dad7b119?w=600&auto=format&fit=crop&q=80'],
                'short_description' => 'Exquisite traditional Kathmandu Valley peacock window replica, masterfully hand-carved from seasoned Sal wood.',
                'description' => '<p>Own a piece of living UNESCO heritage architecture with the <strong>Traditional Hand-Carved Newari Wooden Peacock Window Frame</strong>. Created by master woodcarvers of Bhaktapur, Nepal, honoring 15th-century Malla dynasty woodwork traditions.</p><p>Features intricate openwork plumage, floral filigree, and a pre-installed rear brass hanging bracket for effortless wall display.</p>',
                'specifications' => [
                    ['label' => 'Dimensions', 'value' => '40 cm H x 32 cm W x 6 cm D'],
                    ['label' => 'Wood Type', 'value' => 'Seasoned Nepali Sal Hardwood'],
                    ['label' => 'Finish', 'value' => 'Antique Dark Teak Wax Polish'],
                    ['label' => 'Origin', 'value' => 'Bhaktapur Artisans, Nepal'],
                ],
                'is_featured' => true,
                'is_active' => true,
                'in_stock' => true,
                'on_sale' => true,
            ],

            [
                'name' => 'Pure Handcrafted Ayurvedic Copper Water Pitcher (1.5L)',
                'slug' => 'pure-handcrafted-ayurvedic-copper-pitcher',
                'category_id' => $drinkwareCat->id,
                'brand_id' => $maitigharBrand->id,
                'sku' => 'MG-CWP-014',
                'price' => 42.00,
                'regular_price' => 55.00,
                'sale_price' => 42.00,
                'main_image' => 'https://images.unsplash.com/photo-1616401784845-180882ba9ba8?w=600&auto=format&fit=crop&q=80',
                'images' => ['https://images.unsplash.com/photo-1616401784845-180882ba9ba8?w=600&auto=format&fit=crop&q=80'],
                'short_description' => '99.7% pure hammered Ayurvedic copper jug with lid for overnight water charging and natural alkaline wellness.',
                'description' => '<p>Practice the ancient Ayurvedic ritual of <em>Tamra Jal</em> with our <strong>Pure Handcrafted Ayurvedic Copper Water Pitcher</strong>. Storing drinking water in pure copper naturally charges water with trace oligodynamic copper ions that aid digestion, boost immunity, and maintain natural cellular vitality.</p>',
                'specifications' => [
                    ['label' => 'Capacity', 'value' => '1.5 Liters / 50 oz'],
                    ['label' => 'Material', 'value' => '99.7% Pure Food-Grade Hammered Copper'],
                    ['label' => 'Includes', 'value' => 'Tight-Fitting Copper Lid with Knob Handle'],
                    ['label' => 'Origin', 'value' => 'Coppersmith Artisans of Tansen, Palpa'],
                ],
                'is_featured' => true,
                'is_active' => true,
                'in_stock' => true,
                'on_sale' => true,
            ],
            [
                'name' => 'Personalized Laser-Engraved Bamboo Tea Chest with 6 Compartments',
                'slug' => 'personalized-laser-engraved-bamboo-tea-chest',
                'category_id' => $engravingCat->id,
                'brand_id' => $bambooBrand->id,
                'sku' => 'TBB-TC-015',
                'price' => 39.00,
                'regular_price' => 49.00,
                'sale_price' => 39.00,
                'main_image' => 'https://images.unsplash.com/photo-1597481499750-3e6b22637e12?w=600&auto=format&fit=crop&q=80',
                'images' => ['https://images.unsplash.com/photo-1597481499750-3e6b22637e12?w=600&auto=format&fit=crop&q=80'],
                'short_description' => 'Custom-engraved solid bamboo tea organizer box with magnetic lid and transparent viewing window.',
                'description' => '<p>Elevate your hospitality or create an unforgettable corporate or wedding gift with the <strong>Personalized Laser-Engraved Bamboo Tea Chest</strong>. Features six deep compartments to neatly arrange tea bags, loose-leaf tins, or spices.</p>',
                'specifications' => [
                    ['label' => 'Dimensions', 'value' => '32 cm x 22 cm x 10 cm'],
                    ['label' => 'Compartments', 'value' => '6 Equal Removable Divider Slots'],
                    ['label' => 'Customization', 'value' => 'High-Definition Laser Engraved Lid'],
                    ['label' => 'Material', 'value' => '100% Solid Seasoned Bamboo'],
                ],
                'is_featured' => true,
                'is_active' => true,
                'in_stock' => true,
                'on_sale' => true,
            ],
        ];

        foreach ($products as $data) {
            Product::updateOrCreate(
                ['slug' => $data['slug']],
                [
                    'category_id' => $data['category_id'],
                    'brand_id' => $data['brand_id'],
                    'name' => $data['name'],
                    'sku' => $data['sku'],
                    'price' => $data['price'],
                    'regular_price' => $data['regular_price'],
                    'sale_price' => $data['sale_price'],
                    'old_price' => $data['regular_price'],
                    'main_image' => $data['main_image'],
                    'images' => $data['images'],
                    'gallery_images' => [],
                    'short_description' => $data['short_description'],
                    'description' => $data['description'],
                    'specifications' => $data['specifications'],
                    'delivery_policy' => 'Fast delivery across Nepal and Australia. Safe packaging guaranteed.',
                    'shipping_returns' => '7-day easy return policy for unused products in original packaging.',
                    'is_active' => $data['is_active'],
                    'is_featured' => $data['is_featured'],
                    'in_stock' => $data['in_stock'],
                    'on_sale' => $data['on_sale'],
                    'meta_title' => $data['name'] . ' | Maitighar',
                    'meta_description' => $data['short_description'],
                    'meta_keywords' => 'nepali handicraft, authentic, ' . strtolower($data['name']),
                ]
            );
        }
    }
}
