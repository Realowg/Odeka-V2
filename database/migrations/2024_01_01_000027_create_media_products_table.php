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
        Schema::create('media_products', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('products_id');
            $table->string('name', 255);
            $table->timestamps();

            $table->index('products_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('media_products');
    }
};