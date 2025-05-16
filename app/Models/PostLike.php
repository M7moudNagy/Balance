<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostLike extends Model
{
    protected $fillable = ['post_id', 'user_id', 'user_type'];

    public function user()
    {
        return $this->morphTo();
    }

    public function post()
    {
        return $this->belongsTo(Post::class);
    }
}

