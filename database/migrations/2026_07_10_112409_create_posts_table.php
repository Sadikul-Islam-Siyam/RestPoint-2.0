<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('game_id')->constrained('games')->cascadeOnDelete();
            $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->enum('type', ['help', 'discussion']);
            $table->string('title');
            $table->longText('body');
            $table->boolean('is_solved')->default(false);
            $table->boolean('is_pinned')->default(false);
            $table->boolean('is_spoiler')->default(false);
            $table->unsignedInteger('views')->default(0);
            $table->softDeletes();
            $table->timestamps();

            $table->index(['game_id', 'category_id']);
            $table->index(['type', 'is_solved']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
