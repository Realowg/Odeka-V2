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
        Schema::create('media', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('updates_id');
            $table->unsignedBigInteger('user_id');
            $table->string('type', 100);
            $table->string('image', 255);
            $table->string('width', 5)->nullable();
            $table->string('height', 5)->nullable();
            $table->string('img_type', 255);
            $table->string('video', 255);
            $table->enum('encoded', ["yes","no"])->default('no');
            $table->string('video_poster', 255)->nullable();
            $table->string('duration_video', 50)->nullable();
            $table->string('quality_video', 20)->nullable();
            $table->string('video_embed', 200);
            $table->string('music', 255);
            $table->string('file', 255);
            $table->string('file_name', 255);
            $table->string('file_size', 255);
            $table->string('bytes', 255)->nullable();
            $table->string('mime', 255)->nullable();
            $table->string('token', 255);
            $table->enum('status', ["active","pending"])->default('active');
            $table->string('job_id', 200)->nullable();
            $table->timestamps();

            $table->index('updates_id');
            $table->index('user_id');
            $table->index('job_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('media');
    }
};