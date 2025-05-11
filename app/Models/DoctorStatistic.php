<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DoctorStatistic extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $casts = [
        'average_rating' => 'float',
    ];
    

    public function doctor()
    {
        return $this->belongsTo(Doctor::class, 'doctor_id');
    }

    public function getAverageRatingAttribute()
    {
        return $this->rating_count > 0 ? round($this->rating_sum / $this->rating_count, 1) : 0;
    }
    public function updateDoctorPatientCount()
{
    // جيب الدكتور المرتبط بالإحصائيات
    $doctor = Doctor::find($this->doctor_id);

    // احسب عدد المرضى المرتبطين بالدكتور
    $patientsCount = $doctor->patients()->count();

    // حدث القيمة وخزنها
    $this->patients_count = $patientsCount;
    $this->save();
}

    
}

