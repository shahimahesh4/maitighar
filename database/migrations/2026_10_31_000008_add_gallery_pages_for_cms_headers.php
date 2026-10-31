<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('pages')) {
            return;
        }

        $now = now();

        foreach ([
            [
                'title' => 'Our Photo Gallery',
                'slug' => 'gallery',
                'content' => '<p>Explore handcrafted artistry, cultural showcases, and memorable moments from Maitighar.</p>',
                'meta_title' => 'Photo Gallery | Maitighar',
                'meta_description' => 'Explore handcrafted artistry, cultural showcases, and memorable moments from Maitighar.',
                'status' => 'published',
                'sort_order' => 7,
            ],
            [
                'title' => 'Our Video Gallery',
                'slug' => 'videos',
                'content' => '<p>Watch artisan craft, cultural stories, product showcases, and inspiring moments from Maitighar.</p>',
                'meta_title' => 'Video Gallery | Maitighar',
                'meta_description' => 'Watch artisan craft, cultural stories, product showcases, and inspiring moments from Maitighar.',
                'status' => 'published',
                'sort_order' => 8,
            ],
        ] as $page) {
            if (! DB::table('pages')->where('slug', $page['slug'])->exists()) {
                DB::table('pages')->insert([...$page, 'created_at' => $now, 'updated_at' => $now]);
            }
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('pages')) {
            return;
        }

        DB::table('pages')
            ->whereIn('slug', ['gallery', 'videos'])
            ->whereIn('title', ['Our Photo Gallery', 'Our Video Gallery'])
            ->delete();
    }
};
