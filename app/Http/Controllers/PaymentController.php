<?php

namespace App\Http\Controllers;

use App\Events\PaymentFailed;
use App\Events\PaymentReceived;
use App\Models\Order;
use App\Models\PaymentTransaction;
use App\Services\Payment\PaymentGatewayManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    protected PaymentGatewayManager $gatewayManager;

    public function __construct(PaymentGatewayManager $gatewayManager)
    {
        $this->gatewayManager = $gatewayManager;
    }

    /**
     * Handle payment redirect callbacks.
     */
    public function callback(Request $request, string $gateway)
    {
        $drivers = config('payment.drivers', []);
        abort_unless(array_key_exists($gateway, $drivers), 404);

        Log::info('Payment callback received.', [
            'gateway' => $gateway,
            'method' => $request->method(),
            'ip' => $request->ip(),
        ]);

        try {
            $result = $this->gatewayManager->driver($gateway)->verifyWebhook($request);
            $order = $result->orderId ? Order::find($result->orderId) : null;

            if ($result->valid && $result->status === 'completed' && $order) {
                $this->markOrderAsPaid($order, $gateway, $result->gatewayTransactionId, $result->rawPayload);

                return redirect()->route('success', ['order_id' => $order->id]);
            }

            if ($order) {
                $this->markOrderAsFailed($order, $gateway, $result->gatewayTransactionId, 'Payment verification failed.');
            }
        } catch (\Throwable $exception) {
            Log::error("Payment callback failed for {$gateway}: {$exception->getMessage()}", ['exception' => $exception]);
        }

        return redirect()->route('cancelled')->with('error', 'Payment failed or was cancelled.');
    }

    /**
     * Handle payment webhook requests.
     */
    public function webhook(Request $request, string $gateway)
    {
        $drivers = config('payment.drivers', []);
        abort_unless(array_key_exists($gateway, $drivers), 404);

        Log::info("Webhook received for gateway: {$gateway}");

        try {
            $driver = $this->gatewayManager->driver($gateway);
            $result = $driver->verifyWebhook($request);

            if (! $result->valid) {
                Log::warning("Invalid webhook signature for {$gateway}.");

                return response()->json(['error' => 'Invalid signature'], 400);
            }

            if ($result->orderId) {
                $order = Order::find($result->orderId);
                if ($order) {
                    if ($result->status === 'completed') {
                        $this->markOrderAsPaid($order, $gateway, $result->gatewayTransactionId, $result->rawPayload);
                    } elseif ($result->status === 'failed') {
                        $this->markOrderAsFailed($order, $gateway, $result->gatewayTransactionId, 'Payment failed via webhook event');
                    } elseif ($result->status === 'refunded') {
                        $this->markOrderAsRefunded($order, $result->rawPayload);
                    }
                }
            }

            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            Log::error("Webhook error for {$gateway}: ".$e->getMessage(), ['exception' => $e]);

            return response()->json(['error' => 'Webhook handling failed'], 500);
        }
    }

    /**
     * Cancel route.
     */
    public function cancel($orderId)
    {
        $order = Order::find($orderId);

        return redirect()->route('cancelled')->with('error', 'Payment was cancelled by the user.');
    }

    /**
     * Helper: Mark order as successfully paid.
     */
    protected function markOrderAsPaid(Order $order, string $gatewayCode, ?string $txnId, array $payload)
    {
        DB::transaction(function () use ($order, $gatewayCode, $txnId, $payload) {
            $order->update([
                'payment_status' => 'paid',
                'status' => 'processing',
            ]);

            // Create or update transaction record
            PaymentTransaction::updateOrCreate(
                [
                    'order_id' => $order->id,
                    'type' => 'payment',
                ],
                [
                    'user_id' => $order->user_id,
                    'gateway_code' => $gatewayCode,
                    'gateway_transaction_id' => $txnId,
                    'amount' => $order->grand_total,
                    'currency' => $order->currency ?? 'NPR',
                    'status' => 'completed',
                    'payload' => $payload,
                ]
            );
        });

        // Trigger Event
        event(new PaymentReceived($order));
    }

    /**
     * Helper: Mark order as failed payment.
     */
    protected function markOrderAsFailed(Order $order, string $gatewayCode, ?string $txnId, string $errorMsg)
    {
        DB::transaction(function () use ($order, $gatewayCode, $txnId, $errorMsg) {
            $order->update([
                'payment_status' => 'failed',
            ]);

            PaymentTransaction::updateOrCreate(
                [
                    'order_id' => $order->id,
                    'type' => 'payment',
                ],
                [
                    'user_id' => $order->user_id,
                    'gateway_code' => $gatewayCode,
                    'gateway_transaction_id' => $txnId,
                    'amount' => $order->grand_total,
                    'currency' => $order->currency ?? 'NPR',
                    'status' => 'failed',
                    'error_message' => $errorMsg,
                ]
            );
        });

        // Trigger Event
        event(new PaymentFailed($order, $errorMsg));
    }

    /**
     * Helper: Mark order as refunded.
     */
    protected function markOrderAsRefunded(Order $order, array $payload)
    {
        DB::transaction(function () use ($order) {
            $order->update([
                'payment_status' => 'refunded',
            ]);

            $transaction = PaymentTransaction::where('order_id', $order->id)->where('type', 'payment')->first();
            if ($transaction) {
                $transaction->update([
                    'status' => 'refunded',
                ]);
            }
        });
    }
}
