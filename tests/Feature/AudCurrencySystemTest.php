<?php

namespace Tests\Feature;

use App\Helpers\CartManagement;
use App\Livewire\CheckoutPage;
use App\Mail\BrandedMessageMail;
use App\Models\Address;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Order;
use App\Models\PaymentGateway;
use App\Models\Product;
use App\Models\Setting;
use App\Models\ShippingRate;
use App\Models\User;
use App\Services\Payment\Drivers\StripeDriver;
use App\Services\Payment\PaymentGatewayManager;
use App\Support\Money;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AudCurrencySystemTest extends TestCase
{
    use DatabaseTransactions;

    protected Category $category;
    protected Brand $brand;

    protected function setUp(): void
    {
        parent::setUp();

        Setting::set('default_currency', 'AUD');
        Setting::set('currency_symbol', '$');

        $this->category = Category::firstOrCreate(
            ['slug' => 'demo-category'],
            ['name' => 'Demo Category', 'is_active' => true]
        );

        $this->brand = Brand::firstOrCreate(
            ['slug' => 'demo-brand'],
            ['name' => 'Demo Brand', 'is_active' => true]
        );

        ShippingRate::firstOrCreate(
            ['shipping_method' => 'Standard Delivery', 'zone_name' => 'Sydney Metro'],
            ['cost' => 10.00, 'is_active' => true]
        );

        PaymentGateway::updateOrCreate(
            ['code' => 'stripe'],
            [
                'name' => 'Credit / Debit Card (Stripe)',
                'driver' => 'stripe',
                'is_active' => true,
                'environment' => 'sandbox',
                'priority' => 1,
                'is_default' => true,
                'health_status' => 'healthy',
                'credentials' => [
                    'publishable_key' => 'pk_test_sample',
                    'secret_key' => 'sk_test_sample',
                ],
            ]
        );

        PaymentGateway::updateOrCreate(
            ['code' => 'cod'],
            [
                'name' => 'Cash on Delivery',
                'driver' => 'cod',
                'is_active' => true,
                'environment' => 'live',
                'priority' => 2,
                'is_default' => false,
                'health_status' => 'healthy',
            ]
        );
    }

    #[Test]
    public function money_formats_aud_prices_with_dollar_symbol_only_and_two_decimals(): void
    {
        $this->assertSame('$100.00', Money::format(100, 'AUD'));
        $this->assertSame('$1,250.00', Money::format(1250, 'AUD'));
        $this->assertSame('$0.00', Money::format(0, 'AUD'));
        $this->assertSame('$9.95', Money::format(9.95, 'AUD'));
    }

    #[Test]
    public function money_format_with_code_appends_aud(): void
    {
        $this->assertSame('$100.00 AUD', Money::formatWithCode(100, 'AUD'));
        $this->assertSame('$1,250.00 AUD', Money::formatWithCode(1250, 'AUD'));
    }

    #[Test]
    public function money_formats_historical_npr_and_alternative_currencies(): void
    {
        $this->assertSame('Rs. 100.00', Money::format(100, 'NPR'));
        $this->assertSame('$65.00 USD', Money::format(65, 'USD'));
    }

    #[Test]
    public function currency_switcher_route_updates_session_and_cookies(): void
    {
        $response = $this->get(route('currency.switch', 'USD'));
        $response->assertSessionHas('currency', 'USD');
        $response->assertCookie('currency', 'USD');
    }

    #[Test]
    public function footer_displays_australian_dollars_notice_for_aud(): void
    {
        session(['currency' => 'AUD']);
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('All prices are in Australian dollars (AUD).');
    }

    #[Test]
    public function checkout_displays_card_charge_in_aud_and_pay_button_with_aud(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $product = Product::create([
            'category_id' => $this->category->id,
            'brand_id' => $this->brand->id,
            'name' => 'Test AUD Product',
            'slug' => 'test-aud-product',
            'price' => 100.00,
            'sale_price' => 100.00,
            'regular_price' => 120.00,
            'sku' => 'TEST-AUD-1',
            'is_active' => true,
            'in_stock' => true,
        ]);

        $shippingRate = ShippingRate::first();

        CartManagement::addCartItemsToCookie([
            [
                'product_id' => $product->id,
                'name' => $product->name,
                'image' => '',
                'quantity' => 1,
                'unit_amount' => 100.00,
                'total_amount' => 100.00,
            ],
        ]);

        Livewire::test(CheckoutPage::class)
            ->set('shipping_rate_id', $shippingRate->id)
            ->assertSee('Your card will be charged in Australian dollars (AUD).')
            ->assertSee('Pay $110.00 AUD');
    }

    #[Test]
    public function checkout_processes_order_in_aud_and_validates_amounts_server_side(): void
    {
        Mail::fake();
        config(['cache.default' => 'array']);

        $user = User::factory()->create();
        $this->actingAs($user);

        $product = Product::create([
            'category_id' => $this->category->id,
            'brand_id' => $this->brand->id,
            'name' => 'Authentic AUD Bag',
            'slug' => 'authentic-aud-bag',
            'price' => 50.00,
            'sale_price' => 50.00,
            'regular_price' => 60.00,
            'sku' => 'BAG-001',
            'is_active' => true,
            'in_stock' => true,
        ]);

        $shippingRate = ShippingRate::first();

        // Customer attempts to submit tampered cart with $1 instead of $50
        $tamperedCart = [
            [
                'product_id' => $product->id,
                'name' => $product->name,
                'image' => '',
                'quantity' => 2,
                'unit_amount' => 1.00, // Tampered client price
                'total_amount' => 2.00,
            ],
        ];

        CartManagement::addCartItemsToCookie($tamperedCart);

        $component = Livewire::test(CheckoutPage::class)
            ->set('first_name', 'Jane')
            ->set('last_name', 'Doe')
            ->set('phone', '+61412345678')
            ->set('street_address', '100 George St')
            ->set('city', 'Sydney')
            ->set('state', 'NSW')
            ->set('zip_code', '2000')
            ->set('payment_method', 'cod')
            ->set('shipping_rate_id', $shippingRate->id)
            ->call('placeOrder');

        // Extract confirmation code from email
        $code = null;
        Mail::assertSent(BrandedMessageMail::class, function (BrandedMessageMail $mail) use (&$code, $user): bool {
            $code = $mail->details['Confirmation code'] ?? null;

            return $mail->hasTo($user->email) && is_string($code);
        });

        $this->assertNotNull($code);

        // Verify OTP and complete order
        $component->set('orderOtp', $code)
            ->call('verifyOrderOtp');

        // Order created on server must recalculate 2 * $50 = $100 + $10 shipping = $110, in AUD
        $order = Order::where('user_id', $user->id)->first();
        $this->assertNotNull($order);
        $this->assertSame('AUD', $order->currency);
        $this->assertEquals(110.00, $order->grand_total);
        $this->assertEquals(10.00, $order->shipping_amount);
    }

    #[Test]
    public function stripe_driver_supports_aud_and_submits_card_payments_in_cents(): void
    {
        $user = User::factory()->create();
        $order = Order::create([
            'user_id' => $user->id,
            'grand_total' => 125.50,
            'payment_method' => 'stripe',
            'payment_status' => 'pending',
            'status' => 'new',
            'currency' => 'AUD',
            'shipping_amount' => 10.00,
            'shipping_method' => 'Standard',
        ]);

        $recordedRequest = null;
        Http::fake([
            'https://api.stripe.com/v1/checkout/sessions' => function ($request) use (&$recordedRequest) {
                $recordedRequest = $request;

                return Http::response([
                    'id' => 'cs_test_session_123456',
                    'url' => 'https://checkout.stripe.com/pay/cs_test_session_123456',
                ], 200);
            },
        ]);

        $driver = app(PaymentGatewayManager::class)->driver('stripe');
        $this->assertInstanceOf(StripeDriver::class, $driver);

        $response = $driver->purchase($order);

        $this->assertTrue($response->success);
        $this->assertSame('pending', $response->status);
        $this->assertSame('cs_test_session_123456', $response->transactionId);
        $this->assertSame('https://checkout.stripe.com/pay/cs_test_session_123456', $response->redirectUrl);

        // Verify payload explicitly sent AUD and 12550 cents
        $this->assertNotNull($recordedRequest);
        $data = $recordedRequest->data();
        $this->assertSame('aud', $data['line_items'][0]['price_data']['currency']);
        $this->assertEquals(12550, $data['line_items'][0]['price_data']['unit_amount']);
    }
}
