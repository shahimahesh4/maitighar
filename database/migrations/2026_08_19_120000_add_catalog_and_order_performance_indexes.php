<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table): void {
            $table->index(['user_id', 'created_at'], 'orders_user_created_index');
        });

        Schema::table('products', function (Blueprint $table): void {
            $table->index(['is_active', 'created_at'], 'products_active_created_index');
            $table->index(['is_active', 'on_sale', 'created_at'], 'products_sale_created_index');
            $table->index(['is_active', 'price'], 'products_active_price_index');
        });
    }

    public function down(): void
    {
        Schema::table('orders', fn (Blueprint $table) => $table->dropIndex('orders_user_created_index'));
        Schema::table('products', function (Blueprint $table): void {
            $table->dropIndex('products_active_created_index');
            $table->dropIndex('products_sale_created_index');
            $table->dropIndex('products_active_price_index');
        });
    }
};
