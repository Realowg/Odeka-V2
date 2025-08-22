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
        Schema::create('deposits', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('txn_id', 200);
            $table->unsignedInteger('amount');
            $table->string('payment_gateway', 100);
            $table->timestamp('date')->useCurrent();
            $table->enum('status', ["active","pending"])->default('active');
            $table->string('screenshot_transfer', 100);
            $table->string('percentage_applied', 50)->nullable();
            $table->string('transaction_fee');
            $table->text('taxes');

            $table->index('user_id');
            $table->index('txn_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deposits');
    }
};