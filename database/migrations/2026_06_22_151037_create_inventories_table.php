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
    Schema::create('inventories', function (Blueprint $table) {

        $table->id();

        // Laptop, Desktop, Mouse, Keyboard, Mobile
        $table->string('asset_type');

        // Laptop, Mouse, Keyboard
        $table->string('serial_no')->nullable();

        // Desktop
        $table->string('cpu_serial_no')->nullable();
        $table->string('monitor_serial_no')->nullable();

        // Mobile
        $table->string('imei')->nullable();

        $table->enum('sim_provider', [
            'Airtel',
            'Jio',
            'VI',
            'BSNL'
        ])->nullable();

        $table->integer('plan_days')->nullable();

        // Admin notes
        $table->text('message')->nullable();

        // Asset status
        $table->enum('status', [
            'Available',
            'Assigned',
            'Damaged',
            'Lost',
            'Repair'
        ])->default('Available');

        $table->timestamps();

    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventories');
    }
};
