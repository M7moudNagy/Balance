<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PatientTip extends Model
{
    use HasFactory;

    protected $table = 'patient_tip';

    protected $fillable = [
        'patient_id',
        'tip_id',
        'status',
    ];
    public function patient(){
        return $this->belongsToMany(Patient::class);
    }
    public function tip(){
        return $this->belongsToMany(Tip::class);
    }
}
