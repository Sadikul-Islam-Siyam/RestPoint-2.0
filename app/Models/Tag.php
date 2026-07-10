<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['game_id', 'name', 'slug'])]
class Tag extends Model
{
    use HasFactory;

    public function game()
    {
        return $this->belongsTo(Game::class);
    }

    public function posts()
    {
        return $this->belongsToMany(Post::class, 'post_tags');
    }
}
