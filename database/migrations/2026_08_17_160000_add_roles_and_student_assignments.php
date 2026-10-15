<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role', 30)->default('customer')->after('password')->index();
            $table->foreignId('trainer_id')->nullable()->after('role')->constrained('users')->nullOnDelete();
        });

        // Preserve admin access for accounts created before roles existed.
        DB::table('users')->update(['role' => 'super_admin']);
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('trainer_id');
            $table->dropIndex(['role']);
            $table->dropColumn('role');
        });
    }
};
