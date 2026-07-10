<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('games', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('cover_image')->nullable();
            $table->string('banner_image')->nullable();
            $table->string('trailer_url')->nullable();
            $table->string('genre')->nullable();
            $table->string('platform')->nullable();
            $table->string('developer')->nullable();
            $table->date('release_date')->nullable();
            $table->unsignedBigInteger('external_api_id')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('games');
    }
};
