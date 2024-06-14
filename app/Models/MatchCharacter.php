<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MatchCharacter extends Model
{
    use HasFactory;

    protected $fillable = [
        'match_id',
        'character_id',
        'role',
    ];

    public function match()
    {
        return $this->belongsTo(GameMatch::class);
    }

    public function character()
    {
        return $this->belongsTo(Character::class);
    }
}
