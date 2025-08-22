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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('txn_id', 250);
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('subscriptions_id');
            $table->unsignedBigInteger('subscribed');
            $table->decimal('earning_net_user', 10, 2);
            $table->decimal('earning_net_admin', 10, 2);
            $table->string('payment_gateway', 100);
            $table->string('approved', 50)->default(1);
            $table->string('amount');
            $table->string('type', 100)->default('subscription');
            $table->string('percentage_applied', 50);
            $table->unsignedInteger('referred_commission');
            $table->text('taxes');
            $table->integer('direct_payment')->default(0);
            $table->unsignedInteger('gift_id')->nullable();
            $table->timestamps();

            $table->index('txn_id');
            $table->index('user_id');
            $table->index('subscriptions_id');
            $table->index('gift_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};