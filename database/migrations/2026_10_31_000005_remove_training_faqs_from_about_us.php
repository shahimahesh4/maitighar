<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $pageId = DB::table('pages')->where('slug', 'about-us')->value('id');

        if (! $pageId) {
            return;
        }

        DB::table('page_faqs')
            ->where('page_id', $pageId)
            ->whereIn('question', [
                'Do I need previous beauty experience to join?',
                'Is the training practical or theory-based?',
                'Do students receive a certificate?',
                'Does Maitighar provide professional skill training?',
                'Who can join Maitighar training programs?',
                'Do I need previous experience to join a training course?',
                'How can I learn more about available training programs?',
            ])
            ->delete();
    }

    public function down(): void
    {
        // Removed FAQs are intentionally not recreated on rollback.
    }
};
