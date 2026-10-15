<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('media_folders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_id')->nullable()->constrained('media_folders')->nullOnDelete();
            $table->string('name');
            $table->timestamps();

            $table->unique(['parent_id', 'name']);
        });

        Schema::create('media_assets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('media_folder_id')->nullable()->constrained()->nullOnDelete();
            $table->string('disk')->default('public');
            $table->string('path');
            $table->string('name');
            $table->string('mime_type')->nullable();
            $table->unsignedBigInteger('size')->nullable();
            $table->string('alt_text')->nullable();
            $table->text('caption')->nullable();
            $table->timestamps();

            $table->unique(['disk', 'path']);
            $table->index(['media_folder_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('media_assets');
        Schema::dropIfExists('media_folders');
    }
};
