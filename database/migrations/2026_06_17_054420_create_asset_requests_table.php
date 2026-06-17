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
       Schema::create('asset_requests', function (Blueprint $table) {

    $table->id();

    $table->foreignId('employee_id');

    $table->foreignId('employee_asset_id');

    $table->string('request_type');

    $table->string('subject');

    $table->text('message');

    $table->json('photos')->nullable();

    $table->enum(
        'status',
        [
            'Pending',
            'Approved',
            'Rejected',
            'Completed'
        ]
    )->default('Pending');

    $table->text('admin_remark')->nullable();

    $table->foreignId('approved_by')
        ->nullable();

    $table->timestamp('approved_at')
        ->nullable();

    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asset_requests');
    }
};
