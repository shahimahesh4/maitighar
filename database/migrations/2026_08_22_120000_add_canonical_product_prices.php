<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table): void {
            $table->decimal('regular_price', 10, 2)->nullable()->after('old_price');
            $table->decimal('sale_price', 10, 2)->nullable()->after('regular_price');
            $table->unsignedTinyInteger('discount_percentage')->nullable()->after('sale_price');
        });

        DB::table('products')->orderBy('id')->chunkById(200, function ($products): void {
            foreach ($products as $product) {
                $salePrice = (float) $product->price;
                $regularPrice = (float) ($product->old_price ?: $product->price);
                DB::table('products')->where('id', $product->id)->update([
                    'regular_price' => $regularPrice,
                    'sale_price' => $salePrice,
                    'discount_percentage' => $regularPrice > $salePrice && $regularPrice > 0 ? (int) round((($regularPrice - $salePrice) / $regularPrice) * 100) : null,
                ]);
            }
        });
    }

    public function down(): void
    {
        Schema::table('products', fn (Blueprint $table) => $table->dropColumn(['regular_price', 'sale_price', 'discount_percentage']));
    }
};
