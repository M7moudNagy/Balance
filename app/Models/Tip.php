<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tip extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'category_id',
        'published_date',
        'notes',
        'doctor_id'

//        'status',
    ];

    // علاقة Many-to-Many مع المرضى
    public function patients()
    {
        return $this->belongsToMany(Patient::class, 'patient_tip')
            ->withPivot('status')
            ->withTimestamps();
    }

    // علاقة Many-to-One مع الفئات
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }
}
