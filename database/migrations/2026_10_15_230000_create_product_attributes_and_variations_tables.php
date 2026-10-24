<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Add product_type and stock_quantity to products
        Schema::table('products', function (Blueprint $table) {
            if (! Schema::hasColumn('products', 'product_type')) {
                $table->string('product_type')->default('simple')->after('name')->index();
            }
            if (! Schema::hasColumn('products', 'stock_quantity')) {
                $table->integer('stock_quantity')->default(0)->after('in_stock');
            }
        });

        // 2. Global Attributes table
        if (! Schema::hasTable('attributes')) {
            Schema::create('attributes', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('slug')->unique();
                $table->string('type')->default('select'); // select, color
                $table->integer('sort_order')->default(0);
                $table->timestamps();
            });
        }

        // 3. Global Attribute Values table
        if (! Schema::hasTable('attribute_values')) {
            Schema::create('attribute_values', function (Blueprint $table) {
                $table->id();
                $table->foreignId('attribute_id')->constrained('attributes')->cascadeOnDelete();
                $table->string('value');
                $table->string('slug');
                $table->string('color_code')->nullable(); // Hex color for color swatches e.g. #FF0000
                $table->integer('sort_order')->default(0);
                $table->timestamps();

                $table->unique(['attribute_id', 'value']);
            });
        }

        // 4. Product Attributes assignment
        if (! Schema::hasTable('product_attributes')) {
            Schema::create('product_attributes', function (Blueprint $table) {
                $table->id();
                $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
                $table->foreignId('attribute_id')->constrained('attributes')->cascadeOnDelete();
                $table->boolean('is_visible')->default(true); // visible in product specifications
                $table->boolean('is_variation')->default(false); // used to generate variations
                $table->integer('sort_order')->default(0);
                $table->timestamps();

                $table->unique(['product_id', 'attribute_id']);
            });
        }

        // 5. Product Attribute Values assignment
        if (! Schema::hasTable('product_attribute_values')) {
            Schema::create('product_attribute_values', function (Blueprint $table) {
                $table->id();
                $table->foreignId('product_attribute_id')->constrained('product_attributes')->cascadeOnDelete();
                $table->foreignId('attribute_value_id')->constrained('attribute_values')->cascadeOnDelete();
                $table->timestamps();

                $table->unique(['product_attribute_id', 'attribute_value_id'], 'prod_attr_val_unique');
            });
        }

        // 6. Product Variations
        if (! Schema::hasTable('product_variations')) {
            Schema::create('product_variations', function (Blueprint $table) {
                $table->id();
                $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
                $table->string('sku')->nullable()->unique();
                $table->decimal('regular_price', 12, 2)->nullable();
                $table->decimal('sale_price', 12, 2)->nullable();
                $table->integer('stock_quantity')->default(0);
                $table->boolean('manage_stock')->default(true);
                $table->string('image')->nullable();
                $table->boolean('is_enabled')->default(true);
                $table->integer('sort_order')->default(0);
                $table->timestamps();
            });
        }

        // 7. Product Variation Attribute Values (defines each combination)
        if (! Schema::hasTable('product_variation_attribute_values')) {
            Schema::create('product_variation_attribute_values', function (Blueprint $table) {
                $table->id();
                $table->foreignId('product_variation_id')->constrained('product_variations')->cascadeOnDelete();
                $table->foreignId('attribute_id')->constrained('attributes')->cascadeOnDelete();
                $table->foreignId('attribute_value_id')->constrained('attribute_values')->cascadeOnDelete();
                $table->timestamps();

                $table->unique(['product_variation_id', 'attribute_id'], 'prod_var_attr_val_unique');
            });
        }

        // 8. Order Items additions for variations snapshot
        Schema::table('order_items', function (Blueprint $table) {
            if (! Schema::hasColumn('order_items', 'variation_id')) {
                $table->foreignId('variation_id')->nullable()->after('product_id')->constrained('product_variations')->nullOnDelete();
            }
            if (! Schema::hasColumn('order_items', 'variation_sku')) {
                $table->string('variation_sku')->nullable()->after('variation_id');
            }
            if (! Schema::hasColumn('order_items', 'options_text')) {
                $table->string('options_text')->nullable()->after('variation_sku');
            }
        });

        // 9. Inventory Logs variation support
        Schema::table('inventory_logs', function (Blueprint $table) {
            if (! Schema::hasColumn('inventory_logs', 'variation_id')) {
                $table->foreignId('variation_id')->nullable()->after('product_id')->constrained('product_variations')->nullOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('inventory_logs') && Schema::hasColumn('inventory_logs', 'variation_id')) {
            Schema::table('inventory_logs', function (Blueprint $table) {
                $table->dropConstrainedForeignId('variation_id');
            });
        }

        if (Schema::hasTable('order_items')) {
            Schema::table('order_items', function (Blueprint $table) {
                if (Schema::hasColumn('order_items', 'variation_id')) {
                    $table->dropConstrainedForeignId('variation_id');
                }
                if (Schema::hasColumn('order_items', 'variation_sku')) {
                    $table->dropColumn('variation_sku');
                }
                if (Schema::hasColumn('order_items', 'options_text')) {
                    $table->dropColumn('options_text');
                }
            });
        }

        Schema::dropIfExists('product_variation_attribute_values');
        Schema::dropIfExists('product_variations');
        Schema::dropIfExists('product_attribute_values');
        Schema::dropIfExists('product_attributes');
        Schema::dropIfExists('attribute_values');
        Schema::dropIfExists('attributes');

        if (Schema::hasTable('products')) {
            Schema::table('products', function (Blueprint $table) {
                if (Schema::hasColumn('products', 'product_type')) {
                    $table->dropColumn('product_type');
                }
                if (Schema::hasColumn('products', 'stock_quantity')) {
                    $table->dropColumn('stock_quantity');
                }
            });
        }
    }
};
