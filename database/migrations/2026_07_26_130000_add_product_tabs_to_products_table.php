<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->json('product_tabs')->nullable()->after('custom_tab_content');
        });

        DB::table('products')
            ->select(['id', 'delivery_policy', 'shipping_returns', 'custom_tab_title', 'custom_tab_content'])
            ->orderBy('id')
            ->chunkById(100, function ($products): void {
                foreach ($products as $product) {
                    $tabs = [];

                    if (filled($product->delivery_policy)) {
                        $tabs[] = ['title' => 'Delivery Policy', 'content' => $product->delivery_policy];
                    }
                    if (filled($product->shipping_returns)) {
                        $tabs[] = ['title' => 'Shipping & Returns', 'content' => $product->shipping_returns];
                    }
                    if (filled($product->custom_tab_title) && filled($product->custom_tab_content)) {
                        $tabs[] = ['title' => $product->custom_tab_title, 'content' => $product->custom_tab_content];
                    }

                    if ($tabs !== []) {
                        DB::table('products')->where('id', $product->id)->update([
                            'product_tabs' => json_encode($tabs, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                        ]);
                    }
                }
            });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('product_tabs');
        });
    }
};
