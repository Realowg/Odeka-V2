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
        Schema::create('verification_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('address', 200);
            $table->string('city', 150);
            $table->string('zip', 50);
            $table->string('image', 100);
            $table->string('image_reverse', 100)->nullable();
            $table->string('image_selfie', 100)->nullable();
            $table->enum('status', ["pending","approved"])->default('pending');
            $table->string('form_w9', 100);
            $table->timestamps();

            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('verification_requests');
    }
};