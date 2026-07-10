<?php

namespace Database\Factories;

use App\Models\Post;
use App\Models\User;
use App\Models\Game;
use Illuminate\Database\Eloquent\Factories\Factory;

class PostFactory extends Factory
{
    protected $model = Post::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'game_id' => Game::factory(),
            'category_id' => null,
            'type' => fake()->randomElement(['help', 'discussion']),
            'title' => rtrim(fake()->sentence(), '.'),
            'body' => fake()->paragraphs(3, true),
            'is_solved' => false,
            'is_pinned' => false,
            'is_spoiler' => fake()->boolean(10),
            'views' => fake()->numberBetween(0, 500),
        ];
    }
}
