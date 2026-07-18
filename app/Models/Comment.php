<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['post_id', 'user_id', 'parent_id', 'body', 'is_accepted'])]
class Comment extends Model
{
    use HasFactory, SoftDeletes;

    protected function casts(): array
    {
        return [
            'is_accepted' => 'boolean',
        ];
    }

    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function parent()
    {
        return $this->belongsTo(Comment::class, 'parent_id');
    }

    public function replies()
    {
        return $this->hasMany(Comment::class, 'parent_id');
    }

    public function votes()
    {
        return $this->morphMany(Vote::class, 'votable');
    }

    public function reports()
    {
        return $this->morphMany(Report::class, 'reportable');
    }

    public function getFormattedBodyAttribute(): string
    {
        // Safe whitelist for rich text formatting in Trix editor comments
        $body = strip_tags($this->body, ['div', 'strong', 'em', 'pre', 'code', 'a', 'ul', 'ol', 'li', 'br', 'span']);

        // Spoiler markup: ||spoiler||
        $body = preg_replace(
            '/\|\|(.*?)\|\|/s',
            '<span x-data="{ revealed: false }" @click="revealed = !revealed" :class="revealed ? \'\' : \'blur-[5px] select-none cursor-pointer bg-gray-200 dark:bg-white/10 px-1 rounded\'" class="transition-all duration-200" title="Click to reveal spoiler">$1</span>',
            $body
        );

        return $body;
    }
}
