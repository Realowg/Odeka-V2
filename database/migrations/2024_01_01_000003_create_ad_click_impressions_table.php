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
        Schema::create('ad_click_impressions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('advertisings_id')->constrained('advertisings')->onDelete('cascade');
            $table->char('type', 20);
            $table->string('ip', 100);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ad_click_impressions');
    }
};
