<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['game_id', 'store_name', 'url', 'icon'])]
class GameLink extends Model
{
    use HasFactory;

    public function game()
    {
        return $this->belongsTo(Game::class);
    }
}
