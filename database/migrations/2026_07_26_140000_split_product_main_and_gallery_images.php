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
            $table->string('main_image')->nullable()->after('images');
            $table->json('gallery_images')->nullable()->after('main_image');
        });

        DB::table('products')
            ->select(['id', 'images'])
            ->orderBy('id')
            ->chunkById(100, function ($products): void {
                foreach ($products as $product) {
                    $images = json_decode($product->images ?? '[]', true);
                    if (! is_array($images) || $images === []) {
                        continue;
                    }

                    DB::table('products')->where('id', $product->id)->update([
                        'main_image' => array_shift($images),
                        'gallery_images' => $images === [] ? null : json_encode(array_values($images), JSON_UNESCAPED_SLASHES),
                    ]);
                }
            });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['main_image', 'gallery_images']);
        });
    }
};
