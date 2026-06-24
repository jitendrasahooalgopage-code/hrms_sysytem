<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            // Only add columns that don't already exist
            if (!Schema::hasColumn('employees', 'blood_group')) {
                $table->string('blood_group')->nullable()->after('avatar');
            }
            if (!Schema::hasColumn('employees', 'emergency_phone')) {
                $table->string('emergency_phone')->nullable()->after('phone');
            }
            if (!Schema::hasColumn('employees', 'official_phone')) {
                $table->string('official_phone')->nullable()->after('emergency_phone');
            }
            if (!Schema::hasColumn('employees', 'doj')) {
                $table->date('doj')->nullable()->after('dob'); 
            }
            if (!Schema::hasColumn('employees', 'official_email')) {
                $table->string('official_email')->nullable()->after('email');
            }
            if (!Schema::hasColumn('employees', 'personal_email')) {
                $table->string('personal_email')->nullable()->after('official_email');
            }
            if (!Schema::hasColumn('employees', 'present_address')) {
                $table->text('present_address')->nullable()->after('address');
            }
            if (!Schema::hasColumn('employees', 'permanent_address')) {
                $table->text('permanent_address')->nullable()->after('present_address');
            }
            // emp_status is referenced in the model fillable but may differ from 'status'
            if (!Schema::hasColumn('employees', 'emp_status')) {
                $table->string('emp_status')->nullable()->after('status');
            }
        });
    }

    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn([
                'blood_group',
                'emergency_phone',
                'official_phone',
                'doj',
                'official_email',
                'personal_email',
                'present_address',
                'permanent_address',
                'emp_status',
            ]);
        });
    }
};
