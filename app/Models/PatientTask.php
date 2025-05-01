<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PatientTask extends Model
{
    use HasFactory;
    protected $table = 'patients_tasks';
    protected $guarded = [];
    protected $casts = [
        'patient_id' => 'array',
    ];
    public function patient(){
        return $this->belongsToMany(Patient::class);
    }
    public function task(){
        return $this->belongsToMany(Task::class);
    }
}
