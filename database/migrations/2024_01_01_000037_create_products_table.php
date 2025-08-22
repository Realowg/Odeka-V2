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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('name', 255);
            $table->char('type', 20)->default('digital');
            $table->decimal('price', 10, 2);
            $table->unsignedInteger('delivery_time');
            $table->char('country_free_shipping', 20);
            $table->text('tags');
            $table->text('description');
            $table->string('file', 255);
            $table->string('mime', 50)->nullable();
            $table->string('extension', 50)->nullable();
            $table->string('size', 50)->nullable();
            $table->enum('status', ["0","1"])->default(1);
            $table->decimal('shipping_fee', 10, 2);
            $table->unsignedInteger('quantity');
            $table->string('box_contents', 200);
            $table->string('category', 20)->nullable();
            $table->unsignedInteger('downloads');
            $table->text('external_link');
            $table->timestamps();

            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};