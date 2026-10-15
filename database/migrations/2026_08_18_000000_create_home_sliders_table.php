<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('home_sliders', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('eyebrow')->nullable();
            $table->string('title');
            $table->string('subtitle')->nullable();
            $table->string('desktop_image');
            $table->string('mobile_image')->nullable();
            $table->string('button_label')->nullable();
            $table->string('button_url')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->unsignedInteger('duration')->default(6000);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['is_active', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('home_sliders');
    }
};
