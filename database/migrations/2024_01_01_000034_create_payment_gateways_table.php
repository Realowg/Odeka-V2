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
        Schema::create('payment_gateways', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50);
            $table->string('type', 255);
            $table->enum('enabled', ["1","0"])->default(1);
            $table->enum('sandbox', ["true","false"])->default('true');
            $table->decimal('fee', 3, 1);
            $table->decimal('fee_cents', 6, 2);
            $table->string('email', 80);
            $table->string('token', 200);
            $table->string('key', 255);
            $table->string('key_secret', 255);
            $table->text('bank_info');
            $table->enum('recurrent', ["yes","no"]);
            $table->string('logo', 50);
            $table->string('webhook_secret', 255);
            $table->enum('subscription', ["yes","no"])->default('yes');
            $table->string('ccbill_accnum', 200);
            $table->string('ccbill_subacc', 200);
            $table->string('ccbill_flexid', 200);
            $table->string('ccbill_salt', 200);
            $table->string('ccbill_subacc_subscriptions', 200);
            $table->string('project_id', 255)->nullable();
            $table->string('project_secret', 255)->nullable();
            $table->string('ccbill_datalink_username', 255)->nullable();
            $table->string('ccbill_datalink_password', 255)->nullable();
            $table->integer('ccbill_skip_subaccount_cancellations')->default(0);
            $table->integer('allow_payments_alipay')->default(0);
            $table->string('crypto_currency', 50)->nullable();

            $table->index('project_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_gateways');
    }
};