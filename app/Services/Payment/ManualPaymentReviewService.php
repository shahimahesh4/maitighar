<?php

namespace App\Services\Payment;

use App\Events\PaymentFailed;
use App\Events\PaymentReceived;
use App\Models\PaymentTransaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ManualPaymentReviewService
{
    public function approve(PaymentTransaction $transaction, int $adminId): void
    {
        $this->ensureReviewable($transaction);
        DB::transaction(function () use ($transaction, $adminId) {
            $transaction->update(['status' => 'completed', 'verified_at' => now(), 'verified_by' => $adminId, 'rejection_reason' => null, 'error_message' => null]);
            $transaction->order->update(['payment_status' => 'paid', 'status' => 'processing']);
        });
        event(new PaymentReceived($transaction->order->fresh()));
    }

    public function reject(PaymentTransaction $transaction, int $adminId, string $reason): void
    {
        $this->ensureReviewable($transaction);
        DB::transaction(function () use ($transaction, $adminId, $reason) {
            $transaction->update(['status' => 'rejected', 'verified_at' => now(), 'verified_by' => $adminId, 'rejection_reason' => $reason, 'error_message' => $reason]);
            $transaction->order->update(['payment_status' => 'failed']);
        });
        event(new PaymentFailed($transaction->order->fresh(), $reason));
    }

    private function ensureReviewable(PaymentTransaction $transaction): void
    {
        if ($transaction->gateway_code !== 'bank_transfer' || ! in_array($transaction->status, ['pending', 'pending_verification'], true) || blank($transaction->voucher_path) || blank($transaction->payment_reference)) {
            throw ValidationException::withMessages(['payment' => 'This transaction does not have a complete, pending manual payment proof.']);
        }
    }
}
