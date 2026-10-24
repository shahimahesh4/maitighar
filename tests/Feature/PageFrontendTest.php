<?php

namespace Tests\Feature;

use App\Models\Page;
use Database\Seeders\PolicyPageSeeder;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class PageFrontendTest extends TestCase
{
    use DatabaseTransactions;

    public function test_published_page_is_rendered_on_frontend(): void
    {
        $page = Page::create([
            'title' => 'About Us',
            'slug' => 'about-us',
            'content' => '<p>We craft beautiful experiences.</p>',
            'meta_title' => 'About Us',
            'meta_description' => 'Learn more about our story.',
            'meta_keywords' => 'about, beauty, studio',
            'status' => 'published',
            'sort_order' => 1,
        ]);

        $page->faqs()->create([
            'question' => 'How can I contact you?',
            'answer' => 'You can reach our studio directly through the contact page.',
            'status' => 'published',
            'sort_order' => 1,
        ]);

        $response = $this->get('/about-us');

        $response->assertStatus(200);
        $response->assertSee($page->title);
        $response->assertSee('How can I contact you?');
    }

    public function test_refund_return_policy_page_is_seeded_and_rendered(): void
    {
        $this->seed(PolicyPageSeeder::class);

        $response = $this->get('/refund-return-policy');

        $response->assertOk();
        $response->assertSee('Refund &amp; Return Policy', false);
        $response->assertSee('Return eligibility');
        $response->assertSee('Damaged, defective, or incorrect orders');
    }
}
