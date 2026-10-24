<?php

namespace Database\Seeders;

use App\Models\Attribute;
use App\Models\AttributeValue;
use Illuminate\Database\Seeder;

class AttributeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $size = Attribute::firstOrCreate(
            ['slug' => 'size'],
            ['name' => 'Size', 'type' => 'select', 'sort_order' => 1]
        );

        $sizeValues = [
            ['value' => 'XS', 'slug' => 'xs', 'sort_order' => 1],
            ['value' => 'S', 'slug' => 's', 'sort_order' => 2],
            ['value' => 'M', 'slug' => 'm', 'sort_order' => 3],
            ['value' => 'L', 'slug' => 'l', 'sort_order' => 4],
            ['value' => 'XL', 'slug' => 'xl', 'sort_order' => 5],
            ['value' => 'XXL', 'slug' => 'xxl', 'sort_order' => 6],
        ];

        foreach ($sizeValues as $v) {
            AttributeValue::firstOrCreate(
                ['attribute_id' => $size->id, 'value' => $v['value']],
                ['slug' => $v['slug'], 'sort_order' => $v['sort_order']]
            );
        }

        $colour = Attribute::firstOrCreate(
            ['slug' => 'colour'],
            ['name' => 'Colour', 'type' => 'color', 'sort_order' => 2]
        );

        $colourValues = [
            ['value' => 'Red', 'slug' => 'red', 'color_code' => '#DC2626', 'sort_order' => 1],
            ['value' => 'Navy Blue', 'slug' => 'navy-blue', 'color_code' => '#1E3A8A', 'sort_order' => 2],
            ['value' => 'Forest Green', 'slug' => 'forest-green', 'color_code' => '#166534', 'sort_order' => 3],
            ['value' => 'Black', 'slug' => 'black', 'color_code' => '#111827', 'sort_order' => 4],
            ['value' => 'Cream / White', 'slug' => 'cream-white', 'color_code' => '#F9FAFB', 'sort_order' => 5],
            ['value' => 'Maroon', 'slug' => 'maroon', 'color_code' => '#800000', 'sort_order' => 6],
        ];

        foreach ($colourValues as $v) {
            AttributeValue::firstOrCreate(
                ['attribute_id' => $colour->id, 'value' => $v['value']],
                ['slug' => $v['slug'], 'color_code' => $v['color_code'], 'sort_order' => $v['sort_order']]
            );
        }

        $material = Attribute::firstOrCreate(
            ['slug' => 'material'],
            ['name' => 'Material', 'type' => 'select', 'sort_order' => 3]
        );

        $materialValues = [
            ['value' => 'Handwoven Palpali Dhaka', 'slug' => 'handwoven-palpali-dhaka', 'sort_order' => 1],
            ['value' => 'Pure Pashmina Silk', 'slug' => 'pure-pashmina-silk', 'sort_order' => 2],
            ['value' => 'Organic Himalayan Cotton', 'slug' => 'organic-himalayan-cotton', 'sort_order' => 3],
        ];

        foreach ($materialValues as $v) {
            AttributeValue::firstOrCreate(
                ['attribute_id' => $material->id, 'value' => $v['value']],
                ['slug' => $v['slug'], 'sort_order' => $v['sort_order']]
            );
        }
    }
}
