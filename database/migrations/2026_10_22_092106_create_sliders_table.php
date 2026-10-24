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
        Schema::create('sliders', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->boolean('autoplay')->default(true);
            $table->unsignedInteger('autoplay_speed')->default(5000);
            $table->unsignedInteger('transition_speed')->default(800);
            $table->string('transition_effect')->default('fade'); // fade, slide
            $table->boolean('show_navigation')->default(true);
            $table->boolean('show_pagination')->default(true);
            $table->boolean('pause_on_hover')->default(true);
            $table->string('desktop_height')->default('650px');
            $table->string('tablet_height')->default('500px');
            $table->string('mobile_height')->default('450px');
            $table->json('settings')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sliders');
    }
};
