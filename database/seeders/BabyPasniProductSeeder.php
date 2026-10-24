<?php

namespace Database\Seeders;

use App\Models\Attribute;
use App\Models\AttributeValue;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductAttribute;
use App\Models\ProductVariation;
use App\Models\ProductVariationAttributeValue;
use Illuminate\Database\Seeder;

class BabyPasniProductSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Ensure Brand & Category
        $brand = Brand::firstOrCreate(
            ['slug' => 'maitighar'],
            ['name' => 'Maitighar', 'image' => 'images/logo.png', 'is_active' => true]
        );

        $category = Category::firstOrCreate(
            ['slug' => 'palpali-dhaka-kids'],
            ['name' => 'Palpali Dhaka Kids Collection', 'is_active' => true]
        );

        // 2. Ensure Size Attribute & Baby Size Values
        $sizeAttr = Attribute::firstOrCreate(
            ['slug' => 'size'],
            ['name' => 'Size', 'type' => 'button', 'sort_order' => 1]
        );

        $babySizes = [
            ['value' => '3-6 Months', 'slug' => '3-6-months', 'sort_order' => 10],
            ['value' => '6-9 Months (Pasni)', 'slug' => '6-9-months-pasni', 'sort_order' => 11],
            ['value' => '9-12 Months', 'slug' => '9-12-months', 'sort_order' => 12],
            ['value' => '1-2 Years', 'slug' => '1-2-years', 'sort_order' => 13],
            ['value' => '2-3 Years', 'slug' => '2-3-years', 'sort_order' => 14],
        ];

        $sizeValueModels = [];
        foreach ($babySizes as $bs) {
            $sizeValueModels[$bs['slug']] = AttributeValue::firstOrCreate(
                ['attribute_id' => $sizeAttr->id, 'value' => $bs['value']],
                ['slug' => $bs['slug'], 'sort_order' => $bs['sort_order']]
            );
        }

        // ==========================================
        // 3. Product 1: Palpali Dhaka Baby Boy Pasni Set
        // ==========================================
        $boyProduct = Product::updateOrCreate(
            ['slug' => 'palpali-dhaka-baby-boy-pasni-set'],
            [
                'category_id' => $category->id,
                'brand_id' => $brand->id,
                'name' => 'Palpali Dhaka Baby Boy Pasni Set (5-Piece)',
                'product_type' => 'variable',
                'sku' => 'MG-KIDS-PASNI-001',
                'main_image' => 'products/palpali_dhaka_baby_pasni_set.jpg',
                'images' => ['products/palpali_dhaka_baby_pasni_set.jpg'],
                'gallery_images' => ['products/palpali_dhaka_baby_pasni_set.jpg'],
                'price' => 85.00,
                'regular_price' => 105.00,
                'sale_price' => 85.00,
                'old_price' => 105.00,
                'discount_percentage' => 19,
                'stock_quantity' => 25,
                'short_description' => 'Authentic handwoven Nepali Palpali Dhaka 5-piece Pasni (Rice Feeding Ceremony) outfit for baby boys, including Kurta, Surwal, Topi, waistcoat, and matching booties.',
                'description' => '<p>Celebrate your baby’s auspicious milestone <strong>Pasni (Rice Feeding Ceremony)</strong> with this royal, handcrafted <strong>Palpali Dhaka 5-Piece Ceremony Set</strong>. Tailored in Nepal using soft, baby-safe blush pink cotton-silk fabric paired with authentic, hand-loomed Palpali Dhaka geometric motifs.</p><ul><li><strong>Included Pieces (5-Piece Set):</strong> Soft pink Kurta tunic, matching Surwal pants, tailored Palpali Dhaka waistcoat with decorative brass buttons, traditional Nepali Dhaka Topi, and coordinating Dhaka booties.</li><li><strong>Gentle on Sensitive Baby Skin:</strong> Breathable, gentle inner cotton lining designed for maximum comfort during rituals and photo sessions.</li><li><strong>Occasions:</strong> Pasni / Rice Feeding, Nwaran (Naming Ceremony), Dashain, Tihar, weddings, and traditional family celebrations.</li></ul>',
                'specifications' => [
                    ['label' => 'Pieces Included', 'value' => '5 (Kurta, Surwal, Dhaka Waistcoat, Dhaka Topi, Dhaka Booties)'],
                    ['label' => 'Fabric', 'value' => 'Handwoven Palpali Dhaka Cotton & Soft Cotton-Silk'],
                    ['label' => 'Occasion', 'value' => 'Pasni (Rice Feeding Ceremony), Nwaran, Celebrations'],
                    ['label' => 'Care Instructions', 'value' => 'Dry Clean or Gentle Cold Hand Wash'],
                    ['label' => 'Origin', 'value' => 'Handcrafted in Palpa & Kathmandu, Nepal'],
                ],
                'delivery_policy' => 'Express tracked shipping across Australia and worldwide. Carefully packaged in keepsake boxes.',
                'shipping_returns' => '7-day hassle-free exchange assistance for sizing and fit.',
                'is_active' => true,
                'is_featured' => true,
                'in_stock' => true,
                'on_sale' => true,
                'meta_title' => 'Palpali Dhaka Baby Boy Pasni Set (5-Piece) | Maitighar',
                'meta_description' => 'Authentic Nepali Palpali Dhaka 5-piece Pasni outfit for baby boys. Kurta, Surwal, Topi, Waistcoat, and Booties handcrafted with love.',
                'meta_keywords' => 'palpali dhaka baby, pasni set boy, nepali rice feeding dress, baby dhaka topi, maitighar kids',
            ]
        );

        $prodAttrBoy = ProductAttribute::firstOrCreate(
            ['product_id' => $boyProduct->id, 'attribute_id' => $sizeAttr->id],
            ['is_visible' => true, 'is_variation' => true, 'sort_order' => 1]
        );
        $prodAttrBoy->values()->sync([
            $sizeValueModels['3-6-months']->id,
            $sizeValueModels['6-9-months-pasni']->id,
            $sizeValueModels['9-12-months']->id,
        ]);

        $boyVariations = [
            ['size_slug' => '3-6-months', 'sku' => 'MG-KIDS-PASNI-3-6M', 'regular_price' => 105.00, 'sale_price' => 85.00, 'stock' => 8],
            ['size_slug' => '6-9-months-pasni', 'sku' => 'MG-KIDS-PASNI-6-9M', 'regular_price' => 105.00, 'sale_price' => 85.00, 'stock' => 10],
            ['size_slug' => '9-12-months', 'sku' => 'MG-KIDS-PASNI-9-12M', 'regular_price' => 110.00, 'sale_price' => 90.00, 'stock' => 7],
        ];

        foreach ($boyVariations as $vData) {
            $valModel = $sizeValueModels[$vData['size_slug']];

            $variation = ProductVariation::updateOrCreate(
                ['product_id' => $boyProduct->id, 'sku' => $vData['sku']],
                [
                    'regular_price' => $vData['regular_price'],
                    'sale_price' => $vData['sale_price'],
                    'stock_quantity' => $vData['stock'],
                    'manage_stock' => true,
                    'is_enabled' => true,
                    'image' => 'products/palpali_dhaka_baby_pasni_set.jpg',
                ]
            );

            ProductVariationAttributeValue::firstOrCreate(
                ['product_variation_id' => $variation->id, 'attribute_id' => $sizeAttr->id],
                ['attribute_value_id' => $valModel->id]
            );
        }
        $boyProduct->syncPriceAndStockFromVariations();

        // ==========================================
        // 4. Product 2: Palpali Brocade & Tulle Baby Girl Pasni Frock
        // ==========================================
        $girlProduct = Product::updateOrCreate(
            ['slug' => 'palpali-brocade-baby-girl-pasni-frock'],
            [
                'category_id' => $category->id,
                'brand_id' => $brand->id,
                'name' => 'Palpali Brocade & Tulle Baby Girl Pasni Frock (with Matching Headband)',
                'product_type' => 'variable',
                'sku' => 'MG-KIDS-GIRL-PASNI-001',
                'main_image' => 'products/palpali_baby_girl_pasni_frock.jpg',
                'images' => ['products/palpali_baby_girl_pasni_frock.jpg'],
                'gallery_images' => ['products/palpali_baby_girl_pasni_frock.jpg'],
                'price' => 89.00,
                'regular_price' => 115.00,
                'sale_price' => 89.00,
                'old_price' => 115.00,
                'discount_percentage' => 22,
                'stock_quantity' => 24,
                'short_description' => 'Royal crimson Palpali brocade and tiered net tulle Pasni ceremony frock for baby girls, featuring an elegant crossover Angrakha neckline, zari embellishments, velvet ribbon ties, and coordinating floral headband.',
                'description' => '<p>Dazzle on your baby girl’s ceremonial <strong>Pasni (Rice Feeding Ceremony)</strong> or festive family celebrations with this stunning <strong>Designer Palpali Brocade & Tulle Baby Girl Frock Set</strong>. Crafted in a rich, auspicious crimson red, this dress marries authentic Nepalese brocade artistry with whimsical, fairytale layers of soft tiered tulle.</p><ul><li><strong>Included Set:</strong> Royal crimson brocade and tiered tulle ceremony dress plus matching handcrafted floral headband.</li><li><strong>Design Details:</strong> Classic crossover Angrakha neckline with intricate golden zari border and soft velvet tie accents.</li><li><strong>Baby-Safe Lining:</strong> 100% gentle, breathable cotton inner lining to keep tender skin happy and itch-free throughout the ceremony and photo session.</li><li><strong>Occasions:</strong> Pasni / Rice Feeding Ceremony, Nwaran, Birthdays, Dashain, Tihar, and wedding celebrations.</li></ul>',
                'specifications' => [
                    ['label' => 'Pieces Included', 'value' => '2 (Ceremony Frock & Matching Floral Headband)'],
                    ['label' => 'Fabric', 'value' => 'Authentic Brocade with Soft Net Tulle & 100% Breathable Cotton Inner Lining'],
                    ['label' => 'Color', 'value' => 'Auspicious Crimson Red & Multi-Color Floral Brocade'],
                    ['label' => 'Neckline', 'value' => 'Cross-over Angrakha Neckline with Velvet Ribbon Accents'],
                    ['label' => 'Care Instructions', 'value' => 'Dry Clean or Gentle Cold Spot Clean'],
                    ['label' => 'Origin', 'value' => 'Handcrafted in Kathmandu, Nepal'],
                ],
                'delivery_policy' => 'Express tracked shipping across Australia and worldwide. Carefully packaged in keepsake boxes.',
                'shipping_returns' => '7-day hassle-free exchange assistance for sizing and fit.',
                'is_active' => true,
                'is_featured' => true,
                'in_stock' => true,
                'on_sale' => true,
                'meta_title' => 'Palpali Brocade & Tulle Baby Girl Pasni Frock (with Headband) | Maitighar',
                'meta_description' => 'Designer royal crimson Palpali brocade and tiered tulle baby girl Pasni dress with floral headband. Perfect for Rice Feeding Ceremony.',
                'meta_keywords' => 'baby girl pasni dress, nepali pasni frock, red dhaka baby dress, rice feeding ceremony girl, maitighar kids',
            ]
        );

        $prodAttrGirl = ProductAttribute::firstOrCreate(
            ['product_id' => $girlProduct->id, 'attribute_id' => $sizeAttr->id],
            ['is_visible' => true, 'is_variation' => true, 'sort_order' => 1]
        );
        $prodAttrGirl->values()->sync([
            $sizeValueModels['6-9-months-pasni']->id,
            $sizeValueModels['9-12-months']->id,
            $sizeValueModels['1-2-years']->id,
        ]);

        $girlVariations = [
            ['size_slug' => '6-9-months-pasni', 'sku' => 'MG-KIDS-GIRL-6-9M', 'regular_price' => 115.00, 'sale_price' => 89.00, 'stock' => 8],
            ['size_slug' => '9-12-months', 'sku' => 'MG-KIDS-GIRL-9-12M', 'regular_price' => 115.00, 'sale_price' => 89.00, 'stock' => 8],
            ['size_slug' => '1-2-years', 'sku' => 'MG-KIDS-GIRL-1-2Y', 'regular_price' => 120.00, 'sale_price' => 95.00, 'stock' => 8],
        ];

        foreach ($girlVariations as $vData) {
            $valModel = $sizeValueModels[$vData['size_slug']];

            $variation = ProductVariation::updateOrCreate(
                ['product_id' => $girlProduct->id, 'sku' => $vData['sku']],
                [
                    'regular_price' => $vData['regular_price'],
                    'sale_price' => $vData['sale_price'],
                    'stock_quantity' => $vData['stock'],
                    'manage_stock' => true,
                    'is_enabled' => true,
                    'image' => 'products/palpali_baby_girl_pasni_frock.jpg',
                ]
            );

            ProductVariationAttributeValue::firstOrCreate(
                ['product_variation_id' => $variation->id, 'attribute_id' => $sizeAttr->id],
                ['attribute_value_id' => $valModel->id]
            );
        }
        $girlProduct->syncPriceAndStockFromVariations();
    }
}
