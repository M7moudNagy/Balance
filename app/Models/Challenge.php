<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Challenge extends Model
{
    use HasFactory;

    protected $fillable = ['content', 'image', 'user_id', 'user_type','type'];

    public function user()
    {
        return $this->morphTo();
    }

    public function likes()
    {
        return $this->hasMany(ChallengeLike::class);
    }

    public function comments()
    {
        return $this->hasMany(ChallengeComment::class);
    }
    public function likedByCurrentUser()
    {
        return $this->hasOne(ChallengeLike::class)->where('user_id', auth()->id());
    }
}
