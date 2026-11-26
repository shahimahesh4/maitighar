<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Associated FAQs are removed by the existing foreign-key cascade.
        DB::table('pages')->where('slug', 'academy-training')->delete();
    }

    public function down(): void
    {
        // The page was intentionally removed and is not restored on rollback.
    }
};
