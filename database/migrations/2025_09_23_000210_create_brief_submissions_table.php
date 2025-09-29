<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('brief_submissions', function (Blueprint $table) {
            $table->id();
            $table->string('company', 255)->nullable();
            $table->string('name', 255);
            $table->string('email', 255);
            $table->string('phone', 100)->nullable();
            $table->text('objectives')->nullable();
            $table->text('budget')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('brief_submissions');
    }
};


