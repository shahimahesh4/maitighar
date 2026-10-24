<?php

namespace App\Livewire;

use App\Events\PaymentReceived;
use App\Helpers\CartManagement;
use App\Livewire\Partials\Navbar;
use App\Models\Address;
use App\Models\Coupon;
use App\Models\InventoryLog;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\PaymentTransaction;
use App\Models\Product;
use App\Models\ProductVariation;
use App\Models\ShippingRate;
use App\Services\Payment\PaymentGatewayManager;
use App\Services\OrderConfirmationOtpService;
use App\Services\OrderConfirmationService;
use App\Support\Money;
use App\Traits\GoogleRecaptcha;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Title('Checkout - Maitighar')]
class CheckoutPage extends Component
{
    use GoogleRecaptcha;
    use WithFileUploads;

    public $cart_items = [];

    public $grand_total = 0;

    // Address fields
    public $first_name;

    public $last_name;

    public $phone;

    public $street_address;

    public $city;

    public $state;

    public $zip_code;

    public $payment_method = 'cod'; // default

    public $active_gateways = [];

    public $shipping_rates = [];

    public $shipping_rate_id;

    public float $shipping_amount = 0.0;

    public string $shipping_method = '';

    public $payment_reference;

    public $payment_voucher;

    // Coupon fields
    public $coupon_code;

    public $discount_amount = 0;

    public $applied_coupon;

    public bool $awaitingOrderOtp = false;

    public string $orderOtp = '';

    public function mount()
    {
        if (! auth()->check()) {
            return redirect()->route('login');
        }

        $this->cart_items = CartManagement::getCartItemsFromCookie();
        if (count($this->cart_items) == 0) {
            return redirect()->route('all-products');
        }

        $this->grand_total = CartManagement::calculateGrandTotal($this->cart_items);

        $this->shipping_rates = ShippingRate::query()
            ->where('is_active', true)
            ->orderBy('cost')
            ->orderBy('min_delivery_days')
            ->get()
            ->toArray();
        $this->shipping_rate_id = null;

        // Prepopulate with user details if possible
        $user = auth()->user();
        $nameParts = explode(' ', $user->name, 2);
        $this->first_name = $nameParts[0] ?? '';
        $this->last_name = $nameParts[1] ?? '';
        $this->phone = $user->phone;
        $this->street_address = $user->address;
        $this->city = $user->city;
        $this->state = $user->state;
        $this->zip_code = $user->postal_code;

        // Load active payment gateways
        $manager = app(PaymentGatewayManager::class);
        $this->active_gateways = $manager->getActiveGateways()->toArray();
        $this->payment_method = $manager->getDefaultGatewayCode();
    }

    public function applyCoupon()
    {
        if (empty($this->coupon_code)) {
            session()->flash('coupon_error', 'Please enter a promo code.');

            return;
        }

        $coupon = Coupon::where('code', strtoupper($this->coupon_code))->first();

        if (! $coupon) {
            session()->flash('coupon_error', 'Invalid promo code.');
            $this->discount_amount = 0;
            $this->applied_coupon = null;
            $this->recalculateTotal();

            return;
        }

        if (! $coupon->isValid()) {
            session()->flash('coupon_error', 'This promo code is inactive or expired.');
            $this->discount_amount = 0;
            $this->applied_coupon = null;
            $this->recalculateTotal();

            return;
        }

        $subtotal = CartManagement::calculateGrandTotal($this->cart_items);

        if (! $coupon->isValidForAmount($subtotal)) {
            session()->flash('coupon_error', 'Minimum order value of '.Money::format($coupon->min_amount).' required.');
            $this->discount_amount = 0;
            $this->applied_coupon = null;
            $this->recalculateTotal();

            return;
        }

        $this->discount_amount = $coupon->calculateDiscount($subtotal);
        $this->applied_coupon = $coupon->code;
        $this->recalculateTotal();

        session()->flash('coupon_success', 'Promo code applied successfully!');
    }

    public function recalculateTotal()
    {
        $subtotal = CartManagement::calculateGrandTotal($this->cart_items);
        $this->grand_total = max(0, $subtotal - $this->discount_amount) + $this->shipping_amount;
    }

    public function updatedShippingRateId(): void
    {
        $this->syncShippingRate();
    }

