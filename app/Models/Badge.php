<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['name', 'icon', 'description', 'condition_key'])]
class Badge extends Model
{
    use HasFactory;

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_badges')->withPivot('earned_at')->withTimestamps();
    }
}
