<?php

namespace Database\Seeders;

use App\Models\PaymentGateway;
use Illuminate\Database\Seeder;

class PaymentGatewaySeeder extends Seeder
{
    public function run(): void
    {
        PaymentGateway::whereIn('code', ['stripe', 'razorpay', 'paytm'])->delete();

        $khalti = PaymentGateway::firstOrNew(['code' => 'khalti']);
        $khaltiCredentials = array_merge(['secret_key' => ''], $khalti->credentials ?? []);
        $khaltiSettings = array_merge(['website_url' => config('app.url'), 'merchant_name' => config('app.name')], $khalti->settings ?? []);
        $khalti->fill([
            'name' => 'Khalti', 'driver' => 'khalti', 'is_active' => $khalti->exists ? $khalti->is_active : false,
            'environment' => $khalti->exists ? $khalti->environment : 'sandbox', 'priority' => 1, 'is_default' => false,
            'credentials' => $khaltiCredentials, 'settings' => $khaltiSettings, 'health_status' => $khalti->health_status ?? 'unknown',
        ])->save();

        $esewa = PaymentGateway::firstOrNew(['code' => 'esewa']);
        $esewaCredentials = array_merge(['product_code' => 'EPAYTEST', 'secret_key' => '8gBm/:&EnhH.1/q'], $esewa->credentials ?? []);
        $esewa->fill([
            'name' => 'eSewa', 'driver' => 'esewa', 'is_active' => $esewa->exists ? $esewa->is_active : true,
            'environment' => $esewa->exists ? $esewa->environment : 'sandbox', 'priority' => 2, 'is_default' => false,
            'credentials' => $esewaCredentials, 'settings' => $esewa->settings ?? [], 'health_status' => $esewa->health_status ?? 'unknown',
        ])->save();

        PaymentGateway::updateOrCreate(['code' => 'bank_transfer'], [
            'name' => 'Bank Transfer', 'driver' => 'manual', 'is_active' => true,
            'environment' => 'live', 'priority' => 3, 'is_default' => false,
            'credentials' => [],
            'settings' => [
                'bank_name' => 'Configure bank name in admin',
                'account_name' => 'Configure account holder in admin',
                'account_number' => 'Configure account number in admin',
                'instructions' => 'Transfer the exact total and upload the payment voucher below.',
            ],
            'health_status' => 'healthy',
        ]);

        PaymentGateway::updateOrCreate(['code' => 'qr_payment'], [
            'name' => 'Scan QR & Upload Voucher', 'driver' => 'manual', 'is_active' => true,
            'environment' => 'live', 'priority' => 4, 'is_default' => false,
            'credentials' => [],
            'settings' => [
                'bank_name' => 'Configure QR provider in admin',
                'qr_image' => null,
                'instructions' => 'Scan the QR code, pay the exact total, then upload the payment voucher below.',
            ],
            'health_status' => 'healthy',
        ]);
        PaymentGateway::updateOrCreate(['code' => 'cod'], [
            'name' => 'Cash on Delivery', 'driver' => 'cod', 'is_active' => true,
            'environment' => 'sandbox', 'priority' => 5, 'is_default' => true,
            'credentials' => [], 'settings' => [], 'health_status' => 'healthy',
        ]);

        PaymentGateway::updateOrCreate(['code' => 'fonepay'], [
            'name' => 'Fonepay', 'driver' => 'fonepay', 'is_active' => false,
            'environment' => 'sandbox', 'priority' => 6, 'is_default' => false,
            'credentials' => [],
            'settings' => ['onboarding_required' => true, 'note' => 'Activate after receiving merchant API credentials from Fonepay.'],
            'health_status' => 'unknown',
        ]);
    }
}