    public function placeOrder()
    {
        $activeCodes = collect($this->active_gateways)->pluck('code')->implode(',');

        $rules = [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'street_address' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'zip_code' => 'required|string|max:10',
            'payment_method' => 'required|string|in:'.($activeCodes ?: 'cod'),
            'shipping_rate_id' => ['required', 'integer', 'exists:shipping_rates,id'],
        ];

        if ($this->payment_method === 'bank_transfer') {
            $rules['payment_reference'] = 'required|string|max:100';
            $rules['payment_voucher'] = 'required|image|mimes:jpg,jpeg,png,webp|max:5120';
        }

        $rules = array_merge($this->getRecaptchaRules(), $rules);
        try {
            $this->validate($rules);
        } catch (ValidationException $exception) {
            $this->setErrorBag($exception->validator->errors());
            $this->dispatch('checkout-validation-failed');

            return;
        }

        if (! $this->verifyRecaptcha()) {
            $this->dispatch('checkout-validation-failed');

            return;
        }

        $otpService = app(OrderConfirmationOtpService::class);
        if (! $otpService->pending(auth()->user())) {
            $otpService->send(auth()->user());
        }
        $this->awaitingOrderOtp = true;
        $this->reset('orderOtp');
        session()->flash('order_otp_status', 'A six-digit confirmation code was sent to '.auth()->user()->email.'.');
        $this->dispatch('order-otp-ready');
    }

    public function verifyOrderOtp()
    {
        $validated = $this->validate([
            'orderOtp' => ['required', 'digits:6'],
        ]);

        if (! app(OrderConfirmationOtpService::class)->verify(auth()->user(), $validated['orderOtp'])) {
            $this->addError('orderOtp', 'The code is invalid, expired, or has exceeded the attempt limit.');
            $this->dispatch('order-otp-ready');

            return null;
        }

        $this->awaitingOrderOtp = false;

        return $this->completeOrder();
    }

    public function resendOrderOtp(): void
    {
        app(OrderConfirmationOtpService::class)->send(auth()->user());
        $this->reset('orderOtp');
        session()->flash('order_otp_status', 'A new order confirmation code was sent.');
        $this->dispatch('order-otp-ready');
    }

