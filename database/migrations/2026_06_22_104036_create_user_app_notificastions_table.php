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
        Schema::create('user_app_notificastions', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->text('body')->nullable();
            $table->string('type')->default('general');
            // Types: general, policy, salary, holiday, leave, security, announcement, personal
            $table->string('category')->default('ALL');
            // Categories match UI tabs: ALL, ANNOUNCEMENTS, PERSONAL, LEAVES, HOLIDAY, SYSTEM
            $table->string('icon')->nullable();           // icon class or image path
            $table->string('icon_color')->default('primary'); // bootstrap color
            $table->json('target_roles')->nullable();     // null = all roles
            $table->json('target_employee_ids')->nullable(); // null = all employees
            $table->boolean('is_broadcast')->default(false); // send to everyone
            $table->string('action_url')->nullable();     // optional deep link
            $table->string('action_label')->nullable();
            $table->timestamp('scheduled_at')->nullable(); // future scheduling
            $table->timestamp('sent_at')->nullable();
            $table->enum('status', ['draft', 'scheduled', 'sent', 'failed'])->default('draft');
            $table->integer('created_by')->nullable();
            $table->timestamps();
           

            $table->index(['status', 'scheduled_at']);
            $table->index('category');
            $table->index('created_by');
        });

        // Pivot: which employee received which notification + read status
        Schema::create('employee_notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('notification_id')
                  ->constrained('user_app_notificastions')->cascadeOnDelete();
            $table->foreignId('user_id')
                  ->constrained('users')->cascadeOnDelete();
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->timestamp('dismissed_at')->nullable();
            $table->timestamps();

            $table->unique(['notification_id', 'user_id']);
            $table->index(['user_id', 'is_read']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_notifications');
        Schema::dropIfExists('user_app_notificastions');
    }
};
