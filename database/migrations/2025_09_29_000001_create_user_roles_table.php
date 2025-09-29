<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('user_roles', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id');
            $table->string('role_name')->index(); // super_admin, admin, moderator, finance, support
            $table->json('permissions')->nullable(); // Additional custom permissions
            $table->unsignedInteger('created_by'); // Admin who assigned the role
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users');
            
            // Indexes for performance
            $table->index(['user_id', 'is_active']);
            $table->index('role_name');
        });

        // Enforce one active role per user without restricting multiple inactive roles
        try {
            $driver = Schema::getConnection()->getDriverName();

            if ($driver === 'pgsql') {
                DB::statement('CREATE UNIQUE INDEX user_roles_one_active_per_user ON user_roles (user_id) WHERE is_active = true');
            } elseif ($driver === 'mysql') {
                DB::statement('ALTER TABLE user_roles ADD COLUMN active_user_id INT UNSIGNED GENERATED ALWAYS AS (CASE WHEN is_active = 1 THEN user_id ELSE NULL END) STORED');
                DB::statement('CREATE UNIQUE INDEX user_roles_one_active_per_user ON user_roles (active_user_id)');
            }
        } catch (\Throwable $e) {
            // no-op if DB does not support generated columns or index already exists
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_roles');
    }
};