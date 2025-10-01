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
        Schema::create('odeva_conversations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('creator_id');
            $table->unsignedBigInteger('subscriber_id')->nullable();
            $table->enum('status', ['active', 'archived'])->default('active');
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['creator_id', 'subscriber_id']);
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('odeva_conversations');
    }
};
