<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $replace = static fn (?string $value): ?string => $value === null
            ? null
            : str_ireplace('specialized skill training', 'community experiences', $value);

        DB::table('pages')->orderBy('id')->each(function (object $page) use ($replace): void {
            $boxes = json_decode($page->about_info_boxes ?? '[]', true) ?: [];

            array_walk_recursive($boxes, function (&$value) use ($replace): void {
                if (is_string($value)) {
                    $value = $replace($value);
                }
            });

            $content = $replace($page->content);
            $boxJson = $page->about_info_boxes === null ? null : json_encode($boxes, JSON_UNESCAPED_UNICODE);

            if ($content !== $page->content || $boxJson !== $page->about_info_boxes) {
                DB::table('pages')->where('id', $page->id)->update([
                    'content' => $content,
                    'about_info_boxes' => $boxJson,
                    'updated_at' => now(),
                ]);
            }
        });

        DB::table('page_faqs')->orderBy('id')->each(function (object $faq) use ($replace): void {
            $question = $replace($faq->question);
            $answer = $replace($faq->answer);

            if ($question !== $faq->question || $answer !== $faq->answer) {
                DB::table('page_faqs')->where('id', $faq->id)->update([
                    'question' => $question,
                    'answer' => $answer,
                    'updated_at' => now(),
                ]);
            }
        });
    }

    public function down(): void
    {
        // Content wording changes are intentionally preserved on rollback.
    }
};
