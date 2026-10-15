<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payment_transactions', function (Blueprint $table) {
            $table->string('payment_reference')->nullable()->after('gateway_transaction_id');
            $table->string('voucher_path')->nullable()->after('payment_reference');
            $table->timestamp('proof_submitted_at')->nullable()->after('voucher_path');
            $table->timestamp('verified_at')->nullable()->after('proof_submitted_at');
            $table->foreignId('verified_by')->nullable()->after('verified_at')->constrained('users')->nullOnDelete();
            $table->text('rejection_reason')->nullable()->after('verified_by');
        });
    }

    public function down(): void
    {
        Schema::table('payment_transactions', function (Blueprint $table) {
            $table->dropConstrainedForeignId('verified_by');
            $table->dropColumn(['payment_reference', 'voucher_path', 'proof_submitted_at', 'verified_at', 'rejection_reason']);
        });
    }
};
