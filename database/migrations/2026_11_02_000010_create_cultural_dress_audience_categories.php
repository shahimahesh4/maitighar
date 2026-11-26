<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

return new class extends Migration
{
    /**
     * Add the category tree used by the homepage collection banners without
     * altering the existing cultural categories or moving live products.
     */
    public function up(): void
    {
        $now = now();

        $ensureCategory = static function (string $name, string $slug, ?int $parentId = null, ?string $image = null) use ($now): int {
            $category = DB::table('categories')->where('slug', $slug)->first();

            if ($category) {
                return (int) $category->id;
            }

            return (int) DB::table('categories')->insertGetId([
                'parent_id' => $parentId,
                'name' => $name,
                'slug' => $slug,
                'image' => $image,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        };

        $culturalClothingId = $ensureCategory('Cultural Clothing', 'cultural-clothing');

        $audiences = [
            'womens' => ['name' => "Women's Cultural Dresses", 'slug' => 'womens-cultural-dresses'],
            'mens' => ['name' => "Men's Cultural Dresses", 'slug' => 'mens-cultural-dresses'],
            'kids' => ['name' => "Kids' Cultural Dresses", 'slug' => 'kids-cultural-dresses'],
        ];

        $cultures = [
            'tharu' => 'Tharu',
            'newari' => 'Newari',
            'rai' => 'Rai',
            'tamang' => 'Tamang',
            'magar' => 'Magar',
            'gurung' => 'Gurung',
        ];

        foreach ($audiences as $audienceKey => $audience) {
            $audienceId = $ensureCategory($audience['name'], $audience['slug'], $culturalClothingId);

            foreach ($cultures as $cultureSlug => $cultureName) {
                $legacyImage = DB::table('categories')
                    ->where('parent_id', $culturalClothingId)
                    ->where('slug', 'like', $cultureSlug.'%')
                    ->value('image');

                $ensureCategory(
                    $cultureName.' Cultural Dress',
                    $cultureSlug.'-'.$audienceKey.'-cultural-dress',
                    $audienceId,
                    $legacyImage,
                );
            }
        }

        Cache::forget('navigation.categories');
    }

    public function down(): void
    {
        // Intentionally retained: administrators may have assigned products to
        // these categories after this migration first ran. A rollback must not
        // delete live catalogue data.
    }
};
