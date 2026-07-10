<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('votes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('votable_type');
            $table->unsignedBigInteger('votable_id');
            $table->timestamps();

            $table->unique(['user_id', 'votable_type', 'votable_id']);
            $table->index(['votable_type', 'votable_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('votes');
    }
};
