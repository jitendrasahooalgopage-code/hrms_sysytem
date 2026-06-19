<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendance_logs', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->string('name')->nullable();
            $table->string('email')->nullable();

            // Location data
            $table->decimal('checkin_latitude', 10, 8)->nullable();
            $table->decimal('checkin_longitude', 11, 8)->nullable();
            $table->text('checkin_address')->nullable();
            $table->float('checkin_accuracy')->nullable();

            $table->decimal('checkout_latitude', 10, 8)->nullable();
            $table->decimal('checkout_longitude', 11, 8)->nullable();
            $table->text('checkout_address')->nullable();
            $table->float('checkout_accuracy')->nullable();

            // Timestamps
            $table->timestamp('checkin_at')->nullable();
            $table->timestamp('checkout_at')->nullable();

            // Duration in seconds (filled on checkout)
            $table->integer('session_duration')->nullable();

            // Status: 'active' = checked in, 'completed' = checked out
            $table->enum('status', ['active', 'completed'])->default('active');

            // Device/browser info at check-in
            $table->string('ip_address', 45)->nullable();
            $table->string('device_type')->nullable();
            $table->string('browser')->nullable();
            $table->string('platform')->nullable();
            $table->text('user_agent')->nullable();

            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index(['user_id', 'checkin_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_logs');
    }
};
