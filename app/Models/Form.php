<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Form extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function patients()
    {
        return $this->belongsToMany(Patient::class, 'patient_forms')
            ->withPivot('status')
            ->withTimestamps();
    }
    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }
    public function questions()
    {
        return $this->hasMany(Question::class);
    }
}
