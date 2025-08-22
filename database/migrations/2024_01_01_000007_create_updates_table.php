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
        Schema::create('updates', function (Blueprint $table) {
            $table->id();
            $table->string('title', 150)->nullable();
            $table->text('description');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->timestamp('date')->useCurrent();
            $table->string('token_id');
            $table->enum('locked', ['yes', 'no'])->default('no');
            $table->enum('fixed_post', ['0', '1'])->default('0');
            $table->decimal('price', 10, 2)->default(0);
            $table->char('status', 20)->default('active');
            $table->unsignedBigInteger('video_views')->default(0);
            $table->string('ip', 200)->nullable();
            $table->timestamp('scheduled_date')->nullable();
            $table->boolean('schedule')->default(false);
            $table->boolean('editing')->default(false);
            $table->boolean('can_media_edit')->default(false);
            
            $table->index(['user_id', 'status']);
            $table->index('date');
            $table->index('scheduled_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('updates');
    }
};
