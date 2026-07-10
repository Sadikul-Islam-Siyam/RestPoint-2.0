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

    protected function casts(): array
    {
        return [
            'is_pinned' => 'boolean',
            'is_solved' => 'boolean',
            'is_spoiler' => 'boolean',
        ];
    }

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

    public function reports()
    {
        return $this->morphMany(Report::class, 'reportable');
    }

    public function scopeSolved($query)
    {
        return $query->where('is_solved', true);
    }

    public function scopeForGame($query, $gameId)
    {
        return $query->where('game_id', $gameId);
    }

    public function getFormattedBodyAttribute(): string
    {
        $body = $this->body;

        // 1. YouTube embedding (watch?v=...)
        $body = preg_replace(
            '/https?:\/\/(?:www\.)?youtube\.com\/watch\?v=([a-zA-Z0-9_-]+)/',
            '<div class="my-4"><iframe class="w-full aspect-video rounded border border-gray-200 dark:border-white/5 shadow-sm" src="https://www.youtube.com/embed/$1" frameborder="0" allowfullscreen></iframe></div>',
            $body
        );

        // 2. YouTube embedding (youtu.be/...)
        $body = preg_replace(
            '/https?:\/\/youtu\.be\/([a-zA-Z0-9_-]+)/',
            '<div class="my-4"><iframe class="w-full aspect-video rounded border border-gray-200 dark:border-white/5 shadow-sm" src="https://www.youtube.com/embed/$1" frameborder="0" allowfullscreen></iframe></div>',
            $body
        );

        // 3. Spoiler markup: ||spoiler||
        $body = preg_replace(
            '/\|\|(.*?)\|\|/s',
            '<span x-data="{ revealed: false }" @click="revealed = !revealed" :class="revealed ? \'\' : \'blur-[5px] select-none cursor-pointer bg-gray-200 dark:bg-white/10 px-1 rounded\'" class="transition-all duration-200" title="Click to reveal spoiler">$1</span>',
            $body
        );

        return $body;
    }
}
