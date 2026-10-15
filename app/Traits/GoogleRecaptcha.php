<?php

namespace App\Traits;

use App\Services\GoogleRecaptchaService;

trait GoogleRecaptcha
{
    public string $recaptchaToken = '';

    protected function getRecaptchaRules(): array
    {
        if (! app(GoogleRecaptchaService::class)->shouldProtect()) {
            return [];
        }

        return [
            'recaptchaToken' => ['required', 'string'],
        ];
    }

    protected function verifyRecaptcha(): bool
    {
        $service = app(GoogleRecaptchaService::class);

        if (! $service->shouldProtect()) {
            return true;
        }

        if (! $service->verify($this->recaptchaToken)) {
            $this->addError('recaptchaToken', 'Google reCAPTCHA validation failed. Please try again.');

            return false;
        }

        return true;
    }
}
