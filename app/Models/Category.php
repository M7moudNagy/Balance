<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;
    protected $table = 'categories';
    protected $fillable = ['name'];

    public function tasks(){
        return $this->hasMany(Task::class);
    }
    public function tips()
    {
        return $this->hasMany(Tip::class);
    }
}
