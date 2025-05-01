<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Response extends Model
{
    use HasFactory;

    protected $table = 'responses'; // تأكيد اسم الجدول
    protected $fillable = ['form_id', 'question_id', 'patient_id', 'answer', 'description'];

    public function question()
    {
        return $this->belongsTo(Question::class, 'question_id');
    }

    public function form()
    {
        return $this->belongsTo(Form::class, 'form_id');
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class, 'patient_id');
    }
}
