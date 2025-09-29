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
        Schema::create('user_roles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('role_name')->index(); // super_admin, admin, moderator, finance, support
            $table->json('permissions')->nullable(); // Additional custom permissions
            $table->unsignedBigInteger('created_by'); // Admin who assigned the role
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users');
            
            // Ensure one active role per user
            $table->unique(['user_id', 'is_active']);
            
            $table->index(['user_id', 'is_active']);
            $table->index('role_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_roles');
    }
};