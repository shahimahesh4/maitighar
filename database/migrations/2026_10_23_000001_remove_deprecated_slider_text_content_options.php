<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private const REMOVED_KEYS = [
        'badge_text',
        'subtitle',
        'slogan',
        'description',
        'price',
        'discount',
        'text_color',
        'text_alignment',
    ];

    public function up(): void
    {
        DB::table('slider_items')
            ->whereNotNull('text_content')
            ->orderBy('id')
            ->eachById(function (object $item): void {
                $content = json_decode($item->text_content, true);

                if (! is_array($content)) {
                    return;
                }

                foreach (self::REMOVED_KEYS as $key) {
                    unset($content[$key]);
                }

                DB::table('slider_items')
                    ->where('id', $item->id)
                    ->update(['text_content' => json_encode($content)]);
            });
    }

    public function down(): void
    {
        // Removed copy values cannot be reconstructed safely.
    }
};
