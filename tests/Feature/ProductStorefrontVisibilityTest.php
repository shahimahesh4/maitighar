<?php

namespace Tests\Feature;

use App\Models\Product;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ProductStorefrontVisibilityTest extends TestCase
{
    use DatabaseTransactions;

    public function test_storefront_scope_only_returns_active_in_stock_products(): void
    {
        $visible = Product::factory()->create(['is_active' => true, 'in_stock' => true]);
        $inactive = Product::factory()->create(['is_active' => false, 'in_stock' => true]);
        $outOfStock = Product::factory()->create(['is_active' => true, 'in_stock' => false]);

        $ids = Product::storefront()
            ->whereIn('id', [$visible->id, $inactive->id, $outOfStock->id])
            ->pluck('id')
            ->all();

        $this->assertSame([$visible->id], $ids);
    }

    public function test_on_sale_toggle_is_not_overwritten_by_discounted_prices(): void
    {
        $product = Product::factory()->create([
            'regular_price' => 100,
            'sale_price' => 80,
            'on_sale' => false,
        ]);

        $product->update(['name' => 'Updated '.$product->name]);

        $this->assertFalse($product->fresh()->on_sale);
    }
}
