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
        Schema::create('odeva_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('creator_id');
            $table->enum('status', ['trial', 'active', 'paused', 'cancelled'])->default('trial');
            $table->date('trial_ends_at')->nullable();
            $table->date('next_billing_date')->nullable();
            $table->decimal('price', 10, 2)->default(0);
            $table->string('currency', 3)->default('USD');
            $table->boolean('automation_enabled')->default(false);
            $table->json('settings')->nullable();
            $table->timestamps();

            $table->index('creator_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('odeva_subscriptions');
    }
};
