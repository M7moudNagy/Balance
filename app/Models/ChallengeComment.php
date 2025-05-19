<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChallengeComment extends Model
{
    use HasFactory;
    
    protected $fillable = ['comment', 'challenge_id', 'user_id', 'user_type'];

    public function user()
    {
        return $this->morphTo();
    }

    public function challange()
    {
        return $this->belongsTo(Challenge::class);
    }
}
