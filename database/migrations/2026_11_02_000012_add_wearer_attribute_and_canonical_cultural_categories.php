<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $now = now();
        $wearer = DB::table('attributes')->where('slug', 'wearer')->first();
        $wearerId = $wearer?->id ?? DB::table('attributes')->insertGetId([
            'name' => 'Wearer', 'slug' => 'wearer', 'type' => 'select', 'sort_order' => 0,
            'created_at' => $now, 'updated_at' => $now,
        ]);

        foreach (['men' => 'Men', 'women' => 'Women', 'boys' => 'Boys', 'girls' => 'Girls'] as $slug => $value) {
            DB::table('attribute_values')->updateOrInsert(
                ['attribute_id' => $wearerId, 'slug' => $slug],
                ['value' => $value, 'sort_order' => array_search($slug, ['men', 'women', 'boys', 'girls'], true), 'updated_at' => $now, 'created_at' => $now],
            );
        }

        $root = DB::table('categories')->where('slug', 'cultural-clothing')->first();
        if ($root) {
            foreach (['limbu' => 'Limbu Cultural Dress', 'lama' => 'Lama Cultural Dress'] as $slug => $name) {
                DB::table('categories')->updateOrInsert(
                    ['slug' => $slug.'-cultural-dress'],
                    ['parent_id' => $root->id, 'name' => $name, 'is_active' => true, 'updated_at' => $now, 'created_at' => $now],
                );
            }

            // Hide only the empty, auto-created duplicate branches. Existing
            // cultural categories and every product assignment remain intact.
            DB::table('categories')->whereIn('slug', ['womens-cultural-dresses', 'mens-cultural-dresses', 'kids-cultural-dresses'])->update(['is_active' => false, 'collection_audience' => null, 'updated_at' => $now]);
            DB::table('categories')->where('slug', 'like', '%-womens-cultural-dress')->orWhere('slug', 'like', '%-mens-cultural-dress')->orWhere('slug', 'like', '%-kids-cultural-dress')->update(['is_active' => false, 'updated_at' => $now]);
        }

        Cache::forget('navigation.categories');
    }

    public function down(): void {}
};
