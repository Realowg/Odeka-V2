<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('odeva_cost_analytics', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->unsignedBigInteger('creator_id')->nullable();
            $table->integer('total_requests')->default(0);
            $table->integer('total_tokens')->default(0);
            $table->decimal('total_cost', 10, 6)->default(0);
            $table->json('breakdown')->nullable(); // { chat: X, functions: Y, automation: Z }
            $table->timestamps();

            $table->foreign('creator_id')->references('id')->on('users')->onDelete('cascade');

            $table->unique(['date', 'creator_id']);
            $table->index('date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('odeva_cost_analytics');
    }
};

