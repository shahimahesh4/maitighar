<?php

namespace Tests\Feature;

use App\Livewire\Partials\Footer;
use App\Models\Subscriber;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class FooterTest extends TestCase
{
    use RefreshDatabase;

    public function test_footer_newsletter_subscription_is_saved(): void
    {
        Livewire::test(Footer::class)
            ->set('subscriberEmail', 'visitor@example.com')
            ->call('subscribe')
            ->assertHasNoErrors()
            ->assertSet('subscriberEmail', '');

        $this->assertDatabaseHas('subscribers', ['email' => 'visitor@example.com', 'is_active' => true]);
    }

    public function test_footer_rejects_invalid_email(): void
    {
        Livewire::test(Footer::class)
            ->set('subscriberEmail', 'not-an-email')
            ->call('subscribe')
            ->assertHasErrors(['subscriberEmail']);

        $this->assertSame(0, Subscriber::count());
    }
}
