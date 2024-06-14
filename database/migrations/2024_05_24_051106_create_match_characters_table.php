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
        Schema::create('match_characters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('match_id')->constrained('matches');
            $table->foreignId('character_id')->constrained('characters');
            $table->enum('role',['player','ally','enemy']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('match_characters');
    }
};
