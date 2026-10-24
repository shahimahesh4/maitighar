<?php

namespace Tests\Feature;

use App\Livewire\Auth\RegisterPage;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Livewire\Livewire;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use DatabaseTransactions;

    public function test_customer_can_register_with_a_nepal_phone_number(): void
    {
        Livewire::test(RegisterPage::class)
            ->set('name', 'Beauty Customer')
            ->set('email', 'beauty@example.com')
            ->set('phone', '9812345678')
            ->set('password', 'Kba!2026Secure')
            ->set('password_confirmation', 'Kba!2026Secure')
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect('/');

        $this->assertDatabaseHas('users', [
            'email' => 'beauty@example.com',
            'phone' => '+9779812345678',
        ]);

        $this->assertAuthenticatedAs(User::where('email', 'beauty@example.com')->first());
    }

    public function test_registration_rejects_an_invalid_nepal_phone_number(): void
    {
        Livewire::test(RegisterPage::class)
            ->set('name', 'Beauty Customer')
            ->set('email', 'beauty@example.com')
            ->set('phone', '12345')
            ->set('password', 'Kba!2026Secure')
            ->set('password_confirmation', 'Kba!2026Secure')
            ->call('save')
            ->assertHasErrors(['phone']);
    }

    public function test_registration_rejects_a_weak_password(): void
    {
        Livewire::test(RegisterPage::class)
            ->set('name', 'Beauty Customer')
            ->set('email', 'beauty@example.com')
            ->set('phone', '9812345678')
            ->set('password', 'password123')
            ->set('password_confirmation', 'password123')
            ->call('save')
            ->assertHasErrors(['password']);
    }
}
