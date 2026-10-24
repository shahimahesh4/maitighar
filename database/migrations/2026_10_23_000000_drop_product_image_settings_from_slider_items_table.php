<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('slider_items', function (Blueprint $table): void {
            $table->dropColumn('product_image_settings');
        });
    }

    public function down(): void
    {
        Schema::table('slider_items', function (Blueprint $table): void {
            $table->json('product_image_settings')->nullable()->after('buttons');
        });
    }
};
