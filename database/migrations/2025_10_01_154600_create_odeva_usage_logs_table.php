<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('odeva_usage_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('creator_id');
            $table->unsignedBigInteger('subscriber_id')->nullable();
            $table->unsignedBigInteger('conversation_id')->nullable();
            $table->string('action', 100); // chat, function_call, automation
            $table->integer('tokens_used')->default(0);
            $table->decimal('cost', 10, 6)->default(0);
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->foreign('creator_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('subscriber_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('conversation_id')->references('id')->on('odeva_conversations')->onDelete('cascade');

            $table->index(['creator_id', 'created_at']);
            $table->index('action');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('odeva_usage_logs');
    }
};

