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
        Schema::create('api_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('method', 10);
            $table->string('endpoint', 500);
            $table->integer('status_code');
            $table->text('request_body')->nullable();
            $table->text('response_body')->nullable();
            $table->ipAddress('ip_address');
            $table->decimal('duration', 8, 3)->nullable(); // seconds
            $table->timestamp('created_at');
            
            $table->index(['user_id', 'created_at']);
            $table->index(['endpoint', 'created_at']);
            $table->index('status_code');
            
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('api_logs');
    }
};
