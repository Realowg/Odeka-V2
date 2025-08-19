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
        Schema::create('live_comments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('live_streamings_id');
            $table->text('comment');
            $table->unsignedInteger('joined')->default(1);
            $table->enum('tip', ["0","1"])->default(0);
            $table->decimal('earnings', 10, 2);
            $table->unsignedInteger('gift_id')->nullable();
            $table->timestamps();

            $table->index('user_id');
            $table->index('live_streamings_id');
            $table->index('gift_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('live_comments');
    }
};