<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->dropForeign(['trainer_id']);
            $table->dropColumn([
                'trainer_id',
                'emergency_contact_name',
                'emergency_contact_relationship',
                'emergency_contact_phone',
                'guardian_name',
                'guardian_phone',
                'education_level',
                'institution',
            ]);
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->foreignId('trainer_id')->nullable()->after('role')->constrained('users')->nullOnDelete();
            $table->string('emergency_contact_name')->nullable()->after('postal_code');
            $table->string('emergency_contact_relationship', 60)->nullable()->after('emergency_contact_name');
            $table->string('emergency_contact_phone', 30)->nullable()->after('emergency_contact_relationship');
            $table->string('guardian_name')->nullable()->after('emergency_contact_phone');
            $table->string('guardian_phone', 30)->nullable()->after('guardian_name');
            $table->string('education_level', 100)->nullable()->after('guardian_phone');
            $table->string('institution')->nullable()->after('education_level');
        });
    }
};
