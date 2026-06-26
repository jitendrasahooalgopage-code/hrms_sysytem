<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leave_allocations', function (Blueprint $table) {
            $table->id();
            $table->year('year');
            $table->foreignId('leave_type_id')
                  ->constrained('leave_types')
                  ->cascadeOnDelete();
            $table->integer('days');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leave_allocations');
    }
};