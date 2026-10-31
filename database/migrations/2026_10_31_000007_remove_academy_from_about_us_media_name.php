<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('media_assets')) {
            return;
        }

        DB::table('media_assets')
            ->where('name', 'About Us Academy')
            ->update(['name' => 'About Us']);
    }

    public function down(): void
    {
        if (! Schema::hasTable('media_assets')) {
            return;
        }

        DB::table('media_assets')
            ->where('name', 'About Us')
            ->where('path', 'media/demo/about-us-academy.png')
            ->update(['name' => 'About Us Academy']);
    }
};
