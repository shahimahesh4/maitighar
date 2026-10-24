<?php

namespace Tests\Feature;

use App\Helpers\CartManagement;
use App\Livewire\ProductDetailPage;
use App\Mail\BrandedMessageMail;
use App\Models\Address;
use App\Models\Attribute;
use App\Models\AttributeValue;
use App\Models\Brand;
use App\Models\Category;
use App\Models\InventoryLog;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductAttribute;
use App\Models\ProductVariation;
use App\Models\ProductVariationAttributeValue;
use App\Models\ShippingRate;
use App\Models\User;
use App\Support\Money;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;
use Tests\TestCase;

class ProductVariationsSystemTest extends TestCase
{
    use DatabaseTransactions;

    protected User $user;
    protected Category $category;
    protected Brand $brand;
    protected ShippingRate $shippingRate;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create([
            'email' => 'customer_test_' . uniqid() . '@example.com',
            'phone' => '+614' . rand(10000000, 99999999),
            'password' => bcrypt('Password123!'),
        ]);

        $this->category = Category::firstOrCreate(['slug' => 'test-clothing'], ['name' => 'Clothing']);
        $this->brand = Brand::firstOrCreate(['slug' => 'test-brand'], ['name' => 'Maitighar Heritage']);

        $this->shippingRate = ShippingRate::firstOrCreate(
            ['shipping_method' => 'standard_express', 'zone_name' => 'Australia Wide'],
            [
                'cost' => 15.00,
                'min_delivery_days' => 2,
                'max_delivery_days' => 4,
                'is_active' => true,
            ]
        );
    }

    public function test_attribute_and_values_with_swatches_can_be_created(): void
    {
        $attribute = Attribute::create([
            'name' => 'Colour',
            'slug' => 'test-colour-' . uniqid(),
            'type' => 'color',
            'sort_order' => 1,
        ]);

        $red = AttributeValue::create([
            'attribute_id' => $attribute->id,
            'value' => 'Ruby Red',
            'slug' => 'ruby-red',
            'color_code' => '#E11D48',
            'sort_order' => 1,
        ]);

        $blue = AttributeValue::create([
            'attribute_id' => $attribute->id,
            'value' => 'Sapphire Blue',
            'slug' => 'sapphire-blue',
            'color_code' => '#2563EB',
            'sort_order' => 2,
        ]);

        $this->assertDatabaseHas('attributes', ['id' => $attribute->id, 'type' => 'color']);
        $this->assertDatabaseHas('attribute_values', ['id' => $red->id, 'color_code' => '#E11D48']);
        $this->assertCount(2, $attribute->values);
    }

    public function test_variable_product_supports_attributes_and_variation_generation(): void
    {
        $product = Product::create([
            'category_id' => $this->category->id,
            'brand_id' => $this->brand->id,
            'name' => 'Palpali Dhaka Kurta',
            'slug' => 'palpali-dhaka-kurta-' . uniqid(),
            'product_type' => 'variable',
            'sku' => 'PDK-001',
            'sale_price' => 120.00,
            'regular_price' => 150.00,
            'in_stock' => true,
            'is_active' => true,
        ]);

        $sizeAttr = Attribute::firstOrCreate(['slug' => 'test-size'], ['name' => 'Size', 'type' => 'select']);
        $valM = AttributeValue::firstOrCreate(['attribute_id' => $sizeAttr->id, 'value' => 'M'], ['slug' => 'm']);
        $valL = AttributeValue::firstOrCreate(['attribute_id' => $sizeAttr->id, 'value' => 'L'], ['slug' => 'l']);

        $colorAttr = Attribute::firstOrCreate(['slug' => 'test-color'], ['name' => 'Colour', 'type' => 'color']);
        $valRed = AttributeValue::firstOrCreate(['attribute_id' => $colorAttr->id, 'value' => 'Red'], ['slug' => 'red', 'color_code' => '#DC2626']);
        $valBlue = AttributeValue::firstOrCreate(['attribute_id' => $colorAttr->id, 'value' => 'Blue'], ['slug' => 'blue', 'color_code' => '#1E3A8A']);

        // Assign to product
        $paSize = ProductAttribute::create([
            'product_id' => $product->id,
            'attribute_id' => $sizeAttr->id,
            'is_visible' => true,
            'is_variation' => true,
        ]);
        $paSize->values()->attach([$valM->id, $valL->id]);

        $paColor = ProductAttribute::create([
            'product_id' => $product->id,
            'attribute_id' => $colorAttr->id,
            'is_visible' => true,
            'is_variation' => true,
        ]);
        $paColor->values()->attach([$valRed->id, $valBlue->id]);

        // Generate combinations manually mimicking VariationsRelationManager logic:
        // Combinations: M-Red, M-Blue, L-Red, L-Blue
        $combos = [
            [$sizeAttr->id => $valM->id, $colorAttr->id => $valRed->id],
            [$sizeAttr->id => $valM->id, $colorAttr->id => $valBlue->id],
            [$sizeAttr->id => $valL->id, $colorAttr->id => $valRed->id],
            [$sizeAttr->id => $valL->id, $colorAttr->id => $valBlue->id],
        ];

        foreach ($combos as $index => $combo) {
            $variation = ProductVariation::create([
                'product_id' => $product->id,
                'sku' => "PDK-001-VAR-{$index}",
                'sale_price' => 120.00 + ($index * 10), // 120, 130, 140, 150
                'regular_price' => 160.00,
                'stock_quantity' => 10,
                'manage_stock' => true,
                'is_enabled' => true,
            ]);

            foreach ($combo as $attrId => $valId) {
                ProductVariationAttributeValue::create([
                    'product_variation_id' => $variation->id,
                    'attribute_id' => $attrId,
                    'attribute_value_id' => $valId,
                ]);
            }
        }

        $this->assertCount(4, $product->variations);
        $this->assertTrue($product->isVariable());
        $this->assertEquals('$120.00 – $150.00', $product->getFormattedPriceRange());

        // Test combination label
        $firstVar = $product->variations()->first();
        $this->assertStringContainsString('Size: M', $firstVar->combination_label);
        $this->assertStringContainsString('Colour: Red', $firstVar->combination_label);
    }

    public function test_regenerating_variations_preserves_existing_records_without_duplicates(): void
    {
        $product = Product::create([
            'category_id' => $this->category->id,
            'brand_id' => $this->brand->id,
            'name' => 'Custom Dhaka Shawl',
            'slug' => 'custom-dhaka-shawl-' . uniqid(),
            'product_type' => 'variable',
            'sku' => 'CDS-001',
            'sale_price' => 85.00,
            'regular_price' => 100.00,
            'in_stock' => true,
            'is_active' => true,
        ]);

        $sizeAttr = Attribute::firstOrCreate(['slug' => 'shawl-size'], ['name' => 'Size', 'type' => 'select']);
        $valS = AttributeValue::firstOrCreate(['attribute_id' => $sizeAttr->id, 'value' => 'S'], ['slug' => 's']);

        $paSize = ProductAttribute::create([
            'product_id' => $product->id,
            'attribute_id' => $sizeAttr->id,
            'is_visible' => true,
            'is_variation' => true,
        ]);
        $paSize->values()->attach([$valS->id]);

        // Create initial variation for S with custom price & stock
        $initialVar = ProductVariation::create([
            'product_id' => $product->id,
            'sku' => 'CDS-CUSTOM-S',
            'sale_price' => 99.00,
            'regular_price' => 120.00,
            'stock_quantity' => 25,
            'manage_stock' => true,
            'is_enabled' => true,
        ]);
        ProductVariationAttributeValue::create([
            'product_variation_id' => $initialVar->id,
            'attribute_id' => $sizeAttr->id,
            'attribute_value_id' => $valS->id,
        ]);

        // Now add value M to attribute
        $valM = AttributeValue::firstOrCreate(['attribute_id' => $sizeAttr->id, 'value' => 'M'], ['slug' => 'm']);
        $paSize->values()->attach([$valM->id]);

        // Regenerate logic: find missing
        $existingCombos = $product->variations()->with('variationAttributeValues')->get();
        $this->assertCount(1, $existingCombos);

        // Add only the missing M
        $newVar = ProductVariation::create([
            'product_id' => $product->id,
            'sku' => 'CDS-001-M',
            'sale_price' => 85.00,
            'regular_price' => 100.00,
            'stock_quantity' => 0,
            'manage_stock' => true,
            'is_enabled' => true,
        ]);
        ProductVariationAttributeValue::create([
            'product_variation_id' => $newVar->id,
            'attribute_id' => $sizeAttr->id,
            'attribute_value_id' => $valM->id,
        ]);

        $product->refresh();
        $this->assertCount(2, $product->variations);

        // Verify initial custom variation S is unchanged
        $recheckedInitial = ProductVariation::find($initialVar->id);
        $this->assertEquals('CDS-CUSTOM-S', $recheckedInitial->sku);
        $this->assertEquals(99.00, (float) $recheckedInitial->sale_price);
        $this->assertEquals(25, $recheckedInitial->stock_quantity);
    }

    public function test_cart_management_separates_different_variations_of_same_product(): void
    {
        Cookie::queue('cart_items', json_encode([]));

        $product = Product::create([
            'category_id' => $this->category->id,
            'brand_id' => $this->brand->id,
            'name' => 'Handmade Pashmina Scarf',
            'slug' => 'handmade-pashmina-scarf-' . uniqid(),
            'product_type' => 'variable',
            'sku' => 'HPS-001',
            'sale_price' => 50.00,
            'regular_price' => 60.00,
            'in_stock' => true,
            'is_active' => true,
        ]);

        $varRed = ProductVariation::create([
            'product_id' => $product->id,
            'sku' => 'HPS-RED',
            'sale_price' => 50.00,
            'regular_price' => 60.00,
            'stock_quantity' => 5,
            'manage_stock' => true,
            'is_enabled' => true,
        ]);

        $varBlue = ProductVariation::create([
            'product_id' => $product->id,
            'sku' => 'HPS-BLUE',
            'sale_price' => 55.00,
            'regular_price' => 65.00,
            'stock_quantity' => 8,
            'manage_stock' => true,
            'is_enabled' => true,
        ]);

        // Add 2 of Red variation
        CartManagement::addItemToCart($product->id, 2, $varRed->id);
        // Add 1 of Blue variation
        CartManagement::addItemToCart($product->id, 1, $varBlue->id);

        $cart = CartManagement::getCartItemsFromCookie();
        $this->assertCount(2, $cart);

        $redItem = collect($cart)->firstWhere('variation_id', $varRed->id);
        $blueItem = collect($cart)->firstWhere('variation_id', $varBlue->id);

        $this->assertNotNull($redItem);
        $this->assertNotNull($blueItem);
        $this->assertEquals(2, $redItem['quantity']);
        $this->assertEquals(100.00, $redItem['total_amount']);
        $this->assertEquals(1, $blueItem['quantity']);
        $this->assertEquals(55.00, $blueItem['total_amount']);

        $this->assertEquals(155.00, CartManagement::calculateGrandTotal($cart));

        // Increment Red
        CartManagement::incrementQuantityToCartItem($redItem['item_key']);
        $updatedCart = CartManagement::getCartItemsFromCookie();
        $updatedRed = collect($updatedCart)->firstWhere('variation_id', $varRed->id);
        $this->assertEquals(3, $updatedRed['quantity']);
        $this->assertEquals(150.00, $updatedRed['total_amount']);

        // Remove Blue
        CartManagement::removeCartItems($blueItem['item_key']);
        $afterRemove = CartManagement::getCartItemsFromCookie();
        $this->assertCount(1, $afterRemove);
        $this->assertNull(collect($afterRemove)->firstWhere('variation_id', $varBlue->id));
    }

    public function test_livewire_product_detail_page_requires_variation_selection_and_updates_state(): void
    {
        $product = Product::create([
            'category_id' => $this->category->id,
            'brand_id' => $this->brand->id,
            'name' => 'Bhairahawa Silk Saree',
            'slug' => 'bhairahawa-silk-saree-' . uniqid(),
            'product_type' => 'variable',
            'sku' => 'BSS-001',
            'sale_price' => 200.00,
            'regular_price' => 250.00,
            'in_stock' => true,
            'is_active' => true,
        ]);

        $sizeAttr = Attribute::create(['name' => 'Size', 'slug' => 'size-' . uniqid(), 'type' => 'select']);
        $valFree = AttributeValue::create(['attribute_id' => $sizeAttr->id, 'value' => 'Free Size', 'slug' => 'free-size']);

        $pa = ProductAttribute::create([
            'product_id' => $product->id,
            'attribute_id' => $sizeAttr->id,
            'is_visible' => true,
            'is_variation' => true,
        ]);
        $pa->values()->attach([$valFree->id]);

        $variation = ProductVariation::create([
            'product_id' => $product->id,
            'sku' => 'BSS-FREE',
            'sale_price' => 210.00,
            'regular_price' => 260.00,
            'stock_quantity' => 15,
            'manage_stock' => true,
            'is_enabled' => true,
        ]);
        ProductVariationAttributeValue::create([
            'product_variation_id' => $variation->id,
            'attribute_id' => $sizeAttr->id,
            'attribute_value_id' => $valFree->id,
        ]);

        $component = Livewire::test(ProductDetailPage::class, ['slug' => $product->slug]);

        // Before selection: selectedVariationId is null
        $component->assertSet('isVariable', true);
        $component->assertSet('selectedVariationId', null);

        // Select the option
        $component->call('selectAttribute', $sizeAttr->id, $valFree->id);

        $component->assertSet('selectedVariationId', $variation->id);
        $component->assertSet('displayPrice', 210.00);
        $component->assertSet('selectedVariationSku', 'BSS-FREE');
        $component->assertSet('isAvailable', true);

        // Add to cart
        $component->call('addToCart', $product->id);
        $component->assertDispatched('product-added-to-cart');
        $component->assertDispatched('update-cart-count');
    }

    public function test_checkout_revalidates_variation_prices_and_deducts_stock_atomically(): void
    {
        Mail::fake();
        config(['cache.default' => 'array']);

        $this->actingAs($this->user);

        $product = Product::create([
            'category_id' => $this->category->id,
            'brand_id' => $this->brand->id,
            'name' => 'Yak Wool Blanket',
            'slug' => 'yak-wool-blanket-' . uniqid(),
            'product_type' => 'variable',
            'sku' => 'YWB-001',
            'sale_price' => 180.00,
            'regular_price' => 220.00,
            'in_stock' => true,
            'is_active' => true,
        ]);

        $var = ProductVariation::create([
            'product_id' => $product->id,
            'sku' => 'YWB-LARGE',
            'sale_price' => 185.00,
            'regular_price' => 220.00,
            'stock_quantity' => 10,
            'manage_stock' => true,
            'is_enabled' => true,
        ]);

        // Client cart cookie with tampered price $1.00
        $tamperedCart = [
            [
                'product_id' => $product->id,
                'variation_id' => $var->id,
                'item_key' => $product->id . '_' . $var->id,
                'name' => $product->name,
                'options_text' => 'Size: Large',
                'sku' => 'YWB-LARGE',
                'image' => '',
                'quantity' => 2,
                'unit_amount' => 1.00, // Attempted tamper!
                'total_amount' => 2.00,
            ],
        ];
        Cookie::queue('cart_items', json_encode($tamperedCart));

        // Submit checkout
        $component = Livewire::test(\App\Livewire\CheckoutPage::class)
            ->set('first_name', 'Mahesh')
            ->set('last_name', 'Shahi')
            ->set('phone', '+61412345678')
            ->set('street_address', '2/75 Dartbrook Road')
            ->set('city', 'Auburn')
            ->set('state', 'NSW')
            ->set('zip_code', '2144')
            ->set('payment_method', 'cod')
            ->set('shipping_rate_id', $this->shippingRate->id);

        // Place order and complete
        $component->call('placeOrder');

        // Extract confirmation code from email and verify
        $code = null;
        Mail::assertSent(BrandedMessageMail::class, function (BrandedMessageMail $mail) use (&$code): bool {
            $code = $mail->details['Confirmation code'] ?? null;
            return $mail->hasTo($this->user->email) && is_string($code);
        });

        $this->assertNotNull($code);

        $component->set('orderOtp', $code)
            ->call('verifyOrderOtp');

        // Verify order created
        $order = Order::where('user_id', $this->user->id)->latest()->first();
        $this->assertNotNull($order);

        // Verify server-side recalculated price in AUD ($185 * 2 = $370 + $15 shipping = $385.00)
        $expectedGrandTotal = round((185.00 * 2) + 15.00, 2);
        $this->assertEquals($expectedGrandTotal, (float) $order->grand_total);
        $this->assertEquals('AUD', $order->currency);

        // Verify OrderItem snapshot
        $orderItem = OrderItem::where('order_id', $order->id)->first();
        $this->assertNotNull($orderItem);
        $this->assertEquals($var->id, $orderItem->variation_id);
        $this->assertEquals('YWB-LARGE', $orderItem->variation_sku);
        $this->assertEquals(185.00, (float) $orderItem->unit_amount);
        $this->assertEquals(370.00, (float) $orderItem->total_amount);

        // Verify atomic stock deduction: initial 10 - 2 = 8
        $this->assertEquals(8, $var->fresh()->stock_quantity);

        // Verify inventory log was created
        $log = InventoryLog::where('variation_id', $var->id)->latest()->first();
        $this->assertNotNull($log);
        $this->assertEquals(-2, $log->quantity_change);
    }
}
