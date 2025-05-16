<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Response extends Model
{
    use HasFactory;

    protected $table = 'responses';
    protected $fillable = [
        'patient_id',
        'task_id',
        'question_id',
        'answer_text',
        'time_taken',
    ];

    public function question()
    {
        return $this->belongsTo(Question::class);
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function task()
    {
        return $this->belongsTo(Task::class);
    }
}
