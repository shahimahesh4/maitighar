<?php

namespace App\Filament\Resources\PaymentGatewayResource\Pages;

use App\Filament\Resources\PaymentGatewayResource;
use App\Models\PaymentGateway;
use Filament\Resources\Pages\CreateRecord;

class CreatePaymentGateway extends CreateRecord
{
    protected static string $resource = PaymentGatewayResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Enforce default behavior sync
        if ($data['is_default'] ?? false) {
            PaymentGateway::where('is_default', true)->update(['is_default' => false]);
        }

        return $data;
    }
}
