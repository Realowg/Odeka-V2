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
        Schema::table('subscriptions', function (Blueprint $table) {
            // Add missing columns from original Odeka subscriptions table
            $table->unsignedBigInteger('creator_id')->after('user_id');
            $table->string('last_payment', 255)->nullable()->after('ends_at');
            $table->enum('free', ['yes', 'no'])->default('no')->after('last_payment');
            $table->string('subscription_id', 50)->after('free');
            $table->enum('cancelled', ['yes', 'no'])->default('no')->after('subscription_id');
            $table->enum('rebill_wallet', ['on', 'off'])->default('off')->after('cancelled');
            $table->string('interval', 100)->default('monthly')->after('rebill_wallet');
            $table->text('taxes')->nullable()->after('interval');
            $table->string('payment_id', 255)->nullable()->after('taxes');
            
            // Also need to modify stripe_id to not be unique since original doesn't require uniqueness
            $table->dropUnique(['stripe_id']);
            $table->string('stripe_id', 255)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            // Remove the added columns in reverse order
            $table->dropColumn([
                'payment_id',
                'taxes', 
                'interval',
                'rebill_wallet',
                'cancelled',
                'subscription_id',
                'free',
                'last_payment',
                'creator_id'
            ]);
            
            // Restore unique constraint on stripe_id
            $table->string('stripe_id')->unique()->change();
        });
    }
};
