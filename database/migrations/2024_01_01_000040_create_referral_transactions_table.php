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
        Schema::create('referral_transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('transactions_id')->nullable();
            $table->unsignedBigInteger('referrals_id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('referred_by');
            $table->string('earnings');
            $table->char('type', 25);
            $table->timestamps();

            $table->index('transactions_id');
            $table->index('referrals_id');
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('referral_transactions');
    }
};