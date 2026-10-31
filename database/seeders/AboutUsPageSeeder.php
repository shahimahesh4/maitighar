<?php

namespace Database\Seeders;

use App\Models\Page;
use Illuminate\Database\Seeder;

class AboutUsPageSeeder extends Seeder
{
    public function run(): void
    {
        $page = Page::updateOrCreate(
            ['slug' => 'about-us'],
            [
                'title' => 'About Us',
                'about_info_boxes' => [
                    ['icon' => 'bi-compass', 'title' => 'Our Mission', 'description' => 'To share Nepal’s heritage, tradition, and craftsmanship through authentic products, meaningful experiences, and heartfelt service. We connect people with the stories, skills, and care behind every creation, keeping heritage present in everyday life.'],
                    ['icon' => 'bi-eye', 'title' => 'Our Vision', 'description' => 'To become a trusted bridge between Nepal and the world, where living heritage inspires connection and supports artisan communities. We give people meaningful ways to discover, celebrate, and share Nepalese culture with pride, creativity, and a lasting sense of belonging.'],
                    ['icon' => 'bi-patch-check', 'title' => 'Why Choose Us?', 'description' => 'Choose Maitighar for authentic Nepali craft, thoughtful personal care, practical learning, and warm support from experienced mentors. Every product and service is selected with quality, culture, and community in mind, so each experience feels personal, welcoming, and meaningful.'],
                ],
                'content' => <<<'HTML'
<p>Maitighar celebrates Nepalese Heritage with Love. We are dedicated to offering timeless traditions, handcrafted heritage items, personal care services, and meaningful community experiences in Nepal.</p>
<p>Our experience brings together guided mentorship, hands-on practice, personalized customer care, and high quality standards. Everyone is welcomed into an environment where culture, creativity, and authenticity shine.</p>
<h2>Our mission</h2>
<p>Our mission is to celebrate authentic Nepalese heritage, make quality experiences accessible, and deliver heartfelt service and premium handcrafted essentials to our valued patrons.</p>
<h2>What makes Maitighar special</h2>
<p>Authentic Nepali craft and heritage-inspired products. Hands-on learning with professional tools and techniques. Guidance from experienced mentors and artisans. Dedicated customer support and personal care services. A welcoming, family-like community embodying Nepalese warmth.</p>
<h2>More than a brand</h2>
<p>Alongside our collection, our team provides personalized services and community experiences. We bring modern elegance to timeless Nepali traditions.</p>
<p>Whether you want to explore our collection, join a special gathering, or experience our services, our team is ready to welcome you to Maitighar.</p>
HTML,
                'thumbnail_image' => 'images/kba/about-us-academy.png',
                'banner_image' => 'images/kba/about-us-academy.png',
                'meta_title' => 'About Maitighar | Nepalese Heritage with Love',
                'meta_description' => 'Meet Maitighar in Nepal—Nepalese Heritage with Love, authentic collections, experienced mentorship, career support, and personalized services.',
                'meta_keywords' => 'Maitighar, Nepalese Heritage with Love, Nepali craft, Nepal, heritage products',
                'status' => 'published',
                'sort_order' => 1,
            ]
        );

        $faqs = [
            ['question' => 'Where is Maitighar located?', 'answer' => '<p>We are located at 2/75 Dartbrook Road, Auburn NSW, Sydney, NSW 2144, Australia.</p>'],
        ];

        foreach ($faqs as $index => $faq) {
            $page->faqs()->updateOrCreate(
                ['question' => $faq['question']],
                ['answer' => $faq['answer'], 'status' => 'published', 'sort_order' => $index + 1]
            );
        }
    }
}
