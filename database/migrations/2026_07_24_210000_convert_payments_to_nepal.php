<?php

use App\Models\PaymentGateway;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('orders')->whereRaw('LOWER(currency) = ?', ['inr'])->update(['currency' => 'NPR']);
        DB::table('payment_transactions')->whereRaw('LOWER(currency) = ?', ['inr'])->update(['currency' => 'NPR']);
        PaymentGateway::whereIn('code', ['stripe', 'razorpay', 'paytm'])->delete();
    }

    public function down(): void
    {
        DB::table('orders')->where('currency', 'NPR')->update(['currency' => 'INR']);
        DB::table('payment_transactions')->where('currency', 'NPR')->update(['currency' => 'INR']);
    }
};
