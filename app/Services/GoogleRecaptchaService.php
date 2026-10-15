<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Http;

class GoogleRecaptchaService
{
    public function enabled(): bool
    {
        return filter_var(Setting::get('google_recaptcha_enabled', false), FILTER_VALIDATE_BOOL);
    }

    public function siteKey(): ?string
    {
        return Setting::get('google_recaptcha_site_key', '');
    }

    public function secretKey(): ?string
    {
        return Setting::get('google_recaptcha_secret_key', '');
    }

    public function shouldProtect(): bool
    {
        return $this->enabled() && filled($this->siteKey()) && filled($this->secretKey());
    }

    public function scriptSrc(): ?string
    {
        if (! $this->shouldProtect()) {
            return null;
        }

        return 'https://www.google.com/recaptcha/api.js?render='.$this->siteKey();
    }

    public function verify(string $token, ?string $remoteIp = null): bool
    {
        if (! $this->shouldProtect() || blank($token)) {
            return false;
        }

        try {
            $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
                'secret' => $this->secretKey(),
                'response' => $token,
                'remoteip' => $remoteIp ?? request()->ip(),
            ]);
        } catch (\Throwable $e) {
            return false;
        }

        if (! $response->successful()) {
            return false;
        }

        $data = $response->json();

        return isset($data['success']) && $data['success'] === true;
    }
}
