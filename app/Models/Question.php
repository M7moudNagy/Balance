<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    protected $table = 'questions'; // تأكد أن اسم الجدول متطابق
    protected $fillable = ['form_id', 'type', 'question', 'options'];
    protected $timestamp = false;
    protected $casts = [
        'options' => 'array', // يحول البيانات تلقائيًا من JSON إلى مصفوفة
    ];

    public function form()
    {
        return $this->belongsTo(Form::class, 'form_id');
    }

    public function responses()
    {
        return $this->hasMany(Response::class, 'question_id');
    }
}
