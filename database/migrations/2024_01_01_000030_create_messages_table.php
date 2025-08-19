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
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('conversations_id');
            $table->unsignedBigInteger('from_user_id');
            $table->unsignedBigInteger('to_user_id');
            $table->text('message');
            $table->string('attach_file', 100);
            $table->enum('status', ["new","readed"])->default('new');
            $table->enum('remove_from', ["0","1"])->default(1);
            $table->string('file', 150);
            $table->string('original_name', 255);
            $table->string('format', 10);
            $table->string('size', 50);
            $table->decimal('price', 10, 2);
            $table->enum('tip', ["yes","no"])->default('no');
            $table->unsignedInteger('tip_amount');
            $table->enum('mode', ["active","pending"])->default('active');
            $table->unsignedInteger('gift_id')->nullable();
            $table->decimal('gift_amount', 10, 2);
            $table->timestamps();

            $table->index('conversations_id');
            $table->index('from_user_id');
            $table->index('to_user_id');
            $table->index('gift_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};