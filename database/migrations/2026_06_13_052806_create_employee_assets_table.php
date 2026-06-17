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
        Schema::create('employee_assets', function (Blueprint $table) {

            $table->id();

            $table->foreignId('employee_id')
                ->constrained('employees')
                ->cascadeOnDelete();

            // Example:
            // Laptop,Desktop,Mouse
            $table->text('asset_name');
            

            $table->text('message')->nullable();

            $table->date('assigned_date');

            $table->enum('status', [
                'Assigned',
                'Returned',
                'Damaged',
                'Lost'
            ])->default('Assigned');

            $table->timestamps();

            // One employee = one asset record
            $table->unique('employee_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_assets');
    }
};