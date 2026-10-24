<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Models\ProductVariation;
use App\Models\Setting;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ResequenceProductSkus extends Command
{
    protected $signature = 'products:resequence-skus
        {--all : Resequence all products from 001 sequentially}
        {--prefix= : Custom SKU prefix (defaults to store setting or MG-AUS-)}
        {--dry-run : Preview changes without saving to the database}';

    protected $description = 'Resequence product SKUs sequentially (e.g. MG-AUS-001, MG-AUS-002, ...)';

    public function handle(): int
    {
        $prefix = (string) ($this->option('prefix') ?: Setting::get('sku_prefix', 'MG-AUS-'));
        $isDryRun = (bool) $this->option('dry-run');
        $resequenceAll = (bool) $this->option('all');

        $this->info("Target SKU Prefix: [{$prefix}]" . ($isDryRun ? ' (DRY RUN)' : ''));

        $products = Product::orderBy('id')->get();
        if ($products->isEmpty()) {
            $this->warn('No products found in the database.');

            return self::SUCCESS;
        }

        $changes = [];
        $currentCounter = 1;

        if ($resequenceAll) {
            foreach ($products as $product) {
                $newSku = $prefix . str_pad((string) $currentCounter, 3, '0', STR_PAD_LEFT);
                $currentCounter++;

                $changes[] = [
                    'id' => $product->id,
                    'name' => $product->name,
                    'old_sku' => $product->sku,
                    'new_sku' => $newSku,
                    'record' => $product,
                ];
            }
        } else {
            // Only update products that don't already match the pattern prefix + 3 digits
            $pattern = '/^' . preg_quote($prefix, '/') . '\d{3,}$/';

            // Find max existing number among matching products
            $maxExisting = 0;
            foreach ($products as $product) {
                if (preg_match('/^' . preg_quote($prefix, '/') . '(\d+)$/', (string) $product->sku, $matches)) {
                    $num = (int) $matches[1];
                    if ($num > $maxExisting) {
                        $maxExisting = $num;
                    }
                }
            }

            $nextNumber = $maxExisting + 1;

            foreach ($products as $product) {
                if (! preg_match($pattern, (string) $product->sku)) {
                    $newSku = $prefix . str_pad((string) $nextNumber, 3, '0', STR_PAD_LEFT);
                    $nextNumber++;

                    $changes[] = [
                        'id' => $product->id,
                        'name' => $product->name,
                        'old_sku' => $product->sku,
                        'new_sku' => $newSku,
                        'record' => $product,
                    ];
                }
            }
        }

        if (empty($changes)) {
            $this->info('All product SKUs are already properly formatted.');

            return self::SUCCESS;
        }

        $this->table(
            ['ID', 'Product Name', 'Current SKU', 'New SKU'],
            array_map(fn ($c) => [$c['id'], $c['name'], $c['old_sku'], $c['new_sku']], $changes)
        );

        if ($isDryRun) {
            $this->info(count($changes) . ' product(s) would be updated. No changes were made (dry-run).');

            return self::SUCCESS;
        }

        DB::transaction(function () use ($changes): void {
            // In case of potential collision during batch swap, temporarily prefix with TMP-
            foreach ($changes as $item) {
                DB::table('products')->where('id', $item['id'])->update(['sku' => 'TMP-' . uniqid()]);
            }

            foreach ($changes as $item) {
                DB::table('products')->where('id', $item['id'])->update(['sku' => $item['new_sku']]);

                // Also update variations if they used the old product SKU as a prefix
                if (! empty($item['old_sku'])) {
                    $variations = ProductVariation::where('product_id', $item['id'])->get();
                    foreach ($variations as $variation) {
                        if (str_starts_with((string) $variation->sku, (string) $item['old_sku'])) {
                            $updatedVarSku = $item['new_sku'] . substr((string) $variation->sku, strlen((string) $item['old_sku']));
                            $variation->update(['sku' => $updatedVarSku]);
                        }
                    }
                }
            }
        });

        $this->info('Successfully updated ' . count($changes) . ' product SKU(s)!');

        return self::SUCCESS;
    }
}
