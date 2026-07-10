<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('game_links', function (Blueprint $table) {
            $table->id();
            $table->foreignId('game_id')->constrained('games')->cascadeOnDelete();
            $table->string('store_name');
            $table->string('url');
            $table->string('icon')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('game_links');
    }
};
