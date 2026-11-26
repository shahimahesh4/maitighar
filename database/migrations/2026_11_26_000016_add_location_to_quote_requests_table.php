<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('quote_requests', function (Blueprint $table) {
            if (! Schema::hasColumn('quote_requests', 'city')) {
                $table->string('city', 100)->nullable()->after('phone');
            }

            if (! Schema::hasColumn('quote_requests', 'address')) {
                $table->string('address', 500)->nullable()->after('city');
            }
        });
    }

    public function down(): void
    {
        Schema::table('quote_requests', function (Blueprint $table) {
            $columns = array_filter(['city', 'address'], fn (string $column): bool => Schema::hasColumn('quote_requests', $column));

            if ($columns !== []) {
                $table->dropColumn($columns);
            }
        });
    }
};
