<?php

namespace Database\Seeders;

use App\Models\PaymentGateway;
use Illuminate\Database\Seeder;

class PaymentGatewaySeeder extends Seeder
{
    public function run(): void
    {
        PaymentGateway::whereIn('code', [
            'khalti',
            'esewa',
            'qr_payment',
            'fonepay',
            'razorpay',
            'paytm',
        ])->delete();

        PaymentGateway::updateOrCreate(['code' => 'stripe'], [
            'name' => 'Credit / Debit Card',
            'driver' => 'stripe',
            'is_active' => true,
            'environment' => 'sandbox',
            'priority' => 1,
            'is_default' => false,
            'credentials' => [
                'secret_key' => env('STRIPE_SECRET_KEY', 'sk_test_placeholder'),
                'publishable_key' => env('STRIPE_KEY', 'pk_test_placeholder'),
            ],
            'settings' => [
                'currency' => 'AUD',
            ],
            'health_status' => 'healthy',
        ]);

        PaymentGateway::updateOrCreate(['code' => 'bank_transfer'], [
            'name' => 'Bank Transfer',
            'driver' => 'manual',
            'is_active' => true,
            'environment' => 'live',
            'priority' => 2,
            'is_default' => false,
            'credentials' => [],
            'settings' => [
                'bank_name' => 'Commonwealth Bank of Australia (CBA)',
                'account_name' => 'Maitighar Pty Ltd',
                'account_number' => 'BSB: 062-000 Account: 12345678',
                'instructions' => 'Transfer the exact total in AUD and upload your payment receipt below.',
            ],
            'health_status' => 'healthy',
        ]);

        PaymentGateway::updateOrCreate(['code' => 'cod'], [
            'name' => 'Cash on Delivery',
            'driver' => 'cod',
            'is_active' => true,
            'environment' => 'live',
            'priority' => 3,
            'is_default' => true,
            'credentials' => [],
            'settings' => [],
            'health_status' => 'healthy',
        ]);
    }
}
