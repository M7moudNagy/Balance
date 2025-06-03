<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Session extends Model
{
    use HasFactory;

    protected $fillable = ['doctor_id', 'patient_id', 'date', 'time', 'platform_link', 'status'];

    public function doctor() {
        return $this->belongsTo(Doctor::class);
    }

    public function patient() {
        return $this->belongsTo(Patient::class);
    }
}
