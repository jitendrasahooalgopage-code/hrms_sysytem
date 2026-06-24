<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('job_positions', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->string('department')->nullable();
            $table->string('location')->nullable();
            $table->enum('type', ['full_time', 'part_time', 'contract', 'internship'])->default('full_time');
            $table->integer('openings')->default(1);
            $table->boolean('is_active')->default(true);
            $table->integer('created_by')->nullable();
            $table->timestamps();
        });

        Schema::create('candidates', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('cv_path')->nullable();
            $table->string('cv_original_name')->nullable();
            $table->string('linkedin_url')->nullable();
            $table->string('portfolio_url')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('candidate_id')->constrained()->cascadeOnDelete();
            $table->foreignId('job_position_id')->constrained()->cascadeOnDelete();
            $table->enum('stage', [
                'applied',
                'screening',
                'technical',
                'hr_interview',
                'final_round',
                'offer',
                'hired',
                'rejected',
            ])->default('applied');
            $table->enum('status', ['active', 'on_hold', 'rejected', 'withdrawn'])->default('active');
            $table->integer('stage_order')->default(0);
            $table->text('rejection_reason')->nullable();
            $table->string('source')->nullable(); // LinkedIn, Referral, Job Board, etc.
            $table->decimal('expected_salary', 12, 2)->nullable();
            $table->date('available_from')->nullable();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });

        Schema::create('interview_rounds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained()->cascadeOnDelete();
            $table->string('round_name')->nullable();
            $table->enum('round_type', ['screening', 'technical', 'hr', 'final', 'other'])->default('other');
            $table->enum('mode', ['online', 'in_person', 'phone'])->default('online');
            $table->datetime('scheduled_at')->nullable();
            $table->integer('duration_minutes')->default(60);
            $table->string('meeting_link')->nullable();
            $table->string('location')->nullable();
            $table->foreignId('interviewer_id')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('outcome', ['pending', 'passed', 'failed', 'no_show', 'rescheduled'])->default('pending');
            $table->integer('rating')->nullable(); // 1-5
            $table->text('feedback')->nullable();
            $table->text('internal_notes')->nullable();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });

        Schema::create('application_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained()->cascadeOnDelete();
            $table->string('type')->nullable(); // stage_changed, note_added, interview_scheduled, etc.
            $table->string('description')->nullable();
            $table->json('meta')->nullable();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('application_activities');
        Schema::dropIfExists('interview_rounds');
        Schema::dropIfExists('applications');
        Schema::dropIfExists('candidates');
        Schema::dropIfExists('job_positions');
    }
};
