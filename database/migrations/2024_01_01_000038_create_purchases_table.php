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
        Schema::create('purchases', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('transactions_id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('products_id');
            $table->string('delivery_status', 50)->default('delivered');
            $table->text('description_custom_content');
            $table->string('address', 200)->nullable();
            $table->string('city', 150)->nullable();
            $table->string('zip', 50)->nullable();
            $table->char('phone', 20)->nullable();
            $table->timestamp('expired_at')->nullable();
            $table->timestamps();

            $table->index('transactions_id');
            $table->index('user_id');
            $table->index('products_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchases');
    }
};