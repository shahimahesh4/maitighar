<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('payment_transactions')
            ->whereNotNull('voucher_path')
            ->orderBy('id')
            ->each(function (object $transaction): void {
                $path = (string) $transaction->voucher_path;

                if (Storage::disk('local')->exists($path) || ! Storage::disk('public')->exists($path)) {
                    return;
                }

                Storage::disk('local')->put($path, Storage::disk('public')->get($path));
                Storage::disk('public')->delete($path);
            });
    }

    public function down(): void
    {
        // Receipts intentionally remain private when rolling back.
    }
};
