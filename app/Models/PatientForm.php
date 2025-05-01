<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PatientForm extends Model
{
    use HasFactory;
    protected $table = 'patient_forms';
    protected $guarded = [];
    protected $casts = [
        'patient_id' => 'array',
    ];
    public function patient(){
        return $this->belongsToMany(Patient::class);
    }
    public function form(){
        return $this->belongsToMany(Form::class);
    }
}
