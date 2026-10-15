<?php

namespace App\Filament\Pages\Auth;

use App\Services\CommunicationService;
use Filament\Pages\Auth\Register as BaseRegister;

class Register extends BaseRegister
{
    protected function mutateFormDataBeforeRegister(array $data): array
    {
        if (! app(CommunicationService::class)->emailVerificationEnabled()) {
            $data['email_verified_at'] = now();
        }

        return $data;
    }
}
