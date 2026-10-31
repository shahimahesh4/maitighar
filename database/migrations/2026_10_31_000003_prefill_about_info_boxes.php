<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('pages')
            ->where('slug', 'about-us')
            ->whereNull('about_info_boxes')
            ->update([
                'about_info_boxes' => json_encode([
                    ['icon' => 'bi-compass', 'title' => 'Our Mission', 'description' => 'To share Nepal’s beauty, tradition, and craftsmanship through authentic products, meaningful experiences, and heartfelt service. We connect people with the stories, skills, and care behind every creation, keeping heritage present in everyday life.'],
                    ['icon' => 'bi-eye', 'title' => 'Our Vision', 'description' => 'To become a trusted bridge between Nepal and the world, where living heritage inspires connection and supports artisan communities. We give people meaningful ways to discover, celebrate, and share Nepalese culture with pride, creativity, and a lasting sense of belonging.'],
                    ['icon' => 'bi-patch-check', 'title' => 'Why Choose Us?', 'description' => 'Choose Maitighar for authentic Nepali craft, thoughtful personal care, practical learning, and warm support from experienced mentors. Every product and service is selected with quality, culture, and community in mind, so each experience feels personal, welcoming, and meaningful.'],
                ], JSON_UNESCAPED_UNICODE),
                'updated_at' => now(),
            ]);
    }

    public function down(): void
    {
        // Preserve administrator-created card content during rollback.
    }
};
