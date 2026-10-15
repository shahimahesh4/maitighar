<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('old_price', 10, 2)->nullable()->after('price');
            $table->json('specifications')->nullable()->after('short_description');
            $table->longText('delivery_policy')->nullable()->after('specifications');
            $table->longText('shipping_returns')->nullable()->after('delivery_policy');
            $table->string('custom_tab_title')->nullable()->after('shipping_returns');
            $table->longText('custom_tab_content')->nullable()->after('custom_tab_title');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn([
                'old_price',
                'specifications',
                'delivery_policy',
                'shipping_returns',
                'custom_tab_title',
                'custom_tab_content',
            ]);
        });
    }
};
