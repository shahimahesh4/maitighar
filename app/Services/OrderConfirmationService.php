<?php

namespace App\Services;

use App\Mail\BrandedMessageMail;
use App\Models\Order;
use App\Support\Money;
use Illuminate\Support\Facades\Mail;

class OrderConfirmationService
{
    public function send(Order $order): void
    {
        $order->loadMissing(['user', 'address', 'items.product']);
        if ($order->confirmation_sent_at || ! $order->user?->email) {
            return;
        }

        $items = $order->items->map(function ($item): string {
            $name = $item->product?->name ?? 'Product';
            if (! empty($item->options_text)) {
                $name .= ' ('.$item->options_text.')';
            }

            return $name.' × '.$item->quantity.' — '.Money::format($item->total_amount);
        })->implode("\n");

        $address = $order->address;
        $deliveryAddress = $address
            ? implode(', ', array_filter([$address->street_address, $address->city, $address->state, $address->zip_code]))
            : 'Not provided';
        $subtotal = $order->items->sum(fn ($item): float => (float) $item->total_amount);

        try {
            $message = Mail::to($order->user->email);
            $storeEmail = app(CommunicationService::class)->store()['email'] ?? null;
            if (filter_var($storeEmail, FILTER_VALIDATE_EMAIL) && strcasecmp($storeEmail, $order->user->email) !== 0) {
                $message->cc($storeEmail);
            }

            $message->send(new BrandedMessageMail(
                'Order #'.$order->id.' confirmed',
                'Thank you for your purchase',
                'Your OTP was verified and your order has been confirmed. Here is your purchase summary.',
                [
                    'Order number' => '#'.$order->id,
                    'Customer' => $order->user->name,
                    'Phone' => $address?->phone ?? $order->user->phone,
                    'Delivery address' => $deliveryAddress,
                    'Shipping method' => $order->shipping_method ?: 'Standard delivery',
                    'Payment method' => $this->paymentLabel((string) $order->payment_method),
                    'Payment status' => ucfirst((string) $order->payment_status),
                    'Currency' => strtoupper($order->currency ?: 'AUD').' (Australian Dollars)',
                    'Subtotal' => Money::format($subtotal, $order->currency).' '.strtoupper($order->currency ?: 'AUD'),
                    'Discount' => Money::format($order->discount_amount, $order->currency).' '.strtoupper($order->currency ?: 'AUD'),
                    'Shipping' => Money::format($order->shipping_amount, $order->currency).' '.strtoupper($order->currency ?: 'AUD'),
                    'Order total' => Money::format($order->grand_total, $order->currency).' '.strtoupper($order->currency ?: 'AUD'),
                ],
                'View your order',
                route('my-order-details', ['order' => $order->id]),
                "Products purchased:\n\n{$items}\n\nWe will notify you when the payment or fulfilment status changes.",
            ));

            $order->forceFill(['confirmation_sent_at' => now()])->save();
        } catch (\Throwable $exception) {
            report($exception);
        }
    }

    private function paymentLabel(string $method): string
    {
        return match ($method) {
            'cod' => 'Cash on delivery',
            'bank_transfer' => 'Bank transfer',
            default => ucfirst(str_replace('_', ' ', $method)),
        };
    }
}
