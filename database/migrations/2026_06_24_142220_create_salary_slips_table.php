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
        Schema::create('salary_slips', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->onDelete('cascade');
            $table->string('month');
            $table->integer('year');
            
            // Earnings
            $table->decimal('basic_salary', 12, 2)->default(0.00);
            $table->decimal('house_rent_allowance', 12, 2)->default(0.00);
            $table->decimal('conveyance_allowance', 12, 2)->default(0.00);
            $table->decimal('medical_allowance', 12, 2)->default(0.00);
            $table->decimal('special_allowance', 12, 2)->default(0.00);
            
            // Deductions
            $table->decimal('provident_fund', 12, 2)->default(0.00);
            $table->decimal('professional_tax', 12, 2)->default(0.00);
            $table->decimal('income_tax', 12, 2)->default(0.00);
            $table->decimal('other_deductions', 12, 2)->default(0.00);
            
            // Totals
            $table->decimal('gross_earnings', 12, 2)->default(0.00);
            $table->decimal('total_deductions', 12, 2)->default(0.00);
            $table->decimal('net_salary', 12, 2)->default(0.00);
            
            $table->date('pay_date')->nullable();
            $table->enum('status', ['Paid', 'Pending'])->default('Pending');
            $table->text('remarks')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('salary_slips');
    }
};