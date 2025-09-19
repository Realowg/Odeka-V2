<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('users') && !Schema::hasColumn('users', 'preferred_currency')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('preferred_currency', 3)->nullable()->after('language');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('users') && Schema::hasColumn('users', 'preferred_currency')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('preferred_currency');
            });
        }
    }
};


