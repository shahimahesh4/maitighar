<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $pageId = DB::table('pages')->where('slug', 'about-us')->value('id');

        if (! $pageId) {
            return;
        }

        $faqs = [
            ['What is Maitighar?', 'Maitighar is a Nepalese heritage-inspired brand dedicated to celebrating traditional craftsmanship, cultural clothing, handcrafted products, personal care services, and specialized skill training. We bring together tradition, creativity, and quality under one trusted name.'],
            ['What makes Maitighar unique?', 'Maitighar combines Nepalese cultural heritage with modern lifestyle needs. Our focus on authenticity, quality craftsmanship, personalized customer care, and practical skill development makes every experience meaningful.'],
            ['Where is Maitighar located?', 'Maitighar connects Nepalese heritage with communities in Nepal, Australia, and beyond. Please visit our Contact Us page for location details and enquiries.'],
            ['What does “Nepalese Heritage with Love” mean?', 'Our slogan reflects our commitment to preserving Nepalese traditions, celebrating cultural identity, supporting craftsmanship, and sharing the beauty of Nepal with the world.'],
            ['What types of products does Maitighar offer?', 'We offer a diverse collection of Nepalese cultural clothing, traditional Dhaka products, handcrafted gifts, bamboo accessories, personalized items, and lifestyle essentials inspired by Nepalese heritage.'],
            ['Are Maitighar products authentic?', 'We prioritize authenticity, quality materials, and craftsmanship in our product selection, with a focus on celebrating Nepalese cultural traditions.'],
            ['Can I order personalized or customized products?', 'Yes. Selected products may be personalized with names, logos, messages, or custom engraving. Please contact our team to discuss available customization options.'],
            ['Does Maitighar offer traditional Nepalese clothing?', 'Yes. Our heritage-inspired clothing collections include traditional outfits, Dhaka clothing, cultural accessories, and garments suitable for celebrations, cultural events, and special occasions.'],
            ['Does Maitighar deliver products across Australia?', 'Maitighar aims to make Nepalese heritage products accessible to customers across Australia. Delivery availability, charges, and estimated timeframes are provided during the ordering process.'],
            ['What is the return or exchange policy?', 'Returns and exchanges are subject to our store policies and product eligibility. Please review our Return and Refund Policy for detailed information.'],
            ['What personal care services does Maitighar provide?', 'Maitighar offers personal care services focused on quality, comfort, and individualized attention. Please explore our Services section for currently available treatments and appointments.'],
            ['How can I book a personal care appointment?', 'You can contact our team through the website or available contact channels to enquire about services, appointment availability, and booking procedures.'],
            ['Does Maitighar provide professional skill training?', 'Yes. Maitighar is committed to practical skill development through guided mentorship, hands-on learning, and specialized training programs in Nepal.'],
            ['Who can join Maitighar training programs?', 'Our training opportunities are designed for individuals interested in developing practical skills, improving their knowledge, and exploring personal or professional growth. Entry requirements may vary by program.'],
            ['Do I need previous experience to join a training course?', 'Previous experience may not be necessary for beginner-level programs. Requirements depend on the specific training course.'],
            ['How can I learn more about available training programs?', 'Visit our Training section or contact the Maitighar team for information about courses, schedules, fees, and enrolment procedures.'],
            ['How can I contact Maitighar?', 'You can reach us through our website’s Contact Us page, email, or available customer support channels. Our team is happy to assist with product enquiries, services, and training information.'],
            ['Can I purchase Maitighar products as gifts?', 'Absolutely. Our handcrafted products, personalized accessories, and cultural collections make thoughtful gifts for birthdays, festivals, celebrations, and special occasions.'],
            ['How can I stay updated on new collections and offers?', 'Follow Maitighar on our social media channels and visit our website regularly to discover new products, cultural collections, announcements, and special offers.'],
            ['Why should I choose Maitighar?', 'At Maitighar, we value authenticity, cultural pride, quality craftsmanship, creativity, and customer satisfaction. Whether you are shopping, learning, or enjoying our services, we strive to deliver experiences that reflect our passion for Nepalese heritage.'],
        ];

        foreach ($faqs as $index => [$question, $answer]) {
            DB::table('page_faqs')->updateOrInsert(
                ['page_id' => $pageId, 'question' => $question],
                ['answer' => $answer, 'status' => 'published', 'sort_order' => $index + 1, 'updated_at' => now(), 'created_at' => now()]
            );
        }
    }

    public function down(): void
    {
        // Keep CMS FAQ content intact when rolling back.
    }
};
