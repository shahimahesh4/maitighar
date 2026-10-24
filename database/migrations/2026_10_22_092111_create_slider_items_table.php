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
        Schema::create('slider_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('slider_id')->constrained('sliders')->cascadeOnDelete();
            $table->string('title');
            $table->string('type')->default('animated_content'); // 'single_image' or 'animated_content'
            $table->unsignedInteger('sort_order')->default(0)->index();
            $table->boolean('is_active')->default(true)->index();

            // Option 1: Single Image Banner
            $table->string('desktop_image')->nullable();
            $table->string('mobile_image')->nullable();
            $table->string('link_url')->nullable();
            $table->boolean('open_in_new_tab')->default(false);

            // Option 2: Animated Content Banner (structured JSON columns)
            $table->json('text_content')->nullable();
            $table->json('buttons')->nullable();
            $table->json('product_image_settings')->nullable();
            $table->json('background_settings')->nullable();
            $table->json('layer_animations')->nullable();
            $table->json('responsive_settings')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('slider_items');
    }
};
