<?php

namespace App\Services;

use App\Mail\BrandedMessageMail;
use App\Models\Setting;
use Illuminate\Support\Facades\Mail;

class CommunicationService
{
    public function store(): array
    {
        return [
            'name' => Setting::get('store_name', 'Maitighar'),
            'email' => Setting::get('store_email', 'maitigharaustralia@gmail.com'),
            'phone' => Setting::get('store_phone', '+61 432 685 546'),
            'address' => Setting::get('store_address', '2/75 Dartbrook Road, Auburn NSW, Sydney, NSW 2144'),
            'footer' => Setting::get('email_footer', 'Maitighar - Nepalese Heritage with Love.'),
        ];
    }

    public function receiptsEnabled(): bool
    {
        return filter_var(Setting::get('send_customer_receipts', true), FILTER_VALIDATE_BOOL);
    }

    public function emailVerificationEnabled(): bool
    {
        return filter_var(Setting::get('email_verification_enabled', false), FILTER_VALIDATE_BOOL);
    }

    public function phoneVerificationEnabled(): bool
    {
        return filter_var(Setting::get('phone_verification_enabled', false), FILTER_VALIDATE_BOOL)
            && filter_var(Setting::get('sparrow_sms_enabled', false), FILTER_VALIDATE_BOOL);
    }

    public function send(string $email, string $subject, string $heading, string $intro, array $details = [], ?string $actionLabel = null, ?string $actionUrl = null, ?string $messageText = null): void
    {
        try {
            Mail::to($email)->send(new BrandedMessageMail($subject, $heading, $intro, $details, $actionLabel, $actionUrl, $messageText));
        } catch (\Throwable $exception) {
            report($exception);
        }
    }

    public function notifyAdmin(string $subject, string $heading, string $intro, array $details = [], ?string $actionUrl = null): void
    {
        $email = Setting::get('admin_notification_email', $this->store()['email']);
        $this->send($email, $subject, $heading, $intro, $details, $actionUrl ? 'Open admin panel' : null, $actionUrl);
    }
}
