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
                'content' => <<<'HTML'
<p>Maitighar celebrates Nepalese Heritage with Love. We are dedicated to offering timeless traditions, handcrafted heritage items, personal care services, and specialized skill training in Nepal.</p>
<p>Our experience brings together guided mentorship, hands-on practice, personalized customer care, and high quality standards. Everyone is welcomed into an environment where culture, creativity, and authenticity shine.</p>
<h2>Our mission</h2>
<p>Our mission is to celebrate authentic Nepalese heritage, make quality training accessible, and deliver heartfelt service and premium handcrafted essentials to our valued patrons.</p>
<h2>What makes Maitighar special</h2>
<ul>
<li>Authentic Nepali craft and heritage-inspired products</li>
<li>Hands-on learning with professional tools and techniques</li>
<li>Guidance from experienced mentors and artisans</li>
<li>Dedicated customer support and personal care services</li>
<li>A welcoming, family-like community embodying Nepalese warmth</li>
</ul>
<h2>More than a brand</h2>
<p>Alongside our collection, our team provides personalized services and training. We bring modern elegance to timeless Nepali traditions.</p>
<p>Whether you want to explore our collection, join a specialized training, or experience our services, our team is ready to welcome you to Maitighar.</p>
HTML,
                'thumbnail_image' => 'images/kba/about-us-academy.png',
                'banner_image' => 'images/kba/about-us-academy.png',
                'meta_title' => 'About Maitighar | Nepalese Heritage with Love',
                'meta_description' => 'Meet Maitighar in Nepal—Nepalese Heritage with Love, authentic collections, experienced mentorship, career support, and personalized services.',
                'meta_keywords' => 'Maitighar, Nepalese Heritage with Love, Nepali craft, Nepal, beauty training, heritage products',
                'status' => 'published',
                'sort_order' => 1,
            ]
        );

        $faqs = [
            ['question' => 'Do I need previous beauty experience to join?', 'answer' => '<p>No. Our training welcomes beginners as well as learners who want to improve existing skills. The academy team can help you choose the right course.</p>'],
            ['question' => 'Is the training practical or theory-based?', 'answer' => '<p>Training combines essential theory with guided demonstrations and hands-on practice, so students develop both knowledge and professional confidence.</p>'],
            ['question' => 'Do students receive a certificate?', 'answer' => '<p>Students who successfully complete their selected course, attendance requirements, and practical assessment receive a certificate of completion.</p>'],
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
