<?php

namespace App\Http\Controllers;

use App\Models\PaymentTransaction;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class PaymentVoucherController extends Controller
{
    public function show(PaymentTransaction $transaction): BinaryFileResponse
    {
        abort_unless(
            in_array(auth()->user()?->role, ['super_admin', 'admin'], true),
            403,
        );

        abort_unless(filled($transaction->voucher_path), 404);

        $path = (string) $transaction->voucher_path;
        abort_unless(Storage::disk('local')->exists($path), 404);

        return response()->file(Storage::disk('local')->path($path), [
            'Cache-Control' => 'private, no-store, max-age=0',
            'Content-Disposition' => 'inline; filename="payment-receipt-'.(int) $transaction->id.'.'.pathinfo($path, PATHINFO_EXTENSION).'"',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }
}
