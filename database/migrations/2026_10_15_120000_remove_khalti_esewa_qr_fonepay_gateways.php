<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('payment_gateways')
            ->whereIn('code', ['khalti', 'esewa', 'qr_payment', 'fonepay'])
            ->delete();

        DB::table('orders')
            ->whereIn('payment_method', ['khalti', 'esewa', 'qr_payment', 'fonepay'])
            ->update(['payment_method' => 'cod']);

        DB::table('payment_transactions')
            ->whereIn('gateway_code', ['khalti', 'esewa', 'qr_payment', 'fonepay'])
            ->update(['gateway_code' => 'cod']);
    }

    public function down(): void
    {
        // No-op: Removed legacy third-party gateways are not re-inserted on rollback
    }
};
