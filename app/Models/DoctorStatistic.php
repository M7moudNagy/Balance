<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DoctorStatistic extends Model
{
    use HasFactory;
    protected $fillable = ['doctor_id', 'rating_sum', 'rating_count', 'views'];

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }

    public function getAverageRatingAttribute()
    {
        return $this->rating_count > 0 ? round($this->rating_sum / $this->rating_count, 1) : 0;
    }
}

