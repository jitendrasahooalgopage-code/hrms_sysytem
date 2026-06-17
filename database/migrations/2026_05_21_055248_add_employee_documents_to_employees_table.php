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
            $table->longText('aadhaar_card')->nullable();
            $table->longText('pan_card')->nullable();

            $table->longText('matric_certificate')->nullable();
            $table->longText('plus_two_certificate')->nullable();

            $table->longText('bachelor_degree_certificate')->nullable();
            $table->longText('master_degree_certificate')->nullable();

            $table->longText('address_proof')->nullable();

            $table->longText('last_company_release_letter')->nullable();
            $table->longText('last_company_offer_letter')->nullable();

            $table->longText('salary_slip_1')->nullable();
            $table->longText('salary_slip_2')->nullable();
            $table->longText('salary_slip_3')->nullable();

            $table->longText('bank_passbook_page')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            //
        });
    }
};
