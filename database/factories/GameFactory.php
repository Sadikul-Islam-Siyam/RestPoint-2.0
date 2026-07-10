<?php

namespace Database\Factories;

use App\Models\Game;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class GameFactory extends Factory
{
    protected $model = Game::class;

    public function definition(): array
    {
        $name = fake()->unique()->words(3, true);
        return [
            'name' => ucfirst($name),
            'slug' => Str::slug($name),
            'genre' => fake()->randomElement(['Action RPG', 'FPS', 'Sandbox', 'Strategy']),
            'platform' => fake()->randomElement(['PC', 'PlayStation 5', 'Xbox Series X/S']),
            'developer' => fake()->company(),
            'release_date' => fake()->date(),
        ];
    }
}
