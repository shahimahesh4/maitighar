<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('avatar')->nullable()->after('trainer_id');
            $table->date('date_of_birth')->nullable()->after('avatar');
            $table->string('gender', 30)->nullable()->after('date_of_birth');
            $table->string('address')->nullable()->after('gender');
            $table->string('city', 100)->nullable()->after('address');
            $table->string('state', 100)->nullable()->after('city');
            $table->string('postal_code', 20)->nullable()->after('state');
            $table->string('emergency_contact_name')->nullable()->after('postal_code');
            $table->string('emergency_contact_relationship', 60)->nullable()->after('emergency_contact_name');
            $table->string('emergency_contact_phone', 30)->nullable()->after('emergency_contact_relationship');
            $table->string('guardian_name')->nullable()->after('emergency_contact_phone');
            $table->string('guardian_phone', 30)->nullable()->after('guardian_name');
            $table->string('education_level', 100)->nullable()->after('guardian_phone');
            $table->string('institution')->nullable()->after('education_level');
            $table->string('employee_id', 50)->nullable()->unique()->after('institution');
            $table->string('job_title', 100)->nullable()->after('employee_id');
            $table->string('department', 100)->nullable()->after('job_title');
            $table->date('joining_date')->nullable()->after('department');
            $table->string('employment_type', 50)->nullable()->after('joining_date');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['employee_id']);
            $table->dropColumn([
                'avatar', 'date_of_birth', 'gender', 'address', 'city', 'state', 'postal_code',
                'emergency_contact_name', 'emergency_contact_relationship', 'emergency_contact_phone',
                'guardian_name', 'guardian_phone', 'education_level', 'institution', 'employee_id',
                'job_title', 'department', 'joining_date', 'employment_type',
            ]);
        });
    }
};
