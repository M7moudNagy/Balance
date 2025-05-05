<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DoctorStatistic extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }

    public function getAverageRatingAttribute()
    {
        return $this->rating_count > 0 ? round($this->rating_sum / $this->rating_count, 1) : 0;
    }
    public function updatePatientCount()
    {
        $this->patients_count = $this->doctor->patients()->count(); 
        $this->save();
    }
    
}

