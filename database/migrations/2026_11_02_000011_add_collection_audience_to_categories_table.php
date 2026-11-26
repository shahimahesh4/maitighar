<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table): void {
            $table->string('collection_audience', 20)
                ->nullable()
                ->after('parent_id')
                ->index();
        });

        DB::table('categories')
            ->where('slug', 'womens-cultural-dresses')
            ->update(['collection_audience' => 'women']);
        DB::table('categories')
            ->where('slug', 'mens-cultural-dresses')
            ->update(['collection_audience' => 'men']);
        DB::table('categories')
            ->where('slug', 'kids-cultural-dresses')
            ->update(['collection_audience' => 'kids']);

        Cache::forget('navigation.categories');
    }

    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table): void {
            $table->dropIndex(['collection_audience']);
            $table->dropColumn('collection_audience');
        });
    }
};
