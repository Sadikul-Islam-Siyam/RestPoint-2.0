<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('game_id')->constrained('games')->cascadeOnDelete();
            $table->string('name');
            $table->string('slug');
            $table->text('keywords')->nullable();
            $table->timestamps();
            $table->unique(['game_id', 'slug']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
