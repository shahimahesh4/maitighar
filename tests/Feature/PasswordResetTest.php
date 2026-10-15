<?php

namespace Tests\Feature;

use App\Livewire\Auth\ForgotPasswordPage;
use App\Livewire\Auth\ResetPasswordPage;
use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword as ResetPasswordNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Livewire\Livewire;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_can_request_a_password_reset_link(): void
    {
        Notification::fake();
        $user = User::factory()->create(['email' => 'beauty@example.com']);

        Livewire::test(ForgotPasswordPage::class)
            ->set('email', $user->email)
            ->call('sendResetLink')
            ->assertHasNoErrors();

        Notification::assertSentTo($user, ResetPasswordNotification::class);
    }

    public function test_customer_can_reset_password_with_a_valid_token(): void
    {
        $user = User::factory()->create(['email' => 'beauty@example.com']);
        $token = Password::createToken($user);

        Livewire::test(ResetPasswordPage::class, ['token' => $token])
            ->set('email', $user->email)
            ->set('password', 'new-password-123')
            ->set('password_confirmation', 'new-password-123')
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect(route('login'));

        $this->assertTrue(Hash::check('new-password-123', $user->fresh()->password));
    }
}
