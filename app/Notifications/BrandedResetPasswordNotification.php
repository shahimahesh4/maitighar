<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;

class BrandedResetPasswordNotification extends ResetPassword
{
    public function toMail($notifiable): MailMessage
    {
        $url = url(route('password.reset', ['token' => $this->token, 'email' => $notifiable->getEmailForPasswordReset()], false));

        return (new MailMessage)->subject('Reset your Maitighar password')->view('emails.branded', ['subjectLine' => 'Password reset', 'heading' => 'Reset your password', 'intro' => 'We received a request to reset your account password. This link will expire for your security.', 'details' => ['Account' => $notifiable->email], 'actionLabel' => 'Reset password', 'actionUrl' => $url, 'messageText' => 'If you did not request this change, you can safely ignore this email.']);
    }
}
