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
        Schema::create('media_stories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('stories_id');
            $table->string('name', 255);
            $table->string('type', 100);
            $table->string('video_length', 20)->nullable();
            $table->string('video_poster', 100)->nullable();
            $table->text('text');
            $table->string('font_color', 50)->default('#ffffff');
            $table->string('font', 50)->default('Arial');
            $table->integer('status')->default(0);
            $table->string('job_id', 200)->nullable();
            $table->timestamps();

            $table->index('stories_id');
            $table->index('job_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('media_stories');
    }
};