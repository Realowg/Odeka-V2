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
        Schema::create('live_streamings', function (Blueprint $table) {
            $table->id();
            $table->string('type', 100)->default('normal');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('buyer_id')->nullable();
            $table->string('name', 255);
            $table->text('channel');
            $table->unsignedInteger('minutes');
            $table->unsignedInteger('price');
            $table->enum('status', ["0","1"])->default(0);
            $table->timestamp('joined_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->char('availability', 50)->default('all_pay');
            $table->string('token', 100)->nullable();
            $table->timestamps();

            $table->index('user_id');
            $table->index('buyer_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('live_streamings');
    }
};