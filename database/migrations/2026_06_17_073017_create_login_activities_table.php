<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('login_activities', function (Blueprint $table) {

            $table->id();

            $table->integer('user_id')->nullable();

            $table->string('employee_code')->nullable();

            $table->string('name')->nullable();
            $table->string('email')->nullable();

            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();

            $table->string('device_type')->nullable();
            $table->string('browser')->nullable();
            $table->string('platform')->nullable();

            $table->string('session_id')->nullable();

            $table->enum('activity_type', [
                'login',
                'logout',
                'failed_login',
                'token_login',
                'password_reset'
            ])->default('login');

            $table->enum('status', [
                'success',
                'failed'
            ])->default('success');

            $table->string('login_method')->nullable(); // Password, OTP, SSO

            $table->timestamp('login_at')->nullable();
            $table->timestamp('logout_at')->nullable();

            $table->integer('session_duration')->nullable(); // seconds

            $table->string('location')->nullable();

            $table->json('metadata')->nullable();
            $table->string('lattitude')->nullable();
            $table->string('longitude')->nullable();
            $table->longText('full_address')->nullable();

            $table->timestamps();

            $table->index('user_id');
            $table->index('email');
            
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('login_activities');
    }
};