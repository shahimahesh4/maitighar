<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;

class BrandedVerifyEmailNotification extends VerifyEmail
{
    public function toMail($notifiable): MailMessage
    {
        $url = $this->verificationUrl($notifiable);

        return (new MailMessage)->subject('Verify your Maitighar email')->view('emails.branded', ['subjectLine' => 'Verify email', 'heading' => 'Verify your email address', 'intro' => 'Welcome to Maitighar. Confirm your email address to secure your account.', 'details' => ['Email' => $notifiable->email], 'actionLabel' => 'Verify email', 'actionUrl' => $url, 'messageText' => 'If you did not create this account, no action is required.']);
    }
}
