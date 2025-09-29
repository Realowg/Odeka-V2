<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('user_roles')) {
            return;
        }

        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'mysql') {
            // Drop legacy unique constraint if it exists
            try { DB::statement('ALTER TABLE user_roles DROP INDEX user_roles_user_id_is_active_unique'); } catch (\Throwable $e) {}
            try { DB::statement('ALTER TABLE user_roles DROP INDEX user_roles_user_id_is_active_index'); } catch (\Throwable $e) {}

            // Ensure integer sizes match legacy users.id (commonly INT UNSIGNED)
            try { DB::statement('ALTER TABLE user_roles MODIFY user_id INT UNSIGNED NOT NULL'); } catch (\Throwable $e) {}
            try { DB::statement('ALTER TABLE user_roles MODIFY created_by INT UNSIGNED NOT NULL'); } catch (\Throwable $e) {}

            // Add generated column for partial unique constraint if missing
            $hasActiveUserId = false;
            try {
                $cols = DB::select("SHOW COLUMNS FROM user_roles LIKE 'active_user_id'");
                $hasActiveUserId = !empty($cols);
            } catch (\Throwable $e) {}

            if (!$hasActiveUserId) {
                try {
                    DB::statement('ALTER TABLE user_roles ADD COLUMN active_user_id INT UNSIGNED GENERATED ALWAYS AS (CASE WHEN is_active = 1 THEN user_id ELSE NULL END) STORED');
                } catch (\Throwable $e) {}
            }

            // Create unique index to enforce one active role per user
            try { DB::statement('CREATE UNIQUE INDEX user_roles_one_active_per_user ON user_roles (active_user_id)'); } catch (\Throwable $e) {}

            // Recreate helpful indexes
            try { DB::statement('CREATE INDEX user_roles_user_active_idx ON user_roles (user_id, is_active)'); } catch (\Throwable $e) {}
            try { DB::statement('CREATE INDEX user_roles_role_name_idx ON user_roles (role_name)'); } catch (\Throwable $e) {}

            // Add foreign keys if not present
            try { DB::statement('ALTER TABLE user_roles ADD CONSTRAINT user_roles_user_id_foreign FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE'); } catch (\Throwable $e) {}
            try { DB::statement('ALTER TABLE user_roles ADD CONSTRAINT user_roles_created_by_foreign FOREIGN KEY (created_by) REFERENCES users(id)'); } catch (\Throwable $e) {}
        } elseif ($driver === 'pgsql') {
            // PostgreSQL: replace unique with partial unique index
            try { DB::statement('DROP INDEX IF EXISTS user_roles_user_id_is_active_unique'); } catch (\Throwable $e) {}
            try { DB::statement('CREATE UNIQUE INDEX IF NOT EXISTS user_roles_one_active_per_user ON user_roles (user_id) WHERE is_active = true'); } catch (\Throwable $e) {}
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable('user_roles')) {
            return;
        }

        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'mysql') {
            try { DB::statement('DROP INDEX user_roles_one_active_per_user ON user_roles'); } catch (\Throwable $e) {}
            try { DB::statement('ALTER TABLE user_roles DROP COLUMN active_user_id'); } catch (\Throwable $e) {}
        } elseif ($driver === 'pgsql') {
            try { DB::statement('DROP INDEX IF EXISTS user_roles_one_active_per_user'); } catch (\Throwable $e) {}
        }
    }
};


