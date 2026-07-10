<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['user_id', 'game_id', 'category_id', 'type', 'title', 'body', 'is_solved', 'is_pinned', 'is_spoiler', 'views'])]
class Post extends Model
{
    use HasFactory, SoftDeletes;

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function game()
    {
        return $this->belongsTo(Game::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'post_tags');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function votes()
    {
        return $this->morphMany(Vote::class, 'votable');
    }

    public function scopeSolved($query)
    {
        return $query->where('is_solved', true);
    }

    public function scopeForGame($query, $gameId)
    {
        return $query->where('game_id', $gameId);
    }
}
