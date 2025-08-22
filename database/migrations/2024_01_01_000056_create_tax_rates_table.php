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
        Schema::create('tax_rates', function (Blueprint $table) {
            $table->id();
            $table->string('name', 250);
            $table->integer('type')->default(1);
            $table->decimal('percentage', 5, 2);
            $table->string('country', 100)->nullable();
            $table->string('state', 100)->nullable();
            $table->char('iso_state', 10)->nullable();
            $table->string('stripe_id', 100)->nullable();
            $table->enum('status', ["0","1"])->default(1);
            $table->timestamps();

            $table->index('stripe_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tax_rates');
    }
};