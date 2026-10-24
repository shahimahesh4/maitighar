<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\Setting;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ProductSkuAutoGenerationTest extends TestCase
{
    use DatabaseTransactions;

    protected Category $category;
    protected Brand $brand;

    protected function setUp(): void
    {
        parent::setUp();

        $this->category = Category::firstOrCreate(
            ['slug' => 'test-category-sku'],
            ['name' => 'Test Category SKU', 'is_active' => true]
        );

        $this->brand = Brand::firstOrCreate(
            ['slug' => 'test-brand-sku'],
            ['name' => 'Test Brand SKU', 'is_active' => true]
        );
    }

    public function test_it_generates_sequential_sku_with_default_prefix(): void
    {
        $prefix = 'TESTSKU-';
        $firstSku = Product::generateNextSku($prefix);
        $this->assertSame('TESTSKU-001', $firstSku);

        Product::create([
            'category_id' => $this->category->id,
            'brand_id' => $this->brand->id,
            'name' => 'SKU Test Product 1',
            'slug' => 'sku-test-product-1',
            'sku' => $firstSku,
            'price' => 50,
            'stock_quantity' => 10,
        ]);

        $secondSku = Product::generateNextSku($prefix);
        $this->assertSame('TESTSKU-002', $secondSku);

        Product::create([
            'category_id' => $this->category->id,
            'brand_id' => $this->brand->id,
            'name' => 'SKU Test Product 2',
            'slug' => 'sku-test-product-2',
            'sku' => $secondSku,
            'price' => 50,
            'stock_quantity' => 10,
        ]);

        $thirdSku = Product::generateNextSku($prefix);
        $this->assertSame('TESTSKU-003', $thirdSku);
    }

    public function test_it_auto_assigns_sku_when_sku_is_not_provided_on_creation(): void
    {
        Setting::set('sku_prefix', 'MG-AUS-');

        $product = Product::create([
            'category_id' => $this->category->id,
            'brand_id' => $this->brand->id,
            'name' => 'SKU Auto Test Product',
            'slug' => 'sku-auto-test-product-' . uniqid(),
            'price' => 75,
            'stock_quantity' => 5,
        ]);

        $this->assertNotEmpty($product->sku);
        $this->assertStringStartsWith('MG-AUS-', $product->sku);
        $this->assertMatchesRegularExpression('/^MG-AUS-\d{3,}$/', $product->sku);
    }

    public function test_resequence_command_dry_run_executes_successfully(): void
    {
        $this->artisan('products:resequence-skus', ['--dry-run' => true])
            ->assertExitCode(0);
    }
}
