<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['name', 'slug', 'cover_image', 'banner_image', 'trailer_url', 'genre', 'platform', 'developer', 'release_date', 'external_api_id', 'created_by'])]
class Game extends Model
{
    use HasFactory;

    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    public function categories()
    {
        return $this->hasMany(Category::class);
    }

    public function tags()
    {
        return $this->hasMany(Tag::class);
    }

    public function gameLinks()
    {
        return $this->hasMany(GameLink::class);
    }

    public function followers()
    {
        return $this->belongsToMany(User::class, 'game_follows')->withTimestamps();
    }
}
