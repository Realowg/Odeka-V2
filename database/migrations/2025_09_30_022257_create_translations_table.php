<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('translations', function (Blueprint $table) {
            $table->id();
            $table->string('locale', 10)->index();              // 'en', 'fr', 'es'
            $table->string('group', 100)->index();              // 'admin', 'odeka', 'general'
            $table->string('key', 255);                         // 'hero_headline', 'sign_in'
            $table->text('value')->nullable();                  // The actual translation
            $table->timestamps();
            
            // Composite unique index
            $table->unique(['locale', 'group', 'key'], 'unique_translation');
            
            // Performance indexes
            $table->index(['locale', 'group'], 'idx_locale_group');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('translations');
    }
};