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
        Schema::create('withdrawals', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->enum('status', ["pending","paid"])->default('pending');
            $table->string('amount', 50);
            $table->timestamp('date')->useCurrent();
            $table->string('gateway', 100);
            $table->text('account');
            $table->timestamp('estimated_payment')->nullable();
            $table->timestamp('date_paid')->default('0000-00-00');
            $table->string('txn_id', 255);

            $table->index('user_id');
            $table->index('txn_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('withdrawals');
    }
};