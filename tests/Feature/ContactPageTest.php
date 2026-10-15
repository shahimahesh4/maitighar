<?php

namespace Tests\Feature;

use App\Livewire\ContactPage;
use App\Models\ContactMessage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;
use Tests\TestCase;

class ContactPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_contact_page_stores_a_valid_enquiry(): void
    {
        Mail::fake();

        Livewire::test(ContactPage::class)
            ->set('full_name', 'Sita Sharma')
            ->set('email', 'sita@example.com')
            ->set('phone', '9812345678')
            ->set('address', 'Bhairahawa')
            ->set('subject', 'Makeup course inquiry')
            ->set('message', 'Please share the next professional makeup course date.')
            ->call('submit')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('contact_messages', [
            'email' => 'sita@example.com',
            'phone' => '+9779812345678',
            'subject' => 'Makeup course inquiry',
            'status' => 'new',
        ]);

        $this->assertSame(1, ContactMessage::count());
    }

    public function test_contact_page_rejects_invalid_nepal_phone_number(): void
    {
        Livewire::test(ContactPage::class)
            ->set('full_name', 'Sita Sharma')
            ->set('email', 'sita@example.com')
            ->set('phone', '1234')
            ->set('subject', 'Product enquiry')
            ->set('message', 'Please share more information about this handcrafted product.')
            ->call('submit')
            ->assertHasErrors(['phone']);
    }

    public function test_contact_page_stores_a_valid_australian_phone_number(): void
    {
        Mail::fake();

        Livewire::test(ContactPage::class)
            ->set('full_name', 'Liam Smith')
            ->set('email', 'liam@example.com')
            ->set('phone', '0412 345 678')
            ->set('address', 'Sydney NSW')
            ->set('subject', 'Handicrafts order enquiry')
            ->set('message', 'I would like to inquire about bulk ordering of your handicrafts.')
            ->call('submit')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('contact_messages', [
            'email' => 'liam@example.com',
            'phone' => '+61412345678',
            'subject' => 'Handicrafts order enquiry',
            'status' => 'new',
        ]);
    }
}
