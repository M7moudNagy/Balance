<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    protected $table = 'questions'; // تأكد أن اسم الجدول متطابق
    protected $guarded = [];
    protected $timestamp = false;

    public function task() {
        return $this->belongsTo(Task::class);
    }
    
    public function options() {
        return $this->hasMany(Option::class);
    }
    
    public function responses()
    {
        return $this->hasMany(Response::class, 'question_id');
    }
}