    private function completeOrder()
    {
        $this->syncShippingRate(failIfMissing: true);

        // Server-side validation and recalculation of payable amount in AUD
        $validatedCartItems = [];
        $recalculatedSubtotal = 0.0;
        foreach ($this->cart_items as $item) {
            $product = Product::where('id', $item['product_id'])->where('is_active', true)->first();
            if (! $product) {
                session()->flash('error', 'One or more items in your cart are no longer available.');

                return redirect()->route('cart-products');
            }

            $variationId = ! empty($item['variation_id']) ? (int) $item['variation_id'] : null;
            $qty = max(1, (int) $item['quantity']);

            if ($variationId) {
                $variation = ProductVariation::with('product')
                    ->where('id', $variationId)
                    ->where('product_id', $product->id)
                    ->where('is_enabled', true)
                    ->first();

                if (! $variation) {
                    session()->flash('error', "A variation of {$product->name} is no longer available.");

                    return redirect()->route('cart-products');
                }

                if ($variation->manage_stock && $variation->stock_quantity < $qty) {
                    session()->flash('error', "Only {$variation->stock_quantity} available for {$product->name} ({$variation->combination_label}).");

                    return redirect()->route('cart-products');
                }

                $unitAmount = (float) $variation->effective_price;
                $variationSku = $variation->sku ?: $product->sku;
                $optionsText = $variation->combination_label;
            } else {
                if ($product->stock_quantity > 0 && $product->stock_quantity < $qty) {
                    session()->flash('error', "Only {$product->stock_quantity} available for {$product->name}.");

                    return redirect()->route('cart-products');
                }

                $unitAmount = (float) $product->sale_price;
                $variationSku = $product->sku;
                $optionsText = null;
            }

            $totalAmount = round($unitAmount * $qty, 2);
            $recalculatedSubtotal += $totalAmount;
            $validatedCartItems[] = [
                'product_id' => $product->id,
                'variation_id' => $variationId,
                'variation_sku' => $variationSku,
                'options_text' => $optionsText,
                'quantity' => $qty,
                'unit_amount' => $unitAmount,
                'total_amount' => $totalAmount,
            ];
        }

        // Validate and recalculate discount
        $recalculatedDiscount = 0.0;
        if ($this->applied_coupon) {
            $coupon = Coupon::where('code', $this->applied_coupon)->first();
            if ($coupon && $coupon->isValid() && $coupon->isValidForAmount($recalculatedSubtotal)) {
                $recalculatedDiscount = (float) $coupon->calculateDiscount($recalculatedSubtotal);
            }
        }

        // Validate shipping
        $shippingRate = ShippingRate::find($this->shipping_rate_id);
        $recalculatedShipping = $shippingRate ? (float) $shippingRate->cost : 0.0;

        $finalPayableAmount = round(max(0, $recalculatedSubtotal - $recalculatedDiscount) + $recalculatedShipping, 2);
        $this->grand_total = $finalPayableAmount;
        $this->discount_amount = $recalculatedDiscount;
        $this->shipping_amount = $recalculatedShipping;
        $this->cart_items = $validatedCartItems;

        $currency = 'AUD';

        // Create the order
        $order = DB::transaction(function () use ($finalPayableAmount, $recalculatedShipping, $recalculatedDiscount, $validatedCartItems, $currency) {
            $order = Order::create([
                'user_id' => auth()->id(),
                'grand_total' => $finalPayableAmount,
                'payment_method' => $this->payment_method,
                'payment_status' => 'pending', // all start pending, completed by driver callback/webhook
                'status' => 'new',
                'currency' => $currency,
                'shipping_amount' => $recalculatedShipping,
                'shipping_method' => $this->shipping_method,
                'notes' => 'Placed via checkout page.',
                'coupon_code' => $this->applied_coupon,
                'discount_amount' => $recalculatedDiscount,
            ]);

            // Create order items and deduct stock atomically
            foreach ($validatedCartItems as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'variation_id' => $item['variation_id'],
                    'variation_sku' => $item['variation_sku'],
                    'options_text' => $item['options_text'],
                    'quantity' => $item['quantity'],
                    'unit_amount' => $item['unit_amount'],
                    'total_amount' => $item['total_amount'],
                ]);

                if (! empty($item['variation_id'])) {
                    $lockedVar = ProductVariation::where('id', $item['variation_id'])->lockForUpdate()->first();
                    if ($lockedVar && $lockedVar->manage_stock) {
                        $lockedVar->decrement('stock_quantity', $item['quantity']);
                    }
                    InventoryLog::create([
                        'product_id' => $item['product_id'],
                        'variation_id' => $item['variation_id'],
                        'user_id' => auth()->id(),
                        'quantity_change' => -$item['quantity'],
                        'reason' => 'Customer Purchase (Order #'.$order->id.')',
                    ]);
                    $lockedVar?->product?->syncPriceAndStockFromVariations();
                } else {
                    $lockedProd = Product::where('id', $item['product_id'])->lockForUpdate()->first();
                    if ($lockedProd) {
                        $lockedProd->decrement('stock_quantity', $item['quantity']);
                        if ($lockedProd->stock_quantity <= 0) {
                            $lockedProd->update(['in_stock' => false]);
                        }
                    }
                    InventoryLog::create([
                        'product_id' => $item['product_id'],
                        'variation_id' => null,
                        'user_id' => auth()->id(),
                        'quantity_change' => -$item['quantity'],
                        'reason' => 'Customer Purchase (Order #'.$order->id.')',
                    ]);
                }
            }

            // Create address
            Address::create([
                'order_id' => $order->id,
                'first_name' => $this->first_name,
                'last_name' => $this->last_name,
                'phone' => $this->phone,
                'street_address' => $this->street_address,
                'city' => $this->city,
                'state' => $this->state,
                'zip_code' => $this->zip_code,
            ]);

            return $order;
        });

        app(OrderConfirmationService::class)->send($order);

        $voucherPath = null;
        if ($this->payment_method === 'bank_transfer') {
            $voucherPath = $this->payment_voucher->store('payment-vouchers', 'local');
        }
        // Initialize payment via Gateway Manager
        $manager = app(PaymentGatewayManager::class);

        try {
            $driver = $manager->driver($this->payment_method);

            // Log pending transaction
            $transaction = PaymentTransaction::create([
                'order_id' => $order->id,
                'user_id' => auth()->id(),
                'gateway_code' => $this->payment_method,
                'amount' => $order->grand_total,
                'currency' => $order->currency ?? 'AUD',
                'status' => 'pending',
                'type' => 'payment',
                'payment_reference' => $this->payment_reference,
                'voucher_path' => $voucherPath,
                'proof_submitted_at' => $voucherPath ? now() : null,
            ]);

            $response = $driver->purchase($order);

            if ($response->success) {
                // Clear the cart
                CartManagement::clearCartItems();
                $this->dispatch('update-cart-count', total_count: 0);

                // Update transaction ID with the one returned by gateway
                if ($response->transactionId) {
                    $transaction->update(['gateway_transaction_id' => $response->transactionId]);
                }

                // Handle redirect or final success routing
                if ($response->redirectUrl) {
                    return redirect($response->redirectUrl);
                }

                // If COD or processes instantly
                if ($this->payment_method === 'cod') {
                    $order->update([
                        'payment_status' => 'pending',
                    ]);
                    event(new PaymentReceived($order));
                }

                return redirect()->route('success', ['order_id' => $order->id]);
            }

            // Primary gateway purchase failed. Log error and trigger automated fallback routing.
            $transaction->update([
                'status' => 'failed',
                'error_message' => $response->errorMessage ?? 'Payment failed',
            ]);

            $fallbackResponse = $manager->fallbackRoute($this->payment_method, $order);
            if ($fallbackResponse && $fallbackResponse->success) {
                CartManagement::clearCartItems();
                $this->dispatch('update-cart-count', total_count: 0);

                session()->flash('warning', 'Primary payment gateway failed. Auto-routed to fallback gateway.');

                if ($fallbackResponse->redirectUrl) {
                    return redirect($fallbackResponse->redirectUrl);
                }

                return redirect()->route('success', ['order_id' => $order->id]);
            }

            session()->flash('coupon_error', $response->errorMessage ?? 'Payment processing failed. Please try again.');

            return;

        } catch (\Exception $e) {
            Log::error('Checkout Order placement exception: '.$e->getMessage());

            // Attempt fallback on exception
            $fallbackResponse = $manager->fallbackRoute($this->payment_method, $order);
            if ($fallbackResponse && $fallbackResponse->success) {
                CartManagement::clearCartItems();
                $this->dispatch('update-cart-count', total_count: 0);
                session()->flash('warning', 'Gateway connection error. Auto-routed to fallback.');
                if ($fallbackResponse->redirectUrl) {
                    return redirect($fallbackResponse->redirectUrl);
                }

                return redirect()->route('success', ['order_id' => $order->id]);
            }

            session()->flash('coupon_error', 'An error occurred during payment setup: '.$e->getMessage());

            return;
        }
    }

    public function render(): View
    {
        $selectedGateway = collect($this->active_gateways)->firstWhere('code', $this->payment_method);

        return view('livewire.checkout-page', [
            'cartQuantity' => collect($this->cart_items)->sum('quantity'),
            'subtotal' => CartManagement::calculateGrandTotal($this->cart_items),
            'paymentSettings' => $selectedGateway['settings'] ?? [],
            'checkoutItems' => collect($this->cart_items)
                ->map(fn (array $item): array => [
                    ...$item,
                    'image_url' => $this->resolveImageUrl($item['image'] ?? null),
                ])
                ->all(),
        ]);
    }

    private function syncShippingRate(bool $failIfMissing = false): void
    {
        $rate = ShippingRate::query()
            ->where('is_active', true)
            ->find($this->shipping_rate_id);

        if (! $rate) {
            $this->shipping_amount = 0.0;
            $this->shipping_method = '';
            $this->recalculateTotal();

            if ($failIfMissing) {
                throw ValidationException::withMessages([
                    'shipping_rate_id' => 'Select an available delivery option.',
                ]);
            }

            return;
        }

        $this->shipping_amount = (float) $rate->cost;
        $this->shipping_method = $rate->shipping_method.' — '.$rate->zone_name;
        $this->recalculateTotal();
    }

    private function resolveImageUrl(?string $image): string
    {
        return match (true) {
            blank($image) => asset('images/logo.png'),
            str_starts_with($image, 'http://'), str_starts_with($image, 'https://') => $image,
            str_starts_with($image, '/') => url($image),
            str_starts_with($image, 'images/') => asset($image),
            default => Storage::disk('public')->url($image),
        };
    }
}
