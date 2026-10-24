<?php

use App\Models\Setting;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Ensure centralized store currency settings default to AUD with $ symbol
        Setting::set('default_currency', 'AUD');
        Setting::set('currency_symbol', '$');

        // 2. Set default currency on orders table to AUD
        Schema::table('orders', function (Blueprint $table) {
            $table->string('currency', 10)->default('AUD')->change();
        });

        // 3. Inspect existing orders and transactions without altering historical data
        $nonAudOrdersCount = DB::table('orders')
            ->whereNotNull('currency')
            ->where('currency', '!=', 'AUD')
            ->count();

        if ($nonAudOrdersCount > 0) {
            Log::info("AUD Currency Migration: Found {$nonAudOrdersCount} historical non-AUD order(s). Preserving their original currency tags.");
        }

        // 4. Update any orders that have a null currency to the new store default AUD
        DB::table('orders')->whereNull('currency')->update(['currency' => 'AUD']);
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('currency')->nullable()->change();
        });
    }
};
