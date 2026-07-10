<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('game_follows', function (Blueprint $table) {
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('game_id')->constrained('games')->cascadeOnDelete();
            $table->timestamps();
            $table->primary(['user_id', 'game_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('game_follows');
    }
};
